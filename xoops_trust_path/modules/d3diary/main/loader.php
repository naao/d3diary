<?php
//
// Created on 2006/10/25 by nao-pon http://hypweb.net/
// edited naao 2010/9/26

ignore_user_abort(FALSE);
//error_reporting(0);

if (! isset($_GET['src'])) exit();

// ブラウザキャッシュ有効時間(秒)
$maxage = 86400; // 60*60*24 (1day)

// clear output buffer
while( ob_get_level() ) {
	ob_end_clean() ;
}

// sanitizer class for input validation vulnerabilities
require_once dirname(__FILE__).'/../class/sanitizer.class.php';
$sani = new D3diarySanitizer();

// 変数初期化
if ( $san_line = $sani->san_eval($_GET['src'] ) != 1){
	$src = preg_replace( '/[^\w.%, -]+/' , '' , $_GET['src'] );
} else {
	die( 'wrong request '.$lib ) ;
}
$src = str_replace(' ', ',', $src);

$nocache = (isset($_GET['nc']));

$js_lang = $charset = $pre_width = $cache_file = $gzip_fname = $dir = $out = $type = $src_file = '';
$addcss = array();
$length = $addtime = 0;
$method = empty($_SERVER['REQUEST_METHOD'])? 'GET' : strtoupper($_SERVER['REQUEST_METHOD']);
$pre_id = '';
$js_replaces = array();

if (preg_match('/^(.+)\.([^.]+)$/',$src,$match)) {
	$type = $match[2];
	$src = $match[1];
}

if (!$type || !$src) {
	header( 'HTTP/1.1 404 Not Found' );
	header( 'Content-Length: 0' );
	exit();
}

$basedir = '';

define('UNIX_TIME', (isset($_SERVER['REQUEST_TIME'])? $_SERVER['REQUEST_TIME'] : time()));
$expires = 'Expires: ' . gmdate( 'D, d M Y H:i:s', UNIX_TIME + $maxage ) . ' GMT';

		// Skin dir
		$dir = dirname( dirname(__FILE__) ).'/lib/' ;

switch ($type) {
	case 'css':
		$c_type = 'text/css';

		$src_files = array();
		$srcs = array();
		foreach (explode(',', $src) as $_src) {
			// Default CSS
			// CSS over write (css dir)
			$src_file = $dir.'css/'.$_src.'.css';
			//var_dump($src_file); echo"<br />"; 
			if (is_file($src_file)) {
				$srcs[] = $_src;
				$src_files[$_src] = $src_file;
			}
		}
		$src = join(',', $srcs);
		$src_file = $src_files;

			$out = '';
			foreach($src_file as $_src => $_file) {
				$_out = file_get_contents($_file) . "\n";
				$out .= $_out;
			}

		break;
	case 'js':
		$module_url = XOOPS_URL.'/'.$mydirname;
		$replace = true;
		foreach(explode(',', $src) as $_src) {
			$src_file = $dir.'js/'.$_src.'.js';
			//var_dump($src_file); echo"<br />"; 
			if (is_file($src_file)) {
				$src_files[$_src] = $src_file;
			}
		}
		$src_file = $src_files;
		$c_type = 'application/x-javascript';
			foreach($src_file as $_src => $_file) {
				$_out = file_get_contents($_file) . "\n";
				$out .= $_out;
			}
		break;
	default:
		exit();
}

if ($type === 'js' || $type === 'css' ) {

	$filetime = max(filemtime(__FILE__), get_filemtime($src_file), $addtime);

	$etag = md5($type.$dir.$src.$filetime);

	// 置換処理が必要?
	if ($replace) {
		if ($type === 'css') {
			$out = '';
			foreach($src_file as $_src => $_file) {
				$_out = file_get_contents($_file) . "\n";
				$out .= $_out;
			}
		}
		if ($type === 'js') {
			$out = '';
			foreach($src_file as $_src => $_file) {
				$_out = file_get_contents($_file) . "\n";
				$out .= $_out;
			}
		}
		$length = strlen($out);
	}

	if (!$length) { $length = filesize($src_file); }

	header( 'Content-Type: ' . $c_type );
	header( 'Last-Modified: ' . gmdate( 'D, d M Y H:i:s', $filetime ) . ' GMT' );
	if ($nocache) {
		header( 'Expires: Thu, 01 Dec 1994 16:00:00 GMT' );
		header( 'Cache-Control: no-cache, must-revalidate' );
		header( 'Cache-Control: post-check=0, pre-check=0', false );
		header( 'Pragma: no-cache' );
	} else {
		header( 'Cache-Control: public, max-age=' . $maxage );
		header( $expires );
	}
	header( 'Etag: '. $etag );
	header( 'Content-length: '.$length );

	echo $out;
	
	exit();

} else {
	header( 'HTTP/1.1 404 Not Found' );
	header( 'Content-Length: 0' );
	exit();
}

function get_filemtime ($file) {
	if (! is_array($file)) {
		return filemtime($file);
	} else {
		$time = 0;
		foreach($file as $f) {
			if (is_file($f)) {
				$time = max($time, filemtime($f));
			}
		}
		return $time;
	}
}

// file_get_contents -- Reads entire file into a string
// (PHP 4 >= 4.3.0, PHP 5)
if (! function_exists('file_get_contents')) {
	function file_get_contents($filename, $incpath = false, $resource_context = null)
	{
		if (false === $fh = fopen($filename, 'rb', $incpath)) {
			trigger_error('file_get_contents() failed to open stream: No such file or directory', E_USER_WARNING);
			return false;
		}

		clearstatcache();
		if ($fsize = @filesize($filename)) {
			$data = fread($fh, $fsize);
		} else {
			$data = '';
			while (!feof($fh)) {
				$data .= fread($fh, 8192);
			}
		}

		fclose($fh);
		return $data;
	}
}

?>