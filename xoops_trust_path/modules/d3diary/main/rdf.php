<?php

error_reporting(0);

include_once dirname( dirname(__FILE__) ).'/class/category.class.php';
include_once dirname( dirname(__FILE__) ).'/class/d3diaryConf.class.php';

$category =& D3diaryCategory::getInstance();
$d3dConf =& D3diaryConf::getInstance($mydirname);
$func =& $d3dConf->func ;
$mod_config =& $d3dConf->mod_config ;
$mPerm =& $d3dConf->mPerm ;

$d3dConf->include_footer = false ;

$uid = 0; $uname = "";
$_uid = $func->getpost_param('uid');
if (!empty($_uid)) {
	if( (int)$_uid > 0 ){
		$uid = (int)$_uid;
	}
} elseif (!empty($func->req_uid)) {
		$uid = (int)$func->req_uid;
}

if( $uid > 0 ){
	$ret = $func->get_xoopsuname($uid);
	$uname = $ret['uname'];
	$name = $ret['name'];
	if( $mod_config['use_name'] == 1 && !empty($name)) {
		$uname = $name;
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
	}
}
	$params['cids'] = empty( $category->children ) ? (empty( $cid ) ? array() : array($cid)) : $category->children ;

$b_tag_noquote = $d3dConf->q_tag_noquote;
$b_tag = $d3dConf->q_tag;
	$_entries = array();
	if (!get_magic_quotes_gpc()) {
		$params['tags'] = empty( $b_tag_noquote ) ? array() : explode( ',', addslashes($b_tag_noquote) ) ;
	} else {
		$params['tags'] = empty( $b_tag_noquote ) ? array() : explode( ',', $b_tag_noquote ) ;
	}

	$params['no_external'] = true;
	$_entries = $func->get_blist ( $uid, 0, 30, true, $params );

	$_rss_ver = $func->getpost_param('ver');
	$rss_ver  = !empty($_rss_ver) ?$func-> htmlSpecialChars( $_rss_ver ) : "rss1" ;

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
	$channel['mod_title'] = $func->htmlSpecialChars($func->convert_encoding_utf8($xoopsModule->name()),ENT_QUOTES,"UTF-8");
	if ($uname) {
		$channel['title'] = $func->htmlSpecialChars($func->convert_encoding_utf8($uname),ENT_QUOTES,"UTF-8");
		$channel['lang_title'] = $func->htmlSpecialChars($func->convert_encoding_utf8(_MD_DIARY_PERSON),ENT_QUOTES,"UTF-8");
	} else {
		$channel['title'] = "";
		$channel['lang_title'] = "";
	}
	$channel['description'] = $func->htmlSpecialChars($func->convert_encoding_utf8($xoopsConfig['sitename'].' - '.$xoopsConfig['slogan']),ENT_QUOTES,"UTF-8");
	$channel['language'] = _LANGCODE;
	$channel['creator'] = "D3DIARY - XOOPS DIARY MODULE";
	$channel['category'] = !empty($cname) ? $func->htmlSpecialChars($func->convert_encoding_utf8($cname),ENT_QUOTES,"UTF-8") : $channel['mod_title'] ;
	$channel['tzd'] = $tzd;
	$channel['tzd2'] = $tzd2;

	// item
	$entry = array(); $entries = array();
	foreach ($_entries as $entry){
		$entry['title'] = empty( $entry['title'] ) ? constant('_MD_DIARY_NOTITLE') : $entry['title'];
		$entry['title'] = $func->htmlSpecialChars($func->convert_encoding_utf8($entry['title']),ENT_QUOTES,"UTF-8");
		$entry['uri']   = XOOPS_URL.'/modules/'.$mydirname.'/index.php?page=detail&bid='.$entry['bid'];
		$entry['link']  = $func->htmlSpecialChars($entry['uri'],ENT_QUOTES,"UTF-8");
		if( $mod_config['use_name'] == 1 && !empty($dbdat['name'])) {
			$entry['creator'] = $func->htmlSpecialChars($func->convert_encoding_utf8($entry['name']),ENT_QUOTES,"UTF-8");
		} else {
			$entry['creator'] = $func->htmlSpecialChars($func->convert_encoding_utf8($entry['uname']),ENT_QUOTES,"UTF-8");
		}
		$entry['cid']   = isset($entry['cid']) ? intval($entry['cid']) : 0 ;
		$entry['cname'] = isset($entry['cname']) ? $entry['cname'] : constant('_MD_NOCNAME') ;
		$tmp = preg_split("/[-: ]/",$entry['update_time']);
		$entry['update'] = xoops_getUserTimestamp(mktime($tmp[3],$tmp[4],$tmp[5],$tmp[1],$tmp[2],$tmp[0]), $tzd);
		$tmp = preg_split("/[-: ]/",$entry['create_time']);
		$entry['tstamp'] = xoops_getUserTimestamp(mktime($tmp[3],$tmp[4],$tmp[5],$tmp[1],$tmp[2],$tmp[0]), $tzd);
		$entry['description'] = $func->htmlSpecialChars($func->convert_encoding_utf8($func->substrTarea($entry['diary'], $entry['dohtml'], 300, true, "UTF-8")),ENT_QUOTES,"UTF-8");
		$entry['diary'] = $func->htmlSpecialChars($func->convert_encoding_utf8($func->substrTarea($entry['diary'], $entry['dohtml'], 0, false, "UTF-8")),ENT_QUOTES,"UTF-8");
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
