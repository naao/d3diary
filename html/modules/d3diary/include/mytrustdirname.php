<?php

if( ! defined( 'XOOPS_TRUST_PATH' ) ) die( 'not permit' ) ;

//=========================================================
// main
//=========================================================
$GLOBALS['MY_DIRNAME'] = $MY_DIRNAME;

$MY_TRUST_DIRNAME = 'd3diary' ;

if ( !defined("D3DIARY_TRUST_DIRNAME") ) {
	define("D3DIARY_TRUST_DIRNAME", $MY_TRUST_DIRNAME );
}
if ( !defined("D3DIARY_TRUST_PATH") ) {
	define("D3DIARY_TRUST_PATH", XOOPS_TRUST_PATH.'/modules/'.D3DIARY_TRUST_DIRNAME );
}

?>