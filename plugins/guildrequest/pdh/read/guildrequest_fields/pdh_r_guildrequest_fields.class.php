<?php
/*	Project:	EQdkp-Plus
 *	Package:	GuildRequest Plugin
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

if (!defined('EQDKP_INC')){
	die('Do not access this file directly.');
}

/*+----------------------------------------------------------------------------
  | pdh_r_guildrequest_fields
  +--------------------------------------------------------------------------*/
if (!class_exists('pdh_r_guildrequest_fields')){
	class pdh_r_guildrequest_fields extends pdh_r_generic{

		/**
		* Data array loaded by initialize
		*/
		private $data, $combined;

		/**
		* Hook array
		*/
		public $hooks = array(
			'guildrequest_fields_update'
		);

		/**
		* reset
		* Reset guildrequest_fields read module by clearing cached data
		*/
		public function reset(){
			$this->pdc->del('pdh_guildrequest_fields_table');
			$this->pdc->del('pdh_guildrequest_fields_combined');
			$this->data = NULL;
			$this->combined = NULL;
		}

		/**
		* init
		* Initialize the guildrequest_fields read module by loading all information from db
		*
		* @returns boolean
		*/
		public function init(){
			// try to get from cache first
			$this->data = $this->pdc->get('pdh_guildrequest_fields_table');
			$this->combined = $this->pdc->get('pdh_guildrequest_fields_combined');

			if($this->data !== NULL){
				return true;
			}

			// empty array as default
			$this->data = array();
			$this->combined = array();

			// read all guildrequest_fields entries from db
			$sql = 'SELECT
				*
				FROM `__guildrequest_fields`
				ORDER BY sortid ASC;';
			$result = $this->db->query($sql);
			if ($result){

				// add row by row to local copy
				while ($row = $result->fetchAssoc()){
					$this->data[(int)$row['id']] = array(
						'id' 			=> (int)$row['id'],
						'type'			=> $row['type'],
						'name'			=> $row['name'],
						'help'			=> $row['help'],
						'options'		=> $row['options'],
						'sortid'		=> (int)$row['sortid'],
						'required'		=> (int)$row['required'],
						'in_list'		=> (int)$row['in_list'],
						'dep_field'		=> (int)$row['dep_field'],
						'dep_value'		=> $row['dep_value'],
					);

					$hash = md5($row['name'].intval($row['dep_field']));
					if (!isset($this->combined[$hash])){
						$this->combined[$hash] = array();
						$this->combined[$hash][] = (int)$row['id'];
					} else $this->combined[$hash][] = (int)$row['id'];
				}
			}

			// add data to cache
			$this->pdc->put('pdh_guildrequest_fields_table', $this->data, null);
			$this->pdc->put('pdh_guildrequest_fields_combined', $this->combined, null);

			return true;
		}

		/**
		* get_id_list
		* Return the list of guildrequest_fields ids
		*
		* @returns array(int)
		*/
		public function get_id_list(){
			if (is_array($this->data)){
				return array_keys($this->data);
			}
			return array();
		}

		public function get_id($intID){
			if (isset($this->data[$intID])){
				return $this->data[$intID];
			}
			return false;
		}
	
		public function get_type($intID){
			if (isset($this->data[$intID])){
				return $this->data[$intID]['type'];
			}
			return false;
		}
	
		public function get_name($intID){
			if (isset($this->data[$intID])){
				return $this->data[$intID]['name'];
			}
			return false;
		}
	
		public function get_help($intID){
			if (isset($this->data[$intID])){
				return $this->data[$intID]['help'];
			}
			return false;
		}
	
		public function get_options($intID){
			if (isset($this->data[$intID])){
				return $this->data[$intID]['options'];
			}
			return false;
		}
	
		public function get_sortid($intID){
			if (isset($this->data[$intID])){
				return $this->data[$intID]['sortid'];
			}
			return false;
		}
	
		public function get_required($intID){
			if (isset($this->data[$intID])){
				return $this->data[$intID]['required'];
			}
			return false;
		}
	
		public function get_in_list($intID){
			if (isset($this->data[$intID])){
				return $this->data[$intID]['in_list'];
			}
			return false;
		}
	
		public function get_dep_field($intID){
			if (isset($this->data[$intID])){
				return $this->data[$intID]['dep_field'];
			}
			return false;
		}
	
		public function get_dep_value($intID){
			if (isset($this->data[$intID])){
				return $this->data[$intID]['dep_value'];
			}
			return false;
		}
	
		public function get_combined_fields(){
			$arrCombined = array();
			foreach($this->combined as $key => $val){
				foreach($val as $val2){
					if (!$this->get_in_list($val2)) continue;
					$arrCombined[$key][] = $val2;
				}	
			}
			return $arrCombined;
		}
	
		public function get_combined_field($strKey){
			if (isset($this->combined[$strKey])){
				return $this->combined[$strKey];
			}
		}

	} //end class
} //end if class not exists

?>