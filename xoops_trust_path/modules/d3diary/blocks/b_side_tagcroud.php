<?php

function b_d3dside_tagcroud_show( $options ){

	$mydirname = empty( $options[0] ) ? 'd3diary' : $options[0] ;
	$max_entry = empty( $options[1] ) ? 30 : intval( $options[1] ) ;
	$params['order'] = empty( $options[2] ) ? 'time' : trim( $options[2] ) ;
	$this_template = empty( $options[3] ) ? 'db:'.$mydirname.'_block_side_tagcroud.html' : trim( $options[3] ) ;
	$limit_self = empty( $options[4] ) ? 0 : (int)$options[4] ;
	$max_size = empty( $options[5] ) ? 160 : (int)$options[5] ;	// maximum font-size (%)
	$min_size = empty( $options[6] ) ?  80 : (int)$options[6] ;	// minimum font-size (%)

	if( preg_match( '/[^0-9a-zA-Z_-]/' , $mydirname ) ) die( 'Invalid mydirname' ) ;

	require_once dirname( dirname(__FILE__) ).'/class/d3diaryConf.class.php';
	
	$d3dConf = & D3diaryConf::getInstance($mydirname, 0, "b_side_tagcroud");
	$func =& $d3dConf->func ;
	$mPerm =& $d3dConf->mPerm ;
	$mod_config =& $d3dConf->mod_config ;
	$uid = $d3dConf->uid;
	$req_uid = $d3dConf->req_uid; // overrided by d3dConf
	//var_dump($req_uid);
	
	
	if( $limit_self == 1 ) {				// always show diarylist page
		$req_uid = 0 ;
	} elseif( $limit_self == 2 && $req_uid > 0 ) {		// not show for personal page
		return ;
	} elseif( $limit_self == 3 && $req_uid == 0 ) {		// not show except for personal page
		return ;
	} elseif( $limit_self == 3 && $req_uid > 0 && $d3dConf->q_fr==1 ) {	// case of show friend page
		$req_uid = 0 ;
	}

	if ((int)$mod_config['use_tag'] >= 1) {
		// create base url
		//$page = $d3dConf->page ;
		//$q_mode = $d3dConf->q_mode ;
		$q_cid = $d3dConf->q_cid ;
		//$q_year = $d3dConf->q_year ;
		//$q_month = $d3dConf->q_month ;
		//$q_day = $d3dConf->q_day ;
		$q_fr = $d3dConf->q_fr ;
		
		$where = "";
		if($req_uid > 0 ){
			if ( $q_fr==1 ) {
				$where= "uid IN (". implode(',', $mPerm->req_friends).")";
			} else {
				$where= 'uid='. intval($req_uid);
			}
		}
		$base_url = $d3dConf->urluppr.$d3dConf->urlbase.$d3dConf->url4ex_tag. "&amp;";

		$params['ofst_key'] = "tofst" ;
		$tofst = $func->getpost_param($params['ofst_key']);

		switch ($params['order']) {
			case 'tag_name DESC' :
				$params['order'] = 'tag_name DESC'; break ;
			case 'quantity ASC' :
				$params['order'] = 'quantity ASC'; break ;
			case 'quantity DESC' :
				$params['order'] = 'quantity DESC'; break ;
			case 'reg_unixtime ASC' :
				$params['order'] = 'reg_unixtime ASC'; break ;
			case 'reg_unixtime DESC' :
				$params['order'] = 'reg_unixtime DESC'; break ;
			case 'tag_name ASC' :
			default :
				$params['order'] = 'tag_name ASC';
		}

		// getTagCloud ($where, $min_size, $max_size, $max_displays, $offset_page)
		list( $tagCloud, $tagnavi ) = $func->getTagCloud($where, $min_size, $max_size, $max_entry, $tofst, $params);

		$lang = array();
		//$lang['title'] = constant('_MD_CTITLE');

		$block="";

		$block['tagCloud'] = $tagCloud;
		$block['use_tag'] = $mod_config['use_tag'];
		$block['tagnavi'] = $tagnavi;
		$block['lang'] = $lang;
		$block['mydirname'] = $mydirname;
		$block['base_url'] = $base_url;
		$block['cid'] = $q_cid ;
		$block['tofst'] = $tofst ;
		$block['fr'] = $q_fr ;
	}

	$d3dConf->debug_appendtime('b_side_tagcroud');

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

function b_d3dside_tagcroud_edit( $options )
{
	$mydirname = empty( $options[0] ) ? 'd3diary' : $options[0] ;
	$max_entry = empty( $options[1] ) ? 30 : intval( $options[1] ) ;
	$now_order = empty( $options[2] ) ? 'time' : trim( $options[2] ) ;
	$this_template = empty( $options[3] ) ? 'db:'.$mydirname.'_block_side_tagcroud.html' : trim( $options[3] ) ;
	$limit_self = empty( $options[4] ) ? 0 : (int)$options[4] ;
	$max_size = empty( $options[5] ) ? 160 : (int)$options[5] ;	// maximum font-size (%)
	$min_size = empty( $options[6] ) ?  80 : (int)$options[6] ;	// minimum font-size (%)

	if( preg_match( '/[^0-9a-zA-Z_-]/' , $mydirname ) ) die( 'Invalid mydirname' ) ;
	$orders = array(
		'tag_name ASC'		=> _MB_D3DIARY_TAGORDER_NAMEASC ,
		'tag_name DESC'		=> _MB_D3DIARY_TAGORDER_NAMEDESC ,
		'quantity ASC'		=> _MB_D3DIARY_TAGORDER_COUNTASC ,
		'quantity DESC'		=> _MB_D3DIARY_TAGORDER_COUNTDESC ,
		'reg_unixtime ASC'	=> _MB_D3DIARY_TAGORDER_TIMEASC ,
		'reg_unixtime DESC'	=> _MB_D3DIARY_TAGORDER_TIMEDESC ,
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
		<label for='o5'>" . sprintf( _MB_D3DIARY_TAGSIZE_MAX , "</label><input type='text' size='4' name='options[5]' id='o5' value='$max_size' style='text-align:right;' />" ) . "
		<br />
		<label for='o6'>" . sprintf( _MB_D3DIARY_TAGSIZE_MIN , "</label><input type='text' size='4' name='options[6]' id='o6' value='$min_size' style='text-align:right;' />" ) . "
		<br />
	\n" ;

	return $form;
}
?>