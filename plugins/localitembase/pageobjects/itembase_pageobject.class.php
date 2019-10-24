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


class itembase_pageobject extends pageobject {
  /**
   * __dependencies
   * Get module dependencies
   */
  public static function __shortcuts()
  {
    $shortcuts = array('social' => 'socialplugins');
   	return array_merge(parent::__shortcuts(), $shortcuts);
  }  
  
  /**
   * Constructor
   */
  public function __construct()
  {
    // plugin installed?
    if (!$this->pm->check('localitembase', PLUGIN_INSTALLED))
      message_die($this->user->lang('lit_plugin_not_installed'));
    
    $this->user->check_auth('u_localitembase_view');
    if(!$this->user->is_signedin()) $this->user->check_auth('u_something');
    
    $handler = array(
    	'save'		=> array('process' => 'save', 'csrf' => true),
    	'i'			=> array('process' => 'edit'),
		'import'	=> array('process' => 'import', 'csrf' => true),
		'export'	=> array('process' => 'export'),
    );
    parent::__construct(false, $handler, array('localitembase', 'html_item_name'), null, 'selected_ids[]');

    $this->process();
  }
  
  public function delete(){
  	$arrSelected = $this->in->getArray('selected_ids', 'int');
  	foreach($arrSelected as $itemID){
  		$this->pdh->put('localitembase', 'delete', array($itemID));
  	}
  	$this->pdh->process_hook_queue();
  }
  
  public function save(){
  	$objForm = register('form', array('lit_settings'));
  	$objForm->langPrefix = 'lit_';
  	$objForm->validate = true;
  	$objForm->add_fieldsets($this->fields());
  	$arrValues = $objForm->return_values();
  	
  	include_once($this->root_path."libraries/inputfilter/input.class.php");
  	$filter = new FilterInput(get_tag_blacklist(), get_attr_blacklist(), 1,1);
  	
  	$strGameID = $arrValues['item_gameid'];
  	$strQuality = $arrValues['quality'];
  	if($arrValues['icon'] != ""){
  		$strIcon = str_replace($this->pfh->FolderPath('icons', 'localitembase', 'relative'), "",  $this->root_path.$arrValues['icon']);
  	} elseif($this->in->get('i', 0) > 0) {
  		$strIcon = $this->pdh->get('localitembase', 'icon', array($this->in->get('i', 0)));
  	} else {
  		$strIcon = "";
  	}
  	
  	$arrName = array();
  	$arrImage = array();
  	$arrText = array();
  	$arrUsedLanguages = array();

  	$arrLanguages = $this->user->getAvailableLanguages(false, false, true);
  	foreach($arrLanguages as $key => $val){
  		if($arrValues['name__'.$key] != "" || $arrValues['image__'.$key] != "" || $arrValues['text__'.$key] != ""){
  			$arrUsedLanguages[] = $key;
  			$arrName[$key] = $arrValues['name__'.$key];
  			
  			
  			if($arrValues['image__'.$key] != ""){
  				$arrImage[$key] = str_replace($this->pfh->FolderPath('images', 'localitembase', 'relative'), "", $this->root_path.$arrValues['image__'.$key]);
  			} elseif($this->in->get('i', 0) > 0) {
  				$arrImages = unserialize($this->pdh->get('localitembase', 'image', array($this->in->get('i', 0))));
  				if(isset($arrImages[$key])){
  					$arrImage[$key] = $arrImages[$key];
  				}
  			}
  			$arrText[$key] =   $filter->clean($arrValues['text__'.$key]);
  		}
  	}

  	
  	if($this->in->get('i', 0) > 0){
  		$this->pdh->put('localitembase', 'update', array($this->in->get('i', 0), $strGameID, $strIcon, $strQuality, $arrName, $arrText, $arrImage, $arrUsedLanguages));
  	} else {
  		//$strGameID, $strIcon, $strQuality, $arrNames, $arrText, $arrImages, $arrLanguages
  		$this->pdh->put('localitembase', 'insert', array($strGameID, $strIcon, $strQuality, $arrName, $arrText, $arrImage, $arrUsedLanguages));
  	}
  	
  	$this->pdh->process_hook_queue();
	$this->display();
  }
  
