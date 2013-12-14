<?php
//	global $xoopsConfig;
// language file (modinfo.php)
$langmanpath = XOOPS_TRUST_PATH.'/libs/altsys/class/D3LanguageManager.class.php' ;
if( ! file_exists( $langmanpath ) ) die( 'install the latest altsys' ) ;
require_once( $langmanpath ) ;
$langman =& D3LanguageManager::getInstance() ;
$langman->read( 'modinfo.php' , $mydirname , $mytrustdirname , false ) ;

$constpref = '_MI_' . strtoupper( $mydirname ) ;

$modversion['name']        = $mydirname;
$modversion['version']     = 0.31;
$modversion['detailed_version'] = '0.31.0' ;
$modversion['description'] = constant($constpref."_DIARY_DESC");
$modversion['credits']     = 'Motion Create Inc. (http://www.mc8.jp/)';
$modversion['author'] 	   = 'naaon (original-module "minidiary" by matoyan)';
$modversion['help']        = 'naao http://www.naaon.com/';
$modversion['license']     = 'GPL see LICENSE';
$modversion['official']    = 0;
$modversion['image']       = file_exists( $mydirpath.'/module_icon.png' ) ? 'module_icon.png' : 'module_icon.php' ;
$modversion['dirname']     = $mydirname;
$modversion['trust_dirname'] = $mytrustdirname ;
$modversion['read_any'] = true ;

// Any tables can't be touched by modulesadmin.
$modversion['sqlfile'] = false ;
$modversion['tables'] = array() ;

// Admin things
$modversion['hasAdmin']   = 1;
$modversion['adminindex'] = 'admin/index.php';
$modversion['adminmenu']  = 'admin/admin_menu.php';

// Search
$modversion['hasSearch'] = 1;
$modversion['search']['file'] = "search.php";
$modversion['search']['func'] = $mydirname."_global_search";

// Main contents
$modversion['hasMain'] = 1;

// All Templates can't be touched by modulesadmin.
$modversion['templates'] = array() ;

$modversion['config'][] = array(
	'name'			=> 'menu_layout' ,
	'title'			=> $constpref.'_MENU_LAYOUT' ,
	'description'		=> $constpref.'_MENU_LAYOUTDESC' ,
	'formtype'		=> 'select',
	'valuetype'		=> 'int',
	'default'		=> 0,
	'options'		=> array( $constpref.'_MENU_LAYOUT_RIGHT' => 0, 
		  					  $constpref.'_MENU_LAYOUT_LEFT' => 1,
		  					  $constpref.'_MENU_LAYOUT_NONE' => 2)
);
$modversion['config'][] = array(
	'name'			=> 'rightarea_width' ,
	'title'			=> $constpref.'_RIGHT_WEIDTH' ,
	'description'		=> $constpref.'_RIGHT_WEIDTHDESC' ,
	'formtype'		=> 'textbox',
	'valuetype'		=> 'int',
	'default'		=> 140
);
$modversion['config'][] = array(
	'name'			=> 'use_name' ,
	'title'			=> $constpref.'_USENAME' ,
	'description'		=> $constpref.'_USENAMEDESC' ,
	'formtype'		=> 'select',
	'valuetype'		=> 'int',
	'default'		=> '0',
	'options'		=> array( $constpref.'_USENAME_UNAME' => 0, 
		  					  $constpref.'_USENAME_NAME' => 1)
);
$modversion['config'][] = array(
	'name'			=> 'show_breadcrumbs' ,
	'title'			=> $constpref.'_BREADCRUMBS' ,
	'description'	=> $constpref.'_BREADCRUMBSDESC' ,
	'formtype'		=> 'yesno' ,
	'valuetype'		=> 'int' ,
	'default'		=> 1 ,
	'options'		=> array()
) ;
$modversion['config'][] = array(
	'name'			=> 'preview_charmax' ,
	'title'			=> $constpref.'_PREV_CHARMAX' ,
	'description'		=> $constpref.'_PREV_CHARMAXDESC' ,
	'formtype'		=> 'textbox',
	'valuetype'		=> 'int',
	'default'		=> 80
);

