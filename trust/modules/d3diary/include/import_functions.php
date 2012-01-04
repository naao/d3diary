<?php

if ( ! function_exists('d3diary_import_from_d3diary') ) {
	function d3diary_import_from_d3diary( $mydirname , $import_mid )
	{
		include_once dirname(__FILE__).'/mytable.php' ;

		$db =& Database::getInstance() ;
		$import_mid = intval( $import_mid ) ;

		$module_handler = & xoops_gethandler( 'module' ) ;
		$from_module =& $module_handler->get( $import_mid ) ;
		$from_dirname = $from_module->getVar('dirname') ;
		foreach( $GLOBALS['d3diary_tables'] as $table_name => $columns ) {
			$from_table = $db->prefix( $from_dirname.'_'.$table_name ) ;
			$to_table = $db->prefix( $mydirname.'_'.$table_name ) ;
			$columns4sql = implode( ',' , $columns ) ;
			$db->query( "DELETE FROM `$to_table`" ) ;
			$irs = $db->query( "INSERT INTO `$to_table` ( $columns4sql ) SELECT $columns4sql FROM `$from_table`" ) ;
			if( ! $irs ) redirect_header( XOOPS_URL."/modules/$mydirname/admin/index.php?page=import" , 3 , $from_table._MD_IMPORTERROR ) ;
		}

		// deleted dayly cout up 
		$db->queryF("DELETE FROM ".$db->prefix($mydirname."_cnt")." WHERE ymd<>'1111-11-11'");

		// trackback table
		d3diary_import_trackbacks($mydirname , $from_dirname );

	}
}

if ( ! function_exists('d3diary_import_from_minidiary') ) {
	function d3diary_import_from_minidiary( $mydirname , $import_mid )
	{
		$db =& Database::getInstance() ;
		$import_mid = intval( $import_mid ) ;

		// get name of `contents` table 
		$module_handler =& xoops_gethandler( 'module' ) ;
		$module =& $module_handler->get( $import_mid ) ;
		$target_dirname = $module->getVar('dirname') ;

		// category 
		$from_table = $db->prefix( 'yd_category' ) ;
		$to_table = $db->prefix( $mydirname.'_category' ) ;
		$db->query( "DELETE FROM `$to_table`" ) ;
		$irs = $db->query( "INSERT INTO `$to_table` (uid,cid,cname,corder,blogtype,blogurl,rss,openarea) SELECT uid,cid,cname,corder,'0','','','0' FROM `$from_table`" ) ;
		if( ! $irs ) redirect_header( XOOPS_URL."/modules/$mydirname/admin/index.php?page=import" , 3 , $from_table._MD_IMPORTERROR."1" ) ;

		// cnt 
		$from_table = $db->prefix( 'yd_cnt' ) ;
		$to_table = $db->prefix( $mydirname.'_cnt' ) ;
		$db->query( "DELETE FROM `$to_table`" ) ;
		$irs = $db->query( "INSERT INTO `$to_table` (uid,cnt,ymd) SELECT uid,cnt,ymd FROM `$from_table` WHERE ymd='1111-11-11'" ) ;
		if( ! $irs ) redirect_header( XOOPS_URL."/modules/$mydirname/admin/index.php?page=import" , 3 , $from_table._MD_IMPORTERROR."2" ) ;

		// cnt_ip 
		$from_table = $db->prefix( 'yd_cnt_ip' ) ;
		$to_table = $db->prefix( $mydirname.'_cnt_ip' ) ;
		$db->query( "DELETE FROM `$to_table`" ) ;
		$irs = $db->query( "INSERT INTO `$to_table` (uid,accip,acctime) SELECT uid,accip,acctime FROM `$from_table`" ) ;
		if( ! $irs ) redirect_header( XOOPS_URL."/modules/$mydirname/admin/index.php?page=import" , 3 , $from_table._MD_IMPORTERROR."3" ) ;

		// config 
		$from_table = $db->prefix( 'yd_config' ) ;
		$to_table = $db->prefix( $mydirname.'_config' ) ;
		$db->query( "DELETE FROM `$to_table`" ) ;
		$irs = $db->query( "INSERT INTO `$to_table` (uid,blogtype,blogurl,rss,openarea,mailpost,address,keep,uptime,updated) SELECT uid,blogtype,blogurl,rss,openarea,'0','','0','','' FROM `$from_table`" ) ;
		if( ! $irs ) redirect_header( XOOPS_URL."/modules/$mydirname/admin/index.php?page=import" , 3 , $from_table._MD_IMPORTERROR."4" ) ;

		// diary 
		$from_table = $db->prefix( 'yd_diary' ) ;
		$to_table = $db->prefix( $mydirname.'_diary' ) ;
		$db->query( "DELETE FROM `$to_table`" ) ;
		$irs = $db->query( "INSERT INTO `$to_table` (bid,cid,uid,title,diary,update_time,create_time,openarea) SELECT bid,cid,uid,title,diary,update_time,create_time,'0' FROM `$from_table`" ) ;
		if( ! $irs ) redirect_header( XOOPS_URL."/modules/$mydirname/admin/index.php?page=import" , 3 , $from_table._MD_IMPORTERROR."5" ) ;

		// newentry 
		$from_table = $db->prefix( 'yd_newentry' ) ;
		$to_table = $db->prefix( $mydirname.'_newentry' ) ;
		$db->query( "DELETE FROM `$to_table`" ) ;
		$irs = $db->query( "INSERT INTO `$to_table` (uid,cid,title,url,create_time,blogtype,diary) SELECT uid,'0',title,url,create_time,blogtype,diary FROM `$from_table`" ) ;
		if( ! $irs ) redirect_header( XOOPS_URL."/modules/$mydirname/admin/index.php?page=import" , 3 , $from_table._MD_IMPORTERROR."6" ) ;

		// photo 
		$from_table = $db->prefix( 'yd_photo' ) ;
		$to_table = $db->prefix( $mydirname.'_photo' ) ;
		$db->query( "DELETE FROM `$to_table`" ) ;
		$irs = $db->query( "INSERT INTO `$to_table` (uid,bid,pid,ptype,tstamp) SELECT uid,bid,pid,ptype,tstamp FROM `$from_table`" ) ;
		if( ! $irs ) redirect_header( XOOPS_URL."/modules/$mydirname/admin/index.php?page=import" , 3 , $from_table._MD_IMPORTERROR."7" ) ;

		// deleted dayly cout up 
		$db->queryF("DELETE FROM ".$db->prefix($mydirname."_cnt")." WHERE ymd<>'1111-11-11'");

	}
}