  private function fields(){
  	$fields = array(
  		'general' => array(
  			'item_gameid' => array(
  				'type' => 'text',
  				'size' => 40,
  			),
  			'quality' => array(
  				'type' => 'text',	
  			),
  			'icon' => array(
  				'type'			=> 'file',
  				'preview' 		=> true,
  				'extensions'	=> array('jpg', 'png'),
  				'mimetypes'		=> array(
  						'image/jpeg',
  						'image/png',
  				),
  				'folder'		=> $this->pfh->FolderPath('icons', 'localitembase'),
  				'numerate'		=> true,
  				'default'		=> false,
  			)
  		),
  	);
  	
  	$arrLanguages = $this->user->getAvailableLanguages(false, false, true);
  	foreach($arrLanguages as $key => $val){
  		$fields[$key] = array(
  			'_lang' => $val,
  			'name__'.$key => array(
  				'type' => 'text',
  				'lang' => 'lit_f_item_name',
  				'size' => 40,
  			),
  			'text__'.$key => array(
  				'type' => 'textarea',
  				'lang' => 'lit_f_item_text',
  				'style' => 'width: 95%',
  				'codeinput' => true,
  			),
  			'image__'.$key => array(
  				'lang' => 'lit_f_item_images',
  				'type'	=> 'file',
  				'preview' => true,
  				'extensions'	=> array('jpg', 'png'),
  				'mimetypes'		=> array(
  						'image/jpeg',
  						'image/png',
  				),
  				'folder'		=> $this->pfh->FolderPath('images', 'localitembase'),
  				'numerate'		=> true,
  				'default'		=> false,
  			)
  		);
  	}
  	
  	return $fields;
  }
  
  public function edit(){
  	$arrLanguages = $this->user->getAvailableLanguages(false, false, true);
  	$arrValues = array();
  	
  	$itemID = $this->in->get('i', 0);
  	
  	if($itemID > 0){
  		$arrRawData = $this->pdh->get('localitembase', 'data', array($itemID));
  		$arrValues['item_gameid'] = $arrRawData['item_gameid'];
  		$arrValues['quality'] = $arrRawData['quality'];
  		$arrValues['icon'] = ($arrRawData['icon'] != "") ? $this->pfh->FolderPath('icons', 'localitembase', 'absolute').$arrRawData['icon'] : '';
  		
  		$arrName = unserialize($arrRawData['item_name']);
  		$arrText = unserialize($arrRawData['text']);
  		$arrImage= unserialize($arrRawData['image']);
  		
  		foreach($arrLanguages as $key => $val){
  			if(isset($arrName[$key])) $arrValues['name__'.$key] = $arrName[$key];
  			if(isset($arrText[$key])) $arrValues['text__'.$key] = $arrText[$key];
  			if(isset($arrImage[$key])) $arrValues['image__'.$key] = $this->pfh->FolderPath('images', 'localitembase', 'absolute').$arrImage[$key];
  		}
  	}

  	// initialize form class
  	$objForm = register('form', array('lit_settings'));
  	$objForm->reset_fields();
  	$objForm->lang_prefix = 'lit_';
  	$objForm->validate = true;
  	$objForm->use_fieldsets = true;
  	$objForm->use_dependency = true;
  	$objForm->add_fieldsets($this->fields());
  	
  	// Output the form, pass values in
  	$objForm->output($arrValues);
  	
  	$this->tpl->assign_vars(array(
  		'ITEM_NAME'	=> ($itemID > 0) ? $this->pdh->get('localitembase', 'single_item_name', array($itemID)) : $this->user->lang('lit_add_item'),
  		'INFO_TEXT' => ($this->config->get('infotext', 'localitembase') && $this->config->get('infotext', 'localitembase') != "") ? $this->bbcode->toHTML($this->config->get('infotext', 'localitembase')) : '',
  	));
  	
  	$this->core->set_vars(array(
  			'page_title'		=> (($itemID > 0) ? $this->pdh->get('localitembase', 'single_item_name', array($itemID)) : $this->user->lang('lit_add_item')) .' - '.$this->user->lang('localitembase'),
  			'template_path'		=> $this->pm->get_data('localitembase', 'template_path'),
  			'template_file'		=> 'itembase_edit.html',
  			'display'			=> true)
  	);
  }
  
