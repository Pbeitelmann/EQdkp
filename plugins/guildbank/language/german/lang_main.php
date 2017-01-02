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

$lang = array(
	'guildbank'						=> "Gildenbank",
	'guildbank_short_desc'			=> 'Banken des Raids verwalten',
	'guildbank_long_desc'			=> 'Gildenbank ist ein Plugin um Raidbanken zu verwalten.',
	'guildbank_not_installed'		=> 'Gildenbank ist nicht installiert.',

	// Permissions
	'gb_a_perm_auctions'			=> 'Auktionen verwalten',
	'gb_u_perm_auction'				=> 'An Auktionen teilnehmen',
	'gb_perm_shop'					=> 'Gegenstände einkaufen',

	// Main Menu
	'gb_mainmenu_guildbank'			=> "Gildenbank",

	// Admin Menu
	'gb_adminmenu_guildbank'		=> "Gildenbank",

	//guildbank
	'gb_banker'						=> "Bankier",
	'gb_shop'						=> "Itemshop",
	'gb_not_avail'					=> "n.v.",
	'gb_all_bankers'				=> "Alle Banken",
	'gb_total_bankers'				=> "Vermögen aller Banken",
	'gb_bankchar_name'				=> "Bankcharakter",
	'gb_no_bankchar'				=> 'Keiner',
	'gb_update'						=> 'Letzte Aktivität',
	'gb_tab_transactions'			=> 'Transaktionen',
	'gb_tab_items'					=> 'Gegenstände',
	'gb_tab_auctions'				=> 'Auktionen',
	'gb_title_page'					=> 'Gildenbank ansehen',

	// Shop
	'gb_shop_window'				=> 'Gegenstand einkaufen',
	'gb_shop_icon_title'			=> 'Gegenstand kaufen',
	'gb_shop_buy'					=> 'Kaufen',
	'gb_item_name'					=> 'Gegenstand',
	'gb_item_value'					=> 'Kaufpreis (DKP)',
	'gb_item_value_money'			=> 'Kaufpreis (Geld)',
	'gb_itemcost'					=> 'Kosten',
	'gb_item_date'					=> 'Kaufdatum',
	'gb_dkppool'					=> 'MultiDKP-Pool',
	"gb_default_note"				=> 'Keine Notiz verfügbar',
	'gb_shop'						=> 'Shop',
	'gb_shop_error_nodkp'			=> 'Die vorhandenen DKP reichen nicht zum Kauf dieses Gegenstandes',
	'gb_shop_error_noitem'			=> 'Es ist kein Gegenstand zum Kauf mehr vorhanden',
	"gb_shop_error_noselection"		=> 'Es wurde versucht, null Gegenstände einzukaufen. Diese Auswahl ist nicht zulässig.',
	'gb_shop_buy_subject'			=> 'Gegenstand eingekauft',
	'gb_auction_won_subject'		=> 'Gegenstand ersteigert',
	'gb_shop_buy_successmsg'		=> 'Der Gegenstand wurde zum einkaufen vorgemerkt. Die Transaktion wird nach der Bestätigung durch einen Admin vorgenommen und deinem Konto gutgeschrieben.',
	'gb_confirm_shop_ta_head'		=> 'Gildenbank Gegenstandseinkäufe',
	'gb_confirm_shop_ta_button'		=> 'Einkauf bestätigen',
	'gb_decline_shop_ta_button'		=> 'Einkauf ablehnen',
	'gb_confirm_msg_success'		=> 'Die Transaktion wurde erfolgreich durchgeführt',
	'gb_confirm_msg_delete'			=> 'Die Transaktion wurde erfolgreich abgelehnt',
	'gb_notify_shopta_header'		=> 'Gegenstandseinkäufe freigeben',
	'gb_notify_shopta_confirm_req1'	=> 'Ein Einkauf wartet auf Freigabe',
	'gb_notify_shopta_confirm_req2'	=> "%s Einkäufe warten auf Freigabe",
	'gb_no_item_id_missing'			=> "Die item-ID fehlt. Bitte versuche es erneut oder benachrichtige den Administrator.",

	'gb_confirm_auction_ta_head'	=> 'Gildenbank beendete Auktionen',
	'gb_confirm_auction_ta_button'	=> 'Auktionen bestätigen',
	'gb_decline_auction_ta_button'	=> 'Auktionen ablehnen',
	'gb_notify_auctionta_header'	=> 'Beendete Auktionen freigeben',
	'gb_notify_auctionta_confirm1'	=> 'Eine Auktion wartet auf Freigabe',
	'gb_notify_auctionta_confirm2'	=> "%s Auktionen warten auf Freigabe",

	// manage_auction
	'gb_manage_auctions'			=> 'Auktionen verwalten',
	'gb_auction_management'			=> 'Auktionsverwaltung',
	'gb_auction_head_add'			=> 'Auktion hinzufügen',
	'gb_auction_head_edit'			=> 'Auktion bearbeiten',
	'gb_footer_auction'				=> "... %1\$d Auktion(en) gefunden / %2\$d pro Seite",
	"gb_footer_transaction"			=> "... %1\$d Transaktion(en) gefunden / %2\$d pro Seite",
	"gb_footer_item"				=> "... %1\$d Gegenstand/Gegenstände gefunden / %2\$d pro Seite",
	'gb_add_auction'				=> 'Auktion erstellen',
	'gb_delete_auctions'			=> 'Ausgewählte Gegenstände löschen',
	'gb_add_auction_title'			=> 'Auktion hinzufügen',
	'gb_edit_auction_title'			=> 'Auktion bearbeiten',
	'gb_auction_item'				=> 'Gegenstand',
	'gb_auction_item_help'			=> 'Ein oder mehrere Gegenstände zum versteigern. Bei Mehrfachauswahl werden mehrere Auktionen erstellt',
	'gb_auction_startdate'			=> 'Startzeitpunkt',
	'gb_auction_winner'				=> 'Gewinner',
	'gb_auction_price'				=> 'Maximalgebot',
	'gb_auction_amountbids'			=> 'Anzahl Gebote',
	'gb_auction_duration'			=> 'Auktionsdauer',
	'gb_auction_duration_help'		=> 'Die Auktionsdauer in Stunden',
	'gb_auction_startvalue'			=> 'Startgebotswert',
	'gb_auction_bidsteps'			=> 'Gebotsschrittweite',
	'gb_auction_bidsteps_help'		=> 'Bieter können in diesen Schrittweiten auf den Gegenstand bieten',
	'gb_auction_raidatt'			=> 'Raidteilnamen für Gebot',
	'gb_auction_raidatt_help'		=> 'Anzahl der Raidteilnamen in dem die betreffenden Gegenstände gefallen sind. Bei 0 kann jeder auf den Gegenstand bieten.',
	'gb_confirm_delete_auctions'	=> "Bist Du sicher, dass Du diese Auktion(en) %s löschen willst?",
	'gb_auction_multidkppool'		=> 'Multidkp Pool',
	'gb_auction_multidkppool_help'	=> 'Gib einen Multidkp Pool an, aus dem die Punkte für die Gebote verwendet werden sollen',

	// auction shop
	'gb_auction_icon_title'			=> 'Gebote abgeben',
	'gb_auction_window'				=> 'Auktion',
	'gb_auction_title'				=> 'Auktion & Gebote',
	'gb_button_bid'					=> 'Bieten',
	'gb_error_noidnotloggedin'		=> 'ACHTUNG: Um die Auktionen verwenden zu können, musst du sowohl eingeloggt als auch eine gültige AUktionsID verwenden. Versuche es noch einmal.',
	'gb_auction_avail_dkp'			=> 'Verfügbare Punkte',
	'gb_auction_timeleft'			=> 'Verbleibende Auktionszeit',
	'gb_auction_bid_info'			=> 'Gebot abgeben',
	'gb_bids_footcount'				=> "... %1\$d Gebot(e) / %2\$d pro Seite",
	'gb_bids_loading'				=> 'Lädt...',
	'gb_bids_auctionended'			=> 'Beendet',
	'gb_bids_nobids'				=> 'Keine Gebote',

	// manage_banker
	'gb_manage_bankers'				=> 'Gilden-Bankiers verwalten',
	'gb_confirm_delete_bankers'		=> "Sollen die Bankiers %s gelöscht werden?",
	'gb_banker_mainchar'			=> 'Bank-Charakter',
	'gb_money'						=> 'Guthaben',

	// manage transactions
	'gb_manage_bank_items_title'	=> "Gegegenstände des Bankiers '%s' bearbeiten",
	'gb_manage_bank_items'			=> "Bankgegegenstände bearbeiten",
	'gb_mode'						=> 'Modus',
	'gb_a_mode'					=> array(
		'0'			=> 'Gegenstand',
		'1'			=> 'Transaktion',
	),
	'gb_subject'					=> 'Verwendungszweck',
	'gb_members'					=> 'Empfänger',
	'gb_manage_bank_transa'			=> 'Transaktionen verwalten',
	'gb_title_transaction'			=> 'Transaktionensverwaltung',
	'gb_title_item'					=> 'Gegenstandsverwaltung',
	'gb_item_added'					=> 'Gegenstand hinzugefügt',
	'gb_item_payout'				=> 'Gegenstand verkauft',
	'gb_payout_item'				=> 'Gegenstand verkaufen',
	'add_transaction'				=> 'Transaktion hinzufügen',
	'gb_adjustment_text'			=> 'Gildenbank - Gegenstand wurde gekauft',
	'gb_item_sellable'				=> 'Gegenstand verkaufbar',
	'gb_itemvalue'					=> 'Gegenstandswert',

	// add/edit banker
	'gb_manage_banker'				=> 'Banker verwalten',
	'gb_add_item_title'				=> 'Gegenstand zum Bankkonto hinzufügen',
	'gb_edit_item_title'			=> 'Gegenstand bearbeiten',
	'gb_item_name'					=> "Gegenstand",
	'gb_rarity'						=> 'Gegenstandslevel',
	'gb_type'						=> "Gegenstandsart",
	'gb_dkp'						=> "DKP",
	'gb_amount'						=> "Menge",
	'gb_additem_button'				=> 'Gegenstand speichern',
	'gb_payout_button'				=> 'Gegenstand ausbezahlen',
	'gb_addtrans_button'			=> 'Transaktion speichern',
	'gb_ta_head_transaction'		=> 'Transaktion verwalten',
	'gb_ta_head_payout'				=> 'Gegenstand ausbezahlen',
	'gb_ta_head_item'				=> 'Gegenstand verwalten',
	'gb_banker_added'				=> 'Banker hinzugefügt',
	'gb_money_updated'				=> 'Bankguthaben aktualisiert',

	// settings
	'gb_header_global'				=> "Gildenbank Einstellungen",
	'gb_breadcrumb_settings'		=> "Gildenbank: Einstellungen",
	'gb_saved'						=> "Die Einstellungen wurden erfolgreich gespeichert",
	'gb_fs_banker_display'			=> "Gildenbank Ansichtseinstellungen",
	'gb_f_show_money'				=> "Zeige Bankvermögen",
	'gb_f_help_show_money'			=> "Zeige Bankvermögen (wenn aus: Keine Goldanzeige)",
	'gb_f_merge_bankers'			=> "Alle Banken zusammenfassen",
	'gb_f_help_merge_bankers'		=> "Fasse alle Banken in einer Bank zusammen",
	'gb_fs_itemshop'				=> "Gegenstandstransatkionen",
	'gb_f_use_autoadjust'			=> 'Automatische Korrektur für verkaufte Gegenstände',
	'gb_f_help_use_autoadjust'		=> 'Sollen für jeden verkauften Gegenstand automatische Korrekturen eingetragen werden?',
	'gb_f_default_event'			=> 'Standardereignis für die automatische Korrektur',
	'gb_f_help_default_event'		=> 'Falls die automatische Korrektur verwendet werden soll, muss ein Standardereignis gesetzt werden',

	//filter translations
	'gb_filter_banker'				=> "Bankier auswählen",
	'gb_filter_type'				=> "Gegenstandsart auswählen",
	'gb_filter_rarity'				=> "Gegenstandslevel auswählen",

	// filters
	'gb_a_type'						=> array(
		'quest'		=> "Quest",
		'weapon'	=> "Waffe",
		'reagent'	=> "Reagenz",
		'builder'	=> "Handwerkswaren",
		'armor'		=> "Rüstung",
		'key'		=> "Schlüssel",
		'useable'	=> "Verbrauchbar",
		'misc'		=> "Verschiedenes"
	),
	'gb_a_rarity'					=> array(
		'5'			=> "Legendär",
		'4'			=> "Episch",
		'3'			=> "Rar",
		'2'			=> "Normal",
		'1'			=> "Rest"
	),

	// default currency
	'gb_currency'					=> array(
		'platin'	=> 'Platin',
		'platin_s'	=> 'P',
		'gold'		=> 'Gold',
		'gold_s'	=> 'G',
		'silver'	=> 'Silber',
		'silver_s'	=> 'S',
		'copper'	=> 'Kupfer',
		'copper_s'	=> 'K',
		'diamond'	=> 'Diamant',
		'diamond_s'	=> 'D',
	),

	// credits
	'gb_credits'					=> "Gildenbank %s",
);
?>
