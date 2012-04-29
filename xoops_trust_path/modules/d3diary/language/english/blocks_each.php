<?php

if( defined( 'FOR_XOOPS_LANG_CHECKER' ) ) $mydirname = 'd3diary' ;
$constpref = "_MB_" . strtoupper( $mydirname ) ;

if( defined( 'FOR_XOOPS_LANG_CHECKER' ) || ! defined( $constpref.'_LOADED' ) ) {

define( $constpref.'_LOADED' , 1 ) ;

// definitions for displaying blocks 
define($constpref."_DIARY","Diary");
define($constpref."_NOTITLE","No Title");
define($constpref."_EXIST_COMMENTS","Comments exist");
define($constpref."_NO_COMMENTS","No comment");
define($constpref."_NOCNAME","No category");
define($constpref."_CATEGORY_EDIT","Edit category");
define($constpref."_MORE","See More");
define($constpref."_COMMENT_LIST","Comments");
define($constpref."_DIARY_FRIENDSVIEW","Friends Diary");

define($constpref."_YEAR","/");
define($constpref."_MONTH","/");
define($constpref."_DAY"," ");
define($constpref."_W_SUN","Sun");
define($constpref."_W_MON","Mon");
define($constpref."_W_TUE","Tue");
define($constpref."_W_WED","Wed");
define($constpref."_W_THR","Thr");
define($constpref."_W_FRY","Fri");
define($constpref."_W_SAT","Sat");
define($constpref."_CALWEEK","Su,Mo,Tu,We,Th,Fr,Sa");
define($constpref."_M_JAN","Jan");
define($constpref."_M_FEB","Feb");
define($constpref."_M_MAR","Mar");
define($constpref."_M_APR","Apr");
define($constpref."_M_MAY","May");
define($constpref."_M_JUN","Jun");
define($constpref."_M_JUL","Jul");
define($constpref."_M_AUG","Aug");
define($constpref."_M_SEP","Sep");
define($constpref."_M_OCT","Oct");
define($constpref."_M_NOV","Nov");
define($constpref."_M_DEC","Dec");
define($constpref."_CTITLE"," Calendar");
define($constpref."_BEFORE_MONTH","Prev");
define($constpref."_NEXT_MONTH","Next");

define($constpref."_OTHER","Blogger on other site");
define($constpref."_NEWDIARY","Diary List");
define($constpref."_NEWPHOTO","Image List");

// definitions for displaying d3comment blocks 
define($constpref."_FORUM","Forum");
define($constpref."_TOPIC","Topic");
define($constpref."_REPLIES","Replies");
define($constpref."_VIEWS","Views");
define($constpref."_VOTESCOUNT","Votes");
define($constpref."_VOTESSUM","Scores");
define($constpref."_LASTPOST","Last Post");
define($constpref."_LASTUPDATED","Last Updated");
define($constpref."_LINKTOSEARCH","Search in the forum");
define($constpref."_LINKTOLISTCATEGORIES","Category Index");
define($constpref."_LINKTOLISTFORUMS","Forum Index");
define($constpref."_LINKTOLISTTOPICS","Topic Index");
define($constpref."_ALT_UNSOLVED","Unsolved topic");
define($constpref."_ALT_MARKED","Marked topic");

define($constpref."_B_ORDERPOSTED","New Images"); 
define($constpref."_B_ORDERRANDOM","Randum Images"); 
define($constpref."_PERSON","'s diary"); 

}

?>