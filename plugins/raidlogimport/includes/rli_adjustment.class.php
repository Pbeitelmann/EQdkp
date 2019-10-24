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

if(!defined('EQDKP_INC')) {
	header('HTTP/1.0 Not Found');
	exit;
}

if(!class_exists('rli_adjustment')) {
  class rli_adjustment extends gen_class {
	public static $shortcuts = array('rli', 'in', 'pdh', 'user', 'tpl', 'html',
		'member'	=> 'rli_member',
		'raid'		=> 'rli_raid',
	);
	public static $dependencies = array('rli');

	public function __construct() {
		$this->adjs = $this->rli->get_cache_data('adj');
		if($this->in->exists('adjs')) $this->load_adjs();
	}

	private function config($name) {
		return $this->rli->config($name);
	}
	
	public function reset() {
		$this->adjs = array();
	}

	public function add($reason, $member, $value, $event, $date=0, $raid=0) {
		$this->adjs[] = array('reason' => $reason, 'member' => $member, 'value' => runden($value), 'date' => $date, 'raid' => $raid, 'event' => $event);
	}

	public function add_new($num) {
		while($num > 0) {
			$this->adjs[] = array('reason' => '');
			$num--;
		}
	}
	
	public function update($key, $values) {
		if(is_array($values)) {
			foreach($values as $type => $data) {
				$this->adjs[$key][$type] = $data;
			}
			return true;
		}
		return false;
	}
	
	public function load_adjs() {
		$this->adjs = array();
		foreach($_POST['adjs'] as $a => $adj) {
			if(!(isset($adj['delete']) AND $adj['delete'])) {
				$this->adjs[$a] = $this->in->getArray('adjs:'.$a, '');
				$this->adjs[$a]['value'] = runden($this->in->floatvalue($adj['value']));
			}
		}
	}
	
	public function display($with_form=false) {
		if($this->rli->get_cache_data('progress') == 'adjustments') $this->rli->add_cache_data('progress', 'finish');
		$members = $this->member->get_for_dropdown(4);
		$events = $this->pdh->aget('event', 'name', 0, array($this->pdh->get('event', 'id_list')));
		$raid_select = array_merge(array($this->user->lang('none')), $this->raid->raidlist());
		$a = 0;
		if(is_array($this->adjs)) {
			foreach($this->adjs as $a => $adj) {
				$ev_sel = (isset($adj['event'])) ? $adj['event'] : 0;
				if(runden($adj['value']) == '0' || runden($adj['value']) == '-0') {
					unset($data['adjs'][$a]);
					continue;
				}
				$this->tpl->assign_block_vars('adjs', array(
					'MEMBER'	=> new hdropdown('adjs['.$a.'][member]', array('options' => $members, 'value' => $adj['member'], 'id' => 'adjs_'.$a.'_member')),
					'EVENT'		=> new hdropdown('adjs['.$a.'][event]', array('options' => $events, 'value' => $ev_sel, 'id' => 'adjs_'.$a.'_event')),

					'NOTE'		=> $adj['reason'],
					'VALUE'		=> $adj['value'],
					'RAID'		=> new hdropdown('adjs['.$a.'][raid]', array('options' => $raid_select, 'value' => $adj['raid'], 'id' => 'adjs_'.$a.'raid')),
					'KEY'		=> $a,
				));
			}
		}
		$this->tpl->assign_block_vars('adjs', array(
			'KEY'		=> 999,
			'MEMBER'	=> new hdropdown('adjs[999][member]', array('options' => $members, 'value' => 0, 'id' => 'adjs_999_member')),
			'EVENT'		=> new hdropdown('adjs[999][event]', array('options' => $events, 'value' => 0, 'id' => 'adjs_999_event')),
			'RAID'		=> new hdropdown('adjs[999][raid]', array('options' => $raid_select, 'value' => 0, 'id' => 'adjs_999_raid')),
			
			'DISPLAY'	=> 'style="display: none;"',
			'DELCHK'	=> 'checked="checked"',
		));
		$this->tpl->add_js(
"$('#rli_select_all').click(function() {
	if($(this).prop('checked')) {
		$('.rli_select_me').attr('checked', 'checked');
	} else {
		$('.rli_select_me').removeAttr('checked');
	}
});
var rli_key = ".($a+1).";
$('#add_adj_button').click(function() {
	var adj = $('#adj_999').clone(true);
	adj.find('.rli_select_me').removeAttr('checked');
	adj.html(adj.html().replace(/999/g, rli_key));
	adj.attr('id', 'adj_'+rli_key);
	adj.removeAttr('style');
	$('#adj_999').before(adj);
	rli_key++;
});", 'docready');
	}
	
	public function check_adj_exists($member, $reason, $raid_id=0) {
		if(is_array($this->adjs)) {
			foreach($this->adjs as $key => $adj) {
				if($adj['member'] == $member AND $adj['reason'] == $reason AND (!$raid_id OR $adj['raid'] == $raid_id)) {
					return $key;
				}
			}
		}
		return false;
	}

	public function check($bools) {
		if(is_array($this->adjs)) {
			foreach($this->adjs as $key => $adj) {
				if(!$adj['event'] OR !$adj['member'] OR !$adj['reason'] OR !$adj['value']) {
					$bools['false']['adj'] = false;
				}
			}
		} else {
			$bools['false']['adj'] = 'miss';
		}
		return $bools;
	}
	
	//TODO: try and group adjustments
	public function insert() {
		foreach($this->adjs as $key => $adj) {
			$this->rli->pdh_queue('adjustments', $key, 'adjustment', 'add_adjustment', array($adj['value'], $adj['reason'], array($this->member->name_ids[$adj['member']]), $adj['event'], $adj['raid'], $adj['time']), array('param' => 4, 'type' => 'raids'));
		}
		return true;
	}
	
	public function __destruct() {
		$this->rli->add_cache_data('adj', $this->adjs);
		parent::__destruct();
	}
  }
}

?>