<?php

if( ! defined( 'XOOPS_ROOT_PATH' ) ) exit ;

if( ! preg_match( '/^[0-9a-zA-Z_-]+$/' , $mydirname ) ) exit ;

if( ! class_exists( 'D3diaryPreloadBase' ) ) {

class D3diaryPreloadBase extends XCube_ActionFilter
{
	/*******
	   設定定数のオーバーライドを行う場合、
	   html 側に'/include/preload.inc.php'　を置くか
	   trust側に'/include/preload.inc.php'　を置くかして、
	   その中に以下の如く記載して設定の上書きが可能

	<?php
	   	$this->mobile_diarynum = 8 ;	// 携帯端末でのリスト表示件数
		$this->mobile_charmax = 40 ;	// 携帯端末での記事リスト要旨文字数
	?>

	********/

	var $mobile_diarynum = 5 ;	// 携帯端末でのリスト表示件数初期値
	var $mobile_charmax = 40 ;	// 携帯端末での記事リスト要旨文字数

	var $mydirname = 'd3diary' ;
	var $mod_config ;
	
	function __construct()
	{
		$root_setting_file = XOOPS_ROOT_PATH.'/modules/'.$this->mydirname.'/include/preload.inc.php';
		$trust_setting_file = dirname(__FILE__).'/include/preload.inc.php';
		if( file_exists($root_setting_file) ) {
			include_once $root_setting_file;
		} elseif ( file_exists($trust_setting_file) ) {
			include_once $trust_setting_file;
		}
	}

	function postFilter()
	{
		if( $this->isMobile() === true ) {

			include_once dirname(__FILE__).'/class/d3diaryConf.class.php';
			$d3dConf = & D3diaryConf::getInstance ( $this->mydirname, 0, "preload" ) ;
			$this->mod_config =& $d3dConf->mod_config ;

			// Overrides Module Configs
			$this->mod_config['block_diarynum'] = $this->mobile_diarynum;
			$this->mod_config['preview_charmax'] = $this->mobile_charmax;

		}
	}

	function isMobile()
	{
		if( class_exists( 'Wizin_User' ) ) {
			// WizMobile (gusagi)
			$user =& Wizin_User::getSingleton();
			return $user->bIsMobile ;
		} else if( defined( 'HYP_K_TAI_RENDER' ) && HYP_K_TAI_RENDER && HYP_K_TAI_RENDER != 2 ) {
			// hyp_common ktai-renderer (nao-pon)
			return true ;
		} else {
			return false ;
		}
	}

}

}

if( ! is_numeric( $mydirname{0} ) ) {
	// If you want to name the directory from 0-9, make a site preload.
	eval( 'class '.ucfirst( $mydirname ).'_D3diaryPreload extends D3diaryPreloadBase { var $mydirname = "'.$mydirname.'" ; }' ) ;
}

?>
