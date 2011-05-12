<?php
include_once XOOPS_ROOT_PATH."/class/xoopstree.php";

class Tag
{
	var $tag_id;
	var $tag_name;
	var $bid;
	var $uid;
	var $tag_group;
	var $reg_unixtime;

	var $bids = array();	// for readdb_mul
	var $tags = array();

	function Tag(){
	}

    function &getInstance()
    {
        static $instance;
        if (!isset($instance)) {
            $instance = new Tag();
        }
        return $instance;
    }

	function readdb($mydirname){
		global $xoopsDB;
	
		$sql = "SELECT *
				  FROM ".$xoopsDB->prefix($mydirname.'_tag')."
		          WHERE tag_id='".intval($this->tag_id)."'";

		$result = $xoopsDB->query($sql);
		$num_rows = $xoopsDB->getRowsNum($result);

		while ( $dbdat = $xoopsDB->fetchArray($result) ) {
			$this->tag_name   = $dbdat['tag_name'];
			$this->bid   = $dbdat['bid'];
			$this->uid   = $dbdat['uid'];
			$this->tag_group   = $dbdat['tag_group'];
			$this->reg_unixtime   = $dbdat['reg_unixtime'];
		}
	}

	function readdb_mul($mydirname) {
		global $xoopsDB;
	
		$whr_bids = " WHERE bid IN (".implode(',',$this->bids).")";
		$sql = "SELECT *
				FROM ".$xoopsDB->prefix($mydirname.'_tag').$whr_bids." 
				ORDER BY bid";
		
		$result = $xoopsDB->query($sql);
		$num_rows = $xoopsDB->getRowsNum($result);
		
		$last_b = 0; $i = 0; $p = 0;
		if ($num_rows<1) {return false;}
		$this->tags = array();
		while ( $dbdat = $xoopsDB->fetchArray($result) ) {
			if( $last_b != $dbdat['bid'] ){ $p=0;}
			
			$i= $dbdat['bid'];
			$this->tags[$i][$p]['tag'] = htmlspecialchars($dbdat['tag_name'], ENT_QUOTES) ;
			$this->tags[$i][$p]['tag_urlenc'] = rawurlencode($dbdat['tag_name']);
			$this->tags[$i][$p]['bid'] = $dbdat['bid'] ;
			$this->tags[$i][$p]['uid'] = $dbdat['uid'] ;
			$this->tags[$i][$p]['tag_group'] = $dbdat['tag_group'] ;
			$this->tags[$i][$p]['reg_unixtime']  = $dbdat['reg_unixtime'] ;
			$p++;
			$last_b = $dbdat['bid'];
		}
	}

	function read_bid_byname($mydirname) {
		global $xoopsDB;
	
	       	if (!get_magic_quotes_gpc()) {
			$tag_name = addslashes($this->tag_name);
		} else {
			$tag_name = $this->tag_name;
		}

		$sql = "SELECT bid FROM ".$xoopsDB->prefix($mydirname.'_tag')." 
				WHERE uid='".$this->uid."' AND tag_name='".$tag_name."'";

		$result = $xoopsDB->query($sql);
		$this->bids = array();
		while ( $dbdat = $xoopsDB->fetchArray($result) ) {
			$this->bids[] = (int)$dbdat['bid'];
		}
	}

	function deletedb($mydirname){
		global $xoopsDB;

		$sql = "DELETE FROM ".$xoopsDB->prefix($mydirname.'_tag')." WHERE tag_id='".$this->tag_id."'";
		$result = $xoopsDB->query($sql);
	}

	function deletedbF($mydirname){
		global $xoopsDB;

		$sql = "DELETE FROM ".$xoopsDB->prefix($mydirname.'_tag')." WHERE tag_id='".$this->tag_id."'";
		$result = $xoopsDB->queryF($sql);
	}

	function delete_by_bid($mydirname){
		global $xoopsDB;

		$sql = "DELETE FROM ".$xoopsDB->prefix($mydirname.'_tag')." WHERE uid='".$this->uid."' AND bid='".$this->bid."'";
		$result = $xoopsDB->query($sql);
	}

