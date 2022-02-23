<?php
// +--------------------------------------------------------------------------+
// | PM Plugin for glFusion CMS                                               |
// +--------------------------------------------------------------------------+
// | lib-pm.php                                                               |
// |                                                                          |
// | PM plugin support functions                                              |
// +--------------------------------------------------------------------------+
// | Copyright (C) 2009-2018 by the following authors:                        |
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
    die ('This file can not be used on its own!');
}


function PM_alertMessage( $msgText = '' )
{
    global $_CONF, $_PM_CONF, $LANG_PM00;

    $display = COM_siteHeader('menu',$LANG_PM00['title']);

    $T = new Template(pm_get_template_path());
    $T->set_file (array ('message'=>'pm_alertmsg.thtml'));
    $T->set_var(array(
        'message_title' =>  $LANG_PM00['title'] . $LANG_PM00['error'],
        'message_text'  =>  $msgText,
    ));
    $T->parse ('output', 'message');
    $display .= $T->finish ($T->get_var('output'));

    $display .= COM_siteFooter();
    echo $display;
    exit;
}


function PM_getFolder( $varname='folder' ) {

    if ( isset($_GET[$varname]) ) {
        $folder = strtolower( COM_applyFilter($_GET[$varname]) );
    } elseif (isset($_POST[$varname]) ) {
        $folder = strtolower( COM_applyFilter($_POST[$varname]) );
    } else {
        $folder = 'inbox';
    }

    if ( !in_array($folder,array('inbox','sent','archive','outbox') ) ) {
        $folder = 'inbox';
    }
    return $folder;
}


/*
 * Show the history for a message group
 */
function PM_showHistory( $msg_id = 0, $compose = 0 )
{
    global $_CONF, $_USER, $_TABLES, $LANG_PM00;

    $retval = '';

    $msg = array();

    if ( $msg_id == 0 ) {
        return;
    }

    $dt = new Date('now',$_USER['tzid']);

    $T = new Template(pm_get_template_path());
    $T->set_file (array ('message'=>'message_history.thtml'));
    $retval .= '<h1>'.$LANG_PM00['message_history'].'</h1>';

    $parent_id = DB_getItem($_TABLES['pm_msg'],'parent_id','msg_id='.intval($msg_id));

    $sql = 'SELECT t.*, p.*, u.*
        FROM ' . $_TABLES['pm_msg'] . ' p, ' . $_TABLES['pm_dist'] . ' t, ' . $_TABLES['users'] . ' u
        WHERE t.msg_id = p.msg_id
            AND p.author_uid = u.uid
            AND t.user_id = '.(int) $_USER['uid'];

    if ( $compose ) {
        $extraWhere = ' AND t.folder_name NOT IN ("outbox", "sent")';
    } else {
        $extraWhere = '';
    }
    $sql  = $sql . $extraWhere;

    if (!$parent_id) {
        $sql .= " AND (p.parent_id = $msg_id OR (p.parent_id = 0 AND p.msg_id = ".(int) $msg_id."))";
    } else {
        $sql .= " AND (p.parent_id = " . (int) $parent_id . ' OR p.msg_id = ' . (int) $parent_id . ')';
    }
    $sql .= ' ORDER BY p.message_time DESC ';

    $result = DB_query($sql);
    if ( DB_numRows($result) < 1 ) {
        return;
    }
    $parsers = array();
    $parsers[] = array(array('block','inline','link','listitem'), '_bbc_replacesmiley');

    $counter = 0;
    $prevmsgtime = 0;
    while ( $msg = DB_fetchArray($result) ) {
        if ($compose == 0 && $msg['msg_id'] == $msg_id) {
            continue;
        }
        if ( $prevmsgtime == $msg['message_time'] ) {
            continue;
        }
        $prevmsgtime = $msg['message_time'];
        $subject = htmlentities($msg['message_subject'], ENT_QUOTES, COM_getEncodingt());
        $dt->setTimestamp($msg['message_time']);

        $formatted_msg_text = BBC_formatTextBlock($msg['message_text'],'text', $parsers);
        $T->set_var(array(
            'from'        => $msg['author_uid'],
            'to'          => $msg['user_id'],
            'subject'     => $subject,
            'date'        => $dt->format($dt->getUserFormat(),true),
            'msg_text'    => $formatted_msg_text,
            'from_name'   => $msg['author_name'],
            'to_name'     => $msg['to_address'],
        ));

        $T->parse ('output', 'message',true);
        $counter++;
    }

    if ( $counter == 0 ) {
        return;
    }

    $retval .= $T->finish ($T->get_var('output'));
    return $retval;
}


