<?php
// +--------------------------------------------------------------------------+
// | PM Plugin - glFusion CMS                                                 |
// +--------------------------------------------------------------------------+
// | German_utf-8.php                                                         |
// |                                                                          |
// | German language file                                                     |
// | Modifiziert: August 09 Tony Kluever									  |
// +--------------------------------------------------------------------------+
// | $Id::                                                                   $|
// +--------------------------------------------------------------------------+
// | Copyright (C) 2009-2010 by the following authors:                        |
// |                                                                          |
// | Mark R. Evans          mark AT glfusion DOT org                          |
// +--------------------------------------------------------------------------+
// |                                                                          |
// | This program is free software; you can redistribute it and/or            |
// | modify it under the terms of the GNU General Public License              |
// | as published by the Free Software Foundation; either version 2           |
// | of the License, or (at your option) any later version.                   |
// |                                                                          |
// | This program is distributed in the hope that it will be useful,          |
// | but WITHOUT ANY WARRANTY; without even the implied warranty of           |
// | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the            |
// | GNU General Public License for more details.                             |
// |                                                                          |
// | You should have received a copy of the GNU General Public License        |
// | along with this program; if not, write to the Free Software Foundation,  |
// | Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.          |
// |                                                                          |
// +--------------------------------------------------------------------------+

if (!defined ('GVERSION')) {
    die ('This file can not be used on its own.');
}

$LANG_PM00 = array (
    'menulabel'         => 'Private-Nachrichten',
    'plugin'            => 'pm',
    'admin_menu'        => 'PN-Admin',
    'pm_index'          => 'PN-Index',
    'pm'                => 'PN',
    'user_menu'         => 'Nachrichtenzentale',
    'title'             => 'Private Nachrichte',
    'compose_msg'       => 'Nachricht schreiben',
    'reply_msg'         => 'Nachricht beantworten',
    'preview_msg'       => 'Nachrichtenvorschau',
    'quote_msg'         => 'Nachricht zitieren',
    'delete_msg'        => 'Nachricht löschen',
    'inbox'             => 'Eingang',
    'sent'              => 'Gesendet',
    'archive'           => 'Archiv',
    'outbox'            => 'Ausgang',
    'all_messages'      => 'Alle Nachrichten',
    'one_day'           => '1 Tag',
    'seven_days'        => '7 Tage',
    'two_weeks'         => '2 Wochen',
    'one_month'         => '1 Monat',
    'three_months'      => '3 Monate',
    'six_months'        => '6 Monate',
    'one_year'          => '1 Jahr',
    'author'            => 'Autor',
    'post_time'         => 'Abgesendet:',
    'subject'           => 'Betreff',
    'ascending'         => 'Aufsteigend',
    'descending'        => 'Absteigend',
    'no_subject'        => 'Kein Betreff vorhanden...',
    'by'                => 'Von',
    'to'                => 'An',
    'folder'            => 'Ordner',
    'view_folder'       => 'Ordner anzeigen',
    'go'                => 'Los',
    'messages'          => 'NACHRICHTEN',
    'select'            => 'AUSWÄHLEN',
    'display'           => 'Anzeige',
    'sort_by'           => 'Sortiert nach',
    'view_msg'          => 'Nachricht anzeigen von',
    'return_to'         => 'Zurück nach',
    'sent'              => 'Gesendet',
    'from'              => 'Von',
    'registered'        => 'Registriert',
    'location'          => 'Ort',
    'homepage'          => 'Homepage',
    'move_to_folder'    => 'Verschiebe in Ordner',
    'mark_all'          => 'Alle markieren',
    'unmark_all'        => 'Alle demarkieren',
    'delete_marked'     => 'Markierte gelöscht',
    'archive_marked'    => 'Markierte zu gespeicherten Nachrichten verschieben',
    'message_history'   => 'Nachrichtenverlauf',
    'add_user'          => 'Benutzer hinzufügen',
    'add_friend'        => 'Freund hizufügen',
    'send'              => 'Senden',
    'preview'           => 'Vorschau',
    'cancel'            => 'Abbruch',
    'submit'            => 'Abschicken',
    'batch_confirm'     => 'Möchtets Du die asugewählten Nachrichten wirklich verschieben oder löschen?',
    'delete_confirm'    => 'Möchtest Du die Nachricht wirklich löschen?',
    'no_messages'       => 'Es sind keine Nachrichten in diesem Ordner',
    'manage_friends'    => 'Freunde verwalten',
    'friend_help'       => 'Freunde ermöglicht Dir einen schnellen Zugriff zu Mitgliedern, mit denen Du oft kommunizierst.',
    'your_friends'      => 'Deine Freunde',
    'your_friends_help' => 'Um Benutzernamen zu entfernen, wähle sie aus und klicke auf Abschicken.',
    'add_new_friend'    => 'Neue Freunde hinzufügen',
    'add_new_friend_help' => 'Du kannst mehrere Benutzernamen eingeben, getrennt von einem Komma.',
    'in_friends_list'   => 'ist Dein Freund',
    'send_pm'           => 'Private Nachricht senden',
	'error'				=> 'Fehler',
	'newpm'				=> '<b>Du hast %d neue </b><a href="'.$_CONF['site_url'].'/pm/index.php"><b>private Nachricht(en)</b>',
);

