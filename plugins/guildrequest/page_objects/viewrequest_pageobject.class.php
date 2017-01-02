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

class viewrequest_pageobject extends pageobject
{
  /**
   * __dependencies
   * Get module dependencies
   */
  public static function __shortcuts()
  {
    $shortcuts = array('email' => 'MyMailer');
    return array_merge(parent::__shortcuts(), $shortcuts);
  }
  
  /**
   * Constructor
   */
  public function __construct()
  {
    // plugin installed?
    if (!$this->pm->check('guildrequest', PLUGIN_INSTALLED))
      message_die($this->user->lang('gr_plugin_not_installed'));
	
    $handler = array(
		'vote' => array('process' => 'vote', 'csrf' => true),
		'close' => array('process' => 'close', 'csrf' => true),
		'open' => array('process' => 'open', 'csrf' => true),
		'status_change' => array('process' => 'status_change', 'csrf' => true),
    );
    parent::__construct(false, $handler);

    $this->process();
  }
  
  public function close(){
	$this->user->check_auth('a_guildrequest_manage');
	$row = $this->pdh->get('guildrequest_requests', 'id', array($this->url_id));
	if ($row){
		//Close
		$this->pdh->put('guildrequest_requests', 'close', array($row['id']));
		$this->pdh->process_hook_queue();
		
		$arrStatus = $this->user->lang('gr_status');
		
		$this->hooks->process('gr_close_request', array($row));
		
		$bodyvars = array(
			'USERNAME'		=> $row['username'],
			'COMMENT'		=> $this->in->get('comment', '', 'htmlescape'),
			'STATUS'		=> $arrStatus[$row['status']],
			'DATE'			=> $this->time->user_date($row['tstamp']),
			'GUILDTAG'		=> $this->config->get('guildtag'),
		);
		
		$this->email->SendMailFromAdmin(register('encrypt')->decrypt($row['email']), $this->user->lang('gr_closed_subject'), $this->root_path.'plugins/guildrequest/language/'.$this->user->data['user_lang'].'/email/request_closed.html', $bodyvars);
		
		//Notify applicant
		if($row['user_id'] > 0){
			$this->ntfy->add('guildrequest_new_update_own', $row['id'], $this->pdh->get('user', 'name', array($this->user->id)), $this->routing->build('ViewApplication', $row['username'], $row['id']), $row['user_id']);
		}
	}
  }
  
  public function open(){
	$this->user->check_auth('a_guildrequest_manage');
	$row = $this->pdh->get('guildrequest_requests', 'id', array($this->url_id));
	if ($row){
		//Open
		$this->pdh->put('guildrequest_requests', 'open', array($row['id']));
		$this->pdh->process_hook_queue();
		
		$this->hooks->process('gr_open_request', array($row));
		
		//Notify applicant
		if($row['user_id'] > 0){
			$this->ntfy->add('guildrequest_new_update_own', $row['id'], $this->pdh->get('user', 'name', array($this->user->id)), $this->routing->build('ViewApplication', $row['username'], $row['id']), $row['user_id']);
		}
	}
  }
  
