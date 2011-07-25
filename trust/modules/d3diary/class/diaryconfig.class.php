<?php
include_once XOOPS_ROOT_PATH."/class/xoopstree.php";

class DiaryConfig
{
	var $uid;
	var $blogtype;
	var $blogurl;
	var $rss;
	var $openarea;
	var $mailpost;
	var $address;
	var $keep;
	var $uptime;
	var $updated;

	public function __construct(){
	}

    function &getInstance()
    {
        static $instance;
        if (!isset($instance)) {
            $instance = new DiaryConfig();
        }
        return $instance;
    }

	function readdb($mydirname){
		global $xoopsDB;
	
		$sql = "SELECT *
				  FROM ".$xoopsDB->prefix($mydirname.'_config')."
		          WHERE uid='".$this->uid."'";

		$result = $xoopsDB->query($sql);
		$num_rows = $xoopsDB->getRowsNum($result);
		
		while ( $dbdat = $xoopsDB->fetchArray($result) ) {
			$this->blogtype   = $dbdat['blogtype'];
			$this->blogurl    = $dbdat['blogurl'];
			$this->rss        = $dbdat['rss'];
			$this->openarea   = $dbdat['openarea'];
			$this->mailpost   = $dbdat['mailpost'];
			$this->address    = $dbdat['address'];
			$this->keep	  = $dbdat['keep'];
			$this->uptime     = $dbdat['uptime'];
			$this->updated    = $dbdat['updated'];
		}
		return $num_rows;
	}

	function deletedb($mydirname){
		global $xoopsDB;

		$sql = "DELETE FROM ".$xoopsDB->prefix($mydirname.'_config')." WHERE uid='".$this->uid."'";
		$result = $xoopsDB->query($sql);
		return $this->uid;
	}

	function insertdb($mydirname){
		global $xoopsDB;

		$ctime = !empty( $this->updated ) ? $this->updated : time();

        if (!get_magic_quotes_gpc()) {
			$sql = "INSERT INTO ".$xoopsDB->prefix($mydirname.'_config')."
					(uid, blogtype, blogurl, rss, openarea, mailpost, address, keep, uptime, updated)
					VALUES (
					'".addslashes($this->uid)."',
					'".addslashes($this->blogtype)."',
					'".addslashes($this->blogurl)."',
					'".addslashes($this->rss)."',
					'".addslashes($this->openarea)."',
					'".addslashes($this->mailpost)."',
					'".addslashes($this->address)."',
					'".addslashes($this->keep)."',
					'".addslashes($this->uptime)."',
					'".addslashes($ctime)."'
					)";
		} else {
			$sql = "INSERT INTO ".$xoopsDB->prefix($mydirname.'_config')."
					(uid, blogtype, blogurl, rss, openarea, mailpost, address, keep, uptime, updated)
					VALUES (
					'".$this->uid."',
					'".$this->blogtype."',
					'".$this->blogurl."',
					'".$this->rss."',
					'".$this->openarea."',
					'".$this->mailpost."',
					'".$this->address."',
					'".$this->keep."',
					'".$this->uptime."',
					'".$ctime."'
					)";
		}
		$result = $xoopsDB->query($sql);
		return $xoopsDB->getInsertId();
	}

	function updatedb($mydirname, $force=false ){
		global $xoopsDB;

		$ctime = time();

        if (!get_magic_quotes_gpc()) {
			$sql = "UPDATE ".$xoopsDB->prefix($mydirname.'_config')." SET
					blogtype='".addslashes($this->blogtype)."',
					blogurl='".addslashes($this->blogurl)."',
					rss='".addslashes($this->rss)."',
					openarea='".addslashes($this->openarea)."',
					mailpost='".addslashes($this->mailpost)."',
					address='".addslashes($this->address)."',
					keep='".addslashes($this->keep)."',
					uptime='".addslashes($this->uptime)."',
					updated='".addslashes($ctime)."'
					WHERE uid='".addslashes($this->uid)."'";
		} else {
			$sql = "UPDATE ".$xoopsDB->prefix($mydirname.'_config')." SET
					blogtype='".$this->blogtype."',
					blogurl='".$this->blogurl."',
					rss='".$this->rss."',
					openarea='".$this->openarea."',
					mailpost='".$this->mailpost."',
					address='".$this->address."',
					keep='".$this->keep."',
					uptime='".$this->uptime."',
					updated='".$ctime."'
					WHERE uid='".$this->uid."'";
		}
		if ($force == true) {
			$result = $xoopsDB->queryF($sql);
		} else {
			$result = $xoopsDB->query($sql);
		}
		return $this->uid;
	}

}
?>
