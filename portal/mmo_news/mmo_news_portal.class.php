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

if ( !defined('EQDKP_INC') ){
	header('HTTP/1.0 404 Not Found');exit;
}

class mmo_news_portal extends portal_generic {

	protected static $path		= 'mmo_news';
	protected static $data		= array(
		'name'			=> 'MMO-News',
		'version'		=> '0.2.2',
		'author'		=> 'GodMod',
		'contact'		=> EQDKP_PROJECT_URL,
		'description'	=> 'Shows a Module with News for your game',
		'lang_prefix'	=> 'mmo_news_',
		'icon'			=> 'fa-book',
	);
	protected static $positions = array('middle', 'left1', 'left2', 'right');
	protected $settings	= array(
		'count'	=> array(
			'type'		=>	'text',
			'size'		=>	'3',
			'default'	=> 5,
		),
	);
	protected static $install	= array(
		'autoenable'		=> '1',
		'defaultposition'	=> 'left2',
		'defaultnumber'		=> '5',
	);
	
	protected static $apiLevel = 20;

	public function output() {
		$this->tpl->add_css(
			'.mmo_news_portal .ui-accordion .ui-accordion-content {
				padding: 4px;
			}'	
		);
		
		include_once($this->root_path .'portal/mmo_news/mmo_news_rss.class.php');
		$class = registry::register('mmo_news_rss', array($this->wide_content, $this->config('count')));
		$output = $class->output;
		$this->header = sanitize($class->header);
		return $output;
	}
}
?>
