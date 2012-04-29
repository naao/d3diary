<?php

// D3DIARY IMPORT
define('_MD_D3DIARY_H2_IMPORTFROM','import');
define('_MD_D3DIARY_H3_IMPORTDATABASE','copy the data to d3diary');
define('_MD_D3DIARY_BTN_DOIMPORT','execute importing');
define('_MD_D3DIARY_LABEL_SELECTMODULE','select a module');
define('_MD_D3DIARY_CONFIRM_DOIMPORT','Do you really execute the inporting?');

define('_MD_D3DIARY_HELP_IMPORTFROM','You can import only from d3diary, minidiary, weblogD3 or d3blog. For weblogD3, trackback table will not be imported. For d3blog, trackback table will be also imported but no function in d3diary, and in both case category should be compressed upto 2 layers before execution of importing.<br /><font color="#FF0000">Be careful that the d3diary import is all overwritten.</font>');
define('_MD_D3DIARY_IMPORTDONE','The importing is finished.');
define('_MD_D3DIARY_ERR_INVALIDMID','Cannot execute importing from the module you selected.');
define('_MD_D3DIARY_SQLONIMPORT','Failed the importing.<br />Some possibilities are the differences of database table structure between the from-table with the to-table.<br />Please check both modules are already updated to the newest ones.');

define('_MD_D3DIARY_H3_IMPORTCOM','move the xoops comment data');
define('_MD_D3DIARY_HELP_COMIMPORT','importing comments moves the comments. <br />You cannot undo after the execution.');
define('_MD_D3DIARY_CONFIRM_DOCOMIMPORT','Do you realy move the comments?');

define('_MD_D3DIARY_H3_IMPORTNOTIF','move the xoops notification data');
define('_MD_D3DIARY_HELP_NOTIFIMPORT','importing notification moves the notifications. <br />You cannot undo after the execution.');
define('_MD_D3DIARY_CONFIRM_DONOTIFIMPORT','Do you realy move the notifications?');

// D3DIARY PERMISSION
define('_MD_D3DIARY_LANG_PERMISSION_MANAGER','Permission');
define('_MD_D3DIARY_LANG_CATEGORY_NAME','Category');
define('_MD_D3DIARY_LANG_CATEGORY_GLOBAL','General');
define('_MD_D3DIARY_LANG_GROUP_NAME','Group Name');
define('_MD_D3DIARY_PERMDESC_ALLOW_EDIT','Allow posting diary');
define('_MD_D3DIARY_PERMDESC_ALLOW_HTML','Allow HTML post');
define('_MD_D3DIARY_PERMDESC_ALLOW_REGDATE','Allow submit date specify');
define('_MD_D3DIARY_PERMDESC_ALLOW_GPERM','Allow specify group permission');
define('_MD_D3DIARY_PERMDESC_ALLOW_PPERM','Allow specify user permission');
define('_MD_D3DIARY_PERMDESC_ALLOW_MAILPOST','Allow import from email');
define('_MD_D3DIARY_MESSAGE_DBUPDATE_FAILED','Permission setting change has been failed');
define('_MD_D3DIARY_MESSAGE_DBUPDATE_SUCCESS','Permission setting has been changed');

// For D3pipes Options
define('_MD_D3DIARY_MAXTEXT','Max text length:'); 
define('_MD_D3DIARY_TOPICSPOSTS','Use block:'); 
define('_MD_D3DIARY_USEAGGRE','Use aggregation with other forums:'); 
define('_MD_D3DIARY_CATLIMIT','Category ID');
define('_MD_D3DIARY_FORUMLIMIT','Forum ID');
define('_MD_D3DIARY_DISPLAY_PERSONAL','Max Display for Person:');

?>
