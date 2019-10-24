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

// EQdkp required files/vars
define('EQDKP_INC', true);
define('IN_ADMIN', true);
define('PLUGIN', 'guildrequest');

$eqdkp_root_path = './../../../';
include_once($eqdkp_root_path.'common.php');


/*+----------------------------------------------------------------------------
  | guildrequestForm
  +--------------------------------------------------------------------------*/
class guildrequestForm extends page_generic {

	/**
	* Constructor
	*/
	public function __construct(){
		// plugin installed?
		if (!$this->pm->check('guildrequest', PLUGIN_INSTALLED))
			message_die($this->user->lang('gr_plugin_not_installed'));

		$handler = array(
			'save' => array('process' => 'save', 'csrf' => true, 'check' => 'a_guildrequest_manage'),
		);
		parent::__construct('a_guildrequest_form', $handler, array('guildrequest_fields', 'name'), null, 'field_ids[]');

		$this->process();
	}

	/**
	* save
	* Save the configuration
	*/
	public function save(){
		if (count($this->in->getArray('field', 'string')) > 0){
			//Truncate field table
			$this->pdh->put('guildrequest_fields', 'truncate', array());

			$id = 0;
			foreach($this->in->getArray('field', 'string') as $val){
				if ($val['name'] == '') continue;

				$strType = $val['type'];
				$strName = $val['name'];
				$strHelp = $val['help'];
				if (isset($val['options']) && $val['options'] != '') {
					$arrOptions = explode("\n", $val['options']);
				} else {
					$arrOptions = array();
				}
				$intSortID = $id;
				$intRequired = (isset($val['required']) && (int)$val['required']) ? 1 : 0;
				$intInList = (isset($val['in_list']) && (int)$val['in_list']) ? 1 : 0;
				$strDepField = $val['dep_field'];
				$strDepValue = $val['dep_value'];

				$this->pdh->put('guildrequest_fields', 'add', array($val['id'], $strType, $strName, $strHelp, $arrOptions, $intSortID, $intRequired, $intInList, $strDepField, $strDepValue));
				$id++;
			}
		}
		$this->pdh->process_hook_queue();
		$this->pdc->del_prefix('hptt_guildrequest');

		// Success message
		$this->core->message($this->user->lang('pk_succ_saved'), $this->user->lang('success'), 'green');
		$this->display($messages);
	}

	/**
	* display
	* Display the page
	*
	* @param    array  $messages   Array of Messages to output
	*/
	public function display(){

		$this->tpl->add_js("
		$(\"#gr_form_table tbody\").sortable({
			cancel: '.not-sortable, input, .input, select',
			cursor: 'pointer',
		});
		", "docready");

		$this->confirm_delete($this->user->lang('gr_confirm_delete_field'));
		$this->jquery->selectall_checkbox('selall_fields', 'field_ids[]');

		$arrFields = $this->pdh->get('guildrequest_fields', 'id_list', array());
		$arrDeps[0] = "";
		$arrDeps[999999999] = "__Custom";
		foreach($arrFields as $id){
			$type =  $this->pdh->get('guildrequest_fields', 'type', array($id));
			if ($type == 2 || $type == 5 || $type==6) $arrDeps[$id] = $this->pdh->get('guildrequest_fields', 'name', array($id));
		}

		foreach($arrFields as $id){
			$row = $this->pdh->get('guildrequest_fields', 'id', array($id));
			$row['options'] = unserialize($row['options']);

			$this->tpl->assign_block_vars('field_row', array(
				'KEY'				=> $row['id'],
				'NAME'				=> $row['name'],
				'HELP'				=> $row['help'],
				'TYP_DD'			=> new hdropdown('field['.$row['id'].'][type]', array('options' => $this->user->lang('gr_types'), 'value' => $row['type'], 'class' => 'gr_type', 'js' => 'onchange="type_change_listener(this)"')),
				'OPTIONS_DISABLED'	=> ($row['type'] != 2 && $row['type'] != 5 && $row['type'] != 6) ? 'disabled="disabled"' : '',
				'HELP_DISABLED'		=> ($row['type'] == 3 || $row['type'] == 4) ? 'disabled="disabled"' : '',
				'OPTIONS_HEIGHT'	=> ($row['type'] != 2 && $row['type'] != 5 && $row['type'] != 6) ? '20' : '60',
				'OPTIONS'			=> (count($row['options'])) ? implode("\n", $row['options']) : '',
				'REQUIRED'			=> ($row['required']) ? 'checked="checked"' : '',
				'IN_LIST'			=> ($row['in_list']) ? 'checked="checked"' : '',
				'DEP_VALUE'			=> $row['dep_value'],
				'DEP_DD'			=> new hdropdown('field['.$row['id'].'][dep_field]', array('options' => $arrDeps, 'value' => $row['dep_field'], 'class' => 'gr_dep_field')),
			));
		}
		
		$this->tpl->assign_vars(array(
			'KEY'		=> max($arrFields)+1,
			'TYP_DD'	=> new hdropdown('field[KEY][type]', array('options' => $this->user->lang('gr_types'), 'class' => 'gr_type', 'js' => 'onchange="type_change_listener(this)"')),
		));
		
		// -- EQDKP ---------------------------------------------------------------
		$this->core->set_vars(array(
			'page_title'		=> $this->user->lang('guildrequest').' '.$this->user->lang('gr_manage_form'),
			'template_path'		=> $this->pm->get_data('guildrequest', 'template_path'),
			'template_file'		=> 'admin/form.html',
			'display'			=> true
		));
	}
}
registry::register('guildrequestForm');
?>