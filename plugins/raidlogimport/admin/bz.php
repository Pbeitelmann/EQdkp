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

class rli_Bz extends page_generic {
	public static function __shortcuts() {
		$shortcuts = array('in', 'user', 'core', 'tpl', 'pdh', 'config', 'pm', 'html', 'jquery', 'game');
		return array_merge(parent::$shortcuts, $shortcuts);
	}

	public function __construct() {
		$this->user->check_auth('a_raidlogimport_bz');

		$handler = array(
			'save' => array('process' => 'save', 'csrf' => true),
			'copy' => array('process' => 'copy'),
			'upd'  => array('process' => 'update', 'csrf' => false),
			'inactive' => array('process' => 'switch_inactive', 'csrf' => true)
		);
		parent::__construct(false, $handler, false, null, 'bz_ids[]');
		$this->process();
	}

	private function prepare_data($type, $id, $method='add') {
		$data = array();
		if($type == 'zone') {
			$data = array(
				$this->in->get('string:'.$id, ''),
				$this->in->get('event:'.$id, 0),
				$this->in->get('timebonus:'.$id, 0.0),
				$this->in->get('diff:'.$id, 0),
				$this->in->get('sort:'.$id, 0));
		} else {
			$data = array(
				$this->in->get('string:'.$id, '', 'string'),
				(($this->config->get('event_boss', 'raidlogimport') & 1) ? $this->in->get('event:'.$id, 0) : $this->in->get('note:'.$id, '')),
				$this->in->get('bonus:'.$id, 0.0),
				$this->in->get('timebonus:'.$id, 0.0),
				$this->in->get('diff:'.$id, 0),
				$this->in->get('tozone:'.$id, 0),
				$this->in->get('sort:'.$id, 0));
		}
		if($method == 'update') {
			list($type, $id) = explode('_', $id);
			$data = array_merge(array($id), $data);
		}
		return $data;
	}

	public function save() {
		$message = array('rli_no_save' => array(), 'rli_save_suc' => array());
		if($this->in->get('save') == $this->user->lang('save')) {
			$data = $this->in->getArray('type', 'string');
			foreach($data as $id => $type) {
				$method = ($id == 'neu') ? 'add' : 'update';
				list($old_type, $iid) = explode('_', $id);
				if($old_type == $type OR $method == 'add') {
					$save = $this->pdh->put('rli_'.$type, $method, $this->prepare_data($type, $id, $method));
				} else {
					//type changed: remove and add
					$save = $this->pdh->put('rli_'.$old_type, 'del', array($iid));
					if($save) $save = $this->pdh->put('rli_'.$type, 'add', $this->prepare_data($type, $id, 'add'));
				}
				if($save) {
					$message['rli_save_suc'][] = $this->in->get('string:'.$id, '');
				} else {
					$message['rli_no_save'][] = $this->in->get('string:'.$id, '');
				}
			}
			$this->pdh->process_hook_queue();
		}
		$this->display($message);
	}

	public function copy() {
		$bz_ids = $this->in->getArray('bz_ids', '');
		$zones = array();
		pd($bz_ids);
		foreach($bz_ids as $bz_id) {
			if(strpos($bz_id, 'z') !== 0) continue;
			$zones[] = substr($bz_id, 1);
		}
		pd($zones);
		foreach($zones as $id) {
			$data = array(
				implode($this->config->get('bz_parse', 'raidlogimport'), $this->pdh->get('rli_zone', 'string', array($id))),
				$this->pdh->get('rli_zone', 'event', array($id)),
				$this->pdh->get('rli_zone', 'timebonus', array($id)),
				$this->in->get('diff', 0),
				$this->pdh->get('rli_zone', 'sort', array($id)));
			$new_id = $this->pdh->put('rli_zone', 'add', $data);
			if($new_id) {
				$bosses = $this->pdh->get('rli_boss', 'bosses2zone', array($id));
				foreach($bosses as $bid) {
					$boss_diff = $this->pdh->get('rli_boss', 'diff', array($bid));
					$data = array(
						implode($this->config->get('bz_parse', 'raidlogimport'), $this->pdh->get('rli_boss', 'string', array($bid))),
						$this->pdh->get('rli_boss', 'note', array($bid)),
						$this->pdh->get('rli_boss', 'bonus', array($bid)),
						$this->pdh->get('rli_boss', 'timebonus', array($bid)),
						($boss_diff) ? $this->in->get('diff', 0) : 0,
						$new_id,
						$this->pdh->get('rli_boss', 'sort', array($bid)));
					$this->pdh->put('rli_boss', 'add', $data);
				}
				$message['bz_copy_suc'][] = $this->pdh->geth('rli_zone', 'event', array($id, false));
			} else {
				$message['bz_no_copy'][] = $this->pdh->geth('rli_zone', 'event', array($id, false));
			}
		}
		$this->display($message);
	}

