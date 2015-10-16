<?php
// +--------------------------------------------------------------------------+
// | PM Plugin - glFusion CMS                                                 |
// +--------------------------------------------------------------------------+
// | english_utf-8.php                                                        |
// |                                                                          |
// | English language file                                                    |
// +--------------------------------------------------------------------------+
// | Copyright (C) 2009-2015 by the following authors:                        |
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
    'menulabel'         => 'Private Messages',
    'plugin'            => 'pm',
    'admin_menu'        => 'PM Admin',
    'pm_index'          => 'PM Index',
    'pm'                => 'PM',
    'user_menu'         => 'Message Center',
    'title'             => 'Private Message',
    'compose_msg'       => 'Compose Message',
    'compose'           => 'Compose',
    'reply_msg'         => 'Reply Message',
    'reply'             => 'Reply',
    'preview_msg'       => 'Preview Message',
    'quote_msg'         => 'Quote Message',
    'quote'             => 'Quote',
    'delete_msg'        => 'Delete Message',
    'delete'            => 'Delete',
    'inbox'             => 'Inbox',
    'sent'              => 'Sent',
    'date'              => 'Date',
    'archive'           => 'Archive',
    'outbox'            => 'Outbox',
    'all_messages'      => 'All Messages',
    'one_day'           => '1 Day',
    'seven_days'        => '7 Days',
    'two_weeks'         => '2 Weeks',
    'one_month'         => '1 Month',
    'three_months'      => '3 Months',
    'six_months'        => '6 Months',
    'one_year'          => '1 Year',
    'author'            => 'Author',
    'post_time'         => 'Post Time',
    'subject'           => 'Subject',
    'ascending'         => 'Ascending',
    'descending'        => 'Descending',
    'no_subject'        => 'No subject available...',
    'by'                => 'By',
    'to'                => 'To',
    'folder'            => 'Folder',
    'view_folder'       => 'View folder',
    'go'                => 'Go',
    'messages'          => 'MESSAGES',
    'select'            => 'SELECT',
    'display'           => 'Display',
    'sort_by'           => 'Sort By',
    'view_msg'          => 'View Message From',
    'return_to'         => 'Return To',
    'sent'              => 'Sent',
    'from'              => 'From',
    'registered'        => 'Joined',
    'location'          => 'Location',
    'homepage'          => 'Homepage',
    'move_to_folder'    => 'Move To folder',
    'mark_all'          => 'Mark All',
    'unmark_all'        => 'Unmark All',
    'delete_marked'     => 'Deleted Marked',
    'archive_marked'    => 'Save Marked',
    'message_history'   => 'Message History',
    'add_user'          => 'Add User',
    'add_friend'        => 'Add Friend',
    'send'              => 'Send',
    'preview'           => 'Preview',
    'cancel'            => 'Cancel',
    'submit'            => 'Submit',
    'batch_confirm'     => 'Are you sure you want to move or delete the selected messages?',
    'delete_confirm'    => 'Are you sure you want to delete this message?',
    'no_messages'       => 'There are no messages in this folder',
    'manage_friends'    => 'Manage Friends',
    'friend_help'       => 'Friends enable you quick access to members you communicate with frequently.',
    'your_friends'      => 'Your friends',
    'your_friends_help' => 'To remove usernames select them and click submit.',
    'add_new_friend'    => 'Add New Friends',
    'add_new_friend_help' => 'You may enter several usernames separated by a comma.',
    'in_friends_list'   => 'Is Your Friend',
    'send_pm'           => 'Send Private Message',
    'error'             => ' Error',
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
    'label'                 => 'Private Messages',
    'title'                 => 'Private Messages Configuration'
);
$LANG_confignames['pm'] = array(
    'messages_per_page'     => 'Messages per page',
    'post_speedlimit'       => 'Posting Speedlimit (seconds)',
    'max_recipients'        => 'Max Number of recipients per message',
    'displayblocks'         => 'Display glFusion Blocks',
);
$LANG_configsubgroups['pm'] = array(
    'sg_main'               => 'Main Settings',
);

$LANG_fs['pm'] = array(
    'pm_general'            => 'PM General Settings',
);

$LANG_configselects['pm'] = array(
    0 => array('True' => 1, 'False' => 0),
    1 => array('True' => TRUE, 'False' => FALSE),
    2 => array('Left Blocks' => 0, 'Right Blocks' => 1, 'Left & Right Blocks' => 2, 'None' => 3)
);

$PLG_pm_MESSAGE1 = 'Message(s) successfully sent.';
$PLG_pm_MESSAGE2 = 'Message(s) successfully deleted.';
$PLG_pm_MESSAGE3 = 'Message(s) successfully archived.';
$PLG_pm_MESSAGE4 = 'Private Message Speedlimit Hit - Please wait a minute before sending another message.';
$PLG_pm_MESSAGE5 = 'Friends List Updated.';
?>