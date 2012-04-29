<?php
require_once dirname( dirname(__FILE__) ).'/class/gtickets.php' ;
require_once dirname( dirname(__FILE__) ).'/include/import_functions.php' ;

// get importable modules list
$module_handler =& xoops_gethandler( 'module' ) ;
$modules =& $module_handler->getObjects() ;
$importable_modules = array() ;
$comimportable_modules = array() ;
$notifimportable_modules = array() ;

foreach( $modules as $module ) {
	$mid = $module->getVar('mid') ;
	$dirname = $module->getVar('dirname') ;
	$dirpath = XOOPS_ROOT_PATH.'/modules/'.$dirname ;
	$mytrustdirname = '' ;
	$tables = $module->getInfo('tables') ;
	$version = intval( $module->getVar('version'));
	if( file_exists( $dirpath.'/mytrustdirname.php' ) ) {
		include $dirpath.'/mytrustdirname.php' ;
	}
	if( $mytrustdirname == 'd3diary' && $dirname != $mydirname ) {
		// d3diary
		$importable_modules[$mid] = 'd3diary:'.$module->getVar('name')." ( $dirname )" ;
            	if($module->getVar('hascomments')) {
			$comimportable_modules[ $mid ] = 'd3diary:'. $module->getVar('name')." ( $dirname )";
			$notifimportable_modules[ $mid ] = 'd3diary:'. $module->getVar('name')." ( $dirname )";
		}
	} elseif( $mytrustdirname != 'd3diary' && $dirname == 'minidiary' ) {
		// minidiary
		$importable_modules[$mid] = 'minidiary:'.$module->getVar('name')." ($dirname)" ;
            	if($module->getVar('hascomments')) {
			$comimportable_modules[ $mid ] = 'minidiary:'. $module->getVar('name')." ( $dirname )";
			$notifimportable_modules[ $mid ] = 'minidiary:'. $module->getVar('name')." ( $dirname )";
		}
	} elseif( $mytrustdirname == 'd3blog' ) {
		// d3blog
		$importable_modules[$mid] = 'd3blog:'.$module->getVar('name')." ($dirname)" ;
            	if($module->getVar('hascomments')) {
			$comimportable_modules[ $mid ] = 'd3blog:'. $module->getVar('name')." ( $dirname )";
			$notifimportable_modules[ $mid ] = 'd3blog:'. $module->getVar('name')." ( $dirname )";
		}
	} elseif( $mytrustdirname == 'weblogD3' ) {
		// d3blog
		$importable_modules[$mid] = 'weblogD3:'.$module->getVar('name')." ($dirname)" ;
            	if($module->getVar('hascomments')) {
			$comimportable_modules[ $mid ] = 'weblogD3:'. $module->getVar('name')." ( $dirname )";
			$notifimportable_modules[ $mid ] = 'weblogD3:'. $module->getVar('name')." ( $dirname )";
		}
	}
}


	$module =& $module_handler->getByDirname( $mydirname ) ;
	$mid = $module->getVar('mid');

// TRANSACTION STAGE

if( ! empty( $_POST['do_import'] ) && ! empty( $_POST['import_mid'] ) ) {
	set_time_limit( 0 ) ;

	if ( ! $xoopsGTicket->check( true , 'd3diary_admin' ) ) {
		redirect_header(XOOPS_URL.'/',3,$xoopsGTicket->getErrors());
	}

	$import_mid = intval( @$_POST['import_mid'] ) ;
	if( empty( $importable_modules[ $import_mid ] ) ) die( _MD_D3DIARY_ERR_INVALIDMID ) ;
	list( $fromtype , ) = explode( ':' , $importable_modules[ $import_mid ] ) ;
	switch( $fromtype ) {
		case 'd3diary' :
			d3diary_import_from_d3diary( $mydirname , $import_mid ) ;
			break ;
		case 'minidiary' :
			d3diary_import_from_minidiary( $mydirname , $import_mid ) ;
			break ;
		case 'd3blog' :
			d3diary_import_from_d3blog( $mydirname , $import_mid ) ;
			break ;
		case 'weblogD3' :
			d3diary_import_from_weblogD3( $mydirname , $import_mid ) ;
			break ;
	}
	redirect_header( XOOPS_URL."/modules/$mydirname/admin/index.php?page=import" , 3 , $fromtype._MD_D3DIARY_IMPORTDONE ) ;
	exit ;
}

if( ! empty( $_POST['do_comimport'] ) && ! empty( $_POST['comimport_mid'] ) ) {
        set_time_limit( 0 );    /* invalid when safe_mode is on */

    	if ( ! $xoopsGTicket->check( true , 'd3diary_admin' ) ) {
        	redirect_header(XOOPS_URL.'/',3,$xoopsGTicket->getErrors());
    	}

    	$import_mid = intval( @$_POST['comimport_mid'] ) ;
    	if( !in_array( $import_mid, array_keys($comimportable_modules)) ) die( _MD_D3DIARY_ERR_INVALIDMID );
    	list( $fromtype , $from_name) = explode( ':', $comimportable_modules[ $import_mid ] ) ;

    	//preg_match('/([A-Za-z0-9_-]+)\)$/i', $from_name, $matches);
    	//$import_dirname = $from_name;

    	d3diary_import_comments($mid, $import_mid);

	redirect_header( XOOPS_URL."/modules/$mydirname/admin/index.php?page=import" , 3 , $fromtype._MD_D3DIARY_IMPORTDONE ) ;
	exit ;
}

if( ! empty( $_POST['do_notifimport'] ) && ! empty( $_POST['notifimport_mid'] ) ) {
        set_time_limit( 0 );    /* invalid when safe_mode is on */

    	if ( ! $xoopsGTicket->check( true , 'd3diary_admin' ) ) {
        	redirect_header(XOOPS_URL.'/',3,$xoopsGTicket->getErrors());
    	}
    	$import_mid = intval( @$_POST['notifimport_mid'] ) ;
    	if( !in_array( $import_mid, array_keys($notifimportable_modules)) ) die( _MD_D3DIARY_ERR_INVALIDMID );
    	list( $fromtype , $from_name) = explode( ':', $notifimportable_modules[ $import_mid ] ) ;

    	//preg_match('/([A-Za-z0-9_-]+)\)$/i', $from_name, $matches);
    	//$import_dirname = $from_name;

    	d3diary_import_notifications($mid, $import_mid);

	redirect_header( XOOPS_URL."/modules/$mydirname/admin/index.php?page=import" , 3 , $fromtype._MD_D3DIARY_IMPORTDONE ) ;
	exit ;

}
//
// display stage
//

xoops_cp_header();
include dirname(__FILE__).'/mymenu.php' ;
$tpl = new XoopsTpl() ;
$tpl->assign( array(
	'mydirname' => $mydirname ,
	'mod_name' => $xoopsModule->getVar('name') ,
	'mod_url' => XOOPS_URL.'/modules/'.$mydirname ,
	'mod_config' => $xoopsModuleConfig ,
	'import_from_options' => $importable_modules ,
	'comimport_from_options' => $comimportable_modules ,
	'notifimport_from_options' => $notifimportable_modules ,
	'gticket_hidden' => $xoopsGTicket->getTicketHtml( __LINE__ , 1800 , 'd3diary_admin') ,
) ) ;
$tpl->display( 'db:'.$mydirname.'_admin_import.html' ) ;
xoops_cp_footer();

?>