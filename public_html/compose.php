<?php
// +--------------------------------------------------------------------------+
// | PM Plugin for glFusion CMS                                               |
// +--------------------------------------------------------------------------+
// | compose.php                                                              |
// |                                                                          |
// | PM plugin message editor                                                 |
// +--------------------------------------------------------------------------+
// | Copyright (C) 2009-2014 by the following authors:                        |
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

/*
 * Start of main code
 */

$display = '';

function PM_notify($to_user,$to_uid,$from_user,$pm_subject, $pm_message ) {
    global $_CONF, $_USER, $_TABLES, $LANG_PM_NOTIFY;

    $result = DB_query("SELECT * FROM {$_TABLES['pm_userprefs']} WHERE uid=".(int) $to_uid);
    if ( DB_numRows($result) == 0 ) {
        $sendEmail = 1;
    } else {
        $row = DB_fetchArray($result);
        $sendEmail = $row['notify'];
    }
    if ( $sendEmail != 1 ) {
        return;
    }

    $to_email = DB_getItem($_TABLES['users'],'email','username="'.DB_escapeString($to_user).'"');

    $privateMessage = PM_FormatForEmail( $pm_message,'text');

   // build the template...
    $T = new Template($_CONF['path'] . 'plugins/pm/templates');
    $T->set_file ('email', 'pm_notify.thtml');

    $T->set_var(array(
        'to_username'       => $to_user,
        'from_username'     => $from_user,
        'msg_subject'       => $pm_subject,
        'site_name'         => $_CONF['site_name'] . ' - ' . $_CONF['site_slogan'],
        'site_url'          => $_CONF['site_url'],
        'pm_text'           => $privateMessage,
    ));
    $T->parse('output','email');
    $message = $T->finish($T->get_var('output'));

    $html2txt = new html2text($privateMessage,false);
    $messageText = $html2txt->get_text();

    $to = array($to_email,$to_user);
    $from = array($_CONF['noreply_mail'],$_CONF['site_name']);
    $subject =  $_CONF['site_name'] .' - '. $LANG_PM_NOTIFY['new_pm_notification'];

    COM_mail( $to, $subject, $message, $from, true,0,'',$messageText);

    return true;
}

function PM_FormatForEmail( $str, $postmode='html' ) {
    global $_CONF;

    USES_lib_html2text();

    $parsers = array();
    $parsers[] = array(array('block','inline','link','listitem'));

    $str = PM_BBC_formatTextBlock($str,'text',$parsers);

    // we don't have a stylesheet for email, so replace our div with the style...
    $str = str_replace('<div class="quotemain">','<div style="border: 1px dotted #000;border-left: 4px solid #8394B2;color:#465584;  padding: 4px;  margin: 5px auto 8px auto;">',$str);

    return $str;
}