  public function status_change(){
	$this->user->check_auth('a_guildrequest_manage');
	$row = $this->pdh->get('guildrequest_requests', 'id', array($this->url_id));
	if ($row){
		$this->pdh->put('guildrequest_requests', 'update_status', array($row['id'], $this->in->get('gr_status', 0)));
		$this->pdh->process_hook_queue();
		
		$arrStatus = $this->user->lang('gr_status');
		
		$this->hooks->process('gr_change_status', array('status' => $this->in->get('gr_status', 0), 'data'=> $row));
		
		//Auto Create Account for this user
		if($this->in->get('gr_status', 0) === 2 && $this->config->get('create_account', 'guildrequest') && !$this->config->get('cmsbrige_active') && $row['user_id']===0){
			if($this->pdh->get('user', 'check_username', array($row['username'])) !== 'false' && $this->pdh->get('user', 'check_email', array($row['email'])) !== 'false'){
				
				$salt = $this->user->generate_salt();
				$strPwdHash = $this->user->encrypt_password(random_string(), $salt);
				$newUserId = $this->pdh->put('user', 'insert_user_bridge', array($row['username'], $strPwdHash, register('encrypt')->decrypt($row['email'])));
				
				// Email them their new password
				$user_key = $this->pdh->put('user', 'create_new_activationkey', array($newUserId));
				if(!strlen($user_key)) {
					$this->core->message($this->user->lang('error_set_new_pw'), $this->user->lang('error'), 'red');
				}
				$strPasswordLink = $this->env->link . $this->controller_path_plain. 'Login/Newpassword/?key=' . $user_key;
				
				$bodyvars = array(
						'USERNAME'	=> $row['username'],
						'U_ACTIVATE'=> $this->user->lang('email_changepw').'<br /><br /><a href="'.$strPasswordLink.'">'.$strPasswordLink.'</a>',
						'GUILDTAG'	=> $this->config->get('guildtag'),
				);
				
				$this->email->SendMailFromAdmin(register('encrypt')->decrypt($row['email']), $this->user->lang('email_subject_activation_none'), 'register_activation_none.html', $bodyvars);
			}
		}
		
		
		$bodyvars = array(
			'USERNAME'		=> $row['username'],
			'COMMENT'		=> (strlen($this->in->get('gr_status_text'))) ? '----------------------------<br />'.$this->in->get('gr_status_text').'<br />----------------------------<br />' : '',
			'STATUS'		=> $arrStatus[$this->in->get('gr_status', 0)],
			'DATE'			=> $this->time->user_date($row['tstamp']),
			'GUILDTAG'		=> $this->config->get('guildtag'),
		);
		
		$this->email->SendMailFromAdmin(register('encrypt')->decrypt($row['email']), $this->user->lang('gr_status_subject'), $this->root_path.'plugins/guildrequest/language/'.$this->user->data['user_lang'].'/email/request_status_change.html', $bodyvars);
		
		//Notify applicant
		if($row['user_id'] > 0){
			$this->ntfy->add('guildrequest_new_update_own', $row['id'], $this->pdh->get('user', 'name', array($this->user->id)), $this->routing->build('ViewApplication', $row['username'], $row['id']), $row['user_id']);
		}
	}
  }
  
  public function vote(){
	$this->user->check_auth('u_guildrequest_vote');
	$intID = $this->url_id;
	
	if ($intID && $this->user->is_signedin()){
		$rrow = $this->pdh->get('guildrequest_requests', 'id', array($this->url_id));
		$arrVotedUser = ($rrow['voted_user'] != '') ? unserialize($rrow['voted_user']) : array();
		if (!isset($arrVotedUser[$this->user->id])) {
			$intYes = $rrow['voting_yes'];
			$intNo = $rrow['voting_no'];
			if ($this->in->get('gr_vote') == 'yes'){
				$intYes++;
			} else {
				$intNo++;
			}
			$arrVotedUser[$this->user->id] = ($this->in->get('gr_vote') == 'yes') ? 'yes' : 'no';
			$this->pdh->put('guildrequest_requests', 'update_voting', array(
				$intID, $intYes, $intNo, $arrVotedUser
			));
			
			$this->pdh->process_hook_queue();
		}
	}
  }
  
