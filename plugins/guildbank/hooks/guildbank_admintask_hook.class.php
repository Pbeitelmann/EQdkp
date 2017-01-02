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
	header('HTTP/1.0 404 Not Found');exit;
}

/*+----------------------------------------------------------------------------
  | guildrequest_search_hook
  +--------------------------------------------------------------------------*/
if (!class_exists('guildbank_admintask_hook')) {
	class guildbank_admintask_hook extends gen_class{

		/**
		* hook_init
		* blablabla
		*
		* @return array
		*/
		public function admin_tasks(){
			return array(
				'gb_confirmTA'	=> array(
					'name'			=> 'gb_confirm_shop_ta_head',
					'icon'			=> 'fa fa-check',
					'notify_func'	=> array($this, 'admintask_shopTA_ntfy'),
					'content_func'	=> array($this, 'admintask_shopTA_content'),
					'action_func'	=> array($this, 'admintask_shopTA_handle'),
					'actions'		=> array(
						'confirm'	=> array('icon' => 'fa fa-check', 'title' => 'gb_confirm_shop_ta_button', 'permissions' => array('a_guildbank_manage')),
						'delete'	=> array('icon' => 'fa-times', 'title' => 'gb_decline_shop_ta_button', 'permissions' => array('a_guildbank_manage')),
					),
				),

				//
				'gb_confirm_auction'	=> array(
					'name'			=> 'gb_confirm_auction_ta_head',
					'icon'			=> 'fa fa-check',
					'notify_func'	=> array($this, 'admintask_auctionTA_ntfy'),
					'content_func'	=> array($this, 'admintask_auctionTA_content'),
					'action_func'	=> array($this, 'admintask_auctionTA_handle'),
					'actions'		=> array(
						'confirm'	=> array('icon' => 'fa fa-check', 'title' => 'gb_confirm_auction_ta_button', 'permissions' => array('a_guildbank_manage')),
						'delete'	=> array('icon' => 'fa-times', 'title' => 'gb_decline_auction_ta_button', 'permissions' => array('a_guildbank_manage')),
					),
				),
			);
		}

		public function admintask_auctionTA_ntfy(){
			$confirm		= $this->pdh->get('guildbank_auctions', 'unapproved_auctions');
			if (count($confirm) > 0){
				return array(array(
					'type'		=> 'gb_notify_auctionta',
					'count'		=> count($confirm),
					'msg'		=> (count($confirm) > 1) ? sprintf($this->user->lang('gb_notify_shopta_confirm_req2'), count($confirm)) : $this->user->lang('gb_notify_shopta_confirm_req1'),
					'icon'		=> 'fa-gavel',
					'prio'		=> 1,
				));
			}
			return array();
		}

		public function admintask_auctionTA_handle($strAction, $arrIDs, $strTaskID){
			if ($strAction == 'confirm'){
				if (count($arrIDs)){
					foreach($arrIDs as $ta_id){
						$this->pdh->put('guildbank_transactions', 'confirm_auctionta', array((int)$ta_id));
					}
					$this->pdh->process_hook_queue();
					$this->core->message($this->user->lang('gb_confirm_msg_success'), $this->user->lang('success'), 'green');
				}
			}

			if($strAction == 'delete'){
				if (count($arrIDs)){
					foreach($arrIDs as $ta_id){
						$this->pdh->put('guildbank_auctions', 'set_inactive', array((int)$ta_id));
					}
					$this->pdh->process_hook_queue();
					$this->core->message($this->user->lang('gb_confirm_msg_delete'), $this->user->lang('success'),'green');

				}
			}
		}

		public function admintask_auctionTA_content(){
			$confirm		= $this->pdh->get('guildbank_auctions', 'unapproved_auctions');
			$arrContent		= array();
			if (count($confirm) > 0){
				foreach ($confirm as $transaction){
					$arrContent[]	= array(
							'id'					=> $transaction,
							'gb_item_name'			=> $this->pdh->get('guildbank_auctions',	'html_name',			array($transaction)),
							'gb_auction_startdate'	=> $this->pdh->get('guildbank_auctions',	'startdate',			array($transaction)),
							'gb_auction_winner'		=> $this->pdh->get('guildbank_auctions',	'highest_bidder',		array($transaction)),
							'gb_auction_price'		=> $this->pdh->get('guildbank_auctions',	'highest_value',		array($transaction)),
							'gb_auction_amountbids'	=> $this->pdh->get('guildbank_auctions',	'amount_bids',			array($transaction)),
					);
				}
			}
			return $arrContent;
		}

		public function admintask_shopTA_content(){
			$arrContent		= array();
			require_once($this->root_path.'plugins/guildbank/includes/gb_money.class.php');
			$this->money	= new gb_money();

			//Confirm item transactions
			$confirm		= $this->pdh->get('guildbank_shop_ta', 'id_list');
			if (count($confirm) > 0){
				$nothing	= false;
				foreach ($confirm as $transaction){
					$currency	= $this->pdh->get('guildbank_shop_ta',	'currency',	array($transaction));
					$value		= $this->pdh->get('guildbank_shop_ta',	'value',	array($transaction));
					$value_out	= $value;
					if($currency == 2){
						$value_out = '';
						foreach($this->money->get_data() as $monName=>$monValue){
							$value_out .= $this->money->image($monValue).' '.$this->money->output($value, $monValue).'  ';
						}
					}
					$arrContent[]	= array(
							'id'			=> $transaction,
							'gb_item_name'	=> $this->pdh->get('guildbank_shop_ta',	'item',		array($transaction)),
							'gb_amount'		=> $this->pdh->get('guildbank_shop_ta',	'amount',	array($transaction)),
							'gb_item_date'	=> $this->pdh->get('guildbank_shop_ta',	'html_date',array($transaction)),
							'gb_item_value'	=> $value_out,
							'buyer'			=> $this->pdh->get('guildbank_shop_ta',	'buyer',	array($transaction)),
					);
				}
			}
			return $arrContent;
		}

		public function admintask_shopTA_ntfy(){
			$confirm		= $this->pdh->get('guildbank_shop_ta', 'id_list');
			if (count($confirm) > 0){
				return array(array(
					'type'		=> 'gb_notify_shopta',
					//'count'		=> count($confirm),
					'count'		=> 1,
					'msg'		=> (count($confirm) > 1) ? sprintf($this->user->lang('gb_notify_shopta_confirm_req2'), count($confirm)) : $this->user->lang('gb_notify_shopta_confirm_req1'),
					'icon'		=> 'fa-shopping-cart',
					'prio'		=> 1,
				));
			}
			return array();
		}

		public function admintask_shopTA_handle($strAction, $arrIDs, $strTaskID){
			if ($strAction == 'confirm'){
				if (count($arrIDs)){
					foreach($arrIDs as $ta_id){
						$this->pdh->put('guildbank_transactions', 'confirm_itemta', array((int)$ta_id));
					}
					$this->pdh->process_hook_queue();
					$this->core->message($this->user->lang('gb_confirm_msg_success'), $this->user->lang('success'), 'green');
				}
			}

			if($strAction == 'delete'){
				if (count($arrIDs)){
					foreach($arrIDs as $ta_id){
						$this->pdh->put('guildbank_transactions', 'delete_itemta', array((int)$ta_id));
					}
					$this->pdh->process_hook_queue();
					$this->core->message($this->user->lang('gb_confirm_msg_delete'), $this->user->lang('success'),'green');

				}
			}
		}
	}
}

?>
