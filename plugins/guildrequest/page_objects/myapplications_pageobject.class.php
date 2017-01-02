<?php
/*	Project:	EQdkp-Plus
 *	Package:	GuildRequest Plugin
 *	Link:		http://eqdkp-plus.eu
 *
 *	Copyright (C) 2006-2015 EQdkp-Plus Developer Team
 *
 *	This program is free software: you can redistribute it and/or modify
 *	it under the terms of the GNU Affero General Public License as published
 *	by the Free Software Foundation, either version 3 of the License, or
 *	(at your option) any later version.
 *
 *	This program is distributed in the hope that it will be useful,
 *	but WITHOUT ANY WARRANTY; without even the implied warranty of
 *	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *	GNU Affero General Public License for more details.
 *
 *	You should have received a copy of the GNU Affero General Public License
 *	along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

class myapplications_pageobject extends pageobject
{
  /**
   * __dependencies
   * Get module dependencies
   */
  public static function __shortcuts()
  {
    $shortcuts = array('email' => 'MyMailer');
    return array_merge(parent::__shortcuts(), $shortcuts);
  }
  
  /**
   * Constructor
   */
  public function __construct()
  {
    // plugin installed?
    if (!$this->pm->check('guildrequest', PLUGIN_INSTALLED))
      message_die($this->user->lang('gr_plugin_not_installed'));
	
	$this->user->check_auth('u_guildrequest_add');
	if(!$this->user->is_signedin()) $this->user->check_auth('u_something');
	
    $handler = array(
		'mark_all_read' => array('process' => 'mark_all_read', 'csrf' => 'true'),
    );
    parent::__construct(false, $handler, array('guildrequest_requests', 'username'), null, 'gr[]');

    $this->process();
  }
	
	public function mark_all_read(){
		$arrApplicationIDs = $this->pdh->get('guildrequest_requests', 'id_list', array());
		foreach($arrApplicationIDs as $intID){
			$this->pdh->put('guildrequest_visits', 'add', array($intID));
		}
		$this->pdh->process_hook_queue();
	}

  
  public function display()
  {
	//Output
	$hptt_page_settings	= array (
  'name' => 'hptt_guildrequest',
  'table_main_sub' => '%request_id%',
  'table_subs' => array('%request_id%', '%field_id%'),
  'page_ref' => 'myapplications.php',
  'show_numbers' => false,
  'show_select_boxes' => false,
  'show_detail_twink' => false,
  'table_sort_col' => 0,
  'table_sort_dir' => 'asc',
  'table_presets' => 
  array (
	    0 => 
	    array (
	      'name' => 'gr_date',
	      'sort' => true,
	      'th_add' => '',
	      'td_add' => 'nowrap="nowrap"',
	    ),
  		1 =>
  		array (
  				'name' => 'gr_name',
  				'sort' => true,
  				'th_add' => 'width="50%"',
  				'td_add' => 'nowrap="nowrap"',
  		),
  ),
);
	

	$arrCombinedFields = $this->pdh->get('guildrequest_fields', 'combined_fields', array());

	foreach($arrCombinedFields  as $key => $val){
		if (count($val) == 1){
			$hptt_page_settings['table_presets'][] = array(
				'name' 	=> 'gr_field_'.$val[0],
				'sort' 	=> true,
				'th_add'	=> '',
				'td_add'	=> '',
			);
		} else {
			$hptt_page_settings['table_presets'][] = array(
				'name' 	=> 'gr_combined_field_'.$key,
				'sort' 	=> true,
				'th_add'	=> '',
				'td_add'	=> '',
			);
		}
	}

	
	$hptt_page_settings['table_presets'][] = array(
	'name' => 'gr_status',
      'sort' => true,
      'th_add' => '',
      'td_add' => '',
	);
	$hptt_page_settings['table_presets'][] = array(
	'name' => 'gr_closed',
      'sort' => true,
      'th_add' => '',
      'td_add' => 'align="center"',
	);

		//Sort
		$sort			= $this->in->get('sort');
		$sort_suffix	= '&amp;sort='.$sort;

		$start				= $this->in->get('start', 0);
		$pagination_suffix	= ($start) ? '&amp;start='.$start : '';

	$view_list = $this->pdh->get('guildrequest_requests', 'id_list', array($this->user->id, !$this->user->check_auth('u_guildrequest_view_closed', false)));
	
	$hptt				= $this->get_hptt($hptt_page_settings, $view_list, $view_list, array('%link_url%' => 'viewraid.php', '%link_url_suffix%' => ''), $this->user->id);
	$hptt->setPageRef($this->strPath);
	
	//footer
	$raid_count			= count($view_list);
	$footer_text		= sprintf($this->user->lang('gr_footer'), $raid_count ,$this->user->data['user_rlimit']);
	

	$this->tpl->assign_vars(array (
		'PAGE_OUT'			=> $hptt->get_html_table($sort, $pagination_suffix, $start, $this->user->data['user_rlimit'], $footer_text),
		'GR_PAGINATION'		=> generate_pagination($this->strPath.$this->SID.$sort_suffix, $raid_count, $this->user->data['user_rlimit'], $start),
	));
	
	
	$this->core->set_vars(array (
      'page_title'    => $this->user->lang('gr_myapplications'),
      'template_path' => $this->pm->get_data('guildrequest', 'template_path'),
      'template_file' => 'myapplications.html',
      'display'       => true
    ));
  }
  
}
?>