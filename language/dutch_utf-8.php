<?php
// +--------------------------------------------------------------------------+
// | PM Plugin - glFusion CMS                                                 |
// +--------------------------------------------------------------------------+
// | dutch_utf-8.php                                                          |
// |                                                                          |
// | Dutch language file                                                      |
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
    die ('This file cannot be used on its own.');
}

###############################################################################

$LANG_PM00 = array(
    'menulabel' => 'Prive Berichten',
    'plugin' => 'pm',
    'admin_menu' => 'PM Configuratie',
    'pm_index' => 'PM Index',
    'pm' => 'PM',
    'user_menu' => 'Berichten Center',
    'title' => 'Prive Bericht',
    'compose_msg' => 'Schrijf een Bericht',
    'compose' => 'Compose',
    'reply_msg' => 'Beantwoord Bericht',
    'reply' => 'Reply',
    'preview_msg' => 'Bekijk Bericht',
    'quote_msg' => 'Quote Bericht',
    'quote' => 'Quote',
    'delete_msg' => 'Verwijder Bericht',
    'delete' => 'Delete',
    'inbox' => 'Inbox',
    'sent' => 'Verzonden',
    'date' => 'Date',
    'archive' => 'Archief',
    'outbox' => 'Outbox',
    'all_messages' => 'Alle Berichten',
    'one_day' => '1 Dag',
    'seven_days' => '7 Dagen',
    'two_weeks' => '2 Weken',
    'one_month' => '1 Maand',
    'three_months' => '3 Maanden',
    'six_months' => '6 Maanden',
    'one_year' => '1 Jaar',
    'author' => 'Auteur',
    'post_time' => 'Post Time',
    'subject' => 'Onderwerp',
    'ascending' => 'Oplopend',
    'descending' => 'Aflopend',
    'no_subject' => 'Geen onderwerp beschikbaar...',
    'by' => 'Door',
    'to' => 'Aan',
    'folder' => 'Folder',
    'view_folder' => 'Bekijk folder',
    'go' => 'Ga',
    'messages' => 'BERICHTEN',
    'select' => 'SELECTEER',
    'display' => 'Toon',
    'sort_by' => 'Sorteer Op',
    'view_msg' => 'Bekijk Bericht Van',
    'return_to' => 'Return To',
    'from' => 'Van',
    'registered' => 'Geregistreerd op',
    'location' => 'Location',
    'homepage' => 'Homepage',
    'move_to_folder' => 'Verplaats naar Folder',
    'mark_all' => 'Mark All',
    'unmark_all' => 'Unmark All',
    'delete_marked' => 'Deleted Marked',
    'archive_marked' => 'Move marked to Saved messages',
    'message_history' => 'Berichten Historie',
    'add_user' => 'Voeg een Gebruiker toe',
    'add_friend' => 'Voeg een Vriend(in) toe',
    'send' => 'Verstuur',
    'preview' => 'Bekijk',
    'cancel' => 'Annuleer',
    'submit' => 'Uitvoeren',
    'archive_confirm' => 'Are you sure you want to archive the selected messages?',
    'batch_confirm' => 'Are you sure you want to move or delete the selected messages?',
    'delete_confirm' => 'Are you sure you want to delete this message?',
    'no_messages' => 'Er zijn geen berichten in deze folder',
    'manage_friends' => 'Beheer uw Vrienden',
    'friend_help' => 'Friends enable you quick access to members you communicate with frequently.',
    'your_friends' => 'Uw vrienden',
    'your_friends_help' => 'To remove usernames select them and click submit.',
    'add_new_friend' => 'Voeg Nieuwe Vrienden toe',
    'add_new_friend_help' => 'You may enter several usernames separated by a comma.',
    'in_friends_list' => 'Is Uw Vriend(in)',
    'send_pm' => 'Verstuur een Prive Bericht',
    'error' => ' Error',
    'newpm' => "<b>You have %d new </b><a href=\"{$_CONF['site_url']}/pm/index.php\"><b>private message(s)</b>",
    'address_book' => 'Address Book',
    'site_users' => 'Site Users',
    'options' => 'Options',
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
    'ID' => 'ID',
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
    'smiley' => 'Smileys'
    'system_message' => 'System Message',
);

// Localization of the Admin Configuration UI
$LANG_configsections['pm'] = array(
    'label' => 'Prive Berichten',
    'title' => 'Configuratie Prive Berichten'
);

$LANG_confignames['pm'] = array(
    'messages_per_page' => 'Berichten per pagina',
    'post_speedlimit' => 'Posting Speedlimit (seconds)',
    'max_recipients' => 'Max aantal ontvangers per bericht',
    'displayblocks' => 'Display glFusion Blocks'
);

$LANG_configsubgroups['pm'] = array(
    'sg_main' => 'Hoofd Instellingen'
);

$LANG_fs['pm'] = array(
    'pm_general' => 'PM Algemene Instellingen'
);

// Note: entries 0, 1, and 12 are the same as in $LANG_configselects['Core']
$LANG_configselects['pm'] = array(
    0 => array('True' => 1, 'False' => 0),
    1 => array('True' => true, 'False' => false),
    2 => array('Left Blocks' => 0, 'Right Blocks' => 1, 'Left & Right Blocks' => 2, 'None' => 3)
);
$PLG_pm_MESSAGE1 = 'Message(s) successfully sent.';
$PLG_pm_MESSAGE2 = 'Message(s) successfully deleted.';
$PLG_pm_MESSAGE3 = 'Message(s) successfully archived.';
$PLG_pm_MESSAGE4 = 'Private Message Speedlimit Hit - Please wait a minute before sending another message.';
$PLG_pm_MESSAGE5 = 'Friends List Updated.';

?>
