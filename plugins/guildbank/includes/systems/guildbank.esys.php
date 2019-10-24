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

$systems_guildbank = array(
	'pages' => array(
		'hptt_guildbank_items' => array(
			'name'					=> 'hptt_guildbank_items',
			'table_main_sub'		=> '%item_id%',
			'table_subs'			=> array('%item_id%', '%itt_lang%', '%itt_direct%', '%onlyicon%', '%noicon%'),
			'page_ref'				=> 'guildbank.php',
			'show_numbers'			=> false,
			'show_select_boxes' 	=> false,
			'selectboxes_checkall'	=> false,
			'table_sort_dir'		=> 'desc',
			'table_sort_col'		=> 0,
			'table_presets'			=> array(
				array('name' => 'gb_iname',		'sort' => true,		'th_add' => 'align="center width="100%"',	'td_add' => ''),
				array('name' => 'gb_iamount',	'sort' => true,		'th_add' => 'align="center width="50px"',	'td_add' => ''),
				array('name' => 'gb_itype',		'sort' => true,		'th_add' => 'align="center width="200px"',	'td_add' => ''),
				array('name' => 'gb_ibanker',	'sort' => true,		'th_add' => 'align="center"',				'td_add' => ''),
				array('name' => 'gb_idkp',		'sort' => true,		'th_add' => 'align="center"',				'td_add' => ''),
				array('name' => 'gb_icost',		'sort' => true,		'th_add' => 'align="center"',				'td_add' => ''),
				array('name' => 'gb_ishop',		'sort' => false,	'th_add' => 'align="center"',				'td_add' => '')
			)
		),
		'hptt_guildbank_transactions' => array(
			'name'					=> 'hptt_guildbank_transactions',
			'table_main_sub'		=> '%trans_id%',
			'table_subs'			=> array('%trans_id%', '%itt_lang%', '%itt_direct%', '%onlyicon%', '%noicon%'),
			'page_ref'				=> 'guildbank.php',
			'show_numbers'			=> false,
			'show_select_boxes' 	=> false,
			'selectboxes_checkall'	=> false,
			'table_sort_dir'		=> 'desc',
			'table_sort_col'		=> 0,
			'table_presets'			=> array(
				array('name' => 'gb_tdate',		'sort' => true,		'th_add' => 'align="center width="100%"',	'td_add' => ''),
				array('name' => 'gb_tsubject',	'sort' => true,		'th_add' => 'align="center width="100%"',	'td_add' => ''),
				array('name' => 'gb_titem',		'sort' => true,		'th_add' => 'align="center width="100%"',	'td_add' => ''),
				array('name' => 'gb_tbuyer',	'sort' => true,		'th_add' => 'align="center width="50px"',	'td_add' => ''),
				array('name' => 'gb_tbanker',	'sort' => true,		'th_add' => 'align="center width="200px"',	'td_add' => ''),
				array('name' => 'gb_tvalue',	'sort' => true,		'th_add' => 'align="center"',				'td_add' => ''),
				array('name' => 'gb_tdkp',		'sort' => true,		'th_add' => 'align="center"',				'td_add' => '')
			)
		),
		'hptt_guildbank_auctions' => array(
			'name'					=> 'hptt_guildbank_auctions',
			'table_main_sub'		=> '%auction_id%',
			'table_subs'			=> array('%auction_id%', '%itt_lang%', '%itt_direct%', '%onlyicon%', '%noicon%'),
			'page_ref'				=> 'guildbank.php',
			'show_numbers'			=> false,
			'show_select_boxes' 	=> false,
			'selectboxes_checkall'	=> false,
			'table_sort_dir'		=> 'desc',
			'table_sort_col'		=> 1,
			'table_presets'			=> array(
				array('name' => 'gb_aalink',		'sort' => false,	'th_add' => 'align="center width="20px"',	'td_add' => ''),
				array('name' => 'gb_astartdate',	'sort' => true,		'th_add' => 'align="center width="100%"',	'td_add' => ''),
				array('name' => 'gb_aname',			'sort' => true,		'th_add' => 'align="center width="100%"',	'td_add' => ''),
				array('name' => 'gb_left_atime',	'sort' => true,		'th_add' => 'align="center width="100%"',	'td_add' => ''),
				array('name' => 'gb_astartvalue',	'sort' => true,		'th_add' => 'align="center width="100%"',	'td_add' => ''),
				array('name' => 'gb_bidhibidder',	'sort' => true,		'th_add' => 'align="center width="50px"',	'td_add' => ''),
				array('name' => 'gb_bidhivalue',	'sort' => true,		'th_add' => 'align="center width="50px"',	'td_add' => ''),
			)
		),
		'hptt_guildbank_bids' => array(
			'name'					=> 'hptt_guildbank_bids',
			'table_main_sub'		=> '%bid_id%',
			'table_subs'			=> array('%bid_id%'),
			'page_ref'				=> 'guildauction.php',
			'show_numbers'			=> false,
			'show_select_boxes' 	=> false,
			'selectboxes_checkall'	=> false,
			'table_sort_dir'		=> 'desc',
			'table_sort_col'		=> 0,
			'table_presets'			=> array(
				array('name' => 'gb_biddate',	'sort' => false,	'th_add' => 'align="center width="20%"',	'td_add' => ''),
				array('name' => 'gb_bidmember',	'sort' => false,	'th_add' => 'align="center width="30%"',	'td_add' => ''),
				array('name' => 'gb_bidvalue',	'sort' => false,	'th_add' => 'align="center width="50%"',	'td_add' => ''),
			)
		),
		'hptt_guildbank_admin_items' => array(
			'name'					=> 'hptt_guildbank_admin_items',
			'table_main_sub'		=> '%item_id%',
			'table_subs'			=> array('%item_id%', '%itt_lang%', '%itt_direct%', '%onlyicon%', '%noicon%'),
			'page_ref'				=> 'manage_bank_details.php',
			'show_numbers'			=> true,
			'show_select_boxes' 	=> true,
			'selectboxes_checkall'	=> true,
			'selectbox_name'		=> 'selections',
			'selectbox_valueprefix'	=> 'item_',
			'table_sort_dir'		=> 'desc',
			'table_sort_col'		=> 2,
			'table_presets' => array(
				array('name' => 'gb_iedit',		'sort' => false,	'th_add' => 'align="center width="40px"',	'td_add' => ''),
				array('name' => 'gb_iname',		'sort' => true,		'th_add' => '',								'td_add' => 'style="height:21px;"'),
				array('name' => 'gb_idate',		'sort' => true,		'th_add' => '',								'td_add' => ''),
				array('name' => 'gb_itype',		'sort' => true,		'th_add' => '',								'td_add' => ''),
				array('name' => 'gb_irarity',	'sort' => true,		'th_add' => 'align="center"',				'td_add' => ''),
				array('name' => 'gb_iamount',	'sort' => true,		'th_add' => 'align="center"',				'td_add' => ''),
				array('name' => 'gb_ivalue',	'sort' => true,		'th_add' => 'align="center"',				'td_add' => ''),
				array('name' => 'gb_idkp',		'sort' => true,		'th_add' => 'align="center"',				'td_add' => ''),
			),
		),
		'hptt_guildbank_admin_transactions' => array(
			'name'					=> 'hptt_guildbank_admin_transactions',
			'table_main_sub'		=> '%trans_id%',
			'table_subs'			=> array('%trans_id%', '%itt_lang%', '%itt_direct%', '%onlyicon%', '%noicon%'),
			'page_ref'				=> 'manage_bank_details.php',
			'show_numbers'			=> true,
			'show_select_boxes' 	=> true,
			'selectboxes_checkall'	=> true,
			'selectbox_name'		=> 'selections',
			'selectbox_valueprefix'	=> 'transaction_',
			'table_sort_dir'		=> 'desc',
			'table_sort_col'		=> 1,
			'table_presets'			=> array(
				array('name' => 'gb_tedit',		'sort' => false,	'th_add' => 'align="center width="40px"',	'td_add' => ''),
				array('name' => 'gb_tdate',		'sort' => true,		'th_add' => 'align="center width="100%"',	'td_add' => ''),
				array('name' => 'gb_tsubject',	'sort' => true,		'th_add' => 'align="center width="100%"',	'td_add' => ''),
				array('name' => 'gb_titem',		'sort' => true,		'th_add' => 'align="center width="100%"',	'td_add' => ''),
				array('name' => 'gb_tbuyer',	'sort' => true,		'th_add' => 'align="center width="50px"',	'td_add' => ''),
				array('name' => 'gb_tbanker',	'sort' => true,		'th_add' => 'align="center width="200px"',	'td_add' => ''),
				array('name' => 'gb_tvalue',	'sort' => true,		'th_add' => 'align="center"',				'td_add' => ''),
				array('name' => 'gb_tdkp',		'sort' => true,		'th_add' => 'align="center"',				'td_add' => ''),
			)
		),
		'hptt_guildbank_admin_auctions' => array(
			'name'					=> 'hptt_guildbank_admin_auctions',
			'table_main_sub'		=> '%auction_id%',
			'table_subs'			=> array('%auction_id%', '%itt_lang%', '%itt_direct%', '%onlyicon%', '%noicon%'),
			'page_ref'				=> 'manage_auctions.php',
			'show_numbers'			=> true,
			'show_select_boxes' 	=> true,
			'selectboxes_checkall'	=> true,
			'selectbox_name'		=> 'auction_ids',
			'table_sort_dir'		=> 'desc',
			'table_sort_col'		=> 3,
			'table_presets'			=> array(
				array('name' => 'gb_aedit',			'sort' => false,	'th_add' => 'align="center width="40px"',	'td_add' => ''),
				array('name' => 'gb_aactive',		'sort' => false,	'th_add' => 'align="center"',				'td_add' => ''),
				array('name' => 'gb_aname',			'sort' => true,		'th_add' => 'align="center width="100%"',	'td_add' => ''),
				array('name' => 'gb_astartdate',	'sort' => true,		'th_add' => 'align="center width="100%"',	'td_add' => ''),
				array('name' => 'gb_aduration',		'sort' => true,		'th_add' => 'align="center width="100%"',	'td_add' => ''),
				array('name' => 'gb_astartvalue',	'sort' => true,		'th_add' => 'align="center width="100%"',	'td_add' => ''),
				array('name' => 'gb_abidsteps',		'sort' => true,		'th_add' => 'align="center width="50px"',	'td_add' => ''),
				array('name' => 'gb_left_atime',	'sort' => true,		'th_add' => 'align="center width="50px"',	'td_add' => ''),
				array('name' => 'gb_ahibidder',		'sort' => true,		'th_add' => 'align="center width="200px"',	'td_add' => ''),
				array('name' => 'gb_endvalue',		'sort' => true,		'th_add' => 'align="center width="100px"',	'td_add' => ''),
			)
		),
	)
);

?>
