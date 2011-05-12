<?php

$GLOBALS['d3diary_tables'] = array(
	'category' => array(
		'uid' ,
		'cid' ,
		'cname' ,
		'corder' ,
		'subcat' ,
		'blogtype' ,
		'blogurl' ,
		'rss' ,
		'openarea' ,
		'dohtml' ,
  		'vgids' ,
  		'vpids' ,
	) ,
	
	'cnt' => array(
		'uid' ,
		'cnt' ,
		'ymd' ,
	) ,

	'cnt_ip' => array(
		'uid' ,
		'accip' ,
		'acctime' ,
	) ,

	'config' => array(
		'uid' ,
		'blogtype' ,
		'blogurl' ,
		'rss' ,
		'openarea' ,
  		'mailpost' ,
  		'address' ,
		'keep' ,
  		'uptime' ,
  		'updated' ,
	) ,

	'diary' => array(
		'bid' ,
		'cid' ,
		'uid' ,
		'title' ,
		'diary' ,
		'update_time' ,
		'create_time' ,
		'openarea' ,
		'dohtml' ,
  		'vgids' ,
  		'vpids' ,
  		'view' ,
	) ,

	'newentry' => array(
		'uid' ,
		'cid' ,
		'title' ,
		'url' ,
		'create_time' ,
		'blogtype' ,
		'diary' ,
	) ,

	'photo' => array(
		'uid' ,
		'bid' ,
		'pid' ,
		'ptype' ,
		'tstamp' ,
		'info' ,
	) ,

	'tag' => array(
		'tag_id' ,
		'tag_name' ,
		'bid' ,
		'uid' ,
		'tag_group' ,
		'reg_unixtime' ,
	) ,
) ;

?>