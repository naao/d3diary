<?php

function b_d3diary_bloggerlist_show( $options ){

	global $xoopsUser,$xoopsDB;
	
	$mydirname = empty( $options[0] ) ? 'd3diary' : $options[0] ;
	$max_entry = empty( $options[1] ) ? 10 : intval( $options[1] ) ;
	$params['order'] = $now_order = empty( $options[2] ) ? 'time' : trim( $options[2] ) ;
	$this_template = empty( $options[3] ) ? 'db:'.$mydirname.'_block_bloggerlist.html' : trim( $options[3] ) ;

	if( preg_match( '/[^0-9a-zA-Z_-]/' , $mydirname ) ) die( 'Invalid mydirname' ) ;

	$constpref = '_MB_' . strtoupper( $mydirname ) ;

	require_once dirname( dirname(__FILE__) ).'/class/d3diaryConf.class.php';
	$d3dConf = D3diaryConf::getInstance($mydirname, 0, "b_bloggerlist");
	$func =& $d3dConf->func ;
	$mod_config =& $d3dConf->mod_config ;

	$uid = $d3dConf->uid;
	$req_uid = $d3dConf->req_uid; // overrided by d3dConf
	
	$params['ofst_key'] = "bgbofst" ;
	$_offset_ = $func->getpost_param($params['ofst_key']);
	$offset = isset($_offset_) ?(int)$_offset_ : 0;

	list( $blogger, $blogger2, $bloggernavi ) = $func->get_bloggerlist ( $req_uid, $uid, $max_entry, $offset, $params );

	$block=array();

	$block['blogger'] = $blogger;
	$block['blogger2'] = $blogger2;
	$block['bloggernavi'] = $bloggernavi;
	$block['mydirname'] = $mydirname;
	$block['mod_config'] = $mod_config;
	$block['lang']['other'] = constant($constpref.'_OTHER');
	$block['lang']['more'] = constant($constpref.'_MORE');
	$block['lang']['newdiary'] = constant($constpref.'_NEWDIARY');
	$block['lang']['newphoto'] = constant($constpref.'_NEWPHOTO');
	
	$d3dConf->debug_appendtime('b_bloggerlist');

	if( empty( $options['disable_renderer'] ) ) {
		require_once XOOPS_ROOT_PATH.'/class/template.php' ;
		$tpl = new XoopsTpl() ;
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
	require_once dirname( dirname(__FILE__) ).'/class/d3diaryConf.class.php';
	$d3dConf = D3diaryConf::getInstance($mydirname, 0, "b_bloggerlist");
	$func =& $d3dConf->func ;

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
		<input type='text' size='60' name='options[3]' id='this_template' value='".$func->htmlspecialchars($this_template)."' />
		<br />
	\n" ;

	return $form;
}
?>