function PM_previewMessage( $msgID = 0 )
{
    global $_CONF, $_USER, $_TABLES;

    $retval = '';

    $msg['datetime']   = time();
    $msg['username_list'] = $_POST['username_list'];
    $msg['subject']    = $_POST['subject'];
    $msg['message']    = $_POST['message'];
    $msg['source_uid'] = $_USER['uid'];
    $msg['target_uid'] = $_USER['uid'];

    // get user information
    $username = '';
    $fullname = '';
    $email = '';
    $homepage = '';
    $sig = '';
    $regdate = '';
    $photo = '';
    $about = '';
    $location = '';

    $sql = "SELECT username,fullname,email,homepage,sig,regdate,photo,about,location,emailfromuser FROM {$_TABLES['users']} AS user LEFT JOIN {$_TABLES['userinfo']} AS info ON user.uid=info.uid LEFT JOIN {$_TABLES['userprefs']} AS prefs ON info.uid=prefs.uid WHERE user.uid=".(int) $msg['source_uid'];
    $result = DB_query($sql);
    if ( DB_numRows($result) > 0 ) {
        list($username,$fullname,$email,$homepage,$sig,$regdate,$photo,$about,$location,$emailfromuser) = DB_fetchArray($result);
    }

    // clean things up a little...
    $subject = htmlentities($msg['subject'], ENT_QUOTES, COM_getEncodingt());

    $T = new Template($_CONF['path'] . 'plugins/pm/templates/');
    $T->set_file (array ('message'=>'message_preview.thtml'));

    $parsers = array();
    $parsers[] = array(array('block','inline','link','listitem'), '_bbc_replacesmiley');
    $dt = new Date($msg['datetime'],$_USER['tzid']);
    $T->set_var(array(
        'from'        => $msg['source_uid'],
        'to'          => $msg['target_uid'],
        'subject'     => $subject,
        'date'        => $dt->format($dt->getUserFormat(),true),
        'msg_text'    => PM_BBC_formatTextBlock($msg['message'],'text',$parsers),
        'avatar'      => USER_getPhoto($msg['source_uid'],$photo,'',128),
        'from_name'   => $username,
        'to_name'     => htmlentities($msg['username_list'], ENT_QUOTES, COM_getEncodingt()),
        'rank'        => SEC_inGroup('Root',$msg['source_uid']) ? 'Site Admin' : 'User',
        'registered'  => $regdate,
        'signature'   => nl2br($sig),
        'homepage'    => $homepage,
        'location'    => $location,
        'email'       => $emailfromuser ? $email : '',
    ));

    $T->parse ('output', 'message');
    $retval .= $T->finish ($T->get_var('output'));
    return $retval;
}

function PM_msgEditor($msgid = 0, $reply_msgid = 0,$to='', $subject='', $message='', $errors = array() )
{
    global $_CONF, $_TABLES, $_USER;

    $retval = '';

    $T = new Template($_CONF['path'] . 'plugins/pm/templates/');
    $T->set_file (array (
        'compose'    =>  'compose.thtml',
    ));

    $groupList = '';
    $pm_users_grp_id = DB_getItem($_TABLES['groups'],'grp_id','grp_name="PM Users"');
    $groupList .= $pm_users_grp_id;
    // get all the groups that belong to this group:
    $result = DB_query ("SELECT ug_grp_id FROM {$_TABLES['group_assignments']} WHERE ug_main_grp_id = ".(int) $pm_users_grp_id." AND ug_uid IS NULL");
    $numrows = DB_numRows ($result);
    while ($A = DB_fetchArray($result) ) {
        $groupList .= ','.$A['ug_grp_id'];
    }
    $sql = "SELECT DISTINCT {$_TABLES['users']}.uid,username,fullname,block "
          ."FROM {$_TABLES['group_assignments']},{$_TABLES['users']} LEFT JOIN {$_TABLES['pm_userprefs']} "
          ." ON {$_TABLES['users']}.uid={$_TABLES['pm_userprefs']}.uid "
          ."WHERE {$_TABLES['users']}.uid > 1 AND {$_TABLES['users']}.status=3 "
          ."AND {$_TABLES['users']}.uid<>".(int) $_USER['uid']." "
          ."AND {$_TABLES['users']}.uid = {$_TABLES['group_assignments']}.ug_uid "
          ."AND ({$_TABLES['group_assignments']}.ug_main_grp_id IN ({$groupList})) "
          ."ORDER BY username ASC";

    $userselect = '<select id="combo_user" name="to_name"> ';
    $result = DB_query($sql);
    while ($userRow = DB_fetchArray($result) ) {
        if ( $userRow['block'] != 1 ) {
            $userselect .= '<option value="'.$userRow['username'].'">'.$userRow['username'].'</option>' .LB;
        }
    }
    $userselect .= '</select>';

    $friendselect = '<select id="combo_friend" name="combo_friend" size="5" style="width:10em;"> ';
    $sql = "SELECT * FROM {$_TABLES['pm_friends']} WHERE uid=".(int) $_USER['uid']." ORDER BY friend_name ASC";
    $result = DB_query($sql);
    while ($friendRow = DB_fetchArray($result) ) {
        $friendselect .= '<option value="'.$friendRow['friend_name'].'">'.$friendRow['friend_name'].'</option>' .LB;
    }
    $friendselect .= '</select>';

    $additionalCodes = array();

//    $additionalCodes = array(
//                       array('name'=>'youtube','label'=>'YouTube','help'=>'Embed YouTube Video: [youtube=]text[/youtube]','start_tag'=>'[youtube=]','end_tag'=>'[/youtube]','select'=>''),
//                    );

    $bbcodeEditor = BBC_editor($message,'compose_form','message',$additionalCodes);

    $T->set_var(array(
        'to'          => htmlentities($to, ENT_QUOTES, COM_getEncodingt()),
        'subject'     => htmlentities($subject, ENT_QUOTES, COM_getEncodingt()),
        'userselect'  => $userselect,
        'friendselect'=> $friendselect,
        'reply_msgid' => $reply_msgid,
        'msgid'       => $msgid,
        'editor'        => $bbcodeEditor,
    ));
    $error_message = '';
    if ( count($errors) > 0 ) {
        foreach($errors AS $error) {
            $error_message .= $error .'<br />';
        }
    }
    $T->set_var('error_message',$error_message);

    $T->set_var('gltoken', SEC_createToken());
    $T->set_var('gltoken_name', CSRF_TOKEN);

    $T->parse ('output', 'compose');
    $retval .= $T->finish ($T->get_var('output'));

    return $retval;
}

