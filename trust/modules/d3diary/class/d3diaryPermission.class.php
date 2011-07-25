<?php

if( ! defined( 'XOOPS_ROOT_PATH' ) ) exit ;

if( ! preg_match( '/^[0-9a-zA-Z_-]+$/' , $mydirname ) ) exit ;

if( ! class_exists( 'd3diaryPermission' ) ) {

require_once dirname(__FILE__).'/d3diaryPermissionAbstract.class.php' ;

// singleton
class d3diaryPermission extends d3diaryPermissionAbstract{

public function __construct( & $d3dConf )
{
	if (!defined('XOOPS_ROOT_PATH')) {
	    exit();
	}

	$this->d3dConf = & $d3dConf;
	$this->ini_set();
	
	
 }

function execute( $request )
{
	parent::execute( $request ) ;
}

} //end class
}

if( ! class_exists( $mydirname .'_d3diaryPermission' ) ) {

	eval( 'class '. $mydirname .'_d3diaryPermission extends d3diaryPermission { var $mydirname = "'.$mydirname.'" ; }' ) ;

}

?>