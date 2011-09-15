<?php

$mytrustdirname = basename( dirname( __FILE__ ) ) ;
$mytrustdirpath = dirname( __FILE__ ) ;

// check permission of 'module_read' of this module
// (already checked by common.php)

$langmanpath = XOOPS_TRUST_PATH.'/libs/altsys/class/D3LanguageManager.class.php' ;
if( ! file_exists( $langmanpath ) ) die( 'install the latest altsys' ) ;
require_once( $langmanpath ) ;
$langman =& D3LanguageManager::getInstance() ;
$langman->read( 'main.php' , $mydirname , $mytrustdirname ) ;


$page = preg_replace( '/[^a-zA-Z0-9_-]/' , '' , @$_GET['page'] ) ;
if( empty( $page ) ) {
	preg_match( '/[?&]page\=([a-zA-Z0-9_-]+)/' , @$_SERVER['REQUEST_URI'] , $regs ) ;
	$page = @$regs[1] ;
}

$d3diary_meta_description = '';

// fork each pages
if( file_exists( "$mytrustdirpath/main/$page.php" ) ) {
	include "$mytrustdirpath/main/$page.php" ;
} else {
	include "$mytrustdirpath/main/index.php" ;
}

if( $d3dConf->include_footer == true ) {

	// For XCL 2.2 Call addMeta
	if ($d3diary_meta_description) {
		if (defined('LEGACY_MODULE_VERSION') && version_compare(LEGACY_MODULE_VERSION, '2.2', '>=')) {
			$xclRoot =& XCube_Root::getSingleton();
			$headerScript = $xclRoot->mContext->getAttribute('headerScript');
			$headerScript->addMeta('description', $d3diary_meta_description);
		} elseif (is_object($xoTheme)) {
			$xoTheme->addMeta('meta', 'description', $d3diary_meta_description);
		}
	}
	
	include_once XOOPS_ROOT_PATH.'/footer.php';
}

?>