$LANG_PM_NOTIFY = array(
    'pm_notify'         => 'PN-Benachrichtigungen',
	'new_pm_notification' => 'Neue private Nachricht - Benachrichtigung',
    'hello'             => 'Hallo',
    'subject'           => 'Betreff',
    'new_pm_text'       => 'Du hast eine neue Private Nachricht von',
    'disclaimer'        => 'Du erhältst Diese Nachricht, weil Du E-Mails vom Seiten-Admin erlaubt hast.  Um Deine privaten Einstellungen zu ändern, verwende bitte die Mein Account - Einstellung bei ' . $_CONF['site_url'] ,
    'sincerely'         => 'Danke!',
    'support'           => 'Unterstützung',
    'pm_block'          => 'Block other users from sending me PMs',
    'notify_header'     => 'Private Message Notification from ',
);

$LANG_PM_ERROR = array(
    'token_failure'     => 'Security Token Failure',
    'message_not_found' => 'Nachricht nicht gefunden',
    'no_to_address'     => 'Keine Adresse spezifiziert',
    'no_subject'        => 'Betreff muß ausgefüllt und mehr als vier Zeichen enthalten.',
    'no_message'        => 'Nachrichtentext muß eingetragen und mehr als vier Zeichen enthalten.',
    'unknown_user'      => 'Kann den Benutzer nicht finden:',
    'too_many_recipients' => 'Du hast zu viele Empfänger angegeben - Max. %s erlaubt.',
	'invalid_msg_id'    => 'Ungültige Nachrichten-ID',
    'invalid_reply_id'  => 'Ungültige Antwort-ID',
);

// Localization of the Admin Configuration UI
$LANG_configsections['pm'] = array(
    'label'                 => 'Private-Nachrichten',
    'title'                 => 'Private-Nachrichten - Konfiguration'
);
$LANG_confignames['pm'] = array(
    'messages_per_page'     => 'Nachrichten je Seite',
    'post_speedlimit'       => 'Nachrichten Speedlimit (Sekunden)',
    'max_recipients'        => 'Max. Anzahl der Empfänger je Nachricht',
    'displayblocks'         => 'Display glFusion Blocks',
);
$LANG_configsubgroups['pm'] = array(
    'sg_main'               => 'Haupteinstellungen',
);

$LANG_fs['pm'] = array(
    'pm_general'            => 'PN - Allgemeine Einstellungen',
);

$LANG_configselects['pm'] = array(
    0 => array('True' => 1, 'False' => 0),
    1 => array('True' => TRUE, 'False' => FALSE),
    2 => array('Left Blocks' => 0, 'Right Blocks' => 1, 'Left & Right Blocks' => 2, 'None' => 3)
);

$PLG_pm_MESSAGE1 = 'Nachricht(en) erfolgreich versendet.';
$PLG_pm_MESSAGE2 = 'Nachricht(en) erfolgreich gelöscht.';
$PLG_pm_MESSAGE3 = 'Nachricht(en) erfolgreich archiviert.';
$PLG_pm_MESSAGE4 = 'Private-Nachrichten - Speedlimit: Bitte warte eine Minute, bevor Du eine andere Nachricht versendest.';
$PLG_pm_MESSAGE5 = 'Freundesliste aktualisiert.';
?>