<?php

require '../../mainfile.php' ;
if( ! defined( 'XOOPS_TRUST_PATH' ) ) die( 'set XOOPS_TRUST_PATH in mainfile.php' ) ;

$mydirname = basename( dirname( __FILE__ ) ) ;
$mydirpath = dirname( __FILE__ ) ;
require $mydirpath.'/mytrustdirname.php' ; // set $mytrustdirname

// this is wrapper value for d3blog alternative
//$_GET['req_uid'] = !empty($_GET['req_uid'] ) ? $_GET['req_uid'] : $_GET['uid'];
if (empty($_GET['req_uid'] ) && !empty($_GET['uid'] )) {
	$_GET['req_uid'] = $_GET['uid'];
}

if( @$_GET['mode'] == 'admin' ) {
	require XOOPS_TRUST_PATH.'/modules/'.$mytrustdirname.'/admin.php' ;
} else {
	require XOOPS_TRUST_PATH.'/modules/'.$mytrustdirname.'/main.php' ;
}

?>