  public function display()
  {

	if ($this->in->get('msg') == 'success'){
		$this->core->message($this->user->lang('gr_request_success'), $this->user->lang('success'), 'green');
		$this->tpl->assign_var('S_SUCCESS_MSG', true);
	}
	//prüfe ID und Key
	$intID = intval($this->url_id);
	$strKey = $this->in->get('key');
	$rrow = false;
	$blnIsApplicant = false;
	
	if ($intID){
		$rrow = $this->pdh->get('guildrequest_requests', 'id', array($intID));
		
		if (strlen($strKey)){
			if($rrow['auth_key'] != $this->in->get('key')) message_die($this->user->lang('noauth'));
			$blnIsApplicant = true;
		} elseif($rrow['user_id'] > 0) {
			if($this->user->is_signedin()){
				if($this->user->id === $rrow['user_id'] ){
					$blnIsApplicant = true;
				} elseif(!$this->user->check_auth('u_guildrequest_view', false)) {
					message_die($this->user->lang('noauth'));
				}
			} else {
				message_die($this->user->lang('noauth'));
			}
		} else {
			$this->user->check_auth('u_guildrequest_view');
			if($rrow['closed']) $this->user->check_auth('u_guildrequest_view_closed'); 
		}
	} else {
		message_die($this->user->lang('noauth'));
	}
	
	//setze lastvisit bewerber
	if ($blnIsApplicant){
		$this->pdh->put('guildrequest_requests', 'set_lastvisit', array($intID));
	}
	
	//setze lastvisit user
	$this->pdh->put('guildrequest_visits', 'add', array($intID));
	
	$this->pdh->process_hook_queue();
  
	//Bewerbung anzeigen
	$arrFields = $this->pdh->get('guildrequest_fields', 'id_list', array());
	$intGroup = 0;
	$blnGroupOpen = true;
	$this->tpl->assign_block_vars('tabs', array(
	));
	$arrContent = unserialize($rrow['content']);
	
	$this->tpl->assign_block_vars('tabs.fieldset', array(
		'NAME'	=> $this->user->lang('gr_personal_information'),
	));

	$this->tpl->assign_block_vars('tabs.fieldset.field', array(
		'NAME'		=> $this->user->lang('name'),
		'FIELD'		=> $rrow['username'],
	));
	
	if ($this->user->check_auth('a_guildrequest_manage', false)){
		$this->tpl->assign_block_vars('tabs.fieldset.field', array(
			'NAME'		=> $this->user->lang('email'),
			'FIELD'		=> '<a href="mailto:'.register('encrypt')->decrypt($rrow['email']).'">'.register('encrypt')->decrypt($rrow['email']).'</a>',
		));
	}
	
	$this->tpl->assign_block_vars('tabs.fieldset.field', array(
		'NAME'		=> $this->user->lang('date'),
		'FIELD'		=> $this->time->user_date($rrow['tstamp'], true),
	));
	
	$arrValues = array();
	foreach($arrFields as $id){
		$row = $this->pdh->get('guildrequest_fields', 'id', array($id));
		if (isset($arrContent[$row['id']])) $arrValues[$id] = $arrContent[$row['id']];
		if ($row['type'] == 5){			
			$content = isset($arrContent[$row['id']]) ? unserialize($arrContent[$row['id']]) : array();
			$arrValues[$id] = array_keys($content);
		} 
	}
	
	$this->bbcode->SetSmiliePath($this->server_path.'images/smilies');
	foreach($arrFields as $id){
		$row = $this->pdh->get('guildrequest_fields', 'id', array($id));
		$row['options'] = unserialize($row['options']);
		
		//Only show neccessary fields
		if (isset($row['dep_field']) && $row['dep_field'] && $row['dep_field'] != 999999999){
			$intDepField = $row['dep_field'];
			if (!isset($arrValues[$intDepField])) continue;
				
			if (is_array($arrValues[$intDepField])){
				if (!isset($arrValues[$intDepField][$row['dep_value']])) continue;
			} else {
				if ($arrValues[$intDepField] != $row["dep_value"]) continue;
			}
		}
		
		//Close previous group
		if ($row['type'] == 3){
			$blnGroupOpen = false;
			$intGroup++;
		}
		
		if ($row['type'] == 0 || $row['type'] == 1 || $row['type'] == 2 || $row['type'] == 6){
			if (!$blnGroupOpen){
				$this->tpl->assign_block_vars('tabs.fieldset', array(
					'NAME'	=> $this->user->lang('gr_default_grouplabel'),
					'ID'	=> 'information',
				));
				$blnGroupOpen = true;
			}
			$this->tpl->assign_block_vars('tabs.fieldset.field', array(
					'NAME'			=> $row['name'],
					'FIELD'			=> isset($arrContent[$row['id']]) ? $this->autolink(nl2br($arrContent[$row['id']]),array("target"=>"_blank")) : '',
					'HELP'		=> $row['help'],
			));
		}
		
		if ($row['type'] == 5){
			if (!$blnGroupOpen){
				$this->tpl->assign_block_vars('tabs.fieldset', array(
					'NAME'	=> $this->user->lang('gr_default_grouplabel'),
					'ID'	=> 'information',
				));
				$blnGroupOpen = true;
			}
			
			$content = isset($arrContent[$row['id']]) ? unserialize($arrContent[$row['id']]) : array();

			$this->tpl->assign_block_vars('tabs.fieldset.field', array(
					'NAME'			=> $row['name'],
					'FIELD'			=> implode('; ', array_keys($content)),
					'HELP'			=> $row['help'],
			));
		}
		//BBcode Editor
		if ($row['type'] == 7){
			if (!$blnGroupOpen){
				$this->tpl->assign_block_vars('tabs.fieldset', array(
						'NAME'	=> $this->user->lang('gr_default_grouplabel'),
						'ID'	=> 'information',
				));
				$blnGroupOpen = true;
			}
				
			$content = $this->bbcode->MyEmoticons($this->bbcode->toHTML($arrContent[$row['id']]));
		
			$this->tpl->assign_block_vars('tabs.fieldset.field', array(
					'NAME'			=> $row['name'],
					'FIELD'			=> $content,
					'HELP'			=> $row['help'],
			));
		}

		//Group Label
		if ($row['type'] == 3){
			if (!$blnGroupOpen){
				$this->tpl->assign_block_vars('tabs.fieldset', array(
					'NAME'	=> $row['name'],
					'HELP'	=> $row['help'],
					'ID'	=> utf8_strtolower(str_replace(' ', '', $row['name'])),
				));
				$blnGroupOpen = true;
			}
		}
	}
	
	//Kommentare
	$comments = register('comments', array('ext'));
	$commentOptions = array('attach_id' => $intID, 'page'=>'guildrequest', 'userauth' => 'u_guildrequest_comment', 'formforguests' => true);
	if ($rrow['closed']) {
		$commentOptions['userauth'] = 'a_guildrequest_manage';
	}elseif($this->user->is_signedin() && $blnIsApplicant){
		unset($commentOptions['userauth']);
	}
	$comments->SetVars($commentOptions);
	
	$this->tpl->assign_vars(array(
		'COMMENT_COUNTER'	=> $comments->Count(),
		'COMMENTS'			=> $comments->Show(),
	));
	
	//Kommentare intern
	$int_comments = register('comments', array('int'));
	$commentOptions = array(
			'attach_id' 	=> $intID, 
			'page'			=>'guildrequest_int', 
			'userauth' 		=> 'u_guildrequest_comment_int',
			'ntfy_type' 	=> 'guildrequest_new_update',
			'ntfy_title'	=> sanitize($rrow['username']),
			'ntfy_link' 	=> $this->routing->build('ListApplications'),
			'ntfy_auth'		=> 'u_guildrequest_comment_int',
	);	
	if ($rrow['closed']) $commentOptions['userauth'] = 'a_guildrequest_manage';
	$int_comments->SetVars($commentOptions);
	
	$this->tpl->assign_vars(array(
		'INTERNAL_COMMENT_COUNTER'	=> $int_comments->Count(),
		'INTERNAL_COMMENTS'			=> $int_comments->Show(),
	));
	
	//Vote
	$voting_sum = $rrow['voting_yes'] + $rrow['voting_no'];
	$optionYesProcent = ($voting_sum) ? round(($rrow['voting_yes'] / $voting_sum)*100) : 0;
	$optionNoProcent = ($voting_sum) ? round(($rrow['voting_no'] / $voting_sum)*100) : 0;
	
	$this->tpl->assign_vars(array(
		'VOTE_YES' => $this->jquery->progressbar('gr_vote_yes', $optionYesProcent, array('text' => $this->user->lang('yes').': '.$rrow['voting_yes']." (".$optionYesProcent." %)", 'txtalign' => 'left')),
		'VOTE_NO' => $this->jquery->progressbar('gr_vote_no', $optionNoProcent, array('text' => $this->user->lang('no').': '.$rrow['voting_no']." (".$optionNoProcent." %)", 'txtalign' => 'left')),
	));
	
	$arrVotedUser = ($rrow['voted_user'] != '') ? unserialize($rrow['voted_user']) : array();
	$blnHasVoted = false;
	if (isset($arrVotedUser[$this->user->id])) $blnHasVoted = true;

	$this->jquery->Tab_header('gr_view', true);
	switch($rrow['status']){
		case 0: $icon = 'fa-info-circle'; break;
		case 1: $icon = 'fa-info-circle'; break;
		case 2: $icon = 'fa fa-check'; break;
		case 3: $icon = 'fa fa-exclamation-triangle'; break;
	}
	$arrStatus = $this->user->lang('gr_status');
	
	$this->tpl->assign_vars(array(
		'S_INTERNAL_COMMENTS'	=> $this->user->check_auth('u_guildrequest_comment_int', false),
		'S_VOTE'				=> $this->user->check_auth('u_guildrequest_vote', false),
		'STATUS_ICON'			=> 'fa ',$icon,
		'STATUS_TEXT'			=> sprintf($this->user->lang('gr_status_text'),$arrStatus[$rrow['status']]),
		'S_CLOSED'				=> ($rrow['closed']),
		'S_HAS_VOTED'			=> (!$this->user->is_signedin() || $blnHasVoted || $rrow['closed']),
		'S_IS_GR_ADMIN'			=> $this->user->check_auth('a_guildrequest_manage', false),
		'S_EXTERNAL_USER'		=> (strlen($strKey)),
		'EXTERNAL_KEY'			=> $strKey,
		'STATUS_DD'				=> new hdropdown('gr_status', array('options' => $arrStatus, 'value' => $rrow['status'])),
		'GR_USERNAME'			=> sanitize($rrow['username']),
		'GR_DATE'				=> $this->time->user_date($rrow['tstamp'], true),
		'EXTERNAL_URL'			=> $this->env->link.$this->routing->build('ViewApplication', $rrow['username'], $intID, false, true).'?key=' . $strKey,
	));
	
	//Mark Notifications as Read
	if($this->user->is_signedin()){
		$this->db->prepare("UPDATE __notifications SET `read`=1 WHERE type='guildrequest_new_update' AND user_id=? AND additional_data=?")->execute($this->user->id, sanitize($rrow['username']));
	}
	
	$this->core->set_vars(array (
      'page_title'    => $this->user->lang('gr_viewrequest').' - '.sanitize($rrow['username']),
      'template_path' => $this->pm->get_data('guildrequest', 'template_path'),
      'template_file' => 'viewrequest.html',
      'display'       => true
    ));
  }
  
  private function autolink($str, $attributes=array()) {
	  $attrs = '';
	  foreach ($attributes as $attribute => $value) {
		$attrs .= " {$attribute}=\"{$value}\"";
	  }
	$str = ' ' . $str;
	$str = preg_replace(
	  '`([^"=\'>])(((http|https|ftp)://|www.)[^\s<]+[^\s<\.)])`i',
	  '$1<a href="$2"'.$attrs.'>$2</a>',
	  $str
	);
	$str = substr($str, 1);
	$str = preg_replace('`href=\"www`','href="http://www',$str);
	// fügt http:// hinzu, wenn nicht vorhanden
	return $str;
	}
}
?>