if ( ! function_exists('d3diary_import_from_d3blog') ) {
	function d3diary_import_from_d3blog( $mydirname , $import_mid )
	{
		$db =& Database::getInstance() ;
		$import_mid = intval( $import_mid ) ;

		// get name of `contents` table 
		$module_handler =& xoops_gethandler( 'module' ) ;
		$from_module =& $module_handler->get( $import_mid ) ;
		$from_dirname = $from_module->getVar('dirname') ;

		// diary 
		$gALL = 0;
		$gADM = (int)XOOPS_GROUP_ADMIN;
		$gUSR = (int)XOOPS_GROUP_USERS;
		$gANO = (int)XOOPS_GROUP_ANONYMOUS;

			// translate d3blog's group permission to d3diary's openarea entry
		$slct_openarea = "IF(`groups` LIKE '%|".$gALL."|%','', IF(`groups` LIKE '%|".$gANO."|%','',
					IF(`groups` LIKE '%|".$gUSR."|%','1','10'))) ";

			// erase d3blog's unneccesary group permission 
		$slct_groups_sub = "TRIM(LEADING '|".$gANO."' FROM TRIM(LEADING '|".$gUSR."' 
					FROM TRIM(LEADING '|".$gADM."' FROM TRIM(LEADING '|".$gALL."' FROM groups))))";
		$slct_groups = "IF(".$slct_openarea." LIKE '10',".$slct_groups_sub.",'')";

		$from_table = $db->prefix( $from_dirname.'_entry' ) ;
		$to_table = $db->prefix( $mydirname.'_diary' ) ;
		$db->query( "DELETE FROM `$to_table`" ) ;
		$irs = $db->query( "INSERT INTO `$to_table` (bid,cid,uid,title,diary,update_time,create_time,dohtml,openarea,vgids,view) 
			SELECT bid,cid+10000,uid,title,TRIM(TRAILING '[pagebreak]\n' FROM concat(excerpt,'[pagebreak]\n',body)),
				FROM_UNIXTIME(modified),FROM_UNIXTIME(created),dohtml,".$slct_openarea.",".$slct_groups.",counter 
				FROM `$from_table`" ) ;
		if( ! $irs ) redirect_header( XOOPS_URL."/modules/$mydirname/admin/index.php?page=import" , 3 , $from_table._MD_IMPORTERROR ) ;

		// category 
			// process subcat
		$slct_subcat = "IF(`pid`>0,'1','0')";
		$from_table = $db->prefix( $from_dirname.'_category' ) ;
		$to_table = $db->prefix( $mydirname.'_category' ) ;
		$db->query( "DELETE FROM `$to_table`" ) ;
		$irs = $db->query( "INSERT INTO `$to_table` (uid,cid,cname,corder,subcat,blogtype,blogurl,rss,openarea,vpids) SELECT '0',cid+10000,name,weight,".$slct_subcat.",'0','','','0',pid+10000 FROM `$from_table`" ) ;
		if( ! $irs ) redirect_header( XOOPS_URL."/modules/$mydirname/admin/index.php?page=import" , 3 , $from_table._MD_IMPORTERROR ) ;
		

		$sql = "SELECT * FROM `$to_table` ORDER BY vpids, corder";
		$result = $db->query($sql);

		while ( $dbdat[] = $db->fetchArray($result) ) {	}

		$i=0; $k=0;
		foreach ( $dbdat as $dbrow ) {
			$j=0;
			if($dbrow['subcat']==0){
				$dbdat[$i]['corder'] = $k+10000 ;
				$k++;
				foreach ( $dbdat as $dbrow2 ) {
					if( $dbrow['cid']==(int)$dbrow2['vpids'] ) {
						$dbdat[$j]['corder'] = $k+10000 ;
						$k++;
					}
					$j++;
				}
			}
			$i++;
		}

		foreach ( $dbdat as $dbrow ) {
			$sql = "UPDATE `$to_table` SET 
					corder='".$dbrow['corder']."', vpids='' 
					WHERE cid=".$dbrow['cid'];
			$irs = $db->query($sql);
			$i++;
		}
		
		// trackbacks
		d3diary_import_trackbacks($mydirname , $from_dirname );
		
	}
}

