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
	header('HTTP/1.0 404 Not Found');exit;
}

$lang = array(
	'guildrequest'					=> 'GuildRequest',

	// Description
	'guildrequest_short_desc'		=> 'GuildRequest',
	'guildrequest_long_desc'		=> 'GuildRequest ist ein Plugin mit deren Hilfe man Gildenbewerbungen abwickeln kann.',

	'gr_manage_form'				=> 'Formular verwalten',
	'gr_vote'						=> 'Über Bewerbung abstimmen',
	'gr_view'						=> 'Bewerbungen ansehen',
	'gr_view_closed'				=> 'Geschlossene Bewerbungen ansehen',
	'gr_add'						=> 'Bewerbung schreiben',
	'gr_internal_comment'			=> 'Internen Kommentar schreiben',
	'gr_comment'					=> 'Öffentlichen Kommentar schreiben',

	'gr_plugin_not_installed'		=> 'Das GuildRequest-Plugin ist nicht installiert.',
	'gr_select_options'				=> 'Optionen (1 pro Zeile)',
	'gr_required'					=> 'Verpflichtend',
	'gr_delete_selected_fields'		=> 'Ausgewählte Felder löschen',
	'gr_types'						=> array(
		'Textfeld', 'Textbereich', 'Auswahlfeld', 'Gruppenüberschrift', 'Freitext', 'Checkboxen', 'Radio-Buttons', 'Editor',
	),
	'gr_add_field'					=> 'Neues Feld hinzufügen',
	'gr_delete_field'				=> 'Feld löschen',
	'gr_default_grouplabel'			=> 'Informationen',
	'gr_personal_information'		=> 'Persönliche Informationen',
	'gr_submit_request'				=> 'Bewerbung absenden',
	'gr_email_help'					=> 'Bitte gib eine gültige Email-Adresse an, da Du an diese Email-Adresse alle Benachrichtigungen zu Deiner Bewerbung erhältst.',
	'gr_activationmail_subject'		=> 'Aktiviere deine Bewerbung',
	'gr_viewlink_subject'			=> 'Deine Bewerbung',
	'gr_request_success'			=> 'Deine Bewerbung wurde erfolgreich gespeichert. Eine Email mit dem Link auf diese Seite wurde an Deine Email-Adresse versendet.',
	'gr_request_success_msg'		=> 'Deine Bewerbung wurde erfolgreich gespeichert. Du kannst sie jederzeit über folgenden Link aufrufen: ',
	'gr_vote'						=> 'Abstimmung',
	'gr_internal_comments'			=> 'Interne Kommentare',
	'gr_newcomment_subject'			=> 'Neuer Kommentar zu Deiner Bewerbung',
	'gr_status'						=> array('neu', 'in Bearbeitung', 'Aufgenommen', 'Abgelehnt'),
	'gr_status_text'				=> 'Deine Bewerbung befindet sich in folgendem Status: <b>%s</b>',
	'gr_vote_button'				=> 'Abstimmen',
	'gr_manage_request'				=> 'Bewerbung verwalten',
	'gr_status_help'				=> 'Der Bewerber bekommt bei einer Statusänderung automatisch eine Email gesendet. Willst Du dieser Email noch etwas hinzufügen, benutze das Eingabefeld dafür.',
	'gr_change_status'				=> 'Status ändern',
	'gr_close'						=> 'Bewerbung schließen',
	'gr_open_request'				=> 'Bewerbung wieder öffnen',
	'gr_closed_subject'				=> 'Deine Bewerbung wurde geschlossen',
	'gr_status_subject'				=> 'Deine Bewerbung: Statusänderung',
	'gr_footer'						=> '%1$s Bewerbungen gefunden / %2$s pro Seite',
	'gr_in_list'					=> 'In Liste anzeigen',
	'gr_confirm_delete_requests'	=> 'Bist du sicher, dass Du die Bewerbungen von %s löschen willst?',
	'gr_delete_selected_requests'	=> 'Ausgewählte Bewerbungen löschen',
	'gr_delete_success'				=> 'Die ausgewählten Bewerbungen wurden erfolgreich gelöscht.',
	'gr_notification'				=> '%s neue Bewerbungen/Aktualisierungen',
	'gr_notification_open'			=> '%s offene Bewerbungen',
	'gr_mark_all_as_read'			=> 'Alle Bewerbungen als gelesen markieren',
	'gr_send_notification_mails'	=> 'Benachrichtigungs-Email bei neuer Bewerbung senden',
	'gr_closed'						=> 'Die Bewerbung wurde geschlossen.',
	'gr_notification_subject'		=> 'Neue Bewerbung',
	'gr_jgrowl_notifications'		=> 'PopUp-Benachrichtigungen anzeigen',
	'gr_viewrequest'				=> 'Bewerbung ansehen',
	'gr_dependency'					=> 'Abhängigkeit Feld - Option',
	'gr_customcheck_info'			=> 'Du kannst eigene Abhängigkeitsoptionen festlegen, in dem Du als Dropdown-Option "_Custom" auswählst und im nebenstehenden Feld deinen Code eingibst.<br />Beispiel: ((FIELD1 == "MyValueOne" && FIELD2 == "MyValueTwo") || FIELD3 == "MyValueThree")<br />Beachte, dass bei eigenen Abhängigkeiten die Verpflichtend-Überprüfung nicht zuverlässig funktioniert.',
	'user_sett_fs_guildrequest'		=> 'GuildRequest',
	'user_sett_tab_guildrequest'	=> '<i class="fa fa-pencil-square-o"></i> GuildRequest',
		
	'gr_preview'					=> 'Vorschau',
	'gr_preview_info'				=> 'Diese Vorschau basiert auf dem zuletzt gespeicherten Zustand. Um den aktuellen Stand zu sehen, speichere das bearbeitete Formular ab und öffne dann die Vorschau.',

		
	'user_sett_f_ntfy_guildrequest_new_application' => 'GuildRequest: Neue Bewerbung',
	'user_sett_f_ntfy_guildrequest_new_update'	=> 'GuildRequest: Neue Kommentare',
	'user_sett_f_ntfy_guildrequest_open_applications' => 'GuildRequest: Offene Bewerbungen',
	'user_sett_f_ntfy_guildrequest_new_update_own' => 'GuildRequest: Aktualisierungen für meine Bewerbungen',
	'gr_notify_new_application'	=> "{PRIMARY} hat eine neue Bewerbung verfasst",
	'gr_notify_new_application_grouped' => "{PRIMARY} haben eine neue Bewerbung verfasst",	
	'gr_notify_new_update'	=> "Die Bewerbung von {ADDITIONAL} wurde kommentiert",
	'gr_notify_new_update_grouped' => "Es wurden {COUNT} Bewerbungen kommentiert",
	'gr_notify_new_update_own'	=> "Deine Bewerbung wurde aktualisiert",
	'gr_notify_new_update_own_grouped' => "Es wurden {COUNT} Aktualisierungen an deiner Bewerbung vorgenommen",
		
	'gr_fs_general'	=> 'Allgemein',
	'gr_f_create_account' => 'EQdkp Plus Account anlegen, wenn die Bewerbung akzeptiert wurde',
	'gr_f_help_create_account' => 'Diese Option steht nur zur Verfügung, wenn keine CMS-Bridge aktiviert ist',
	'gr_myapplications' => 'Meine Bewerbungen',
		
	"plugin_statistics_guildrequest_applications" => "GuildRequest: Bewerbungen",
);

?>
