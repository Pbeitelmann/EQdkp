<?php
/* Project: EQdkp-Plus
* Package: Boardpns-Plugin
* Link: http://eqdkp-plus.eu
*
* Copyright (C) 2006-2016 EQdkp-Plus Developer Team
*
* This program is free software: you can redistribute it and/or modify
* it under the terms of the GNU Affero General Public License as published
* by the Free Software Foundation, either version 3 of the License, or
* (at your option) any later version.
*
* This program is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
* GNU Affero General Public License for more details.
*
* You should have received a copy of the GNU Affero General Public License
* along with this program. If not, see <http://www.gnu.org/licenses/>.
*/


if (!defined('EQDKP_INC'))
{
  header('HTTP/1.0 404 Not Found');
  exit;
}


/*+----------------------------------------------------------------------------
  | boardpns
  +--------------------------------------------------------------------------*/
class boardpns extends plugin_generic
{

  public $version    = '0.1.1';
  public $build      = '';
  public $copyright  = 'GodMod';
  
  protected static $apiLevel = 23;

  /**
    * Constructor
    * Initialize all informations for installing/uninstalling plugin
    */
  public function __construct()
  {
    parent::__construct();

    $this->add_data(array (
      'name'              => 'Board-PNs',
      'code'              => 'boardpns',
      'path'              => 'boardpns',
      'template_path'     => 'plugins/boardpns/templates/',
      'icon'              => 'fa-envelope',
      'version'           => $this->version,
      'author'            => $this->copyright,
      'description'       => $this->user->lang('boardpns_short_desc'),
      'long_description'  => $this->user->lang('boardpns_long_desc'),
      'homepage'          => EQDKP_PROJECT_URL,
      'manuallink'        => false,
      'plus_version'      => '2.0',
      'build'             => $this->build,
    ));

    $this->add_dependency(array(
      'plus_version'      => '2.0'
    ));

	$this->add_permission('u', 'view',    'Y', $this->user->lang('boardpns_view'),    array(2,3,4));
		
	$this->add_hook('portal', 'boardpns_portal_hook', 'portal');
	$this->add_hook('wrapper', 'boardpns_wrapper_hook', 'wrapper');
  }

}
?>
