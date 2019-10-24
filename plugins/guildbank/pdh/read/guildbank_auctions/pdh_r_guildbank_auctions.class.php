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

if (!defined('EQDKP_INC')){
	die('Do not access this file directly.');
}

if (!class_exists('pdh_r_guildbank_auctions')){
	class pdh_r_guildbank_auctions extends pdh_r_generic{
		private $data;

		public $hooks = array(
			'guildbank_auction_update'
		);

		public $presets = array(
			'gb_aname'		=> array('name',			array('%auction_id%', '%itt_lang%', '%itt_direct%', '%onlyicon%', '%noicon%'), array()),
			'gb_astartdate'	=> array('startdate',		array('%auction_id%'), array()),
			'gb_astartvalue'=> array('startvalue',		array('%auction_id%'), array()),
			'gb_aduration'	=> array('duration',		array('%auction_id%'), array()),
			'gb_abidsteps'	=> array('bidsteps',		array('%auction_id%'), array()),
			'gb_anote'		=> array('note',			array('%auction_id%'), array()),
			'gb_aactive'	=> array('active',			array('%auction_id%'), array()),
			'gb_aedit'		=> array('edit',			array('%auction_id%'), array()),
			'gb_aalink'		=> array('auctionlink',		array('%auction_id%'), array()),
			'gb_left_atime'	=> array('atime_left_html',	array('%auction_id%'), array()),
			'gb_ahibidder'	=> array('highest_bidder',	array('%auction_id%'), array()),
			'gb_endvalue'	=> array('highest_value', 	array('%auction_id%'), array()),
		);

		public function reset(){
			$this->pdc->del('pdh_guildbank_auction_table');
			unset($this->data);
		}

		public function init(){
			// try to get from cache first
			$this->data = $this->pdc->get('pdh_guildbank_auction_table');
			if($this->data !== NULL){
				return true;
			}

			// empty array as default
			$this->data = array();

			// read all guildbank_fields entries from db
			$sql = 'SELECT * FROM `__guildbank_auctions` ORDER BY auction_id ASC;';
			$result = $this->db->query($sql);
			if ($result){
				// add row by row to local copy
				while (($row = $result->fetchAssoc())){
					$this->data[(int)$row['auction_id']] = array(
						'id'				=> (int)$row['auction_id'],
						'item'				=> (int)$row['auction_item'],
						'startvalue'		=> (int)$row['auction_startvalue'],
						'startdate'			=> (int)$row['auction_startdate'],
						'duration'			=> (int)$row['auction_duration'],
						'bidsteps'			=> (int)$row['auction_bidsteps'],
						'note'				=> $row['auction_note'],
						'raidattendance'	=> (int)$row['auction_raidattendance'],
						'multidkppool'		=> (int)$row['auction_multidkppool'],
						'active'			=> (int)$row['auction_active'],
					);
				}
			}

			// add data to cache
			$this->pdc->put('pdh_guildbank_auction_table', $this->data, null);
			return true;
		}

		public function get_data($id){
			return (isset($this->data[$id])) ? $this->data[$id] : array();
		}

		public function get_id_list($future = true, $past=false, $active=false, $limitpast=30){
			if (is_array($this->data)){
				$ids		= array_keys($this->data);
				$pastday	= ($this->time->time - ($limitpast*86400));
				// filter future
				foreach($ids as $key => $id) {
					$time_left	= $this->get_atime_left($id);
					$start_time	= $this->get_startdate($id);
					$not_active	= ($this->get_active($id) == 0) ? true : false;

					// do the stuff
					if($future && !$past && !$active && $time_left == 0){
						unset($ids[$key]);
					}elseif($active && $not_active){
						unset($ids[$key]);
					}elseif(!$future && $past && !$active && ($start_time < $pastday)){
						unset($ids[$key]);
					}elseif($future && $past && !$active && ($start_time < $pastday)){
						unset($ids[$key]);
					}
				}
				return $ids;
			}
			return array();
		}

		function get_unapproved_auctions(){
			if (is_array($this->data)){
				$ids		= array_keys($this->data);

				// filter future
				foreach($ids as $key => $id) {
					$time_left		= $this->get_atime_left($id);
					$not_active		= ($this->get_active($id) == 0) ? true : false;
					$highest_value	= $this->get_highest_value($id);

					if($not_active || $time_left > 0 || $highest_value == 0){
						unset($ids[$key]);
					}
				}
				return $ids;
			}
			return array();
		}

		function get_count_active_auction(){
			$auctions = $this->get_id_list(true);
			return (count($auctions) > 0) ? count($auctions) : 0;
		}

		public function get_note($id){
			return (isset($this->data[$id]) && $this->data[$id]['note']) ? $this->data[$id]['note'] : '';
		}

		public function get_startdate($id){
			if(isset($this->data[$id]) && $this->data[$id]['startdate']){
				return $this->data[$id]['startdate'];
			}
			return 0;
		}

		public function get_html_startdate($id){
			if($this->get_startdate($id) > 0){
				return $this->time->user_date($this->data[$id]['startdate'], true, false, true);
			}
			return '--';
		}

		public function get_auction_byitem($itemID){
			return 0;
		}

		public function get_duration($id){
			return (isset($this->data[$id]) && $this->data[$id]['duration']) ? (int)$this->data[$id]['duration'] : 0;
		}

		public function get_enddate($id){
			$startdate	= $this->get_startdate($id, true);
			$duration	= $this->get_duration($id);
			$duration	= $duration*3600;
			return ($startdate > 0 && $duration > 0) ? $startdate + $duration : 0;
		}

		public function get_atime_left($id){
			// calculate the time left
			$startts	= $this->get_startdate($id);
			$duration	= (isset($this->data[$id]) && $this->data[$id]['duration'] && (int)$this->data[$id]['duration'] > 0) ? ((int)$this->data[$id]['duration'])*3600 : 0;
			$now		= $this->time->time;

			if($duration > 0){
				$end	= $startts+$duration;
				return ($end > $now) ? $end - $now : 0;
			}
			return 0;
		}

		private function format_time($t,$f=':'){
			return sprintf("%02d%s%02d%s%02d", floor($t/3600), $f, ($t/60)%60, $f, $t%60);
		}

		public function get_atime_left_html($id){
			return ($this->get_atime_left($id) > 0) ? '<span class="dyn_auctiontime" data-endtime="'.$this->get_enddate($id).'"><i class="fa fa-refresh fa-spin"></i> '.$this->user->lang('gb_bids_loading').'</span>' : '<i class="fa fa-check-circle"></i> '.$this->user->lang('gb_bids_auctionended');
		}

		// the time left using momentJS
		public function get_counterJS(){
			$this->tpl->add_js('function optimize_time_output(n){return ((n) < 10 ? "0" : "") + n;}');
			$this->tpl->add_js("
				$('.dyn_auctiontime').each(function(){
					var endTime		= $(this).data('endtime');
					var currentTime	= moment().unix();
					var diffTime	= endTime-currentTime;
					console.log('end: ' + endTime)
					console.log('current: ' + currentTime)
					console.log('diff: '+diffTime);
					var thisdata	= $(this);
					var interval	= 1000;
					var duration	= moment.duration(diffTime*1000, 'milliseconds');

					if(diffTime > 0){
						setInterval(function(){
							duration	= moment.duration(duration - interval, 'milliseconds');
							thisdata.text(optimize_time_output(Math.floor(duration.asHours())) + ':' + optimize_time_output(duration.minutes()) + ':' + optimize_time_output(duration.seconds()));
						}, interval);
					}else{
						thisdata.html('<i class=\"fa fa-check-circle\"></i> ".$this->user->lang('gb_bids_auctionended')."');
					}
				});
				", 'docready');
		}

		public function get_bidsteps($id){
			return (isset($this->data[$id]) && $this->data[$id]['bidsteps']) ? $this->data[$id]['bidsteps'] : 0;
		}

		public function get_startvalue($id){
			return (isset($this->data[$id]) && $this->data[$id]['startvalue']) ? $this->data[$id]['startvalue'] : 0;
		}

		public function get_raidattendance($id){
			return (isset($this->data[$id]) && $this->data[$id]['raidattendance']) ? $this->data[$id]['raidattendance'] : 0;
		}

		public function get_active($id){
			return (isset($this->data[$id]) && $this->data[$id]['active']) ? $this->data[$id]['active'] : 0;
		}

		public function get_multidkppool($id){
			return (isset($this->data[$id]) && $this->data[$id]['multidkppool']) ? $this->data[$id]['multidkppool'] : 0;
		}

		public function get_edit($id){
			return '<a href="javascript:edit_auction(\''.$id.'\');"><i class="fa fa-pencil fa-lg" title="'.$this->user->lang('edit').'"></i></a>';
		}

		public function get_auctionlink($id){
			if($this->user->check_auth('u_guildbank_auction', false)){
				return '<a href="'.$this->routing->build('guildauction').'&auction='.$id.'"><i class="fa fa-gavel fa-lg" title="'.$this->user->lang('gb_auction_icon_title').'"></i></a>';
			}
		}

		public function get_itemid($id){
			return (isset($this->data[$id]) && $this->data[$id]['item']) ? $this->data[$id]['item'] : 0;
		}

		public function get_name($id){
			$itemid	= $this->get_itemid($id);
			return ($itemid > 0) ? $this->pdh->get('guildbank_items', 'name', array($itemid)) : 0;
		}

		public function get_itt_itemname($id, $lang=false, $direct=0, $onlyicon=0, $noicon=false, $in_span=false) {
			if(!isset($this->data[$id])) return false;
			if($this->config->get('infotooltip_use')) {
				$lang = (!$lang) ? $this->user->lang('XML_LANG') : $lang;
				$ext = '';
				if($direct) {
					$options = array(
						'url' => $this->root_path."infotooltip/infotooltip_feed.php?name=".urlencode(base64_encode($this->get_name($id)))."&lang=".$lang."&update=1&direct=1",
						'height' => '340',
						'width' => '400',
						'onclose' => $this->env->request
					);
					$this->jquery->Dialog("infotooltip_update", "Item-Update", $options);
					$ext = '<span style="cursor:pointer;" onclick="infotooltip_update()">Refresh</span>';
				}
				return infotooltip($this->get_name($id), 0, $lang, $direct, $onlyicon, $noicon, '', false, false, $in_span, false).$ext;
			}
			return $this->get_name($id);
		}

		public function get_html_name($item_id, $lang=false, $direct=0, $onlyicon=0, $noicon=false, $in_span=false) {
			return $this->get_itt_itemname($item_id, $lang, $direct, $onlyicon, $noicon, $in_span);
		}

		public function get_highest_bidder($id, $raw=false){
			return $this->pdh->get('guildbank_auction_bids', 'highest_bidder', array($id, $raw, true));
		}

		public function get_auctionwinner($auctionID){
			return max($this->get_highest_bidder($auctionID, true));
		}

		public function get_highest_value($id){
			$bidvalue = $this->pdh->get('guildbank_auction_bids', 'highest_value', array($id));
			return ($bidvalue > 0) ? $bidvalue : $this->user->lang('gb_bids_nobids');
		}

		public function get_amount_bids($id){
			return $this->pdh->get('guildbank_auction_bids', 'amount_bids', array($id));
		}
	} //end class
} //end if class not exists
?>
