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

// EQdkp required files/vars
class addrequest_pageobject extends pageobject {
	/**
	* __dependencies
	* Get module dependencies
	*/
	public static function __shortcuts(){
		$shortcuts = array('email' => 'MyMailer');
		return array_merge(parent::__shortcuts(), $shortcuts);
	}

	private $data = array();

	/**
	* Constructor
	*/
  public function __construct(){
    // plugin installed?
    if (!$this->pm->check('guildrequest', PLUGIN_INSTALLED))
      message_die($this->user->lang('gr_plugin_not_installed'));

    $handler = array(
      'save' => array('process' => 'save', 'csrf' => true, 'check' => 'u_guildrequest_add'),
    );
    parent::__construct('u_guildrequest_add', $handler);

    $this->process();
  }

  public function save(){
	//Build Field-Array
	$arrFields = $this->pdh->get('guildrequest_fields', 'id_list', array());
	$arrInput = $arrValues = array();
	foreach($arrFields as $id){
		$row = $this->pdh->get('guildrequest_fields', 'id', array($id));
		if ($row['type'] == 3 || $row['type'] == 4){
			continue;
		}
		$arrInput[$row['id']] = array(
			'id'		=> $row['id'],
			'name'		=> $row['name'],
			'input' 	=> $this->in->get('gr_field_'.$row['id']),
			'required'	=> ($row['required']),
			'dep_field' => $row['dep_field'],
			'dep_value' => $row['dep_value'],
		);
		
		$arrValues[$row['id']] = $this->in->get('gr_field_'.$row['id']);
		
		//Checkboxes
		if ($row['type'] == 5){
			$arrInput[$row['id']] = array(
				'id'		=> $row['id'],
				'name'		=> $row['name'],
				'input' 	=> serialize($this->in->getArray('gr_field_'.$row['id'], 'int')),
				'required'	=> ($row['required']),
				'dep_field' => $row['dep_field'],
				'dep_value' => $row['dep_value'],
			);
			$arrValues[$row['id']] = $this->in->getArray('gr_field_'.$row['id'], 'int');
		}
	}
	
	if(!$this->user->is_signedin()){
		$arrInput['email'] = array(
			'input' 	=> $this->in->get('gr_email'),
			'name'		=> $this->user->lang('email'),
			'required'	=> true,
			'id'		=> 'email',
		);
		$arrInput['name'] = array(
			'input' 	=> $this->in->get('gr_name'),
			'name'		=> $this->user->lang('name'),
			'required'	=> true,
			'id'		=> 'name',
		);
	}

	$this->data = $arrInput;

	//Check Captcha
	if(!$this->user->is_signedin()){
		if($this->config->get('enable_captcha')){
			require($this->root_path.'libraries/recaptcha/recaptcha.class.php');
			$captcha = new recaptcha;
			$response = $captcha->check_answer($this->config->get('lib_recaptcha_pkey'), $this->env->ip, $this->in->get('g-recaptcha-response'));
			if (!$response->is_valid) {
				$this->core->message($this->user->lang('lib_captcha_wrong'), $this->user->lang('error'), 'red');
				$this->display;
				return;
			}
		}
		
		//Check Username/Email for account creation
		if($this->config->get('create_account', 'guildrequest') && !$this->config->get('cmsbrige_active')){
			
			if ($this->pdh->get('user', 'check_email', array($this->in->get('gr_email'))) == 'false'){
				$this->core->message(str_replace("{0}", sanitize($this->in->get('gr_email')), $this->user->lang('fv_email_alreadyuse')), $this->user->lang('error'), 'red');
				$this->display();
				return;
			}
			
			if ($this->pdh->get('user', 'check_username', array($this->in->get('gr_name'))) == 'false'){
				$this->core->message(str_replace("{0}", sanitize($this->in->get('gr_name')), $this->user->lang('fv_username_alreadyuse')), $this->user->lang('error'), 'red');
				$this->display();
				return;
			}
			
		}

		//Check email
		if (!preg_match("/^([a-zA-Z0-9])+([\.a-zA-Z0-9_-])*@([a-zA-Z0-9_-])+(\.[a-zA-Z0-9_-]+)+/",$this->in->get('gr_email'))){
			$this->core->message($this->user->lang('fv_invalid_email'), $this->user->lang('error'), 'red');
			$this->display();
			return;
		}
	}
	
	//Check Required
	$arrRequired = array();
	foreach ($arrInput as $val){
		if (!$val['required']) continue;
		
		if (isset($val['dep_field']) && $val['dep_field'] && $val['dep_field'] != 999999999){
			$intDepField = $val['dep_field'];
			if (!isset($arrValues[$intDepField])) continue;
			
			if (is_array($arrValues[$intDepField])){
				if (!isset($arrValues[$intDepField][$val['dep_value']])) continue;
			} else {		
				if ($arrValues[$intDepField] != $val["dep_value"]) continue;
			}
		}
		
		if ($val['input'] == '' || $val['input'] == 'a:0:{}') $arrRequired[] = $val['name'];
	}

	if (count($arrRequired) > 0) {
		$this->core->message(implode(', ', $arrRequired), $this->user->lang('missing_values'), 'red');
		$this->display();
		return;
	}
	

	
	//Hook for checking values
	$arrHookResult = $this->hooks->process('gr_addrequest_formcheck', array('name' => $strName, 'email' => $strEmail, 'auth_key' => $strAuthKey, 'data' => $arrToSave), true);
	if(isset($arrHookResult['error']) && $arrHookResult['error'] !== false){
		$this->core->message($arrHookResult['error'], $this->user->lang('error'), 'red');
		$this->display();
		return;
	}
	
	//Insert into DB
	if($this->user->is_signedin()){
		$arrInput['name']['input'] = $this->user->data['username'];
		$arrInput['email']['input'] = $this->user->data['user_email'];
	}
	
	$strName = $arrInput['name']['input'];
	$strEmail = $arrInput['email']['input'];
	$strAuthKey = random_string(false, 40);
	$strActivationKey = random_string(false, 32);
	$arrInput['email']['input'] = register('encrypt')->encrypt($arrInput['email']['input']);
	$arrToSave = array();
	foreach($arrInput as $val){
		$arrToSave[$val['id']] = $val['input'];
	}	
	$strContent = serialize($arrToSave);
	
	$blnResult = $this->pdh->put('guildrequest_requests', 'add', array($strName, $strEmail, $strAuthKey, $strActivationKey, $strContent));
	
	//Hook for e.g. creating users
	$this->hooks->process('gr_addrequest', array('name' => $strName, 'email' => $strEmail, 'auth_key' => $strAuthKey, 'data' => $arrToSave));
	
	$this->pdh->process_hook_queue();
	if (!$blnResult){
		$this->core->message($this->user->lang('error'), $this->user->lang('error'), 'red');
		$this->display();
		return;
	}
	
	//Send Email to User with auth key
	$server_url = $this->env->link.$this->routing->build('ViewApplication', $strName, $blnResult, false, true);
	$bodyvars = array(
		'USERNAME'		=> sanitize($strName),
		'U_ACTIVATE' 	=> $server_url .'?key=' . $strAuthKey,
		'GUILDTAG'		=> $this->config->get('guildtag'),
	);
	
	if(!$this->email->SendMailFromAdmin($strEmail, $this->user->lang('gr_viewlink_subject'), $this->root_path.'plugins/guildrequest/language/'.$this->user->data['user_lang'].'/email/request_viewlink.html', $bodyvars)){
		$this->core->message($this->user->lang('email_subject_send_error'), $this->user->lang('error'), 'red');
		$this->display();
		return;
	} else {
		if($blnResult){
			$arrUsers = $this->pdh->get('user', 'users_with_permission', array('u_guildrequest_view'));
			$this->ntfy->add('guildrequest_new_application', $blnResult, sanitize($strName), $this->routing->build('ListApplications'), $arrUsers, sanitize($strName));
		}
	
		//Redirect to viewrequest page
		redirect($this->routing->build('ViewApplication', $strName, $blnResult, false, true).'?key=' . $strAuthKey.'&msg=success');
	}
  }
  
  
  public function display()
  {
	
	$arrFields = $this->pdh->get('guildrequest_fields', 'id_list', array());
	$intGroup = 0;
	$blnGroupOpen = true;
	$this->tpl->assign_block_vars('tabs', array(
	));
	
	$this->add_personal_group();
		
	foreach($arrFields as $id){
		$row = $this->pdh->get('guildrequest_fields', 'id', array($id));
		$row['options'] = unserialize($row['options']);
		
		//Dependency
		if ($row['dep_field'] && ((strlen($row['dep_value']) && in_array($row['dep_field'], $arrFields)) || $row['dep_field'] == 999999999)) $this->gen_dependency($row);
		
		//Close previous group
		if ($row['type'] == 3){
			$blnGroupOpen = false;
			$intGroup++;
		}
		
		//Input
		if ($row['type'] == 0){
			if (!$blnGroupOpen){
				$this->tpl->assign_block_vars('tabs.fieldset', array(
					'NAME'	=> $this->user->lang('gr_default_grouplabel'),
					'ID'	=> 'information',
				));
				$blnGroupOpen = true;
			}
			

			$this->tpl->assign_block_vars('tabs.fieldset.field', array(
				'NAME'		=> $row['name'],
				'FIELD'		=> new htext('gr_field_'.$row['id'], array('js' => 'style="width:95%"', 'value' => (isset($this->data[$row['id']]) ? $this->data[$row['id']]['input'] : ''))),
				'REQUIRED'	=> ($row['required']),
				'HELP'		=> $row['help'],
				'ID'		=> 'dl_'.$row['id'],
			));
			
		}
		
		//Textarea
		if ($row['type'] == 1){
			if (!$blnGroupOpen){
				$this->tpl->assign_block_vars('tabs.fieldset', array(
					'NAME'	=> $this->user->lang('gr_default_grouplabel'),
					'ID'	=> 'information',
				));
				$blnGroupOpen = true;
			}

			$this->tpl->assign_block_vars('tabs.fieldset.field', array(
				'NAME'		=> $row['name'],
				'FIELD'		=> new htextarea('gr_field_'.$row['id'], array('js' => 'style="width:95%"', 'rows' => 10, 'value' => (isset($this->data[$row['id']]) ? $this->data[$row['id']]['input'] : ''))),
				'REQUIRED'	=> ($row['required']),
				'HELP'		=> $row['help'],
				'ID'		=> 'dl_'.$row['id'],
			));
		}
		
		//Select
		if ($row['type'] == 2){
			if (!$blnGroupOpen){
				$this->tpl->assign_block_vars('tabs.fieldset', array(
					'NAME'	=> $this->user->lang('gr_default_grouplabel'),
					'ID'	=> 'information',
				));
				$blnGroupOpen = true;
			}
			
			$arrOptions = array();
			$arrOptions[''] = $this->user->lang('cl_ms_noneselected');
			foreach($row['options'] as $val){
				$val = trim(str_replace(array("\n", "\r"), "", $val));
				$arrOptions[$val] = $val;
			}
			
			$this->tpl->assign_block_vars('tabs.fieldset.field', array(
				'NAME'		=> $row['name'],
				'FIELD'		=> new hdropdown('gr_field_'.$row['id'], array('options' => $arrOptions, 'value' => (isset($this->data[$row['id']]) ? $this->data[$row['id']]['input'] : ''))),
				'REQUIRED'	=> ($row['required']),
				'HELP'		=> $row['help'],
				'ID'		=> 'dl_'.$row['id'],
			));
		}
		
		//Group Label
		if ($row['type'] == 3){
			if (!$blnGroupOpen){
				$this->tpl->assign_block_vars('tabs.fieldset', array(
					'NAME'	=> $row['name'],
					'ID'	=> utf8_strtolower(str_replace(' ', '', $row['name'])),
					'FID'	=> 'dl_'.$row['id'],
				));
				$blnGroupOpen = true;
			}
		}
		
		//Plain text
		if ($row['type'] == 4){
			if (!$blnGroupOpen){
				$this->tpl->assign_block_vars('tabs.fieldset', array(
					'NAME'	=> $this->user->lang('gr_default_grouplabel'),
					'ID'	=> 'information',
				));
				$blnGroupOpen = true;
			}
			
			$this->tpl->assign_block_vars('tabs.fieldset.field', array(
					'NAME'			=> $row['name'],
					'S_NO_DIVIDER' => true,
					'ID'		=> 'dl_'.$row['id'],
			));
		}
		
		//Checkboxes
		if ($row['type'] == 5){
			if (!$blnGroupOpen){
				$this->tpl->assign_block_vars('tabs.fieldset', array(
					'NAME'	=> $this->user->lang('gr_default_grouplabel'),
					'ID'	=> 'information',
				));
				$blnGroupOpen = true;
			}
			
			$field = '';
			
			$selected = isset($this->data[$row['id']]) ? unserialize($this->data[$row['id']]['input']) : array();
			
			foreach($row['options'] as $val){
				$field .= new hcheckbox('gr_field_'.$row['id'].'['.trim($val).']', array('options' => array(1 => trim($val)), 'value' => (isset($selected[trim($val)]) ? $selected[trim($val)] : ''))).'&nbsp;&nbsp;&nbsp;';
			}
			

			$this->tpl->assign_block_vars('tabs.fieldset.field', array(
				'NAME'		=> $row['name'],
				'FIELD'		=> $field,
				'REQUIRED'	=> ($row['required']),
				'HELP'		=> $row['help'],
				'ID'		=> 'dl_'.$row['id'],
			));
		}
		
		//Radioboxes
		if ($row['type'] == 6){
			if (!$blnGroupOpen){
				$this->tpl->assign_block_vars('tabs.fieldset', array(
					'NAME'	=> $this->user->lang('gr_default_grouplabel'),
					'ID'	=> 'information',
				));
				$blnGroupOpen = true;
			}
			
			$arrOptions = array();
			foreach($row['options'] as $val){
				$arrOptions[trim($val)] = trim($val);
			}
			
			$this->tpl->assign_block_vars('tabs.fieldset.field', array(
				'NAME'		=> $row['name'],
				'FIELD'		=> new hradio('gr_field_'.$row['id'], array('value' => (isset($this->data[$row['id']]) ? $this->data[$row['id']]['input'] : ''), 'options' => $arrOptions)),
				'REQUIRED'	=> ($row['required']),
				'HELP'		=> $row['help'],
				'ID'		=> 'dl_'.$row['id'],
			));
		}
		
		//BBCode Editor
		if ($row['type'] == 7){
			if (!$blnGroupOpen){
				$this->tpl->assign_block_vars('tabs.fieldset', array(
					'NAME'	=> $this->user->lang('gr_default_grouplabel'),
					'ID'	=> 'information',
				));
				$blnGroupOpen = true;
			}

			$this->tpl->assign_block_vars('tabs.fieldset.field', array(
				'NAME'		=> $row['name'],
				'FIELD'		=> new hbbcodeeditor('gr_field_'.$row['id'], array('rows' => 6, 'value' => (isset($this->data[$row['id']]) ? $this->data[$row['id']]['input'] : ''))),
				'REQUIRED'	=> ($row['required']),
				'HELP'		=> $row['help'],
				'ID'		=> 'dl_'.$row['id'],
			));
		}
	}
	
	if(!$this->user->is_signedin() && $this->config->get('enable_captcha')) {
		require($this->root_path.'libraries/recaptcha/recaptcha.class.php');
		$captcha = new recaptcha;
		$this->tpl->assign_vars(array(
			'CAPTCHA'				=> $captcha->get_html($this->config->get('lib_recaptcha_okey')),
			'S_DISPLAY_CATPCHA'		=> true,
		));
	}
	
	$this->tpl->assign_vars(array(
		'S_USERNAME_CHECK'		=> ($this->config->get('create_account', 'guildrequest') && !$this->config->get('cmsbrige_active')) ? true : false,
		'S_HAS_APPLICATIONS'	=> ($this->user->is_signedin() && count($this->pdh->get('guildrequest_requests', 'id_list', array($this->user->id)))) ? true : false,
	));
	
    // -- EQDKP ---------------------------------------------------------------
    $this->core->set_vars(array (
      'page_title'    => $this->user->lang('gr_add'),
      'template_path' => $this->pm->get_data('guildrequest', 'template_path'),
      'template_file' => 'addrequest.html',
      'display'       => true
    ));
	
	
  }
  