	function deletedb_byname_mul($mydirname) {
		global $xoopsDB;
	
	       	if (!get_magic_quotes_gpc()) {
			$tag_name = addslashes($this->tag_name);
		} else {
			$tag_name = $this->tag_name;
		}

		$sql = "DELETE FROM ".$xoopsDB->prefix($mydirname.'_tag')." 
				WHERE uid='".$this->uid."' AND tag_name='".$tag_name."' 
				AND bid IN (".implode(',',$this->bids).")";

		$result = $xoopsDB->query($sql);
	}

	function insertdb($mydirname){
		global $xoopsDB;

 		if ($this->reg_unixtime) {
			$ctime = $this->reg_unixtime;
		} else {
			$ctime = time();
		}

	       	if (!get_magic_quotes_gpc()) {
			$sql = "INSERT INTO ".$xoopsDB->prefix($mydirname.'_tag')."
					(tag_id, tag_name, bid, uid, tag_group, reg_unixtime)
					VALUES (
					'".addslashes($this->tag_id)."',
					'".addslashes($this->tag_name)."',
					'".addslashes($this->bid)."',
					'".addslashes($this->uid)."',
					'".addslashes($this->tag_group)."',
					'".addslashes($ctime)."'
					)";

		} else {
			$sql = "INSERT INTO ".$xoopsDB->prefix($mydirname.'_tag')."
					(tag_id, tag_name, bid, uid, tag_group, reg_unixtime)
					VALUES (
					'".$this->tag_id."',
					'".$this->tag_name."',
					'".$this->bid."',
					'".$this->uid."',
					'".$this->tag_group."',
					'".$ctime."'
					)";
		}
		$result = $xoopsDB->query($sql);
		return $this->tag_id;
	}

	function insertdb_byname_mul($mydirname){
		global $xoopsDB;

 		if ($this->reg_unixtime) {
			$ctime = $this->reg_unixtime;
		} else {
			$ctime = time();
		}

		foreach ($this->bids as $bid) {
			$temp_arr = array($this->tag_id,
					$this->tag_name,
					$bid,
					$this->uid,
					$this->tag_group,
					$ctime);
			if (!get_magic_quotes_gpc()) { $temp_arr = array_map("addslashes",$temp_arr); }
			$tag_arr[] = "('".implode( "','" , $temp_arr)."')";
		}
		
		$tag_arr_str = implode( "," , $tag_arr );

		$sql = "INSERT INTO ".$xoopsDB->prefix($mydirname.'_tag')."
				(tag_id, tag_name, bid, uid, tag_group, reg_unixtime)
				VALUES ".$tag_arr_str ;

		$result = $xoopsDB->query($sql);

	}

	function updatedb($mydirname){
		global $xoopsDB;

        	if (!get_magic_quotes_gpc()) {
			$sql = "UPDATE ".$xoopsDB->prefix($mydirname.'_tag')." SET
					tag_id='".addslashes($this->tag_id)."',
					tag_name='".addslashes($this->tag_name)."',
					bid='".addslashes($this->bid)."',
					uid='".addslashes($this->uid)."',
					tag_group='".addslashes($this->tag_group)."',
					reg_unixtime='".addslashes($this->reg_unixtime)."'
					WHERE tag_id=".addslashes($this->tag_id);
		} else {
			$sql = "UPDATE ".$xoopsDB->prefix($mydirname.'_tag')." SET
					tag_id='".$this->tag_id."',
					tag_name='".$this->tag_name."',
					bid='".$this->bid."',
					uid='".$this->uid."',
					tag_group='".$this->tag_group."',
					reg_unixtime='".$this->reg_unixtime."'
					WHERE tag_id=".$this->tag_id;
		}
		$result = $xoopsDB->query($sql);
		return $this->tag_id;
	}

	function updatedb_byname_mul($mydirname, $rev_tag){
		global $xoopsDB;

		$ctime = time();

	       	if (!get_magic_quotes_gpc()) {
			$tag_name = addslashes($this->tag_name);
			$rev_tag = addslashes($rev_tag);
		} else {
			$tag_name = $this->tag_name;
		}

		$sql = "UPDATE ".$xoopsDB->prefix($mydirname.'_tag')." 
				SET tag_name='".$rev_tag."',
				reg_unixtime='".$ctime."' 
				WHERE uid='".$this->uid."' AND tag_name='".$tag_name."' 
				AND bid IN (".implode(',',$this->bids).")";

		$result = $xoopsDB->query($sql);

	}

}
?>
