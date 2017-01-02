<?php
/*	Project:	EQdkp-Plus
 *	Package:	EQdkp-Plus Language File
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

 
if (!defined('EQDKP_INC')) {
	die('You cannot access this file directly.');
}

//Language: English	
//Created by EQdkp Plus Translation Tool on  2014-12-17 23:17
//File: plugins/guildrequest/language/english/lang_main.php
//Source-Language: german

$lang = array( 
	"guildrequest" => 'GuildRequest',
	"guildrequest_short_desc" => 'GuildRequest',
	"guildrequest_long_desc" => 'GuildRequest is a plugin for managing guild applications.',
	"gr_manage_form" => 'Manage Form',
	"gr_vote" => 'Voting',
	"gr_view" => 'View applications',
	"gr_view_closed" => 'View closed applications',
	"gr_add" => 'Write application',
	"gr_internal_comment" => 'Write internal comment',
	"gr_comment" => 'Write public comment',
	"gr_plugin_not_installed" => 'The GuildRequest-Plugin is not installed.',
	"gr_select_options" => 'Options (1 per line)',
	"gr_required" => 'Mandatory',
	"gr_delete_selected_fields" => 'Delete selected fields',
	"gr_types" => array(
	0 => 'Textfield',
	1 => 'Textarea',
	2 => 'Dropdown',
	3 => 'Grouplabel',
	4 => 'free text',
	5 => 'Checkboxes',
	6 => 'Radio-Buttons',
	7 => 'Editor',
	),
	"gr_add_field" => 'Add new field',
	"gr_delete_field" => 'Delete field',
	"gr_default_grouplabel" => 'Information',
	"gr_personal_information" => 'Personal information',
	"gr_submit_request" => 'Submit application',
	"gr_email_help" => 'Please provide a valid email-address, because you will get all notifications about your application to the provied email-address.',
	"gr_activationmail_subject" => 'Activate your application',
	"gr_viewlink_subject" => 'Your application',
	"gr_request_success" => 'Your application has been saved successfully. An email with the link to this page was sent to your email-address.',
	"gr_request_success_msg" => 'Your application has been saved successfully. You can view your application at any time using the following link: ',
	"gr_internal_comments" => 'Internal comments',
	"gr_newcomment_subject" => 'New comment in your application',
	"gr_status" => array(
	0 => 'new',
	1 => 'in progress',
	2 => 'Accepted',
	3 => 'Rejected',
	),
	"gr_status_text" => 'Your applications has the following status: <b>%s</b>',
	"gr_vote_button" => 'Vote',
	"gr_manage_request" => 'Manage applications',
	"gr_status_help" => 'The applicant gets an automated email on status change. If you want to add something to this email, please use the input field.',
	"gr_change_status" => 'Change status',
	"gr_close" => 'Close application',
	"gr_open_request" => 'Reopen application',
	"gr_closed_subject" => 'Your application has been closed',
	"gr_status_subject" => 'Your application: Status change',
	"gr_footer" => 'found %1$s applications / %2$s per page',
	"gr_in_list" => 'Show in application-list',
	"gr_confirm_delete_requests" => 'Are you sure you want to delete the applications of %s ?',
	"gr_delete_selected_requests" => 'Delete selected applications',
	"gr_delete_success" => 'The selected applications have been deleted successfully.',
	"gr_notification" => '%s new Applications/Comments',
	"gr_notification_open" => '%s open Applications',
	"gr_mark_all_as_read" => 'Mark all Applications as read',
	"gr_send_notification_mails" => 'Send Notification Email on new application',
	"gr_closed" => 'This application is closed.',
	"gr_notification_subject" => 'New application',
	"gr_jgrowl_notifications" => 'Show PopUp Notifications',
	"gr_viewrequest" => 'View Application',
	"gr_dependency" => 'Dependency (Field - Option)',
	"gr_customcheck_info" => 'You can add your own dependecy checks, if you select "_Custom" and enter your expression the field on the right.<br />Example: ((FIELD1 == "MyValueOne" && FIELD2 == "MyValueTwo") || FIELD3 == "MyValueThree")<br />Please not that the required-check will not work correctly for custom dependencies.',
	"user_sett_fs_guildrequest" => 'GuildRequest',
	"user_sett_tab_guildrequest" => '<i class="fa fa-pencil-square-o"></i> GuildRequest',
	"gr_preview" => 'Preview',
	"gr_preview_info" => 'This Preview is generated from the latest saved form. To get the current form, please save your modified form and click on the Preview-Button at the Form Page.',
		
	'user_sett_f_ntfy_guildrequest_new_application' => 'GuildRequest: New application',
	'user_sett_f_ntfy_guildrequest_new_update'	=> 'GuildRequest: New Comments',
	'user_sett_f_ntfy_guildrequest_open_applications' => 'GuildRequest: Open applications',
	'user_sett_f_ntfy_guildrequest_new_update_own' => 'GuildRequest: Updates for my own applications',
	'gr_notify_new_application'	=> "{PRIMARY} added a new application",
	'gr_notify_new_application_grouped' => "{PRIMARY} added a new application",
	'gr_notify_new_update'	=> "The application of {ADDITIONAL} has been commented",
	'gr_notify_new_update_grouped' => "{COUNT} applications have been commented",
		
	'gr_notify_new_update_own'	=> "Your Application was updated",
	'gr_notify_new_update_own_grouped' => "There have been {COUNT} updates on your application",
		
	'gr_fs_general'	=> 'General',
	'gr_f_create_account' => 'Create EQdkp Plus Account, if Application was accepted',
	'gr_f_help_create_account' => 'This option is available if no CMS Bridge is active',
	'gr_myapplications' => 'My Applications',
		
	"plugin_statistics_guildrequest_applications" => "GuildRequest: Applications",
);

?>