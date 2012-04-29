<?php

if( defined( 'FOR_XOOPS_LANG_CHECKER' ) ) $mydirname = 'd3diary' ;
$constpref = "_MB_" . strtoupper( $mydirname ) ;

if( defined( 'FOR_XOOPS_LANG_CHECKER' ) || ! defined( $constpref.'_LOADED' ) ) {

define( $constpref.'_LOADED' , 1 ) ;

// definitions for displaying blocks
define($constpref."_DIARY","Diário");
define($constpref."_NOTITLE","Não existe título");
define($constpref."_EXIST_COMMENTS","Comentários existentes");
define($constpref."_NO_COMMENTS","Não existe comentário");
define($constpref."_NOCNAME","Não existe categoria");
define($constpref."_CATEGORY_EDIT","Editar categoria");
define($constpref."_MORE","Ver mais...");
define($constpref."_COMMENT_LIST","Comentários");
define($constpref."_DIARY_FRIENDSVIEW","Amigos do Diário");

define($constpref."_YEAR","/");
define($constpref."_MONTH","/");
define($constpref."_DAY"," ");
define($constpref."_W_SUN","Dom");
define($constpref."_W_MON","Seg");
define($constpref."_W_TUE","Ter");
define($constpref."_W_WED","Qua");
define($constpref."_W_THR","Qui");
define($constpref."_W_FRY","Sex");
define($constpref."_W_SAT","Sab");
define($constpref."_CALWEEK","Do,Se,Te,Qu,Qu,Se,Sa");
define($constpref."_M_JAN","Jan");
define($constpref."_M_FEB","Fev");
define($constpref."_M_MAR","Mar");
define($constpref."_M_APR","Abr");
define($constpref."_M_MAY","Mai");
define($constpref."_M_JUN","Jun");
define($constpref."_M_JUL","Jul");
define($constpref."_M_AUG","Ago");
define($constpref."_M_SEP","Set");
define($constpref."_M_OCT","Out");
define($constpref."_M_NOV","Nov");
define($constpref."_M_DEC","Dez");
define($constpref."_CTITLE"," Calendário");
define($constpref."_BEFORE_MONTH","Próximo");
define($constpref."_NEXT_MONTH","Anterior");

define($constpref."_OTHER","Blogar em outro site");
define($constpref."_NEWDIARY","Lista de diário");
define($constpref."_NEWPHOTO","Lista de imagem");

// definitions for displaying d3comment blocks

define($constpref."_FORUM","Fórum");
define($constpref."_TOPIC","Tópico");
define($constpref."_REPLIES","Respostas");
define($constpref."_VIEWS","Vizualizações");
define($constpref."_VOTESCOUNT","Votos");
define($constpref."_VOTESSUM","Pontuações");
define($constpref."_LASTPOST","Últimos posts");
define($constpref."_LASTUPDATED","Última atualização");
define($constpref."_LINKTOSEARCH","Busca no fórum");
define($constpref."_LINKTOLISTCATEGORIES","Index da categoria");
define($constpref."_LINKTOLISTFORUMS","Index do fórum");
define($constpref."_LINKTOLISTTOPICS","Index do tópico");
define($constpref."_ALT_UNSOLVED","Tópico não resolvido");
define($constpref."_ALT_MARKED","Tópico marcado");

define($constpref."_B_ORDERPOSTED","Imagens novas"); 
define($constpref."_B_ORDERRANDOM","Imagens de Randum"); 
define($constpref."_PERSON","Autor do diio"); 

}

?>