$modversion['config'][] = array(
	'name'			=> 'block_diarynum' ,
	'title'			=> $constpref.'_BLK_DNUM' ,
	'description'		=> $constpref.'_BLK_DNUMDESC' ,
	'formtype'		=> 'textbox',
	'valuetype'		=> 'int',
	'default'		=> '30'
);

$modversion['config'][] = array(
	'name'			=> 'photo_maxsize' ,
	'title'			=> $constpref.'_PHOTO_MAXSIZE' ,
	'description'		=> $constpref.'_PHOTO_MAXSIZEDESC' ,
	'formtype'		=> 'textbox',
	'valuetype'		=> 'int',
	'default'		=> 2048
);

$modversion['config'][] = array(
	'name'			=> 'photo_useresize' ,
	'title'			=> $constpref.'_PHOTO_USERESIZE' ,
	'description'		=> $constpref.'_PHOTO_USERESIZEDESC' ,
	'formtype'		=> 'select',
	'valuetype'		=> 'int',
	'default'		=> '1',
	'options'		=> array( $constpref.'_PHOTO_USERESIZE_N' => 0, 
		  					  $constpref.'_PHOTO_USERESIZE_Y' => 1)
);

$modversion['config'][] = array(
	'name'			=> 'photo_maxpics' ,
	'title'			=> $constpref.'_PHOTO_MAXPICS' ,
	'description'		=> $constpref.'_PHOTO_MAXPICSDESC' ,
	'formtype'		=> 'select',
	'valuetype'		=> 'int',
	'default'		=> '3',
	'options'		=> array( '3' => 3, '6' => 6, '9' => 9)
);

$modversion['config'][] = array(
	'name'			=> 'photo_thumbsize' ,
	'title'			=> $constpref.'_PHOTO_THUMBSIZE' ,
	'description'		=> $constpref.'_PHOTO_THUMBSIZEDESC' ,
	'formtype'		=> 'textbox',
	'valuetype'		=> 'int',
	'default'		=> 160
);

$modversion['config'][] = array(
	'name'			=> 'photo_useinfo' ,
	'title'			=> $constpref.'_PHOTO_USEINFO' ,
	'description'		=> $constpref.'_PHOTO_USEINFODESC' ,
	'formtype'		=> 'yesno' ,
	'valuetype'		=> 'int' ,
	'default'		=> '0'
) ;

$modversion['config'][] = array(
	'name'			=> 'use_avatar' ,
	'title'			=> $constpref.'_USE_AVATAR' ,
	'description'		=> $constpref.'_USE_AVATARDESC' ,
	'formtype'		=> 'yesno' ,
	'valuetype'		=> 'int' ,
	'default'		=> '0'
) ;

$modversion['config'][] = array(
	'name'			=> 'use_open_cat' ,
	'title'			=> $constpref.'_USE_OPEN_CAT' ,
	'description'		=> $constpref.'_USE_OPEN_CATDESC' ,
	'formtype'		=> 'select',
	'valuetype'		=> 'int',
	'default'		=> '0',
	'options'		=> array( $constpref.'_USE_OPEN_CAT_N' => 0, 
		  					  $constpref.'_USE_OPEN_CAT_Y' => 1,
		  					  $constpref.'_USE_OPEN_CAT_G' => 10,
		  					  $constpref.'_USE_OPEN_CAT_P' => 20)
);

$modversion['config'][] = array(
	'name'			=> 'use_open_entry' ,
	'title'			=> $constpref.'_USE_OPEN_ENTRY' ,
	'description'		=> $constpref.'_USE_OPEN_ENTRYDESC' ,
	'formtype'		=> 'select',
	'valuetype'		=> 'int',
	'default'		=> '0',
	'options'		=> array( $constpref.'_USE_OPEN_ENTRY_N' => 0, 
		  					  $constpref.'_USE_OPEN_ENTRY_Y' => 1,
		  					  $constpref.'_USE_OPEN_ENTRY_G' => 10,
		  					  $constpref.'_USE_OPEN_ENTRY_P' => 20)
);