function PM_deleteMessage( $msg_id=0, $folder='' )
{
    global $_CONF, $_PM_CONF, $_TABLES, $_USER;

    if ( $msg_id == 0 || $folder == '' ) {
        return;
    }

    $msg_id = intval($msg_id);

    if ( !in_array($folder,array('inbox','sent','archive','outbox') ) ) {
        return;
    }

    $sql = "SELECT * FROM {$_TABLES['pm_msg']} msg LEFT JOIN {$_TABLES['pm_dist']} dist ON msg.msg_id=dist.msg_id WHERE msg.msg_id=".(int) $msg_id." AND dist.user_id=".(int) $_USER['uid']." AND folder_name='".DB_escapeString($folder)."'";
    $result = DB_query($sql);
    if ( DB_numRows($result) < 1 ) {
        return;
    }

    if ( $folder == 'outbox' ) {
        // we know no one has read it yet or it wouldn't be in our out box
        DB_query("DELETE FROM {$_TABLES['pm_dist']} WHERE (msg_id = ".(int) $msg_id.")");
        DB_query("DELETE FROM {$_TABLES['pm_msg']} WHERE (msg_id = ".(int) $msg_id.")");
    } else {
        // must be in our in, sent, or archive folder
        DB_query("DELETE FROM {$_TABLES['pm_dist']} WHERE msg_id = ".(int) $msg_id." AND user_id=".(int) $_USER['uid']." AND folder_name='".DB_escapeString($folder)."'");
        $msgCount = DB_count($_TABLES['pm_dist'],'msg_id',(int) $msg_id);
        if ( $msgCount == 0 ) {
            DB_query("DELETE FROM {$_TABLES['pm_msg']} WHERE (msg_id = ".(int) $msg_id.")");
        }
    }
}

// bbcode extensions...

function PM_BBC_formatTextBlock($str,$postmode='text',$parsers=array())
{

    $codes = array();

//-sample code
//    $codes[] = array('youtube', 'usecontent?', '_bbc_youtube', array ('usecontent_param' => 'default'),
//                      'link', array ('listitem', 'block', 'inline'), array ('link'));

    return BBC_formatTextBlock($str,'text',$parsers,$codes);
}


function _bbc_replacesmiley($str) {
    global $_CONF,$_TABLES;

    if (function_exists('msg_showsmilies') ) {
        $str = msg_replaceEmoticons($str);
    }

    return $str;
}

//-sample code
/*---
function _bbc_youtube ($action, $attributes, $content, $params, $node_object) {

    if ($action == 'validate') {
        return true;
    }
    $retval = '<div><div><object width="425" height="344"><param name="movie" value="http://www.youtube.com/v/'.$attributes['default'].'&hl=en_US&fs=1&"></param><param name="allowFullScreen" value="true"></param><param name="allowscriptaccess" value="always"></param><embed src="http://www.youtube.com/v/'.$attributes['default'].'&hl=en_US&fs=1&" type="application/x-shockwave-flash" allowscriptaccess="always" allowfullscreen="true" width="425" height="344"></embed></object></div>'.$content.'</div>';
    if ( $content == '' ) {
        $content = 'Check out this video';
    }
    return $retval;
    return '<a href="http://www.youtube.com/watch?v='.$attributes['default'].'">'.$content.'</a>';
}
*/

function PM_siteHeader($title='', $meta='')
{
    global $_PM_CONF;

    $retval = '';
    $retval .= COM_siteHeader('menu',$title,$meta);
    return $retval;
}

function PM_siteFooter() {
    global $_CONF, $_PM_CONF;

    $retval = '';
    $retval .= COM_siteFooter();
    return $retval;
}

