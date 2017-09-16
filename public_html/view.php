<?php
// +--------------------------------------------------------------------------+
// | PM Plugin for glFusion CMS                                               |
// +--------------------------------------------------------------------------+
// | view.php                                                                 |
// |                                                                          |
// | PM plugin view message                                                   |
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

require_once '../lib-common.php';

if (!in_array('pm', $_PLUGINS)) {
    COM_404();
    exit;
}

USES_lib_user();
USES_lib_bbcode();

require_once $_CONF['path'].'plugins/pm/include/lib-pm.php';


if ( isset($_GET['msgid']) ) {
    $msg_id = COM_applyFilter($_GET['msgid'],true);
} elseif (isset($_POST['msgid']) ) {
    $msg_id = COM_applyFIlter($_POST['msgid'],true);
} else {
    $msg_id = 0;
}

$folder = PM_getFolder( 'folder' );

if ( isset($_GET['p']) ) {
    $page = COM_applyFilter($_GET['p'],true);
} elseif (isset($_POST['page']) ) {
    $page = COM_applyFilter($_POST['p'],true);
} else {
    $page = 1;
}

if ( $msg_id == 0 ) {
    PM_alertMessage( $LANG_PM_ERROR['invalid_msg_id'] );
}

$retval = '';
$toList = '';
$toUs   = 0;

$sql  = "SELECT * ";
$sql .= "FROM {$_TABLES['pm_msg']} msg ";
$sql .= "LEFT JOIN {$_TABLES['pm_dist']} dist ON msg.msg_id=dist.msg_id ";
$sql .= "WHERE msg.msg_id=".(int) $msg_id." AND user_id=".(int) $_USER['uid']." AND dist.folder_name='".DB_escapeString($folder)."'";

$result = DB_query($sql);
if ( DB_numRows($result) > 0 ) {
    $msg = DB_fetchArray($result);

    if ( ($folder == 'inbox' || $folder == 'archive') && $msg['pm_unread'] == 1 ) {
        DB_query("UPDATE {$_TABLES['pm_dist']} SET pm_unread=0 WHERE msg_id=".(int) $msg_id." AND user_id=".(int) $_USER['uid']." AND folder_name='".DB_escapeString($folder)."'");
        DB_query("UPDATE {$_TABLES['pm_dist']} SET folder_name='sent' WHERE msg_id=".(int) $msg_id." AND user_id=".(int) $msg['author_uid']." AND folder_name='outbox'");
        CACHE_remove_instance('menu');
    }

    // get sender information
    $username   = '';
    $fullname   = '';
    $email      = '';
    $homepage   = '';
    $sig        = '';
    $regdate    = '';
    $photo      = '';
    $about      = '';
    $location   = '';

    $sql = "SELECT username,fullname,email,homepage,sig,regdate,photo,about,location,emailfromuser FROM {$_TABLES['users']} AS user LEFT JOIN {$_TABLES['userinfo']} AS info ON user.uid=info.uid LEFT JOIN {$_TABLES['userprefs']} AS prefs ON info.uid=prefs.uid WHERE user.uid=".(int) $msg['author_uid'];
    $result = DB_query($sql);
    if ( DB_numRows($result) > 0 ) {
        list($username,$fullname,$email,$homepage,$sig,$regdate,$photo,$about,$location,$emailfromuser) = DB_fetchArray($result);
    }

    $T = new Template(pm_get_template_path());
    $T->set_file (array ('message'=>'message.thtml'));

    // are they a friend?

    if ( $msg['author_uid'] != $_USER['uid'] ) {
        $friend = DB_count($_TABLES['pm_friends'],array('uid','friend_id'),array((int) $_USER['uid'],(int) $msg['author_uid']));

        if ( $friend ) {
            $T->set_var('add_friend','<br /><strong>'.$LANG_PM00['in_friends_list'].'</strong><br />');
        } else {
            $addFriend = '<a href="#" onclick="ajax_addfriend('.$_USER['uid'].','.$msg['author_uid'].',-1,1);return false;">
              <img src="'.$_CONF['site_url'].'/pm/images/addfriend.gif" alt="Add Friend" />
            </a>';

            $friendHTML = '<div id="u'.$msg['author_uid'].'"><span id="friend'.$msg['author_uid'].'">'
                          .'<br />'.$addFriend.'<br />'
                          .'</span></div>';

            $T->set_var('add_friend',$friendHTML);
        }
    } else {
        $T->set_var('add_friend','');
    }

    $parsers = array();
    $parsers[] = array(array ('block', 'inline', 'listitem'), '_bbc_replacesmiley');

    $message_history = PM_showHistory( $msg['msg_id'], 0 );

    $formatted_msg_text = BBC_formatTextBlock($msg['message_text'],'text', $parsers);
    $dt = new Date($msg['message_time'],$_USER['tzid']);
    $T->set_var(array(
        'from'          => $msg['author_uid'],
        'to'            => $msg['user_id'],
        'subject'       => $msg['message_subject'],
        'date'          => $dt->format($dt->getUserFormat(),true),
        'msg_text'      => $formatted_msg_text,
        'return_link'   => $_CONF['site_url'].'/pm/index.php?folder='.$folder.'&amp;page='.$page.'#msg'.$msg['msg_id'],
        'folder'        => $LANG_PM00[$folder],
        'folder_id'     => $folder,
        'avatar'        => USER_getPhoto($msg['author_uid'],$photo,'',128),
        'from_name'     => $username,
        'from_uid'      => $msg['author_uid'],
        'to_name'       => $msg['to_address'],
        'msg_id'        => $msg['msg_id'],
        'rank'          => SEC_inGroup('Root',$msg['author_uid']) ? 'Site Admin' : 'User',
        'registered'    => $regdate,
        'signature'     => nl2br($sig),
        'homepage'      => $homepage,
        'location'      => $location,
        'email'         => $emailfromuser ? $_CONF['site_url'].'/profiles.php?uid='.$msg['author_uid'] : '',
        'message_history'   => $message_history,
    ));

    $T->parse ('output', 'message');
    $retval .= $T->finish ($T->get_var('output'));
} else {
    $retval = $LANG_PM_ERROR['message_not_found'];
}

$styleLink = '<link rel="stylesheet" type="text/css" href="'.$_CONF['site_url'].'/pm/style.css" />'.LB;

$display = '';
$display .= PM_siteHeader($LANG_PM00['title'],$styleLink);
if ( isset($_GET['msg']) ) {
    $msg_header = COM_applyFilter ($_GET['msg'], true);
} else {
    $msg_header = 0;
}
if ( $msg_header > 0 ) {
    $display .= COM_showMessage ($msg_header, 'pm');
}
$display .= $retval;
$display .= PM_siteFooter();
echo $display;
?>