$modversion['config'][] = array(
	'name'			=> 'use_friend' ,
	'title'			=> $constpref.'_USE_FRIEND' ,
	'description'		=> $constpref.'_USE_FRIENDDESC' ,
	'formtype'		=> 'select',
	'valuetype'		=> 'int',
	'default'		=> '0',
	'options'		=> array( $constpref.'_USE_FRIEND_N' => 0, 
		  					  $constpref.'_USE_XSNS_Y' => 1, 
		  					  $constpref.'_USE_MYFRIENDS_Y' => 2)
);

$modversion['config'][] = array(
	'name'			=> 'friend_dirname' ,
	'title'			=> $constpref.'_FRIEND_DIRNAME' ,
	'description'		=> $constpref.'_FRIEND_DIRNAMEDESC' ,
	'formtype'		=> 'textbox',
	'valuetype'		=> 'text',
	'default'		=> ''
);

$modversion['config'][] = array(
	'name'			=> 'can_read_excerpt' ,
	'title'			=> $constpref.'_EXCERPTOK' ,
	'description'		=> $constpref.'_EXCERPTOKDESC' ,
	'formtype'		=> 'select',
	'valuetype'		=> 'int',
	'default'		=> '0',
	'options'		=> array( $constpref.'_EXCERPTOK_NOUSE' => 0, 
		  					  $constpref.'_EXCERPTOK_FORMEMBER' => 2,
		  					  $constpref.'_EXCERPTOK_FORGUEST' => 3)
);
		  					  //$constpref.'_EXCERPTOK_BYPERSON' => 1, 
$modversion['config'][] = array(
	'name'			=> 'can_disp_com' ,
	'title'			=> $constpref.'_DISP_EXCERPTCOM' ,
	'description'		=> $constpref.'_DISP_EXCERPTCOMDESC' ,
	'formtype'		=> 'yesno' ,
	'valuetype'		=> 'int' ,
	'default'		=> '0'
);

$modversion['config'][] = array(
	'name'			=> 'use_tag' ,
	'title'			=> $constpref.'_USE_TAG' ,
	'description'		=> $constpref.'_USE_TAGDESC' ,
	'formtype'		=> 'select',
	'valuetype'		=> 'int',
	'default'		=> '0',
	'options'		=> array( $constpref.'_USE_TAG_N' => 0, 
		  					  $constpref.'_USE_TAG_INDEXONLY' => 1, 
		  					  $constpref.'_USE_TAG_ALSODIARYLIST' => 2,
		  					  $constpref.'_USE_TAG_BLOCK' => 3)
);

$modversion['config'][] = array(
	'name'			=> 'body_editor' ,
	'title'			=> $constpref.'_BODY_EDITOR' ,
	'description'		=> $constpref.'_BODY_EDITORDSC' ,
	'formtype'		=> 'select' ,
	'valuetype'		=> 'text' ,
	'default'		=> 'small' ,
	'options'		=> array( 'simple' => 'simple' , 'xoopsdhtml' => 'xoopsdhtml' )
) ;

$modversion['config'][] = array(
	'name'			=> 'body_htmleditor' ,
	'title'			=> $constpref.'_BODY_HTMLEDITOR' ,
	'description'		=> $constpref.'_BODY_HTMLEDITORDSC' ,
	'formtype'		=> 'select' ,
	'valuetype'		=> 'text' ,
	'default'		=> 'small' ,
	'options'		=> array( 'no' => 'no' , 'common/fckeditor' => 'common_fckeditor' )
) ;

$modversion['config'][] = array(
	'name'			=> 'htmlpurify_except' ,
	'title'			=> $constpref.'_HTMLPR_EXCEPT' ,
	'description'		=> $constpref.'_HTMLPR_EXCEPTDSC' ,
	'formtype'		=> 'group_multi' ,
	'valuetype'		=> 'array' ,
	'default'		=> array(1,2,4) ,
	'options'		=> array()
) ;

$modversion['config'][] = array(
	'name'			=> 'gticket_timeout' ,
	'title'			=> $constpref.'_GTICKET_SET_TIME' ,
	'description'		=> $constpref.'_GTICKET_SET_TIMEDSC' ,
	'formtype'		=> 'textbox',
	'valuetype'		=> 'int',
	'default'		=> '1800'
) ;

$modversion['config'][] = array(
	'name'			=> 'use_updateping' ,
	'title'			=> $constpref.'_USE_UPDATEPING' ,
	'description'		=> $constpref.'_USE_UPDATEPING_DSC' ,
	'formtype'		=> 'yesno' ,
	'valuetype'		=> 'int' ,
	'default'		=> '0'
) ;

