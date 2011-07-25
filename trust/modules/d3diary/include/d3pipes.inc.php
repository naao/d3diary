<?php
/*
 * Created on 2009/06/09 by naao http://www.naaon.com/
 * $Id: d3pipes.inc.php,v 0.01 2009/06/09 naao Exp $
 */
 
require_once XOOPS_TRUST_PATH . '/modules/d3pipes/joints/D3pipesBlockAbstract.class.php' ;
 
class D3pipesBlockD3diarylistSubstance extends D3pipesBlockAbstract {
 
    var $target_dirname = '' ;
    var $trustdirname = 'd3diary' ;
 
	function init()
	{
		// language files
		$this->includeLanguageBlock() ;

        	// parse and check option for this class
        	$params = array_map( 'trim' , explode( '|' , $this->option ));
        	if(empty($params[0])) {
        	    $this->errors[] = "pipe_error_\n($this->pipe_id)" ;
        	    return false ;
        	}
        	$mydirname = $this->target_dirname = preg_replace('/[^0-9a-zA-Z_-]/', '', $params[0]);

		// configurations (file, name, block_options)
		$this->func_file = XOOPS_ROOT_PATH.'/modules/'.$this->target_dirname.'/blocks/blocks.php' ;
		$this->func_name = 'b_d3diary_list_show' ;

        	$this->block_options = array(
            		'disable_renderer' => true,
            		0 => $this->target_dirname, // mydirname of d3blog
            		1 => empty($params[1]) ? 10 : intval( $params[1] ), // number of entries to show
			2 => 'time' , // order by
			3 => empty( $params[2] ) ? 0 : intval( $params[2] ) , // use_detail
            		4 => empty($params[3]) ? 80 : intval( $params[3] ), // max text length
            		6 => empty( $params[4] ) ? 3 : intval( $params[4] ), // max_entryby_person
        	);
		return true ;
	}
 
	function reassign( $data )
	{
		$myts =& MyTextSanitizer::getInstance();
		$entries = array() ;
		foreach( $data['entry'] as $item ) {
				$ctime = preg_split("/[-: ]/", $item['create_time']);
				$yd_timestamp = mktime($ctime[3],$ctime[4],$ctime[5],$ctime[1],$ctime[2],$ctime[0]);
			$entry = array(
				'pubtime'=> $yd_timestamp ,
				'link' =>  $item['url'] ,
				//'headline' => $this->unhtmlspecialchars( $item['title'] ) ,
				'headline' => $item['title'] ,
				'description' => strip_tags($item['diary'] ) ,
				'allow_html' => true ,
			) ;
			$entry['fingerprint'] = $entry['link'] ;
			$entries[] = $entry ;
		}

		return $entries ;
	}
 
	function renderOptions($index, $current_value = null)
	{
		global $xoopsConfig;
		require_once dirname(dirname(__FILE__)).'/language/'.$xoopsConfig['language'].'/admin.php' ;

        	$index = intval($index);
        	$options = explode('|', $current_value);

        	// options[0]  (dirname)
        	$dirnames = $this->getValidDirnames() ;
        	$ret_0 = '<select name="joint_options['.$index.'][0]">' ;
        	foreach($dirnames as $dirname) {
            	$ret_0 .= '<option value="'.$dirname.'" '.($dirname==@$options[0]?'selected="selected"':'').'>'.$dirname.'</option>' ;
        	}
        	$ret_0 .= '</select>' ;
        	
        	// options[1]  (max_entries)
        	$options[1] = empty($options[1]) ? 10 : intval($options[1]);
        	$ret_1 = _MD_D3PIPES_N4J_MAXENTRIES.'<input type="text" name="joint_options['.$index.'][1]" value="'.$options[1].'" size="2" style="text-align:right;" />' ;

        	// options[2]  (show description)
        	$options[2] = empty($options[2]) ? 0 : intval($options[2]);
		if( $options[2] >0 ) {
			$discyes_checked = 'checked="checked"' ;
			$discno_checked  = '' ;
		} else {
			$discyes_checked = '' ;
			$discno_checked = 'checked="checked"' ;
		}

        	$ret_2 = _MD_D3PIPES_N4J_WITHDESCRIPTION.'<input type="radio" name="joint_options['.$index.'][2]" value="1" '
        		.$discyes_checked.' /><label for="o20">YES</label>
        		<input type="radio" name="joint_options['.$index.'][2]" value="0" '
        		.$discno_checked.' /><label for="o41">NO</label>';

        	// options[3]  (max_entries)
        	$options[3] = empty($options[3]) ? 80 : intval($options[3]);
        	$ret_3 = _MD_D3DIARY_MAXTEXT.'<input type="text" name="joint_options['.$index.'][3]" value="'.$options[3].'" size="6" style="text-align:right;" />' ;

        	// options[4]  (max_entries)
        	$options[4] = empty($options[4]) ? 3 : intval($options[4]);
        	$ret_4 = _MD_D3DIARY_DISPLAY_PERSONAL.'<input type="text" name="joint_options['.$index.'][4]" value="'.$options[4].'" size="2" style="text-align:right;" />' ;


        return '<input type="hidden" name="joint_option['.$index.']" id="joint_option_'.$index.'" value="" />'.$ret_0.' '.$ret_1.' '.$ret_2.' '.$ret_3.' '.$ret_4 ;

	}
}

?>