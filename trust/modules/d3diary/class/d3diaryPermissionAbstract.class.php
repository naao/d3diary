<?php

// ! this class must be called from d3diaryConf class only !
class d3diaryPermissionAbstract {

var $mydirname ;
var $db = null ;	// Database instance
var $mid ;
var $mod_config ;
var $uid = 0 ; 		// intval
var $mygids = Array();
var $my_friends = Array();
var $my_friends2 = Array();
var $req_uid = 0 ; 	// intval
var $req_friends = Array();
var $req_friends2 = Array();
var $is_friend = false;
var $is_friend2 = false;
var $diary = null ;	// diary instance
var $dcfg  = null ;	// diary config instance
var $d3dConf = null ;	// d3dConf
var $gPerm  = null ;	// group permission instance
var $func  = null ;	// group permission instance
var $isadmin = false ;	// boolean
var $isauthor = false ;	// boolean
var $d3d_conf = array();
var $initialized = 0;

function d3diaryPermissionAbstract(& $d3dConf)
{
	if (!defined('XOOPS_ROOT_PATH')) {
	    exit();
	}

	$this->d3dConf = & $d3dConf;
	
	//echo("d3diaryPermission initializad "); var_dump($this->mydirname);
	
 }

function execute( $request )
{
	// abstract (must override it)
}

function &getInstance()
{
	// abstract (must override it)
}

function ini_set()
{	//must be set $this->mydirname, $req_uid before call it

	// copying parent's parameters
	$this->mydirname = $this->d3dConf->mydirname;
	$this->mid = $this->d3dConf->mid;

	$this->uid = $this->d3dConf->uid;
	$this->mod_config = & $this->d3dConf->mod_config;
	$this->dcfg = & $this->d3dConf->dcfg;
	$this->req_uid = $this->d3dConf->req_uid;

	$this->gPerm = & $this->d3dConf->gPerm;
	$this->func = & $this->d3dConf->func;

	$this->mygids = $this->get_mygids();

	//var_dump($this->initialized); 	$this->initialized++;
 	$this->isadmin = $this->check_isadmin();
	$this->isauthor =  ($this->uid > 0 && $this->req_uid === $this->uid) ? true : false ;

	$use_friend = intval($this->mod_config['use_friend']);
	// xsns: ==1 myfriends: ==2
	if (!empty($this->mod_config['friend_dirname']) && ( $use_friend ==1 || $use_friend ==2 )){
		$this->d3d_conf['use_friend']=true;
		if ( $this->uid > 0 ) {
			list($this->my_friends, $this->my_friends2) = $this->get_friends2($this->uid);
			//var_dump($this->my_friends); var_dump($this->my_friends2);
			if( $this->req_uid > 0 ) {
				$this->is_friend = $this->check_is_friend($this->req_uid);
				$this->is_friend2 = $this->check_is_friend2($this->req_uid);
			}
		}
		if( $this->req_uid > 0 ) {
			list($this->req_friends, $this->req_friends2) = $this->get_friends2($this->req_uid);
		}
	} else {
		$this->d3d_conf['use_friend']=false;
	}
	//var_dump($this->mydirname); 
   //}
}

function get_mygids()
{
	global $xoopsUser;

	$mygids = is_object($xoopsUser) ? array_map("intval", array_unique($xoopsUser->getGroups())) : array(3);
	
	return $mygids;
}

function check_isadmin()
{	//must be set $this->uid, $this->mid
	// check module's administration authorization
	$moduleperm_handler =& xoops_gethandler( 'groupperm' ) ;

	if( $this->uid > 0 && $moduleperm_handler->checkRight('module_admin', $this->mid, 
		$this->mygids)) { return true; } else { return false; }
}

function check_exist_user($req_uid)
{
	$db = & $this->d3dConf->db;
	
	$sql = "SELECT count(*) AS count_num FROM ".$db->prefix("users")." 
			WHERE (uid = '$req_uid') ";
	
	if ( !$result = $db->query($sql) ) { return false; }
	if ( !$row = $db->fetchArray($result) ) {	return false; }
	if ( $row['count_num'] <= 0 ) {	return false; }

  	return true;
}

function check_editperm($bid, $uid)
{
	$db = & $this->d3dConf->db;

	$sql = "SELECT count(*) AS count_num FROM ".$db->prefix($this->mydirname."_diary")." 
		WHERE (uid = '$uid') and (bid='$bid')";
		
	if ( !$result = $db->query($sql) ) {
		return false;
	}
	if ( !$row = $db->fetchArray($result) ) {
		return false;
	}
	if ( $row['count_num'] <= 0 ) {
		if ( !$this->isadmin ) {
			return false;
		}
	}
  	return true;
}

function get_allowed_openarea()
{	// call from a specified personnel page only ( do not from diarylist, b_diarylist)
	// return values are:
	// 0=open, 1=members, 2=friends, 3=friends^2, 10=group, 20=personel, 100=scratch

	$allowed_b = array();		// access uid's requiry permission
	
	// a return value
	if( $this->uid <= 0 ) {			$allowed_b = array(0);		}
	elseif ( $this->isauthor == true){	$allowed_b = array(0,1,3,2,100);	}
	elseif ( $this->isadmin == true ){	$allowed_b = array(0,1,3,2,100);	}
	elseif ( $this->d3d_conf['use_friend'] != true ) { $allowed_b = array(0,1);}
	else {	//use friends
		if( $this->is_friend == true){	$allowed_b = array(0,1,3,2);	}
		elseif( $this->is_friend2 != true){ 	$allowed_b = array(0,1);	}
		else {					$allowed_b = array(0,1,3);}
	}
	
	return $allowed_b;
}

function check_permission($chk_uid, $op, $op_ent=0, $op_cat=0, $create_time = 0)
{	// not exact check but only for index page access

	$chk_uid = (int)$chk_uid;
	$op = (int)$op;	
	list($op, $tmp) = $this->override_openarea( $op, $op_ent, $op_cat );

	$is_friend = $this->check_is_friend($chk_uid);
	$is_friend2 = $this->check_is_friend2($chk_uid);
	
	$_permit = $this->can_display($chk_uid, $op, $create_time, $is_friend , 
		$is_friend2);
		
	return $_permit;

}


// naao added each entry list's permission check
function can_display($chk_uid, $op, $create_time = 0, $is_friend=false , 
		$is_friend2=false, $gperms=array(), $pperms=array())
{
	$chk_uid = (int)$chk_uid;
	$op = (int)$op;
	$now = date("Y-m-d H:i:s");
	
	if($this->isadmin){return true;}
	elseif($this->uid > 0 && $chk_uid==$this->uid){return true;}
	elseif($now < $create_time){return false;}
	elseif($op == 0){return true;}
	elseif($op == 1){
		if($this->uid==0){return false;}else{return true;}
	}
	elseif ($this->d3d_conf['use_friend']==true){
		if($op == 2 && $is_friend) {return true;}
		if($op == 3 && $is_friend2) {return true;}
	}
	
	if ($op == 10 || $op == 20){
		//return true;	// temporally. to be fixed
		if (array_intersect($this->mygids, $gperms)) { return true; }
		if($op == 20){
			//return true;	// temporally. to be fixed
			if (in_array($this->uid, $pperms)) { return true; }
		}
	}
	return false;
}

function can_disp()
{
	// abstract (must override it)
	return true;
}

// friend
function check_is_friend( $uid_to )
{
	if(!empty($this->my_friends)) {
	  	if ( in_array( $uid_to, $this->my_friends )) { return true;}
	  	else { return false; }
	} else {
		return false;
	}
}

// friend's friend
function check_is_friend2( $uid_to )
{
	if(!empty($this->my_friends2)) {
  		if ( in_array( $uid_to, $this->my_friends2 )) { return true;}
  		else { return false;}
	} else {
		return false;
	}
}

// get friends
function get_friends( $uid_to )
{	//must be set $this->mydirname, $this->uid, $this->mid,
	//$this->mod_config[], $this->d3d_conf[]
	
	$db = & $this->d3dConf->db;

	$use_friend = intval( $this->mod_config['use_friend']);
	$friend_dirname = $this->mod_config['friend_dirname'];

	if (!empty($friend_dirname) && $use_friend ==1 ){
		// XSNS
		$sql = "SELECT uid_from FROM ".$db->prefix($friend_dirname."_c_friend").
			" WHERE uid_to='".$uid_to."'";
			
	} elseif (!empty($friend_dirname) && $use_friend ==2 ){
		// MYFRIENDS
		$sql = "SELECT uid as uid_from FROM ".$db->prefix($friend_dirname."_friendlist").
			" WHERE friend_uid ='".$uid_to."'";
	}
	

	!$result = $db->query($sql);

	$my_friends = array();
	while($dbdat = $db->fetchArray($result)){
		$my_friends[] = intval($dbdat['uid_from']);
	}
	
	return $my_friends;
}

// get friend's friends
function get_friends2( $uid_to )
{	//must be set $this->mydirname, $this->uid, $this->mid,
	//$this->mod_config[], $this->d3d_conf[]

	$db = & $this->d3dConf->db;

	$use_friend = intval( $this->mod_config['use_friend']);
	$friend_dirname = $this->mod_config['friend_dirname'];

	$friends2 = array(); $friends3 = array();
	$friends = $this->get_friends( $uid_to );
	
	if (count($friends) >0) {
		if (!empty($friend_dirname) && $use_friend ==1 ){
		// XSNS
			$sql = "SELECT uid_from FROM ".$db->prefix($friend_dirname."_c_friend").
				" WHERE uid_to in (".implode(",", $friends).")";
			
		} elseif (!empty($friend_dirname) && $use_friend ==2 ){
			// MYFRIENDS
			$sql = "SELECT uid as uid_from FROM ".$db->prefix($friend_dirname."_friendlist").
				" WHERE friend_uid in (".implode(",", $friends).")";
		}

		!$result = $db->query($sql);
		while($dbdat = $db->fetchArray($result)){
			$friends2[] = intval($dbdat['uid_from']);
		}
		$friends3 = array_unique( array_merge( $friends, $friends2 ));
	}
	$my_friends = array($friends,$friends3);
	return $my_friends;
}

// added for edit.php Trigger Notification 2009/06/10
function get_users_can_read_entry( & $openarea, $op_ent, $op_cat=0, $gp_ent="", $pp_ent="", $gp_cat="", $pp_cat="" )
{

	$_tmp_op = intval($this->d3dConf->dcfg->openarea);

	list( $_got_op , $_slctd_op , $_tmp_gperms, $_tmp_pperms ) 
		= $this->d3dConf->mPerm->override_openarea( $_tmp_op, $op_ent, $op_cat, $gp_ent, $pp_ent, $gp_cat, $pp_cat );
	$openarea = $_got_op;


    if ($openarea != 100){
	
	switch ($openarea) {
	case 2:
		$got_users2notify = $this->req_friends ;
		if ( count($got_users2notify) > 0 ) {
			$users2notify = $got_users2notify ;
		} else {
			$users2notify = array( 0 ); // default_rev : everyone denied
		}
		break;
	case 3:
		$got_users2notify = $this->req_friends2 ;
		if ( count($got_users2notify) > 0 ) {
			$users2notify = $got_users2notify ;
		} else {
			$users2notify = array( 0 ); // default_rev : everyone denied
		}
			break;
	case 10:
		$users2notify = $this->gPerm->getUidsByGids( $_tmp_gperms ) ;
		break;
	case 20:
		$_tmp_gusers = $this->gPerm->getUidsByGids( $_tmp_gperms ) ;
		$users2notify = array_unique( array_merge( $_tmp_gusers, $_tmp_pperms ) ) ;
		break;
	default:
		$users2notify = array(); // default : everyone allowed
	}
    } else {
		$users2notify = array( 0 ); // default_rev : everyone denied
    }
	return $users2notify;
}

function override_openarea( $op, $op_ent, $op_cat, $gp_ent="", $pp_ent="", $gp_cat="", $pp_cat="" ){
	// strong order : 0 < 1 < 3 < 2 < 10 < 20 < 100
	// in case of same numbers, $op_ent is storonger than $op_cat
	// return values are:
	// $_rtn['0'] : 0=open, 1=members, 2=friends, 3=friends^2, 10=group, 20=personel, 100=scratch
	// $_rtn['1'] : selected openarea 0=$op, 1=$op_ent, 2=$op_cat
	// $_rtn['2'] : array permitted groups
	// $_rtn['3'] : array permitted users

	$op = (int)$op; $op_ent = (int)$op_ent; $op_cat = (int)$op_cat;

	$_rtn = array( $op, 0, "", "" );

    if($op < $op_ent || $op < $op_cat){
	if ( $op <= 1 ) {
		switch ($op_ent) {
		case 2:		// upto friends : stronger than 3 but weeker than 10 over
			if ($op_cat >= 10) { $_rtn[1] = 2; }
			else { $_rtn[1] = 1; }
			break;
		case 3:		// upto friends' friends : weeker than 2
			if ($op_cat ==2 || $op_cat >= 10) { $_rtn[1] = 2; }
			else { $_rtn[1] = 1; }
			break;
		default:
			if ($op_cat >= $op_ent ) { $_rtn[1] = 2; }
			else { $_rtn[1] = 1; }
			break;
		}
	} elseif ( $op == 2 ) {
		switch ($op_ent) {
		case 3:
			if ($op_cat >= 10) { $_rtn[1] = 2; }
			break;
		default:
			if ($op_cat >= $op_ent ) { $_rtn[1] = 2; }
			else { $_rtn[1] = 1; }
			break;
		}
	} elseif ( $op == 3 ) {
			if ($op_cat >= $op_ent ) { $_rtn[1] = 2; }
			else { $_rtn[1] = 1; }
	}

    	switch ($_rtn[1]) {
    	case 1:
    		$_rtn[0] = $op_ent; $_tmp_gperms = $gp_ent; $_tmp_pperms = $pp_ent;
    		break ;
    	case 2:
    		$_rtn[0] = $op_cat; $_tmp_gperms = $gp_cat; $_tmp_pperms = $pp_cat;
    		break ;
    	default:
    	}
	$_rtn[2] = isset($_tmp_gperms) ? 
			array_map("intval", explode('|', trim($_tmp_gperms,'|'))) : array();
	$_rtn[3] = isset($_tmp_pperms) ? 
			array_map("intval", explode('|', trim($_tmp_pperms,'|'))) : array();
    }
    
	//var_dump($op);var_dump($op_ent);var_dump($op_cat);var_dump($_rtn); echo "<br />";
	return $_rtn ;
}

function get_open_query( $caller, $params ) {

	if ($this->isadmin==true) { return " 1 "; }

	// base stage
	switch ($caller) {
		case "right_cat2":		// without d.openarea, cfg.openarea
		case "index1_other":		// without d.openarea, cfg.openarea
			$allowed_openarea = implode(",",$this->get_allowed_openarea());
			$whr_openarea = " (c.openarea IN (".$allowed_openarea.") OR c.openarea IS NULL) " ;
			break;
			
		case "right_cal_other":		// without d.openarea
		case "right_blist_other":	// without d.openarea
			$allowed_openarea = implode(",",$this->get_allowed_openarea());
			$whr_openarea = " ((c.openarea IN (".$allowed_openarea.") OR c.openarea IS NULL) 
					AND (cfg.openarea IN (".$allowed_openarea.") OR cfg.openarea IS NULL)) " ;
			break;
			
		case "index1":
		case "detail1":		// for req_uid > 0 pages
			$allowed_openarea = implode(",",$this->get_allowed_openarea());
			$whr_openarea = " ((d.openarea IN (".$allowed_openarea.")) 
					AND (c.openarea IN (".$allowed_openarea.") OR c.openarea IS NULL)) " ;
			break;