$modversion['config'][] = array(
	'name'			=> 'updateping_url' ,
	'title'			=> $constpref.'_UPDATEPING' ,
	'description'		=> $constpref.'_UPDATEPING_DSC' ,
	'formtype'		=> 'textarea' ,
	'valuetype'		=> 'text' ,
	'default'		=> constant($constpref.'_UPDATEPING_SERVERS')
) ;

$modversion['config'][] = array(
	'name'			=> 'enc_from' ,
	'title'			=> $constpref.'_ENC_FROM' ,
	'description'		=> $constpref.'_ENC_FROMDSC' ,
	'formtype'		=> 'select' ,
	'valuetype'		=> 'text' ,
	'default'		=> 'small' ,
	'options'		=> array( 'default' => 'default' , 'xoops_charset' => 'xoops_charset' , 'auto' => 'auto' )
) ;

$modversion['config'][] = array(
	'name'			=> 'permission_class' ,
	'title'			=> $constpref.'_PERM_CLASS' ,
	'description'		=> $constpref.'_PERM_CLASSDSC' ,
	'formtype'		=> 'textbox' ,
	'valuetype'		=> 'text' ,
	'default'		=> 'd3diaryPermission' ,
	'options'		=> array()
) ;

$modversion['config'][] = array(
	'name'			=> 'use_mailpost' ,
	'title'			=> $constpref.'_USE_MAILPOST' ,
	'description'		=> $constpref.'_USE_MAILPOSTDSC' ,
	'formtype'		=> 'yesno' ,
	'valuetype'		=> 'int' ,
	'default'		=> '0'
) ;

$modversion['config'][] = array(
	'name'			=> 'pop3_server' ,
	'title'			=> $constpref.'_POP3_SERVER' ,
	'description'		=> $constpref.'_POP3_SERVER_DESC' ,
	'formtype'		=> 'textbox',
	'valuetype'		=> 'text',
	'default'		=> ''
);

$modversion['config'][] = array(
	'name'			=> 'pop3_port' ,
	'title'			=> $constpref.'_POP3_PORT' ,
	'description'		=> $constpref.'_POP3_PORT_DESC' ,
	'formtype'		=> 'textbox',
	'valuetype'		=> 'int',
	'default'		=> '110'
);

$modversion['config'][] = array(
	'name'			=> 'pop3_apop' ,
	'title'			=> $constpref.'_POP3_APOP' ,
	'description'		=> $constpref.'_POP3_APOP_DESC' ,
	'formtype'		=> 'yesno' ,
	'valuetype'		=> 'int' ,
	'default'		=> '0'
);

$modversion['config'][] = array(
	'name'			=> 'post_email_address' ,
	'title'			=> $constpref.'_POST_EMAIL_ADDRESS' ,
	'description'		=> $constpref.'_POST_EMAIL_ADDRESS_DESC' ,
	'formtype'		=> 'textbox',
	'valuetype'		=> 'text',
	'default'		=> ''
);

$modversion['config'][] = array(
	'name'			=> 'post_email_password' ,
	'title'			=> $constpref.'_POST_EMAIL_PASSWORD' ,
	'description'		=> $constpref.'_POST_EMAIL_PASSWORD_DESC' ,
	'formtype'		=> 'password',
	'valuetype'		=> 'text',
	'default'		=> ''
);

$modversion['config'][] = array(
	'name'			=> 'post_email_fulladd' ,
	'title'			=> $constpref.'_POST_EMAIL_FULLADD' ,
	'description'		=> $constpref.'_POST_EMAIL_FULLADDDSC' ,
	'formtype'		=> 'textbox',
	'valuetype'		=> 'text',
	'default'		=> ''
);

$modversion['config'][] = array(
	'name'			=> 'post_detect_order' ,
	'title'			=> $constpref.'_POST_DETECT_ORDER' ,
	'description'		=> $constpref.'_POST_DETECT_ORDERDSC' ,
	'formtype'		=> 'textbox',
	'valuetype'		=> 'text',
	'default'		=> ''
);

