<?php
/*	Project:	EQdkp-Plus
 *	Package:	EQdkp-plus
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
 
define('EQDKP_INC', true);
$eqdkp_root_path = './../../';
include_once($eqdkp_root_path . 'common.php');
ini_set('display_errors', 1);

class tinyMCEimageUploader extends page_generic {
	
	public function __construct() {
		$handler = array();
		parent::__construct(false, $handler, array());
	
		$this->process();
	}
	
	public function display(){
		$blnIsUser = $this->user->is_signedin() && $this->user->check_auth('u_files_man', false);
		if (!$blnIsUser) {
			header("HTTP/1.0 403 Access Denied");
			return;
		}

		$blnResult = register('uploader')->upload_mime('file', 'system/articleimages', array("image/jpeg","image/png","image/gif"), array('jpg', 'png', 'gif'), 'uploaded_'.md5(rand()), register('pfh')->FolderPath('', 'files'));
		if($blnResult){
			header('Content-type: application/json; charset=utf-8');
			echo json_encode(array('location' => register('pfh')->FolderPath('system/articleimages', 'files', 'absolute').$blnResult));
			return;
		}
		
		header("HTTP/1.0 500 Server Error");
	}
	
}

registry::register('tinyMCEimageUploader');