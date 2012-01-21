<?php

if( ! defined( 'XOOPS_ROOT_PATH' ) ) {
	// when this script is overwritten on core's imagemanager.php
	if( file_exists( 'mainfile.php' ) ) {
		require( 'mainfile.php' ) ;

	// when this script is called directly as this module's imagemanager
	} else {
		require( '../../mainfile.php' ) ;
	}
}

if( ! defined( 'XOOPS_TRUST_PATH' ) ) die( 'set XOOPS_TRUST_PATH in mainfile.php' ) ;

$MY_DIRNAME = basename( dirname( __FILE__ ) ) ;

require XOOPS_ROOT_PATH.'/modules/'.$MY_DIRNAME.'/include/mytrustdirname.php' ; // set $mytrustdirname
require XOOPS_TRUST_PATH.'/modules/'.$MY_TRUST_DIRNAME.'/main/imagemanager.php' ;

?>