$modversion['config'][] = array(
	'name'			=> 'comment_dirname' ,
	'title'			=> $constpref.'_COM_DIRNAME' ,
	'description'		=> $constpref.'_COM_DIRNAMEDSC' ,
	'formtype'		=> 'textbox',
	'valuetype'		=> 'text',
	'default'		=> ''
);

$modversion['config'][]= array(
	'name'	 		=> 'comment_forum_id',
	'title' 		=> $constpref.'_COM_FORUMID',
	'description'		=> $constpref.'_COM_FORUMIDDSC',
	'formtype'		=> 'textbox',
	'valuetype'		=> 'int',
	'default'		=> '0'
);

$modversion['config'][] = array(
	'name'			=> 'comment_order' ,
	'title'			=> $constpref.'_COM_ORDER' ,
	'description'		=> $constpref.'_COM_ORDERDSC' ,
	'formtype'		=> 'select' ,
	'valuetype'		=> 'text' ,
	'default'		=> 'desc' ,
	'options'		=> array( '_OLDESTFIRST' => 'asc' , '_NEWESTFIRST' => 'desc' )
) ;

$modversion['config'][] = array(
	'name'			=> 'comment_view' ,
	'title'			=> $constpref.'_COM_VIEW' ,
	'description'		=> $constpref.'_COM_VIEWDSC' ,
	'formtype'		=> 'select' ,
	'valuetype'		=> 'text' ,
	'default'		=> 'listposts_flat' ,
	'options'		=> array( '_FLAT' => 'listposts_flat' , '_THREADED' => 'listtopics' )
) ;

$modversion['config'][] = array(
	'name'			=> 'comment_posts_num' ,
	'title'			=> $constpref.'_COM_POSTSNUM' ,
	'description'		=> '' ,
	'formtype'		=> 'textbox' ,
	'valuetype'		=> 'int' ,
	'default'		=> '10'
) ;

$modversion['config'][] = array(
	'name'			=> 'comment_anchor_type' ,
	'title'			=> $constpref.'_COM_ANCHOR' ,
	'description'		=> $constpref.'_COM_ANCHORDSC' ,
	'formtype'		=> 'select' ,
	'valuetype'		=> 'text' ,
	'default'		=> '0' ,
	'options'		=> array( $constpref.'_USE_COM_ANCHOR_UNIQUEPATH' => 0, 
		  					  $constpref.'_USE_COM_ANCHOR_POSTNUM' => 1) 
) ;

$modversion['config'][] = array(
	'name'			=> 'use_simplecomment' ,
	'title'			=> $constpref.'_USE_SIMPLECOMMENT' ,
	'description'		=> $constpref.'_USE_SIMPLECOMMENTDESC' ,
	'formtype'		=> 'select',
	'valuetype'		=> 'int',
	'default'		=> '1',
	'options'		=> array( $constpref.'_USE_SIMPLECOMMENT_N' => 0, 
		  					  $constpref.'_USE_SIMPLECOMMENT_Y' => 1)
);

// Block
$i = 1;
$modversion['blocks'][$i] = array(
	'file'			=> 'blocks.php' ,
	'name'			=> constant($constpref.'_BLOCK_NEWENTRY') ,
	'description'		=> constant($constpref.'_BLOCK_NEWENTRYDSC') ,
	'show_func'		=> 'b_d3diary_list_show' ,
	'edit_func'		=> 'b_d3diary_list_edit' ,
	'options'		=> "$mydirname|10|time||" ,
	'template'		=> '' , // use "module" template instead
	'can_clone'		=> true ,
) ;

$i++;
$modversion['blocks'][$i] = array(
	'file'			=> 'blocks.php' ,
	'name'			=> constant($constpref.'_BLOCK_BLOGGER') ,
	'description'		=> constant($constpref.'_BLOCK_BLOGGERDSC') ,
	'show_func'		=> 'b_d3diary_bloggerlist_show' ,
	'edit_func'		=> 'b_d3diary_bloggerlist_edit' ,
	'options'		=> "$mydirname|10|time||" ,
	'template'		=> '' , // use "module" template instead
	'can_clone'		=> true ,
) ;

