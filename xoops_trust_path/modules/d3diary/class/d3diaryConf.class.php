<?php

if( ! class_exists( 'D3diaryConf' ) ) {

class D3diaryConf {

var $mydirname ;
var $db = null ;	// Database instance
var $mPerm = null ;	// (Global) Permission instance
var $gPerm = null ;	// Group and Member Permission instance
var $func = null ;	// function
var $dcfg  = null ;	// diary config instance
var $mPost  = null ;	// mail post instance
var $mid = 0 ;
var $mod_config ;
var $myts ;
var $uid = 0 ; 		// intval
var $uname = null ;
var $req_uid = 0 ; 	// intval
var $page = null ;
var $include_footer = true ;
var $params = array() ;	// photo upload dir or some parameters
var $q_mode = null ;
var $q_cid = 0 ;
var $q_tag = null ;
var $q_tag_noquote = null ;
var $q_year = 0 ;
var $q_month = 0 ;
var $q_day = 0 ;
var $q_odr = null ;
var $q_fr = 0 ;
var $urluppr = null ;
var $urlbase = null ;
var $urlbase_dlst = null ;
var $urlbase_exph = null ;
var $urlbase_exfr = null ;
var $url4_all = null ;
var $url4ex_cat = null ;
var $url4ex_tag = null ;
var $url4ex_date = null ;
var $url4ex_fr = null ;
var $url4ex_ph = null ;
var $url4ex_odr = null ;
var $style_s = array() ;

var $shared = array() ;	// like a shared memry .. should be private variables
var $caller ;
var $calledNo = 1 ;
var $initializedNo = 1 ;
var $is_main = false ;	// called from main==true or (block or else)==false
var $pbreak = "[pagebreak]" ;
var $enc_from = null ;
var $debug_mode ;
var $server_TZ ;
var $debug = array() ;
var $start_time ;

public function __construct($mydirname, $req_uid=0, $caller="")
{
	global $xoopsUser, $xoopsConfig ;
	
	$this->start_time = (int)(microtime(true) * 1000);

	$this->db =& Database::getInstance() ;
	$this->mydirname = $mydirname;
	$this->caller = $caller;
	if (is_object( @$xoopsUser )){
		$this->uid = intval($xoopsUser->getVar('uid'));
		$this->uname = $xoopsUser->getVar('uname');
		$this->name = $xoopsUser->getVar('name');
	} else { $this->uid = 0; $this->uname = ""; $this->name = ""; }

	$this->req_uid = (int)$req_uid > 0 ? (int)$req_uid : 
		((int)$this->getpost_param("req_uid") ? (int)$this->getpost_param("req_uid") : 0 )  ;
	//	((int)$this->getpost_param("req_uid") ? (int)$this->getpost_param("req_uid") : $this->uid )  ; // !! Must not do this

	//var_dump($this->mydirname); var_dump($this->req_uid); var_dump($this->uid); var_dump($caller); echo "<br />";

	// module ID
	$module_handler =& xoops_gethandler('module');
	$this_module =& $module_handler->getByDirname($this->mydirname);
	if (is_object($this_module)) {
		$this->mid = (int)$this_module->getVar('mid');
		$this->module_name = $this_module->getVar('name');
		// module config
		$config_handler =& xoops_gethandler("config");
		$this->mod_config = $config_handler->getConfigsByCat(0, $this->mid);
	}

	// is_main
	$constpref = "_MB_" . strtoupper( $this->mydirname ) ;
	if (defined("_MD_W_SUN")) {
		$this->is_main = true;
	} elseif (!defined($constpref."_W_SUN")) {
		$langmanpath = XOOPS_TRUST_PATH.'/libs/altsys/class/D3LanguageManager.class.php' ;
		if( ! file_exists( $langmanpath ) ) die( 'install the latest altsys' ) ;
		require_once( $langmanpath ) ;
		$langman =& D3LanguageManager::getInstance() ;
		$mytrustdirname =  basename( dirname( dirname( __FILE__ )) ) ;
		$langman->read( 'blocks_each.php' , $this->mydirname , $mytrustdirname , false ) ;
	}

	if( XOOPS_USE_MULTIBYTES == 1 ) {
		// mbstring emulator
		if ( ! extension_loaded( 'mbstring' ) && ! class_exists( 'HypMBString' ) ) {
			if (file_exists(XOOPS_TRUST_PATH . '/class/hyp_common/mbemulator/mb-emulator.php')) {
				require_once(XOOPS_TRUST_PATH . '/class/hyp_common/mbemulator/mb-emulator.php');
			}
		}
		// rss feed encoding from
		switch ($this->mod_config['enc_from']) {
			case 'xoops_charset':
				$this->enc_from = _CHARSET;
				break;
			case 'auto':
				$this->enc_from = "auto";
				break;
			case 'default':
			default:	// null
		}
	}

	$this->params['uploaddir_abs'] = XOOPS_ROOT_PATH.'/modules/'.$mydirname.'/upimg/';	// photo upload dir
	$this->params['previewdir'] = 'prev/';							// photo preview dir
	$this->params['cachedir'] = XOOPS_ROOT_PATH.'/modules/'.$mydirname.'/cache/';		// photo cache dir

	//if( $caller != "xoops_uname" ) { 
		$_year = $this->getpost_param('year');
		$_month = $this->getpost_param('month');
		$this->set_month ( $_year, $_month );

		// permission_class (read ef class and create the object)
		$perm_class = empty( $this->mod_config['permission_class'] ) ? 'd3diaryPermission' : 
			preg_replace( '/[^0-9a-zA-Z_]/' , '' , $this->mod_config['permission_class'] ) ;

		$this->myts =& MyTextSanitizer::getInstance();

		require_once dirname(__FILE__).'/'.$perm_class.'.class.php' ;
		require_once dirname(__FILE__).'/groupperm.class.php' ;
		require_once dirname(__FILE__).'/func.class.php' ;
		include_once dirname(__FILE__).'/diaryconfig.class.php';

		$this->mPerm = new $perm_class($this) ;
		$this->gPerm = new D3dGperm($this) ;
		$this->func = new D3diaryFunc($this) ;

		// needs req_uid
		$this->mPerm->ini_set();
		$this->gPerm->ini_set();
		$this->func->ini_set();

		// get personal config for req_uid
		$this->dcfg = new DiaryConfig();
		$this->dcfg->uid = $this->req_uid;
		$this->dcfg->readdb($this->mydirname);

		// mail post class
		if ($this->caller == "mailpost" || $this->caller == "index") {
			require_once dirname(__FILE__).'/mailpost.class.php' ;
			$this->mPost = new D3diaryMailPost($this);
			$this->mPost->ini_set();
		}

		$this->debug_appendtime('d3dConf_construct');
	//}

	$this->page =$this->func-> htmlspecialchars( $this->func->getpost_param('page') ) ;
	$this->q_mode = $this->func->htmlspecialchars( $this->func->getpost_param('mode') ) ;
	$this->q_cid = (int)$this->func->getpost_param('cid') ;
	$this->q_tag_noquote = rawurldecode( $this->func->getpost_param('tag_name') ) ;
	$this->q_tag = $this->func->htmlspecialchars( $this->q_tag_noquote ) ;
	$this->q_year = (int)$this->func->getpost_param('year') ;
	$this->q_month = (int)$this->func->getpost_param('month') ;
	$this->q_day = (int)$this->func->getpost_param('day') ;
	$this->q_odr = $this->func->htmlspecialchars( $this->func->getpost_param('odr') ) ;
	$this->q_fr = (int)$this->func->getpost_param('fr') ;
	$this->q_multidel = (int)$this->func->getpost_param('multidel') ;

	// create url for sort and common links
	$this->urluppr = XOOPS_URL.'/modules/'.$this->mydirname.'/index.php?' ;
	$this->urlbase_dlst = "page=diarylist" ;
	$this->urlbase_exph = "" ;
	if ( strcmp( $this->page, "photolist" ) == 0 ) {
		if ( $this->req_uid > 0 ) {
			$this->urlbase = "page=photolist&amp;req_uid=".$this->req_uid ;
			$this->urlbase_exph = "req_uid=".$this->req_uid ;
		} else {
			$this->urlbase = "page=photolist" ;
			$this->urlbase_exph = "page=diarylist" ;
		}
	} else {
		if ( $this->req_uid > 0 ) {
			$this->urlbase = "req_uid=".$this->req_uid ;
		} else {
			$this->urlbase = "page=diarylist" ;
		}
		if ( $this->q_multidel > 0 ) {
			$this->urlbase .= "&amp;multidel=1";
		}
	}
		$this->urlbase_exfr = $this->urlbase ;
	
	// exclude category
	if ( strcmp( $this->q_mode, "category" ) == 0) {
		$this->url4_all = "&amp;mode=category&amp;cid=".$this->q_cid ;
	}

	// exclude friend
		$this->url4ex_fr = $this->url4_all ;
	if ( $this->q_fr > 0 && $this->req_uid > 0 ) {
		$this->url4_all .= "&amp;fr=1" ;
		$this->url4ex_cat .= $_tmp_para ;
	}
	
	// exclude date
		$this->url4ex_date = $this->url4_all ;
	if ( $this->q_day > 0) {
		$_tmp_para = "&amp;year=". $this->q_year. "&amp;month=".$this->q_month. "&amp;day=". $this->q_day ;
		$this->url4_all .= $_tmp_para ;
		$this->url4ex_cat .= $_tmp_para ;
		$this->url4ex_fr .= $_tmp_para ;
	} elseif ( $this->q_month > 0) {
		$_tmp_para = "&amp;year=". $this->q_year. "&amp;month=".$this->q_month ;
		$this->url4_all .= $_tmp_para ;
		$this->url4ex_cat .= $_tmp_para ;
		$this->url4ex_fr .= $_tmp_para ;
	}
	// exclude tag
		$this->url4ex_tag =  $this->url4_all ;
	if ( !empty($this->q_tag) ) {
		$_tmp_para = "&amp;tag_name=". $this->q_tag ;
		$this->url4_all .= $_tmp_para ;
		$this->url4ex_date .= $_tmp_para ;
		$this->url4ex_cat .= $_tmp_para ;
		$this->url4ex_fr .= $_tmp_para ;
	}
	
	// exclude order
		$this->url4ex_odr = $this->url4_all ;
	if ( !empty($this->q_odr) ) {
		$_tmp_para = "&amp;odr=". $this->q_odr ;
		$this->url4_all .= $_tmp_para ;
		$this->url4ex_date .= $_tmp_para ;
		$this->url4ex_cat .= $_tmp_para ;
		$this->url4ex_fr .= $_tmp_para ;
		$this->url4ex_tag .= $_tmp_para ;
	}
		$this->url4ex_ph .= $this->url4_all ;
	
		$this->style_s['time_dsc'] = $this->style_s['title_dsc'] = $this->style_s['name_dsc'] = 
			$this->style_s['count_dsc'] = $this->style_s['hit_dsc'] = "d3dSortDsc" ;
		$this->style_s['time_asc'] = $this->style_s['title_asc'] = $this->style_s['name_asc'] = 
			$this->style_s['count_asc'] = $this->style_s['hit_asc'] = "d3dSortAsc" ;
	
	switch ( $this->q_odr ) {
		case "time_asc":
			$this->style_s['time_asc'] = "d3dSortAsc_s" ;
			break;
		case "title_dsc":
			$this->style_s['title_dsc'] = "d3dSortDsc_s" ;
			break;
		case "title_asc":
			$this->style_s['title_asc'] = "d3dSortAsc_s" ;
			break;
		case "name_dsc":
			$this->style_s['name_dsc'] = "d3dSortDsc_s" ;
			break;
		case "name_asc":
			$this->style_s['name_asc'] = "d3dSortAsc_s" ;
			break;
		case "count_dsc":
			$this->style_s['count_dsc'] = "d3dSortDsc_s" ;
			break;
		case "count_asc":
			$this->style_s['count_asc'] = "d3dSortAsc_s" ;
			break;
		case "hit_dsc":
			$this->style_s['hit_dsc'] = "d3dSortDsc_s" ;
			break;
		case "hit_asc":
			$this->style_s['hit_asc'] = "d3dSortAsc_s" ;
			break;
		case "time_dsc":
		default:
			$this->style_s['time_dsc'] = "d3dSortDsc_s" ;
			break;
	}

	$this->debug_mode = $xoopsConfig['debug_mode'] ;	// for debugging
	$this->server_TZ = (int)$xoopsConfig['server_TZ'];

}

function execute( $request )
{
	// abstract (must override it)
}

function & getInstance($mydirname, $req_uid=0, $caller="")
{
	static $instance ;
	if( ! isset( $instance[$mydirname] ) ) {
		$instance[$mydirname] = new D3diaryConf($mydirname, $req_uid, $caller) ;
	}
	return $instance[$mydirname] ;
}

function set_mod_config($req_uid=0, $caller="")
{	//must be set $this->mydirname before call it

}

function override_uid2_requid()
{
	$this->req_uid = $this->uid ;
	$this->mPerm->ini_set();
}

function set_month ( $_year, $_month )
{
	$this->shared['year'] = !empty($_year) ? (int)$_year : (int)date("Y") ;
	$this->shared['month'] = !empty($_month) ? (int)$_month : (int)date("n") ;

}

function get_month ( & $_year, & $_month )
{
	$_year = $this->shared['year'] ;
	$_month = $this->shared['month'] ;

}

function get_photodir ()
{
	return array( $this->params['uploaddir_abs'], $this->params['previewdir'] ) ;
}

function set_new_bids ( $_bids )
{
	$this->shared['new_bids'] = $_bids;
}

function get_new_bids ( & $_bids )
{
	$_bids = isset($this->shared['new_bids']) ? $this->shared['new_bids'] : array() ;
}

function set_new_entries ( $_entries )
{
	$this->shared['new_entries'] = $_entries;
}

function get_new_entries ( & $_entries )
{
	$_entries = isset($this->shared['new_entries']) ? $this->shared['new_entries'] : array() ;
}

function getpost_param( $pname )
{
	if(isset($_GET[$pname]))$pdat=$_GET[$pname];
	elseif(isset($_POST[$pname]))$pdat=$_POST[$pname];
	else $pdat="";
	
	return $pdat;
}

function debug_appendtime( $caller )
{
	$_time = (int)(microtime(true) * 1000) - $this->start_time;
	$this->debug['time'][] = array ( 'caller' => $caller, 'time' => $_time );
}

function debug_gettime() {
	return $this->debug['time'];
}

} //end class
}
?>