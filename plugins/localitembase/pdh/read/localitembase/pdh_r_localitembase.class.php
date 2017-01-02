<?php
/*	Project:	EQdkp-Plus
 *	Package:	Local Itembase Plugin
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

if ( !defined('EQDKP_INC') ){
	die('Do not access this file directly.');
}
				
if ( !class_exists( "pdh_r_localitembase" ) ) {
	class pdh_r_localitembase extends pdh_r_generic{
		public static function __shortcuts() {
		$shortcuts = array();
		return array_merge(parent::$shortcuts, $shortcuts);
	}				
	
	public $default_lang = 'english';
	public $localitembase = null;
	public $items_by_gameid = null;
	public $items_by_name = null;

	public $hooks = array(
		'localitembase_update',
	);		
			
	public $presets = array(
		'localitembase_id' => array('id', array('%intItemID%'), array()),
		'localitembase_item_gameid' => array('item_gameid', array('%intItemID%'), array()),
		'localitembase_quality' => array('quality', array('%intItemID%'), array()),
		'localitembase_icon' => array('icon', array('%intItemID%'), array()),
		'localitembase_item_name' => array('item_name', array('%intItemID%'), array()),
		'localitembase_image' => array('image', array('%intItemID%'), array()),
		'localitembase_text' => array('text', array('%intItemID%'), array()),
		'localitembase_languages' => array('languages', array('%intItemID%'), array()),
		'localitembase_added_date' => array('added_date', array('%intItemID%'), array()),
		'localitembase_added_by' => array('added_by', array('%intItemID%'), array()),
		'localitembase_update_date' => array('update_date', array('%intItemID%'), array()),
		'localitembase_update_by' => array('update_by', array('%intItemID%'), array()),
		'localitembase_editicon' => array('editicon', array('%intItemID%'), array()),
	);
				
	public function reset(){
			$this->pdc->del('pdh_localitembase_table');
			$this->pdc->del('pdh_localitembase_gameid_table');
			$this->pdc->del('pdh_localitembase_name_table');
			
			$this->localitembase = NULL;
			$this->items_by_gameid = NULL;
			$this->items_by_name = NULL;
	}
					
	public function init(){
			$this->localitembase	= $this->pdc->get('pdh_localitembase_table');				
					
			if($this->localitembase !== NULL){
				return true;
			}		

			$objQuery = $this->db->query('SELECT * FROM __plugin_localitembase');
			if($objQuery){
				while($drow = $objQuery->fetchAssoc()){
					$this->localitembase[(int)$drow['id']] = array(
						'id'				=> (int)$drow['id'],
						'item_gameid'		=> $drow['item_gameid'],
						'quality'			=> $drow['quality'],
						'icon'				=> $drow['icon'],
						'item_name'			=> $drow['item_name'],
						'image'				=> $drow['image'],
						'text'				=> $drow['text'],
						'languages'			=> $drow['languages'],
						'added_date'		=> (int)$drow['added_date'],
						'added_by'			=> (int)$drow['added_by'],
						'update_date'		=> (int)$drow['update_date'],
						'update_by'			=> (int)$drow['update_by'],
					);
					
					if($drow['item_gameid'] != "") $this->items_by_gameid[$drow['item_gameid']] = (int)$drow['id'];
					
					$arrNames = unserialize($drow['item_name']);
					foreach($arrNames as $key => $val){
						$this->items_by_name[unsanitize($val)] = (int)$drow['id'];
					}

					$this->pfh->putContent($this->pfh->FolderPath('cache', 'localitembase').'item_'. (int)$drow['id'].'.json', json_encode($this->localitembase[(int)$drow['id']]));
				}
				
				$this->pdc->put('pdh_localitembase_table', $this->localitembase, null);
				$this->pdc->put('pdh_localitembase_gameid_table', $this->items_by_gameid, null);
				$this->pfh->putContent($this->pfh->FolderPath('cache', 'localitembase').'index_gameid.json', json_encode($this->items_by_gameid));
				$this->pdc->put('pdh_localitembase_name_table', $this->items_by_name, null);
				$this->pfh->putContent($this->pfh->FolderPath('cache', 'localitembase').'index_name.json', json_encode($this->items_by_name));
			}

		}	//end init function

		/**
		 * @return multitype: List of all IDs
		 */				
		public function get_id_list(){
			if ($this->localitembase === null) return array();
			return array_keys($this->localitembase);
		}
		
		/**
		 * Get all data of Element with $strID
		 * @return multitype: Array with all data
		 */				
		public function get_data($intItemID){
			if (isset($this->localitembase[$intItemID])){
				return $this->localitembase[$intItemID];
			}
			return false;
		}
				
		/**
		 * Returns id for $intItemID				
		 * @param integer $intItemID
		 * @return multitype id
		 */
		 public function get_id($intItemID){
			if (isset($this->localitembase[$intItemID])){
				return $this->localitembase[$intItemID]['id'];
			}
			return false;
		}

		/**
		 * Returns item_gameid for $intItemID				
		 * @param integer $intItemID
		 * @return multitype item_gameid
		 */
		 public function get_item_gameid($intItemID){
			if (isset($this->localitembase[$intItemID])){
				return $this->localitembase[$intItemID]['item_gameid'];
			}
			return false;
		}

		/**
		 * Returns quality for $intItemID				
		 * @param integer $intItemID
		 * @return multitype quality
		 */
		 public function get_quality($intItemID){
			if (isset($this->localitembase[$intItemID])){
				return $this->localitembase[$intItemID]['quality'];
			}
			return false;
		}

		/**
		 * Returns icon for $intItemID				
		 * @param integer $intItemID
		 * @return multitype icon
		 */
		 public function get_icon($intItemID){
			if (isset($this->localitembase[$intItemID])){
				return $this->localitembase[$intItemID]['icon'];
			}
			return false;
		}

		/**
		 * Returns item_name for $intItemID				
		 * @param integer $intItemID
		 * @return multitype item_name
		 */
		 public function get_item_name($intItemID){
			if (isset($this->localitembase[$intItemID])){
				return $this->localitembase[$intItemID]['item_name'];
			}
			return false;
		}

		/**
		 * Returns image for $intItemID				
		 * @param integer $intItemID
		 * @return multitype image
		 */
		 public function get_image($intItemID){
			if (isset($this->localitembase[$intItemID])){
				return $this->localitembase[$intItemID]['image'];
			}
			return false;
		}

		/**
		 * Returns text for $intItemID				
		 * @param integer $intItemID
		 * @return multitype text
		 */
		 public function get_text($intItemID){
			if (isset($this->localitembase[$intItemID])){
				return $this->localitembase[$intItemID]['text'];
			}
			return false;
		}

		/**
		 * Returns languages for $intItemID				
		 * @param integer $intItemID
		 * @return multitype languages
		 */
		 public function get_languages($intItemID){
			if (isset($this->localitembase[$intItemID])){
				return $this->localitembase[$intItemID]['languages'];
			}
			return false;
		}

		/**
		 * Returns added_date for $intItemID				
		 * @param integer $intItemID
		 * @return multitype added_date
		 */
		 public function get_added_date($intItemID){
			if (isset($this->localitembase[$intItemID])){
				return $this->localitembase[$intItemID]['added_date'];
			}
			return false;
		}

		/**
		 * Returns added_by for $intItemID				
		 * @param integer $intItemID
		 * @return multitype added_by
		 */
		 public function get_added_by($intItemID){
			if (isset($this->localitembase[$intItemID])){
				return $this->localitembase[$intItemID]['added_by'];
			}
			return false;
		}

		/**
		 * Returns update_date for $intItemID				
		 * @param integer $intItemID
		 * @return multitype update_date
		 */
		 public function get_update_date($intItemID){
			if (isset($this->localitembase[$intItemID])){
				return $this->localitembase[$intItemID]['update_date'];
			}
			return false;
		}

		/**
		 * Returns update_by for $intItemID				
		 * @param integer $intItemID
		 * @return multitype update_by
		 */
		 public function get_update_by($intItemID){
			if (isset($this->localitembase[$intItemID])){
				return $this->localitembase[$intItemID]['update_by'];
			}
			return false;
		}
		
		public function get_editicon($intItemID){
			$out = '<a href="'.$this->routing->build('itembase', $this->get_single_item_name($intItemID), 'i'.$intItemID).'">
				<i class="fa fa-pencil fa-lg" title="'.$this->user->lang('edit').'"></i>
			</a>';
		
			return $out;
		}
		

		public function get_html_item_name($intItemID){
			$arrNames = unserialize($this->get_item_name($intItemID));
			$strOut = "";
			foreach($arrNames as $key => $val){
				$strOut .= $val. ' ('.$key.')<br />';
			}
			return $strOut;
		}
		
		public function get_html_added_date($intItemID){
			return $this->time->user_date($this->get_added_date($intItemID));
		}
		
		public function get_html_update_date($intItemID){
			return ($this->get_update_by()) ? $this->time->user_date($this->get_added_date($intItemID)) : "";
		}
		
		public function get_html_added_by($intItemID){
			return $this->pdh->get('user', 'name', array($this->get_added_by($intItemID)));
		}
		
		public function get_html_update_by($intItemID){
			return  ($this->get_update_by()) ? $this->pdh->get('user', 'name', array($this->get_update_by($intItemID))) : "";
		}
		
		public function get_single_item_name($intItemID){
			$arrNames = unserialize($this->get_item_name($intItemID));
			$strOut = "";
			foreach($arrNames as $key => $val){
				$strOut .= $val;
				break;
			}
			return $strOut;
		}
		
		public function get_item_by_name($strName){
			if(isset($this->items_by_name[$strName])) return $this->items_by_name[$strName];
			
			return false;
		}
		
		public function get_item_by_gameid($strGameID){
			if(isset($this->items_by_gameid[$strGameID])) return $this->items_by_gameid[$strGameID];
				
			return false;
		}

	}//end class
}//end if
?>