$i++;
$modversion['blocks'][$i] = array(
	'file'			=> 'blocks.php' ,
	'name'			=> constant($constpref.'_BLOCK_D3COMPOSTS') ,
	'description'		=> constant($constpref.'_BLOCK_D3COMPOSTSDSC') ,
	'show_func'		=> 'b_d3diary_d3comlist_posts_show' ,
	'edit_func'		=> 'b_d3diary_d3comlist_posts_edit' ,
	'options'		=> "$mydirname|10|time|0||" ,
	'template'		=> '' , // use "module" template instead
	'can_clone'		=> true ,
) ;

$i++;
$modversion['blocks'][$i] = array(
	'file'			=> 'blocks.php' ,
	'name'			=> constant($constpref.'_BLOCK_D3COMTOPICS') ,
	'description'		=> constant($constpref.'_BLOCK_D3COMTOPICSDSC') ,
	'show_func'		=> 'b_d3diary_d3comlist_topics_show' ,
	'edit_func'		=> 'b_d3diary_d3comlist_topics_edit' ,
	'options'		=> "$mydirname|10|time|0|||" ,
	'template'		=> '' , // use "module" template instead
	'can_clone'		=> true ,
) ;

$i++;
$modversion['blocks'][$i] = array(
	'file'			=> 'blocks.php' ,
	'name'			=> constant($constpref.'_BLOCK_PERSON') ,
	'description'		=> constant($constpref.'_BLOCK_PERSONDSC') ,
	'show_func'		=> 'b_d3dside_person_show' ,
	'edit_func'		=> 'b_d3dside_person_edit' ,
	'options'		=> "$mydirname|10|time||" ,
	'template'		=> '' , // use "module" template instead
	'can_clone'		=> true ,
) ;

$i++;
$modversion['blocks'][$i] = array(
	'file'			=> 'blocks.php' ,
	'name'			=> constant($constpref.'_BLOCK_CALENDAR') ,
	'description'		=> constant($constpref.'_BLOCK_CALENDARDSC') ,
	'show_func'		=> 'b_d3dside_calendar_show' ,
	'edit_func'		=> 'b_d3dside_calendar_edit' ,
	'options'		=> "$mydirname|10|time||0|" ,
	'template'		=> '' , // use "module" template instead
	'can_clone'		=> true ,
) ;

$i++;
$modversion['blocks'][$i] = array(
	'file'			=> 'blocks.php' ,
	'name'			=> constant($constpref.'_BLOCK_CATEGORY') ,
	'description'		=> constant($constpref.'_BLOCK_CATEGORYDSC') ,
	'show_func'		=> 'b_d3dside_category_show' ,
	'edit_func'		=> 'b_d3dside_category_edit' ,
	'options'		=> "$mydirname|10|time||0|" ,
	'template'		=> '' , // use "module" template instead
	'can_clone'		=> true ,
) ;

$i++;
$modversion['blocks'][$i] = array(
	'file'			=> 'blocks.php' ,
	'name'			=> constant($constpref.'_BLOCK_ENTRY') ,
	'description'		=> constant($constpref.'_BLOCK_ENTRYDSC') ,
	'show_func'		=> 'b_d3dside_entry_show' ,
	'edit_func'		=> 'b_d3dside_entry_edit' ,
	'options'		=> "$mydirname|10|time||0|" ,
	'template'		=> '' , // use "module" template instead
	'can_clone'		=> true ,
) ;

$i++;
$modversion['blocks'][$i] = array(
	'file'			=> 'blocks.php' ,
	'name'			=> constant($constpref.'_BLOCK_COMMENT') ,
	'description'		=> constant($constpref.'_BLOCK_COMMENTDSC') ,
	'show_func'		=> 'b_d3dside_comment_show' ,
	'edit_func'		=> 'b_d3dside_comment_edit' ,
	'options'		=> "$mydirname|10|time||0|" ,
	'template'		=> '' , // use "module" template instead
	'can_clone'		=> true ,
) ;

$i++;
$modversion['blocks'][$i] = array(
	'file'			=> 'blocks.php' ,
	'name'			=> constant($constpref.'_BLOCK_MLIST') ,
	'description'		=> constant($constpref.'_BLOCK_MLISTDSC') ,
	'show_func'		=> 'b_d3dside_mlist_show' ,
	'edit_func'		=> 'b_d3dside_mlist_edit' ,
	'options'		=> "$mydirname|10|time||0|" ,
	'template'		=> '' , // use "module" template instead
	'can_clone'		=> true ,
) ;

