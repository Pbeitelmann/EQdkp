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

if (!defined('EQDKP_INC'))
{
  header('HTTP/1.0 404 Not Found');
  exit;
}


/*+----------------------------------------------------------------------------
  | localitembase
  +--------------------------------------------------------------------------*/
class localitembase extends plugin_generic
{
  /**
   * __dependencies
   * Get module dependencies
   */
  public static function __shortcuts()
  {
    $shortcuts = array('user', 'config', 'pdc', 'pfh', 'pdh', 'routing');
    return array_merge(parent::$shortcuts, $shortcuts);
  }

  public $version    = '1.1.3';
  public $build      = '';
  public $copyright  = 'GodMod';
  public $vstatus    = 'Alpha';
  
  protected static $apiLevel = 23;

  /**
    * Constructor
    * Initialize all informations for installing/uninstalling plugin
    */
  public function __construct()
  {
    parent::__construct();

    $this->add_data(array (
      'name'              => 'Local Itembase',
      'code'              => 'localitembase',
      'path'              => 'localitembase',
      'template_path'     => 'plugins/localitembase/templates/',
      'icon'              => 'fa fa-database',
      'version'           => $this->version,
      'author'            => $this->copyright,
      'description'       => $this->user->lang('localitembase_short_desc'),
      'long_description'  => $this->user->lang('localitembase_long_desc'),
      'homepage'          => EQDKP_PROJECT_URL,
      'manuallink'        => false,
      'plus_version'      => '2.1',
      'build'             => $this->build,
    ));

    $this->add_dependency(array(
      'plus_version'      => '2.1'
    ));

    // -- Register our permissions ------------------------
    // permissions: 'a'=admins, 'u'=user
    // ('a'/'u', Permission-Name, Enable? 'Y'/'N', Language string, array of user-group-ids that should have this permission)
    // Groups: 1 = Guests, 2 = Super-Admin, 3 = Admin, 4 = Member
	$this->add_permission('u', 'view',    'Y', $this->user->lang('view'),    array(2,3,4));
	$this->add_permission('u', 'import',   'N', $this->user->lang('lit_fs_export_import'),  array(2,3));
	$this->add_permission('a', 'settings', 'N', $this->user->lang('menu_settings'), array(2,3));	

    // -- PDH Modules -------------------------------------

	$this->add_pdh_read_module('localitembase');
	$this->add_pdh_write_module('localitembase');
	
    //Routing
	$this->routing->addRoute('ItemBase', 'itembase', 'plugins/localitembase/pageobjects');

	// -- Menu --------------------------------------------
    $this->add_menu('admin', $this->gen_admin_menu());
	$this->add_menu('main', $this->gen_main_menu());

	if($this->config->get('base_css', 'localitembase') && strlen($this->config->get('base_css', 'localitembase'))) $this->tpl->add_css($this->config->get('base_css', 'localitembase'));

  }

  /**
    * pre_install
    * Define Installation
    */
  public function pre_install()
  {
    // include SQL and default configuration data for installation
    include($this->root_path.'plugins/localitembase/includes/sql.php');

    // define installation
    for ($i = 1; $i <= count($localitembaseSQL['install']); $i++)
      $this->add_sql(SQL_INSTALL, $localitembaseSQL['install'][$i]);
	  
    $this->pdc->del_prefix('pdh_localitembase_');
    
    $this->pfh->copy($this->root_path.'plugins/localitembase/parser/localitembase.class.php', $this->root_path.'games/'.$this->config->get('default_game').'/infotooltip/localitembase.class.php');
  }
  
  /**
   * post_install
   * Add Default Settings
   */
  public function post_install(){
  	
  	//Default Settings
  	$arrSave = array(
  		'base_layout' => "<table class='local_itembase_tt outer'>
  <tr>
    <td valign='top' width='10px'>
      <div class='iconmedium'><img src='{ICON}' alt='icon' /></div>
    </td>
    
    <td width='400'>
      <div class='inner'>
        <div>{ITEM_CONTENT}{DEBUG}</div>
      </div> 
    </td>
  </tr>
</table>​​",
  		'base_css' => 'table.local_itembase_tt.outer {
  border: 0;
  border-spacing: 0;
  border-collapse: collapse;
  background: none;
  margin: 0;
  padding: 0;
  text-align: left;
  float: none;
  min-width: 400px;
  position: relative;
  height: 0;
}

.local_itembase_tt .inner {
  float: none;
  text-align: left;
  margin: 0;
  padding: 4px;
  width: auto;
  z-index: 100000001;
  max-width: 400px;
  font-family: Verdana, sans-serif;
  font-variant: normal;
  font-size: 11px;
  line-height: 17px;
  color: #fff;
  border: 1px solid #000;
  border-radius: 5px;
  background-color: #707070;
  margin-left: 80px;
}

/* item icon */
.local_itembase_tt div.iconmedium  {
  width: 70px;
  height:70px;
  background: 4px 4px no-repeat;
  position: absolute;
  margin-left: 2px;
  margin-top: 2px;
  z-index: 1;
}

.local_itembase_tt .iconmedium img {
  width: 70px;
  height:70px;
}',
  		'infotext' => '',
  	);
  	
  	$this->config->set($arrSave, '', 'localitembase');
  	
  }

  /**
    * pre_uninstall
    * Define uninstallation
    */
  public function pre_uninstall()
  {
    // include SQL data for uninstallation
    include($this->root_path.'plugins/localitembase/includes/sql.php');

    for ($i = 1; $i <= count($localitembaseSQL['uninstall']); $i++)
      $this->add_sql(SQL_UNINSTALL, $localitembaseSQL['uninstall'][$i]);

    $this->pfh->Delete($this->root_path.'games/'.$this->config->get('default_game').'/infotooltip/localitembase.class.php');
    $this->pfh->Delete('', 'localitembase');
  }


  /**
    * gen_admin_menu
    * Generate the Admin Menu
    */
  private function gen_admin_menu()
  {

    $admin_menu = array (array(
        'name' => $this->user->lang('localitembase'),
        'icon' => 'fa fa-database',
        1 => array (
          'link'  => 'plugins/localitembase/admin/settings.php'.$this->SID,
          'text'  => $this->user->lang('settings'),
          'check' => 'a_localitembase_settings',
          'icon'  => 'fa-wrench'
        ),
    ));


    return $admin_menu;
  }
  
   /**
    * gen_main_menu
    * Generate the Main Menu
    */
  private function gen_main_menu()
  {
  	$main_menu = array();
	$main_menu[] = array(
		'link'  		=> $this->routing->build('ItemBase', false, false, true, true),
		'text'  		=> $this->user->lang('localitembase'),
		'check' 		=> 'u_localitembase_view',
		//'default_hide'	=> 1,
		//'link_category' => 'mc_localitembase',
	);

    return $main_menu;
  }

}
?>
