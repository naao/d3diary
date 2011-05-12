<?php

function b_d3diary_bloggerlist_show( $options ){

	global $xoopsUser,$xoopsDB;
	if(is_object($xoopsUser)) {
		$uid = $xoopsUser->getVar('uid');
	} else {
		$uid = 0;
	}
	
	$mydirname = empty( $options[0] ) ? 'd3diary' : $options[0] ;
	$max_entry = empty( $options[1] ) ? 10 : intval( $options[1] ) ;
	$now_order = empty( $options[2] ) ? 'time' : trim( $options[2] ) ;
	$this_template = empty( $options[3] ) ? 'db:'.$mydirname.'_block_bloggerlist.html' : trim( $options[3] ) ;

	if( preg_match( '/[^0-9a-zA-Z_-]/' , $mydirname ) ) die( 'Invalid mydirname' ) ;

	$constpref = '_MB_' . strtoupper( $mydirname ) ;

	require_once dirname( dirname(__FILE__) ).'/class/d3diaryConf.class.php';
	
	$d3dConf = D3diaryConf::getInstance($mydirname, 0, "b_bloggerlist");

	// first, get external blogger list
	$sql = "SELECT DISTINCT cfg.uid, u.name, u.uname from "
			.$xoopsDB->prefix($mydirname."_config")." cfg LEFT JOIN "
			.$xoopsDB->prefix("users")
			." u ON cfg.uid=u.uid WHERE cfg.blogtype>'0'" ;
	$result = $xoopsDB->query($sql);
	
	$blogger2 = array(); $blogger2_ids = array(); 
	while( $row = $xoopsDB->fetchArray( $result ) ) {
		$blogger2[]=$row;
		$blogger2_ids[]=$row['uid'];
	}
	
	$now_order=intval($d3dConf->func->getpost_param('order'));

	if ( $now_order == 'time' ) {
		$whr_order = "max_create_time DESC";
	} elseif ( $now_order == 'posted' ) {
		$whr_order = "count DESC";
	} else {
		$whr_order = "max_create_time DESC";
	}

	$sql = "SELECT d.uid, count(d.uid) AS count, MAX(d.create_time) AS max_create_time, u.name, u.uname from "
			.$xoopsDB->prefix($mydirname."_diary")." d LEFT JOIN "
			.$xoopsDB->prefix("users")
			." u ON d.uid=u.uid GROUP BY d.uid, u.name, u.uname ORDER BY ".$whr_order." LIMIT ".$max_entry ;

	$blogger = array();
	$result = $xoopsDB->query($sql);
	while( $row = $xoopsDB->fetchArray( $result ) ) {
		// exclude external bloggers
		if(!in_array($row['uid'],$blogger2_ids)) $blogger[]=$row;
	}
	
	$block="";

	$block['blogger']=$blogger;
	$block['blogger2']=$blogger2;
	$block['mydirname']=$mydirname;
	$block['mod_config']=$d3dConf->mod_config;
	$block['lang']['other']=constant($constpref.'_OTHER');
	$block['lang']['more']=constant($constpref.'_MORE');
	
	$d3dConf->debug_appendtime('b_bloggerlist');

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

function b_d3diary_bloggerlist_edit( $options )
{
	$mydirname = empty( $options[0] ) ? 'd3diary' : $options[0] ;
	$max_entry = empty( $options[1] ) ? 10 : intval( $options[1] ) ;
	$now_order = empty( $options[2] ) ? 'time' : trim( $options[2] ) ;
	$this_template = empty( $options[3] ) ? 'db:'.$mydirname.'_block_bloggerlist.html' : trim( $options[3] ) ;

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
	\n" ;

	return $form;
}
?>