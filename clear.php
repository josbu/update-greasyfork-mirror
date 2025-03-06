<?php
if($_GET['pass']!=='123654'){
    exit();
}
// 获取当前文件的路径
$currentFile = __FILE__;

// 获取当前文件所在目录
$directory = dirname($currentFile);
$directory.='\store\temp';
// 打开目录
$dir = opendir($directory);
// 遍历目录中的所有文件
while (($file = readdir($dir)) !== false) {
    // 排除当前脚本文件和隐藏文件（以.开头的文件）
    if ($file != basename($currentFile) && $file != '.' && $file != '..') {
        // 获取文件的完整路径
        $filePath = $directory . DIRECTORY_SEPARATOR . $file;
        
        // 如果是文件，则删除
        if (is_file($filePath)) {
            unlink($filePath);
        }
    }
}

// 关闭目录
closedir($dir);

echo "success";
?>
