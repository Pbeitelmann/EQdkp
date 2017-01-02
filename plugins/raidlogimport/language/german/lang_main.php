<?php
/*	Project:	EQdkp-Plus
 *	Package:	RaidLogImport Plugin
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

	$lang['raidlogimport'] = 'Raid-Log-Import';
	$lang['action_raidlogimport_bz_upd'] = 'Boss / Zone bearbeitet';
	$lang['action_raidlogimport_bz_add'] = 'Boss / Zone hinzugefügt';
	$lang['action_raidlogimport_bz_del'] = 'Boss / Zone gelöscht';
	$lang['raidlogimport_long_desc'] = 'Das Plugin erlaubt das importieren von allerart Strings. Es können Punkte-Werte für die einzelnen Bosse und pro Stunde eingetragen werden.';
	$lang['raidlogimport_short_desc'] = 'Importiert Raid-Logs';
	$lang['links'] = 'Links';

	//permissions
	$lang['raidlogimport_bz'] = 'Boss/Zonen Verwaltung';
	$lang['raidlogimport_dkp'] = 'Raid-Log importieren';
	
	//messages
	$lang['rli_no_save'] = 'Daten nicht gespeichert!';
	$lang['rli_save_suc'] = 'Daten erfolgreich gespeichert.';
	$lang['rli_no_del'] = 'Daten nicht gelöscht!';
	$lang['rli_del_suc'] = 'Daten erfolgreich gelöscht.';

	//Bz
	$lang['rli_bz_bz'] = 'Bosse / Zonen';
	$lang['rli_bz_abz'] = 'Aktive Bosse / Zonen';
	$lang['rli_bz_ibz'] = 'Inaktive Bosse / Zonen';
	$lang['bz_boss'] = 'Bosse';
	$lang['bz_boss_s'] = 'Boss';
	$lang['bz_boss_oz'] = 'Bosse ohne Zone';
	$lang['bz_zone'] = 'Zonen';
	$lang['bz_zone_s'] = 'Zone';
	$lang['bz_no_zone'] = 'keine Zone';
	$lang['bz_string'] = 'String';
	$lang['bz_bnote'] = 'Notiz';
	$lang['bz_bonus'] = 'Bonus-Punkte';
	$lang['bz_timebonus'] = 'Punkte pro Stunde';
	$lang['bz_diff'] = 'Schwierigkeit';
	$lang['bz_zevent'] = 'Event';
	$lang['bz_update'] = 'Hinzufügen / Markierte bearbeiten';
	$lang['bz_delete'] = 'Markierte löschen';
	$lang['bz_upd'] = 'Bosse / Zonen bearbeiten';
	$lang['bz_type'] = 'Art';
	$lang['bz_note_event'] = 'Notiz / Event';
	$lang['bz_yes'] = 'Ja!';
	$lang['bz_no'] = 'Nein!';
	$lang['bz_no_id'] = 'Nichts ausgewählt.';
	$lang['bz_del'] = 'Bosse / Zonen löschen';
	$lang['bz_confirm_del'] = 'Wollen Sie wirklich Folgendes löschen?';
	$lang['bz_tozone'] = 'In Zone';
	$lang['bz_suc'] = 'Boss / Zonen Erfolg';
	$lang['bz_missing_values'] = 'Es müssen alle Felder ausgefüllt sein.';
	$lang['bz_sort'] = 'Reihenfolge';
	$lang['bz_copy_zone'] = 'Kopiere markierte Zone (samt Bossen) zu Schwierigkeit: ';
	$lang['bz_copy_suc'] = 'Kopieren erfolgreich.';
	$lang['bz_no_copy'] = 'Kopieren fehlgeschlagen!';
	$lang['bz_import_boss'] = 'Boss importieren';
	$lang['bz_set_inactive'] = 'Markierte Zonen (samt Bossen) inaktiv/aktiv schalten';
	$lang['bz_active_suc'] = 'Aktiv/Inaktiv für markierte Zonen umgeschaltet';

	//dkp
	$lang['rli_dkp_insert'] = 'Neues Raid-Log einfügen';
	$lang['rli_data_source'] = 'Daten-Quelle wählen';
	$lang['rli_continue_old'] = 'Vorherigen Importvorgang fortsetzen';
	$lang['rli_send'] = 'Absenden';
	$lang['rli_raidinfo'] = 'Raid Infos';
	$lang['rli_start'] = 'Start';
	$lang['rli_end'] = 'Ende';
	$lang['rli_bosskills'] = 'Bosskills';
	$lang['rli_cost'] = 'Kosten';
	$lang['rli_success'] = 'Erfolg';
	$lang['rli_error'] = 'Daten nicht gespeichert, da ein Fehler aufgetreten ist!';
	$lang['rli_no_mem_create'] = " konnte nicht erzeugt werden. Bitte manuell erstellen";
	$lang['rli_mem_auto'] = " wurde automatische erzeugt";
	$lang['rli_raid_to'] = 'Raid auf %1$s am %2$s';
	$lang['rli_raid_value'] = 'Raidwert';
	$lang['rli_t_points'] = 'Punkte pro Stunde';
	$lang['rli_e_points'] = 'Ereignis-Punkte';
	$lang['rli_b_dkp'] = 'Boss-Punkte';
	$lang['rli_looter'] = 'Looter';
	$lang['xml_error'] = 'XML-Error. Bitte überprüf das Log!';
	$lang['parse_error'] = 'Parse-Error!';
	$lang['rli_check_data'] = 'Daten prüfen';
	$lang['rli_clock'] = 'Uhr';
	$lang['rli_hour'] = 'Stunde';
	$lang['rli_att'] = 'Anwesenheit';
	$lang['rli_checkmem'] = 'Member-Daten überprüfen';
	$lang['rli_back2raid'] = 'Zurück zu Raids';
	$lang['rli_checkraid'] = 'Raids überprüfen';
	$lang['rli_checkitem'] = 'Items überprüfen';
	$lang['rli_itempage'] = 'Itemseite ';
	$lang['rli_back2mem'] = 'Zurück zu Membern';
	$lang['rli_back2item'] = 'Zurück zu Items';
	$lang['rli_checkadj'] = 'Korrekturen überprüfen';
	$lang['rli_calc_note_value'] = 'Raidnotiz neu erstellen';
	$lang['rli_insert'] = 'Raid(s) Einfügen';
	$lang['rli_adjs'] = 'Korrekturen';
	$lang['rli_partial_raid'] = 'Teilweise Raidteilnahme';
	$lang['rli_add_raid'] = 'Raid hinzufügen';
	$lang['rli_add_raids'] = 'Raids hinzufügen';
	$lang['rli_delete_raids_warning'] = 'Soll der Raid/Boss wirklich gelöscht werden?';
	$lang['rli_add_mem'] = 'Mitglied hinzufügen';
	$lang['rli_add_mems'] = 'Mitglieder hinzufügen';
	$lang['rli_delete_members_warning'] = 'Soll der Member wirklich gelöscht werden?';
	$lang['rli_add_time'] = 'Zeitbalken hinzufügen';
	$lang['rli_standby_switch'] = 'Ersatzbank umschalten';
	$lang['rli_del_time'] = 'Zeitbalken löschen';
	$lang['rli_bossname'] = 'Name des Bosses:';
	$lang['rli_bosstime'] = 'Killzeitpunkt:';
	$lang['rli_bossvalue'] = 'Wert / Bonus:';
	$lang['rli_add_item'] = 'Item hinzufügen';
	$lang['rli_delete_items_warning'] = 'Soll das Item wirklich gelöscht werden?';
	$lang['rli_item_id'] = 'Item-ID';
	$lang['rli_add_adj'] = 'Korrektur hinzufügen';
	$lang['rli_add_adjs'] = 'Korrekturen hinzufügen';
	$lang['rli_add_bk'] = 'Bosskill hinzufügen';
	$lang['rli_add_bks'] = 'Bosskills hinzufügen';
	$lang['rli_imp_no_suc'] = 'Import nicht erfolgreich';
	$lang['rli_imp_suc'] = 'Import erfolgreich';
	$lang['rli_members_needed'] = 'Keine Mitglieder gefunden.';
	$lang['rli_raids_needed'] = 'Keine Raids gefunden.';
	$lang['rli_missing_values'] = 'Es fehlen einige Werte. Bitte überprüfe: ';
	$lang['rli_miss'] = 'Folgende Nodes fehlen: ';
	$lang['rli_lgaobk'] = 'Log Guild Attendees on Bosskill muss vor und während des Aufzeichnens deaktiviert sein. Wenn du den Raid trotzdem importieren willst, musst du alle Joins mit der selben Zeit von Bosskills löschen.';
	$lang['wrong_game'] = 'Das Raidlog stammt nicht aus dem unter Konfiguration angegebenen Spiel!';
	$lang['rli_process'] = 'Ausführen';
	$lang['check_raidval'] = 'Raid-Werte überprüfen';
	$lang['rli_choose_mem'] = 'Member wählen ...';
	$lang['rli_go_on'] = 'Weiter';
	$lang['rli_raidatt_upd'] = 'Auf "Aktualisieren" drücken, um die Raidteilnahme mit den neuen Zeiten anzuzeigen.';
	$lang['rli_error_imagecreate'] = 'Fehler beim erstellen der Bilddatei.';
	$lang['rli_save_itempool'] = 'Itempool für markierte Items speichern.';
	$lang['rli_itempool_saved'] = 'Itempools gespeichert!';
	$lang['rli_itempool_partial_save'] = 'Itempools nur teilweise gespeichert.';
	$lang['rli_itempool_nosave'] = 'Nicht gespeicherte Items';
	$lang['rli_help'] = 'Hilfe?';
	$lang['rli_help_dt_member'] = 'Hilfe: Ein Zeitbalken steht für die jeweilige Anwesenheit im Raid. Ein grüner Balken steht für Anwesenheit, und lila Balken für Ersatzbank. Neue Zeitbalken können durch Doppelklick auf freien Platz hinzugefügt werden. Zu löschende Balken müssen angeklickt werden und werden mit der Löschen-Taste entfernt. Der Hintergrund der Zeile (rot/grün) zeigt an, ob der Teilnehmer die Raidteilnahme hat. Die Totenköpfe sind die Bosskills (Mouseover).';
	$lang['rli_member_refresh_for_view'] = 'Drücke Aktualisieren, um den Raidslider anzuzeigen.';
	$lang['rli_loading'] = 'Bitte Warten';
	$lang['rli_finish'] = 'Abschluss';
	
	// error messages
	$lang['rli_error_member_create'] = 'Erstellung des Charakters %s schlug fehl.';
	$lang['rli_error_no_raid'] = 'Es muss mindestens ein Raid erstellt werden.';
	$lang['rli_error_no_attendant']  = 'Es muss mindestens einen Raidteilnehmer geben.';
	$lang['rli_error_no_buyer'] = 'Item-Käufer des Items %s nicht in der Datenbank oder im Raid gefunden.';
	$lang['rli_error_item_no_raid'] = 'Das Item %s wurde keinem Raid zugewiesen.';
	$lang['rli_error_no_parser'] = 'Es wurde kein Parser ausgewählt.';
	$lang['rli_error_wrong_format'] = 'Der eingestellte Parser (%s) passt nicht zum Raid-Log.';

	//config
	$lang['new_member_rank'] = 'Standard-Rang bei automatischer Erstellung';
	$lang['raidcount'] = 'Wie sollen die Raids erstellt werden?';
	$lang['raidcount_0'] = 'Ein Raid für alles';
	$lang['raidcount_1'] = 'Ein Raid pro Stunde';
	$lang['raidcount_2'] = 'Ein Raid pro Boss';
	$lang['raidcount_3'] = 'Ein Raid pro Stunde und pro Boss';
	//moved from dkp
	$lang['wrong_settings'] = '<i class="fa fa-exclamation-triangle fa-2x"></i> Verkehrte Einstellungen!';
	$lang['wrong_settings_1'] = $lang['wrong_settings'].' '.$lang['raidcount_1'].' kann nicht mit keinen Zeit-Punkten kombiniert werden.';
	$lang['wrong_settings_2'] = $lang['wrong_settings'].' '.$lang['raidcount_2'].' kann nicht mit keinen Boss-Punkten kombiniert werden.';
	$lang['wrong_settings_3'] = $lang['wrong_settings'].' '.$lang['raidcount_3'].' kann nicht mit keinen Boss- und/oder Zeit-Punkten kombiniert werden.';
	//move end
	$lang['attendance_begin'] = 'Bonus für Anwesenheit am Raidbeginn';
	$lang['attendance_end'] = 'Bonus für Anwesenheit am Raidende';
	$lang['config_success'] = 'Konfigurations Erfolg';
	$lang['event_boss'] = 'Existiert für jeden Boss ein Event?';
	$lang['event_boss_1'] = 'Ja';
	$lang['event_boss_2'] = 'Als Notiz den Namen des Bosses benutzen';
	$lang['attendance_raid'] = 'Soll für die Anwesenheit ein extra Raid angelegt werden?';
	$lang['loottime'] = 'Zeit in Sekunden, die der Loot noch zum Boss davor gehört';
	$lang['attendance_time'] = 'Zeit in Sekunden, die der Invite dauert, bzw. das Raid-Ende dauert';
	$lang['rli_inst_version'] = 'Installierte Version';
	$lang['bz_parse'] = 'Trennzeichen zwischen den Strings die zu einem "Event" gehören';
	$lang['parser'] = 'Welches XML-Format hat der String?';
	$lang['parser_empty'] = 'Leerer String';
	$lang['rli_man_db_up'] = 'DB-Update erzwingen';
	$lang['use_dkp'] = 'Welche Punkte sollen verwendet werden?';
	$lang['use_dkp_1'] = 'Boss-Punkte';
	$lang['use_dkp_2'] = 'Zeit-Punkte';
	$lang['use_dkp_4'] = 'Ereignis-Punkte';
	$lang['null_sum'] = 'Null-Summen-Systen verwenden?';
	$lang['null_sum_0'] = 'Nein';
	$lang['null_sum_1'] = 'Jedes Mitglied im Raid bekommt die Punkte';
	$lang['null_sum_2'] = 'Jedes Mitglied im System bekommt die Punkte';
	$lang['deactivate_adj'] = "Korrekturen deaktivieren?";
	$lang['deactivate_adj_warn'] = "Durch setzen dieser Option bekommen die Member immer alle DKP vom Raid!";
	$lang['auto_minus'] = 'Automatischer Punkte-Abzug?';
	$lang['auto_minus_help'] = "Diese Einstellung bewirkt, dass Spieler, die die eingestellte Anzahl an Raids nicht dabeigewesen sind, Minus-DKP bekommen. Wenn Null-Summen aktiviert sind, wird das ganze über ein Item gemacht.";
	$lang['am_raidnum'] = 'Anzahl der Raids';
	$lang['am_value'] = 'Wert der abgezogenen Punkte';
	$lang['am_name'] = 'Mangelnde Teilnahme';
	$lang['am_value_raids'] = 'Punkt-Wert = Wert der letzten Anzahl Raids';
	$lang['am_allxraids'] = "Raidzähler, bei Vergabe von Minus-DKP resetten?";
	$lang['am_allxraids_help'] = "Beispiel: Ein Mitglied bekommt nach 3 Raids Minus-DKP. Beim 4ten ist er wieder nicht dabei, wenn diese Option deaktiviert ist bekommt er wieder Minus-DKP. Ist sie hingegen aktiviert, würde er erst beim 6ten Raid den er fehlt wieder Minus-DKP bekommen.";
	$lang['title_am'] = 'Automatischer Punkte-Abzug';
	$lang['title_adj'] = 'Korrekturen';
	$lang['title_att'] = 'Anwesenheit';
	$lang['title_general'] = 'Allgemein';
	$lang['title_loot'] = 'Loot / Items';
	$lang['title_parse'] = 'Parse-Einstellungen';
	$lang['title_hnh_suffix'] = 'Heroic / Non-Heroic';
	$lang['title_member'] = 'Mitglieder Einstellungen';
	$lang['ignore_dissed'] = 'Bestimmte Looter ignorieren?';
	$lang['ignore_dissed_help'] = 'z.B. Disenchanted oder bank. Separariert durch Kommata, ohne Unterscheidung von Groß- und Kleinschreibung';
	$lang['member_miss_time'] = 'Zeit in Sekunden, die ein Member fehlen kann, ohne dass er Abzüge bekommt.';
	$lang['s_member_rank'] = 'Member Rang anzeigen?';
	$lang['s_member_rank_1'] = 'Member-Übersicht';
	$lang['s_member_rank_2'] = 'Loot-Übersicht';
	$lang['s_member_rank_4'] = 'Korrekturen-Übersicht';
	$lang['rli_manual'] = 'Sollte dir die Bedeutung einiger Optionen nicht klar sein, so wirf einen Blick ins Manual (<a href="./../language/german/Manual.pdf">link</a>).';
	$lang['member_start'] = 'Start-Punkte, die ein Mitglied bekommt, wenn es automatisch erstellt wird.';
	$lang['member_start_name'] = 'Start-Punkte'; //value is used for reason of adjustment
	$lang['member_start_event'] = 'Ereignis für die Start-Punkte';
	$lang['member_raid'] = 'Ab wie viel %Teilnahme soll das Mitglied dem Raid zugewiesen werden?';
	$lang['att_note_begin'] = 'Raidnotiz des Anwesenheits-Start-Raids';
	$lang['att_note_end'] = 'Raidnotiz des Anwesenheits-End-Raids';
	$lang['raid_note_time']	= 'Raidnotiz der Raids pro Stunde';
	$lang['raid_note_time_0'] = '20:00-21:00, 21:00-22:00, usw.';
	$lang['raid_note_time_1'] = '1.Stunde, 2.Stunde, usw.';
	$lang['timedkp_handle']	= "Berechnung der Zeit-Punkte";
	$lang['timedkp_handle_help'] = "0: exakte Berechnung pro Minute<br />&gt;0: Minuten, nach denen der Member die vollen Punkte der Stunde bekommt";
	$lang['member_display'] = 'Wie soll die Member-Liste angezeigt werden?';
	$lang['member_display_0'] = 'Multi-Select';
	$lang['member_display_1'] = 'Mehrere Checkboxen';
	$lang['member_display_2'] = 'Detaillierte Join/Leave Infos';
	$lang['member_display_help'] = "Um die Ansicht '".$lang['member_display_1']."' oder '".$lang['member_display_2']."' zu verwenden, muss die GD-Lib vorhanden sein (PHP-Extension). Folgende GD-Lib wurde gefunden:<br />%s";
	$lang['no_gd_lib'] = "<span class='negative'>keine GD-Lib gefunden</span>";
	$lang['title_standby'] = 'Ersatzbank-Einstellungen';
	$lang['standby_raid'] = 'Sollen Ersatzbank-Mitglieder einem Raid zugeordnet werden?';
	$lang['standby_raid_0'] = 'Nein, keinem.';
	$lang['standby_raid_1'] = 'Ja, einen Extra-Raid anlegen.';
	$lang['standby_raid_2'] = 'Ja, dem (den) normalem/n Raid(s).';
	$lang['standby_absolute'] = 'Absolute Punkte für die Ersatzbank vergeben?';
	$lang['standby_value'] = 'Wie viel Prozent der Punkte, bzw. wie viele Punkte absolut, sollen Ersatzbankspieler bekommen?';
	$lang['standby_att'] = 'Sollen Ersatzbankspieler Einlade/Ende-Punkte bekommen?';
	$lang['standby_att_1'] = 'Einlade-Punkte';
	$lang['standby_att_2'] = 'Ende-Punkte';
	$lang['standby_dkptype'] = 'Welche Punkte sollen Ersatzbankspieler bekommen?';
	$lang['standby_dkptype_1'] = $lang['use_dkp_1'];
	$lang['standby_dkptype_2'] = $lang['use_dkp_2'];
	$lang['standby_dkptype_4'] = $lang['use_dkp_4'];
	$lang['standby_raidnote'] = 'Notiz des Raids für die Ersatzbank';
	$lang['standby_raid_note'] = 'Ersatzbank';
	$lang['itempool_save'] = 'Itempools können pro Item und Ereignis gespeichert werden.';
	$lang['itempool_save_help'] = 'Auf der Item-Seite kann für alle angezeigten Items die Itempool-Auswahl gespeichert werden. Beim nächsten Import des Items in dem Raid, wird automatisch der gespeicherte Itempool gewählt.';
	$lang['del_dbl_times'] = 'Sollen doppelte Zeiten gelöscht werden? Bei Joins die spätere, bei Leaves die frühere Zeit.';
	$lang['autocomplete'] = 'Felder mit autocomplete-Funktion versehen?';
	$lang['autocomplete_1'] = 'Mitgliedsname';
	$lang['autocomplete_2'] = 'Itemname';
	$lang['no_del_warn'] = 'Keine Warnung beim Löschen anzeigen';
	$lang['autocreate_zones'] = 'Zonen automatisch Erstellen, sofern noch nicht vorhanden';
	$lang['autocreate_bosses'] = 'Bosse automatisch Erstellen, sofern noch nicht vorhanden';

	//portal
	$lang['p_rli_zone_display'] = 'Welche Zonen sollen angezeigt werden?';
	$lang['dkpvals'] = 'Punkte-Werte';
?>