  private function gen_dependency($row){
  	$arrTypes = $this->pdh->aget('guildrequest_fields', 'type', 0, array( $this->pdh->get('guildrequest_fields', 'id_list', array())));

  	if ($row['dep_field'] == 999999999){
		$expr = html_entity_decode($row['dep_value']);
		
		$expr = preg_replace("/[^a-zA-Z0-9=&|?\"'() ]/", "", $expr);
		$expr = preg_replace("#FIELD([0-9]*)#", "field_data[\"gr_field_$1\"]", $expr);
  		
  		$this->tpl->assign_block_vars("gr_listener_row", array(
  				'TARGET' => "dl_".$row['id'],
  				'EXPR'	 => $expr,
  		));
  		
  	} else {
  	
	  	$intType = $arrTypes[$row['dep_field']];
	
	  	if ($intType == 2){
	  		//Select
	  		$this->tpl->add_js('
				$(document).on("change", "#gr_field_'.$row['dep_field'].'", function () {
					gr_dep_check_value("gr_field_'.$row['dep_field'].'", "'.$row['dep_value'].'", "dl_'.$row['id'].'");
				});
				gr_dep_check_value("gr_field_'.$row['dep_field'].'", "'.$row['dep_value'].'", "dl_'.$row['id'].'");
			', 'docready');
	  	}elseif($intType == 5){
	  		//Checkbox
	  		$this->tpl->add_js('
	  			$(document).on("change", "input[name^=\'gr_field_'.$row['dep_field'].'\']", function () {
					gr_dep_check_cb("gr_field_'.$row['dep_field'].'", "'.$row['dep_value'].'", "dl_'.$row['id'].'");
				});
				gr_dep_check_cb("gr_field_'.$row['dep_field'].'", "'.$row['dep_value'].'", "dl_'.$row['id'].'");
			', 'docready');
	  	}elseif($intType == 6){
	  		//Radio
	  		$this->tpl->add_js('
			$(document).on("change", "input[name=\'gr_field_'.$row['dep_field'].'\']", function () {
					gr_dep_check_radio("gr_field_'.$row['dep_field'].'", "'.$row['dep_value'].'", "dl_'.$row['id'].'");
				});
				gr_dep_check_radio("gr_field_'.$row['dep_field'].'", "'.$row['dep_value'].'", "dl_'.$row['id'].'");
			', 'docready');
	  	}
  	}
  	
  }
  
  private function add_personal_group(){
  	
  	
	$this->tpl->assign_block_vars('tabs.fieldset', array(
		'NAME'	=> $this->user->lang('gr_personal_information'),
		'ID'	=> 'personal_information',
	));
	
	if($this->user->is_signedin()) return;

	$this->tpl->assign_block_vars('tabs.fieldset.field', array(
		'NAME'		=> $this->user->lang('name'),
		'FIELD'		=> new htext('gr_name', array('js' => 'style="width:95%"', 'value' => (isset($this->data['name']) ? $this->data['name']['input'] : ''))),
		'REQUIRED'	=> true,
	));
	

	$this->tpl->assign_block_vars('tabs.fieldset.field', array(
		'NAME'		=> $this->user->lang('email'),
		'FIELD'		=> new htext('gr_email', array('js' =>'style="width:95%"', 'value' => (isset($this->data['email']) ? $this->data['email']['input'] : ''))),
		'REQUIRED'	=> true,
		'HELP'		=> $this->user->lang('gr_email_help'),
	));
  }
}
?>