  public function import(){
  	$this->user->check_auth('u_localitembase_import');
  	
	$strCachePath	= $this->pfh->FolderPath('cache', 'localitembase');
	$strIconPath	= $this->pfh->FolderPath('icons', 'localitembase');
	$strImagePath	= $this->pfh->FolderPath('images', 'localitembase');
	
	$uploader = register('uploader');
	$strZipName = $uploader->upload_mime('file', '', array('application/zip'), array('zip'), 'localitembase_dump', $strCachePath);
	
	if(!$strZipName || !file_exists($strCachePath.$strZipName)){ 
		header("HTTP/1.1 500 Internal Error");
		exit; 
	}
	
	$objZIP	= registry::register('zip', array($strCachePath.$strZipName));
	$objZIP->extract($strCachePath.'import/');
	$objZIP->close();
	
	$arrItemIDs	= array();
	$arrJSON	= file_get_contents($strCachePath.'import/localitembase_dump.json');
	$arrJSON	= json_decode($arrJSON, true);
	
	foreach($this->pdh->get('localitembase', 'id_list', array()) as $itemID){
  		$arrItemIDs[$itemID] = $this->pdh->get('localitembase', 'item_gameid', array($itemID));
  	}
  	
  	include_once($this->root_path."libraries/inputfilter/input.class.php");
  	$filter = new FilterInput(get_tag_blacklist(), get_attr_blacklist(), 1,1);

	foreach($arrJSON as $arrItemDump){
		if(!in_array($arrItemDump['item_gameid'], $arrItemIDs)){
			$oldText = unserialize($arrItemDump['text']);
			foreach($oldText as $key => $val){
				$oldText[$key] = $filter->clean($val);
			}
			
			$arrLanguages = unserialize($arrItemDump['languages']);
			$arrNewLanguage = sanitize($arrLanguages);
			
			$this->pdh->put('localitembase', 'insert', array(
					sanitize($arrItemDump['item_gameid']),
					sanitize($arrItemDump['icon']), 
					sanitize($arrItemDump['quality']), 
					sanitize(unserialize($arrItemDump['item_name'])), 
					$oldText, 
					sanitize(unserialize($arrItemDump['image'])), 
					serialize($arrNewLanguage)));
				
			if(!empty($arrItemDump['icon'])) {
				$strIcon = preg_replace("/[^a-zA-Z0-9_.-]/iU", "", $arrItemDump['icon']);
				$strExtension = strtolower(pathinfo($strIcon, PATHINFO_EXTENSION));
				if(in_array($strExtension, array('jpg', 'png'))){
					$this->pfh->FileMove($strCachePath.'import/icons/'.$strIcon, $strIconPath.$strIcon);
				}
			}
			
			$arrImages = unserialize($arrItemDump['image']);
			foreach($arrImages as $strImage){
				$strImage = preg_replace("/[^a-zA-Z0-9_.-]/iU", "", $strImage);
				$strExtension = strtolower(pathinfo($strImage, PATHINFO_EXTENSION));
				if(in_array($strExtension, array('jpg', 'png'))){
					$this->pfh->FileMove($strCachePath.'import/images/'.$strImage, $strImagePath.$strImage);
				}
			}
		}
	}
	
	$this->pdh->process_hook_queue();
	$this->pfh->Delete($strCachePath.'import/');
	
	exit;
  }
  
