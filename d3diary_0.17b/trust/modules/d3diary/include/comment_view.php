<?php

if (!defined('XOOPS_ROOT_PATH') || !is_object($xoopsModule)) {
	exit();
}
include_once XOOPS_ROOT_PATH.'/include/comment_constants.php';
if( defined( 'XOOPS_CUBE_LEGACY')){
	include_once XOOPS_ROOT_PATH.'/modules/legacy/include/xoops2_system_constants.inc.php';
}else{
	include_once XOOPS_ROOT_PATH.'/modules/system/constants.php';
}

if (XOOPS_COMMENT_APPROVENONE != $xoopsModuleConfig['com_rule']) {

	$gperm_handler = & xoops_gethandler( 'groupperm' );
	$groups = ( $xoopsUser ) ? $xoopsUser -> getGroups() : XOOPS_GROUP_ANONYMOUS;
	$xoopsTpl->assign( 'xoops_iscommentadmin', $gperm_handler->checkRight( 'system_admin', XOOPS_SYSTEM_COMMENT, $groups) );

 	include_once XOOPS_ROOT_PATH.'/language/'.$xoopsConfig['language'].'/comment.php';
	$comment_config = $xoopsModule->getInfo('comments');
	$com_itemid = (trim($comment_config['itemName']) != '' && isset($_GET[$comment_config['itemName']])) ? intval($_GET[$comment_config['itemName']]) : 0;

	if ($com_itemid > 0) {
		$com_mode = isset($_GET['com_mode']) ? htmlspecialchars(trim($_GET['com_mode']), ENT_QUOTES) : '';
		if ($com_mode == '') {
			if (is_object($xoopsUser)) {
				$com_mode = $xoopsUser->getVar('umode');
			} else {
				$com_mode = $xoopsConfig['com_mode'];
			}
		}
		$xoopsTpl->assign('comment_mode', $com_mode);

		if (!isset($_GET['com_order'])) {
			if (is_object($xoopsUser)) {
				$com_order = $xoopsUser->getVar('uorder');
			} else {
				$com_order = $xoopsConfig['com_order'];
			}
		} else {
			$com_order = intval($_GET['com_order']);
		}
		if ($com_order != XOOPS_COMMENT_OLD1ST) {
			$xoopsTpl->assign(array('comment_order' => XOOPS_COMMENT_NEW1ST, 'order_other' => XOOPS_COMMENT_OLD1ST));
			$com_dborder = 'DESC';
		} else {
			$xoopsTpl->assign(array('comment_order' => XOOPS_COMMENT_OLD1ST, 'order_other' => XOOPS_COMMENT_NEW1ST));
			$com_dborder = 'ASC';
		}
		// admins can view all comments and IPs, others can only view approved(active) comments
		if (is_object($xoopsUser) && $xoopsUser->isAdmin($xoopsModule->getVar('mid'))) {
			$admin_view = true;
		} else {
			$admin_view = false;
		}

		$com_id = isset($_GET['com_id']) ? intval($_GET['com_id']) : 0;
		$com_rootid = isset($_GET['com_rootid']) ? intval($_GET['com_rootid']) : 0;
		$comment_handler =& xoops_gethandler('comment');
		// flat view only
		$comments =& $comment_handler->getByItemId($xoopsModule->getVar('mid'), $com_itemid, $com_dborder);
		include_once XOOPS_ROOT_PATH.'/class/commentrenderer.php';
		$renderer =& XoopsCommentRenderer::instance($xoopsTpl);
		$renderer->setComments($comments);
		$renderer->renderFlatView($admin_view);

		// assign comment nav bar
		$navbar = '
<form method="get" action="'.$comment_config['pageName'].'">
<table width="95%" cellspacing="1">
  <tr>
    <td class="even">'._MD_COMVIEW_SETTING.'<select name="com_mode"><option value="flat"';
		if ($com_mode == 'flat') {
			$navbar .= ' selected="selected"';
		}
		$navbar .= '>'._FLAT.'</option><option value="thread"';
		if ($com_mode == 'thread' || $com_mode == '') {
			$navbar .= ' selected="selected"';
		}
		$navbar .= '>'. _THREADED .'</option><option value="nest"';
		if ($com_mode == 'nest') {
			$navbar .= ' selected="selected"';
		}
		$navbar .= '>'. _NESTED .'</option></select> <select name="com_order"><option value="'.XOOPS_COMMENT_OLD1ST.'"';
		if ($com_order == XOOPS_COMMENT_OLD1ST) {
			$navbar .= ' selected="selected"';
		}
		$navbar .= '>'. _OLDESTFIRST .'</option><option value="'.XOOPS_COMMENT_NEW1ST.'"';
		if ($com_order == XOOPS_COMMENT_NEW1ST) {
			$navbar .= ' selected="selected"';
		}
		unset($postcomment_link);
		$navbar .= '>'. _NEWESTFIRST .'</option></select><input type="hidden" name="'.$comment_config['itemName'].'" value="'.$com_itemid.'" /> <input type="submit" value="'.constant("_MD_SETTING").'" class="formButton" />';
		if (!empty($xoopsModuleConfig['com_anonpost']) || is_object($xoopsUser)) {
			$postcomment_link = 'comment_new.php?com_itemid='.$com_itemid.'&amp;com_order='.$com_order.'&amp;com_mode='.$com_mode;

			$xoopsTpl->assign('anon_canpost', true);
		}
		$link_extra = '';
		if (isset($comment_config['extraParams']) && is_array($comment_config['extraParams'])) {
			foreach ($comment_config['extraParams'] as $extra_param) {
			    if (isset(${$extra_param})) {
			        $link_extra .= '&amp;'.$extra_param.'='.${$extra_param};
			        $hidden_value = htmlspecialchars(${$extra_param}, ENT_QUOTES);
			        $extra_param_val = ${$extra_param};
			    } elseif (isset($_POST[$extra_param])) {
			        $extra_param_val = $_POST[$extra_param];
			    } elseif (isset($_GET[$extra_param])) {
			        $extra_param_val = $_GET[$extra_param];
			    }
			    if (isset($extra_param_val)) {
			        $link_extra .= '&amp;'.$extra_param.'='.$extra_param_val;
			        $hidden_value = htmlspecialchars($extra_param_val, ENT_QUOTES);
					$navbar .= '<input type="hidden" name="'.$extra_param.'" value="'.$hidden_value.'" />';
				}
			}
		}
//		if (isset($postcomment_link)) {
//			$navbar .= '&nbsp;<input type="button" onclick="self.location.href=\''.$postcomment_link.''.$link_extra.'\'" class="formButton" value="'._CM_POSTCOMMENT.'" />';
//		}
		$navbar .= '
    </td>
  </tr>
</table>
</form>';
		$xoopsTpl->assign(array('commentsnav' => $navbar, 'editcomment_link' => 'comment_edit.php?com_itemid='.$com_itemid.'&amp;com_order='.$com_order.'&amp;com_mode='.$com_mode.''.$link_extra, 'deletecomment_link' => 'comment_delete.php?com_itemid='.$com_itemid.'&amp;com_order='.$com_order.'&amp;com_mode='.$com_mode.''.$link_extra, 'replycomment_link' => 'comment_reply.php?com_itemid='.$com_itemid.'&amp;com_order='.$com_order.'&amp;com_mode='.$com_mode.''.$link_extra));

		// assign some lang variables
		$xoopsTpl->assign(array('lang_from' => _CM_FROM, 'lang_joined' => _CM_JOINED, 'lang_posts' => _CM_POSTS, 'lang_poster' => _CM_POSTER, 'lang_thread' => _CM_THREAD, 'lang_edit' => _EDIT, 'lang_delete' => _DELETE, 'lang_reply' => _REPLY, 'lang_subject' => _CM_REPLIES, 'lang_posted' => _CM_POSTED, 'lang_updated' => _CM_UPDATED, 'lang_notice' => _CM_NOTICE));

		$newform="
<form name='commentform' id='commentform' action='comment_post.php' method='post' onsubmit='return xoopsFormValidate_commentform();'>

<table width='100%' cellspacing='1'>
<tr valign='top' align='left'>
<td class='head'>".constant("_MD_DIARY_TITLE")."</td>
<td class='even'>
<input type='text' name='com_title' id='com_title' size='50' maxlength='255' value='$yr_comment_title' />
</td>
</tr>
<tr valign='top' align='left'>
<td class='head'>".constant("_MD_COMMENT")."</td>
<td class='even'>
<a name='moresmiley'></a>
<textarea id='com_text' name='com_text' onselect=\"xoopsSavePosition('com_text');\" onclick=\"xoopsSavePosition('com_text');\" onkeyup=\"xoopsSavePosition('com_text');\" cols='50' rows='10'></textarea>
<br />".constant("_MD_SMILY_MENU")." 
";
		$sql = "SELECT *
			  FROM ".$xoopsDB->prefix('smiles')."
	          WHERE display=1";
		$result = $xoopsDB->query($sql);
		while ( $dbdat = $xoopsDB->fetchArray($result) ) {
			$newform.="<img onclick='xoopsCodeSmilie(\"com_text\", \" ".$dbdat['code']." \");' onmouseover='style.cursor=\"hand\"' src='".XOOPS_URL."/uploads/".$dbdat['smile_url']."' alt='".$dbdat['emotion']."' />";
		}
		$newform.="
&nbsp;[<a href='#moresmiley' onclick='javascript:openWithSelfMain(\"".XOOPS_URL."/misc.php?action=showpopups&amp;type=smilies&amp;target=com_text\",\"smilies\",300,475);'>".constant("_MD_MORE")."</a>]
</td>
</tr>
<tr valign='top' align='left'>
<td class='head'>
<input type='hidden' name='dohtml' id='dohtml' value='0' />
<input type='hidden' name='dosmiley' id='dosmiley' value='1' />
<input type='hidden' name='doxcode' id='doxcode' value='1' />
<input type='hidden' name='dobr' id='dobr' value='1' />

<input type='hidden' name='com_pid' id='com_pid' value='0' />
<input type='hidden' name='com_rootid' id='com_rootid' value='0' />
<input type='hidden' name='com_id' id='com_id' value='0' />
<input type='hidden' name='com_itemid' id='com_itemid' value='".$com_itemid."' />
<input type='hidden' name='com_order' id='com_order' value='0' />
<input type='hidden' name='com_mode' id='com_mode' value='flat' />
</td>
<td class='even'>
<input type='submit' class='formButton' name='com_dopost'  id='com_dopost' value='".constant("_MD_SUBMIT")."' />
</td></tr></table></form>

<!-- Start Form Vaidation JavaScript //-->
<script type='text/javascript'>
<!--//
function xoopsFormValidate_commentform() {
    myform = window.document.commentform;
if ( myform.com_title.value == '' ) { window.alert('".constant("_MD_COMMENT_ERR_TITLE")."'); myform.com_title.focus(); return false; }
if ( myform.com_text.value == '' ) { window.alert('".constant("_MD_COMMENT_ERR_COM")."'); myform.com_text.focus(); return false; }
return true;
}
//--></script>
<!-- End Form Vaidation JavaScript //-->
		";
		$xoopsTpl->assign("yr_comment_form", $newform);
	}
}
?>