function PM_FormatForEmail( $str, $postmode='html' )
{
    global $_CONF;

    USES_lib_bbcode();

    $parsers = array();
    $parsers[] = array(array('block','inline','link','listitem'));
    $str = PM_BBC_formatTextBlock($str,'text',$parsers);

    // we don't have a stylesheet for email, so replace our div with the style...
    $str = str_replace('<div class="quotemain">','<div style="border: 1px dotted #000;border-left: 4px solid #8394B2;color:#465584;  padding: 4px;  margin: 5px auto 8px auto;">',$str);

    return $str;
}


function PM_notify($to_user, $to_uid, $from_user, $pm_subject, $pm_message, $force=false)
{
    global $_CONF, $_USER, $_TABLES, $LANG_PM_NOTIFY;

    $result = DB_query("SELECT * FROM {$_TABLES['pm_userprefs']} WHERE uid=".(int) $to_uid);
    if (DB_numRows($result) == 0) {
        $sendEmail = 1;
    } else {
        $row = DB_fetchArray($result);
        $sendEmail = $force || $row['notify'];
    }
    if (!$sendEmail) {
        return;
    }

    $to_email = DB_getItem($_TABLES['users'],'email','username="'.DB_escapeString($to_user).'"');
    //$privateMessage = PM_FormatForEmail( $pm_message,'text');

   // build the template...
    $T = new Template(pm_get_template_path());
    $T->set_file ('email', 'pm_notify.thtml');

    $T->set_var(array(
        'to_username'       => $to_user,
        'from_username'     => $from_user,
        'msg_subject'       => $pm_subject,
        'site_name'         => $_CONF['site_name'] . ' - ' . $_CONF['site_slogan'],
        'site_url'          => $_CONF['site_url'],
        'pm_text'           => $pm_message,
    ));
    $T->parse('output','email');
    $message = $T->finish($T->get_var('output'));

    $html2txt = new Html2Text\Html2Text($pm_message,false);
    $messageText = $html2txt->get_text();

    $to = array($to_email,$to_user);
    $from = array($_CONF['noreply_mail'],$_CONF['site_name']);
    $subject =  $_CONF['site_name'] .' - '. $LANG_PM_NOTIFY['new_pm_notification'];

    COM_mail( $to, $subject, $message, $from, true,0,'',$messageText);

    return true;
}


/**
 * Send a private message, either by a system event or from a user.
 *
 * Parameter array consists of:
 *   - to : (int)Single or array of user IDs
 *   - bcc : (int)Single or array of user IDs
 *   - subject : (string)
 *   - message : (string)
 *   - author_id : (int) Sending user ID, zero for a system message
 * @param   array   $A  Array of message properties
 */
