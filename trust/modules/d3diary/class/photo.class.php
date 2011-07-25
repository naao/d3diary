<?php
include_once XOOPS_ROOT_PATH."/class/xoopstree.php";

class D3diaryPhoto
{
	var $uid;
	var $bid;
	var $pid;
	var $ptype;
	var $tstamp;
	var $info = "";
	
	var $bids   = array();	// for readdb_mul
	var $photos = array();
	var $pids   = array();	// for deletedb_mul

	public function __construct(){
	}

    function &getInstance()
    {
        static $instance;
        if (!isset($instance)) {
            $instance = new D3diaryPhoto();
        }
        return $instance;
    }

	function init_values($mydirname){
		unset ($this->pid);
		unset ($this->ptype);
		unset ($this->tstamp);
		unset ($this->info);
		unset ($this->bids);
		unset ($this->photos);
		unset ($this->pids);
	}

	function readdb($mydirname){
		global $xoopsDB;
	
		$sql = "SELECT * FROM ".$xoopsDB->prefix($mydirname.'_photo')." 
				WHERE bid='".$this->bid."' and pid='".$this->pid."'";

		$result = $xoopsDB->query($sql);
		$num_rows = $xoopsDB->getRowsNum($result);
		
		while ( $dbdat = $xoopsDB->fetchArray($result) ) {
			$this->uid = (int)$dbdat['uid'] ;
			$this->ptype = $dbdat['ptype'];
			$this->info = $dbdat['info'] ;
			$this->tstamp = $dbdat['tstamp'] ;
		}

		//var_dump($sql);

	}

	function readdb_mul($mydirname) {
		global $xoopsDB;
	
		$this->pid = ""; $this->ptype = "";
		
		$whr_bids = " WHERE bid IN (".implode(',',$this->bids).")";
		$sql = "SELECT *
				FROM ".$xoopsDB->prefix($mydirname.'_photo').$whr_bids." 
				ORDER BY bid, tstamp, pid";
		
		$result = $xoopsDB->query($sql);
		$num_rows = $xoopsDB->getRowsNum($result);
		
		$last_b = 0; $i = 0;
		if ($num_rows<1) {return false;}
		$this->photos = array();
		while ( $dbdat = $xoopsDB->fetchArray($result) ) {
			if( $last_b != $dbdat['bid'] ){ $p=0;}
			
			$i= (int)$dbdat['bid'];
			$photo = & $this->photos[$i][$p] ;
			
			$photo['uid']	= (int)$dbdat['uid'] ;
			$photo['pid']   = $dbdat['pid'] ;
			$photo['ptype'] = $dbdat['ptype'] ;
			$photo['pname'] = $dbdat['pid'].$dbdat['ptype'] ;
			$photo['thumbnail'] = "t_".$dbdat['pid'].$dbdat['ptype'] ;
			$photo['info']  = $dbdat['info'] ;
			//var_dump($i);var_dump($p); var_dump($this->photos[$i][$p]); echo"<br />";
			$p++;
			$last_b = $dbdat['bid'];
		}
	}

	function readdb_bypids($mydirname) {
		global $xoopsDB;
		
		$this->bids = "";
		
		$whr_pids = " WHERE pid IN ('".implode('\',\'',$this->pids)."')";
		$sql = "SELECT *
				FROM ".$xoopsDB->prefix($mydirname.'_photo').$whr_pids." 
				ORDER BY bid, tstamp, pid";
		//var_dump($sql); echo"<br />";
		$result = $xoopsDB->query($sql);
		$num_rows = $xoopsDB->getRowsNum($result);

		if ($num_rows<1) {return false;}
		$photo = array() ;
		while ( $dbdat = $xoopsDB->fetchArray($result) ) {
				$photo['pid']   = $dbdat['pid'] ;
				$photo['ptype'] = $dbdat['ptype'] ;
				$photo['pname'] = $dbdat['pid'].$dbdat['ptype'] ;
				$photo['thumbnail'] = "t_".$dbdat['pid'].$dbdat['ptype'] ;
				$photo['info']  = $dbdat['info'] ;
				$photo['bid']   = (int)$dbdat['bid'] ;
				$photo['uid']   = (int)$dbdat['uid'] ;
			//var_dump($photo); echo"<br />";
				$this->photos[$photo['pid']] = $photo;
		}
	}

	function readrand($mydirname){
		global $xoopsDB;

		$this->pid = ""; $this->ptype = "";
		
		$sql = "SELECT *
				  FROM ".$xoopsDB->prefix($mydirname.'_photo')."
		          WHERE bid='".intval($this->bid)."' ORDER BY rand() LIMIT 0,1";

		$result = $xoopsDB->query($sql);
		$num_rows = $xoopsDB->getRowsNum($result);

		while ( $dbdat = $xoopsDB->fetchArray($result) ) {
			$this->pid   = $dbdat['pid'];
			$this->ptype = $dbdat['ptype'];
		}
	}