$i++;
$modversion['blocks'][$i] = array(
	'file'			=> 'blocks.php' ,
	'name'			=> constant($constpref.'_BLOCK_FRIENDS') ,
	'description'		=> constant($constpref.'_BLOCK_FRIENDSDSC') ,
	'show_func'		=> 'b_d3dside_friends_show' ,
	'edit_func'		=> 'b_d3dside_friends_edit' ,
	'options'		=> "$mydirname|10|time||" ,
	'template'		=> '' , // use "module" template instead
	'can_clone'		=> true ,
) ;

$i++;
$modversion['blocks'][$i] = array(
	'file'			=> 'blocks.php' ,
	'name'			=> constant($constpref.'_BLOCK_TAGCROUD') ,
	'description'		=> constant($constpref.'_BLOCK_TAGCROUDDSC') ,
	'show_func'		=> 'b_d3dside_tagcroud_show' ,
	'edit_func'		=> 'b_d3dside_tagcroud_edit' ,
	'options'		=> "$mydirname|10|time||0|" ,
	'template'		=> '' , // use "module" template instead
	'can_clone'		=> true ,
) ;

$i++;
$modversion['blocks'][$i] = array(
	'file'			=> 'blocks.php' ,
	'name'			=> constant($constpref.'_BLOCK_PHOTOS') ,
	'description'		=> constant($constpref.'_BLOCK_PHOTOSDSC') ,
	'show_func'		=> 'b_d3d_photolist_show' ,
	'edit_func'		=> 'b_d3d_photolist_edit' ,
	'options'		=> "$mydirname|10|time||0|||0|50|0|" ,
	'template'		=> '' , // use "module" template instead
	'can_clone'		=> true ,
) ;

// Comments
$modversion['hasComments'] = 1;
$modversion['comments']['pageName'] = 'detail.php';
$modversion['comments']['itemName'] = 'bid';

// Notification
$modversion['hasNotification'] = 1;
$modversion['notification']['lookup_file'] = 'notification.php';
$modversion['notification']['lookup_func'] = "{$mydirname}_notify_iteminfo";

$modversion['notification']['category'][1]['name'] = 'global';
$modversion['notification']['category'][1]['title'] = constant($constpref.'_GLOBAL_NOTIFY');
$modversion['notification']['category'][1]['description'] = constant($constpref.'_GLOBAL_NOTIFYDSC');
$modversion['notification']['category'][1]['subscribe_from'] = array('index.php','diarylist.php','detail.php');

$modversion['notification']['category'][2]['name'] = 'blogger';
$modversion['notification']['category'][2]['title'] = constant($constpref.'_BLOGGER_NOTIFY');
$modversion['notification']['category'][2]['description'] = constant($constpref.'_BLOGGER_NOTIFYDSC');
$modversion['notification']['category'][2]['subscribe_from'] = array('index.php','diarylist.php','detail.php');
$modversion['notification']['category'][2]['item_name'] = 'req_uid';
$modversion['notification']['category'][2]['allow_bookmark'] = 0;

$modversion['notification']['category'][3]['name'] = 'entry';
$modversion['notification']['category'][3]['title'] = constant($constpref.'_ENTRY_NOTIFY');
$modversion['notification']['category'][3]['description'] = constant($constpref.'_ENTRY_NOTIFYDSC');
$modversion['notification']['category'][3]['subscribe_from'] = array('index.php','detail.php');
$modversion['notification']['category'][3]['item_name'] = 'bid';
$modversion['notification']['category'][3]['allow_bookmark'] = 0;

$modversion['notification']['event'][1]['name'] = 'new_entry';
$modversion['notification']['event'][1]['category'] = 'global';
$modversion['notification']['event'][1]['title'] = constant($constpref.'_GLOBAL_NEWENTRY_NOTIFY');
$modversion['notification']['event'][1]['caption'] = constant($constpref.'_GLOBAL_NEWENTRY_NOTIFYCAP');
$modversion['notification']['event'][1]['description'] = constant($constpref.'_GLOBAL_NEWENTRY_NOTIFYDSC');
$modversion['notification']['event'][1]['mail_template'] = 'global_newentry_notify';
$modversion['notification']['event'][1]['mail_subject'] =constant($constpref.'_GLOBAL_NEWENTRY_NOTIFYSBJ');

