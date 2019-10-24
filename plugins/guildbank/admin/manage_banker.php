<?php
/*	Project:	EQdkp-Plus
 *	Package:	Guildbanker Plugin
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

define('EQDKP_INC', true);
define('IN_ADMIN', true);
define('PLUGIN', 'guildbank');

$eqdkp_root_path = './../../../';
include_once('./../includes/common.php');

class Manage_Banker extends page_generic {
	public static function __shortcuts() {
		$shortcuts = array('money' => 'gb_money');
		return array_merge(parent::$shortcuts, $shortcuts);
	}

	public function __construct(){
		$this->user->check_auth('a_guildbank_manage');
		$handler = array(
			'save'		=> array('process' => 'save',			'csrf'=>true),
		);
		parent::__construct(false, $handler, array('guildbank_banker', 'name'), null, 'banker_ids[]');
		$this->process();
	}

	public function save() {
		$retu = array();
		$bankers = $this->get_post();
		if($bankers) {
			$id_list = $this->pdh->get('guildbank_banker', 'id_list');
			foreach($bankers as $banker) {
				$func				= (in_array($banker['id'], $id_list)) ? 'update' : 'add';
				$money				= $this->money->input($banker, 'money_{ID}');
				$retu[]				= $this->pdh->put('guildbank_banker', $func, array($banker['id'], $banker['name'], $money, $banker['bankchar'], $banker['note']));
				$names[]			= $banker['name'];
			}
			if(in_array(false, $retu)) {
				$message = array('title' => $this->user->lang('save_nosuc'), 'text' => implode(', ', $names), 'color' => 'red');
			} elseif(in_array(true, $retu)) {
				$message = array('title' => $this->user->lang('save_suc'), 'text' => implode(', ', $names), 'color' => 'green');
			}
		}else{
			$message = array('title' => '', 'text' => $this->user->lang('no_calendars_selected'), 'color' => 'grey');
		}
		$this->display($message);
	}

	public function delete() {
		$banker_ids = $this->in->getArray('banker_ids', 'int');
		if($banker_ids) {
			foreach($banker_ids as $id) {
				$names[] = $this->pdh->get('guildbank_banker', 'name', ($id));
				$retu[] = $this->pdh->put('guildbank_banker', 'delete', array($id));
			}
			if(in_array(false, $retu)) {
				$message = array('title' => $this->user->lang('del_no_suc'), 'text' => implode(', ', $names), 'color' => 'red');
			} else {
				$message = array('title' => $this->user->lang('del_suc'), 'text' => implode(', ', $names), 'color' => 'green');
			}
		} else {
			$message = array('title' => '', 'text' => $this->user->lang('no_calendars_selected'), 'color' => 'grey');
		}
		$this->display($message);
	}

	public function display($messages=false) {
		if($messages) {
			$this->pdh->process_hook_queue();
			$this->core->messages($messages);
		}

		// bankchar
		$bankchars	= array(0 => '---') + $this->pdh->aget('member', 'name', 0, array($this->pdh->get('member', 'id_list')));
		$new_id		= 0;
		$order		= $this->in->get('order','0.0');
		$arrBanker	= $this->pdh->aget('guildbank_banker', 'name', 0, array($this->pdh->get('guildbank_banker', 'id_list')));
		if($order == '0.0') {
			arsort($arrBanker);
		} else {
			asort($arrBanker);
		}
		$key		= 0;
		$new_id		= 1;
		ksort($arrBanker);
		foreach($arrBanker as $id => $name) {
			$this->tpl->assign_block_vars('bankers', array(
				'KEY'			=> $key,
				'ID'			=> $id,
				'NAME'			=> $name,
				'DR_BANKCHAR'	=> new hdropdown('bankers['.$key.'][bankchar]', array('options' => $bankchars, 'value' => $this->pdh->get('guildbank_banker', 'bankchar', array($id, true)), 'id' => 'bankchar'.$key)),
				'MONEY'			=> $this->money->editfields($this->pdh->get('guildbank_transactions', 'money_summ', array($id)), 'bankers['.$key.'][money_{ID}]'),
				'NOTE'			=> $this->pdh->get('guildbank_banker', 'note', array($id)),
			));
			$key++;
			$new_id	= ($new_id == $id) ? $id+1 : $new_id;
		}
		$this->confirm_delete($this->user->lang('gb_confirm_delete_bankers'));

		$this->tpl->assign_vars(array(
			'SID'			=> $this->SID,
			'ID'			=> $new_id,
			'KEY'			=> $key,
			'DR_BANKCHAR'	=> new hdropdown('bankers['.$key.'][bankchar]', array('options' => $bankchars, 'id' => 'bankchar'.$key)),
			'MONEY'			=> $this->money->editfields(0, 'bankers['.$key.'][money_{ID}]'),
		));

		$this->core->set_vars(array(
			'page_title'		=> $this->user->lang('gb_manage_bankers'),
			'template_path'		=> $this->pm->get_data('guildbank', 'template_path'),
			'template_file'		=> 'admin/manage_banker.html',
			'display'			=> true)
		);
	}

	private function get_post() {
		$bankers 	= array();
		$banker_id	= 0;
		$selected = $this->in->getArray('banker_ids', 'int');
		if($this->in->exists('bankers', 'string')) {
			foreach($this->in->getArray('bankers', 'string') as $key => $banker) {
				if(isset($banker['id']) && $banker['id'] && !empty($banker['name'])) {
					$bankers[$banker_id] = array(
						'selected'	=> (in_array($banker['id'], $selected)) ? $banker['id'] : false,
						'id'		=> $this->in->get('bankers:'.$key.':id', 0),
						'name'		=> $this->in->get('bankers:'.$key.':name', ''),
						'bankchar'	=> $this->in->get('bankers:'.$key.':bankchar', 0),
						'note'		=> $this->in->get('bankers:'.$key.':note', '')
					);
					foreach($this->money->get_data() as $monName=>$monValue){
						$bankers[$banker_id]['money_'.$monName] = $this->in->get('bankers:'.$key.':money_'.$monName, '');
					}
				}
				$banker_id++;
			}
			return $bankers;
		}
		return false;
	}
}
registry::register('Manage_Banker');
?>