function PM_msgSend( )
{
    global $_CONF, $_PM_CONF, $_TABLES, $_USER, $LANG_PM_ERROR;

    if ( !SEC_checkToken() ) {
        $errArray[] = 'Security Token Failure';
        return array(false,$errArray);
    }

    $toList = $_POST['username_list'];

    $toArray = explode(',',$toList);

    $counter = 0;

    $errArray = array();
    $distributionList = array();

    $toList = '';
    foreach ( $toArray AS $to ) {
        $to = trim(COM_applyFilter($to));
        if ( $to == '' ) {
            continue;
        }
        $sql = "SELECT {$_TABLES['users']}.uid, block FROM {$_TABLES['users']} LEFT JOIN {$_TABLES['pm_userprefs']} "
              ." ON {$_TABLES['users']}.uid={$_TABLES['pm_userprefs']}.uid "
              ." WHERE {$_TABLES['users']}.username='".DB_escapeString($to)."'";

        $result = DB_query($sql);
        if ( DB_numRows($result) < 1 ) {
            $errArray[] = $LANG_PM_ERROR['unknown_user']. ': '.$to;
        } else {
            list($toUID,$block) = DB_fetchArray($result);
            if ( $block == 1 ) {
                $errArray[] = $LANG_PM_ERROR['private_user']. ': '.$to;
            } else {
                $distributionList[$counter]['uid'] = $toUID;
                $distributionList[$counter]['username'] = $to;
                if ( $counter > 0 ) {
                    $toList .= ','.$to;
                } else {
                    $toList .= $to;
                }
                $counter++;
            }
        }
    }
    if ( $counter == 0 && count($errArray) == 0 ) {
        $errArray[] = $LANG_PM_ERROR['no_to_address'];
    }
    if ( count($errArray) > 0 ) {
        return array(false,$errArray);
    }

    if ( $counter > $_PM_CONF['max_recipients'] ) {
        $errArray[] = sprintf($LANG_PM_ERROR['too_many_recipients'], $_PM_CONF['max_recipients']);
        return array(false,$errArray);
    }

    $subject = $_POST['subject'];
    if ( strlen($subject) < 4 ) {
        $errArray[] = $LANG_PM_ERROR['no_subject'];
    }
    $message = $_POST['message'];
    if ( strlen($message) < 4 ) {
        $errArray[] = $LANG_PM_ERROR['no_message'];
    }
    if ( count($errArray) > 0 ) {
        return array(false,$errArray);
    }

    // do a little cleaning...
    $subject = strip_tags($subject);

    $parent_id = 0;

    $reply_msgid = COM_applyFilter($_POST['reply_msgid'],true);

    if ( $reply_msgid > 0 ) {
        $pm_replied = 1;
        $parent_id = DB_getItem($_TABLES['pm_msg'],'parent_id','msg_id='.(int) $reply_msgid );
        if ( $parent_id == 0 ) {
            $parent_id = $reply_msgid;
        }
    } else {
        $pm_replied = 0;
    }

    $msgCount = count($distributionList);
    $REMOTE_ADDR = $_SERVER['REMOTE_ADDR'];

    $sql  = "INSERT INTO {$_TABLES['pm_msg']} ";
    $sql .= "(parent_id,author_uid,author_name,author_ip,message_time,message_subject,message_text,to_address,bcc_address) ";
    $sql .= "VALUES($parent_id,".(int) $_USER['uid'].",'".DB_escapeString($_USER['username'])."','".DB_escapeString($REMOTE_ADDR)."',UNIX_TIMESTAMP(),'".DB_escapeString($subject)."','".DB_escapeString($message)."','".DB_escapeString($toList)."','')";

    DB_query($sql);

    $lastmsg_id = DB_insertID();
    // insert a record for each recipient
    for($x=0;$x<$msgCount;$x++) {
        $targetUID = (int) $distributionList[$x]['uid'];
        $targetUserName = DB_escapeString($distributionList[$x]['username']);
        $sql  = "INSERT INTO {$_TABLES['pm_dist']} ";
        $sql .= "(msg_id,user_id,username,author_uid) ";
        $sql .= "VALUES (".(int) $lastmsg_id.",".(int) $targetUID.",'$targetUserName',".(int) $_USER['uid'].")";
        DB_query($sql);

        PM_notify($targetUserName,$targetUID,$_USER['username'],$subject,$message );
    }

    // insert a record for the user sending the message...
    $sql  = "INSERT INTO {$_TABLES['pm_dist']} ";
    $sql .= "(msg_id,user_id,username,author_uid,folder_name,pm_unread,pm_replied) ";
    $sql .= "VALUES (".(int)$lastmsg_id.",".(int) $_USER['uid'].",'".DB_escapeString($_USER['username'])."',".(int) $_USER['uid'].",'outbox',0,0)";
    DB_query($sql);

    // update original record to show it has been replied...
    DB_query("UPDATE {$_TABLES['pm_dist']} SET pm_replied=1 WHERE msg_id=".(int) $reply_msgid." AND user_id=".(int) $_USER['uid']." AND folder_name NOT IN ('outbox','sent')");

    COM_updateSpeedlimit ('pm');
    CACHE_remove_instance('stmenu');

    return array(true,'');
}

