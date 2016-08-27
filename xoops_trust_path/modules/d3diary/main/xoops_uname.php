<?php

error_reporting(0);

include_once dirname( dirname(__FILE__) ).'/class/d3diaryConf.class.php';
$d3dConf->include_footer = false ;

$q = (isset($_GET['q']))? (string)$_GET['q'] : "";
$enc = (isset($_GET['e']))? (string)$_GET['e'] : "";

$dats = array();
$oq = $q = str_replace("\0","",$q);
$enc = strtoupper(str_replace("\0","",$enc));
$encs = array( 'EUC-JP' , 'SJIS', 'EUCJP-WIN', 'SJIS-WIN', 'JIS', 'ISO-2022-JP' );
$use_mb = (in_array($enc, $encs));
$use_utf8 = ($enc === 'UTF-8');

$d3dConf = & D3diaryConf::getInstance ( $mydirname, 0, "xoops_uname" ) ;
$gPerm =& $d3dConf->gPerm ;
$mod_config =& $d3dConf->mod_config ;
$uid = $d3dConf->uid;

if($uid<=0) {
	exit;
}
if( $gPerm->use_pp ==1 ) {
	$_tempGperm = $gPerm->getUidsByName( array_keys($gPerm->gperm_config) );
	// check edit permission by group
	if(!empty($_tempGperm['allow_ppermission'])){
		if(!isset($_tempGperm['allow_ppermission'][$uid])) {
			exit();
		}
	} else {
		exit();
	}
} else {
		exit();
}

if ($q !== "") {
	
	$q = setEncoding($q, $enc, 'UTF-8');
	$q = addslashesGPC($q);

	if ($mod_config['use_name'] == 1) {
		$where1 = " WHERE name LIKE '".$q."%'";
		$where2 = " WHERE name LIKE '%".$q."%' AND name NOT LIKE '".$q."%'";
		$order = " ORDER BY name ASC";
	} else {
		$where1 = " WHERE uname LIKE '".$q."%'";
		$where2 = " WHERE uname LIKE '%".$q."%' AND uname NOT LIKE '".$q."%'";
		$order = " ORDER BY uname ASC";
	}
	$limit = 100;

	$db = & $d3dConf->db;
	
	$sql = "SELECT uid, uname, name FROM ".$db->prefix('users').$where1.$order." LIMIT ".$limit;
	$names = $unames = $suggests = $tags = array();
	if ($result = $db->query($sql))
	{
		while($dat = $db->fetchArray($result))
		{
			$unames[] = '"'.str_replace('"','\"',$dat['uname']).'['.$dat['uid'].']"';
			$names[] = '"'.str_replace('"','\"',$dat['name']).'['.$dat['uid'].']"';
		}
	}

	$count = count($unames);
	if ($count < $limit)
	{
		$sql = "SELECT uid, uname, name FROM ".$db->prefix('users').$where2.$order." LIMIT ".($limit - $count);
		if ($result = $db->query($sql))
		{
			while($dat = $db->fetchArray($result))
			{
				$unames[] = '"'.str_replace('"','\"',$dat['uname']).'['.$dat['uid'].']"';
				$names[] = '"'.str_replace('"','\"',$dat['name']).'['.$dat['uid'].']"';
			}
		}		
	}

}

$oq = '"'.str_replace('"','\"',$oq).'"';
$oq = setEncoding($oq, 'UTF-8');	// don't set no3 argment

if ($mod_config['use_name'] == 1) {
	$ret = join(", ", $names);
} else {
	$ret = join(", ", $unames);
}

$ret = setEncoding($ret, 'UTF-8');	// don't set no3 argment
$ret = 'this.setSuggest(' . $oq . ',new Array(' . $ret . '));';
$ret = setEncoding($ret, 'UTF-8');	// don't set no3 argment

// clear output buffer
while( ob_get_level() ) {
	ob_end_clean() ;
}

header ("Content-Type: text/plain; charset=UTF-8");
header ("Content-Length: ".strlen($ret));
echo $ret;
exit;

// magic_quotes_gpc checked addslashes()
function addslashesGPC($str) {
	if (! get_magic_quotes_gpc()) {
		$str = addslashes($str);
	} else {
		if (ini_get('magic_quotes_sybase')) {
			$str = addslashes(str_replace("''", "'", $str));
		}
	}
	return $str;
}

function setEncoding($str, $newEncoding, $currentEncoding) {
	if (XOOPS_USE_MULTIBYTES == 1) {
		$encodingList = mb_list_encodings();
		if(!$currentEncoding){
			$currentEncoding = mb_detect_encoding($str, $encodingList);
		}
		$changeEncoding = mb_convert_encoding($str, $newEncoding, $currentEncoding);
		return $changeEncoding;
	}
	return utf8_encode($str);
}

?>
