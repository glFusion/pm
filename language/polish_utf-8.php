<?php
// +--------------------------------------------------------------------------+
// | PM Plugin - glFusion CMS                                                 |
// +--------------------------------------------------------------------------+
// | polish.php                                                               |
// |                                                                          |
// | Polish language file                                                     |
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
    'menulabel' => 'Prywatne Wiadomosci',
    'plugin' => 'pm',
    'admin_menu' => 'PM Admin',
    'pm_index' => 'PM Index',
    'pm' => 'PM',
    'user_menu' => 'Skrzynka Odbiorcza',
    'title' => 'Wiadomosc Prywatna',
    'compose_msg' => 'Nowa wiadomosc',
    'compose' => 'Compose',
    'reply_msg' => 'Odpowiedz',
    'reply' => 'Reply',
    'preview_msg' => 'Podglad Wiadomosci',
    'quote_msg' => 'Cytuj Wiadomosc',
    'quote' => 'Quote',
    'delete_msg' => 'Usun Wiadomosc',
    'delete' => 'Delete',
    'inbox' => 'Skrzynka Odbiorcza',
    'sent' => 'Wyslany',
    'date' => 'Date',
    'archive' => 'Archiwum',
    'outbox' => 'Skrzynka Nadawcza',
    'all_messages' => 'Wszystkie Wiadomosci',
    'one_day' => '1 Dzien',
    'seven_days' => '7 Dni',
    'two_weeks' => '2 Tygodnie',
    'one_month' => '1 Miesiac',
    'three_months' => '3 Miesiace',
    'six_months' => '6 Miesiecy',
    'one_year' => '1 Rok',
    'author' => 'Autor',
    'post_time' => 'Czas Posta',
    'subject' => 'Temat',
    'ascending' => 'Rosnaco',
    'descending' => 'Malejaco',
    'no_subject' => 'Brak tematów...',
    'by' => 'Przez',
    'to' => 'Do',
    'folder' => 'Folder',
    'view_folder' => 'Zobacz folder',
    'go' => 'Go',
    'messages' => 'WIADOMOSC',
    'select' => 'WYBIERZ',
    'display' => 'Wyswietl',
    'sort_by' => 'Sortuj wg',
    'view_msg' => 'Wyswietl Wiadomosci',
    'return_to' => 'Wróc do',
    'from' => 'Do',
    'registered' => 'Dolaczyl',
    'location' => 'Localizacja',
    'homepage' => 'Strona Domowa',
    'move_to_folder' => 'Przenies do folderu',
    'mark_all' => 'Zaznacz wszystkie',
    'unmark_all' => 'Odznacz wszystkie',
    'delete_marked' => 'Usunieto Oznaczono',
    'archive_marked' => 'Przenies zaznaczone do zapisanych wiadomosci',
    'message_history' => 'Historia wiadomosci',
    'add_user' => 'Dodaj uzytkownika',
    'add_friend' => 'Dodaj przyjaciela',
    'send' => 'Wyslac',
    'preview' => 'Podglad',
    'cancel' => 'Zrezygnuj',
    'submit' => 'Wyslij',
    'archive_confirm' => 'Are you sure you want to archive the selected messages?',
    'batch_confirm' => 'Czy na pewno chcesz przeniesc lub usunac wybrane wiadomosci?',
    'delete_confirm' => 'Czy na pewno chcesz usunac ta wiadomosc?',
    'no_messages' => 'Brak wiadomosci w tym folderze',
    'manage_friends' => 'Zarzadzanie Znajomi',
    'friend_help' => 'Znajomi umozliwiaja szybki kontakt.',
    'your_friends' => 'Twoi znajomi',
    'your_friends_help' => 'Aby usunac uzytkownika zaznacz i kliknij.',
    'add_new_friend' => 'Dodawanie nowych przyjaciól',
    'add_new_friend_help' => 'Mozna podac kilka nazw uzytkowników oddzielone przecinkiem.',
    'in_friends_list' => 'Jest twoim przyjacielem',
    'send_pm' => 'Send Private Message',
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
    'block_user' => 'Block User',
    'block_confirm' => 'Are you sure you want to block these users?',
    'block' => 'Block',
    'blocked' => 'Blocked',
    'friend' => 'Friend',
    'friends' => 'Friends',
    'friend_added' => 'Added a friend.',
    'friend_removed' => 'Removed from the address book.',
    'user_blocked' => 'Blocked the sender',
    'user_unblocked' => 'The sender has been un-blocked.',
);

// Localization of the Admin Configuration UI
$LANG_configsections['pm'] = array(
    'label' => 'Prywatne wiadomosci',
    'title' => '	Prywatne wiadomosci Konfiguracja'
);

$LANG_confignames['pm'] = array(
    'messages_per_page' => 'Wiadomosci na stronie',
    'post_speedlimit' => 'Posting Speedlimit (seconds)',
    'max_recipients' => 'Maksymalna liczba adresatów na wiadomosc',
    'displayblocks' => 'Display glFusion Blocks'
);

$LANG_configsubgroups['pm'] = array(
    'sg_main' => 'Glówne Ustawienia'
);

$LANG_fs['pm'] = array(
    'pm_general' => 'PM Ustawienia ogólne'
);

// Note: entries 0, 1, and 12 are the same as in $LANG_configselects['Core']
$LANG_configselects['pm'] = array(
    0 => array('True' => 1, 'False' => 0),
    1 => array('True' => true, 'False' => false),
    2 => array('Left Blocks' => 0, 'Right Blocks' => 1, 'Left & Right Blocks' => 2, 'None' => 3)
);
$PLG_pm_MESSAGE1 = 'Wiadomosci zostaly wyslane.';
$PLG_pm_MESSAGE2 = 'Wiadomosci zostaly usuniete.';
$PLG_pm_MESSAGE3 = 'Message(s) successfully archived.';
$PLG_pm_MESSAGE4 = 'Private Message Speedlimit Hit - Please wait a minute before sending another message.';
$PLG_pm_MESSAGE5 = 'Aktualizacja listy przyjaciól.';

?>
