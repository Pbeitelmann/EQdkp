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

if(!class_exists('rli_parse')) {
	class rli_parse extends gen_class {
		public static $shortcuts = array('rli', 'in', 'config', 'user',
			'adj'		=> 'rli_adjustment',
			'item'		=> 'rli_item',
			'member'	=> 'rli_member',
			'raid'		=> 'rli_raid',
		);

		private $toload = array();

		public function parse_string($log, $parser=false) {
			$parser = ($parser) ? $parser : $this->rli->config('parser');
			$path = $this->root_path.'plugins/raidlogimport/includes/parser/';
			if(is_file($path.$parser.'.parser.class.php')) {
				include_once($path.'parser.aclass.php');
				include_once($path.$parser.'.parser.class.php');
				if($parser::$xml) {
					$log = @simplexml_load_string($log);
					if ($log === false) {
						message_die($this->user->lang('xml_error'));
					}
				}
				$back = $parser::check($log);
				if($back[1]) {
					$this->raid->flush_data();
					$data = $parser::parse($log);
					foreach($data as $type => $ddata) {
						switch($type) {
							case 'zones':
								foreach($ddata as $args) {call_user_func_array(array($this->raid, 'add_zone'), $args);}
								break;
							case 'bosses':
								foreach($ddata as $args) {call_user_func_array(array($this->raid, 'add_bosskill'), $args);}
								break;
							case 'members':
								foreach($ddata as $args) {call_user_func_array(array($this->member, 'add'), $args);}
								break;
							case 'times':
								foreach($ddata as $args) {call_user_func_array(array($this->member, 'add_time'), $args);}
								break;
							case 'items':
								foreach($ddata as $args) {call_user_func_array(array($this->item, 'add'), $args);}
								break;
						}
					}
					$this->raid->create();
					$this->raid->recalc(true);
					$this->member->finish();
				} else {
					message_die(sprintf($this->user->lang('rli_error_wrong_format'), $parser::$name).'<br />'.$this->user->lang('rli_miss').implode(', ', $back[2]));
				}
			} else {
				message_die($this->user->lang('rli_error_no_parser'));
			}
		}
	}
}
?>