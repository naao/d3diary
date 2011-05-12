<?php

	if( ! defined( 'XOOPS_ROOT_PATH' ) ) exit ;
	if( ! defined( 'XOOPS_TRUST_PATH' ) ) exit ;

	/*
		$db : db instance
		$myts : MyTextSanitizer instance
		$this->year : year
		$this->month : month
		$this->date : date
		$this->week_start : sunday:0 monday:1
		$this->user_TZ : user's timezone (+1.5 etc)
		$this->server_TZ : server's timezone (-2.5 etc)
		$tzoffset_s2u : the offset from server to user
		$now : the result of time()
		$plugin = array('dirname'=>'dirname','name'=>'name','dotgif'=>'*.gif','options'=>'options')
		
		$plugin_returns[ DATE ][]
	*/

	$mydirname = $plugin['dirname'] ;

	// set range (added 86400 second margin "begin" & "end")
	$range_start_s = mktime(0,0,0,$this->month,$this->date-1,$this->year) ;
	$range_end_s = mktime(0,0,0,$this->month,$this->date+2,$this->year) ;

	// options
	$options = array();
	if(!empty($plugin['options'])){
		$options = explode( '|' , $plugin['options'] ) ;
	}
		
	$desc_length = !empty($options[0]) ? $options[0] : 0 ;

	$mytrustdirpath = XOOPS_TRUST_PATH.'/modules/d3diary' ;
	require_once $mytrustdirpath."/class/d3diaryConf.class.php";
	require_once $mytrustdirpath."/class/photo.class.php";

	$d3dConf = D3diaryConf::getInstance($mydirname, 0, "whatsnew");
	$myts =& $d3dConf->myts;
	$uid = $d3dConf->uid;
	//$req_uid = $d3dConf->req_uid; // overrided by d3dConf
	$req_uid = 0; // overrided by d3dConf

	$params['range_start'] = date("Y-m-d H:i:s", $range_start_s);
	$params['range_end'] = date("Y-m-d H:i:s", $range_end_s);
	$entry	= $d3dConf->func->get_blist ($req_uid, $uid, 10, true, $params ); // dosort = true

	$URL_MOD = XOOPS_URL."/modules/".$mydirname;
	
	if (!empty($entry)) {
		$i=0;
		foreach ( $entry as $b => $e){
			$target_date =  date('j', $e['tstamp']+$tzoffset_s2u);
			$description = ($desc_length>0) ? 
					trim($d3dConf->func->substrTarea($e['diary'], $e['dohtml'], $desc_length , true)) : "";
			$tmp_array = array(
				'dotgif' => $plugin['dotgif'] ,
				'dirname' => $plugin['dirname'] ,
				'link' => $e['url'] ,
				'id' =>  $e['bid'] ,
				'server_time' => $server_time ,
				'user_time' => $user_time ,
				'name' => 'bid' ,
				'title' => $myts->makeTboxData4Show($e['title']) ,
				'description' => $description ,
			) ;
			// multiple gifs allowed per a plugin & per a day
			$plugin_returns[ $target_date ][] = $tmp_array ;
		}
	
	}

?>