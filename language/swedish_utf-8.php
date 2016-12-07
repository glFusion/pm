<?php
// +--------------------------------------------------------------------------+
// | PM Plugin - glFusion CMS                                                 |
// +--------------------------------------------------------------------------+
// | swedish_utf-8.php                                                        |
// |                                                                          |
// | Swedish language file                                                    |
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
    'menulabel'         => 'Privata Meddelanden',
    'plugin'            => 'pm',
    'admin_menu'        => 'PM Admin',
    'pm_index'          => 'PM Index',
    'pm'                => 'PM',
    'user_menu'         => 'Meddelande Center',
    'title'             => 'Privata Meddelanden',
    'compose_msg'       => 'Skriv meddelande',
    'compose'           => 'Compose',
    'reply_msg'         => 'Svara meddelande',
    'reply'             => 'Reply',
    'preview_msg'       => 'Förhandsvisa meddelande',
    'quote_msg'         => 'Citera  meddelande',
    'quote'             => 'Quote',
    'delete_msg'        => 'Radera meddelande',
    'delete'            => 'Delete',
    'inbox'             => 'Inkorg',
    'sent'              => 'Skickade',
    'date'              => 'Date',
    'archive'           => 'Arkiv',
    'outbox'            => 'Utbox',
    'all_messages'      => 'Alla meddelanden',
    'one_day'           => '1 dag',
    'seven_days'        => '7 dagar',
    'two_weeks'         => '2 veckor',
    'one_month'         => '1 månad',
    'three_months'      => '3 månader',
    'six_months'        => '6 månader',
    'one_year'          => '1 år',
    'author'            => 'skribent',
    'post_time'         => 'Post-tid',
    'subject'           => 'Ämne',
    'ascending'         => 'Stigandes',
    'descending'        => 'Fallande',
    'no_subject'        => 'Inget ämne tillgängligt...',
    'by'                => 'Av',
    'to'                => 'Till',
    'folder'            => 'Katalog',
    'view_folder'       => 'Visa katalog',
    'go'                => 'Gå',
    'messages'          => 'MEDDELANDEN',
    'select'            => 'VÄLJ',
    'display'           => 'Visa',
    'sort_by'           => 'Sortera efter',
    'view_msg'          => 'Vissa meddelande från',
    'return_to'         => 'Svara till',
    'sent'              => 'Skickat',
    'from'              => 'Från',
    'registered'        => 'Blev medlem',
    'location'          => 'Plats',
    'homepage'          => 'Hemsida',
    'move_to_folder'    => 'Flytta till katalog',
    'mark_all'          => 'Markera alla',
    'unmark_all'        => 'Avmarkera alla',
    'delete_marked'     => 'Radera markerade',
    'archive_marked'    => 'Flytta markerade till Sparade meddelanden',
    'message_history'   => 'Meddelandehistorik',
    'add_user'          => 'Lägg till användare',
    'add_friend'        => 'Lägg till vän',
    'send'              => 'Skicka',
    'preview'           => 'Förhandsvisa',
    'cancel'            => 'Avbryt',
    'submit'            => 'Skicka',
    'batch_confirm'     => 'Är du säker på att du vill flytta eller radera de valda meddelandena',
    'archive_confirm'   => 'Are you sure you want to archive the selected messages?',
    'delete_confirm'    => 'Är du säker på att du vill radera detta meddelande?',
    'no_messages'       => 'Det finns inga meddelande i denna katalog',
    'manage_friends'    => 'Hantera vänner',
    'friend_help'       => 'Vänner gör så att du snabbt får tillgång till medlemmar som du skriver till ofta.',
    'your_friends'      => 'Din vännerlista',
    'your_friends_help' => 'För att avlägsna användarnamn välj dem och klicka på skicka.',
    'add_new_friend'    => 'Lägg till ny vän',
    'add_new_friend_help' => 'Du kan lägga till flera användare genom att separera dessa med ett kommatecken.',
    'in_friends_list'   => 'Är din vän',
    'send_pm'           => 'Skicka privat meddelande',
    'error'             => ' Error',
    'newpm'             => '<b>Du har %d nytt/nya </b><a href="'.$_CONF['site_url'].'/pm/index.php"><b>privata meddelande(n)</b>',
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
    'pm_notify'         => 'PM besked',
    'new_pm_notification' => 'Nytt privat meddelande-besked',
    'hello'             => 'Tjenare',
    'subject'           => 'Ämne',
    'new_pm_text'       => 'Du har ett nytt privat meddelande från',
    'disclaimer'        => 'Du har mottagit detta för du tillåter "privat meddelande-besked". För att ändra dina sekretessinställningar, var god använd Mitt konto-alternativ på ' . $_CONF['site_url'] ,
    'sincerely'         => 'Tackar!',
    'support'           => 'Support',
    'pm_block'          => 'Block other users from sending me PMs',
    'notify_header'     => 'Private Message Notification from ',
);

$LANG_PM_ERROR = array(
    'token_failure'     => 'Felande säkerhetsnyckel',
    'message_not_found' => 'Meddelande ej funnet',
    'no_to_address'     => 'Ingen till adress specifierad',
    'no_subject'        => 'Ämnet får ej vara blankt och måste vara längre än 4 karaktärer.',
    'no_message'        => 'Meddelandet får inte vara blankt och måste minst vara 4 karaktärer långt.',
    'unknown_user'      => 'Kunde inte hitta användare:',
    'too_many_recipients' => 'Du har lagt till för många mottagare - Max %s tillåtna.',
    'invalid_msg_id'    => 'Invalid meddelande-ID',
    'invalid_reply_id'  => 'Invalid svars-ID',
    'private_user'      => 'User does not allow PM messages',
);

// Localization of the Admin Configuration UI
$LANG_configsections['pm'] = array(
    'label'                 => 'Privata Meddelanden',
    'title'                 => 'Privata Meddelanden konfiguration'
);
$LANG_confignames['pm'] = array(
    'messages_per_page'     => 'Meddelanden per sida',
    'post_speedlimit'       => 'Postnings hastighetsgräns (sekunder)',
    'max_recipients'        => 'Max antal mottagare per meddelande',
    'displayblocks'         => 'Display glFusion Blocks',
);
$LANG_configsubgroups['pm'] = array(
    'sg_main'               => 'Huvudinställningar',
);

$LANG_fs['pm'] = array(
    'pm_general'            => 'PM Allmänna inställningar',
);

$LANG_configselects['pm'] = array(
    0 => array('True' => 1, 'False' => 0),
    1 => array('True' => TRUE, 'False' => FALSE),
    2 => array('Left Blocks' => 0, 'Right Blocks' => 1, 'Left & Right Blocks' => 2, 'None' => 3)
);

$PLG_pm_MESSAGE1 = 'Meddelande(n) skickat.';
$PLG_pm_MESSAGE2 = 'Meddelande(n) raderad.';
$PLG_pm_MESSAGE3 = 'Meddelande(n) sparades.';
$PLG_pm_MESSAGE4 = 'Privata Meddelandes fartbegränsning nådd - Var god vänta några menuter innan du skickar ännu ett meddelande.';
$PLG_pm_MESSAGE5 = 'Vännerlistan uppdaterad.';
?>