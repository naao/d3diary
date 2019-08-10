<?php
class D3diaryDiary
{
	var $uid;
	var $bid;
	var $cid;
	var $title;
	var $diary;
	var $create_time;
	var $update_time;
	var $openarea;
	var $dohtml;
	var $vgids;
	var $vpids;
	var $view;

	var $bids;	// for multiread
	var $diaries=array();

	public function __construct(){
	}

    function &getInstance()
    {
        static $instance;
        if (!isset($instance)) {
            $instance = new D3diaryDiary();
        }
        return $instance;
    }

	function readdb($mydirname){
		global $xoopsDB ;
	
		$sql = "SELECT *
				  FROM ".$xoopsDB->prefix($mydirname.'_diary')."
		          WHERE bid=".intval($this->bid);

		$result = $xoopsDB->query($sql);
		$num_rows = $xoopsDB->getRowsNum($result);

		while ( $dbdat = $xoopsDB->fetchArray($result) ) {
			$this->cid   = $dbdat['cid'];
			$this->uid   = $dbdat['uid'];
			$this->title   = $dbdat['title'];
			$this->diary   = $dbdat['diary'];
			$this->create_time   = $dbdat['create_time'];
			$this->update_time   = $dbdat['update_time'];
			$this->openarea   = $dbdat['openarea'];
			$this->dohtml   = $dbdat['dohtml'];
			$this->vgids   = $dbdat['vgids'];
			$this->vpids   = $dbdat['vpids'];
			$this->view   = $dbdat['view'];
		}
	}

	function readdb_mul($mydirname){
		global $xoopsDB ;
	
		$whr_bids = " WHERE bid IN (".implode(',',$this->bids).")";
		$sql = "SELECT *
				  FROM ".$xoopsDB->prefix($mydirname.'_diary').$whr_bids." 
				  order by bid";
		$result = $xoopsDB->query($sql);
		$num_rows = $xoopsDB->getRowsNum($result);

		while ( $dbdat = $xoopsDB->fetchArray($result) ) {
			$this->diaries[(int)$dbdat['bid']] = $dbdat;
		}
	}

	function deletedb($mydirname){
		global $xoopsDB;

		$sql = "DELETE FROM ".$xoopsDB->prefix($mydirname.'_diary')." WHERE bid='".$this->bid."'";
		$result = $xoopsDB->query($sql);
		
		// newentryºï½ü
		$sql = "DELETE FROM ".$xoopsDB->prefix($mydirname.'_newentry')." WHERE uid='".$this->uid."' AND blogtype='0'";
		$result = $xoopsDB->query($sql);
		
	}

	function deletedb_mul($mydirname){
		global $xoopsDB;
		
		// at first, check bids for specified req_uid ( $this->uid must be set )
		$whr_bids = " WHERE uid=".intval($this->uid)." AND bid IN (".implode(',',$this->bids).")";
		$sql = "SELECT bid
				  FROM ".$xoopsDB->prefix($mydirname.'_diary').$whr_bids;
		$result = $xoopsDB->query($sql);
		$num_rows = $xoopsDB->getRowsNum($result);
		
		if ( $num_rows > 0 ) {
			$bids = array();
			while ( $dbdat = $xoopsDB->fetchArray($result) ) {
				$bids[] = $dbdat['bid'];
			}
		
			// delete checked bids
			$whr_bids = " WHERE bid IN (".implode(',',$bids).")";
			$sql = "DELETE FROM ".$xoopsDB->prefix($mydirname.'_diary').$whr_bids;
			$result = $xoopsDB->query($sql);
		
			// newentryºï½ü
			$sql = "DELETE FROM ".$xoopsDB->prefix($mydirname.'_newentry')." WHERE uid='".intval($this->uid)."' AND blogtype='0'";
			$result = $xoopsDB->query($sql);
		}
		
		return $bids;

	}