		case "dlist1":
		case "right_blist":
		case "b_com_topics":	// for req_uid == 0 pages with cfg.openarea
		default:
			$allowed_openarea = implode(",",$this->get_allowed_openarea());
			$whr_openarea = " ((d.openarea IN (".$allowed_openarea.")) 
					AND (cfg.openarea IN (".$allowed_openarea.") OR cfg.openarea IS NULL) 
					AND (c.openarea IN (".$allowed_openarea.") OR c.openarea IS NULL)) " ;
	}

	// no additional clause for guest access
	if ( $this->uid == 0 ) { return $whr_openarea ; }

	// friends perm stage
	if ($this->d3d_conf['use_friend'] === true) {
		switch ($caller) {
			case "right_cat2":
				break ;
			case "right_cal_other":
			case "right_blist_other":
			case "index1_other":		// without d.openarea, cfg.openarea
				$this->append_friend_query4cat( $caller, $whr_openarea ); //by ref
				break ;
			
			default:
				$this->append_friend_query( $caller, $whr_openarea ); //by ref
		}
	}

	// group and personal perm stage
	switch ($caller) {
		case "index1_other":
		case "right_cat2":
		case "right_cal_other":
		case "right_blist_other":
			$this->append_gperm_query4cat( $caller, $whr_openarea ); //by ref
			break;
		default:
			// append group permissions and personal permissions
			if( $params['use_gp'] == 1 ) { 
				$this->append_gperm_query( $caller, $whr_openarea ); //by ref
			}
			if( $params['use_pp'] == 1 ) { 
				$this->append_pperm_query( $caller, $whr_openarea, $this->uid ); //by ref
			}
	}
	
	// finally, allow author himself
	switch ($caller) {
		case "right_cat2":
		case "right_cal_other":
		case "index1_other":
		case "right_blist_other":
			return $whr_openarea;
			break;
		default:
			return ("(d.uid='".$this->uid."' OR (d.openarea <>100 AND ".$whr_openarea." )) ");
	}

}

