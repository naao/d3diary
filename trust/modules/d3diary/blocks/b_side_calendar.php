<?php

function b_d3dside_calendar_show( $options ){

	$mydirname = empty( $options[0] ) ? 'd3diary' : $options[0] ;
	$max_entry = empty( $options[1] ) ? 10 : (int)$options[1] ;
	$now_order = empty( $options[2] ) ? 'time' : trim( $options[2] ) ;
	$this_template = empty( $options[3] ) ? 'db:'.$mydirname.'_block_side_calendar.html' : trim( $options[3] ) ;
	$limit_self = empty( $options[4] ) ? 0 : (int)$options[4] ;

	if( preg_match( '/[^0-9a-zA-Z_-]/' , $mydirname ) ) die( 'Invalid mydirname' ) ;

	require_once dirname( dirname(__FILE__) ).'/class/d3diaryConf.class.php';
	
	$d3dConf = & D3diaryConf::getInstance($mydirname, 0, "b_side_calendar");
	$func =& $d3dConf->func ;
	$uid = $d3dConf->uid;
	$req_uid = $d3dConf->req_uid; // overrided by d3dConf
	//var_dump($req_uid);
	
	if( $limit_self == 1 ) {				// always show diarylist page
		$req_uid = 0 ;
	} elseif( $limit_self == 2 && $req_uid > 0 ) {		// not show for personal page
		return ;
	} elseif( $limit_self == 3 && $req_uid == 0 ) {		// not show except for personal page
		return ;
	}
		$d3dConf->get_month ($_year, $_month);
		$year = isset($_year) ? (int)$_year : intval(date("Y")) ;
		$month = isset($_month) ? (int)$_month : intval(date("n")) ;
		//var_dump( $d3dConf->mydirname );
		//var_dump($_year);var_dump($_month);
		//var_dump($year);var_dump($month);
		
		$page = $d3dConf->page ;

	// create base url
	//$page = & $d3dConf->page ;
	//$q_mode = & $d3dConf->q_mode ;
	//$q_cid = & $d3dConf->q_cid ;
	//$q_tag = & $d3dConf->q_tag ;
	$q_fr = $d3dConf->q_fr ;

		$base_url = $d3dConf->urluppr.$d3dConf->urlbase.$d3dConf->url4ex_date."&amp;";

		list( $yd_calender, $yd_cal_month ) = $func->get_calender ( $req_uid, $year, $month, $uid, $base_url, true );

		$lang = array();
		//$lang['title'] = constant('_MD_CTITLE');

		$block="";

		$block['yd_calender'] = $yd_calender;
		$block['yd_year'] = $year;
		$block['yd_prev_year'] = $year -1 ;
		$block['yd_next_year'] = $year +1;
		$block['yd_month'] = $month;
		$block['yd_cal_month'] = $yd_cal_month;
		$block['lang'] = $lang;
		$block['mydirname'] = $mydirname;
		$block['base_url'] = $base_url;

	$d3dConf->debug_appendtime('b_side_calendar');

		if( empty( $options['disable_renderer'] ) ) {
			require_once XOOPS_ROOT_PATH.'/class/template.php' ;
			$tpl =& new XoopsTpl() ;
			$tpl->assign( 'block' , $block ) ;
			$ret['content'] = $tpl->fetch( $this_template ) ;
			return $ret ;
		} else {
			return $block ;
		}
}

function b_d3dside_calendar_edit( $options )
{
	$mydirname = empty( $options[0] ) ? 'd3diary' : $options[0] ;
	$max_entry = empty( $options[1] ) ? 10 : (int)$options[1] ;
	$now_order = empty( $options[2] ) ? 'time' : trim( $options[2] ) ;
	$this_template = empty( $options[3] ) ? 'db:'.$mydirname.'_block_side_calendar.html' : trim( $options[3] ) ;
	$limit_self = empty( $options[4] ) ? 0 : (int)$options[4] ;

	if( preg_match( '/[^0-9a-zA-Z_-]/' , $mydirname ) ) die( 'Invalid mydirname' ) ;
	$orders = array(
		'time' => _MB_D3DIARY_ORDERTIMED ,
		'posted' => _MB_D3DIARY_ORDERPOSTED ,
	) ;
	
	$order_options = '' ;
	foreach( $orders as $order_value => $order_name ) {
		$selected = $order_value == $now_order ? "selected='selected'" : "" ;
		$order_options .= "<option value='$order_value' $selected>$order_name</option>\n" ;
	}

	$limitselfs = array(
		0 => _MB_D3DIARY_LIMITSELF_NO ,
		1 => _MB_D3DIARY_LIMITSELF_ALL ,
		2 => _MB_D3DIARY_LIMITSELF_OTHER ,
		3 => _MB_D3DIARY_LIMITSELF_PERSON ,
	) ;
	
	$limitself_options = '' ;
	foreach( $limitselfs as $limitself_value => $limitself_name ) {
		$selected = $limitself_value == $limit_self ? "selected='selected'" : "" ;
		$limitself_options .= "<option value='$limitself_value' $selected>$limitself_name</option>\n" ;
	}

	$form = "
		<input type='hidden' name='options[0]' value='$mydirname' />
		<label for='o1'>" . sprintf( _MB_D3DIARY_DISPLAY , "</label><input type='text' size='4' name='options[1]' id='o1' value='$max_entry' style='text-align:right;' />" ) . "
		<br />
		<label for='orderrule'>"._MB_D3DIARY_ORDERRULE."</label>&nbsp;:
		<select name='options[2]' id='orderrule'>
			$order_options
		</select>
		<br />
		<label for='this_template'>"._MB_D3DIARY_THISTEMPLATE."</label>&nbsp;:
		<input type='text' size='60' name='options[3]' id='this_template' value='".htmlspecialchars($this_template,ENT_QUOTES)."' />
		<br />
		<label for='limitself'>"._MB_D3DIARY_LIMITSELF."</label>&nbsp;:
		<select name='options[4]' id='limitself'>
			$limitself_options
		</select>
		<br />
	\n" ;

	return $form;
}
?>