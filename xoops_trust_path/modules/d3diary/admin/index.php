<?php

include_once XOOPS_TRUST_PATH."/modules/d3diary/class/d3diaryConf.class.php";
$d3dConf = & D3diaryConf::getInstance ( $mydirname, 0, "admin" ) ;

// edit parameters
$eparam = array() ;
// photo parameters
list( $eparam['uploaddir'] , $eparam['previewdir'] ) = $d3dConf->get_photodir() ;	// photo upload dir, preview dir
$eparam['cachedir'] = $d3dConf->params['cachedir'];

$check_dir = array(
	$eparam['uploaddir']	,
	$eparam['uploaddir'].'/'.$eparam['previewdir']	,
	$eparam['cachedir']
);

$check_dir = array_unique($check_dir);
sort($check_dir);

$dir_res = array();

foreach($check_dir as $dir){
	$dir = rtrim($dir, '/');
	if (is_writable($dir)) {
		$dir .= ' (<span style="color:green;font-weight:bold;">OK</span>)';
	} else {
		$dir .= ' (<span style="color:red;font-weight:bold;">NG</span>)';
	}
	$dir_res[] = $dir;
}

$dir_res = '<ul><li>'.join('</li><li>', $dir_res).'</li></ul>';

$check_funcs = array(
	'imagecreatefromgif',
	'imagecreatefromjpeg',
	'imagecreatefrompng',
	'imagecreatetruecolor',
	'imagecopyresampled'
	);
	
$func_res = array();

foreach($check_funcs as $_func){
		$_rslt = $_func;
	if (function_exists($_func)){
		$_rslt .= ' (<span style="color:green;font-weight:bold;">OK</span>)';
	} else {
		$_rslt .= ' (<span style="color:red;font-weight:bold;">NG</span>)';
	}
	$func_res[] = $_rslt;
}

$func_res = '<ul><li>'.join('</li><li>', $func_res).'</li></ul>';

// output
xoops_cp_header() ;

include dirname(__FILE__).'/mymenu.php' ;

echo <<<EOD

<h3>Writable check results</h3>
$dir_res

<h3>Function check results</h3>
$func_res

EOD;

xoops_cp_footer() ;

?>