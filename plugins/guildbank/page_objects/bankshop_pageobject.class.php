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

class bankshop_pageobject extends pageobject {

	public static function __shortcuts(){
		$shortcuts = array('money' => 'gb_money');
		return array_merge(parent::$shortcuts, $shortcuts);
	}

	private $data = array();

	public function __construct(){
		if (!$this->pm->check('guildbank', PLUGIN_INSTALLED))
			message_die($this->user->lang('guildbank_not_installed'));

		// load the includes
		require_once($this->root_path.'plugins/guildbank/includes/gb_money.class.php');

		$handler = array(
			'save'		=> array('process' => 'save', 'csrf' => true, 'check' => 'u_guildbank_shop'),
			'moderate'	=> array('process' => 'moderate', 'check' => 'a_guildbank_manage'),
		);
		parent::__construct('u_guildbank_shop', $handler, array(), null, '', 'item');
		$this->process();
	}

	// confirm the
	public function moderate(){
		$this->pdh->put('guildbank_transactions', 'confirm_transaction', array($this->in->get('moderate', 0)));
	}

	public function save(){
		if(!$this->url_id || (int)$this->url_id < 1){
			message_die($this->user->lang('gb_no_item_id_missing'));
		}

		$old_amount		= $this->pdh->get('guildbank_items', 'amount', array($this->url_id));
		$amount_temp	= $this->pdh->get('guildbank_shop_ta', 'amount', array($this->url_id));
		$old_amount		= ($amount_temp > 0) ? $old_amount-$amount_temp : $old_amount;
		$amount_buy		= $this->in->get('amount', 1);
		$buyer			= $this->in->get('char', 1);
		$currency		= $this->in->get('currency', 'dkp');
		$charDKP		= $this->pdh->get('points', 'current', array($buyer, $this->in->get('dkppool', 1), 0, 0, false));
		$error			= false;

		if($amount_buy > 0){
			if($old_amount > 0){
				if($currency == 'cash'){
					$item_cost = $this->money->input();
					$this->pdh->put('guildbank_transactions', 'buy_item', array($this->url_id, $buyer, $item_cost, $amount_buy, 2));

					// process the hook queue
					$this->pdh->process_hook_queue();
				}else{
					$item_cost		= $this->in->get('costs', 0);
					// check if the meber has enough DKP
					if($charDKP >= ($amount_buy*$item_cost)){
						// perform the process
						$this->pdh->put('guildbank_transactions', 'buy_item', array($this->url_id, $buyer, $item_cost, $amount_buy));

						// process the hook queue
						$this->pdh->process_hook_queue();
					}else{
						// Error message if not enough DKP
						$error	= $this->user->lang('gb_shop_error_nodkp');
					}
				}

			}else{
				// Error message if amount is too low
				$error	= $this->user->lang('gb_shop_error_noitem');
			}
		}else{
			$error	= $this->user->lang('gb_shop_error_noselection');
		}

		if($error){
			$this->tpl->assign_vars(array(
				'SHOWMESSAGE'	=> true,
				'MSGCOLOR'		=> 'red',
				'MSGICON'		=> 'fa-exclamation-triangle',
				'MSGTEXT'		=> $error,
			));
		}else{
			$this->tpl->assign_vars(array(
				'SHOWMESSAGE'	=> true,
				'MSGCOLOR'		=> 'green',
				'MSGICON'		=> 'fa-exclamation-triangle',
				'MSGTEXT'		=> $this->user->lang('gb_shop_buy_successmsg'),
			));
		}
		$this->display();
	}

	// shop display
	public function display(){
		if(!$this->url_id || (int)$this->url_id < 1){
			message_die($this->user->lang('gb_no_item_id_missing'));
		}

		$amount		= $this->pdh->get('guildbank_items', 'amount', array($this->url_id));
		$dkp		= $this->pdh->get('guildbank_items', 'dkp', array($this->url_id));
		$money		= $this->pdh->get('guildbank_transactions', 'itemvalue', array($this->url_id));
		$dkppools	= $this->pdh->aget('multidkp', 'name', 0, array($this->pdh->get('multidkp', 'id_list')));
		#$points	= $this->pdh->get('points', 'current', array($mainchar, $dkppool));
		#$this->pdh->get('member', 'connection_id', array($user_id));

		// the money stuff
		if($money > 0){
			foreach($this->money->get_data() as $monName=>$monValue){
				$this->tpl->assign_block_vars('money_row', array(
					'NAME'		=> $monName,
					'VALUE'		=> $this->money->output($money, $monValue),
					'IMAGE'		=> $this->money->image($monValue)
				));
			}
		}

		$this->tpl->assign_vars(array(
			'NOSELECTION'		=> ($this->url_id > 0) ? true : false,
			'BUYFORMONEY'		=> ($dkp == 0 && $money > 0) ? true : false,
			'DD_ITEMS'			=> new hdropdown('item', array('options' => $this->pdh->aget('guildbank_items', 'name', 0, array($this->pdh->get('guildbank_items', 'id_list', array(0,0,0,0,1)))), 'value' => $this->url_id, 'id' => 'items_id')),
			'ITEM'				=> $this->pdh->get('guildbank_items', 'name', array($this->url_id)),
			'ITEM_ID'			=> $this->url_id,
			'DD_MYCHARS'		=> new hdropdown('char', array('options' => $this->pdh->aget('member', 'name', 0, array($this->pdh->get('member', 'connection_id', array($this->user->data['user_id'])))))),
			'DD_AMOUNT'			=> new hdropdown('amount', array('options' => (($amount > 0) ? range(0, $amount) : 1), 'value' => 0)),
			'DD_MULTIDKPPOOL'	=> (count($dkppools) > 1) ? new hdropdown('dkppool', array('options' => $dkppools, 'value' => 0)) : new hhidden('dkppool', array('value' => $dkppools[0])),
			'DKP'				=> $dkp,
			'MONEY'				=> $this->money->editfields($money, 'money_{ID}', false, true),
		));

		$this->core->set_vars(array(
 			'page_title'		=> sprintf($this->user->lang('admin_title_prefix'), $this->config->get('guildtag'), $this->config->get('dkp_name')).': '.$this->user->lang('guildbank_title'),
 			'template_path'		=> $this->pm->get_data('guildbank', 'template_path'),
 			'template_file'		=> 'bankshop.html',
 			'display'			=> true,
			'header_format'		=> ($this->in->get('simple_head')) ? 'simple' : 'full',
		));
	}
}
?>
