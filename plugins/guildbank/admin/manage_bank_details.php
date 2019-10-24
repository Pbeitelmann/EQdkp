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

class Manage_BankDetails extends page_generic {
	public static function __shortcuts() {
		$shortcuts = array('money' => 'gb_money');
		return array_merge(parent::$shortcuts, $shortcuts);
	}

	public function __construct(){
		$this->user->check_auth('a_guildbank_manage');
		$handler = array(
			'save'				=> array('process' => 'save',			'csrf'=>true),
			'perform_payout'	=> array('process' => 'perform_payout',	'csrf'=>true),
			'addedit'			=> array('process' => 'display_add'),
			'payout'			=> array('process' => 'display_payout'),
		);
		parent::__construct(false, $handler, array('guildbank_items', 'deletename'), null, 'selections[]');
		$this->process();
	}

	public function save() {
		$retu		= array();
		$edit		= $this->in->get('editmode', 0);
		$mode		= $this->in->get('mode', 0);
		$char		= $this->in->get('char', 0);
		$func		= ($edit > 0 && ($mode == 0 && $this->in->get('item', 0) > 0) || ($mode == 1 && $this->in->get('transaction', 0) > 0)) ? 'update' : 'add';

		// transactions
		if($mode == 1){
			$money		= $this->money->input();
			$retu		= $this->pdh->put('guildbank_transactions', $func, array(
				//$intID, $intBanker, $intChar, $intItem, $intDKP, $intValue, $strSubject
				$this->in->get('transaction', 0), $this->in->get('banker', 0), $char, 0, 0, $money, $this->in->get('subject', '')
			));

		// items
		}else{
			$money		= $this->money->input(false, 'money2_{ID}');
			$retu		= $this->pdh->put('guildbank_items', $func, array(
			//$intID, $strBanker, $strName, $intRarity, $strType, $intAmount, $intDKP, $intMoney, $intChar, $intSellable=0, $strSubject='gb_item_added'
			$this->in->get('item', 0), $this->in->get('banker', 0), $this->in->get('name', ''), $this->in->get('rarity', 0), $this->in->get('type', ''), $this->in->get('amount', 0), $this->in->get('dkp', 0), $money, $char, $this->in->get('sellable', 0)));
		}

		if($retu) {
			$message = array('title' => $this->user->lang('save_nosuc'), 'text' => implode(', ', $names), 'color' => 'red');
		} elseif(in_array(true, $retu)) {
			$message = array('title' => $this->user->lang('save_suc'), 'text' => implode(', ', $names), 'color' => 'green');
		}
		$this->pdh->process_hook_queue();

		// close the dialog
		$this->tpl->add_js('jQuery.FrameDialog.closeDialog();');
	}

	public function perform_payout(){
		$buyer		= $this->in->get('char', 0);
		$item		= $this->in->get('item', 0);
		$amount		= $this->in->get('amount', 0);
		$dkp		= -$this->in->get('dkp', 0);
		$money		= $this->money->input();

		if($buyer > 0 && $item > 0){
			// calculate the new amount
			$amount_new	= $this->pdh->get('guildbank_items', 'amount', array($item)) - $amount;

			// add the transaction
			$retu		= $this->pdh->put('guildbank_transactions', 'add', array(
				//$intID, $intBanker, $intChar, $intItem, $intDKP, $intValue, $strSubject
				$this->in->get('transaction', 0), $this->in->get('banker', 0), $buyer, $item, $dkp, $money, 'gb_item_payout'
			));

			// reduce amount of items
			$this->pdh->put('guildbank_items', 'amount', array($item, $amount_new));

			// add a auto correction here...
			if($this->config->get('use_autoadjust', 'guildbank') > 0 && $this->config->get('default_event', 'guildbank') > 0){
				//add_adjustment($adjustment_value, $adjustment_reason, $member_ids, $event_id, $raid_id=NULL, $time=false, $group_key = null)
				$this->pdh->put('adjustment', 'add_adjustment', array($dkp, $this->user->lang('gb_adjustment_text'), $buyer, $this->config->get('default_event', 'guildbank')));
			}
			$this->pdh->process_hook_queue();

			// close the dialog
			$this->tpl->add_js('jQuery.FrameDialog.closeDialog();');
		}
	}

