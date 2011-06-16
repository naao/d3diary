<?php

eval( ' function xoops_module_update_'.$mydirname.'( $module ) { return d3diary_onupdate_base( $module , "'.$mydirname.'" ) ; } ' ) ;


if( ! function_exists( 'd3diary_onupdate_base' ) ) {

function d3diary_onupdate_base( $module , $mydirname )
{
	// transations on module update

	global $msgs ; // TODO :-D

	// for Cube 2.1
	if( defined( 'XOOPS_CUBE_LEGACY' ) ) {
		$root =& XCube_Root::getSingleton();
		$root->mDelegateManager->add( 'Legacy.Admin.Event.ModuleUpdate.' . ucfirst($mydirname) . '.Success', 'd3diary_message_append_onupdate' ) ;
		$msgs = array() ;
	} else {
		if( ! is_array( $msgs ) ) $msgs = array() ;
	}

	$db =& Database::getInstance() ;
	$mid = $module->getVar('mid') ;



	// TABLES (write here ALTER TABLE etc. if necessary)

	// configs (Though I know it is not a recommended way...)
	$check_sql = "SHOW COLUMNS FROM ".$db->prefix("config")." LIKE 'conf_title'" ;
	if( ( $result = $db->query( $check_sql ) ) && ( $myrow = $db->fetchArray( $result ) ) && @$myrow['Type'] == 'varchar(30)' ) {
		$db->queryF( "ALTER TABLE ".$db->prefix("config")." MODIFY `conf_title` varchar(255) NOT NULL default '', MODIFY `conf_desc` varchar(255) NOT NULL default ''" ) ;
	}

	// 0.01 -> 0.01
	$check_sql = "SELECT blogtype FROM ".$db->prefix($mydirname."_category") ;
	if( ! $db->query( $check_sql ) ) {
		$db->queryF( "ALTER TABLE ".$db->prefix($mydirname."_category")." ADD blogtype tinyint(3) NOT NULL default '0' AFTER corder" ) ;
		$db->queryF( "ALTER TABLE ".$db->prefix($mydirname."_category")." ADD blogurl text AFTER blogtype" ) ;
		$db->queryF( "ALTER TABLE ".$db->prefix($mydirname."_category")." ADD rss text AFTER blogurl" ) ;
		$db->queryF( "ALTER TABLE ".$db->prefix($mydirname."_category")." ADD openarea tinyint(3) NOT NULL AFTER rss" ); 
	}

	// 0.01 -> 0.01
	$check_sql = "SELECT openarea FROM ".$db->prefix($mydirname."_diary") ;
	if( ! $db->query( $check_sql ) ) {
		$db->queryF( "ALTER TABLE ".$db->prefix($mydirname."_diary")." ADD openarea tinyint(3) unsigned NOT NULL AFTER create_time" ) ;
	}
	
	// 0.01 -> 0.01
	$check_sql = "SELECT cid FROM ".$db->prefix($mydirname."_newentry") ;
	if( ! $db->query( $check_sql ) ) {
		$db->queryF( "ALTER TABLE ".$db->prefix($mydirname."_newentry")." ADD cid int(10) unsigned NOT NULL default '0' AFTER uid" ) ;
		$db->queryF( "ALTER TABLE ".$db->prefix($mydirname."_newentry")." DROP PRIMARY KEY" ) ;
		$db->queryF( "ALTER TABLE ".$db->prefix($mydirname."_newentry")." ADD PRIMARY KEY (uid,cid)" ) ;
	}
	
	// 0.01 -> 0.01
	$check_sql = "SELECT tag_id FROM ".$db->prefix($mydirname."_tag") ;
	if( ! $db->query( $check_sql ) ) {
		$db->queryF( "CREATE TABLE ".$db->prefix($mydirname."_tag")." ( tag_id int(11) unsigned NOT NULL auto_increment, tag_name varchar(64) NOT NULL default '',	bid int(11) unsigned NOT NULL default '0', uid mediumint(8) unsigned NOT NULL default '0',	tag_group int(11) unsigned NOT NULL default '0', reg_unixtime int(11) unsigned NOT NULL default '0', PRIMARY KEY  (tag_id), KEY (tag_name), KEY (bid), KEY (uid) ) TYPE=MyISAM" ) ;
	}
	
	// 0.01 -> 0.01
	// deleted dayly cout up 
	$db->queryF("DELETE FROM ".$db->prefix($mydirname."_cnt")." WHERE ymd<>'1111-11-11'");

	// 0.05 -> 0.06
	// add dohtml
	$check_sql = "SELECT dohtml FROM ".$db->prefix($mydirname."_diary") ;
	if( ! $db->query( $check_sql ) ) {
		$db->queryF( "ALTER TABLE ".$db->prefix($mydirname."_diary")." ADD dohtml tinyint(1) unsigned NOT NULL AFTER openarea" ) ;
	}
	
	$check_sql = "SELECT dohtml FROM ".$db->prefix($mydirname."_category") ;
	if( ! $db->query( $check_sql ) ) {
		$db->queryF( "ALTER TABLE ".$db->prefix($mydirname."_category")." ADD dohtml tinyint(1) unsigned NOT NULL AFTER openarea" ) ;
	}
	
	// 0.11a2 -> 0.11a3
	// add subcategory
	$check_sql = "SELECT subcat FROM ".$db->prefix($mydirname."_category") ;
	if( ! $db->query( $check_sql ) ) {
		$db->queryF( "ALTER TABLE ".$db->prefix($mydirname."_category")." ADD subcat tinyint(1) unsigned NOT NULL default '0' AFTER corder" ) ;
	}
	
	// 0.11a3 -> 0.12
	// add group and personal permissions
	$check_sql = "SELECT vgids FROM ".$db->prefix($mydirname."_category") ;
	if( ! $db->query( $check_sql ) ) {
		$db->queryF( "ALTER TABLE ".$db->prefix($mydirname."_category")." ADD vgids varchar(255) default NULL AFTER dohtml" ) ;
		$db->queryF( "ALTER TABLE ".$db->prefix($mydirname."_category")." ADD vpids varchar(255) default NULL AFTER vgids" ) ;
		$db->queryF( "ALTER TABLE ".$db->prefix($mydirname."_diary")." ADD vgids varchar(255) default NULL AFTER dohtml" ) ;
		$db->queryF( "ALTER TABLE ".$db->prefix($mydirname."_diary")." ADD vpids varchar(255) default NULL AFTER vgids" ) ;
	}
	// add page view
	$check_sql = "SELECT view FROM ".$db->prefix($mydirname."_diary") ;
	if( ! $db->query( $check_sql ) ) {
		$db->queryF( "ALTER TABLE ".$db->prefix($mydirname."_diary")." ADD view int(10) unsigned NOT NULL default '0' AFTER vpids" ) ;
	}
	
	// 0.12b2 -> 0.12b3
	// add photo info
	$check_sql = "SELECT info FROM ".$db->prefix($mydirname."_photo") ;
	if( ! $db->query( $check_sql ) ) {
		$db->queryF( "ALTER TABLE ".$db->prefix($mydirname."_photo")." ADD info text AFTER tstamp" ) ;
	}
	
	// 0.13c -> 0.14
	// cppy tag registered unixtime from each diary's create_time
	$sql = "SELECT t.tag_id, d.create_time FROM ".$db->prefix($mydirname."_tag"). " t 
				INNER JOIN ".$db->prefix($mydirname."_diary")." d USING(bid) 
				WHERE t.bid=d.bid AND t.reg_unixtime='0'";
	if( $result = $db->query( $sql ) ) {
		while ( $dbdat = $db->fetchArray($result) ) {
			$ctime = split("[-: ]", $dbdat['create_time']);
			$tstamp = mktime($ctime[3],$ctime[4],$ctime[5],$ctime[1],$ctime[2],$ctime[0]);
			$sql = "UPDATE ".$db->prefix($mydirname."_tag")." SET 
					reg_unixtime='".$tstamp."' WHERE tag_id='".$dbdat['tag_id']."'";
			$irs = $db->query($sql);
		}
	}
	
	// 0.15 -> 0.16
	// add mail post
	$check_sql = "SELECT mailpost FROM ".$db->prefix($mydirname."_config") ;
	if( ! $db->query( $check_sql ) ) {
		$db->queryF( "ALTER TABLE ".$db->prefix($mydirname."_config")." 
			ADD `mailpost` tinyint(1) unsigned NOT NULL default '0' AFTER openarea" ) ;
		$db->queryF( "ALTER TABLE ".$db->prefix($mydirname."_config")." 
			ADD `address` varchar(255) NOT NULL default '' AFTER mailpost" ) ;
		$db->queryF( "ALTER TABLE ".$db->prefix($mydirname."_config")." 
			ADD `keep` tinyint(1) NOT NULL default '0' AFTER address" ) ;
		$db->queryF( "ALTER TABLE ".$db->prefix($mydirname."_config")." 
			ADD `uptime` int(10) unsigned NOT NULL default '0' AFTER keep" ) ;
		$db->queryF( "ALTER TABLE ".$db->prefix($mydirname."_config")." 
			ADD `updated` int(10) unsigned NOT NULL default '0' AFTER uptime" ) ;
	}
	
	// 0.18
	// modify photo `tstamp` column data type from timestamp to datetime
	$result = mysql_query("SELECT `tstamp` FROM ".$db->prefix($mydirname."_photo")) ;
	$field_type  = mysql_field_type($result, 0);
	if ( $field_type == "timestamp" ) {
		$db->queryF( "ALTER TABLE ".$db->prefix($mydirname."_photo")." modify `tstamp` datetime NOT NULL" ) ;
		// for NULL tstamp, copy from diary's create_time
		$sql = "UPDATE ".$db->prefix($mydirname."_photo"). " p 
				INNER JOIN ".$db->prefix($mydirname."_diary")." d USING(bid) 
				SET p.tstamp=d.create_time WHERE (p.tstamp IS NULL) OR (p.tstamp = '0000-00-00 00:00:00')";
		$result = $db->queryF( $sql ) ;
	}
	
	// TEMPLATES (all templates have been already removed by modulesadmin)
	$tplfile_handler =& xoops_gethandler( 'tplfile' ) ;
	$tpl_path = dirname(__FILE__).'/templates' ;
	if( $handler = @opendir( $tpl_path . '/' ) ) {
		while( ( $file = readdir( $handler ) ) !== false ) {
			if( substr( $file , 0 , 1 ) == '.' ) continue ;
			$file_path = $tpl_path . '/' . $file ;
			if( is_file( $file_path ) ) {
				$mtime = intval( @filemtime( $file_path ) ) ;
				$tplfile =& $tplfile_handler->create() ;
				$tplfile->setVar( 'tpl_source' , file_get_contents( $file_path ) , true ) ;
				$tplfile->setVar( 'tpl_refid' , $mid ) ;
				$tplfile->setVar( 'tpl_tplset' , 'default' ) ;
				$tplfile->setVar( 'tpl_file' , $mydirname . '_' . $file ) ;
				$tplfile->setVar( 'tpl_desc' , '' , true ) ;
				$tplfile->setVar( 'tpl_module' , $mydirname ) ;
				$tplfile->setVar( 'tpl_lastmodified' , $mtime ) ;
				$tplfile->setVar( 'tpl_lastimported' , 0 ) ;
				$tplfile->setVar( 'tpl_type' , 'module' ) ;
				if( ! $tplfile_handler->insert( $tplfile ) ) {
					$msgs[] = '<span style="color:#ff0000;">ERROR: Could not insert template <b>'.htmlspecialchars($mydirname.'_'.$file).'</b> to the database.</span>';
				} else {
					$tplid = $tplfile->getVar( 'tpl_id' ) ;
					$msgs[] = 'Template <b>'.htmlspecialchars($mydirname.'_'.$file).'</b> added to the database. (ID: <b>'.$tplid.'</b>)';
					// generate compiled file
					include_once XOOPS_ROOT_PATH.'/class/xoopsblock.php' ;
					include_once XOOPS_ROOT_PATH.'/class/template.php' ;
					if( ! xoops_template_touch( $tplid ) ) {
						$msgs[] = '<span style="color:#ff0000;">ERROR: Failed compiling template <b>'.htmlspecialchars($mydirname.'_'.$file).'</b>.</span>';
					} else {
						$msgs[] = 'Template <b>'.htmlspecialchars($mydirname.'_'.$file).'</b> compiled.</span>';
					}
				}
			}
		}
		closedir( $handler ) ;
	}

	include_once XOOPS_ROOT_PATH.'/class/xoopsblock.php' ;
	include_once XOOPS_ROOT_PATH.'/class/template.php' ;
	xoops_template_clear_module_cache( $mid ) ;

	return true ;
}

function d3diary_message_append_onupdate( &$module_obj , &$log )
{
	if( is_array( @$GLOBALS['msgs'] ) ) {
		foreach( $GLOBALS['msgs'] as $message ) {
			$log->add( strip_tags( $message ) ) ;
		}
	}

	// use mLog->addWarning() or mLog->addError() if necessary
}

}

?>