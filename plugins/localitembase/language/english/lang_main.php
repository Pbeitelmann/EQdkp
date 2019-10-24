<?php
/*	Project:	EQdkp-Plus
 *	Package:	MediaCenter Plugin
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

if (!defined('EQDKP_INC'))
{
    header('HTTP/1.0 404 Not Found');exit;
}

$lang = array(
	'localitembase'                    => 'Local Itemdatabase',
	
	// Description
	'localitembase_short_desc'         => 'Local Itemdatabase',
	'localitembase_long_desc'          => 'Create and manage your own items',
		
	'lit_plugin_not_installed'		=> 'The LocalItembase-Plugin is not installed.',
	'lit_config_saved'				=> 'The configuration has been successfully saved',
		
	'lit_fs_items' => 'Items',
	'lit_f_base_layout' => 'HTML-Baselayout of the Itemtooltip',
	'lit_f_base_css' => 'CSS for Styling of the Itemtooltip',
	'lit_f_infotext' => 'Informationtext for User',
	'lit_fs_export_import' => 'Import & Export',
	'lit_f_export' => 'Export Items',
	'lit_f_import' => 'Import Items',
	'lit_f_import_success' => 'Successfully imported Items',
	'lit_add_item' => 'Add new Item',
	'lit_delete_selected_items' => 'Delete selected items',
	'lit_delete_confirm' => 'Are you sure you want to delete the following items: %s',
	
	'lit_fs_general' => 'General',
	'lit_f_item_gameid' => 'Ingame-ID',
	'lit_f_help_item_gameid' => 'Insert here the Ingame-ID of this Item. The Ingame-ID identifies the Item.',
	'lit_f_quality' => 'Item-Quality',
	'lit_f_help_quality' => 'Quality of the Item. Will be used for the color of the Itemname.',
	'lit_f_item_text' => 'Item-Description',
	'lit_f_item_text_help' => 'You can insert the uploaded item-image into your text using the {IMAGE} placeholder. This field accepts HTML.',
	'lit_f_item_name' => 'Itemname',
	'lit_f_item_name_help' => 'Name of the Item, in current language',
	'lit_f_item_images' => 'Item-Image',
	'lit_f_item_images_help' => 'Image, like a Screenshot, of the Item. Can be used instead, or with the Item-Description.',
	'lit_f_icon' => 'Item-Icon',
	'lit_f_help_icon' => 'Icon of the Item, normally 16px or 32px width',
	'lit_download' => 'Download',
 );

?>
