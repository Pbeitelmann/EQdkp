<?php
/*	Project:	EQdkp-Plus
 *	Package:	MMO News Portal Module
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

define('EQDKP_INC', true);

$eqdkp_root_path = './../../';
include_once($eqdkp_root_path.'common.php');
include_once($eqdkp_root_path . 'portal/mmo_news/mmo_news_rss.class.php');
$mmo_news = registry::register('mmo_news_rss', array(false));
$mmo_news->updateRSS();
?>