function append_friend_query( $caller, &$whr ) {

		$whr_sub1 = ""; $whr_sub2 = "";

	switch ($caller) {
		case "index1":
		case "detail1":	// for req_uid > 0 pages
			if(!empty($this->my_friends)) {
				$whr_sub1 = " OR (((d.openarea='2' AND (c.openarea<='3' OR c.openarea IS NULL)) 
					OR (c.openarea='2' AND d.openarea<='3')) 
					AND d.uid IN (".implode(",",$this->my_friends).")) ";
			}
			if(!empty($this->my_friends2)) {
				$whr_sub2 = " OR (((d.openarea='3' AND (c.openarea<='3' OR c.openarea IS NULL)) 
					OR (c.openarea='3' AND d.openarea<='3')) 
					AND d.uid IN (".implode(",",$this->my_friends2).")) ";
			}
			break;
			
		default:	// for req_uid == 0 pages with cfg.openarea
			if(!empty($this->my_friends)) {
				$whr_sub1 = " OR (((d.openarea='2' AND (c.openarea<='3' OR c.openarea IS NULL) 
						AND (cfg.openarea<='3' OR cfg.openarea IS NULL)) 
					OR (c.openarea='2' AND d.openarea<='3' AND (cfg.openarea<='3' OR cfg.openarea IS NULL))
					OR (cfg.openarea='2' AND d.openarea<='3' AND (c.openarea<='3' OR c.openarea IS NULL))) 
					AND d.uid IN (".implode(",",$this->my_friends).")) ";
			}
			if(!empty($this->my_friends2)) {
				$whr_sub2 = " OR (((d.openarea='3' AND (c.openarea<='3' OR c.openarea IS NULL) 
						AND (cfg.openarea<='3' OR cfg.openarea IS NULL)) 
					OR (c.openarea='3' AND d.openarea<='3' AND (cfg.openarea<='3' OR cfg.openarea IS NULL)) 
					OR (cfg.openarea='3' AND d.openarea<='3' AND (c.openarea<='3' OR c.openarea IS NULL))) 
					AND d.uid IN (".implode(",",$this->my_friends2).")) ";
			}
	}
		$whr = "(".$whr.$whr_sub1.$whr_sub2.") ";
}