	public function switch_inactive() {
		$ids = $this->in->getArray('bz_ids', 'string');
		foreach($ids as $id) {
			if(strpos($id, 'z') !== 0) continue;
			$id = intval(substr($id, 1));
			$this->pdh->put('rli_zone', 'switch_inactive', array($id));
			$zones[] = $this->pdh->geth('rli_zone', 'event', array($id, false));
		}
		$this->display(array('bz_active_suc' => $zones));
	}

	public function delete() {
		if($this->in->exists('bz_ids')) {
			$bz_ids = $this->in->getArray('bz_ids', 'string');
			foreach($bz_ids as $id) {
				if(strpos($id, 'b') !== false) {
					$id = substr($id, 1);
					$note = $this->pdh->get('rli_boss', 'note', array($id));
					if($this->pdh->put('rli_boss', 'del', array($id))) {
						$message['rli_del_suc'][] = $note;
					} else {
						$message['rli_no_del'][] = $note;
					}
				} else {
					$id = substr($id, 1);
					$event = $this->pdh->get('rli_zone', 'event', array($id, false));
					if($this->pdh->put('rli_zone', 'del', array($id))) {
						$message['rli_del_suc'][] = $event;
					} else {
						$message['rli_no_del'][] = $event;
					}
				}
			}
		} else {
			$message['rli_no_del'][] = $this->user->lang('bz_no_id');
		}
		$this->display($message);
	}

	private function get_upd_data($type, $id) {
		return array(
				'ID'			=> $type.'_'.$id,
				'STRING'		=> implode($this->config->get('bz_parse', 'raidlogimport'), $this->pdh->get('rli_'.$type, 'string', array($id))),
				'NOTE'			=> ($type == 'boss') ? $this->pdh->get('rli_boss', 'note', array($id)) : '',
				'BONUS'			=> ($type == 'boss') ? $this->pdh->get('rli_boss', 'bonus', array($id)) : '',
				'TIMEBONUS'		=> $this->pdh->get('rli_'.$type, 'timebonus', array($id)),
				'DIFF'			=> $this->pdh->get('rli_'.$type, 'diff', array($id)),
				'SORT'			=> $this->pdh->get('rli_'.$type, 'sort', array($id)),
				'BSELECTED'		=> ($type == 'boss') ? 'selected="selected"' : '',
				'ZSELECTED'		=> ($type == 'zone') ? 'selected="selected"' : '',
				'DIFF_ARRAY'	=> new hdropdown("diff[".$type."_".$id."]", array('options' => $this->diff_drop, 'value' => $this->pdh->get('rli_'.$type, 'diff', array($id)))),
				'ZONE_ARRAY'	=> new hdropdown("tozone[".$type."_".$id."]", array('options' => $this->zone_drop, 'value' => (($type == 'boss') ? $this->pdh->get('rli_boss', 'tozone', array($id)) : $id))),
				'EVENTS'		=> new hdropdown("event[".$type."_".$id."]", array('options' => $this->event_drop, 'value' => (($type == 'zone') ? $this->pdh->get('rli_zone', 'event', array($id)) : $this->pdh->get('rli_boss', 'note', array($id)))))
		);
	}