	function insertdb( $mydirname, $force=false ){
		global $xoopsDB;

		if ($this->create_time) {
			$ctime= $this->create_time;
		} else {
			$ctime=date( "Y-m-d H:i:s" );
		}

		if ( $this->openarea != 100) {
			$sql = "DELETE FROM ".$xoopsDB->prefix($mydirname.'_newentry').
				" WHERE uid='".$this->uid."' AND blogtype='0'";
			$result = $xoopsDB->query($sql);
		}

        	if (!get_magic_quotes_gpc()) {
			$sql = "INSERT INTO ".$xoopsDB->prefix($mydirname.'_diary')."
					(uid, cid, title, diary, create_time, update_time, openarea, dohtml, vgids, vpids)
					VALUES (
					'".addslashes($this->uid)."',
					'".addslashes($this->cid)."',
					'".addslashes($this->title)."',
					'".addslashes($this->diary)."',
					'".addslashes($ctime)."',
					'".addslashes($ctime)."',
					'".addslashes($this->openarea)."',
					'".addslashes($this->dohtml)."',
					'".addslashes($this->vgids)."',
					'".addslashes($this->vpids)."'
					)";

			$tmptitle=addslashes($this->title);
		} else {
			$sql = "INSERT INTO ".$xoopsDB->prefix($mydirname.'_diary')."
					(uid, cid, title, diary, create_time, update_time, openarea, dohtml, vgids, vpids)
					VALUES (
					'".$this->uid."',
					'".$this->cid."',
					'".$this->title."',
					'".$this->diary."',
					'".$ctime."',
					'".$ctime."',
					'".$this->openarea."',
					'".$this->dohtml."',
					'".$this->vgids."',
					'".$this->vpids."'
					)";
			$tmptitle=$this->title;
		}

		if ($force == true) {
			$result = $xoopsDB->queryF($sql);
		} else {
			$result = $xoopsDB->query($sql);
		}
		$this->bid=$xoopsDB->getInsertId();

		return $this->bid;
	}

	function updatedb($mydirname, $update_create_time){
		global $xoopsDB;

		$utime=date("Y-m-d H:i:s");
		
        	if (!get_magic_quotes_gpc()) {
			$cid=addslashes($this->cid);
			$title=addslashes($this->title);
			$diary=addslashes($this->diary);
			$openarea=addslashes($this->openarea);
			$dohtml=addslashes($this->dohtml);
			$bid=addslashes($this->bid);
			$vgids=addslashes($this->vgids);
			$vpids=addslashes($this->vpids);
		} else {
			$cid=$this->cid;
			$title=$this->title;
			$diary=$this->diary;
			$openarea=$this->openarea;
			$dohtml=$this->dohtml;
			$bid=$this->bid;
			$vgids=$this->vgids;
			$vpids=$this->vpids;
		}
		

		// update the create_time
		if ($update_create_time) {
			$sql = "DELETE FROM ".$xoopsDB->prefix($mydirname.'_newentry').
				" WHERE uid='".$this->uid."' AND blogtype='0'";
			$result = $xoopsDB->query($sql);
			
			$sql = "UPDATE ".$xoopsDB->prefix($mydirname.'_diary')." SET
					cid='".$cid."',
					title='".$title."',
					diary='".$diary."',
					update_time='".$utime."',
					create_time='".$utime."',
					openarea='".$openarea."',
					dohtml='".$dohtml."',
					vgids='".$vgids."',
					vpids='".$vpids."'
					WHERE bid=".$bid;
			$result = $xoopsDB->query($sql);

		// no-update the create_time
		} else {
			if ($this->create_time) {
				$ctime= $this->create_time ;
				$sql = "UPDATE ".$xoopsDB->prefix($mydirname.'_diary')." SET
					cid='".$cid."',
					title='".$title."',
					diary='".$diary."',
					update_time='".$utime."',
					create_time='".$ctime."',
					openarea='".$openarea."',
					dohtml='".$dohtml."',
					vgids='".$vgids."',
					vpids='".$vpids."'
					WHERE bid=".$bid;
			} else {
				$sql = "UPDATE ".$xoopsDB->prefix($mydirname.'_diary')." SET
					cid='".$cid."',
					title='".$title."',
					diary='".$diary."',
					update_time='".$utime."',
					openarea='".$openarea."',
					dohtml='".$dohtml."',
					vgids='".$vgids."',
					vpids='".$vpids."'
					WHERE bid=".$bid;
			}
			
			$result = $xoopsDB->query($sql);
		}
	}

}
?>
