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

// EQdkp required files/vars
define('EQDKP_INC', true);
define('IN_ADMIN', true);
define('PLUGIN', 'localitembase');

$eqdkp_root_path = './../../../';
include_once($eqdkp_root_path.'common.php');


/*+----------------------------------------------------------------------------
  | localitembaseSettings
  +--------------------------------------------------------------------------*/
class LocalItembaseSettings extends page_generic
{
  /**
   * __dependencies
   * Get module dependencies
   */
  public static function __shortcuts()
  {
    $shortcuts = array('pm', 'user', 'config', 'core', 'in', 'jquery', 'html', 'tpl');
    return array_merge(parent::$shortcuts, $shortcuts);
  }

  /**
   * Constructor
   */
  public function __construct()
  {
    // plugin installed?
    if (!$this->pm->check('localitembase', PLUGIN_INSTALLED))
      message_die($this->user->lang('lit_plugin_not_installed'));

    $handler = array(
      'save' => array('process' => 'save', 'csrf' => true),
    );
	
	$this->user->check_auth('a_localitembase_settings');  
	
    parent::__construct(null, $handler);

    $this->process();
  }
  
  private $arrData = false;

  /**
   * save
   * Save the configuration
   */
  public function save()
  {

  	$objForm = register('form', array('lit_settings'));
  	$objForm->langPrefix = 'lit_';
  	$objForm->validate = true;
  	$objForm->add_fieldsets($this->fields());
  	$arrValues = $objForm->return_values();

  	if ($objForm->error){
  		$this->arrData = $arrValues;
  	} else {  
  		$blnWatermarkChanged = false;
  		if (!$arrValues['watermark_logo']) {
  			$arrValues['watermark_logo'] = $this->config->get('watermark_logo', 'localitembase');
  		} else {
  			$blnWatermarkChanged = true;
  		}
  		if($arrValues['watermark_position'] != $this->config->get('watermark_position', 'localitembase') || $arrValues['watermark_transparency'] != $this->config->get('watermark_transparency', 'localitembase') || $arrValues['watermark_enabled'] != $this->config->get('watermark_enabled', 'localitembase')){
  			$blnWatermarkChanged = true;
  		}
  		
  		if($blnWatermarkChanged){
  			$this->deleteWatermarkImages();
  		}
  		
	  	// update configuration
	    $this->config->set($arrValues, '', 'localitembase');
	    // Success message
	    $messages[] = $this->user->lang('mc_config_saved');
	
	    $this->display($messages);
  	}
   
  }
  
  
  private function fields(){
  	$arrFields = array(
  		'items' => array(
  			'base_layout' => array(
  				'type'		=> 'textarea',
  				'codeinput'	=> true,
  				'style'		=> 'width: 95%',
  				'rows'		=> 15,
  			),
	  		'base_css' => array(
	  			'type'		=> 'textarea',
	  			'codeinput'	=> true,
	  			'rows'		=> 10,
	  			'style'		=> 'width: 95%',
	  		),
  			'infotext' => array(
  				'type'		=> 'textarea',
  				'bbcodeeditor' => true,
  			),
  		),
  			/*
  		'export_import' => array(
  			'export' => array(
  				'type' => 'button',
  				'buttontype' => 'submit',
  				'buttonvalue' => '<i class="fa fa-download"></i> '.$this->user->lang('lit_f_export'),
  			),
  			'importfield' => array(
  				'type' 		=> 'textarea',
  				'style'		=> 'width: 95%',
  				'codeinput'	=> true,
  			),
	  		'import' => array(
	  			'type' => 'button',
	  			'buttontype' => 'submit',
	  			'buttonvalue' => '<i class="fa fa-upload"></i> '.$this->user->lang('lit_f_import'),
	  		),
  		),
  		*/
  	);
  
  	return $arrFields;
  }
  

  /**
   * display
   * Display the page
   *
   * @param    array  $messages   Array of Messages to output
   */
  public function display($messages=array())
  {
    // -- Messages ------------------------------------------------------------
    if ($messages)
    {
      foreach($messages as $name)
        $this->core->message($name, $this->user->lang('localitembase'), 'green');
    }
    
    $arrValues = $this->config->get_config('localitembase');
    if ($this->arrData !== false) $arrValues = $this->arrData;

    // -- Template ------------------------------------------------------------
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
	
	$this->jquery->CodeEditor('base_layout', '', 'html');
	$this->jquery->CodeEditor('base_css', '', 'css');
	
    // -- EQDKP ---------------------------------------------------------------
    $this->core->set_vars(array(
      'page_title'    => $this->user->lang('localitembase').' '.$this->user->lang('settings'),
      'template_path' => $this->pm->get_data('localitembase', 'template_path'),
      'template_file' => 'admin/settings.html',
      'display'       => true
    ));
  }
  
}

registry::register('LocalItembaseSettings');

?>