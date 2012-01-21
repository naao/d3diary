<?php
// image size conversion

$mytrustdirpath = dirname(dirname( __FILE__ )) ;
require_once $mytrustdirpath."/class/d3diaryConf.class.php";
$d3dConf = D3diaryConf::getInstance($mydirname, 0, "imgresize");

$cachedir = $d3dConf->params['cachedir'];

// check input
if(empty($_GET['fname']) or preg_match ("/\.\.\//",$_GET['fname'])){
	print_emptyimg();
	exit();
}
# http ~ remove domain
$fname = str_replace(XOOPS_URL."/", "", $_GET['fname']);
$dirlist=str_replace("/", "_", $fname);

if(!empty($_GET['maxsize']) and intval($_GET['maxsize'])>0 and !empty($fname)){
	$maxsize=intval($_GET['maxsize']);
	$fname=XOOPS_ROOT_PATH."/".$fname;
	$cachename=$cachedir."maxsize_".$maxsize."_".$dirlist;
}else{
	print_emptyimg();
	exit();
}

// desplay cache
if(is_file($cachename)){
	print_img($cachename);
	exit();
}

list($width, $height, $type, $attr) = getimagesize($fname);
// display small image as original size
if($maxsize>$width and $maxsize>$height){
	print_img($fname);
	exit();
}


// check file exists
if(!is_file($fname)){
	print_emptyimg();
	exit();
}

// resize ,save 
$ftype=strrchr($fname, "."); // extention
if(strcasecmp($ftype, ".png")==0){
	$src_id = imagecreatefrompng($fname);
}elseif(strcasecmp($ftype, ".jpg")==0 or strcasecmp($ftype, ".jpeg")==0){
	$src_id = imagecreatefromjpeg($fname);
}elseif(strcasecmp($ftype, ".gif")==0){
	$src_id = imagecreatefromgif($fname);
}

if($width >= $height){
	$th_width  = $maxsize;
	$th_height = $height * $th_width / $width;
} else {
	$th_height = $maxsize;
	$th_width  = $width * $th_height / $height;
}

$dst_id = imagecreatetruecolor($th_width, $th_height);
imagecopyresampled($dst_id, $src_id, 0, 0, 0, 0, $th_width, $th_height, $width, $height);
if(strcasecmp($ftype, ".png")==0){
	imagepng($dst_id, $cachename);
}elseif(strcasecmp($ftype, ".jpg")==0 or strcasecmp($ftype, ".jpeg")==0){
	imagejpeg($dst_id, $cachename);
}elseif(strcasecmp($ftype, ".gif")==0){
	imagegif($dst_id, $cachename);
}

imagedestroy($src_id);
imagedestroy($dst_id);

print_img($cachename);
exit();


function print_emptyimg(){
	
	header("Content-type: image/gif\n\n");
	//readfile(XOOPS_ROOT_PATH."/modules/minidiary/images/blank.gif");
	readfile(XOOPS_ROOT_PATH."/modules/".$mydirname."/images/blank.gif");
}


// imgname is already checked
function print_img($imgname){
	$ftype=strrchr($imgname, "."); // extention
	$type="Content-type: image/";
	if(strcasecmp($ftype, ".png")==0){
		$type.="png\n\n";
	}elseif(strcasecmp($ftype, ".jpg")==0 or strcasecmp($ftype, ".jpeg")==0){
		$type.="jpg\n\n";
	}elseif(strcasecmp($ftype, ".gif")==0){
		$type.="gif\n\n";
	}else{
		print_emptyimg();
		exit();
	}
	header($type);
	readfile($imgname);
}

?>