	public function delete() {
		$tmp_ids	= $this->in->getArray('selections');
		$first_id	= explode("_", $tmp_ids[0]);
		if($first_id[0] == 'transaction'){
			$banker_id	= (isset($first_id[1]) && $first_id[1] > 0) ? $this->pdh->get('guildbank_transactions', 'banker', array($first_id[1], true)) : false;
		}else{
			$banker_id	= (isset($first_id[1]) && $first_id[1] > 0) ? $this->pdh->get('guildbank_items', 'banker', array($first_id[1])) : false;
		}

		if(is_array($tmp_ids) && count($tmp_ids) > 0){
			// now, lets get the information..
			foreach($tmp_ids as $id){
				$tmp_id		= explode("_", $id);
				$real_id	= $tmp_id[1];
				$type		= $tmp_id[0];

				// perform the delete-process
				if($real_id > 0 && $type != ''){
					$pdh_module	= ($type == 'transaction') ? 'guildbank_transactions' : 'guildbank_items';
					$names[] = $this->pdh->get('guildbank_items', 'deletename', ($id));

					// now, delete it!
					$retu[] = $this->pdh->put($pdh_module, 'delete', array($real_id));
				}

				// the message
				if(in_array(false, $retu)) {
					$message = array('title' => $this->user->lang('del_no_suc'), 'text' => implode(', ', $names), 'color' => 'red');
				}else{
					$message = array('title' => $this->user->lang('del_suc'), 'text' => implode(', ', $names), 'color' => 'green');
				}
			}
		}else{
			$message = array('title' => '', 'text' => $this->user->lang('no_calendars_selected'), 'color' => 'grey');
		}
		$this->pdh->process_hook_queue();
		$this->display($message, $banker_id);
	}

	public function display($messages=false, $banker = false){
		$bankerID 		= ($banker > 0) ? $banker : $this->in->get('g', 0);
		$banker_name	= $this->pdh->get('guildbank_banker', 'name', array($bankerID));

		//init infotooltip
		infotooltip_js();
		$this->money->loadMoneyClass();
		require_once($this->root_path.'plugins/guildbank/includes/systems/guildbank.esys.php');

		// -- display entries ITEMS ------------------------------------------------
		$view_items		= $this->pdh->get('guildbank_items', 'id_list', array($bankerID));
		$hptt_items		= $this->get_hptt($systems_guildbank['pages']['hptt_guildbank_admin_items'], $view_items, $view_items, array('%itt_lang%' => false, '%itt_direct%' => 0, '%onlyicon%' => 0, '%noicon%' => 0), 'admin_i'.$bankerID, 'isort');
		$page_suffix	= '&amp;start='.$this->in->get('start', 0).'&amp;g='.$bankerID.'#fragment-items';
		$sort_suffix_i	= '&amp;isort='.$this->in->get('isort');
		$item_count		= count($view_items);
		$item_footer	= sprintf($this->user->lang('gb_footer_item'), $item_count, $this->user->data['user_ilimit']);

		// -- display entries TRANSACTIONS -----------------------------------------
		$ta_list		= $this->pdh->get('guildbank_transactions', 'id_list', array($bankerID));
		$hptt_transa	= $this->get_hptt($systems_guildbank['pages']['hptt_guildbank_admin_transactions'], $ta_list, $ta_list, array('%itt_lang%' => false, '%itt_direct%' => 0, '%onlyicon%' => 0, '%noicon%' => 0), 'admin_ta'.$bankerID, 'tsort');
		$ta_count		= count($ta_list);
		$page_suffix_ta	= '&amp;tastart='.$this->in->get('tastart', 0).'&amp;g='.$bankerID.'#fragment-transactions';
		$sort_suffix_ta	= '&amp;tsort='.$this->in->get('tsort');
		$footer_transa	= sprintf($this->user->lang('gb_footer_transaction'), $ta_count, $this->user->data['user_ilimit']);

		// start ouptut
		$this->jquery->Tab_header('guildbank_tab', true);

		// build the url for the dialogs
		$redirect_url		= 'manage_bank_details.php'.$this->SID.'&g='.$bankerID.'&details=true';
		$transactions_url	= 'manage_bank_details.php'.$this->SID.'&simple_head=true&addedit=true&g='.$bankerID;
		$payout_url			= 'manage_bank_details.php'.$this->SID.'&simple_head=true&g='.$bankerID;

		$this->jquery->dialog('add_transaction', $this->user->lang('gb_manage_bank_transa'), array('url' => $transactions_url.'&mode=1', 'width' => 600, 'height' => 450, 'onclose'=> $redirect_url));
		$this->jquery->dialog('edit_transaction', $this->user->lang('gb_manage_bank_transa'), array('url' => $transactions_url."&mode=1&t='+id+'", 'width' => 600, 'height' => 450, 'onclose'=> $redirect_url, 'withid' => 'id'));
		$this->jquery->dialog('add_item', $this->user->lang('gb_ta_head_item'), array('url' => $transactions_url.'&mode=0', 'width' => 600, 'height' => 580, 'onclose'=> $redirect_url));
		$this->jquery->dialog('edit_item', $this->user->lang('gb_ta_head_item'), array('url' => $transactions_url."&mode=0&i='+id+'", 'width' => 600, 'height' => 580, 'onclose'=> $redirect_url, 'withid' => 'id'));
		$this->jquery->dialog('payout_item', $this->user->lang('gb_ta_head_payout'), array('url' => $payout_url."&payout=true", 'width' => 600, 'height' => 400, 'onclose'=> $redirect_url));

		$this->confirm_delete($this->user->lang('confirm_delete_items'));
		$this->tpl->assign_vars(array(
			'BANKID'				=> $bankerID,
			'SID'					=> $this->SID,

			'BANKNAME'				=> $this->pdh->get('guildbank_banker', 'name', array($bankerID)),

			'ITEM_LIST'				=> $hptt_items->get_html_table($this->in->get('isort'), $page_suffix, $this->in->get('start', 0), $this->user->data['user_ilimit'], $item_footer),
			'PAGINATION_ITEMS'		=> generate_pagination('manage_bank_details.php'.$this->SID.$sort_suffix_i.'#fragment-items', $item_count, $this->user->data['user_ilimit'], $this->in->get('start', 0)),
			'ITEMS_COLUMN_COUNT'	=> $hptt_items->get_column_count(),

			'TRANSA_LIST'			=> $hptt_transa->get_html_table($this->in->get('tsort'), $page_suffix_ta, $this->in->get('tastart', 0), $this->user->data['user_ilimit'], $footer_transa),
			'TRANSA_PAGINATION'		=> generate_pagination('manage_bank_details.php'.$this->SID.'&g='.$bankerID.$sort_suffix_ta, $ta_count, $this->user->data['user_ilimit'], $this->in->get('tastart', 0), 'tastart'),
			'TRANSA_COLUMN_COUNT'	=> $hptt_transa->get_column_count(),
			'L_BC_CURRENTPAGE'		=> sprintf($this->user->lang('gb_manage_bank_items_title'), $banker_name),
		));

		$this->core->set_vars(array(
			'page_title'		=> sprintf($this->user->lang('gb_manage_bank_items_title'), $banker_name),
			'template_file'		=> 'admin/manage_banker_items.html',
			'template_path'		=> $this->pm->get_data('guildbank', 'template_path'),
			'display'			=> true)
		);
	}

