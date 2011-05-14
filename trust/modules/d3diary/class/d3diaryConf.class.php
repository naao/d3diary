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
var $mid ;
var $mod_config ;
var $myts ;
var $uid = 0 ; 		// intval
var $uname = null ;
var $req_uid = 0 ; 	// intval
var $page ;
var $q_mode ;
var $q_cid ;
var $q_tag ;
var $q_year ;
var $q_month ;
var $q_day ;
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

function D3diaryConf($mydirname, $req_uid=0, $caller="")
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
	$this->mid = intval($this_module->getVar('mid'));
	$this->module_name = $this_module->getVar('name');

	// module config
	$config_handler =& xoops_gethandler("config");
	$this->mod_config = $config_handler->getConfigsByCat(0, $this->mid);

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

		$this->mPerm = & new $perm_class($this) ;
		$this->gPerm = & new D3dGperm($this) ;
		$this->func = & new D3diaryFunc($this) ;

		// needs req_uid
		$this->mPerm->ini_set();
		$this->gPerm->ini_set();
		$this->func->ini_set();

		// get personal config for req_uid
		$this->dcfg = & new DiaryConfig();
		$this->dcfg->uid = $this->req_uid;
		$this->dcfg->readdb($this->mydirname);

		// mail post class
		if ($this->caller == "mailpost" || $this->caller == "index") {
			require_once dirname(__FILE__).'/mailpost.class.php' ;
			$this->mPost = & new D3diaryMailPost($this);
			$this->mPost->ini_set();
		}

		$this->debug_appendtime('d3dConf_construct');
	//}

	$this->page = htmlspecialchars( $this->func->getpost_param('page'), ENT_QUOTES ) ;
	$this->q_mode = htmlspecialchars( $this->func->getpost_param('mode'), ENT_QUOTES ) ;
	$this->q_cid = (int)$this->func->getpost_param('cid') ;
	$this->q_tag = htmlspecialchars( $this->func->getpost_param('tag_name'), ENT_QUOTES ) ;
	$this->q_year = (int)$this->func->getpost_param('year') ;
	$this->q_month = (int)$this->func->getpost_param('month') ;
	$this->q_day = (int)$this->func->getpost_param('day') ;

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
		$instance[$mydirname] = & new D3diaryConf($mydirname, $req_uid, $caller) ;
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

function set_new_bids ( $_bids )
{
	$this->shared['new_bids'] = $_bids;
}

function get_new_bids ( & $_bids )
{
	$_bids = isset($this->shared['new_bids']) ? $this->shared['new_bids'] : array() ;
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