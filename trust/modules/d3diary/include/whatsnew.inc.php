<?php
/*
 * Created on 2010/11/28 by naao http://www.naaon.com/
 * $Id: whatsnew.inc.php,v 0.01 2010/11/28 naao Exp $
 */

// === eval begin ===
eval( '
	function '.$mydirname.'_new($limit=0, $offset=0) {
		return d3diary_new_base( "'.$mydirname.'", $limit , $offset ) ;
	}
' ) ;

if( ! function_exists( 'd3diary_new_base' ) ) {

	function d3diary_new_base( $mydirname, $limit=0, $offset=0 ) 
	{
		if( preg_match( '/[^0-9a-zA-Z_-]/' , $mydirname ) ) die( 'Invalid mydirname' ) ;
		$constpref = '_MB_' . strtoupper( $mydirname ) ;
		$_enc = _CHARSET;

		$URL_MOD = XOOPS_URL."/modules/".$mydirname;

		$mytrustdirpath = dirname(dirname( __FILE__ )) ;
		//include_once "$mytrustdirpath/include.php";
		require_once $mytrustdirpath."/class/d3diaryConf.class.php";
		require_once $mytrustdirpath."/class/photo.class.php";

		$d3dConf = D3diaryConf::getInstance($mydirname, 0, "whatsnew");
		$photo =& Photo::getInstance();
		$myts =& $d3dConf->myts;
		$uid = $d3dConf->uid;
		//$req_uid = $d3dConf->req_uid; // overrided by d3dConf
		$req_uid = 0; // overrided by d3dConf

		$entry	= $d3dConf->func->get_blist_tstamp ($req_uid, $uid, $limit, false, $mytstamp ); // dosort = false, byref $mytstamp

		// random photos
		$d3dConf->get_new_bids( $got_bids ) ;
		$photo->bids = $got_bids ;
		$photo->readrand_mul($mydirname);
		foreach ( $photo->photos as $i => $_photo ) {
				$entry[$i]['photo'] = $_photo['pid'].$_photo['ptype'];
		}
		unset($photo->photos);
	
		$ret	= array();
		if (!empty($entry)) {
			array_multisort($mytstamp, SORT_DESC, $entry);
			$i=0;
			foreach ( $entry as $b => $e){
				$entry_temp[$i]	= $e;
				$ret[$i]['description']	= trim($d3dConf->func->substrTarea($e['diary'], $e['dohtml'], 0, true));
				$ret[$i]['link']	= $e['url'];
				$ret[$i]['cname'] 	=  $myts->makeTboxData4Show($e['cname']);
				$ret[$i]['cat_link'] 	= $URL_MOD."/index.php?page=category&amp;cid=".$e['cid'];
				$ret[$i]['title']    	= $e['title'];
				$ret[$i]['time']	= $e['tstamp'];
				$ret[$i]['uid']  	= $e['uid'];
				$ret[$i]['hits'] 	= $e['view'];
				$ret[$i]['image']	= !empty($e['photo']) ? $URL_MOD."/upimg/".$e['photo'] : "";
				$ret[$i]['id']   	= $e['bid'];
				$i++;
			}
		
		}

		return $ret;
	}
}

?>