function append_friend_query4cat( $caller, &$whr ) {

		$whr_sub1 = ""; $whr_sub2 = "";

	switch ($caller) {
		case "right_blist_other":
			if(!empty($this->my_friends)) {
				$whr_sub1 = " OR ((c.openarea='2' AND (cfg.openarea<='3' OR cfg.openarea IS NULL)) 
					AND d.uid IN (".implode(",",$this->my_friends).")) ";
			}
			if(!empty($this->my_friends2)) {
				$whr_sub2 = " OR ((c.openarea='3' AND (cfg.openarea<='3' OR cfg.openarea IS NULL)) 
					AND d.uid IN (".implode(",",$this->my_friends2).")) ";
			}
			break;
		default:
			if(!empty($this->my_friends)) {
				$whr_sub1 = " OR (c.openarea='2' AND d.uid IN (".implode(",",$this->my_friends).")) ";
			}
			if(!empty($this->my_friends2)) {
				$whr_sub2 = " OR (c.openarea='3' AND d.uid IN (".implode(",",$this->my_friends2).")) ";
			}
	}
		$whr = "(".$whr.$whr_sub1.$whr_sub2.") ";
}

function append_gperm_query( $caller, &$whr ) {
	switch ($caller) {
		default:
			if (!empty($this->mygids)) {
				$whr_sub1 = " OR ((d.openarea='10' OR d.openarea='20') AND (";
		      		foreach($this->mygids as $gid) {
					$whr_sub1 .= "d.vgids LIKE '%|".$gid."|%' OR ";
				}
            			$whr_sub1 = rtrim( $whr_sub1, "OR " ). "))" ;
				$whr_sub2 = str_replace('d.','c.',$whr_sub1) ;
				//var_dump($whr_sub1);var_dump($whr_sub2);
				$whr = "(".$whr.$whr_sub1.$whr_sub2.") ";
			}
	}
}

function append_gperm_query4cat( $caller, &$whr ) {
	switch ($caller) {
		case "right_blist_other":
		case "right_cat1":
		default:
			if (!empty($this->mygids)) {
				$whr_sub = " OR ((c.openarea='10' OR c.openarea='20') AND (";
		      		foreach($this->mygids as $gid) {
					$whr_sub .= "vgids LIKE '%|".$gid."|%' OR ";
				}
            			$whr_sub = rtrim( $whr_sub, "OR " ). "))" ;
				//var_dump($whr_sub);
				$whr = "(".$whr.$whr_sub.") ";
			}
	}
}

function append_pperm_query( $caller, &$whr, $uid ) {
	switch ($caller) {
		case "index1":
		default:
			$whr = "(".$whr." OR (d.openarea='20' AND d.vpids LIKE '%|".$this->uid."|%') 
					OR (c.openarea='20' AND c.vpids LIKE '%|".$this->uid."|%'))";
	}
}

} //end class

?>