<?php
require '../../../mainfile.php' ;
if( ! defined( 'XOOPS_TRUST_PATH' ) ) die( 'set XOOPS_TRUST_PATH in mainfile.php' ) ;

$mydirpath = dirname(dirname( __FILE__ )) ;
$mydirname = basename($mydirpath ) ;
//$mydirurl = XOOPS_URL.'/modules/'.$mydirname;

require $mydirpath.'/mytrustdirname.php' ; // set $mytrustdirname

require XOOPS_TRUST_PATH.'/modules/'.$mytrustdirname.'/include/imgresize.php' ;
?>