	// ---------------------------------------------------------
	// Displays add/edit item dialog
	// ---------------------------------------------------------
	public function display_add(){
		$bankerID		= $this->in->get('g', 0);
		$itemID			= $this->in->get('i', 0);
		$transactionID	= $this->in->get('t', 0);
		$mode_select	= $this->in->get('mode', 0);
		$edit_charID	= $this->pdh->get('guildbank_transactions', 'char', array(0));
		$edit_mode		= false;
		$edit_charID	= 0;
		$money			= 0;
		$item_sellable	= 0;

		if($itemID > 0){
			$mode_select		= 0;
			$edit_mode			= true;
			$item_sellable		= $this->pdh->get('guildbank_items', 'sellable', array($itemID));
			$edit_bankid		= $this->pdh->get('guildbank_items', 'banker', array($itemID));
			$money				= $this->pdh->get('guildbank_transactions', 'money_summ', array($edit_bankid));
			$edit_charID		= $this->pdh->get('guildbank_transactions', 'char', array($this->pdh->get('guildbank_transactions', 'transaction_id', array($itemID))));
		}elseif($transactionID > 0){
			$mode_select		= 1;
			$edit_mode			= true;
			$money				= $this->pdh->get('guildbank_transactions', 'value', array($transactionID, true));
			$edit_charID		= $this->pdh->get('guildbank_transactions', 'char', array($transactionID, true));
		}

		$rarity			= $this->pdh->get('guildbank_items', 'rarity', array($itemID, true));
		$type			= $this->pdh->get('guildbank_items', 'type', array($itemID, true));
		$this->tpl->assign_vars(array(
			'S_EDIT'		=> $edit_mode,
			'EDITMODE'		=> ($edit_mode) ? '1' : '0',
			'S_TRANSACT'	=> ($mode_select == '1') ? true : false,
			'MODE'			=> $mode_select,
			'ITEMID'		=> $itemID,
			'TAID'			=> $transactionID,
			'MONEY_TRANS'	=> $this->money->editfields($money, 'money_{ID}', true),
			'MONEY_ITEM'	=> $this->money->editfields($money, 'money2_{ID}'),
			'DD_RARITY'		=> new hdropdown('rarity', array('options' => $this->pdh->get('guildbank_items', 'itemrarity'), 'value' => (($itemID > 0) ? $rarity : ''))),
			'DD_TYPE'		=> new hdropdown('type', array('options' => $this->pdh->get('guildbank_items', 'itemtype'), 'value' => $type)),
			'V_SUBJECT'		=> ($itemID > 0) ? $this->pdh->get('guildbank_transactions', 'subject', array($transactionID)) : '',
			'ITEM'			=> new htext('name', array('value' => (($itemID > 0) ? $this->pdh->get('guildbank_items', 'name', array($itemID)) : ''), 'size' => '40', 'autocomplete' => $this->pdh->aget('item', 'name', 0, array($this->pdh->get('item', 'id_list'))))),
			'AMOUNT'		=> ($itemID > 0) ? $this->pdh->get('guildbank_items', 'amount', array($itemID)) : 0,
			'DKP'			=> ($itemID > 0) ? $this->pdh->get('guildbank_transactions', 'dkp', array($itemID)) : 0,
			'AUCTIONTIME'	=> ($itemID > 0) ? $this->pdh->get('guildbank_items', 'auctiontime', array($edit_bankid)) : 48,
			'BANKERID'		=> ($bankerID > 0) ? $bankerID : $this->pdh->get('guildbank_items', 'banker', array($itemID)),
			'MS_MEMBERS'	=> new hdropdown('char', array('options' => $this->pdh->aget('member', 'name', 0, array($this->pdh->get('member', 'id_list'))), 'value' => $edit_charID)),
			'DD_MODE'		=> new hdropdown('mode', array('options' => $this->user->lang('gb_a_mode'), 'value' => $mode_select, 'id' => 'selectmode', 'disabled' => $edit_mode)),
			'R_SELLABLE'	=> new hradio('sellable', array('value' => $item_sellable, 'options' => array('0'=>$this->user->lang('no'),'1'=>$this->user->lang('yes')))),
		));

		$this->core->set_vars(array(
			'page_title'		=> ($itemID > 0) ? $this->user->lang('gb_edit_item_title') : $this->user->lang('gb_add_item_title'),
			'template_file'		=> 'admin/manage_banker_add_items.html',
			'template_path'		=> $this->pm->get_data('guildbank', 'template_path'),
			'header_format'		=> ($this->in->get('simple_head')) ? 'simple' : 'full',
			'display'			=> true)
		);
	}

