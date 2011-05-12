<?php
include_once XOOPS_ROOT_PATH."/class/xoopstree.php";

class Category
{
	var $uid;
	var $cid;
	var $cname;
	var $corder;
	var $subcat;
	var $children;
	var $parent;
	var $blogtype;
	var $blogurl;
	var $rss;
	var $openarea;
	var $dohtml;
	var $vgids;
	var $vpids;

	function Category(){
	}

    function &getInstance()
    {
        static $instance;
        if (!isset($instance)) {
            $instance = new Category();
        }
        return $instance;
    }

	function readdb($mydirname){
		global $xoopsDB;
	
		if($this->cid>0){
			$sql = "SELECT *
					  FROM ".$xoopsDB->prefix($mydirname.'_category')."
			          WHERE uid='".intval($this->uid)."' and cid='".intval($this->cid)."'";

			$result = $xoopsDB->query($sql);
			$num_rows = $xoopsDB->getRowsNum($result);

			while ( $dbdat = $xoopsDB->fetchArray($result) ) {
				$this->cname   = $dbdat['cname'];
				$this->corder   = $dbdat['corder'];
				$this->subcat   = $dbdat['subcat'];
				$this->blogtype   = $dbdat['blogtype'];
				$this->blogurl   = $dbdat['blogurl'];
				$this->rss   = $dbdat['rss'];
				$this->openarea   = $dbdat['openarea'];
				$this->dohtml   = $dbdat['dohtml'];
				$this->vgids   = $dbdat['vgids'];
				$this->vpids   = $dbdat['vpids'];
			}
		}else{
			$this->cname = constant('_MD_NOCNAME');
		}
	}

	function getchildren($mydirname){
		global $xoopsDB;
	
		if($this->uid>=0){
			$sql = "SELECT *
					  FROM ".$xoopsDB->prefix($mydirname.'_category')."
			          WHERE uid='".intval($this->uid)."' OR uid='0' ORDER BY corder";

			$result = $xoopsDB->query($sql);
			$num_rows = $xoopsDB->getRowsNum($result);

			$before = 0;
			$i=0;
			while ( $dbdat = $xoopsDB->fetchArray($result) ) {
				if (intval($dbdat['cid']) == intval($this->cid)){
					$this->cname   = $dbdat['cname'];
					$this->corder   = $dbdat['corder'];
					$this->subcat   = $dbdat['subcat'];
					$this->blogtype   = $dbdat['blogtype'];
					$this->blogurl   = $dbdat['blogurl'];
					$this->rss   = $dbdat['rss'];
					$this->openarea   = $dbdat['openarea'];
					$this->dohtml   = $dbdat['dohtml'];
					$this->vgids   = $dbdat['vgids'];
					$this->vpids   = $dbdat['vpids'];
					$this->children[0] = intval($this->cid);
					if(intval($dbdat['subcat'])==0){
						$before = $i;
					}
				} elseif ($this->children[0] && intval($dbdat['subcat'])==1 && $i==$before+1){
					$this->children[] = intval($dbdat['cid']);
					$before = $i;
				}
				$i++;
			}
			//var_dump($this->children);
		}else{
			$this->cname = constant('_MD_NOCNAME');
		}
	}

	function deletedb($mydirname){
		global $xoopsDB;

		$sql = "DELETE FROM ".$xoopsDB->prefix($mydirname.'_category')
			." WHERE uid=".$this->uid." and cid=".$this->cid;
			
		if($result = $xoopsDB->query($sql)){
			$sql = "DELETE FROM ".$xoopsDB->prefix($mydirname.'_newentry')
				." WHERE uid='".$this->uid."' and cid='".$this->cid."'";
			$result = $xoopsDB->query($sql);
		}

	}

