<?php
// +--------------------------------------------------------------------------+
// | PM Plugin - glFusion CMS                                                 |
// +--------------------------------------------------------------------------+
// | english_utf-8.php                                                        |
// |                                                                          |
// | English language file                                                    |
// +--------------------------------------------------------------------------+
// | Copyright (C) 2009-2016 by the following authors:                        |
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
    'compose'           => 'Compose',
    'reply_msg'         => 'Nachricht beantworten',
    'reply'             => 'Reply',
    'preview_msg'       => 'Nachrichtenvorschau',
    'quote_msg'         => 'Nachricht zitieren',
    'quote'             => 'Quote',
    'delete_msg'        => 'Nachricht löschen',
    'delete'            => 'Delete',
    'inbox'             => 'Eingang',
    'sent'              => 'Gesendet',
    'date'              => 'Date',
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
    'sent'              => 'Sent',
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
    'archive_confirm'   => 'Are you sure you want to archive the selected messages?',
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
    'error'             => 'Fehler',
    'newpm'             => '<b>You have %d new </b><a href="'.$_CONF['site_url'].'/pm/index.php"><b>private message(s)</b>',
    'address_book'      => 'Address Book',
    'site_users'        => 'Site Users',
    'options'           => 'Options',
    'FONTCOLOR' => 'Color',
    'FONTSIZE' => 'Font',
    'CLOSETAGS' => 'Close Tags',
    'CODETIP' => 'Tip: Styles can be applied quickly to selected text',
    'TINY' => 'Tiny',
    'SMALL' => 'Small',
    'NORMAL' => 'Normal',
    'LARGE' => 'Large',
    'HUGE' => 'Huge',
    'DEFAULT' => 'Default',
    'DKRED' => 'Dark Red',
    'RED' => 'Red',
    'ORANGE' => 'Orange',
    'BROWN' => 'Brown',
    'YELLOW' => 'Yellow',
    'GREEN' => 'Green',
    'OLIVE' => 'Olive',
    'CYAN' => 'Cyan',
    'BLUE' => 'Blue',
    'DKBLUE' => 'Dark Blue',
    'INDIGO' => 'Indigo',
    'VIOLET' => 'Violet',
    'WHITE' => 'White',
    'BLACK' => 'Black',
    'ID'    => 'ID',
    'b_help' => 'Bold text: [b]text[/b]',
    'i_help' => 'Italic text: [i]text[/i]',
    'u_help' => 'Underline text: [u]text[/u]',
    'q_help' => 'Quote text: [quote]text[/quote]',
    'c_help' => 'Code display: [code]code[/code]',
    'l_help' => 'List: [list]text[/list]',
    'o_help' => 'Ordered list: [olist]text[/olist]',
    'p_help' => '[img]http://image_url[/img]  or [img w=100 h=200][/img]',
    'w_help' => 'Insert URL: [url]http://url[/url] or [url=http://url]URL text[/url]',
    'a_help' => 'Close all open bbCode tags',
    's_help' => 'Font color: [color=red]text[/color]  Tip: you can also use color=#FF0000',
    'f_help' => 'Font size: [size=x-small]small text[/size]',
    'h_help' => 'Click to view more detailed help',
    't_help' => 'Use [file]#[/file] to embed an attached image in the post',
    'e_help' => 'List item: [*]text',
    'smiley' => 'Smileys',

);

$LANG_PM_NOTIFY = array(
    'pm_notify'         => 'PM Notifications',
    'new_pm_notification' => 'New Private Message Notification',
    'hello'             => 'Hello',
    'subject'           => 'Subject',
    'new_pm_text'       => 'You have a new Private Message from',
    'disclaimer'        => 'You are receiving this because you allow Private Message notifications.  To change your privacy settings, please use the My Account option at ' . $_CONF['site_url'] ,
    'sincerely'         => 'Thanks!',
    'support'           => 'Support',
    'pm_block'          => 'Block other users from sending me PMs',
    'notify_header'     => 'Private Message Notification from ',
);

$LANG_PM_ERROR = array(
    'token_failure'     => 'Security Token Failure',
    'message_not_found' => 'Message not found',
    'no_to_address'     => 'No to address specified',
    'no_subject'        => 'Subject must not be blank and must be greater than 4 characters in length.',
    'no_message'        => 'Message body must not be blank and must be greater than 4 characters in length.',
    'unknown_user'      => 'Unable to locate user:',
    'too_many_recipients' => 'You have included too many recipients - Maximum %s allowed.',
    'invalid_msg_id'    => 'Invalid Message ID',
    'invalid_reply_id'  => 'Invalid Reply ID',
    'private_user'      => 'User does not allow PM messages',
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

$LANG_configSelect['pm'] = array(
    0 => array(1 => 'True', 0 => 'False'),
    1 => array(true => 'True', false => 'False'),
    2 => array(0 => 'Left Blocks', 1 => 'Right Blocks', 2 => 'Left & Right Blocks', 3 => 'None')
);

$PLG_pm_MESSAGE1 = 'Nachricht(en) erfolgreich versendet.';
$PLG_pm_MESSAGE2 = 'Nachricht(en) erfolgreich gelöscht.';
$PLG_pm_MESSAGE3 = 'Nachricht(en) erfolgreich archiviert.';
$PLG_pm_MESSAGE4 = 'Private-Nachrichten - Speedlimit: Bitte warte eine Minute, bevor Du eine andere Nachricht versendest.';
$PLG_pm_MESSAGE5 = 'Freundesliste aktualisiert.';
?>