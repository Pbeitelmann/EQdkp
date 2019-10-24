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
  | pdh_r_guildrequest_visits
  +--------------------------------------------------------------------------*/
if (!class_exists('pdh_r_guildrequest_visits')){
	class pdh_r_guildrequest_visits extends pdh_r_generic{

		/**
		* Data array loaded by initialize
		*/
		private $data;

		/**
		* Hook array
		*/
		public $hooks = array(
			'guildrequest_visits_update'
		);

		/**
		* reset
		* Reset guildrequest_visits read module by clearing cached data
		*/
		public function reset(){
			$this->pdc->del('pdh_guildrequest_visits_table');
			unset($this->data);
		}

		/**
		* init
		* Initialize the guildrequest_visits read module by loading all information from db
		*
		* @returns boolean
		*/
		public function init(){
			// try to get from cache first
			$this->data = $this->pdc->get('pdh_guildrequest_visits_table');
			if($this->data !== NULL){
				return true;
			}

			// empty array as default
			$this->data = array();

			// read all guildrequest_visits entries from db
			$sql = 'SELECT
				*
				FROM `__guildrequest_visits`';
			$result = $this->db->query($sql);
			if ($result){

				// add row by row to local copy
				while ($row =  $result->fetchAssoc()){
					$this->data[(int)$row['user_id']][(int)$row['request_id']] = array(
						'request_id'	=> (int)$row['request_id'],
						'user_id'		=> (int)$row['user_id'],
						'lastvisit'		=> (int)$row['lastvisit'],
					);
				}
			}

			// add data to cache
			$this->pdc->put('pdh_guildrequest_visits_table', $this->data, null);
			return true;
		}

		public function get_user_visists($intUserID){
			if (isset($this->data[$intUserID])){
				return $this->data[$intUserID];
			}
			return false;
		}

	} //end class
} //end if class not exists
?>