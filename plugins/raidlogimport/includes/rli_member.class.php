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

if(!defined('EQDKP_INC')){
	header('HTTP/1.0 Not Found'); exit;
}

if(!class_exists('rli_member')) {
	class rli_member extends gen_class {
		public static $shortcuts = array('cconfig' => 'config', 'rli', 'in', 'pdh', 'user', 'tpl', 'html', 'jquery', 'time', 'pfh', 'game',
			'adj'		=> 'rli_adjustment',
			'member'	=> 'rli_member',
			'raid'		=> 'rli_raid',
		);
		public static $dependencies = array('rli');

		private $members = array();
		private $member_ranks = array();
		private $timebar_created = false;
		private $positions = array('up', 'middle', 'down');
		private $updown = array(false,false);
		private $rpos = array();
		private $boss_data = array();
		public $raid_members = array();
		public $name_ids = array();

		public function __construct() {
			$this->members = $this->rli->get_cache_data('member');
			if($this->in->exists('members')) $this->load_members();
		}

		public function reset() {
			$this->members = array();
		}

		private function config($name) {
			return $this->rli->config($name);
		}

		public function add($name, $class=0, $race=0, $lvl=0, $note='') {
			if($race == 'Scourge' || $race == 'SCOURGE') {
				$race = 'Undead';
			}
			if($race == 'BloodElf' || $race == 'BLOODELF') {
				$race = 'Blood Elf';
			}
			if($race == 'NightElf' || $race == 'NIGHTELF') {
				$race = 'Night Elf';
			}
			if($class == 'DEATHKNIGHT' || $class == 'DeathKnight') {
				$class = 'Death Knight';
			}
			if($class == 'DRUID') {
				$class = 'Druid';
			}
			if(!deep_in_array($name, $this->members)) $this->members[] = array('name' => $name, 'class' => $class, 'race' => $race, 'level' => $lvl, 'note' => $note);
		}

		public function add_time($name, $time, $type, $extra=0) {
			settype($time, 'int');
			foreach($this->members as $key => &$mem) {
				if(isset($mem['name']) AND $mem['name'] == $name) {
					if(isset($this->members['times'][$key]) AND is_array($this->members['times'][$key]) AND array_key_exists($time, $this->members['times'][$key])) {
						unset($this->members['times'][$key][$time]);
					} else {
						$this->members['times'][$key][$time] = (string) $type;
						if($extra) {
							$this->members['times'][$key][$time] .= '_'.$extra;
						}
					}
					break;
				}
			}
		}

		public function load_members() {
			$globalattraids = $this->raid->get_attendance_raids();
			$one_attendant = false;
			foreach($_POST['members'] as $k => $mem) {
				if(!(is_array($this->members) AND in_array($k, array_keys($this->members)))) {
					$this->members[$k] = array();
				}
				foreach($this->members as $key => &$member) {
					if($k == $key) {
						if(isset($mem['delete']) AND $mem['delete']) {
							unset($this->members[$key]);
							continue;
						}
						$member['name'] = $this->in->get('members:'.$key.':name', '');
						if($this->config('member_display') == 2) {
							$times = array();
							foreach($mem['times'] as $tk => $time) {
								$times[$tk]['join'] = $this->in->get('members:'.$key.':times:'.$tk.':join', 0);
								$times[$tk]['leave'] = $this->in->get('members:'.$key.':times:'.$tk.':leave', 0);
								$extra = $this->in->get('members:'.$key.':times:'.$tk.':extra', '');
								if($extra) $times[$tk][$extra] = 1;
							}
							$member['times'] = $times;
							$member['raid_list'] = $this->raid->get_memberraids($member['times']);
							$a = $this->raid->get_attendance($member['times']);
							$member['att_begin'] = $a['begin'];
							$member['att_end'] = $a['end'];
							unset($a);
						} else {
							$member['raid_list'] = $this->in->getArray('members:'.$key.':raid_list', 'int');
							$member['att_begin'] = (isset($mem['att_begin'])) ? true : false;
							$member['att_end'] = (isset($mem['att_end'])) ? true : false;
						}
						if($member['raid_list']) {
							foreach($member['raid_list'] as $raid_id) {
								$one_attendant = true;
								if(!$this->config('attendance_raid') OR ($raid_id != $globalattraids['begin'] AND $raid_id != $globalattraids['end'])) {
									$dkp = $this->raid->get_value($raid_id, $member['times'], array($member['att_begin'], $member['att_end']));
									$dkp = runden($dkp);
									$raid = $this->raid->get($raid_id);
									if($dkp <  $raid['value']) {
										//add an adjustment
										$dkp -= $raid['value'];
										$akey = $this->adj->check_adj_exists($member['name'], $this->user->lang('rli_partial_raid'), $raid_id);
										if($akey !== false) {
											$this->adj->update($akey, array('value' => $dkp));
										} else {
											$this->adj->add($this->user->lang('rli_partial_raid'), $member['name'], $dkp, $raid['event'], $raid['begin'], $raid_id);
										}
									}
								}
							}
						}
					}
				}
			}
			if(!$one_attendant) {
				$this->rli->error('process_members', $this->user->lang('rli_error_no_attendant'));
			}
		}

		public function finish() {
			$begin = $this->raid->get_start_end();
			$end = $begin['end'];
			$begin = $begin['begin'];
			$error = '';
			foreach($this->members['times'] as $key => $times) {
				ksort($times);
				$count = 1;
				$size =  count($times);
				$lasttype = false;
				$lasttime = false;
				foreach($times as $time => $type) {
					if($type == $lasttype) {
						// if($this->config('del_dbl_times')) {
							if($type == 'join') {
								if($time > $lasttime) {
									unset($times[$time]);
									continue;
								} else {
									unset($times[$lasttime]);
								}
							} else {
								if($time > $lasttime) {
									unset($times[$lasttime]);
								} else {
									unset($times[$time]);
									continue;
								}
							}
						// } else {
							// $error .= '<br />Wrong Member: '.$this->members[$key]['name'].', '.$type.'-times: '.date('H:i:s', $time).' and '.date('H:i:s', $lasttime);
						// }
					} elseif($type == 'join' AND $lasttype == 'join_standby') {
						$new_time = $time-1;
						$times[$new_time] = 'leave_standby';
					} else {
						if($begin AND $type == 'join' AND ($begin + $this->config('member_miss_time')) > $time AND $count == 1) {
							unset($times[$time]);
							$times[$begin] = 'join';
						}
						if($end AND $type == 'leave' AND ($end - $this->config('member_miss_time')) < $time AND $count == $size) {
							unset($times[$time]);
							$times[$end] = 'leave';
						}
						if($type == 'join' AND ($time - $this->config('member_miss_time')) < $lasttime) {
							unset($times[$time]);
							unset($times[$lasttime]);
						}
					}
					$lasttype = $type;
					$lasttime = $time;
					$count++;
				}
				ksort($times);
				$tkey = 0;
				$new_times = array();
				foreach($times as $time => $type) {
					$extra = '';
					if(strpos($type, '_') !== false) list($type, $extra) = explode('_', $type);
					if($type == 'join') {
						$new_times[$tkey] = array($type => $time);
						if($extra) {
							$new_times[$tkey][$extra] = true;
						}
					}
					if($type == 'leave') {
						$new_times[$tkey][$type] = $time;
						$tkey++;
					}
				}
				$this->members[$key]['times'] = $new_times;
			}
			unset($this->members['times']);
			if($error != '') {
				message_die($error); //TODO: remove message_die
			}
		}

		public function add_new($num) {
			for($i=1; $i<=$num; $i++) {
				$this->members[] = array('name' => '', 'times' => array());
			}
		}

		public function display($with_form=false) {
			$globalattraids = $this->raid->get_attendance_raids();
			$key = 0;
			$first_run = false;
			if($this->rli->get_cache_data('progress') == 'members') {
				$this->create_memberraids();
				$this->rli->add_cache_data('progress', 'items');
			}
			foreach($this->members as $key => $member) {
				if($with_form) {
					if($this->config('s_member_rank') & 1) {
						$member['rank'] = $this->rank_suffix($member['name']);
					}
					if($this->config('member_display') == 1 AND extension_loaded('gd')) {
						$raid_list = $this->raid->get_checkraidlist($member['raid_list'], $key);
					}
					elseif($this->config('member_display') == 2 AND extension_loaded('gd')) {
						$raid_list = '';
						$detail_raid_list = true;
					} else {
						$raid_list = '<td>'.(new hmultiselect('members['.$key.'][raid_list]', array('options' => $this->raid->raidlist(), 'value' => $member['raid_list'], 'id' => 'members_'.$key.'_raidlist'))).'</td>';
					}
					$att_begin = ((isset($member['att_begin']) AND $member['att_begin']) OR (!isset($member['att_begin']) AND $a['begin'])) ? 'checked="checked"' : '';
					$att_end = ((isset($member['att_end']) AND $member['att_end']) OR (!isset($member['att_end']) AND $a['end'])) ? 'checked="checked"' : '';
					//js deletion
					if(!$this->rli->config('no_del_warn')) {
						$options = array(
							'custom_js' => "$('#'+del_id).css('display', 'none'); $('#'+del_id+'submit').removeAttr('disabled');",
							'withid' => 'del_id',
							'message' => $this->user->lang('rli_delete_members_warning')
						);
						$this->jquery->Dialog('delete_warning', $this->user->lang('confirm_deletion'), $options, 'confirm');
					}
				} else {
					$att_begin = (isset($member['att_begin']) AND $member['att_begin']) ? $this->user->lang('yes') : $this->user->lang('no');
					$att_end = (isset($member['att_end']) AND $member['att_end']) ? $this->user->lang('yes') : $this->user->lang('no');
					$raid_list = array();
					if(is_array($member['raid_list'])) {
						$this->raid->raidlist();
						foreach($member['raid_list'] as $rkey) {
							$raid_list[] = $this->raid->raidlist[$rkey];
						}
					}
				}
				$this->tpl->assign_block_vars('player', array(
					'MITGLIED'	=> ($with_form) ? $member['name'] : (($key < 9) ? '&nbsp;&nbsp;' : '').($key+1).'&nbsp;'.$member['name'],
					'RAID_LIST'	=> ($with_form) ? $raid_list : implode('; ', $raid_list),
					'ATT_BEGIN'	=> $att_begin,
					'ATT_END'	=> $att_end,
					'KEY'		=> $key,
					'NR'		=> $key +1,
					'RANK'		=> ($this->config('s_member_rank') & 1) ? $this->rank_suffix($member['name']) : '',
					'DELDIS'	=> 'disabled="disabled"',
				));
				if(isset($detail_raid_list)) $this->detailed_times_list($key, $member['raid_list']);
				$key++;
			}//foreach members
			//a member to copy from for js-addition
			if($with_form) {
				if(!isset($detail_raid_list)) {
					if($this->config('member_display') == 1 AND extension_loaded('gd')) {
						$raid_list = $this->raid->get_checkraidlist(array(), 999);
					} else {
						$raid_list = '<td>'.(new hmultiselect('members[999][raid_list]', array('options' => $this->raid->raidlist, 'id' => 'members_999_raidlist'))).'</td>';
					}
				}
				$this->tpl->assign_block_vars('player', array(
					'RAID_LIST'	=> (!isset($detai_raid_list)) ? $raid_list : '<td>'.$this->user->lang('rli_member_refresh_for_view').'</td>',
					'KEY'		=> 999,
					'DISPLAY'	=> 'style="display: none;"',
				));
				$this->members[999]['times'] = array();
				if(isset($detail_raid_list)) $this->detailed_times_list(999, array());
				unset($this->members[999]);
				$this->jquery->qtip('#dt_help', $this->user->lang('rli_help_dt_member'), array('my' => 'center right', 'at' => 'left center'));
				$this->tpl->add_js(
	"var rli_key = ".(($key) ? $key : 1).";
	$('.del_mem').click(function() {
		$(this).removeClass('del_mem');
		".($this->rli->config('no_del_warn') ? "$('#'+$(this).data('id')).css('display', 'none');
		$('#'+$(this).data('id')+'submit').removeAttr('disabled');" : "delete_warning($(this).data('id'));")."
	});
	$('#add_mem_button').click(function() {
		var mem = $('#memberrow_999').clone(true);
		mem.find('#memberrow_999submit').attr('disabled', 'disabled');
		mem.html(mem.html().replace(/999/g, rli_key));
		mem.attr('id', 'memberrow_'+rli_key);
		mem.removeAttr('style');
		mem.find('td:first').html((rli_key+1)+$.trim(mem.find('td:first').html()));
		$('#memberrow_999').before(mem);
		rli_key++;
	});", 'docready');
				
		if($this->config('member_display') == 1){
			$this->tpl->add_js("
				var header_height = 0;
			    $('.verticalText').each(function() {
					console.log($(this).outerWidth());
					console.log($(this).width());
			        if ($(this).outerWidth() > header_height) header_height = $(this).outerWidth();
			    });
			    $('tr td.raidrow').height(header_height+25);
			    $('.verticalText').width(20);
			", 'docready');
		}		
				
			}
		}

		public function rank_suffix($mname) {
			$this->get_member_ranks();
			$rank = (isset($this->member_ranks[$mname])) ? $this->member_ranks[$mname] : $this->member_ranks['new'];
			return ' ('.$rank.')';
		}

		public function get_for_dropdown($rank_page) {
			$members = array();
			foreach($this->members as $member) {
				$members[$member['name']] = $member['name'];
				if($this->config('s_member_rank') & $rank_page)
					$members[$member['name']] .= $this->rank_suffix($member['name']);
			}
			return $members;
		}

		public function check($bools) {
			if(is_array($this->members)) {
				foreach($this->members as $key => $member) {
					if(!$member['name']) {
						$bools['false']['mem'] = false;
					}
				}
			} else {
				$bools['false']['mem'] = 'miss';
			}
			return $bools;
		}
	
		public function check_special($name) {
			$special_chars = $this->cconfig->get('special_members');
			if(in_array($name, $this->pdh->aget('member', 'name', 0, array($special_chars)))) return true;
			$members = $this->pdh->aget('member', 'name', 0, array($this->pdh->get('member', 'id_list', array(false, false, false))));
			if(!($id = array_search($name, $members))) {
				$data = array('name' => $name);
				$id = $this->pdh->put('member', 'addorupdate_member', array(0, $data));
			}
			$special_chars[] = $id;
			$this->cconfig->set('special_members', $special_chars);
			return true;
		}

		public function insert() {
			foreach($this->members as $member) {
				$intMemberId = false;
				$servername = false;
				$membername = $member['name'];
				
				if(register('config')->get('default_game') === "wow" && strpos($member['name'], '-')){
					//add a possibility to track wow's "super cool" cross-realm naming idea
					list($membername, $servername) = explode('-', $member['name']);
					$servername = preg_replace_callback(
							"/([^A-Z\'\"\-; ])([A-Z])/",
							function($m) { return $m[1].' '.$m[2]; },
							$servername
					);
					
					$intMemberId = $this->pdh->get('member', 'id', array($membername, array('servername' => $servername)));
				} else {
					$intMemberId = $this->pdh->get('member', 'id', array($member['name']));
				}
			
				if(!$intMemberId) {
					$data = array(
						'name' 		=> $membername,
						'level' 	=> $member['level'],
						'race'		=> $this->game->get_id('races', $member['race']),
						'class'		=> $this->game->get_id('classes', $member['class']),
						'rankid'	=> $this->config('new_member_rank'),
					);
					if($servername && $servername != ""){
						$data['servername'] = $servername;
					}

					$id = $this->pdh->put('member', 'addorupdate_member', array(0, $data));

					if(!$id) {
						$this->rli->error('process_members', sprintf($this->user->lang('rli_error_member_create'), $member['name']));
						return false;
					}
				} else {
					$id = $intMemberId;
				}
			
				$this->raid_members[$id] = $member['raid_list'];
				$this->name_ids[$member['name']] = $id;
			}
			$this->pdh->process_hook_queue();
			// add disenchanted / bank to name_ids array
			$dis_id = $this->pdh->get('member', 'id', array('disenchanted'));
			$bank_id = $this->pdh->get('member', 'id', array('bank'));
			if($dis_id) {
				$this->name_ids['Disenchanted'] = $dis_id;
				$this->name_ids['disenchanted'] = $dis_id;
			}
			if($bank_id) {
				$this->name_ids['bank'] = $bank_id;
				$this->name_ids['Bank'] = $bank_id;
			}

			return true;
		}
	
		private function create_memberraids() {
			foreach($this->members as &$member) {
				$member['raid_list'] = $this->raid->get_memberraids($member['times']);
				$a = $this->raid->get_attendance($member['times']);
				if(isset($a['begin']) AND !in_array($globalattraids['begin'], $member['raid_list'])) {
					$member['raid_list'][] = $globalattraids['begin'];
				}
				if(isset($a['end']) AND !in_array($globalattraids['end'], $member['raid_list'])) {
					$member['raid_list'][] = $globalattraids['end'];
				}
			}
		}

		private function raid_positions($raids, $begin) {
			if(!empty($this->raids_positioned)) return true;
			$suf = '';
			if($this->updown[0] !== $this->updown[1]) {
				$suf = ' half';
				if($this->updown[0]) {
					$pos = 0;
				} else {
					$pos = 2;
				}
			} else {
				$pos = 1;
			}
			foreach($raids as $rkey => $raid) {
				if($this->raid->get_standby_raid() == $rkey) {
					$pos = 2;
				} elseif($this->config('raidcount') & 1 && $this->config('raidcount') & 2 AND count($raid['bosskills']) == 1) {
					$pos = 0;
				}
				$this->rpos[$rkey] = $this->positions[$pos].$suf;
			}
			$this->raids_positioned = true;
			return true;
		}

		private function init_times_list($width) {
			if(!isset($this->px_time)) {
				$this->px_time = (($width['end'] - $width['begin']) / 20);
				settype($px_time, 'int');
				$bars = 1;
				if($this->config('standby_raid') == 1) {
					$bars++;
					$this->updown[0] = true;
				}
				if($this->config('raidcount') & 1 AND $this->config('raidcount') & 2) {
					$bars++;
					$this->updown[1] = true;
				}
				$this->height = 11 + $bars*14;
			}
		}

		private function detailed_times_list($key, $mraids) {
			$width = $this->raid->get_start_end();
			$this->init_times_list($width);

			$raids = $this->raid->get_data();
			$this->raid_positions($raids, $width['begin']);
			foreach($raids as $rkey => $raid) {
				$w = ($raid['end']-$raid['begin'])/20;
				$m = ($raid['begin']-$width['begin'])/20;
				settype($w, 'int');
				settype($m, 'int');
				$w--;
				$disabled = (in_array($rkey, $mraids)) ? "" : " disabled='disabled'";
				$active = (in_array($rkey, $mraids)) ? " active" : "";
				$this->tpl->assign_block_vars('player.member_raids', array(
					'KEY'	=> $rkey,
					'RPOS'	=> $this->rpos[$rkey],
					'ACTIVE' => $active,
					'DISABLED' => $disabled,
					'WIDTH' => $w,
					'LEFT' => $m)
				);
				if(!isset($this->bosses_done)) {
					foreach($raid['bosskills'] as $bkey => $boss) {
						$m = ($boss['time']-$width['begin'])/20 - 4;
						settype($m, 'int');
						$this->jquery->qtip('.rli_boss', 'return $(".rli_boss_c", this).html();', array('contfunc' => true));
						$this->boss_data[] = array(
							'KEY' => $bkey,
							'LEFT' => $m,
							'NAME' => (is_numeric($boss['id'])) ? $this->pdh->get('rli_boss', 'note', array($boss['id'])) : $boss['id'],
							'TIME' => $this->time->user_date($boss['time'], false, true),
							'VALUE'	=> $boss['bonus']
						);
					}
				}
			}
			$this->bosses_done = true;
			foreach($this->boss_data as $boss_data) {
				$this->tpl->assign_block_vars('player.bosses', $boss_data);
			}
			$tkey = 0;
			foreach($this->members[$key]['times'] as $mtime) {
				$s = (isset($mtime['standby']) AND $mtime['standby']) ? 'standby' : '';
				$w = ($mtime['leave']-$mtime['join'])/20;
				$ml = ($mtime['join']-$width['begin'])/20;
				if ($ml < 0)  $ml = 0;
				settype($w, 'int');
				settype($ml, 'int');
				$this->tpl->assign_block_vars('player.times', array(
					'KEY'		=> $tkey,
					'STANDBY'	=> $s,
					'EXTRA'		=> (!$s) ? '0' : 'standby',
					'WIDTH'		=> $w,
					'LEFT'		=> $ml,
					'JOIN'		=> $mtime['join'],
					'LEAVE'		=> $mtime['leave']
				));
				$tkey++;
			}
			$this->create_timebar($width['begin'], $width['end']);

			//only do this once
			if(!isset($this->tpl_assignments)) {

				$this->tpl->assign_vars(array(
					'CONTEXT_MENU' => true,
					'PXTIME' => $this->px_time,
					'HEIGHT' => $this->height)
				);

				$this->tpl->js_file($this->root_path.'plugins/raidlogimport/templates/dmem.js');
				$this->tpl->css_file($this->root_path.'plugins/raidlogimport/templates/base_template/dmem.css');

				$this->tpl->add_css(".time_scale {
									position: absolute;
									background-image: url(".$this->pfh->FilePath('images/time_scale.png', 'raidlogimport', false, 'serverpath').");
									background-repeat: repeat-x;
									width: ".$this->px_time."px;
									height: 18px;
									margin-top: 10px;
									z-index: 16;
								}");
				$this->tpl->add_js("$('#member_form').data('raid_start', ".$width['begin'].");
								$(document).on('mouseenter', '.add_time', function(){
									$('#time_scale_' + member_id).attr('class', 'time_scale');
								});
								$(document).on('mouseleave', '.add_time', function(){
									$('#time_scale_' + member_id).attr('class', 'time_scale_hide');
								});
								$('.add_time').on('dblclick', function(event){
									target = event.target;
									var myclass = $(target).attr('class');
									if (myclass == 'time_middle'){
										change_standby();
									}

									if ($(target).hasClass('raid')){
										add_timeframe();
									}
									});

								$(document).keydown( function(event) {
							if ( event.which == 46 ) {
								remove_timeframe();
							}
						});
						
								$(document).on('contextmenu', '.add_time', function(e) {
									$('<div id=\"rc_overlay\"></div>').css({left : '0px', top : '0px',position: 'absolute', width: '100%', height: '100%', zIndex: '200' }).click(function() {
										$(this).remove();
										$('#myrcm').hide();
									}).bind('contextmenu' , function(){return false;}).appendTo(document.body);
									$('#myrcm').css({ position: 'absolute', left: e.pageX+'px', top: e.pageY+'px', zIndex: '201' }).show();
									return false;
								});
								$('#rli_add_dmem').click(function() { add_timeframe(); $('#myrcm').hide();$('#rc_overlay').remove();});
								$('#rli_del_dmem').click(function() { remove_timeframe(); $('#myrcm').hide();$('#rc_overlay').remove();});
								$('#rli_swi_dmem').click(function() { change_standby(); $('#myrcm').hide();$('#rc_overlay').remove();});", 'docready');
				$this->tpl_assignments = true;
			}
		}

		private function create_timebar($start, $end) {		
			if(!$this->timebar_created && $this->px_time) {
				$px_time = ($this->px_time > 5000) ? 5000 : $this->px_time; //prevent very big images (although 5000 is quite big)
				$im = imagecreate($px_time, 18);
				$black = imagecolorallocate($im, 0,0,0);
				$white = imagecolorallocate($im, 255,255,255);
				imagefill($im, 0, 0, $white);
				imageline($im, 0,0,$px_time, 0, $black);
				$c = 2;
				for($i=0; $i<=$px_time;) {
					$y = 3;
					$c++;
					if($c == 3) {
						$y = 5;
						$c = 0;
					}
					imageline($im, $i, 1, $i, $y, $black);
					$i = $i+15;
				}
				$start += 900;
				$counter = 1;
				for($i=$start; $i < $end;) {
					$x = $counter*45 - 14;
					imagestring($im, 2, $x, 5, date('H:i', $i), $black);
					$i += 900;
					$counter++;
				}
				$this->timescalefile = $this->pfh->FilePath('images/time_scale.png', 'raidlogimport');
				imagepng($im, $this->timescalefile);
				imagedestroy($im);
				$this->timebar_created = true;
			}
		}

		private function get_member_ranks() {
			if(!$this->member_ranks) {
				$member_id_rank = $this->pdh->aget('member', 'rankname', 0, array($this->pdh->get('member', 'id_list')));
				foreach($member_id_rank as $id => $rank) {
					$this->member_ranks[$this->pdh->get('member', 'name', array($id))] = $rank;
				}
				$this->member_ranks['new'] = $this->pdh->get('rank', 'name', array($this->config('new_member_rank')));
			}
		}

		public function __destruct() {
			$this->rli->add_cache_data('member', $this->members);
			parent::__destruct();
		}
	}
}

?>