	function insertdb($mydirname, $common_cat=0){
		global $xoopsDB;
		
		if($common_cat==1){
			$sql = "SELECT MAX(cid) as maxcid
				FROM ".$xoopsDB->prefix($mydirname.'_category')."
		          	WHERE uid='0' AND cid>'10000'";
		} else {
			$sql = "SELECT MAX(cid) as maxcid
				FROM ".$xoopsDB->prefix($mydirname.'_category')."
		          	WHERE uid='".$this->uid."' AND cid<='10000'";
		}
		$result = $xoopsDB->query($sql);
		$dbdat = $xoopsDB->fetchArray($result);
		
		if (!empty($dbdat['maxcid'])) {
			$this->cid=$dbdat['maxcid']+1;
		}else{
		    if($common_cat==1){
			$this->cid=10001;
		    } else {
			$this->cid=1;
		    }
		}


		if($common_cat==1){
			$sql = "SELECT MAX(corder) as maxcorder
				FROM ".$xoopsDB->prefix($mydirname.'_category')."
		          	WHERE uid='0' AND cid>'10000'";
		} else {
			$sql = "SELECT MAX(corder) as maxcorder
				FROM ".$xoopsDB->prefix($mydirname.'_category')."
		          	WHERE uid='".$this->uid."' AND cid<='10000'";
		}
		$result = $xoopsDB->query($sql);
		$dbdat = $xoopsDB->fetchArray($result);

		if (!empty($dbdat['maxcorder']) ) {
			$this->corder=$dbdat['maxcorder']+1;
		}else{
		    if($common_cat==1){
			$this->corder=10001;
		    } else {
			$this->corder=1;
		    }
		}

        if (!get_magic_quotes_gpc()) {
			$sql = "INSERT INTO ".$xoopsDB->prefix($mydirname.'_category')."
					(uid, cid, cname, corder, subcat, blogtype, blogurl, rss, openarea, dohtml, vgids, vpids)
					VALUES (
					'".addslashes($this->uid)."',
					'".addslashes($this->cid)."',
					'".addslashes($this->cname)."',
					'".addslashes($this->corder)."',
					'".addslashes($this->subcat)."',
					'".addslashes($this->blogtype)."',
					'".addslashes($this->blogurl)."',
					'".addslashes($this->rss)."',
					'".addslashes($this->openarea)."',
					'".addslashes($this->dohtml)."',
					'".addslashes($this->vgids)."',
					'".addslashes($this->vpids)."'
					)";
		} else {
			$sql = "INSERT INTO ".$xoopsDB->prefix($mydirname.'_category')."
					(uid, cid, cname, corder, subcat, blogtype, blogurl, rss, openarea, dohtml, vgids, vpids)
					VALUES (
					'".$this->uid."',
					'".$this->cid."',
					'".$this->cname."',
					'".$this->corder."',
					'".$this->subcat."',
					'".$this->blogtype."',
					'".$this->blogurl."',
					'".$this->rss."',
					'".$this->openarea."',
					'".$this->dohtml."',
					'".$this->vgids."',
					'".$this->vpids."'
					)";
		}
		$result = $xoopsDB->query($sql);
		return $this->cid;
	}

	function updatedb($mydirname){
		global $xoopsDB;

        if (!get_magic_quotes_gpc()) {
			$sql = "UPDATE ".$xoopsDB->prefix($mydirname.'_category')." SET
					cname='".addslashes($this->cname)."',
					corder='".addslashes($this->corder)."',
					subcat='".addslashes($this->subcat)."',
					blogtype='".addslashes($this->blogtype)."',
					blogurl='".addslashes($this->blogurl)."',
					rss='".addslashes($this->rss)."',
					openarea='".addslashes($this->openarea)."',
					dohtml='".addslashes($this->dohtml)."',
					vgids='".addslashes($this->vgids)."',
					vpids='".addslashes($this->vpids)."'
					WHERE uid=".addslashes($this->uid)." and cid=".addslashes($this->cid);
		} else {
			$sql = "UPDATE ".$xoopsDB->prefix($mydirname.'_category')." SET
					cname='".$this->cname."',
					corder='".$this->corder."',
					subcat='".$this->subcat."',
					blogtype='".$this->blogtype."',
					blogurl='".$this->blogurl."',
					rss='".$this->rss."',
					openarea='".$this->openarea."',
					dohtml='".$this->dohtml."',
					vgids='".$this->vgids."',
					vpids='".$this->vpids."'
					WHERE uid=".$this->uid." and cid=".$this->cid;
		}
		$result = $xoopsDB->query($sql);
	}

}
?>
