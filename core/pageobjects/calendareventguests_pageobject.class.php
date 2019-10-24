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

class calendareventguests_pageobject extends pageobject {

	public function __construct() {
		$handler = array();
		parent::__construct(false, $handler, array());
		$this->process();
	}

	private function is_raidleader(){
		$ev_ext				= $this->pdh->get('calendar_events', 'extension', array($this->in->get('eventid', 0)));
		$raidleaders_chars	= ($ev_ext['raidleader'] > 0) ? $ev_ext['raidleader'] : array();
		$raidleaders_users	= $this->pdh->get('member', 'userid', array($raidleaders_chars));
		return (is_array($raidleaders_users) && in_array($this->user->data['user_id'], $raidleaders_users)) ? true : false;
	}

	public function add(){
		if($this->user->check_auth('a_cal_revent_conf', false) || $this->is_raidleader()){
			if($this->in->get('membername')){
				if($this->in->get('guestid', 0) > 0){
					$blub = $this->pdh->put('calendar_raids_guests', 'update_guest', array(
						$this->in->get('guestid', 0), $this->in->get('class'), $this->in->get('group'), $this->in->get('note')
					));
				}else{
					$blub = $this->pdh->put('calendar_raids_guests', 'insert_guest', array(
						$this->in->get('eventid', 0), $this->in->get('membername'), $this->in->get('class'), $this->in->get('group'), $this->in->get('note')
					));
				}
			}

		}else{
			if (!$this->user->is_signedin() && $this->config->get('enable_captcha') == 1 && $this->config->get('lib_recaptcha_pkey') && strlen($this->config->get('lib_recaptcha_pkey'))){
				require($this->root_path.'libraries/recaptcha/recaptcha.class.php');
				$captcha = new recaptcha;
				$response = $captcha->check_answer ($this->config->get('lib_recaptcha_pkey'), $this->env->ip, $this->in->get('g-recaptcha-response'));
				if (!$response->is_valid) {
					$this->core->message($this->user->lang('lib_captcha_wrong'), $this->user->lang('error'), 'red');
					return;
				}
			}

			// check if the email is validate
			if(!preg_match("/^([a-zA-Z0-9])+([\.a-zA-Z0-9_\-\+])*@([a-zA-Z0-9_-])+(\.[a-zA-Z0-9_-]+)+/", $this->in->get('email'))){
				$this->display( $this->user->lang('fv_invalid_email') );
				return;
			}

			// check if name is empty
			if($this->in->get('membername') == ''){
				$this->display( $this->user->lang('fv_invalid_name') );
				return;
			}

			// check if email already joined
			if($this->pdh->get('calendar_raids_guests', 'check_email', array($this->in->get('eventid', 0), $this->in->get('email'))) == 'true'){
				$this->display( str_replace("{0}", $this->in->get('email'), $this->user->lang('fv_email_alreadyuse')) );
				return;
			}
			$blub = $this->pdh->put('calendar_raids_guests', 'insert_guest', array(
				$this->in->get('eventid', 0), $this->in->get('membername'), $this->in->get('class'), 0, $this->in->get('note'), $this->in->get('email')
			));
		}
		$this->pdh->process_hook_queue();
		$this->tpl->add_js('jQuery.FrameDialog.closeDialog();');
	}

	public function display($error=false){
		if($error) {
			$this->core->message($error, $this->user->lang('error'), 'red');
		}
		$guestdata = ($this->in->get('guestid', 0) > 0) ? $this->pdh->get('calendar_raids_guests', 'guest', array($this->in->get('guestid', 0))) : array();

		$display_captcha = false;
		if (!$this->user->is_signedin() && $this->config->get('enable_captcha') == 1){
			require($this->root_path.'libraries/recaptcha/recaptcha.class.php');
			$captcha = new recaptcha;
			$display_captcha = $captcha->get_html($this->config->get('lib_recaptcha_okey'));
		}

		$this->tpl->assign_vars(array(
			'PERM_ADD'				=> ($this->user->check_auth('a_cal_revent_conf', false) || $this->is_raidleader()) ? true : false,
			'PERM_GUESTAPPLICATION'	=> ($this->config->get('calendar_raid_guests') == 2) ? true : false,
			'EVENT_ID'				=> $this->in->get('eventid', 0),
			'GUEST_ID'				=> $this->in->get('guestid', 0),
			'CLASS_DD'				=> new hdropdown('class', array('options' => $this->game->get_primary_classes(array('id_0')), 'value' => ((isset($guestdata['class'])) ? $guestdata['class'] : ''))),

			// captcha
			'CEG_CAPTCHA'			=> $display_captcha,
			'S_CEG_DISPLAY_CATPCHA' => (($this->user->is_signedin()) ? false : true),

			// the edit input
			'MEMBER_NAME'			=> (isset($guestdata['name'])) ? sanitize($guestdata['name']) : '',
			'NOTE'					=> (isset($guestdata['note'])) ? sanitize($guestdata['note']) : '',
		));

		$this->core->set_vars(array(
			'page_title'		=> $this->user->lang('raidevent_raid_guests'),
			'header_format'		=> 'simple',
			'template_file'		=> 'calendar/guests.html',
			'display'			=> true
		));
	}
}
?>
