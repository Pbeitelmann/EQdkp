<?php
/*	Project:	EQdkp-Plus
 *	Package:	Word of the moment Portal Module
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

class wordofthemoment_portal extends portal_generic {
	
	protected static $path		= 'wordofthemoment';
	protected static $data		= array(
		'name'			=> 'Word of the Moment',
		'version'		=> '3.0.0',
		'author'		=> 'WalleniuM',
		'icon'			=> 'fa-book',
		'contact'		=> EQDKP_PROJECT_URL,
		'description'	=> 'Output a randomword or sentence of the moment',
		'lang_prefix'	=> 'words_'
	);
	protected static $positions = array('left1', 'left2', 'right', 'middle','bottom');
	protected $settings	= array(
		'words'		=> array(
			'type'			=> 'bbcodeeditor',
			'cols'			=> '30',
			'rows'			=> '20',
			'codeinput'		=> false,
		),

	);
	protected static $install	= array(
		'autoenable'		=> '0',
		'defaultposition'	=> 'right',
		'defaultnumber'		=> '7',
	);
	
	protected static $apiLevel = 20;

	public function output() {
		$words = explode("\n", $this->config('words'));

		if(count($words) > 1){
			$strWord = '';
			while($strWord === ''){
				shuffle($words);
				$strWord = trim($words[0]);
				$myout = $strWord = $this->bbcode->toHTML(trim($words[0]));
			}

		} elseif(count($words) === 0 && $words[0] != ''){
			$myout = $words[0];
		} else {
			$myout = $this->user->lang('pk_wotm_nobd');
		}
		return $myout;
	}
}
?>
