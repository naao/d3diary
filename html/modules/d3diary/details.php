<?php
// this is wrapper file for d3blog and weblogD3 alternative
require '../../mainfile.php' ;
if( ! defined( 'XOOPS_TRUST_PATH' ) ) die( 'set XOOPS_TRUST_PATH in mainfile.php' ) ;

$mydirname = basename( dirname( __FILE__ ) ) ;
$mydirpath = dirname( __FILE__ ) ;
$mydirurl = XOOPS_URL.'/modules/'.$mydirname;

require $mydirpath.'/mytrustdirname.php' ; // set $mytrustdirname

$_GET['page'] = 'detail';
$_GET['bid'] = isset($_GET['blog_id']) ? $_GET['blog_id'] : $_GET['bid'] ; // for weblogD3 wrapper

require XOOPS_TRUST_PATH.'/modules/'.$mytrustdirname.'/main.php' ;
?>