	public function display_payout(){
		$itemID	= 0;
		if($itemID > 0){
			$bankerID		= $this->pdh->get('guildbank_items', 'banker', array($itemID));
			$edit_charID	= 0;
		}else{
			$bankerID		= $this->in->get('g', 0);
			$edit_charID	= 0;
		}
		$money			= $this->pdh->get('guildbank_transactions', 'money_summ', array($bankerID));
		$this->tpl->assign_vars(array(
			'MONEY'			=> $this->money->editfields($money),
			'AMOUNT'		=> ($itemID > 0) ? $this->pdh->get('guildbank_items', 'amount', array($itemID)) : 0,
			'DKP'			=> ($itemID > 0) ? $this->pdh->get('guildbank_transactions', 'dkp', array($edit_bankid)) : 0,
			'BANKERID'		=> ($bankerID > 0) ? $bankerID : $this->pdh->get('guildbank_items', 'banker', array($itemID)),
			'DD_ITEMS'		=> new hdropdown('item', array('options' => $this->pdh->aget('guildbank_items', 'name', 0, array($this->pdh->get('guildbank_items', 'id_list', array($bankerID)))), 'value' => 0)),
			'DD_CHARS'		=> new hdropdown('char', array('options' => $this->pdh->aget('member', 'name', 0, array($this->pdh->get('member', 'id_list'))), 'value' => $edit_charID)),
		));

		$this->core->set_vars(array(
			'page_title'		=> ($itemID > 0) ? $this->user->lang('gb_edit_item_title') : $this->user->lang('gb_add_item_title'),
			'template_file'		=> 'admin/manage_banker_payout_items.html',
			'template_path'		=> $this->pm->get_data('guildbank', 'template_path'),
			'header_format'		=> ($this->in->get('simple_head')) ? 'simple' : 'full',
			'display'			=> true)
		);
	}
}
registry::register('Manage_BankDetails');
?>
