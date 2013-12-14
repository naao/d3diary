<?php
function b_d3diary_d3comlist_posts_show( $options ){

	global $xoopsDB;

	$mydirname = empty( $options[0] ) ? 'd3diary' : $options[0] ;
	$max_posts = empty( $options[1] ) ? 10 : intval( $options[1] ) ;
	$now_order = empty( $options[2] ) ? 'time' : trim( $options[2] ) ;
	$req_uid = empty( $options[3] ) ? 0 : intval( $options[3] ) ;
	$use_detail = empty( $options[4] ) ? false : true ;
	$this_template = empty( $options[5] ) ? 'db:'.$mydirname.'_block_d3comlist_posts.html' : trim( $options[5] ) ;

	$use_aggre = empty( $options[6] ) ? false : true ;
	$categories = empty( $options[7] ) ? array() : explode(',',$options[7]) ;
	$forums = empty( $options[8] ) ? array() : explode(',',$options[8]) ;

	require_once dirname( dirname(__FILE__) ).'/class/d3diaryConf.class.php';
	$d3dConf = & D3diaryConf::getInstance($mydirname, 0, "b_d3comlist_posts");
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

	// order
	$whr_order = '1' ;
	switch( $now_order ) {
		case 'votes':
			$odr = 'p.votes_count DESC';
			break;
		case 'points':
			$odr = 'p.votes_sum DESC';
			break;
		case 'average':
			$odr = 'p.votes_sum/p.votes_count DESC, p.votes_count DESC';
			$whr_order = 'p.votes_count>0' ;
			break;
		case 'time':
		default:
			$odr = 'p.post_time DESC';
			break;
	}

    	// finally, query d3comments
	$whr_extid = 'f.forum_id='.$com_forum_id ;

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

	$sql = "SELECT p.post_id, p.subject, p.votes_sum, p.votes_count, p.post_time, p.post_text, p.uid, 
		p.guest_name, p.html, p.smiley, p.xcode, p.br, p.unique_path, u.uname, u.name,
		f.forum_id, f.forum_title, t.topic_external_link_id 
		FROM ".$xoopsDB->prefix($com_dirname."_posts")." p 
		LEFT JOIN ".$xoopsDB->prefix($com_dirname."_topics")." t ON p.topic_id=t.topic_id 
		LEFT JOIN ".$xoopsDB->prefix($com_dirname."_forums")." f ON f.forum_id=t.forum_id 
		LEFT JOIN ".$xoopsDB->prefix($mydirname."_diary")." d ON t.topic_external_link_id=d.bid 
		LEFT JOIN ".$xoopsDB->prefix($mydirname.'_category')." c ON ((d.uid=c.uid  OR c.uid='0') AND d.cid=c.cid) 
		LEFT JOIN ".$xoopsDB->prefix($mydirname.'_config')." cfg ON d.uid=cfg.uid 
		LEFT JOIN ".$xoopsDB->prefix('users')." u ON p.uid=u.uid 
		WHERE ! t.topic_invisible AND (($whr_extid AND $whr_openarea) OR ($whr_cats_or_forums)) 
			AND ($whr_order) ORDER BY $odr ";

	if( $result = $xoopsDB->query( $sql , $max_posts , 0 )) {

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
		while( $post_row = $xoopsDB->fetchArray( $result ) ) {
			// d3comment overridings
			$can_display = true;	//default
			if( is_object( $d3com[intval($post_row['forum_id'])]) ) {
				$d3com_obj = $d3com[intval($post_row['forum_id'])];
				$external_link_id = intval($post_row['topic_external_link_id']);
				if( ( $external_link_id = $d3com_obj->validate_id( $external_link_id ) ) === false ) {
					$can_display = false;
				}
			}
		   if ($can_display == true) {	// naao
			if($com_anchor_type==1) {
				$unique_path = $post_row['post_id'];
			} else {
				$unique_path = ltrim($post_row['unique_path'], ".");
			}
			if($use_detail) {
				$temp_post_text = strip_tags( $myts->displayTarea(strip_tags($post_row['post_text']), $post_row['html'], $post_row['smiley'], $post_row['xcode'], 1, $post_row['br'] ) );
			} else {
				$temp_post_text = "";
			}
			$post4assign = array(
				'id' => intval( $post_row['post_id'] ) ,
				'unique_path' => $unique_path ,
				'subject' => $myts->makeTboxData4Show( $post_row['subject'] ) ,
				'forum_id' => intval( $post_row['forum_id'] ) ,
				'forum_title' => $myts->makeTboxData4Show( $post_row['forum_title'] ) ,
				'votes_count' => $post_row['votes_count'] ,
				'votes_sum' => intval( $post_row['votes_sum'] ) ,
				'post_time' => intval( $post_row['post_time'] ) ,
				'post_time_formatted' => formatTimestamp( $post_row['post_time'] , 'm' ) ,
				'uid' => intval( $post_row['uid'] ) ,
				//'uname' => XoopsUser::getUnameFromId( $post_row['uid'] ) ,
				'uname' => !empty($post_row['uname']) ? $func->htmlspecialchars( $post_row['uname'] ) : "" ,
				'name' => !empty($post_row['name']) ? $func->htmlspecialchars( $post_row['name'] ) : "" ,
				'link_id' => intval( $post_row['topic_external_link_id'] ) ,
				'post_text' => $temp_post_text ,
				'guest_name' => !empty($post_row['guest_name']) ? $func->htmlspecialchars( $post_row['guest_name'] ) : 
						$GLOBALS['xoopsConfig']['anonymous'] ,
				'can_display' => intval( $can_display ) ,
			) ;
			$block['posts'][] = $post4assign ;
		    }	// end if( $can_display
	   }	// end while
	}	// end if( $result

	$d3dConf->debug_appendtime('b_d3com_posts');

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

function b_d3diary_d3comlist_posts_edit( $options )
{
	$mydirname = empty( $options[0] ) ? 'd3diary' : $options[0] ;
	require_once dirname( dirname(__FILE__) ).'/class/d3diaryConf.class.php';
	$d3dConf = D3diaryConf::getInstance($mydirname, 0, "b_d3comlist_posts");
	$func =& $d3dConf->func ;

	$max_posts = empty( $options[1] ) ? 10 : intval( $options[1] ) ;
	$now_order = empty( $options[2] ) ? 'time' : trim( $options[2] ) ;
	$request_uid = empty( $options[3] ) ? 0 : intval( $options[3] ) ;
	$use_detail = empty( $options[4] ) ? false : true ;
	$this_template = empty( $options[5] ) ? 'db:'.$mydirname.'_block_d3comlist_posts.html' : trim( $options[5] ) ;

	$use_aggre = empty( $options[6] ) ? false : true ;
	$categories = empty( $options[7] ) ? array() : explode(',',$options[7]) ;
	$forums = empty( $options[8] ) ? array() : explode(',',$options[8]) ;

	if( preg_match( '/[^0-9a-zA-Z_-]/' , $mydirname ) ) die( 'Invalid mydirname' ) ;

	$orders = array(
		'time' => _MB_D3DIARY_ORDERTIMED ,
		'views' => _MB_D3DIARY_ORDERVIEWSD ,
		'replies' => _MB_D3DIARY_ORDERREPLIESD ,
		'votes' => _MB_D3DIARY_ORDERVOTESD ,
		'points' => _MB_D3DIARY_ORDERPOINTSD ,
		'average' => _MB_D3DIARY_ORDERAVERAGED ,
	) ;
	
	$order_options = '' ;
	foreach( $orders as $order_value => $order_name ) {
		$selected = $order_value == $now_order ? "selected='selected'" : "" ;
		$order_options .= "<option value='$order_value' $selected>$order_name</option>\n" ;
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
		<label for='o1'>" . sprintf( _MB_D3DIARY_DISPLAY , "</label><input type='text' size='4' name='options[1]' id='o1' value='$max_posts' style='text-align:right;' />" ) . "
		<br />
		<label for='orderrule'>"._MB_D3DIARY_ORDERRULE."</label>&nbsp;:
		<select name='options[2]' id='orderrule'>
			$order_options
		</select>
		<br />
		<label for='request_uid'>"._MB_D3DIARY_REQ_UID."</label>&nbsp;:
		<input type='text' size='20' name='options[3]' id='request_uid' value='$request_uid' style='text-align:right;' />
		<br />
		<label for='o4'>"._MB_D3DIARY_ASSIGNDETAIL."</label>&nbsp;:
		<input type='radio' name='options[4]' id='o41' value='1' $use_detailyes_checked /><label for='o41'>"._YES."</label>
		<input type='radio' name='options[4]' id='o40' value='0' $use_detailno_checked /><label for='o40'>"._NO."</label>
		<br />
		<label for='this_template'>"._MB_D3DIARY_THISTEMPLATE."</label>&nbsp;:
		<input type='text' size='60' name='options[5]' id='this_template' value='".$func->htmlspecialchars($this_template)."' />
		<br />
		<hr />
		
		<label for='o6'>"._MB_D3DIARY_USEAGGRE."</label>&nbsp;:
		<input type='radio' name='options[6]' id='o51' value='1' $use_aggreyes_checked /><label for='o61'>"._YES."</label>
		<input type='radio' name='options[6]' id='o50' value='0' $use_aggreno_checked /><label for='o60'>"._NO."</label>
		<br />
		<label for='categories'>"._MB_D3DIARY_CATLIMIT."</label>&nbsp;:
		<input type='text' size='20' name='options[7]' id='categories' value='".implode(',',$categories)."' />"._MB_D3DIARY_CATLIMITDSC."
		<br />
		<label for='forums'>"._MB_D3DIARY_FORUMLIMIT."</label>&nbsp;:
		<input type='text' size='20' name='options[8]' id='forums' value='".implode(',',$forums)."' />"._MB_D3DIARY_FORUMLIMITDSC."
		<br />
	\n" ;

	return $form;
}

?>