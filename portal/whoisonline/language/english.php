<?php
/*	Project:	EQdkp-Plus
 *	Package:	EQdkp-Plus Language File
 *	Link:		http://eqdkp-plus.eu
 *
 *	Copyright (C) 2006-2015 EQdkp-Plus Developer Team
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

 
if (!defined('EQDKP_INC')) {
	die('You cannot access this file directly.');
}

//Language: English	
//Created by EQdkp Plus Translation Tool on  2014-12-17 23:17
//File: portal/whoisonline/language/english.php
//Source-Language: german

$lang = array( 
	"whoisonline" => 'User Online',
	"whoisonline_name" => 'User Online',
	"whoisonline_desc" => 'Show online/offline users',
);

//  Settings
$lang['whoisonline_f_limit_total']	= 'Maximium number of shown users';
$lang['whoisonline_f_help_limit_total']	= 'No more user than this number will be displayed.';
$lang['whoisonline_f_limit_online']	= 'Maximum number of shown online user';
$lang['whoisonline_f_help_limit_online']	= 'Insert 0 to show all online user';
$lang['whoisonline_f_limit_offline']	= 'Maximum number of shown offline user';
$lang['whoisonline_f_help_limit_offline']	= 'Insert 0 to not display offline user';
$lang['whoisonline_f_show_guests']	= 'Show Guests';
// Portal Modul
$lang['wo_last_activity']			= 'Last activity';
$lang['wo_type_options']			= array('List', 'Avatars only');
$lang['whoisonline_f_view']			= 'View';
$lang['wo_and_guests']				= 'and %d Visitors';
?>