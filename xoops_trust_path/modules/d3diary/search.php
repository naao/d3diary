<?php

eval( '

function '.$mydirname.'_global_search( $keywords , $andor , $limit , $offset , $userid )
{
	return d3diary_global_search_base( "'.$mydirname.'" , $keywords , $andor , $limit , $offset , $userid ) ;
}

' ) ;


if( ! function_exists( 'd3diary_global_search_base' ) ) {

function d3diary_global_search_base($mydirname , $queryarray, $andor, $limit, $offset, $userid)
{
	global $xoopsDB, $xoopsUser;
	
	require dirname(__FILE__).'/class/d3diaryConf.class.php';

	$d3dConf =& D3diaryConf::getInstance($mydirname, 0, "search");
	$func =& $d3dConf->func;
	$mod_config =& $d3dConf->mod_config;
	// for sanitizing contents
	$myts =& $d3dConf->myts;

	$ret = array();
#	if ( $userid != 0 ) {
#		return $ret;
#	}

	$uid = $d3dConf->uid;
	
	$editperm=0;
	if($d3dConf->mPerm->isadmin){$editperm=1;}

	$sql = "SELECT *
			FROM ".$xoopsDB->prefix($mydirname.'_config');
	$result = $xoopsDB->query($sql);

	$openarea = array();	$is_friend = array();	$is_friend2 = array();
	
	$showcontext = isset( $_GET['showcontext'] ) ? $_GET['showcontext'] : 0 ;
	if( $showcontext == 1){
		$q_diary = "d.diary,";
	}else{
		$q_diary = "";
	}
	
	if($d3dConf->mPerm->isadmin){
		$whr_openarea = " 1 ";
	} else {
		$_params4op['use_gp'] = $d3dConf->gPerm->use_gp;
		$_params4op['use_pp'] = $d3dConf->gPerm->use_pp;
		$whr_openarea = $d3dConf->mPerm->get_open_query( "search", $_params4op );
	}

	$sql = "SELECT d.bid, d.uid, d.title, ".$q_diary." d.create_time, d.openarea AS openarea_entry, 
			d.vgids AS vgids, d.vpids AS vpids, cfg.openarea AS openarea, 
			c.openarea AS openarea_cat, c.vgids AS vgids_cat, c.vpids AS vpids_cat 
		FROM ".$xoopsDB->prefix($mydirname.'_diary')." d LEFT JOIN "
		.$xoopsDB->prefix($mydirname.'_config')." cfg ON d.uid=cfg.uid LEFT JOIN "
		.$xoopsDB->prefix($mydirname.'_category')." c ON (d.cid=c.cid AND (d.uid=c.uid OR c.uid='0')) WHERE "
		.$whr_openarea;
	
    	if ( $userid != 0 ) {
        	$sql .= " AND d.uid=".$userid." ";
    	}

	// because count() returns 1 even if a supplied variable
	// is not an array, we must check if $querryarray is really an array
	$count = count($queryarray);
	if ( $count > 0 && is_array($queryarray) ) {
		$queryarray[0]=mysql_real_escape_string($queryarray[0]);
		$sql .= " AND ((d.title LIKE '%$queryarray[0]%' OR d.diary LIKE '%$queryarray[0]%') ";
		for ( $i = 1; $i < $count; $i++ ) {
			$queryarray[$i]=mysql_real_escape_string($queryarray[$i]);
			$sql .= " $andor ";
			$sql .= " (d.title LIKE '%$queryarray[$i]%' OR d.diary LIKE '%$queryarray[$i]%') ";
		}
		$sql .= ") ";
	}
	    	//var_dump($sql);
	$sql .= " ORDER BY d.create_time DESC";
	$result = $xoopsDB->query($sql,$limit,$offset);
	$i = 0;

	while ( $dbdat = $xoopsDB->fetchArray($result) ) {

		$dbdat['uid'] = intval($dbdat['uid']);
		
		$ret[$i]['image'] = "images/pencil.gif";
		$ret[$i]['link'] = "index.php?page=detail&amp;bid=".$dbdat['bid'];
		$ret[$i]['title'] = $func->htmlspecialchars($dbdat['title']);
		$tmparray = preg_split("/[-: ]/", $dbdat['create_time']);
		$ret[$i]['time'] = mktime($tmparray[3], $tmparray[4], $tmparray[5], $tmparray[1], $tmparray[2], $tmparray[0]);
		$ret[$i]['uid'] = $dbdat['uid'];
		if( !empty($dbdat['diary']) ){
			//start main
			$context = $dbdat['diary'];
//			$context = strip_tags($myts->displayTarea(strip_tags($context)));
//			$context = strip_tags($context);
			$context = $func->substrTarea( $context, (int)$dbdat['dohtml'], $mod_config['preview_charmax'] , true, "" );
			$ret[$i]['context'] = d3diary_search_make_context3($context,$queryarray);
			//end main
		}
		$i++;
	}
	return $ret;
}



//nao-pon's hack
function d3diary_search_make_context3($text,$words,$l=255)
{
	static $strcut = "";
	if (!$strcut)
		$strcut = create_function ( '$a,$b,$c', (function_exists('mb_strcut'))?
			'return mb_strcut($a,$b,$c);':
			'return strcut($a,$b,$c);');
	
	if (!is_array($words)) $words = array();
	
	$ret = "";
	$q_word = str_replace(" ","|",preg_quote(join(' ',$words),"/"));
	
	if (preg_match("/$q_word/i",$text,$match))
	{
		$ret = ltrim(preg_replace('/\s+/', ' ', $text));
		list($pre, $aft)=preg_split("/$q_word/i", $ret, 2);
		$m = intval($l/2);
		if(strlen($pre) > $m){
			$ret = "... ";
		}
		$ret .= $strcut($pre, max(strlen($pre)-$m+1,0),$m).$match[0];
		$m = $l-strlen($ret);
		$ret .= $strcut($aft, 0, min(strlen($aft),$m));
		if (strlen($aft) > $m) $ret .= " ...";
	}
	
	if (!$ret)
		$ret = $strcut($text, 0, $l);
	
	return $ret;
}

}
?>