	function readrand_mul($mydirname){
		global $xoopsDB;
	
		$this->photos = array();
		$photos = array();
		$whr_bids = " WHERE bid IN (".implode(',',$this->bids).")";
		$sql = "SELECT *
				  FROM ".$xoopsDB->prefix($mydirname.'_photo').$whr_bids." 
				  order by bid";

		$result = $xoopsDB->query($sql);
		$num_rows = $xoopsDB->getRowsNum($result);
		
		$last_b = 0; $i = 0; $_pid=array(); $_ptype=array();
		if ($num_rows<1) {return false;}
		while ( $dbdat = $xoopsDB->fetchArray($result) ) {
			if( $last_b > 0 && $last_b != $dbdat['bid'] ){
				$rand  = rand(0,$i-1) ;
				$this->photos[$last_b]['bid'] = $last_b ;
				$this->photos[$last_b]['pid'] = $_pid[$rand] ;
				$this->photos[$last_b]['ptype'] = $_ptype[$rand] ;
				$i = 0;
				$_pid=array(); $_ptype=array();
			}
			$last_b = $dbdat['bid'];
			$_pid[$i] = $dbdat['pid'];
			$_ptype[$i] = $dbdat['ptype'];
			$i++;
		}
			// for last 1 photo
				$rand  = rand(0,$i-1) ;
				$this->photos[$last_b]['bid'] = $last_b ;
				$this->photos[$last_b]['pid'] = $_pid[$rand] ;
				$this->photos[$last_b]['ptype'] = $_ptype[$rand] ;
	}

	function deletedb($mydirname){
		global $xoopsDB;

		$sql = "DELETE FROM ".$xoopsDB->prefix($mydirname.'_photo')." WHERE bid='".$this->bid."' and pid='".$this->pid."'";
		$result = $xoopsDB->query($sql);
		return ($result);
	}

	function deletedbF($mydirname){
		global $xoopsDB;

		$sql = "DELETE FROM ".$xoopsDB->prefix($mydirname.'_photo')." WHERE bid='".$this->bid."' and pid='".$this->pid."'";
		$result = $xoopsDB->queryF($sql);
		return ($result);
	}

	function deletedb_mul($mydirname){
		global $xoopsDB;
	
		$sql = "DELETE FROM ".$xoopsDB->prefix($mydirname.'_photo')." 
				 WHERE bid='".$this->bid."' and pid IN (".implode(',',$this->pids).")";
		$result = $xoopsDB->query($sql);
	}

	function deletedbF_mul($mydirname){
		global $xoopsDB;
	
		$sql = "DELETE FROM ".$xoopsDB->prefix($mydirname.'_photo')." 
				 WHERE bid='".$this->bid."' and pid IN (".implode(',',$this->pids).")";
		$result = $xoopsDB->queryF($sql);
	}

	function insertdb($mydirname, $force=false ){
		global $xoopsDB;

		if ($this->tstamp) {
			$ctime= $this->tstamp;
		} else {
			$ctime=date( "Y-m-d H:i:s" );
		}

        if (!get_magic_quotes_gpc()) {
			$sql = "INSERT INTO ".$xoopsDB->prefix($mydirname.'_photo')."
					(uid, bid, pid, ptype, tstamp, info)
					VALUES (
					'".addslashes($this->uid)."',
					'".addslashes($this->bid)."',
					'".addslashes($this->pid)."',
					'".addslashes($this->ptype)."',
					'".addslashes($ctime)."',
					'".addslashes($this->info)."'
					)";
		} else {
			$sql = "INSERT INTO ".$xoopsDB->prefix($mydirname.'_photo')."
					(uid, bid, pid, ptype, tstamp, info)
					VALUES (
					'".$this->uid."',
					'".$this->bid."',
					'".$this->pid."',
					'".$this->ptype."',
					'".$ctime."',
					'".$this->info."'
					)";
		}
		if ($force == true) {
			$result = $xoopsDB->queryF($sql);
		} else {
			$result = $xoopsDB->query($sql);
		}
		return $this->pid;
	}

	function updatedb($mydirname){
		global $xoopsDB;

      		if (!get_magic_quotes_gpc()) {
			$sql = "UPDATE ".$xoopsDB->prefix($mydirname.'_photo')." SET
					uid='".addslashes($this->uid)."',
					bid='".addslashes($this->bid)."',
					pid='".addslashes($this->pid)."',
					ptype='".addslashes($this->ptype)."',
					tstamp='".addslashes($this->tstamp)."',
					info='".addslashes($this->info)."'
					WHERE bid='".$this->bid."' and pid='".$this->pid."'";
		} else {
			$sql = "UPDATE ".$xoopsDB->prefix($mydirname.'_photo')." SET
					uid='".$this->uid."',
					bid='".$this->bid."',
					pid='".$this->pid."',
					ptype='".$this->ptype."',
					tstamp='".$this->tstamp."',
					info='".$this->info."'
					WHERE bid='".$this->bid."' and pid='".$this->pid."'";
		}
			$result = $xoopsDB->query($sql);
			//var_dump($sql);
			return $this->pid;
	}

	function updatedb_bid($mydirname, $new_bid){
		global $xoopsDB;

      		if (!get_magic_quotes_gpc()) {
			$sql = "UPDATE ".$xoopsDB->prefix($mydirname.'_photo')." SET
					uid='".addslashes($this->uid)."',
					bid='".addslashes($new_bid)."',
					pid='".addslashes($this->pid)."',
					ptype='".addslashes($this->ptype)."',
					tstamp='".addslashes($this->tstamp)."',
					info='".addslashes($this->info)."'
					WHERE bid='".$this->bid."' and pid='".$this->pid."'";
		} else {
			$sql = "UPDATE ".$xoopsDB->prefix($mydirname.'_photo')." SET
					uid='".$this->uid."',
					bid='".$new_bid."',
					pid='".$this->pid."',
					ptype='".$this->ptype."',
					tstamp='".$this->tstamp."',
					info='".$this->info."'
					WHERE bid='".$this->bid."' and pid='".$this->pid."'";
		}
			$result = $xoopsDB->query($sql);
			//var_dump($sql);
			return $this->pid;
	}

}
?>
