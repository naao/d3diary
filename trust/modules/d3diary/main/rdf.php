<?php

error_reporting(0);

include_once dirname( dirname(__FILE__) ).'/class/category.class.php';
include_once dirname( dirname(__FILE__) ).'/class/d3diaryConf.class.php';

$category =& D3diaryCategory::getInstance();
$d3dConf =& D3diaryConf::getInstance($mydirname);
$func =& $d3dConf->func ;
$mod_config =& $d3dConf->mod_config ;

$uid = 0; $uname = "";
$_uid = $func->getpost_param('uid');
if (!empty($_uid)) {
	if( (int)$_uid > 0 ){
		$uid = $_uid;
		$ret = $func->get_xoopsuname($uid);
		$uname = $ret['uname'];
		$name = $ret['name'];

		if( $mod_config['use_name'] == 1 && !empty($name)) {
			$uname = $name;
		}
	}
}

$cid = 0; $whr_cid = ""; //$cname = "";
$_cid = $func->getpost_param('cid');
if (!empty($_cid)) {
	if(  (int)$_cid > 0 ){
		$cid = (int)$_cid;
		$category->uid=$uid;
		$category->cid=$cid;
		$category->getchildren($mydirname);
		$cname = $category->cname;
		if($category->children){
			$whr_cid =" AND d.cid IN (".implode(",",$category->children).") ";
		} else {
			$whr_cid =" AND d.cid=".$cid;
		}
	}
}
	$_rss_ver = $func->getpost_param('ver');
	$rss_ver  = !empty($_rss_ver) ? htmlSpecialChars($_rss_ver, ENT_QUOTES ) : "rss1" ;

require_once(XOOPS_ROOT_PATH.'/class/template.php');

header ('Content-Type:text/xml; charset=utf-8');
$tpl = new XoopsTpl();
//$tpl->xoops_setCaching(2);
//$tpl->xoops_setCacheTime(10);

//if (!$tpl->is_cached("db:{$mydirname}_rdf.xml")) {
	// get timezone offset
        $_tzd  = date('O', time());
        $tzd  = substr( chunk_split( $_tzd, 3, ':' ), 0, 6 );
        $tzd2  = str_replace(":", "", substr( chunk_split( $_tzd, 3, ':' ), 0, 6 ));

	global $xoopsModule;
	$channel['mod_title'] = htmlSpecialChars($func->convert_encoding_utf8($xoopsModule->name()), ENT_QUOTES);
	if ($uname) {
		$channel['title'] = htmlSpecialChars($func->convert_encoding_utf8($uname), ENT_QUOTES);
		$channel['lang_title'] = htmlSpecialChars($func->convert_encoding_utf8(_MD_DIARY_PERSON), ENT_QUOTES);
	} else {
		$channel['title'] = "";
		$channel['lang_title'] = "";
	}
	$channel['description'] =  htmlSpecialChars($func->convert_encoding_utf8($xoopsConfig['sitename'].' - '.$xoopsConfig['slogan']), ENT_QUOTES);
	$channel['language'] = _LANGCODE;
	$channel['creator'] = "D3DIARY - XOOPS DIARY MODULE";
	$channel['category'] = !empty($cname) ? htmlSpecialChars($func->convert_encoding_utf8($cname), ENT_QUOTES) : $channel['mod_title'] ;
	$channel['tzd'] = $tzd;
	$channel['tzd2'] = $tzd2;

	// for neglect future entry
	$now = date( "Y-m-d H:i:s" );
	$whr_ctime = " AND create_time<'".$now."'";

	// query
	$sql = "SELECT d.uid AS uid, d.bid AS bid, d.title AS title, d.diary AS diary, d.update_time AS update_time,
			d.create_time AS create_time, d.dohtml as dohtml, u.uname, u.name, c.cid AS cid, c.cname AS cname 
			FROM ".$xoopsDB->prefix($mydirname.'_diary')." d 
			INNER JOIN ".$xoopsDB->prefix('users')." u USING(uid) 
			LEFT JOIN ".$xoopsDB->prefix($mydirname.'_config'). " cfg ON d.uid=cfg.uid 
			LEFT JOIN ".$xoopsDB->prefix($mydirname.'_category')." c 
			ON (d.cid=c.cid AND (d.uid=c.uid OR c.uid=0)) 
			WHERE (d.openarea='0' OR d.openarea IS NULL) AND (cfg.openarea='0' OR cfg.openarea IS NULL) 
			AND (c.openarea='0' OR c.openarea IS NULL) ".$whr_cid.$whr_ctime;

	if($uid>0){
		$sql .= " AND d.uid='".$uid."' ";
	}
	$sql .= " ORDER BY create_time DESC LIMIT 0,30";
	

	// item
	$entry = array(); $entries = array();
	$result = $xoopsDB->query($sql);
	while ( $dbdat = $xoopsDB->fetchArray($result)){
		$entry['title'] = empty( $dbdat['title'] ) ? constant('_MD_DIARY_NOTITLE') : $dbdat['title'];
		$entry['title'] = htmlSpecialChars($func->convert_encoding_utf8($entry['title']), ENT_QUOTES);
		$entry['uri']   = XOOPS_URL.'/modules/'.$mydirname.'/index.php?page=detail&bid='.$dbdat['bid'];
		$entry['link']  = htmlSpecialChars($entry['uri'], ENT_QUOTES);
		$entry['uid']   = $dbdat['uid'];
		if( $mod_config['use_name'] == 1 && !empty($dbdat['name'])) {
			$entry['creator'] = htmlSpecialChars($func->convert_encoding_utf8($dbdat['name']), ENT_QUOTES);
		} else {
			$entry['creator'] = htmlSpecialChars($func->convert_encoding_utf8($dbdat['uname']), ENT_QUOTES);
		}
		$entry['cid']   = isset($dbdat['cid']) ? intval($dbdat['cid']) : 0 ;
		$entry['cname'] = isset($dbdat['cname']) ? $dbdat['cname'] : constant('_MD_NOCNAME') ;
		$tmp = split("[-: ]",$dbdat['update_time']);
		$entry['update'] = xoops_getUserTimestamp(mktime($tmp[3],$tmp[4],$tmp[5],$tmp[1],$tmp[2],$tmp[0]), $tzd);
		$tmp = split("[-: ]",$dbdat['create_time']);
		$entry['tstamp'] = xoops_getUserTimestamp(mktime($tmp[3],$tmp[4],$tmp[5],$tmp[1],$tmp[2],$tmp[0]), $tzd);
		$entry['description'] = htmlSpecialChars($func->convert_encoding_utf8($func->substrTarea($dbdat['diary'], $dbdat['dohtml'], 300, true, "UTF-8")), ENT_QUOTES);
		$entry['diary'] = htmlSpecialChars($func->convert_encoding_utf8($func->substrTarea($dbdat['diary'], $dbdat['dohtml'], 0, false, "UTF-8")), ENT_QUOTES);
		$entries[]=$entry;
	}

	$channel['lastbuild'] = $entries[0]['tstamp'] ;

	$tpl->assign(array(
			"channel" => $channel,
			"yd_data" => $entries,
			"rss_ver" => $rss_ver,
			"mod_url" => XOOPS_URL."/modules/".$mydirname,
			"mydirname" => $mydirname,
			"mod_config" =>  $mod_config,
			));
	// write to Template
	$tpl->display("db:{$mydirname}_rdf.xml");
//}

?>
