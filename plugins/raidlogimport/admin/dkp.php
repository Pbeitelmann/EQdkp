<?php
/*	Project:	EQdkp-Plus
 *	Package:	RaidLogImport Plugin
 *	Link:		http://eqdkp-plus.eu
 *
 *	Copyright (C) 2006-2016 EQdkp-Plus Developer Team
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
$eqdkp_root_path = './../../../';
include_once('./../includes/common.php');

class rli_import extends page_generic {
	public static function __shortcuts() {
		$shortcuts = array('user', 'rli', 'in', 'tpl', 'core', 'pm', 'config', 'jquery', 'db',
			'adj'		=> 'rli_adjustment',
			'item'		=> 'rli_item',
			'member'	=> 'rli_member',
			'parser'	=> 'rli_parse',
			'raid'		=> 'rli_raid',
		);
		return array_merge(parent::$shortcuts, $shortcuts);
	}

	public function __construct() {
		$this->user->check_auth('a_raidlogimport_dkp');
		
		$handler = array(
			'checkraid'	=> array('process' => 'process_raids'),
			'checkmem'	=> array('process' => 'process_members'),
			'checkitem'	=> array('process' => 'process_items'),
			'save_itempools' => array('process' => 'itempool_save'),
			'checkadj'	=> array('process' => 'process_adjustments'),
			'viewall'	=> array('process' => 'process_views'),
			'insert'	=> array('process' => 'insert_log'),
			'eventvalue'=> array('process'	=> 'ajax_eventvalue'),
		);
		parent::__construct(false, $handler);
		// save template state to return to if errors occur
		$this->tpl->save_state('rli_start');
		$this->process();
	}
	
	public function ajax_eventvalue(){
		header('content-type: text/html; charset=UTF-8');
		
		$event_id = $this->in->get('event', 0);
		$event_value = $this->pdh->geth("event", "value", array($event_id));
		echo runden($event_value);
		die();
	}
	
	private function process_error($main_process) {
		if($this->rli->error_check()) {
			$error = $this->rli->get_error();
			if(!empty($error['process']) && $error['process'] != $main_process) {
				$this->tpl->load_state('rli_start');
			}
			if(is_array($error['messages'])) {
				$this->core->messages($error['messages']);
			}
			if(!empty($error['process']) && $error['process'] != $main_process) {
				$this->{$error['process']}(false);
			}
		}
	}

	public function process_raids($error_out=true) {

		if($this->in->get('checkraid') == 'submit') {
			$parser = ($this->in->exists('parser')) ? $this->in->get('parser') : $this->rli->config('parser');
			$this->rli->flush_cache();
			if($this->in->exists('log') && $parser != 'empty') {
				$log = trim(str_replace("&", "and", stripslashes(html_entity_decode($_POST['log']))));
				$log = (is_utf8($log)) ? $log : utf8_encode($log);
				$this->parser->parse_string($log, $parser);
			}
			$this->rli->add_cache_data('progress', 'members');
		}
		$this->raid->add_new($this->in->get('raid_add', 0));

		if($this->in->get('checkraid') == 'recalc') {
			$this->raid->recalc();
		}

		$this->raid->display(true);

		$this->tpl->assign_vars(array(
			'USE_TIMEDKP' => ($this->rli->config('use_dkp') & 2),
			'USE_BOSSDKP' => ($this->rli->config('use_dkp') & 1),
			'USE_EVENTDKP' => ($this->rli->config('use_dkp') & 4))
		);
		//language
		lang2tpl();
		
		// error processing
		if($error_out) $this->process_error('process_raids');
		$this->rli->nav('raids');

		$this->core->set_vars(array(
			'page_title'        => $this->user->lang('rli_check_data'),
			'template_path'     => $this->pm->get_data('raidlogimport', 'template_path'),
			'template_file'     => 'raids.html',
			'display'           => true)
		);
	}

	public function process_members($error_out=true) {
		$this->member->add_new($this->in->get('members_add', 0));

		//display members
		$this->member->display(true);

		// show raids
		$this->raid->display();

		//language
		lang2tpl();
		
		// error processing
		if($error_out) $this->process_error('process_members');
		$this->rli->nav('members');

		$this->tpl->assign_vars(array(
			'S_ATT_BEGIN'	 => ($this->rli->config('attendance_begin') > 0 AND !$this->rli->config('attendance_raid')) ? TRUE : FALSE,
			'S_ATT_END'		 => ($this->rli->config('attendance_end') > 0 AND !$this->rli->config('attendance_raid')) ? TRUE : FALSE,
			'MEMBER_DISPLAY' => ($this->rli->config('member_display') == 1) ? $this->raid->th_raidlist : false,
			'RAIDCOUNT'		 => ($this->rli->config('member_display') == 1) ? $this->raid->count() : 1,
			'RAIDCOUNT3'	 => ($this->rli->config('member_display') == 1) ? $this->raid->count()+2 : 3,
			'DETAIL_RAIDLIST' =>($this->rli->config('member_display') == 2 && extension_loaded('gd')) ? true : false)
		);

		$this->core->set_vars(array(
			'page_title'        => $this->user->lang('rli_check_data'),
			'template_path'     => $this->pm->get_data('raidlogimport', 'template_path'),
			'template_file'     => 'members.html',
			'display'           => true)
		);
	}

	public function process_items($error_out=true) {
		$this->item->add_new($this->in->get('items_add', 0));
		$this->member->display();
		$this->raid->display();
		$this->item->display(true);
		
		$this->tpl->assign_vars(array(
			'S_ATT_BEGIN'	=> ($this->rli->config('attendance_begin') > 0 AND !$this->rli->config('attendance_raid')) ? true : false,
			'S_ATT_END'		=> ($this->rli->config('attendance_end') > 0 AND !$this->rli->config('attendance_raid')) ? true : false)
		);

		//language
		lang2tpl();
		
		// error processing
		if($error_out) $this->process_error('process_items');
		$this->rli->nav('items');
		
		$this->core->set_vars(array(
			'page_title'        => $this->user->lang('rli_check_data'),
			'template_path'     => $this->pm->get_data('raidlogimport', 'template_path'),
			'template_file'     => 'items.html',
			'display'           => true)
		);
	}
	
	public function itempool_save() {
		$this->item->save_itempools();
		$this->process_items();
	}

	public function process_adjustments($error_out=true) {
		$this->adj->add_new($this->in->get('adjs_add', 0));

		$this->member->display();
		$this->raid->display();
		$this->item->display();
		$this->adj->display(true);

		$this->tpl->assign_vars(array(
			'S_ATT_BEGIN'	=> ($this->rli->config('attendance_begin') > 0 AND !$this->rli->config('attendance_raid')) ? true : false,
			'S_ATT_END'		=> ($this->rli->config('attendance_end') > 0 AND !$this->rli->config('attendance_raid')) ? true : false)
		);

		//language
		lang2tpl();
		
		// error processing
		if($error_out) $this->process_error('process_adjustments');
		$this->rli->nav('adjustments');
		
		$this->core->set_vars(array(
			'page_title'        => $this->user->lang('rli_check_data'),
			'template_path'     => $this->pm->get_data('raidlogimport', 'template_path'),
			'template_file'     => 'adjustments.html',
			'display'           => true)
		);
	}
	
	public function process_views() {
			
		$this->member->display();
		$this->raid->display();
		$this->item->display();
		$this->adj->display();
		
		//language
		lang2tpl();
		
		// error processing
		if($error_out) $this->process_error('process_views');
		$this->rli->nav('viewall');
		
		$this->core->set_vars(array(
			'page_title'        => $this->user->lang('rli_check_data'),
			'template_path'     => $this->pm->get_data('raidlogimport', 'template_path'),
			'template_file'     => 'viewall.html',
			'display'           => true)
		);
	}

	public function insert_log() {
		$message = array();
		$bools = $this->rli->check_data();
		if(!in_array('miss', $bools) AND !in_array(false, $bools)) {
			#$this->db->beginTransaction();
			$this->member->insert();
			$this->raid->insert();
			$this->item->insert();
			if(!$this->rli->config('deactivate_adj')) $this->adj->insert();
			$this->process_error('insert_log');
			$this->rli->process_pdh_queue();
			$this->pdh->process_hook_queue();
			$this->rli->flush_cache();
	
			$this->rli->nav('finish');
			$this->core->set_vars(array(
				'page_title'        => $this->user->lang('rli_imp_suc'),
				'template_path'     => $this->pm->get_data('raidlogimport', 'template_path'),
				'template_file'     => 'finish.html',
				'display'           => true)
			);
		} else {
			unset($_POST);
			$check = $this->user->lang('rli_missing_values').'<br />';
			foreach($bools['false'] as $loc => $la) {
				if($la == 'miss') {
					$check .= $this->user->lang('rli_'.$loc.'_needed');
				}
				$check .= '<input type="submit" name="check'.$loc.'" value="'.$this->user->lang('rli_check'.$loc).'" class="mainoption" /><br />';
			}
			$this->tpl->assign_vars(array(
				'L_NO_IMP_SUC'	=> $this->user->lang('rli_imp_no_suc'),
				'CHECK'			=> $check)
			);
			$this->rli->nav('finish');
			$this->core->set_vars(array(
				'page_title'		=> $this->user->lang('rli_imp_no_suc'),
				'template_path'		=> $this->pm->get_data('raidlogimport', 'template_path'),
				'template_file'		=> 'check_input.html',
				'display'			=> true,
				)
			);
		}
	}

	public function display($messages=array()) {
		if($messages) {
			foreach($messages as $title => $message) {
				$type = ($title == 'rli_error' or $title == 'rli_no_mem_create') ? 'red' : 'green';
				if(is_array($message)) {
					$message = implode(',<br />', $message);
				}
				$this->core->message($message, $this->user->lang($title).':', $type);
			}
		}
		$this->tpl->assign_vars(array(
			'L_DATA_SOURCE'	 => $this->user->lang('rli_data_source'),
			'L_CONTINUE_OLD' => $this->user->lang('rli_continue_old'),
			'L_INSERT'		 => $this->user->lang('rli_dkp_insert'),
			'L_SEND'		 => $this->user->lang('rli_send'),
			'DISABLED'		 => ($this->rli->data_available()) ? '' : 'disabled="disabled"',
			'PARSER_DD'		 => new hdropdown('parser', array('options' => getAvailableParsers(), 'value' => $this->rli->config('parser'))),
			'S_STEP1'        => true)
		);
		
		$this->tpl->add_js("\$('#show_log_form').click(function() {\$('#log_form').show(200)});",'docready');

		$this->core->set_vars(array(
			'page_title'        => $this->user->lang('rli_data_source'),
			'template_path'     => $this->pm->get_data('raidlogimport', 'template_path'),
			'template_file'     => 'log_insert.html',
			'display'           => true,
			)
		);
	}
}
registry::register('rli_import');
?>