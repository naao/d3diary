<?php

require_once XOOPS_TRUST_PATH . '/modules/d3pipes/joints/D3pipesBlockAbstract.class.php' ;

class D3pipesBlockD3diaryd3comSubstance extends D3pipesBlockAbstract {

	var $target_dirname = '' ;
	var $trustdirname = 'd3diary' ;

	function init()
	{
		// parse and check option for this class
		$params = array_map( 'trim' , explode( '|' , $this->option ) ) ;
		if( empty( $params[0] ) ) {
			$this->errors[] = _MD_D3PIPES_ERR_INVALIDDIRNAMEINBLOCK."\n($this->pipe_id)" ;
			return false ;
		}
		$this->target_dirname = preg_replace( '/[^0-9a-zA-Z_-]/' , '' , $params[0] ) ;

			// configurations (file, name, block_options)
			$this->func_file = XOOPS_ROOT_PATH.'/modules/'.$this->target_dirname.'/blocks/blocks.php' ;
		if (intval( $params[3] )>0) {
			$this->func_name = 'b_d3diary_d3comlist_posts_show' ;
			$this->block_options = array(
				'disable_renderer' => true ,
				0 => $this->target_dirname , // mydirname of pico
				1 => empty( $params[1] ) ? 10 : intval( $params[1] ) , // max_entries
				2 => 'time' , // order by
				3 => empty( $params[2] ) ? 0 : intval( $params[2] ) , // request_uid
				4 => empty( $params[4] ) ? 0 : intval( $params[4] ) , // use_detail
				5 => '' , //template
				6 => empty( $params[5] ) ? 10 : intval( $params[5] ) , // use_aggregation
				7 => preg_replace( '/[^0-9,]/' , '' , @$params[6] ) , // category limitation
				8 => preg_replace( '/[^0-9,]/' , '' , @$params[7] ) , // forum limitation
			) ;
		} else {
			$this->func_name = 'b_d3diary_d3comlist_topics_show' ;
			$this->block_options = array(
				'disable_renderer' => true ,
				0 => $this->target_dirname , // mydirname of pico
				1 => empty( $params[1] ) ? 10 : intval( $params[1] ) , // max_entries
				2 => 0 , // ishow fullsize
				3 => 'time' , // order by
				4 => empty( $params[2] ) ? 0 : intval( $params[2] ) , // request_uid
				5 => '' , // ismarkup
				6 => empty( $params[4] ) ? 0 : intval( $params[4] ) , // use_detail
				7 => '' , //template
				8 => empty( $params[5] ) ? 10 : intval( $params[5] ) , // use_aggregation
				9 => preg_replace( '/[^0-9,]/' , '' , @$params[6] ) , // category limitation
				10 => preg_replace( '/[^0-9,]/' , '' , @$params[7] ) , // forum limitation
			) ;
		}

		return true ;
	}

	function reassign( $data )
	{
		$data = $this->unhtmlspecialchars( $data ) ; // d3 modules has a rule assigning escaped variables

		$entries = array() ;
		if(!empty($data['topics'])){
		     foreach( $data['topics'] as $topic ) {
		     	if($data['com_forum_id'] == $topic['forum_id']) {
		     		$link_url = XOOPS_URL.'/modules/'.$data['mydirname'].'/index.php?page=detail&bid='.$topic['link_id'].'#post_id'.$topic['last_post_id'];
		     	} else {
		     		$link_url = $data['mod_url'].'/index.php?topic_id='.$topic['id'].'#post_id'.$topic['last_post_id'];
		     	}
		     	
			$entry = array(
				'pubtime' => $topic['last_post_time'] , // timestamp
				'link' => $link_url ,
				//'headline' => $topic['title'] ,
				'headline' => '['.$topic['forum_title'].'] '.$topic['title'] ,
				'description' => $topic['post_text'] ,
			) ;
			$entry['fingerprint'] = $entry['link'] ;
			$entries[] = $entry ;
		    }
		} elseif(!empty($data['posts'])) {
		     foreach( $data['posts'] as $post ) {
		     	if($data['com_forum_id'] == $post['forum_id']) {
		     		$link_url = XOOPS_URL.'/modules/'.$data['mydirname'].'/index.php?page=detail&bid='.$post['link_id'].'#post_id'.$post['id'];
		     	} else {
		     		$link_url = $data['mod_url'].'/index.php?post_id='.$post['id'];
		     	}

			$entry = array(
				'pubtime' => $post['post_time'] , // timestamp
				'link' => $link_url ,
				//'headline' => $post['subject'] ,
				'headline' => '['.$post['forum_title'].'] '.$post['subject'] ,
				'description' => $post['post_text'] ,
			) ;
			$entry['fingerprint'] = $entry['link'] ;
			$entries[] = $entry ;
		    }
		}

		return $entries ;
	}