  public function export(){
  	$this->user->check_auth('u_localitembase_import');
  	
	$arrItems		= array();
	$strZipName		= 'localitembase_dump_'.date('Y_m_d').'.zip';
	$strCachePath	= $this->pfh->FolderPath('cache', 'localitembase');
	$strIconPath	= $this->pfh->FolderPath('icons', 'localitembase');
	$strImagePath	= $this->pfh->FolderPath('images', 'localitembase');
	
	foreach($this->pdh->get('localitembase', 'id_list', array()) as $itemID){
  		$arrItems[] = $this->pdh->get('localitembase', 'data', array($itemID));
  	}
	
	$this->pfh->putContent($strCachePath.'localitembase_dump.json', json_encode($arrItems));
	$this->pfh->Delete($strCachePath.$strZipName);
	
	$objZIP	= registry::register('zip', array($strCachePath.$strZipName));
	
	$objZIP->add($strCachePath.'localitembase_dump.json', $strCachePath);
	$objZIP->add($strIconPath, $strIconPath, 'icons/');
	$objZIP->add($strImagePath, $strImagePath, 'images/');
	$objZIP->delete('icons/index.html');
	$objZIP->delete('images/index.html');
	
	$objZIP->create();
	
	$this->pfh->Delete($strCachePath.'localitembase_dump.json');
	
	if(file_exists($strCachePath.$strZipName)){
		header('Content-Type: application/octet-stream');
		header('Content-Length: '.$this->pfh->FileSize($strCachePath.$strZipName));
		header('Content-Disposition: attachment; filename="'.$strZipName.'"');
		header('Content-Transfer-Encoding: binary');
		
		readfile($strCachePath.$strZipName);
		
		$this->pfh->Delete($strCachePath.$strZipName);
		exit;
	}
  }
  
  public function display(){
  	//Success Message For Import
  	if($this->in->exists('success')){
  		$this->core->message($this->user->lang('lit_f_import_success'), $this->user->lang('success'), 'success', false);
  	}
  	
  	$view_list = $this->pdh->get('localitembase', 'id_list', array());
  	
  	$hptt_page_settings = array(
  			'name'				=> 'hptt_localitembase',
  			'table_main_sub'	=> '%intItemID%',
  			'table_subs'		=> array('%intItemID%'),
  			'page_ref'			=> 'manage_media.php',
  			'show_numbers'		=> false,
  			'show_select_boxes'	=> true,
  			'selectboxes_checkall'=>true,
  			'show_detail_twink'	=> false,
  			'table_sort_dir'	=> 'asc',
  			'table_sort_col'	=> 0,
  			'table_presets'		=> array(
  					array('name' => 'localitembase_editicon',	'sort' => false, 'th_add' => 'width="20"', 'td_add' => ''),
  					array('name' => 'localitembase_item_name',	'sort' => true, 'th_add' => '', 'td_add' => ''),
  					array('name' => 'localitembase_item_gameid',	'sort' => true, 'th_add' => '', 'td_add' => ''),
  					array('name' => 'localitembase_quality',	'sort' => true, 'th_add' => '', 'td_add' => ''),
  					array('name' => 'localitembase_added_date',		'sort' => true, 'th_add' => '', 'td_add' => ''),
  					array('name' => 'localitembase_update_date',		'sort' => true, 'th_add' => '', 'td_add' => ''),
  					array('name' => 'localitembase_update_by','sort' => true, 'th_add' => '', 'td_add' => ''),	
  			),
  	);
  	$hptt = $this->get_hptt($hptt_page_settings, $view_list, $view_list, array('%link_url%' => $this->routing->simpleBuild('Itembase'), '%link_url_suffix%' => ''));
  	$page_suffix = '&amp;start='.$this->in->get('start', 0);
  	$sort_suffix = '?sort='.$this->in->get('sort');
  	$hptt->setPageRef($this->strPath);
  	$intLimit = 25;
  	$start	  = $this->in->get('start', 0);
  	
  	$item_count = count($view_list);
  	
  	$this->confirm_delete($this->user->lang('lit_delete_confirm'));
  	
  	$this->tpl->assign_vars(array(
  			'ITEM_LIST'			=> $hptt->get_html_table($this->in->get('sort'), $page_suffix, $start, $intLimit),
  			'HPTT_COLUMN_COUNT'	=> $hptt->get_column_count(),
  			'PAGINATION'		=> generate_pagination($this->strPath.$this->SID.$sort_suffix, $item_count, $intLimit, $start),
  			'NEW_ITEM_LINK'		=> $this->routing->build('itembase', 'New-Item', 'i0'),
  			'S_HAS_IMPORT_PERM' => $this->user->check_auth('u_localitembase_import', false),
	));
  	
  	$this->core->set_vars(array(
  			'page_title'		=> $this->user->lang('localitembase'),
  			'template_path'		=> $this->pm->get_data('localitembase', 'template_path'),
  			'template_file'		=> 'itembase.html',
  			'display'			=> true)
  	);

  }
 	
}
?>
