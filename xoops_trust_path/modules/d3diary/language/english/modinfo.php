<?php
if( defined( 'FOR_XOOPS_LANG_CHECKER' ) ) $mydirname = 'd3diary' ;
$constpref = '_MI_' . strtoupper( $mydirname ) ;

if( defined( 'FOR_XOOPS_LANG_CHECKER' ) || ! defined( $constpref.'_LOADED' ) ) {

define( $constpref.'_LOADED' , 1 ) ;

// Module Info

// The name of this module
define($constpref."_DIARY_NAME","D3diary");
define($constpref."_DIARY_DESC","D3diary");

define($constpref."_DIARYLIST","New Diaries");
define($constpref."_PHOTOLIST","New Images");
define($constpref."_FRIENDSDIARY","Friends Diaries");
define($constpref."_EDIT","Write Diary");
define($constpref."_CATEGORY","Category");
define($constpref."_COMMENT","Comment List");
define($constpref."_CONFIG","Diary Setting");
define($constpref."_CONFIG_CATEGORY","Category Setting");
define($constpref."_YES","YES");
define($constpref."_NO","NO");

// Admin
define($constpref.'_ADMENU_MYLANGADMIN','Language constant management');
define($constpref.'_ADMENU_MYTPLSADMIN','Template management');
define($constpref.'_ADMENU_MYBLOCKSADMIN','Block / Authority management');
define($constpref.'_ADMENU_IMPORT','Importing');
define($constpref.'_ADMENU_PERMISSION','Permission management');

// module config
define($constpref."_MENU_LAYOUT","Menu Layout");
define($constpref."_MENU_LAYOUTDESC","Manu layout for calendars etc.");
define($constpref."_MENU_LAYOUT_RIGHT","Show menu on right side");
define($constpref."_MENU_LAYOUT_LEFT","Show menu on left side");
define($constpref."_MENU_LAYOUT_NONE","Do not show side menu (use blocks)");

define($constpref."_RIGHT_WEIDTH","Width of side menu");
define($constpref."_RIGHT_WEIDTHDESC","Specify the width of side menu in pixcels.<br />Default value is 140pixcels");

define($constpref."_USENAME","display name");
define($constpref."_USENAMEDESC","which name to use display name 'uname' or 'name'. <br /> xoops default is 'uname'");
define($constpref."_USENAME_UNAME","use'uname'");
define($constpref."_USENAME_NAME","use'name'");

define($constpref."_BREADCRUMBS","Show breadcrumbs");
define($constpref."_BREADCRUMBSDESC","select YES to display breadcrumbs or <br/>NO to use xoops_breadcrumbs of xoops themes");

define($constpref."_PREV_CHARMAX","Maximum charactors for content list");
define($constpref."_PREV_CHARMAXDESC","Maximum charactors for blocks and content list.");

define($constpref."_BLK_DNUM","Maximum items for content list for a page");
define($constpref."_BLK_DNUMDESC","Setting of maximum items for a content list page.<br /> Items are devided for each pages.");

define($constpref."_PHOTO_MAXSIZE","Max. image data size (KB)");
define($constpref."_PHOTO_MAXSIZEDESC","Allowed maximum image data size , in (KB)");

define($constpref."_PHOTO_USERESIZE","Save in shrink image");
define($constpref."_PHOTO_USERESIZEDESC","Save in shrink image automatically, <br/>to less than 640pxcels.");
define($constpref."_PHOTO_USERESIZE_Y","Shrink Image");
define($constpref."_PHOTO_USERESIZE_N","NO Shrink");

define($constpref."_PHOTO_THUMBSIZE","Thumbnail image size");
define($constpref."_PHOTO_THUMBSIZEDESC","Set thumbnail image size in pixcels");
// define($constpref."_PHOTO_RESIZEMAX","Maximum shrink size");
// define($constpref."_PHOTO_RESIZEMAXDESC","Maximum shrink size setting by px (pixcel)");

define($constpref."_PHOTO_MAXPICS","Maximum Image numbers");
define($constpref."_PHOTO_MAXPICSDESC","Maximum allowed number setting for image uploading.");
define($constpref."_PHOTO_USEINFO","Put information for each picture");
define($constpref."_PHOTO_USEINFODESC","Select 'YES' to put information for each picture");

define($constpref."_USE_AVATAR","Show user avarar");
define($constpref."_USE_AVATARDESC","Select to show user avatar in sidebar and diarylist pages.");

define($constpref."_USE_OPEN_CAT","Authority setting for each category");
define($constpref."_USE_OPEN_CATDESC","Select Enable for using Authority setting and extenal blog setting for each category.");
define($constpref."_USE_OPEN_CAT_N","Authority and Extenal blog setting for each category:OFF");
define($constpref."_USE_OPEN_CAT_Y","Authority and Extenal blog setting for each category:ON");
define($constpref."_USE_OPEN_CAT_G","Authority for each category: upto Group setting & Extenal blog setting:ON");
define($constpref."_USE_OPEN_CAT_P","Authority for each category: upto Group and Member setting & Extenal blog setting:ON");

define($constpref."_USE_OPEN_ENTRY","Authority setting for each entry");
define($constpref."_USE_OPEN_ENTRYDESC","Select Enable for using Authority setting for each entry.");
define($constpref."_USE_OPEN_ENTRY_N","Authority setting for each entry:OFF");
define($constpref."_USE_OPEN_ENTRY_Y","Authority setting for each entry:ON");
define($constpref."_USE_OPEN_ENTRY_G","Authority setting for each entry:upto Group setting");
define($constpref."_USE_OPEN_ENTRY_P","Authority setting for each entry:upto Group and Member setting");

define($constpref."_GROUP_SHOW_ALL","Show all groups to the authority group selector");
define($constpref."_GROUP_SHOW_ALLDESC","For setting authority groups, select 'YES' to show all groups in the selector. Select 'NO' to hide groups except for author's own groups.<br />Both case are same for administrators to show all groups.");
define($constpref."_GROUP_EXCLD_SEL","Hidden groups");
define($constpref."_GROUP_EXCLD_SELDESC","In case selected 'YES' for 'show all groups to the selector, selected groups hore are not listed. <br />But author's own groups are everytime listed, and neither administrators nor general users selected is not valid, and for administrators all groups are listed.");

define($constpref."_USE_FRIEND","Cooperation setteing with Friend module");
define($constpref."_USE_FRIENDDESC","Enable Authority setting includes Friend Cooperation function or not. <br/><br/>Remain OFF if you have not installed XSNS nor Myfirend module.");
define($constpref."_USE_FRIEND_N","Cooperation with Friend module:OFF");
define($constpref."_USE_XSNS_Y","Cooperation with XSNS module:ON");
define($constpref."_USE_MYFRIENDS_Y","Cooperation with Myfriends module:ON");

define($constpref."_FRIEND_DIRNAME","Directory name of Friend Cooperation module");
define($constpref."_FRIEND_DIRNAMEDESC","If you use Cooperation function with Friend module, input the directory name.");

define($constpref."_EXCERPTOK","Enable to show title and summary for not permitted entry.");
define($constpref."_EXCERPTOKDESC","Select openarea to be enabled to show the title and summary<br /> for not permitted entry except the draft.");
define($constpref."_EXCERPTOK_NOUSE","Do not show for not permitted entry");
//define($constpref."_EXCERPTOK_BYPERSON","Transfer it to the setting of everybody");
define($constpref."_EXCERPTOK_FORMEMBER","Open for login members");
define($constpref."_EXCERPTOK_FORGUEST","Open for all guests");

define($constpref."_DISP_EXCERPTCOM","Show comment in case of enabled showing title and summary.");
define($constpref."_DISP_EXCERPTCOMDESC","Select 'YES' to show comments, 'NO' to hide them.");

define($constpref."_USE_TAG","TAG function ON/OFF");
define($constpref."_USE_TAGDESC","Using TAG function , select the area to show TAG-Cloud.");
define($constpref."_USE_TAG_N","TAG function :OFF");
define($constpref."_USE_TAG_INDEXONLY","Show TAG-Cloud on INDEX page");
define($constpref."_USE_TAG_ALSODIARYLIST","Show TAG-Cloud on everyone's INDEX page and mix list page.");
define($constpref."_USE_TAG_BLOCK","Do not show TAG-Cloud. (Use blocks)");

define($constpref."_BODY_EDITOR","Which body editor");
define($constpref."_BODY_EDITORDSC","simple doesn't display BBcode helper. Selecting 'xoopsdhml' is helpful to use BBcode.");
define($constpref."_BODY_HTMLEDITOR","Display HTML editor button");
define($constpref."_BODY_HTMLEDITORDSC","Allow HTML for specific groups on 'Permission Management' tab screen, then select 'common/FCKeditor' to show FCKeditor button.");
define($constpref.'_HTMLPR_EXCEPT','Groups can avoid purification by HTMLPurifier');
define($constpref.'_HTMLPR_EXCEPTDSC','Post from users who are not belonged these groups will be forced to purified as sanitized HTML by HTMLPurifier in Protector>=3.14. This purification cannot work with PHP4');
define($constpref."_GTICKET_SET_TIME","Timeout on sentry form (sec)");
define($constpref."_GTICKET_SET_TIMEDSC","Timeout setting after displayed entry form.<br />You can repost again after timeout error.");

define($constpref."_USE_UPDATEPING","Enable update ping" );
define($constpref."_USE_UPDATEPING_DSC","Select YES for using update ping" );
define($constpref."_UPDATEPING","Update ping servers URL" );
define($constpref."_UPDATEPING_DSC","devide each URL by break" );
define($constpref."_UPDATEPING_SERVERS","http://ping.rss.drecom.jp/\nhttp://blog.goo.ne.jp/XMLRPC" );
define($constpref."_ENC_FROM" , "Internal encoding translation for RSS Feed");
define($constpref."_ENC_FROMDSC" , "Normally 'default' is applicable, if RSS feed is garbleed, 'xoops_chrset' or 'auto' may be better.");
define($constpref.'_PERM_CLASS' , 'Class name for view permission');
define($constpref.'_PERM_CLASSDSC' , 'input Class name to overrides the view permission. Default is d3diaryPermission');

define($constpref."_USE_MAILPOST" , 'Use post by e-mail');
define($constpref."_USE_MAILPOSTDSC" , 'Select "YES" to enable post via e-mail, and check the group on permission setting.');
define($constpref."_POP3_SERVER","POP mail server");
define($constpref."_POP3_SERVER_DESC","POP mail server name");
define($constpref."_POP3_PORT","POP server port");
define($constpref."_POP3_PORT_DESC","Please contact to server administrator. Normally POP server uses port 110.");
define($constpref."_POP3_APOP","Use APOP encrypt authorization");
define($constpref."_POP3_APOP_DESC","Please contact to server administrator whether to use APOP encrypt authorization");
define($constpref."_POST_EMAIL_ADDRESS","Mail account ID for inporting");
define($constpref."_POST_EMAIL_ADDRESS_DESC","Input the POP3 account ID for inporting");
define($constpref."_POST_EMAIL_PASSWORD","POP3 password for inporting");
define($constpref."_POST_EMAIL_PASSWORD_DESC","Input the POP3 password for inporting");
define($constpref."_POST_EMAIL_FULLADD","Mail full address");
define($constpref."_POST_EMAIL_FULLADDDSC","Full address for post to which is not for control but only for show.");
define($constpref."_POST_DETECT_ORDER","Mail text encoding detect order");
define($constpref."_POST_DETECT_ORDERDSC","Set mail text encoding detect order.<br />Vacant means 'auto'. If some garble is occured, input text series from 'ISO-2022-JP, UTF-8, UTF-7, ASCII, EUC-JP, JIS, SJIS, eucJP-win, SJIS-win'<br />Ex: 'ISO-2022-JP, UTF-8, JIS, EUC-JP, eucJP-win, SJIS'");

define($constpref."_USE_SIMPLECOMMENT","Comment mode");
define($constpref."_USE_SIMPLECOMMENTDESC","You can use Easy Comment Form instead of XOOPS original comment form. <br/><br/> Guest cannot write using Easy Comment Form. ");
define($constpref."_USE_SIMPLECOMMENT_Y","Use Easy Comment Form");
define($constpref."_USE_SIMPLECOMMENT_N","Use XOOPS original comment form");

//d3comment integration
define($constpref."_COM_DIRNAME","Directory name for d3forum comment integration");
define($constpref."_COM_DIRNAMEDSC","When use D3-comment integration system. <br/>write your d3forum (html) directory <br/>If you do not use comments or use xoops comment system, leave this in empty.");
define($constpref."_COM_FORUMID","Forum number for d3forum comment integration");
define($constpref."_COM_FORUMIDDSC","When you set above integration diredtory, write forum_id");
define($constpref."_COM_ORDER","Order of comment integration");
define($constpref."_COM_ORDERDSC","When you set comment integration, select display order of comment posts");
define($constpref."_COM_VIEW","View of comment-integration");
define($constpref."_COM_VIEWDSC","select flat or thread");
define($constpref."_COM_POSTSNUM","Max posts displayed in comment integration");
define($constpref."_COM_ANCHOR","entry anchor of comment integration");
define($constpref."_COM_ANCHORDSC","Default entry anchor is 'post_path'.<br />If you use 'post_id' for multiple-topics for one entry , <br /> you have to edit d3forum template and here both.");
define($constpref."_USE_COM_ANCHOR_UNIQUEPATH","Use d3forum default 'post_path'");
define($constpref."_USE_COM_ANCHOR_POSTNUM","Use 'post_id'");

//notifications
define($constpref."_GLOBAL_NOTIFY","All personnel Diary");
define($constpref."_GLOBAL_NOTIFYDSC","All personnel Diary");
define($constpref."_BLOGGER_NOTIFY","Specified person's Diary");
define($constpref."_BLOGGER_NOTIFYDSC","Specified person's Diary");

define($constpref."_GLOBAL_NEWENTRY_NOTIFY","New Diary");
define($constpref."_GLOBAL_NEWENTRY_NOTIFYCAP","Notify All personnel new Diary");
define($constpref."_GLOBAL_NEWENTRY_NOTIFYDSC","Notify All personnel new Diary");
define($constpref."_GLOBAL_NEWENTRY_NOTIFYSBJ","[{X_SITENAME}] {X_MODULE} new Diary");

define($constpref."_BLOGGER_NEWENTRY_NOTIFY","Specified person's Diary [limited to d3comment]");
define($constpref."_BLOGGER_NEWENTRY_NOTIFYCAP","Notify this person's new Diary");
define($constpref."_BLOGGER_NEWENTRY_NOTIFYDSC","Notify this person's new Diary");
define($constpref."_BLOGGER_NEWENTRY_NOTIFYSBJ","[{X_SITENAME}] {X_MODULE} new Diary");

define($constpref."_BLOGGER_COMMENT_NOTIFY","Comment for Specified person");
define($constpref."_BLOGGER_COMMENT_NOTIFYCAP","Notify Comment for this person");
define($constpref."_BLOGGER_COMMENT_NOTIFYDSC","Notify Comment for this person");
define($constpref."_BLOGGER_COMMENT_NOTIFYSBJ","[{X_SITENAME}] {X_MODULE} new Comment");

define($constpref."_ENTRY_NOTIFY","Comment for this entry");
define($constpref."_ENTRY_NOTIFYDSC","Notify Comment for this entry");

// Block
define($constpref."_BLOCK_NEWENTRY","New Diary / Blog");
define($constpref."_BLOCK_NEWENTRYDSC","New Entries - New Diary");
define($constpref."_BLOCK_BLOGGER","New Blogger List");
define($constpref."_BLOCK_BLOGGERDSC","New Blogger List");
define($constpref."_BLOCK_D3COMPOSTS","New Comment entry List");
define($constpref."_BLOCK_D3COMPOSTSDSC","Valid for d3coment integration");
define($constpref."_BLOCK_D3COMTOPICS","New Comment Topics List");
define($constpref."_BLOCK_D3COMTOPICSDSC","Valid for d3coment integration");
define($constpref."_BLOCK_PERSON","Author");
define($constpref."_BLOCK_PERSONDSC","Diary author block");
define($constpref."_BLOCK_CALENDAR","Calendar");
define($constpref."_BLOCK_CALENDARDSC","Diary calendar block");
define($constpref."_BLOCK_CATEGORY","Category");
define($constpref."_BLOCK_CATEGORYDSC","Diary category block");
define($constpref."_BLOCK_ENTRY","New entries");
define($constpref."_BLOCK_ENTRYDSC","New entrys block");
define($constpref."_BLOCK_COMMENT","New comments");
define($constpref."_BLOCK_COMMENTDSC","new comments block");
define($constpref."_BLOCK_MLIST","Month");
define($constpref."_BLOCK_MLISTDSC","Monthly show block");
define($constpref."_BLOCK_FRIENDS","Friends");
define($constpref."_BLOCK_FRIENDSDSC","Friends list block");
define($constpref."_BLOCK_TAGCROUD","Tag croud");
define($constpref."_BLOCK_TAGCROUDDSC","Tag croud block");
define($constpref."_BLOCK_PHOTOS","Photos");
define($constpref."_BLOCK_PHOTOSDSC","Photos block");

//others
define($constpref."_BLOGGER","'s diary");

}
?>