	function renderOptions( $index , $current_value = null )
	{
		global $xoopsConfig;
		require_once dirname(dirname(__FILE__)).'/language/'.$xoopsConfig['language'].'/admin.php' ;
	
		$index = intval( $index ) ;
		$options = explode( '|' , $current_value ) ;


		// options[0]  (dirname)
		$dirnames = $this->getValidDirnames() ;
		$ret_0 = '<select name="joint_options['.$index.'][0]">' ;
		foreach( $dirnames as $dirname ) {
			$ret_0 .= '<option value="'.$dirname.'" '.($dirname==@$options[0]?'selected="selected"':'').'>'.$dirname.'</option>' ;
		}
		$ret_0 .= '</select>' ;

        	// options[1]  (max_entries)
        	$options[1] = empty($options[1]) ? 5 : intval($options[1]);
        	$ret_1 = _MD_D3PIPES_N4J_MAXENTRIES.'<input type="text" name="joint_options['.$index.'][1]" value="'.$options[1].'" size="2" style="text-align:right;" />' ;

        	// options[2]  (blogger_id)
        	$options[2] = empty($options[2]) ? 0 : intval($options[2]);
        	$ret_2 = 'blogger_id <input type="text" name="joint_options['.$index.'][2]" value="'.$options[2].'" size="2" style="text-align:right;" />' ;

        	// options[3]  (show topics or posts)
        	$options[3] = empty($options[3]) ? 0 : intval($options[3]);
		if( $options[3] >0 ) {
			$topics_checked = '' ;
			$posts_checked  = 'checked="checked"' ;
		} else {
			$topics_checked = 'checked="checked"' ;
			$posts_checked = '' ;
		}

        	$ret_3 = _MD_D3DIARY_TOPICSPOSTS
        		.'<input type="radio" name="joint_options['.$index.'][3]" value="0" '
        		.$topics_checked.' /><label for="o30">topics</label>
        		<input type="radio" name="joint_options['.$index.'][3]" value="1" '
        		.$posts_checked.' /><label for="o31">posts</label>';

        	// options[4]  (show description)
        	$options[4] = empty($options[4]) ? 0 : intval($options[4]);
		if( $options[4] >0 ) {
			$discyes_checked = 'checked="checked"' ;
			$discno_checked  = '' ;
		} else {
			$discyes_checked = '' ;
			$discno_checked = 'checked="checked"' ;
		}

        	$ret_4 = _MD_D3PIPES_N4J_WITHDESCRIPTION.'<input type="radio" name="joint_options['.$index.'][4]" value="1" '
        		.$discyes_checked.' /><label for="o40">YES</label>
        		<input type="radio" name="joint_options['.$index.'][4]" value="0" '
        		.$discno_checked.' /><label for="o41">NO</label>';

        	// options[5]  (forum aggrefaton)
        	$options[5] = empty($options[5]) ? 0 : intval($options[5]);
		if( $options[5] >0 ) {
			$aggreyes_checked = 'checked="checked"' ;
			$aggreno_checked  = '' ;
		} else {
			$aggreyes_checked = '' ;
			$aggreno_checked = 'checked="checked"' ;
		}

        	$ret_5 = _MD_D3DIARY_USEAGGRE
        		.'<input type="radio" name="joint_options['.$index.'][5]" value="1" '
        		.$aggreyes_checked.' /><label for="o50">YES</label>
        		<input type="radio" name="joint_options['.$index.'][5]" value="0" '
        		.$aggreno_checked.' /><label for="o51">NO</label>';

		// options[6]  (cat_ids)
		$options[6] = preg_replace( '/[^0-9,]/' , '' , @$options[6] ) ;
		$ret_6 = _MD_D3DIARY_CATLIMIT.'<input type="text" name="joint_options['.$index.'][6]" value="'.$options[6].'" size="8" />' ;

		// options[7]  (forum_ids)
		$options[7] = preg_replace( '/[^0-9,]/' , '' , @$options[7] ) ;
		$ret_7 = _MD_D3DIARY_FORUMLIMIT.'<input type="text" name="joint_options['.$index.'][7]" value="'.$options[7].'" size="8" />' ;

		return '<input type="hidden" name="joint_option['.$index.']" id="joint_option_'.$index.'" value="" />'.$ret_0.' '.$ret_1.'&nbsp;'.$ret_2.'<br />'.$ret_3.'<br />'.$ret_4.'<br />'.$ret_5.'&nbsp;'.$ret_6.'&nbsp;'.$ret_7 ;

	}

}

?>