<?php
function b_d3diary_d3comlist_topics_show( $options ){

	global $xoopsUser, $xoopsDB;

	$mydirname = empty( $options[0] ) ? 'd3diary' : $options[0] ;
	$max_topics = empty( $options[1] ) ? 10 : intval( $options[1] ) ;
	$show_fullsize = empty( $options[2] ) ? false : true ;
	$now_order = empty( $options[3] ) ? 'time' : trim( $options[3] ) ;
	$req_uid = empty( $options[4] ) ? 0 : intval( $options[4] ) ;
	$is_markup = empty( $options[5] ) ? false : true ;
	$use_detail = empty( $options[6] ) ? false : true ;
	$this_template = empty( $options[7] ) ? 'db:'.$mydirname.'_block_d3comlist_topics.html' : trim( $options[7] ) ;

	$use_aggre = empty( $options[8] ) ? false : true ;
	$categories = empty( $options[9] ) ? array() : explode(',',$options[9]) ;
	$forums = empty( $options[10] ) ? array() : explode(',',$options[10]) ;

	require_once dirname( dirname(__FILE__) ).'/class/d3diaryConf.class.php';
	$d3dConf = & D3diaryConf::getInstance($mydirname, 0, "b_d3comlist_topics");
	$func =& $d3dConf->func ;
	$myts =& $d3dConf->myts;
	$mPerm =& $d3dConf->mPerm ;
	$gPerm =& $d3dConf->gPerm ;
	$mod_config =& $d3dConf->mod_config ;

	$uid = $d3dConf->uid;

	$com_dirname = $mod_config['comment_dirname'];
	$com_forum_id = intval($mod_config['comment_forum_id']);
	$com_anchor_type = intval($mod_config['comment_anchor_type']);
	
	// if comment integration is not set, exit immediately
	if(empty($com_dirname) || $com_forum_id<=0) {
		echo "This block needs d3forum comment integration setting!";
		return array() ;
	}

	// d3forum_configs
	$module_handler =& xoops_gethandler( 'module' ) ;
	$module =& $module_handler->getByDirname( $com_dirname ) ;
	$mid_com = $module->getVar('mid');
	$config_handler =& xoops_gethandler('config');
	$configs = $config_handler->getConfigsByCat(0, $mid_com);
	
	$editperm=0;
	$owner=0;
	if($uid>0 && $req_uid==$uid){$editperm=1;}
	if($mPerm->isadmin){$editperm=1;}

	// forums can be read by current viewer (check by forum_access)
	$temparr_forums_can_read = $func->get_d3comforums_can_read( $com_dirname, $uid );
	if(!empty($temparr_forums_can_read)) {
		// remove comment_forum_id from allowed forums array
		$arrcom_forum_id[] = $com_forum_id;
		$gotarr_forums_can_read = array_diff($temparr_forums_can_read, $arrcom_forum_id);
	}

	// get all forums
	$sql = "SELECT forum_id, forum_external_link_format FROM ".$xoopsDB->prefix($com_dirname."_forums") ;
	$frs = $xoopsDB->query( $sql ) ;
	$d3com = array() ;
	while( $forum_row = $xoopsDB->fetchArray( $frs ) ) {
		// d3comment object
		$temp_forum_id = intval($forum_row['forum_id']);
		// exclude d3diary's comment forum
		if( ($temp_forum_id != $com_forum_id) && ! empty( $forum_row['forum_external_link_format'] ) ) {
			$d3com[$temp_forum_id] =& $func->get_d3com_object( $com_dirname , $forum_row['forum_external_link_format'] ) ;
		} else {
			$d3com[$temp_forum_id] = false ;
		}
	}

	if ( !in_array( $com_forum_id, $temparr_forums_can_read ) ) {
		$whr_extid = '0';
	}

	// order
	$whr_order = '1' ;
	switch( $now_order ) {
		case 'views':
			$odr = 't.topic_views DESC';
			break;
		case 'replies':
			$odr = 't.topic_posts_count DESC';
			break;
		case 'votes':
			$odr = 't.topic_votes_count DESC';
			break;
		case 'points':
			$odr = 't.topic_votes_sum DESC';
			break;
		case 'average':
			$odr = 't.topic_votes_sum/t.topic_votes_count DESC, topic_votes_count DESC';
			$whr_order = 't.topic_votes_count>0' ;
			break;
		case 'time':
		default:
			$odr = 't.topic_last_post_time DESC';
			break;
	}

	// finally, query d3comments
	$whr_extid = 'f.forum_id='.$com_forum_id ;

	// use solved or not
	if( empty( $configs['use_solved'] ) ) {
		$sel_solved = '1 AS topic_solved' ;
	} else {
		$sel_solved = 't.topic_solved' ;
	}

	if( $use_aggre ) {
		// categories
		$categories = array_map( 'intval' , $categories ) ;
		$categories4assign = implode(',',$categories) ;
		// forums
		$forums = array_map( 'intval' , $forums ) ;
		$forums4assign = implode(',',$forums) ;
		if( empty( $categories ) &&  empty( $forums )){
			$arrforums_to_read = $gotarr_forums_can_read;
			$whr_cats_or_forums = 'f.forum_id IN ('.implode(',',$arrforums_to_read).')' ;
			
		} else {
		    if(empty( $categories ) && !empty( $forums )) {
			$arrforums_to_read = array_intersect($gotarr_forums_can_read, $forums);
			$whr_cats_or_forums = 'f.forum_id IN ('.implode(',',$arrforums_to_read).')' ;
			
		    } elseif( !empty( $categories ) && empty( $forums )) {
			$whr_categories = 'f.cat_id IN ('.implode(',',$categories).')' ;
			$whr_cats_or_forums = $whr_categories. ' AND f.forum_id IN ('.implode(',',$gotarr_forums_can_read).')' ;
			
		    } else {	// !empty( $categories ) && !empty( $forums )
			$whr_categories = 'f.cat_id IN ('.implode(',',$categories).')' ;
			$whr_forums = 'f.forum_id IN ('.implode(',',$gotarr_forums_can_read).')' ;
			$arrforums_to_read = array_intersect($gotarr_forums_can_read, $forums);
			$whr_cats_or_forums = $whr_categories. ' AND '.$whr_forums .' OR f.forum_id IN ('.implode(',',$arrforums_to_read).')' ;
		    }
		}
	} else {
		$whr_cats_or_forums = '0' ;
		$categories4assign = "" ;
		$forums4assign = "" ;
	}

	// diary permission
	if($mPerm->isadmin){
		$whr_openarea = " 1 ";
	} else {
		$_params4op['use_gp'] = $gPerm->use_gp;
		$_params4op['use_pp'] = $gPerm->use_pp;
		$whr_openarea = $mPerm->get_open_query( "b_d3com_topics", $_params4op );
	}

	if( $uid > 0 && $is_markup ) {
		$sql = "SELECT t.topic_id, t.topic_title, t.topic_last_uid, t.topic_last_post_id, t.topic_last_post_time, 
			t.topic_views, t.topic_votes_count, t.topic_votes_sum, t.topic_posts_count, t.topic_external_link_id, 
			$sel_solved, t.forum_id, 
			p.post_id, p.subject, p.post_text, p.guest_name, p.html, p.smiley, p.xcode, p.br, p.unique_path,
			 f.forum_title, u2t.u2t_marked, u.uname, u.name 
			FROM ".$xoopsDB->prefix($com_dirname."_topics")." t 
			LEFT JOIN ".$xoopsDB->prefix($com_dirname."_forums")." f ON f.forum_id=t.forum_id 
			LEFT JOIN ".$xoopsDB->prefix($com_dirname."_posts")." p ON t.topic_last_post_id=p.post_id 
			LEFT JOIN ".$xoopsDB->prefix($mydirname."_diary")." d ON t.topic_external_link_id=d.bid 
			LEFT JOIN ".$xoopsDB->prefix($mydirname.'_category')." c ON ((d.uid=c.uid  OR c.uid='0') AND d.cid=c.cid)  
			LEFT JOIN ".$xoopsDB->prefix($mydirname.'_config')." cfg ON d.uid=cfg.uid 
			LEFT JOIN ".$xoopsDB->prefix($com_dirname."_users2topics")." u2t ON u2t.topic_id=t.topic_id AND u2t.uid=$uid 
			LEFT JOIN ".$xoopsDB->prefix('users')." u ON u.uid=t.topic_last_uid 
			WHERE ! t.topic_invisible AND (($whr_extid AND $whr_openarea) OR ($whr_cats_or_forums)) 
			AND ($whr_order) ORDER BY u2t.u2t_marked<=>1 DESC , $odr";
	} else {
		$sql = "SELECT t.topic_id, t.topic_title, t.topic_last_uid, t.topic_last_post_id, t.topic_last_post_time, 
			t.topic_views, t.topic_votes_count, t.topic_votes_sum, t.topic_posts_count, t.topic_external_link_id, 
			$sel_solved, t.forum_id, 
			p.post_id, p.subject, p.post_text, p.guest_name, p.html, p.smiley, p.xcode, p.br, p.unique_path, 
			f.forum_title, 0 AS u2t_marked, u.uname, u.name 
			FROM ".$xoopsDB->prefix($com_dirname."_topics")." t 
			LEFT JOIN ".$xoopsDB->prefix($com_dirname."_forums")." f ON f.forum_id=t.forum_id 
			LEFT JOIN ".$xoopsDB->prefix($com_dirname."_posts")." p ON t.topic_last_post_id=p.post_id 
			LEFT JOIN ".$xoopsDB->prefix($mydirname."_diary")." d ON t.topic_external_link_id=d.bid 
			LEFT JOIN ".$xoopsDB->prefix($mydirname.'_category')." c ON ((d.uid=c.uid  OR c.uid='0') AND d.cid=c.cid)  
			LEFT JOIN ".$xoopsDB->prefix($mydirname.'_config')." cfg ON d.uid=cfg.uid 
			LEFT JOIN ".$xoopsDB->prefix('users')." u ON u.uid=t.topic_last_uid 
			WHERE ! t.topic_invisible AND (($whr_extid AND $whr_openarea) OR ($whr_cats_or_forums)) 
			AND ($whr_order) ORDER BY $odr";
	}

	if( $result = $xoopsDB->query( $sql , $max_topics , 0 )) {

		$constpref = '_MB_' . strtoupper( $mydirname ) ;
		if($com_anchor_type==1) {
			$unique_key = "#post_id";
		} else {
			$unique_key = "#post_path";
		}

		$block = array( 
			'mydirname' => $mydirname ,
			'mod_url' => XOOPS_URL.'/modules/'.$com_dirname ,
			'mod_imageurl' => XOOPS_URL.'/modules/'.$com_dirname.'/'.$configs['images_dir'] ,
			'categories' => $categories4assign ,
			'forums' => $forums4assign ,
			'full_view' => $show_fullsize ,
			'unique_key' => $unique_key ,
			'use_aggre' => $use_aggre ,
			'com_forum_id' => $com_forum_id ,
			'mod_config' => $mod_config ,
			'lang_forum' => constant($constpref.'_FORUM') ,
			'lang_topic' => constant($constpref.'_TOPIC') ,
			'lang_replies' => constant($constpref.'_REPLIES') ,
			'lang_views' => constant($constpref.'_VIEWS') ,
			'lang_votescount' => constant($constpref.'_VOTESCOUNT') ,
			'lang_votessum' => constant($constpref.'_VOTESSUM') ,
			'lang_lastpost' => constant($constpref.'_LASTPOST') ,
			'lang_linktosearch' => constant($constpref.'_LINKTOSEARCH') ,
			'lang_linktolistcategories' => constant($constpref.'_LINKTOLISTCATEGORIES') ,
			'lang_linktolistforums' => constant($constpref.'_LINKTOLISTFORUMS') ,
			'lang_linktolisttopics' => constant($constpref.'_LINKTOLISTTOPICS') ,
		) ;

	   while( $topic_row = $xoopsDB->fetchArray( $result ) ) {
		// d3comment overridings
		$can_display = true;	//default
		if( is_object( $d3com[intval($topic_row['forum_id'])]) ) {
			$d3com_obj = $d3com[intval($topic_row['forum_id'])];
			$external_link_id = intval($topic_row['topic_external_link_id']);
			if( ( $external_link_id = $d3com_obj->validate_id( $external_link_id ) ) === false ) {
				$can_display = false;
			}
		}
			
		if ($can_display == true) {	// naao
			if($com_anchor_type==1) {
				$unique_path = $topic_row['post_id'];
			} else {
				$unique_path = ltrim($topic_row['unique_path'], ".");
			}
			if($use_detail) {
				$temp_post_text = strip_tags( $myts->displayTarea(strip_tags($topic_row['post_text']), $topic_row['html'], $topic_row['smiley'], $topic_row['xcode'], 1, $topic_row['br'] ) );
			} else {
				$temp_post_text = "";
			}
		    $topic4assign = array(
			'id' => intval( $topic_row['topic_id'] ) ,
			'unique_path' => $unique_path ,
			'title' => $myts->makeTboxData4Show( $topic_row['topic_title'] ) ,
			'forum_id' => intval( $topic_row['forum_id'] ) ,
			'forum_title' => $myts->makeTboxData4Show( $topic_row['forum_title'] ) ,
			'replies' => $topic_row['topic_posts_count'] - 1 ,
			'views' => intval( $topic_row['topic_views'] ) ,
			'votes_count' => $topic_row['topic_votes_count'] ,
			'votes_sum' => intval( $topic_row['topic_votes_sum'] ) ,
			'last_post_id' => intval( $topic_row['topic_last_post_id'] ) ,
			'last_post_time' => intval( $topic_row['topic_last_post_time'] ) ,
			'last_post_time_formatted' => formatTimestamp($topic_row['topic_last_post_time'] , 'm' ) ,
			'last_uid' => intval( $topic_row['topic_last_uid'] ) ,
		//	'last_uname' => XoopsUser::getUnameFromId( $topic_row['topic_last_uid']) ,
			'last_uname' => !empty($topic_row['uname']) ? htmlspecialchars( $topic_row['uname'], ENT_QUOTES ) : "" ,
			'last_name' => !empty($topic_row['name']) ? htmlspecialchars( $topic_row['name'], ENT_QUOTES ) : "" ,
			'solved' => intval( $topic_row['topic_solved'] ) ,
			'u2t_marked' => intval( $topic_row['u2t_marked'] ) ,
			'link_id' => intval( $topic_row['topic_external_link_id'] ) ,
			'post_text' => $temp_post_text ,
			'guest_name' => !empty($topic_row['guest_name']) ? htmlspecialchars( $topic_row['guest_name'], ENT_QUOTES ) : 
					$GLOBALS['xoopsConfig']['anonymous'] ,
			'can_display' => intval( $can_display ) ,
		    ) ;
		    $block['topics'][] = $topic4assign ;
		    $reg_time[] = intval( $topic_row['topic_last_post_time'] );
		}	// end if( $can_display
	   }	// end while
	}	// end if( $result

	//var_dump($mPerm->mydirname);

	$d3dConf->debug_appendtime('b_d3com_topics');

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

function b_d3diary_d3comlist_topics_edit( $options )
{
	$mydirname = empty( $options[0] ) ? 'd3diary' : $options[0] ;
	$max_topics = empty( $options[1] ) ? 10 : intval( $options[1] ) ;
	$show_fullsize = empty( $options[2] ) ? false : true ;
	$now_order = empty( $options[3] ) ? 'time' : trim( $options[3] ) ;
	$request_uid = empty( $options[4] ) ? 0 : intval( $options[4] ) ;
	$is_markup = empty( $options[5] ) ? false : true ;
	$use_detail = empty( $options[6] ) ? false : true ;
	$this_template = empty( $options[7] ) ? 'db:'.$mydirname.'_block_d3comlist_topics.html' : trim( $options[7] ) ;

	$use_aggre = empty( $options[8] ) ? false : true ;
	$categories = empty( $options[9] ) ? array() : explode(',',$options[9]) ;
	$forums = empty( $options[10] ) ? array() : explode(',',$options[10]) ;

	if( preg_match( '/[^0-9a-zA-Z_-]/' , $mydirname ) ) die( 'Invalid mydirname' ) ;

	if( $show_fullsize ) {
		$fullyes_checked = "checked='checked'" ;
		$fullno_checked = "" ;
	} else {
		$fullno_checked = "checked='checked'" ;
		$fullyes_checked = "" ;
	}

	$orders = array(
		'time' => _MB_D3DIARY_ORDERTIMED ,
		'votes' => _MB_D3DIARY_ORDERVOTESD ,
		'points' => _MB_D3DIARY_ORDERPOINTSD ,
		'average' => _MB_D3DIARY_ORDERAVERAGED ,
	) ;
	$order_options = '' ;
	foreach( $orders as $order_value => $order_name ) {
		$selected = $order_value == $now_order ? "selected='selected'" : "" ;
		$order_options .= "<option value='$order_value' $selected>$order_name</option>\n" ;
	}

	if( $is_markup ) {
		$markupyes_checked = "checked='checked'" ;
		$markupno_checked = "" ;
	} else {
		$markupno_checked = "checked='checked'" ;
		$markupyes_checked = "" ;
	}
	
	if( $use_aggre ) {
		$use_aggreyes_checked = "checked='checked'" ;
		$use_aggreno_checked = "" ;
	} else {
		$use_aggreno_checked = "checked='checked'" ;
		$use_aggreyes_checked = "" ;
	}
	if( $use_detail ) {
		$use_detailyes_checked = "checked='checked'" ;
		$use_detailno_checked = "" ;
	} else {
		$use_detailno_checked = "checked='checked'" ;
		$use_detailyes_checked = "" ;
	}

	$categories = array_map( 'intval' , $categories ) ;
	$forums = array_map( 'intval' , $forums ) ;

	$form = "
		<input type='hidden' name='options[0]' value='$mydirname' />
		<label for='o1'>" . sprintf( _MB_D3DIARY_DISPLAY , "</label><input type='text' size='4' name='options[1]' id='o1' value='$max_topics' style='text-align:right;' />" ) . "
		<br />
		<label for='o2'>"._MB_D3DIARY_DISPLAYF."</label>&nbsp;:
		<input type='radio' name='options[2]' id='o21' value='1' $fullyes_checked /><label for='o21'>"._YES."</label>
		<input type='radio' name='options[2]' id='o20' value='0' $fullno_checked /><label for='o20'>"._NO."</label>
		<br />
		<label for='orderrule'>"._MB_D3DIARY_ORDERRULE."</label>&nbsp;:
		<select name='options[3]' id='orderrule'>
			$order_options
		</select>
		<br />
		<label for='request_uid'>"._MB_D3DIARY_REQ_UID."</label>&nbsp;:
		<input type='text' size='20' name='options[4]' id='request_uid' value='$request_uid' style='text-align:right;' />
		<br />
		<label for='ismarkup'>"._MB_D3DIARY_MARKISUP."</label>&nbsp;:
		<input type='radio' name='options[5]' id='markupyes' value='1' $markupyes_checked /><label for='markupyes'>"._YES."</label>
		<input type='radio' name='options[5]' id='markupno' value='0' $markupno_checked /><label for='markupno'>"._NO."</label>
		<br />
		<label for='o6'>"._MB_D3DIARY_ASSIGNDETAIL."</label>&nbsp;:
		<input type='radio' name='options[6]' id='o61' value='1' $use_detailyes_checked /><label for='o61'>"._YES."</label>
		<input type='radio' name='options[6]' id='o60' value='0' $use_detailno_checked /><label for='o60'>"._NO."</label>
		<br />
		<label for='this_template'>"._MB_D3DIARY_THISTEMPLATE."</label>&nbsp;:
		<input type='text' size='60' name='options[7]' id='this_template' value='".htmlspecialchars($this_template,ENT_QUOTES)."' />
		<br />
		<hr />
		
		<label for='o8'>"._MB_D3DIARY_USEAGGRE."</label>&nbsp;:
		<input type='radio' name='options[8]' id='o81' value='1' $use_aggreyes_checked /><label for='o81'>"._YES."</label>
		<input type='radio' name='options[8]' id='o80' value='0' $use_aggreno_checked /><label for='o80'>"._NO."</label>
		<br />
		<label for='categories'>"._MB_D3DIARY_CATLIMIT."</label>&nbsp;:
		<input type='text' size='20' name='options[9]' id='categories' value='".implode(',',$categories)."' />"._MB_D3DIARY_CATLIMITDSC."
		<br />
		<label for='forums'>"._MB_D3DIARY_FORUMLIMIT."</label>&nbsp;:
		<input type='text' size='20' name='options[10]' id='forums' value='".implode(',',$forums)."' />"._MB_D3DIARY_FORUMLIMITDSC."
		<br />
	\n" ;

	return $form;
}

?>