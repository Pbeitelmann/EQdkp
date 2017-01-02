<?php
/*	Project:	EQdkp-Plus
 *	Package:	Who is online Portal Module
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

if ( !defined('EQDKP_INC') ){
	header('HTTP/1.0 404 Not Found');exit;
}

// Title
$lang['whoisonline']				= 'User Online';
$lang['whoisonline_name']			= 'User Online';
$lang['whoisonline_desc']			= 'Anzeige für Online/Offline Benutzer';

//  Settings
$lang['whoisonline_f_limit_total']	= 'Maximale Anzahl an angezeigten Benutzern';
$lang['whoisonline_f_help_limit_total']	= 'Wird die maximale Anzahl von Online-Benutzern nicht erreicht, wird mit Offline-Benutzer aufgefüllt, bis deren maximale Anzahl erreicht ist.';
$lang['whoisonline_f_limit_online']	= 'Maximale Anzahl an angezeigten Online-Benutzern';
$lang['whoisonline_f_help_limit_online']	= 'Trage 0 ein, um alle Online-Benutzer anzuzeigen';
$lang['whoisonline_f_limit_offline']	= 'Maximale Anzahl an angezeigten Offline-Benutzern';
$lang['whoisonline_f_help_limit_offline']	= 'Trage 0 ein, um keine Offline-Benutzer anzuzeigen';
$lang['whoisonline_f_show_guests']	= 'Gäste anzeigen';
// Portal Modul
$lang['wo_last_activity']			= 'Letzte Aktivität';
$lang['wo_type_options']			= array('Liste', 'Nur Avatare');
$lang['whoisonline_f_view']			= 'Darstellung';
$lang['wo_and_guests']				= 'und %d Besucher';
?>
