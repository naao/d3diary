<?php
include_once XOOPS_ROOT_PATH."/class/xoopstree.php";

class Diary
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

	function Diary(){
	}

    function &getInstance()
    {
        static $instance;
        if (!isset($instance)) {
            $instance = new Diary();
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

	function deletedb($mydirname){
		global $xoopsDB;

		$sql = "DELETE FROM ".$xoopsDB->prefix($mydirname.'_diary')." WHERE bid='".$this->bid."'";
		$result = $xoopsDB->query($sql);
		
		// newentry¹¹¿·
		$sql = "DELETE FROM ".$xoopsDB->prefix($mydirname.'_newentry')." WHERE uid='".$this->uid."' AND cid='0'";
		$result = $xoopsDB->query($sql);
		
		$sql = "SELECT *
				  FROM ".$xoopsDB->prefix($mydirname.'_diary')."
		          WHERE uid='".intval($this->uid)."' ORDER BY create_time DESC";

		$result = $xoopsDB->query($sql);
		if ( $dbdat = $xoopsDB->fetchArray($result) ) {
		
	        if (!get_magic_quotes_gpc()) {
				$tmptitle=addslashes($dbdat['title']);
			}else{
				$tmptitle=$dbdat['title'];
			}
			
			$sql = "INSERT INTO ".$xoopsDB->prefix($mydirname.'_newentry')."
					(uid, cid, title, url, create_time, blogtype)
					VALUES (
					'".$dbdat['uid']."',
					'".$dbdat['cid']."',
					'".$tmptitle."',
					'".XOOPS_URL."/modules".$mydirname."/index.php?page=detail&bid=".$dbdat['bid']."',
					'".$dbdat['create_time']."',
					'0',
					'".$dbdat['openarea']."'
					)";
			$result = $xoopsDB->query($sql);
		}

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
				" WHERE uid='".$this->uid."' AND cid='0'";
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

	if ( $this->openarea != 100) {
	        if (!get_magic_quotes_gpc()) {
			$sql = "INSERT INTO ".$xoopsDB->prefix($mydirname.'_newentry')."
				(uid, cid, title, url, create_time, blogtype, diary)
				VALUES (
				'".addslashes($this->uid)."',
				'".addslashes($this->cid)."',
				'".addslashes($tmptitle)."',
				'".addslashes(XOOPS_URL."/modules/".$mydirname."/index.php?page=detail&bid=".$this->bid)."',
				'".addslashes($ctime)."',
				'0',
				'".addslashes($this->diary)."'
				)";
		} else {
			$sql = "INSERT INTO ".$xoopsDB->prefix($mydirname.'_newentry')."
				(uid, cid, title, url, create_time, blogtype, diary)
				VALUES (
				'".$this->uid."',
				'".$this->cid."',
				'".$tmptitle."',
				'".XOOPS_URL."/modules/".$mydirname."/index.php?page=detail&bid=".$this->bid."',
				'".$ctime."',
				'0',
				'".$this->diary."'
				)";
		}
		if ($force == true) {
			$result = $xoopsDB->queryF($sql);
		} else {
			$result = $xoopsDB->query($sql);
		}
	}
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
				" WHERE uid='".$this->uid."' AND cid='0'";
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

			$sql = "INSERT INTO ".$xoopsDB->prefix($mydirname.'_newentry')."
				(uid, cid, title, url, create_time, blogtype)
				VALUES (
				'".$this->uid."',
				'".$this->cid."',
				'".$title."',
				'".XOOPS_URL."/modules/".$mydirname."/index.php?page=detail&bid=".$this->bid."',
				'".$utime."',
				'0'
				)";
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
