<?php
// +--------------------------------------------------------------------------+
// | PM Plugin - glFusion CMS                                                 |
// +--------------------------------------------------------------------------+
// | dutch_utf-8.php                                                          |
// |                                                                          |
// | Dutch language file                                                      |
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
    'menulabel'         => 'Prive Berichten',
    'plugin'            => 'pm',
    'admin_menu'        => 'PM Configuratie',
    'pm_index'          => 'PM Index',
    'pm'                => 'PM',
    'user_menu'         => 'Berichten Center',
    'title'             => 'Prive Bericht',
    'compose_msg'       => 'Schrijf een Bericht',
    'reply_msg'         => 'Beantwoord Bericht',
    'preview_msg'       => 'Bekijk Bericht',
    'quote_msg'         => 'Quote Bericht',
    'delete_msg'        => 'Verwijder Bericht',
    'inbox'             => 'Inbox',
    'sent'              => 'Sent',
    'archive'           => 'Archief',
    'outbox'            => 'Outbox',
    'all_messages'      => 'Alle Berichten',
    'one_day'           => '1 Dag',
    'seven_days'        => '7 Dagen',
    'two_weeks'         => '2 Weken',
    'one_month'         => '1 Maand',
    'three_months'      => '3 Maanden',
    'six_months'        => '6 Maanden',
    'one_year'          => '1 Jaar',
    'author'            => 'Auteur',
    'post_time'         => 'Post Time',
    'subject'           => 'Onderwerp',
    'ascending'         => 'Oplopend',
    'descending'        => 'Aflopend',
    'no_subject'        => 'Geen onderwerp beschikbaar...',
    'by'                => 'Door',
    'to'                => 'Aan',
    'folder'            => 'Folder',
    'view_folder'       => 'Bekijk folder',
    'go'                => 'Ga',
    'messages'          => 'BERICHTEN',
    'select'            => 'SELECTEER',
    'display'           => 'Toon',
    'sort_by'           => 'Sorteer Op',
    'view_msg'          => 'Bekijk Bericht Van',
    'return_to'         => 'Return To',
    'sent'              => 'Verzonden',
    'from'              => 'Van',
    'registered'        => 'Geregistreerd op',
    'location'          => 'Location',
    'homepage'          => 'Homepage',
    'move_to_folder'    => 'Verplaats naar Folder',
    'mark_all'          => 'Mark All',
    'unmark_all'        => 'Unmark All',
    'delete_marked'     => 'Deleted Marked',
    'archive_marked'    => 'Move marked to Saved messages',
    'message_history'   => 'Berichten Historie',
    'add_user'          => 'Voeg een Gebruiker toe',
    'add_friend'        => 'Voeg een Vriend(in) toe',
    'send'              => 'Verstuur',
    'preview'           => 'Bekijk',
    'cancel'            => 'Annuleer',
    'submit'            => 'Uitvoeren',
    'batch_confirm'     => 'Are you sure you want to move or delete the selected messages?',
    'delete_confirm'    => 'Are you sure you want to delete this message?',
    'no_messages'       => 'Er zijn geen berichten in deze folder',
    'manage_friends'    => 'Beheer uw Vrienden',
    'friend_help'       => 'Friends enable you quick access to members you communicate with frequently.',
    'your_friends'      => 'Uw vrienden',
    'your_friends_help' => 'To remove usernames select them and click submit.',
    'add_new_friend'    => 'Voeg Nieuwe Vrienden toe',
    'add_new_friend_help' => 'You may enter several usernames separated by a comma.',
    'in_friends_list'   => 'Is Uw Vriend(in)',
    'send_pm'           => 'Verstuur een Prive Bericht',
);

$LANG_PM_NOTIFY = array(
    'pm_notify'         => 'PM Meldingen',
    'new_pm_notification' => 'Melding Nieuw Prive Bericht',
    'hello'             => 'Hallo',
    'subject'           => 'Onderwerp',
    'new_pm_text'       => 'U heeft een nieuw Prive bericht van',
    'disclaimer'        => 'You are receiving this because you allow email from the site admin.  To change your privacy settings, please use the My Account setting at ' . $_CONF['site_url'] ,
    'sincerely'         => 'Bedankt!',
    'support'           => 'Support',
    'pm_block'          => 'Block other users from sending me PMs',
    'notify_header'     => 'Private Message Notification from ',
);

$LANG_PM_ERROR = array(
    'token_failure'     => 'Security Token Failure',
    'message_not_found' => 'Bericht niet gevonden',
    'no_to_address'     => 'No to address specified',
    'no_subject'        => 'Subject must not be blank and must be greater than 4 characters in length.',
    'no_message'        => 'Message body must not be blank and must be greater than 4 characters in length.',
    'unknown_user'      => 'Kan de volgende gebruiker niet vinden:',
    'too_many_recipients' => 'You have included too many recipients - Maximum %s allowed.',
);

// Localization of the Admin Configuration UI
$LANG_configsections['pm'] = array(
    'label'                 => 'Prive Berichten',
    'title'                 => 'Configuratie Prive Berichten',
);
$LANG_confignames['pm'] = array(
    'messages_per_page'     => 'Berichten per pagina',
    'post_speedlimit'       => 'Posting Speedlimit (seconds)',
    'max_recipients'        => 'Max aantal ontvangers per bericht',
);
$LANG_configsubgroups['pm'] = array(
    'sg_main'               => 'Hoofd Instellingen',
);

$LANG_fs['pm'] = array(
    'pm_general'            => 'PM Algemene Instellingen',
);

$PLG_pm_MESSAGE1 = 'Message(s) successfully sent.';
$PLG_pm_MESSAGE2 = 'Message(s) successfully deleted.';
$PLG_pm_MESSAGE3 = 'Message(s) successfully archived.';
$PLG_pm_MESSAGE4 = 'Private Message Speedlimit Hit - Please wait a minute before sending another message.';
$PLG_pm_MESSAGE5 = 'Friends List Updated.';
?>