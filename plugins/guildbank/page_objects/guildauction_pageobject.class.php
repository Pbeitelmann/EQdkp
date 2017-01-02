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

class guildauction_pageobject extends pageobject {

	private $data = array();

	public function __construct(){
		if (!$this->pm->check('guildbank', PLUGIN_INSTALLED))
			message_die($this->user->lang('guildbank_not_installed'));

		$handler = array(
			'bid'		=> array('process' => 'perform_bid', 'csrf' => true, 'check' => 'u_guildbank_auction'),
		);
		parent::__construct('u_guildbank_auction', $handler, array(), null, '', 'auction');
		$this->process();
	}

	public function perform_bid(){
		$intMemberID	= $this->in->get('memberid', 0);
		$intBidValue	= $this->in->get('bidvalue', 0);
		$intMDKPID		= $this->pdh->get('guildbank_auctions', 'multidkppool', array($this->url_id));
		$intCurrDKP		= $this->pdh->get('points', 'current', array($intMemberID, $intMDKPID, 0, 0, false));
		$intAttendance	= $this->pdh->get('guildbank_auctions', 'raidattendance', array($this->url_id));

		// check if the meber has enough DKP
		$bid_allowed	= ($intCurrDKP >= $intBidValue) ? true : false;

		// now, check if the other requirements are met
		if($bid_allowed){
			if($intAttendance > 0){
				$intItemName	= $this->pdh->get('guildbank_auctions', 'name', array($this->url_id));
				$intItemIDs		= $this->pdh->get('item', 'ids_by_name', array($intItemName));
				$intItems		= $this->pdh->get('raid', 'raidids4memberid_item', array($intMemberID, $intItemIDs));
				$bid_allowed	= ($intAttendance >= count($intItems)) ? true : false;
			}
		}

		// perform the process
		if($bid_allowed && $intMemberID > 0){
			$this->pdh->put('guildbank_auction_bids', 'add', array($this->url_id, $this->time->time, $intMemberID, $intBidValue));
		}
		$this->pdh->process_hook_queue();
		$this->display();
	}

	// shop display
	public function display(){
		require_once($this->root_path.'plugins/guildbank/includes/systems/guildbank.esys.php');

		$bid_list		= $this->pdh->get('guildbank_auction_bids', 'id_list', array($this->url_id));
		$hptt_bids		= $this->get_hptt($systems_guildbank['pages']['hptt_guildbank_bids'], $bid_list, $bid_list, array(), 'bids'.$this->url_id);
		$page_suffix	= '&amp;start='.$this->in->get('start', 0);
		$sort_suffix	= '&amp;sort='.$this->in->get('sort');
		$bids_count		= count($bid_list);
		$footer_bids	= sprintf($this->user->lang('gb_bids_footcount'), $bid_list, $this->user->data['user_ilimit']);

		// data
		$dkppool		= $this->pdh->get('guildbank_auctions', 'multidkppool', array($this->url_id));
		$actual_bid		= $this->pdh->get('guildbank_auction_bids', 'highest_value', array($this->url_id));
		$mainchar		= $this->pdh->get('member', 'mainchar', array($this->user->data['user_id']));
		$points			= $this->pdh->get('points', 'current', array($mainchar, $dkppool, 0, 0, false));
		$bidsteps		= $this->pdh->get('guildbank_auctions', 'bidsteps', array($this->url_id));
		$bidspinner		= ((int)$actual_bid > 0) ? $actual_bid+$bidsteps : $this->pdh->get('guildbank_auctions', 'startvalue', array($this->url_id));

		$this->pdh->get('guildbank_auctions', 'counterJS');
		$this->tpl->assign_vars(array(
			'ROUTING_BANKER'	=> $this->routing->build('guildbank'),
			'ERROR_WARNING'		=> (!$this->url_id || !$this->user->is_signedin()) ? true : false,
			'DD_MYCHARS'		=> new hdropdown('memberid', array('value' => $mainchar, 'options' => $this->pdh->aget('member', 'name', 0, array($this->pdh->get('member', 'connection_id', array($this->user->data['user_id'])))))),
			'MY_DKPPOINTS'		=> $points.' '.$this->config->get('dkp_name'),
			'BID_SPINNER'		=> new hspinner('bidvalue', array('value' => $bidspinner, 'step'=> 10, 'min' => $bidspinner, 'max' => $points, 'onlyinteger' => true)),
			'TIMELEFT'			=> $this->pdh->get('guildbank_auctions', 'atime_left_html', array($this->url_id)),

			'BIDS_TABLE'		=> $hptt_bids->get_html_table($this->in->get('sort'), $page_suffix, $this->in->get('start', 0), $this->user->data['user_ilimit'], $footer_bids),
			'PAGINATION_BIDS'	=> generate_pagination($this->routing->build('guildauction').$sort_suffix, $bids_count, $this->user->data['user_ilimit'], $this->in->get('start', 0)),
		));

		$this->core->set_vars(array(
			'page_title'		=> $this->user->lang('gb_auction_window'),
			'template_path'		=> $this->pm->get_data('guildbank', 'template_path'),
			'template_file'		=> 'auction.html',
			'display'			=> true,
		));
	}
}
?>