function PM_sendmessage(array $A)
{
    global $_CONF, $_PM_CONF, $_TABLES, $_USER, $LANG_PM_ERROR;

    $errArray = array();
    $toList = array();
    $to_str = '';
    $to_users = array();
    if (isset($A['to'])) {
        $toList = $A['to'];
        if (!is_array($toList)) {
            $toList = array($toList);
        }
        if (!empty($toList)) {
            $to_str = DB_escapeString(implode(',', $toList));
            $sql = "SELECT u.uid, u.username, up.block
                FROM {$_TABLES['users']} AS u
                LEFT JOIN {$_TABLES['pm_userprefs']} AS up
                ON u.uid=up.uid
                WHERE u.uid IN ($to_str)";
            $result = DB_query($sql);
            if (DB_numRows($result) > 0) {
                $to_users = DB_fetchAll($result, false);
            }
        }
    }

    $bccList = array();
    $bcc_str = '';
    $bcc_users = array();
    if (isset($A['bcc'])) {
        $bccList = $A['bcc'];
        if (!is_array($bccList)) {
            $bccList = array($bccList);
        }
        if (!empty($bccList)) {
            $bcc_str = DB_escapeString(implode(',', $bccList));
            $sql = "SELECT u.uid, u.username, up.block
                FROM {$_TABLES['users']} AS u
                LEFT JOIN {$_TABLES['pm_userprefs']} AS up
                ON u.uid=up.uid
                WHERE u.uid IN ($bcc_str)";
            $result = DB_query($sql);
            if (DB_numRows($result) > 0) {
                $bcc_users = DB_fetchAll($result, false);
            }
        }
    }

    if (!empty($to_users) || !empty($bcc_users)) {
        $to_address = array();
        foreach ($to_users as $user) {
            $to_address[] = $user['username'];
        }
        $to_address = implode(',', $to_address);

        $bcc_address = array();
        foreach ($bcc_users as $user) {
            $bcc_address[] = $user['username'];
        }
        $bcc_address = implode(',', $bcc_address);

        $subject = $A['subject'];
        if ( strlen($subject) < 4 ) {
            $errArray[] = $LANG_PM_ERROR['no_subject'];
        }
        $message = $A['message'];
        if ( strlen($message) < 4 ) {
            $errArray[] = $LANG_PM_ERROR['no_message'];
        }
        if ( count($errArray) > 0 ) {
            return array(false,$errArray);
        }

        if (isset($A['author_id'])) {
            $author_id = (int)$A['author_id'];
            $author_name = DB_getItem($_TABLES['users'], 'username', "uid=$author_id");
            $remote_addr = $_SERVER['REMOTE_ADDR'];
        } else {
            $author_id = 0;
            $author_name = 'System Message';
            $remote_addr = '0.0.0.0';
        }
        $remote_addr = DB_escapeString($remote_addr);

        // do a little cleaning...
        $subject = strip_tags($subject);
        $parent_id = 0;
        $reply_msgid = 0;
        $pm_replied = 0;

        // Add the message record
        $sql  = "INSERT INTO {$_TABLES['pm_msg']} SET
            parent_id = $parent_id,
            author_uid = $author_id,
            author_name = '" . DB_escapeString($author_name) . "',
            author_ip = '$remote_addr',
            message_time = UNIX_TIMESTAMP(),
            message_subject = '" . DB_escapeString($subject) . "',
            message_text = '" . DB_escapeString($message) . "',
            to_address = '" . DB_escapeString($to_address) . "',
            bcc_address = '" . DB_escapeString($bcc_address) . "'";
        DB_query($sql);
        $lastmsg_id = DB_insertID();

        // Format the message once for email
        $message = PM_FormatForEmail($message, 'text');

        // Add a mailbox record for each recipient and notify
        foreach ($to_users as $user) {
            $sql = "INSERT INTO {$_TABLES['pm_dist']} SET
                msg_id = $lastmsg_id,
                user_id = {$user['uid']},
                username = '" . DB_escapeString($user['username']) . "',
                author_uid = $author_id,
                pm_bcc = 0";
            DB_query($sql);
            PM_notify($user['username'], $user['uid'], $author_name, $subject, $message, $author_id == 0);
        }
        foreach ($bcc_users as $user) {
            $sql = "INSERT INTO {$_TABLES['pm_dist']} SET
                msg_id = $lastmsg_id,
                user_id = {$user['uid']},
                username = '" . DB_escapeString($user['username']) . "',
                author_uid = $author_id,
                pm_bcc = 1";
            DB_query($sql);
            PM_notify($user['username'], $user['uid'], $author_name, $subject, $message, $author_id == 0);
        }
    }

    // Add an outbox message for the author, if not a system-generated message.
    if ($author_id > 0) {
        $sql  = "INSERT INTO {$_TABLES['pm_dist']} SET
            msg_id = $lastmsg_id,
            user_id = $author_id,
            username = '" . DB_escapeString($author_name) . "',
            author_uid = $author_id,
            folder_name = 'outbox',
            pm_unread = 0,
            pm_replied = 0";
        DB_query($sql);

        // update original record to show it has been replied...
        DB_query(
            "UPDATE {$_TABLES['pm_dist']} SET pm_replied=1
            WHERE msg_id =" . (int)$reply_msgid .
            " AND user_id= $author_id AND folder_name NOT IN ('outbox','sent')"
        );
        COM_updateSpeedlimit ('pm');
    }
    CACHE_remove_instance('stmenu');
    return array(true,'');
}

