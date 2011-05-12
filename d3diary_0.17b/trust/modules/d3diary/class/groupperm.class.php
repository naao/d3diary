<?php
if( ! class_exists( 'D3dGperm' ) ) {

class D3dGperm
{
	var $gperm_id;
	var $gperm_groupid;
	var $gperm_itemid;
	var $gperm_modid;
	var $gperm_name;
	
	var $d3dConf ;
	var $mPerm ;
	var $func ;
	var $mydirname ;
	var $mid ;
	var $mod_config ;
	var $dcfg ;
	var $uid ;
	var $req_uid ;
	var $group_list = array();
	var $gperm_config = array();
	var $use_gp ;
	var $use_pp ;

	function D3dGperm( & $d3dConf ){

		$this->d3dConf = & $d3dConf;

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
	
	$this->mPerm = & $this->d3dConf->mPerm;
	$this->func = & $this->d3dConf->func;


		// group permissions
		$member_handler =& xoops_gethandler('member');
		$this->group_list =& $member_handler->getGroupList(0, $this->mid);
		$this->gperm_config = array(
  	    		'allow_edit' => '_MD_D3DIARY_PERMDESC_ALLOW_EDIT',
	    		'allow_html' => '_MD_D3DIARY_PERMDESC_ALLOW_HTML',
	    		'allow_regdate' => '_MD_D3DIARY_PERMDESC_ALLOW_REGDATE',
    			'allow_mailpost' => '_MD_D3DIARY_PERMDESC_ALLOW_MAILPOST',
	    		'allow_gpermission' => '_MD_D3DIARY_PERMDESC_ALLOW_GPERM',
	    		'allow_ppermission' => '_MD_D3DIARY_PERMDESC_ALLOW_PPERM'
			);

		$this->use_gp = 0;  $this->use_pp = 0;
		$_oc = (int)$this->mod_config['use_open_cat'];
		$_oe = (int)$this->mod_config['use_open_entry'];
		if( $_oc == 10 || $_oc == 20 || $_oe == 10 || $_oe == 20 ) { $this->use_gp = 1; }
		if( $_oc == 20 || $_oe == 20 ) { $this->use_pp = 1; }
}

function &getUidsByGids( $gids=array() )
{
 	$db = & $this->d3dConf->db;

	$str_gids = implode( ',' , $gids );
	$sql = "select uid FROM ".$db->prefix('groups_users_link')." WHERE groupid IN (".$str_gids.")";

	$result = $db->query($sql);
	$uid_gperm=array();
	while ( $dbdat = $db->fetchArray($result) ) {
		$uid_gperm[] = (int)$dbdat['uid'];
	}
	$uid_gperm = array_unique($uid_gperm);
	return $uid_gperm;
}

function &getUidsByName($item_name, $mid=null, $item_id=0)
{
 	$db = & $this->d3dConf->db;

   	$_item_name = implode(",",$item_name);
    	$_item_name = str_replace(",","','",$_item_name);
    	$mid = !empty($mid) ? (int)$mid : $this->mid;

	$sql = "select * FROM ".$db->prefix('groups_users_link')." gu 
			INNER JOIN ".$db->prefix('group_permission')." gp 
				ON gp.gperm_groupid=gu.groupid AND gp.gperm_modid='".$mid."' AND 
				gp.gperm_name IN ('".$_item_name."') 
				ORDER BY gp.gperm_name";

	$result = $db->query($sql);
	$i = 0; $lastkey = ""; $temp_gperm=array(); $uid_gperm=array();
	while ( $dbdat = $db->fetchArray($result) ) {
		$temp_gperm[$dbdat['gperm_name']][] = (int)$dbdat['uid'];
	}
	
	foreach ($temp_gperm as $key=>$gp) { $uid_gperm[$key] = array_unique($gp); }
	return $uid_gperm;
}

function &getUsersByName($item_name, $mid=null, $item_id=0)
{
 	$db = & $this->d3dConf->db;

    	$_item_name = implode(",",$item_name);
    	$_item_name = str_replace(",","','",$_item_name);
    	$mid = !empty($mid) ? (int)$mid : $this->mid;

	$sql = "select * FROM ".$db->prefix('users')." u 
			INNER JOIN ".$db->prefix('groups_users_link')." gu USING(uid) 
			INNER JOIN ".$db->prefix('group_permission')." gp 
				ON gp.gperm_groupid=gu.groupid AND gp.gperm_modid='".$mid."' AND 
				gp.gperm_name IN ('".$_item_name."') 
				ORDER BY gp.gperm_name";

	$result = $db->query($sql);
	$i = 0; $lastkey = ""; $temp_gperm=array(); $uname_gperm=array();
	while ( $dbdat = $db->fetchArray($result) ) {
		$temp_gperm[$dbdat['gperm_name']][] = $dbdat['uname'];
	}
	
	foreach ($temp_gperm as $key=>$gp) { $uname_gperm[$key] = array_unique($gp); }
	return $uname_gperm;
}

function &getPermsByGroup($item_id=0, $mid=null)
{
 	$db = & $this->d3dConf->db;

	$item_id = intval($item_id);
	
	$sql = "select * FROM ".$db->prefix('group_permission')." 
				WHERE gperm_modid='".$mid."' AND gperm_itemid='".$item_id."' 
				ORDER BY gperm_name";

	$result = $db->query($sql);

	while ( $dbdat = $db->fetchArray($result) ) {
		$temp_gperm[$dbdat['gperm_itemid']][$dbdat['gperm_groupid']][$dbdat['gperm_name']] = 1;
	}
	//var_dump($temp_gperm);
	return $temp_gperm;

}

function deleteGroupPerm($item_id=0, $mid=null)
{
 	$db = & $this->d3dConf->db;

	$sql = "DELETE FROM ".$db->prefix('group_permission')." 
				WHERE gperm_modid='".$mid."' AND gperm_itemid='".$item_id."'";
				
		$result = $db->query($sql);

}

function insertGroupPerm()
{
  	$db = & $this->d3dConf->db;

      if (!get_magic_quotes_gpc()) {
		$sql = "INSERT INTO ".$db->prefix('group_permission')." 
				(gperm_groupid, gperm_itemid, gperm_modid, gperm_name)
				VALUES (
				'".addslashes($this->gperm_groupid)."',
				'".addslashes($this->gperm_itemid)."',
				'".addslashes($this->gperm_modid)."',
				'".addslashes($this->gperm_name)."'
				)";
					
	} else {
		$sql = "INSERT INTO ".$db->prefix('group_permission')." 
				(gperm_groupid, gperm_itemid, gperm_modid, gperm_name)
				VALUES (
				'".$this->gperm_groupid."',
				'".$this->gperm_itemid."',
				'".$this->gperm_modid."',
				'".$this->gperm_name."'
				)";
	}
	
	$result = $db->query($sql);
	//var_dump($sql);
	return $result;
}

}	//end class d3dGperm
}
?>
