<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
$originUrl = 'https://update.cn-greasyfork.org'; //源网站请求的update链接
$replaceUpdateUrl = 'https://update.gfs.com'; //用于替换的update链接
$route = $_GET['route'];
$key = md5($route);
$fn = __DIR__ . '/' . 'store/';
$routes = explode('/', $route);
// exit($route);
if (count($routes) >= 3 && is_numeric($routes[1])) {
    $fn .= 'forever';
} else {
    $fn .= 'temp';
}
$file_name = $fn . '/' . $key . '.js';
if (file_exists($file_name)) {
    $js =  file_get_contents($file_name);
    header('Access-Control-Allow-Origin: *');

    // 设置内容类型为 JavaScript
    header('Content-Type: text/plain; charset=utf-8');
    exit($js);
} else {
    $url = $originUrl . '/' . 'scripts/' . rawurlencode($route);
    $key = md5($route);
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        "User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/58.0.3029.110 Safari/537.36",
        "Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng,*/*;q=0.8",
        "Accept-Encoding: gzip, deflate",
        "Accept-Language: en-US,en;q=0.9",
        "Connection: keep-alive",
        "Upgrade-Insecure-Requests: 1"
    ));
    // curl_setopt($ch, CURLOPT_PROXY, "http://127.0.0.1:10809");
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_ENCODING, '');
    $response = curl_exec($ch);
    if ($response === false) {
        echo $url;
        if (curl_errno($ch)) {
            echo "cURL 错误码: " . curl_errno($ch) . "\n";
            echo "cURL 错误信息: " . curl_error($ch) . "\n";
        }
        exit();
    }
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    // 检查是否是 302 重定向
    if ($httpCode == 302 || $httpCode == 301) {
        preg_match('/Location: (.+)/', $response, $matches);
        $redirectUrl = trim($matches[1]);
        $redirect_url = str_ireplace($originUrl, $replaceUpdateUrl, $redirect_url);
        header("Location: " . $redirect_url);
        exit();
    } else {
        $js = $response;
        // $js = mb_convert_encoding($js, 'HTML-ENTITIES', 'UTF-8');
        $js = str_ireplace($originUrl, $replaceUpdateUrl, $js);
        $file = fopen($file_name, 'w');
        if ($file) {
            fwrite($file, $js);
            // 关闭文件
            fclose($file);
        }
        header('Access-Control-Allow-Origin: *');

        // 设置内容类型为 JavaScript
        header('Content-Type: text/plain; charset=utf-8');
        exit($js);
    }
}
