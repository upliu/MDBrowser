<?php
// define the root path, you can change this on your condition
define('FILE_ROOT', __DIR__ . DIRECTORY_SEPARATOR); 

require 'vendor/autoload.php';

$path = isset($_GET['path']) ? $_GET['path'] : '';

$realpath = realpath(FILE_ROOT . $path);

if (is_dir($realpath)) {
	$realdir = rtrim($realpath, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
} else {
	$realdir = rtrim(dirname($realpath), DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
	$path = dirname($path);
}

// for security
if (strpos($realdir, FILE_ROOT) !== 0) {
	die('Access Denied!');
}

// auto refresh the page ?
$auto_refresh = empty($_GET['auto_refresh']) ? 0 : 1;

echo '<div style="border:1px solid #CCC;">';

$dir = '';
$links[] = '<a href="?">ROOT</a>';
$dirs = explode(DIRECTORY_SEPARATOR, $path);
foreach ($dirs as $dir_part) {
	$dir .= $dir_part . DIRECTORY_SEPARATOR;
	$links[] = sprintf('<a href="?path=%s&auto_refresh=%s">%s</a>', $dir, $auto_refresh, $dir_part);
}
echo '<p><span style="color:red;">Current path:</span> ' . implode(DIRECTORY_SEPARATOR, $links) . '</p>';
foreach (glob($realdir . '*') as $file) {
	$show_file = substr($file, strlen($realdir));
	$show_path = substr($file, strlen(FILE_ROOT));
	printf('<p><a href="?path=%s&auto_refresh=%s">%s</a></p>', $show_path, $auto_refresh, $show_file);
}
echo '</div>';

if (is_file($realpath)) {
	if (preg_match('/(\.md|\.markdown)$/i', $realpath)) {
		echo \Michelf\Markdown::defaultTransform(file_get_contents($realpath));
	} else if (strtolower(substr($realpath, -4)) === '.php') {
		highlight_file($realpath);
	} else {
		echo '<pre>';
		echo file_get_contents($realpath);
		echo '</pre>';
	}
}

if ($auto_refresh) {
	echo '<script>setTimeout(function(){window.location.reload(true);}, 2000);</script>';
}