$modversion['notification']['event'][2]['name'] = 'new_entry';
$modversion['notification']['event'][2]['category'] = 'blogger';
$modversion['notification']['event'][2]['title'] = constant($constpref.'_BLOGGER_NEWENTRY_NOTIFY');
$modversion['notification']['event'][2]['caption'] = constant($constpref.'_BLOGGER_NEWENTRY_NOTIFYCAP');
$modversion['notification']['event'][2]['description'] = constant($constpref.'_BLOGGER_NEWENTRY_NOTIFYDSC');
$modversion['notification']['event'][2]['mail_template'] = 'blogger_newentry_notify';
$modversion['notification']['event'][2]['mail_subject'] = constant($constpref.'_BLOGGER_NEWENTRY_NOTIFYSBJ');

$modversion['notification']['event'][3]['name'] = 'new_comment';
$modversion['notification']['event'][3]['category'] = 'blogger';
$modversion['notification']['event'][3]['title'] = constant($constpref.'_BLOGGER_COMMENT_NOTIFY');
$modversion['notification']['event'][3]['caption'] = constant($constpref.'_BLOGGER_COMMENT_NOTIFYCAP');
$modversion['notification']['event'][3]['description'] = constant($constpref.'_BLOGGER_COMMENT_NOTIFYDSC');
$modversion['notification']['event'][3]['mail_template'] = 'blogger_comment_notify';
$modversion['notification']['event'][3]['mail_subject'] = constant($constpref.'_BLOGGER_COMMENT_NOTIFYSBJ');
// submenu
$modversion['sub'][] = array('name' => constant($constpref.'_DIARYLIST'), 'url' => 'index.php?page=diarylist');

if (is_object(@$GLOBALS['xoopsUser'])) {
	// module ID
	$module_handler =& xoops_gethandler('module');
	$this_module =& $module_handler->getByDirname($mydirname);
	if(!empty($this_module)){
		$mid = $this_module->getVar('mid');

		// module config
		$config_handler =& xoops_gethandler("config");
		$moduleConfig = $config_handler->getConfigsByCat(0, $mid);
		$use_friend = intval( $moduleConfig['use_friend']);
		$friend_dirname = $moduleConfig['friend_dirname'];
		if (($use_friend ==1 || $use_friend ==2) && !empty($friend_dirname)){
			$modversion['sub'][] = array('name' => constant($constpref.'_FRIENDSDIARY'), 'url' => 'index.php?mode=friends');
		}
	}
}
$modversion['sub'][] = array('name' => constant($constpref.'_PHOTOLIST'), 'url' => 'index.php?page=photolist');
$modversion['sub'][] = array('name' => constant($constpref.'_COMMENT'), 'url' => 'index.php?page=viewcomment');
if (is_object(@$GLOBALS['xoopsUser'])) {
	$modversion['sub'][] = array('name' => constant($constpref.'_EDIT'), 'url' => 'index.php?page=edit');
	$modversion['sub'][] = array('name' => constant($constpref.'_CONFIG'), 'url' => 'index.php?page=usr_config');
	$modversion['sub'][] = array('name' => constant($constpref.'_CONFIG_CATEGORY'), 'url' => 'index.php?page=editcategory');
}

$modversion['onInstall'] = 'oninstall.php' ;
$modversion['onUpdate'] = 'onupdate.php' ;
$modversion['onUninstall'] = 'onuninstall.php' ;

// keep block's options
if( ! defined( 'XOOPS_CUBE_LEGACY' ) && substr( XOOPS_VERSION , 6 , 3 ) < 2.1 
        && ! empty( $_POST['fct'] ) && ! empty( $_POST['op'] ) 
        && $_POST['fct'] == 'modulesadmin' && $_POST['op'] == 'update_ok' 
        && $_POST['dirname'] == $modversion['dirname'] ) {
	include dirname(__FILE__).'/include/x20_keepblockoptions.inc.php' ;
}


?>