	private function prepare_diff_drop() {
		if(!isset($this->diff_drop)) $this->diff_drop = array(	
				0 => $this->user->lang('diff_0'), 
				1 => $this->user->lang('diff_1'), 
				2 => $this->user->lang('diff_2'), 
				3 => $this->user->lang('diff_3'), 
				4 => $this->user->lang('diff_4'),
				5 => $this->user->lang('diff_5'),
				6 => $this->user->lang('diff_6'),
				7 => $this->user->lang('diff_7'),
				8 => $this->user->lang('diff_8'),
				9 => $this->user->lang('diff_9'),
				11 => $this->user->lang('diff_11'),
				12 => $this->user->lang('diff_12'),
				14 => $this->user->lang('diff_14'),
				15 => $this->user->lang('diff_15'),
				16 => $this->user->lang('diff_16'),
				);
	}

	public function update() {
		if(empty($this->zone_drop)) {
			$this->zone_drop = $this->pdh->aget('rli_zone', 'html_string', 0, array($this->pdh->get('rli_zone', 'id_list')));
			$this->zone_drop[0] = $this->user->lang('bz_no_zone');
			ksort($this->zone_drop);
		}
		if(empty($this->event_drop)) $this->event_drop = $this->pdh->aget('event', 'name', 0, array($this->pdh->get('event', 'id_list')));
		$this->prepare_diff_drop();
		if($this->in->exists('bz_ids')) {
			$bz_ids = $this->in->getArray('bz_ids', 'string');
			foreach($bz_ids as $id) {
				if(strpos($id, 'b') !== false) {
					$this->tpl->assign_block_vars('upd_list', $this->get_upd_data('boss', substr($id, 1)));
				} else {
					$this->tpl->assign_block_vars('upd_list', $this->get_upd_data('zone', substr($id, 1)));
				}
			}
		} else {
			$this->tpl->assign_block_vars('upd_list', array(
				'ID'		=> 'neu',
				'STRING'	=> $this->in->get('string'),
				'NOTE'		=> $this->in->get('note'),
				'BONUS'		=> $this->in->get('bonus'),
				'TIMEBONUS'	=> $this->in->get('timebonus'),
				'SORT'		=> '',
				'BSELECTED'	=> 'true',
				'ZSELECTED'	=> '',
				'DIFF_ARRAY' => new hdropdown('diff[neu]', array('options' => $this->diff_drop, 'value' => $this->in->get('diff'))),
				'ZONE_ARRAY' => new hdropdown('tozone[neu]', array('options' => $this->zone_drop, 'value' => $this->in->get('zone_id'))),
				'EVENTS'	=> new hdropdown('event[neu]', array('options' => $this->event_drop))
			));
		}

		$this->tpl->assign_vars(array(
			'S_DIFF'		=> ($this->game->get_game() == 'wow') ? true : false,
			'S_BOSSEVENT'	=> ($this->config->get('event_boss', 'raidlogimport')  & 1) ? true : false,
		));
		$js = '
			$(".boss_zone_type").change(function(){
				var id = $(this).attr("id").substr(5);
				if($(this).val() == "zone") {
					$("#bonus_"+id).attr("class", "bz_hide");
					$("#tozone_"+id).attr("class", "bz_hide");
					';
		if(!($this->config->get('event_boss', 'raidlogimport') & 1)) {
			$js .= '$("#event_"+id).attr("class", "bz_show");
					$("#note_"+id).attr("class", "bz_hide");';
		}
		$js .= '
				} else {
					$("#bonus_"+id).attr("class", "bz_show");
					$("#tozone_"+id).attr("class", "bz_show");
					';
		if(!($this->config->get('event_boss', 'raidlogimport') & 1)) {
			$js .= '$("#event_"+id).attr("class", "bz_hide");
					$("#note_"+id).attr("class", "bz_show");';
		}
		$js .= '
				}
			});';
		$this->tpl->add_js($js, 'docready');
		$this->tpl->add_css('
			.bz_show {
				position: relative;
			}
			.bz_hide {
				display: none;
			}');
		$this->core->set_vars(array(
			'page_title'        => sprintf($this->user->lang('admin_title_prefix'), $this->config->get('guildtag'), $this->config->get('dkp_name')).': '.$this->user->lang('rli_bz_bz'),
			'template_path'     => $this->pm->get_data('raidlogimport', 'template_path'),
			'template_file'     => 'bz_upd.html',
			'header_format'		=> $this->simple_head,
			'display'           => true,
			)
		);
	}

	public function display($messages=array()) {
		if($messages) {
			$this->pdh->process_hook_queue();
			$type = 'green';
			foreach($messages as $title => $mess) {
				if(strpos('no', $title) !== false) {
					$type = 'red';
				}
				if($mess) {
					$this->core->message(implode(', ', $mess), $this->user->lang($title), $type);
				}
			}
		}
		$bosses = $this->pdh->get('rli_boss', 'id_list', array(false));
		$tozone = array();
		$sorting = array();
		$zones = $this->pdh->get('rli_zone', 'id_list', array(false));
		foreach($bosses as $boss_id) {
			$sorting['boss'][$boss_id] = $this->pdh->get('rli_boss', 'sort', array($boss_id));
			$tozone[$this->pdh->get('rli_boss', 'tozone', array($boss_id))][] = $boss_id;
		}
		foreach($zones as $id) {
			$sorting['zone'][$id] = $this->pdh->get('rli_zone', 'sort', array($id));
			if(!in_array($id, array_keys($tozone))) {
				$tozone[$id] = array();
			}
		}
		asort($sorting['boss']);
		asort($sorting['zone']);
		foreach($sorting['zone'] as $zone_id => $zsort) {
			$this->assign2tpl($zone_id, $sorting, $tozone);
		}
		if(isset($tozone[0]) AND count($tozone[0]) > 0) {
			$this->assign2tpl(0, $sorting, $tozone);
		}
		$this->prepare_diff_drop();
		$this->confirm_delete();
		$this->tpl->assign_vars(array(
			'S_DIFF'		=> ($this->config->get('default_game') == 'wow') ? true : false,
			'DIFF_DROP'		=> new hdropdown('diff', array('options' => $this->diff_drop)),
		));
		$this->jquery->Tab_header('rli_manage_bz');
		$this->core->set_vars(array(
			'page_title'        => $this->user->lang('rli_bz_bz'),
			'template_path'     => $this->pm->get_data('raidlogimport', 'template_path'),
			'template_file'     => 'bz.html',
			'header_format'		=> $this->simple_head,
			'display'           => true,
			)
		);
	}

	private function assign2tpl($zone_id, $sorting, $tozone) {
		$this->jquery->Collapse('#zone_'.$zone_id);
		$inactive = (!$zone_id || $this->pdh->get('rli_zone', 'active', array($zone_id))) ? '' : 'inactive_';
		$this->tpl->assign_block_vars($inactive.'zone_list', array(
			'ZID'		=> $zone_id,
			'ZSTRING'	=> ($zone_id) ? $this->pdh->geth('rli_zone', 'string', array($zone_id)) : $this->user->lang('bz_boss_oz'),
			'ZTIMEBONUS'=> ($zone_id) ? $this->pdh->geth('rli_zone', 'timebonus', array($zone_id)) : '',
			'ZVAL'		=> ($zone_id && $this->config->get('use_dkp', 'raidlogimport') & 4) ? $this->pdh->geth('event', 'value', array($this->pdh->get('rli_zone', 'event', array($zone_id)))) : '&nbsp;',
			'ZNOTE'		=> ($zone_id) ? $this->pdh->geth('rli_zone', 'event', array($zone_id)) : '')
		);
		foreach($sorting['boss'] as $boss_id => $bsort) {
			if(in_array($boss_id, $tozone[$zone_id])) {
				$this->tpl->assign_block_vars($inactive.'zone_list.'.$inactive.'boss_list', array(
					'BID'		=> $boss_id,
					'BSTRING'	=> $this->pdh->geth('rli_boss', 'string', array($boss_id)),
					'BNOTE'		=> $this->pdh->geth('rli_boss', 'note', array($boss_id)),
					'BBONUS'	=> $this->pdh->get('rli_boss', 'bonus', array($boss_id)),
					'BTIMEBONUS'=> $this->pdh->get('rli_boss', 'timebonus', array($boss_id))
				));
			}
		}
	}
}

registry::register('rli_Bz');