<?php

$xoopsOption['nocommon'] = true;
require '../../mainfile.php';
if(!defined('XOOPS_TRUST_PATH')){
	die('set XOOPS_TRUST_PATH into mainfile.php');
}

$mydirpath = dirname(__FILE__);
$mydirname = basename($mydirpath);

require $mydirpath.'/mytrustdirname.php';
require XOOPS_TRUST_PATH.'/modules/'.$mytrustdirname.'/'.basename(__FILE__);

?>