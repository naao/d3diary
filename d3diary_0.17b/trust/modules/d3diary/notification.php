<?php

eval( '
function '.$mydirname.'_notify_iteminfo( $category, $item_id )
{
	return d3diary_notify_base( "'.$mydirname.'" , $category , $item_id ) ;
}
' ) ;

//if( ! function_exists( 'd3diary_notify_base' ) ) {

   function d3diary_notify_base( $mydirname , $category , $item_id )
   {
	$db =& Database::getInstance() ;

	$module_handler =& xoops_gethandler( 'module' ) ;
	$module =& $module_handler->getByDirname( $mydirname ) ;
	$mid = intval($module->getVar('mid'));

	$config_handler =& xoops_gethandler("config");
	$mod_config = $config_handler->getConfigsByCat(0, $mid);

	if( $category == 'global' ) {
		$item['name'] = '';
		$item['url'] = '';
		return $item ;
	}

	if( $category == 'blogger' ) {
		$user_handler =& xoops_gethandler("user") ;
		$yd_user_obj =& $user_handler->get( intval($item_id) ) ;
		if( is_object( $yd_user_obj ) ) {
			$yd_uname = $mod_config['use_name']==1 ? 
					$yd_user_obj->getVar("name") : $yd_user_obj->getVar("uname");
		}
		$item["name"] = $yd_uname.constant('_MI_'. strtoupper( $mydirname ).'_BLOGGER') ;
		//$item["name"] = $yd_uname ;
		$item["url"] = XOOPS_URL . "/modules/". $mydirname ."/index.php?req_uid=$item_id" ;
		return $item ;
	}
	
	if( $category == 'entry' ) {
		// Assume we have a valid event_id
		$sql = "SELECT title FROM ".$db->prefix($mydirname."_diary")." WHERE bid='$item_id'";
		$rs = $db->query( $sql ) ;
		list( $title ) = $db->fetchRow( $rs ) ;
		$item["name"] = $title ;
		$item["url"] = XOOPS_URL . "/modules/". $mydirname ."/index.php?page=detail&amp;bid=$item_id" ;
		return $item ;
	}

   }

//}

?>