if ( isset($_POST['send']) ) {
    $mode = 'send';
} elseif (isset($_POST['preview']) ) {
    $mode = 'preview';
} elseif (isset($_POST['cancel']) ) {
    $mode = 'cancel';
} elseif ( isset($_GET['mode']) ) {
    $mode = $_GET['mode'];
} else {
    $mode = 'new';
}

$message = '';

switch ( $mode ) {
    case 'new' :
        COM_clearSpeedlimit ($_PM_CONF['post_speedlimit'], 'pm');
        $last = COM_checkSpeedlimit ('pm');
        if ($last > 0) {
            echo COM_refresh($_CONF['site_url'].'/pm/index.php?msg=4');
            exit;
        }
        if ( isset($_GET['uid']) ) {
            $uid = COM_applyFilter($_GET['uid'],true);
            $to_name = DB_getItem($_TABLES['users'],'username','uid='. (int) $uid) . ',';
        } else {
            if ( isset($_GET['username']) ) {
                $to_name = COM_applyFilter($_GET['username']) . ',';
            } else {
                $to_name = '';
            }
        }
        $body = PM_msgEditor(0,0,$to_name);
        break;
    case 'edit' :
        $body = PM_msgEditor($msgid);
        break;
    case 'quote' :
        COM_clearSpeedlimit ($_PM_CONF['post_speedlimit'], 'pm');
        $last = COM_checkSpeedlimit ('pm');
        if ($last > 0) {
            echo COM_refresh($_CONF['site_url'].'/pm/index.php?msg=4');
            exit;
        }
        $reply_msgid = COM_applyFilter($_GET['msgid'],true);
        $sql = "SELECT * FROM {$_TABLES['pm_msg']} msg LEFT JOIN {$_TABLES['pm_dist']} dist ON msg.msg_id=dist.msg_id WHERE msg.msg_id=".(int) $reply_msgid ." AND dist.user_id=".(int) $_USER['uid'];
        $result = DB_query($sql);
        if ( DB_numRows($result) < 1 ) {
            PM_alertMessage( $LANG_PM_ERROR['invalid_reply_id'] );
        }
        $msg = DB_fetchArray($result);
        $message = $msg['message_text'];
        $source_username = $msg['author_name'].',';
        $subject = $msg['message_subject'];
        $message = '[quote][u]Quote by: '.$source_username.'[/u][p]'.$message.'[/p][/quote]';
        if ( substr($subject,0,3) == 'Re:' ) {
            $prefix = '';
        } else {
            $prefix = 'Re: ';
        }
        $body = PM_msgEditor(0,$reply_msgid,$source_username,$prefix.$subject,$message);
        $body .= PM_showHistory($reply_msgid,true);
        break;
    case 'reply' :
        COM_clearSpeedlimit ($_PM_CONF['post_speedlimit'], 'pm');
        $reply_msgid = COM_applyFilter($_GET['msgid'],true);
        $last = COM_checkSpeedlimit ('pm');
        if ($last > 0) {
            echo COM_refresh($_CONF['site_url'].'/pm/view.php?msgid='.(int) $reply_msgid.'&amp;msg=4');
            exit;
        }
        $sql = "SELECT * FROM {$_TABLES['pm_msg']} msg LEFT JOIN {$_TABLES['pm_dist']} dist ON msg.msg_id=dist.msg_id WHERE msg.msg_id=".(int) $reply_msgid." AND dist.user_id=".(int) $_USER['uid'];
        $result = DB_query($sql);
        if ( DB_numRows($result) < 1 ) {
            PM_alertMessage( $LANG_PM_ERROR['invalid_reply_id'] );
        }
        $msg = DB_fetchArray($result);
        $source_username = $msg['author_name'].',';
        $subject = $msg['message_subject'];
        if ( substr($subject,0,3) == 'Re:' ) {
            $prefix = '';
        } else {
            $prefix = 'Re: ';
        }
        $body = PM_msgEditor(0,$reply_msgid,$source_username,$prefix.$subject);
        $body .= PM_showHistory($reply_msgid,true);
        break;
    case 'preview' :
        $body        = PM_previewMessage();
        $to          = $_POST['username_list'];
        $subject     = $_POST['subject'];
        $message     = $_POST['message'];
        $msgid       = COM_applyFilter($_POST['msgid'],true);
        $reply_msgid = COM_applyFilter($_POST['reply_msgid'],true);
        $body       .= PM_msgEditor($msgid,$reply_msgid,$to,$subject,$message);
        $body       .= PM_showHistory($reply_msgid,true);
        break;
    case 'send' :
        COM_clearSpeedlimit ($_PM_CONF['post_speedlimit'], 'pm');
        $last = COM_checkSpeedlimit ('pm');
        if ($last > 0) {
            echo COM_refresh($_CONF['site_url'].'/pm/index.php?msg=4');
            exit;
        }
        list($rc,$errors) = PM_msgSend( );
        if ( !$rc ) {
            $to          = $_POST['username_list'];
            $subject     = $_POST['subject'];
            $message     = $_POST['message'];
            $msgid       = COM_applyFilter($_POST['msgid'],true);
            $reply_msgid = COM_applyFilter($_POST['reply_msgid'],true);
            $body        = PM_msgEditor($msgid,$reply_msgid,$to,$subject,$message,$errors);
        } else {
            echo COM_refresh($_CONF['site_url'].'/pm/index.php?msg=1');
            exit;
        }
        break;
    case 'cancel' :
        echo COM_refresh($_CONF['site_url'].'/pm/index.php');
        exit;
        break;
}

$styleLink = '<link rel="stylesheet" type="text/css" href="'.$_CONF['site_url'].'/pm/style.css" />'.LB;

$display = PM_siteHeader($LANG_PM00['title'],$styleLink);
$display .= $body;
$display .= PM_siteFooter();
echo $display;
?>