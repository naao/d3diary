<?php

if( defined( 'FOR_XOOPS_LANG_CHECKER' ) ) $mydirname = 'd3diary' ;
$constpref = "_MB_" . strtoupper( $mydirname ) ;

if( defined( 'FOR_XOOPS_LANG_CHECKER' ) || ! defined( $constpref.'_LOADED' ) ) {

define( $constpref.'_LOADED' , 1 ) ;

// definitions for displaying blocks 
define($constpref."_DIARY","日記");
define($constpref."_NOTITLE","タイトルなし");
define($constpref."_EXIST_COMMENTS","コメントあり");
define($constpref."_NO_COMMENTS","コメントなし");
define($constpref."_NOCNAME","未分類");
define($constpref."_CATEGORY_EDIT","カテゴリーの編集");
define($constpref."_MORE","もっと見る");
define($constpref."_COMMENT_LIST","コメント一覧");
define($constpref."_DIARY_FRIENDSVIEW","友人日記一覧");

define($constpref."_YEAR","年");
define($constpref."_MONTH","月");
define($constpref."_DAY","日");
define($constpref."_W_SUN","日");
define($constpref."_W_MON","月");
define($constpref."_W_TUE","火");
define($constpref."_W_WED","水");
define($constpref."_W_THR","木");
define($constpref."_W_FRY","金");
define($constpref."_W_SAT","土");
define($constpref."_CALWEEK","日,月,火,水,木,金,土");
define($constpref."_M_JAN","1月");
define($constpref."_M_FEB","2月");
define($constpref."_M_MAR","3月");
define($constpref."_M_APR","4月");
define($constpref."_M_MAY","5月");
define($constpref."_M_JUN","6月");
define($constpref."_M_JUL","7月");
define($constpref."_M_AUG","8月");
define($constpref."_M_SEP","9月");
define($constpref."_M_OCT","10月");
define($constpref."_M_NOV","11月");
define($constpref."_M_DEC","12月");
define($constpref."_CTITLE","のカレンダー");
define($constpref."_BEFORE_MONTH","前の月");
define($constpref."_NEXT_MONTH","次の月");

define($constpref."_OTHER","外部ブロガー");

// definitions for displaying d3comment blocks 
define($constpref."_FORUM","フォーラム");
define($constpref."_TOPIC","トピック");
define($constpref."_REPLIES","返信");
define($constpref."_VIEWS","閲覧");
define($constpref."_VOTESCOUNT","投票");
define($constpref."_VOTESSUM","得票");
define($constpref."_LASTPOST","最終投稿");
define($constpref."_LASTUPDATED","最終更新");
define($constpref."_LINKTOSEARCH","フォーラム内検索へ");
define($constpref."_LINKTOLISTCATEGORIES","カテゴリー一覧へ");
define($constpref."_LINKTOLISTFORUMS","フォーラム一覧へ");
define($constpref."_LINKTOLISTTOPICS","トピック一覧へ");
define($constpref."_ALT_UNSOLVED","未解決トピック");
define($constpref."_ALT_MARKED","注目トピック");

define($constpref."_ORDERPOSTED","新着画像"); 
define($constpref."_ORDERRANDOM","ランダム画像"); 
define($constpref."_PERSON","さんの日記"); 

}

?>