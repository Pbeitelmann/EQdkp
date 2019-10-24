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

if(!defined('EQDKP_INC'))
{
	header('HTTP/1.0 Not Found');
	exit;
}

include_once($eqdkp_root_path.'common.php');
if(!version_compare(phpversion(), '5.1.2', '>=')){
	message_die('This Plugin needs at least PHP-Version 5.1.2. Your Version is: '.phpversion().'.');
}
if (!registry::register('plugin_manager')->check('raidlogimport', PLUGIN_INSTALLED) ) {
	message_die('The Raid-Log-Import plugin is not installed.');
}
require_once($eqdkp_root_path.'plugins/raidlogimport/includes/functions.php');
require_once($eqdkp_root_path.'plugins/raidlogimport/includes/rli_adjustment.class.php');
require_once($eqdkp_root_path.'plugins/raidlogimport/includes/rli_item.class.php');
require_once($eqdkp_root_path.'plugins/raidlogimport/includes/rli_member.class.php');
require_once($eqdkp_root_path.'plugins/raidlogimport/includes/rli_parse.class.php');
require_once($eqdkp_root_path.'plugins/raidlogimport/includes/rli_raid.class.php');
require_once($eqdkp_root_path.'plugins/raidlogimport/includes/rli.class.php');
?>