<?php

function b_d3diary_list_show( $options ){

	include_once dirname( dirname(__FILE__) ).'/class/photo.class.php';
	$photo =& Photo::getInstance();

	$mydirname = empty( $options[0] ) ? 'd3diary' : $options[0] ;
	$max_entry = empty( $options[1] ) ? 10 : intval( $options[1] ) ;
	$now_order = empty( $options[2] ) ? 'time' : trim( $options[2] ) ;
	$use_detail = empty( $options[3] ) ? false : true ;
	$max_length = empty( $options[4] ) ? 80 : intval( $options[4] ) ;
	$this_template = empty( $options[5] ) ? 'db:'.$mydirname.'_block_diarylist.html' : trim( $options[5] ) ;
	$max_entryby_person = empty( $options[6] ) ? 3 : intval( $options[6] ) ;
	if (!get_magic_quotes_gpc()) {
		$params['categories'] = empty( $options[7] ) ? array() : explode( ',', addslashes($options[7]) ) ;
		$params['tags'] = empty( $options[8] ) ? array() : explode( ',', addslashes($options[8]) ) ;
	} else {
		$params['categories'] = empty( $options[7] ) ? array() : explode( ',', $options[7] ) ;
		$params['tags'] = empty( $options[8] ) ? array() : explode( ',', $options[8] ) ;
	}

	if( preg_match( '/[^0-9a-zA-Z_-]/' , $mydirname ) ) die( 'Invalid mydirname' ) ;
	$constpref = '_MB_' . strtoupper( $mydirname ) ;
	$_enc = _CHARSET;

	require_once dirname( dirname(__FILE__) ).'/class/d3diaryConf.class.php';
	$d3dConf = & D3diaryConf::getInstance($mydirname, 0, "b_diarylist");
	$myts =& $d3dConf->myts;

	$uid = $d3dConf->uid;
	$req_uid = $d3dConf->req_uid; // overrided by d3dConf
	$max_entry_4query =  30 + $max_entry ;
	
	$mytstamp = array();	// by ref
	// dosort = false, byref $mytstamp
	$entry = $d3dConf->func->get_blist_tstamp ( $req_uid, $uid, $max_entry_4query, false, $mytstamp, $params );
	
	// random photos
	$d3dConf->get_new_bids( $got_bids ) ;
	$photo->bids = $got_bids ;
	$photo->readrand_mul($mydirname);
	foreach ( $photo->photos as $i => $_photo ) {
			$entry[$i]['photo'] = $_photo['pid'].$_photo['ptype'];
	}
	unset($photo->photos);
	
	// comment counts, newest comments
	list($yd_comment,$yd_com_key) = $d3dConf->func->get_commentlist(0,$uid,$got_bids,100,true);
	if(!empty($yd_comment)){
		foreach( $yd_comment as $_com){
			$i = (int)$_com['bid'];
			$entry[$i]['com_num'] = $_com['com_num'];
			$entry[$i]['unique_path'] = $_com['unique_path'];
			$entry[$i]['com_uname'] = $myts->makeTboxData4Show($_com['uname']);
			$entry[$i]['com_name'] = $myts->makeTboxData4Show($_com['name']);
			$entry[$i]['com_title'] = $myts->makeTboxData4Show($_com['title']);
			$entry[$i]['com_datetime'] = $_com['datetime'];
			$entry[$i]['newcom'] = $_com['newcom'];
		//echo "<br />"; var_dump($i); var_dump($mydirname); var_dump($entry[$i]); echo "<br />";echo "<br />";
		}
	}

	$entry_temp = array();
	if (!empty($mytstamp) && !empty($entry)) {
		array_multisort($mytstamp, SORT_DESC, $entry);
		$i=0; $j=0;
		$usrcnt = array();
		foreach ( $entry as $b => $e){
			if(empty($e)){break;}
			if($j>=$max_entry){break;}
			//var_dump($e); echo"<br />";
		    if(isset($usrcnt[$e['uid']])){
			if( $usrcnt[$e['uid']]<$max_entryby_person ){
				$entry_temp[$j] = $e;
				$entry_temp[$j]['diary'] = $d3dConf->func->substrTarea($e['diary'], $e['dohtml'], $max_length, true);
				$usrcnt[$e['uid']] ++;
				$j++;
			}
		    } else {
				$entry_temp[$j] = $e;
				$entry_temp[$j]['diary'] = $d3dConf->func->substrTarea($e['diary'], $e['dohtml'], $max_length, true);
				$usrcnt[$e['uid']] = 1;
				$j++;
		    }
				$i++;
		}
	}
		$lang = array();
		$lang['exist_comments'] = constant($constpref.'_EXIST_COMMENTS');
		$lang['no_comments'] = constant($constpref.'_NO_COMMENTS');
		$lang['more'] = constant($constpref.'_MORE');

	$block="";
		$block['entry'] = $entry_temp;
		$block['yd_uid'] = $d3dConf->req_uid;
		$block['lang'] = $lang;
		$block['categories'] = implode(',', $params['categories']);
		$block['tags'] = implode(',', $params['tags']);
		$block['mydirname'] = $mydirname;
		$block['mod_config'] = $d3dConf->mod_config;
		$block['yd_com_key'] = $yd_com_key;
		
	$d3dConf->debug_appendtime('b_diarylist');

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

function b_d3diary_list_edit( $options )
{
	$mydirname = empty( $options[0] ) ? 'd3diary' : $options[0] ;
	$max_entry = empty( $options[1] ) ? 10 : intval( $options[1] ) ;
	$now_order = empty( $options[2] ) ? 'time' : trim( $options[2] ) ;
	$use_detail = empty( $options[3] ) ? false : true ;
	$max_length = empty( $options[4] ) ? 80 : intval( $options[4] ) ;
	$this_template = empty( $options[5] ) ? 'db:'.$mydirname.'_block_diarylist.html' : trim( $options[5] ) ;
	$max_entryby_person = empty( $options[6] ) ? 3 : intval( $options[6] ) ;
	$categories = empty( $options[7] ) ? "" : $options[7] ;
	$tags = empty( $options[8] ) ? "" : $options[8] ;

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

	if( $use_detail ) {
		$use_detailyes_checked = "checked='checked'" ;
		$use_detailno_checked = "" ;
	} else {
		$use_detailno_checked = "checked='checked'" ;
		$use_detailyes_checked = "" ;
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
		<label for='o3'>"._MB_D3DIARY_ASSIGNDETAIL."</label>&nbsp;:
		<input type='radio' name='options[3]' id='o31' value='1' $use_detailyes_checked /><label for='o31'>"._YES."</label>
		<input type='radio' name='options[3]' id='o30' value='0' $use_detailno_checked /><label for='o30'>"._NO."</label>
		<br />
		<label for='o4'>" ._MB_D3DIARY_MAXLENGTH."</label><input type='text' size='6' name='options[4]' id='o4' value='".$max_length."' style='text-align:right;' />
		<br />
		<label for='this_template'>"._MB_D3DIARY_THISTEMPLATE."</label>&nbsp;:
		<input type='text' size='60' name='options[5]' id='this_template' value='".htmlspecialchars($this_template,ENT_QUOTES)."' />
		<br />
		<label for='o6'>" . sprintf( _MB_D3DIARY_DISPLAY_PERSONAL , "</label><input type='text' size='4' name='options[6]' id='o6' value='$max_entryby_person' style='text-align:right;' />" ) . "
		<br />
		<label for='o7'>"._MB_D3DIARY_QUERY_CATEGORY."</label>&nbsp;:
		<input type='text' size='60' name='options[7]' id='o7' value='".htmlspecialchars($categories,ENT_QUOTES)."' />
		<br />
		<label for='o8'>"._MB_D3DIARY_QUERY_TAG."</label>&nbsp;:
		<input type='text' size='60' name='options[8]' id='o8' value='".htmlspecialchars($tags,ENT_QUOTES)."' />
		<br />
\n" ;
	return $form;
}
?>