if ( ! function_exists('d3diary_import_trackbacks') ) {
	function d3diary_import_trackbacks($mydirname , $from_dirname )
	{
		$db =& Database::getInstance();
		$from_table = $db->prefix( $from_dirname.'_trackback' ) ;
		$to_table = $db->prefix( $mydirname.'_trackback' ) ;

		// trackbacks
	    $check_sql = "SELECT tid FROM `$from_table` ";
	    if( $db->query( $check_sql ) ) {
		$check_sql = "SELECT tid FROM `$to_table` ";
		if( ! $db->query( $check_sql ) ) {
			$db->queryF( "CREATE TABLE `$to_table`  (
				tid int(8) NOT NULL auto_increment,
				bid int(8) NOT NULL,
				blog_name varchar(255) NOT NULL,
				title varchar(255) NOT NULL,
				excerpt text NOT NULL,
				url varchar(150) NOT NULL,
				trackback_url varchar(150) NOT NULL,
				direction int(1) NOT NULL default '0',
				`host` varchar(15) NOT NULL,
				tbkey varchar(12) NOT NULL,
				approved int(1) NOT NULL default '0',
				created int(10) NOT NULL default '0',
				PRIMARY KEY (tid),
				KEY bid (bid),
				KEY tbkey (tbkey),
				KEY trackback_url (trackback_url)
				) ENGINE=MyISAM" );
		}
		$db->query( "DELETE FROM `$to_table`" ) ;
		$irs = $db->query( "INSERT INTO `$to_table` 
				(tid,bid,blog_name,title,excerpt,url,trackback_url,direction,host,tbkey,approved,created) 
				SELECT tid,bid,blog_name,title,excerpt,url,trackback_url,direction,host,tbkey,approved,created 
				FROM `$from_table` " ) ;
		if( ! $irs ) redirect_header( XOOPS_URL."/modules/$mydirname/admin/index.php?page=import" , 3 , $from_table._MD_IMPORTERROR ) ;
	    }
	}
}

if ( ! function_exists('d3diary_import_comments') ) {
	function d3diary_import_comments($to_mid , $from_mid )
	{
		$db =& Database::getInstance();
		$table = $db->prefix( 'xoopscomments' ) ;
	    	$sql = "UPDATE ".$table." SET com_modid=".intval($to_mid)." WHERE com_modid=".intval($from_mid);
		$irs = $db->query( $sql ) ;
	    	if(!$irs ) redirect_header( XOOPS_URL."/modules/$mydirname/admin/index.php?page=import" , 3 ,_MD_IMPORTERROR ) ;
	    	
	}

}

if ( ! function_exists('d3diary_import_notifications') ) {
	function d3diary_import_notifications($to_mid, $from_mid)
	{
		$db =& Database::getInstance();
		$table = $db->prefix( 'xoopsnotifications' ) ;
	    	$sql = "UPDATE ".$table." SET not_modid=".intval($to_mid)." WHERE not_modid=".intval($from_mid);
		$irs = $db->query( $sql ) ;
	    	if(!$irs ) redirect_header( XOOPS_URL."/modules/$mydirname/admin/index.php?page=import" , 3 ,_MD_IMPORTERROR ) ;
	}
}

if ( ! function_exists('d3diary_import_from_weblogD3') ) {
	function d3diary_import_from_weblogD3( $mydirname , $import_mid )
	{
		$db =& Database::getInstance() ;
		$import_mid = intval( $import_mid ) ;

		// get name of `contents` table 
		$module_handler =& xoops_gethandler( 'module' ) ;
		$from_module =& $module_handler->get( $import_mid ) ;
		$from_dirname = $from_module->getVar('dirname') ;

		// diary 
		$gALL = 'all';
		$gADM = (int)XOOPS_GROUP_ADMIN;
		$gUSR = (int)XOOPS_GROUP_USERS;
		$gANO = (int)XOOPS_GROUP_ANONYMOUS;

			// translate weblogD3's group permission to d3diary's openarea entry
		$slct_openarea = "IF(`private` LIKE 'Y',100, IF(`permission_group` LIKE '".$gALL."','', 
					IF(`permission_group` LIKE '%|".$gANO."|%','',
					IF(`permission_group` LIKE '%|".$gUSR."|%','1','10')))) ";

			// erase d3blog's unneccesary group permission 
		$slct_groups_sub = "TRIM(LEADING '|".$gANO."' FROM TRIM(LEADING '|".$gUSR."' 
					FROM TRIM(LEADING '|".$gADM."' FROM TRIM(LEADING '".$gALL."' FROM permission_group))))";
		$slct_groups = "IF(".$slct_openarea." LIKE '10',".$slct_groups_sub.",'')";

		$weblog_pagebreak = "'---UnderThisSeparatorIsLatterHalf---'";
		$from_table = $db->prefix( $from_dirname.'_entry' ) ;
		$to_table = $db->prefix( $mydirname.'_diary' ) ;
		$db->query( "DELETE FROM `$to_table`" ) ;
		$sql =  "INSERT INTO `$to_table` (bid,cid,uid,title,diary,update_time,create_time,dohtml,openarea,vgids,view) 
			SELECT blog_id,cat_id+10000,user_id,title,REPLACE(contents, $weblog_pagebreak, '[pagebreak]\n'),
				FROM_UNIXTIME(created),FROM_UNIXTIME(created),dohtml,".$slct_openarea.",".$slct_groups.",`reads` 
				FROM `$from_table`" ;
		$irs = $db->query( $sql ) ;
		if( ! $irs ) redirect_header( XOOPS_URL."/modules/$mydirname/admin/index.php?page=import" , 3 , $from_table._MD_IMPORTERROR ) ;

		// category 
			// process subcat
		$slct_subcat = "IF(`cat_pid`>0,'1','0')";
		$from_table = $db->prefix( $from_dirname.'_category' ) ;
		$to_table = $db->prefix( $mydirname.'_category' ) ;
		$db->query( "DELETE FROM `$to_table`" ) ;
		$irs = $db->query( "INSERT INTO `$to_table` (uid,cid,cname,corder,subcat,blogtype,blogurl,rss,openarea,vpids) SELECT '0',cat_id+10000,cat_title,'0',".$slct_subcat.",'0','','','0',cat_pid+10000 FROM `$from_table`" ) ;
		if( ! $irs ) redirect_header( XOOPS_URL."/modules/$mydirname/admin/index.php?page=import" , 3 , $from_table._MD_IMPORTERROR ) ;
		

		$sql = "SELECT * FROM `$to_table` ORDER BY vpids, corder";
		$result = $db->query($sql);

		while ( $dbdat[] = $db->fetchArray($result) ) {	}

		$i=0; $k=0;
		foreach ( $dbdat as $dbrow ) {
			$j=0;
			if($dbrow['subcat']==0){
				$dbdat[$i]['corder'] = $k+10000 ;
				$k++;
				foreach ( $dbdat as $dbrow2 ) {
					if( $dbrow['cid']==(int)$dbrow2['vpids'] ) {
						$dbdat[$j]['corder'] = $k+10000 ;
						$k++;
					}
					$j++;
				}
			}
			$i++;
		}

		foreach ( $dbdat as $dbrow ) {
			$sql = "UPDATE `$to_table` SET 
					corder='".$dbrow['corder']."', vpids='' 
					WHERE cid=".$dbrow['cid'];
			$irs = $db->query($sql);
			$i++;
		}
		
		// trackbacks
		//d3diary_import_trackbacks_weblogD3($mydirname , $from_dirname );
		
	}
}

?>