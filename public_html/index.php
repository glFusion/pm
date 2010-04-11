<?php
// +--------------------------------------------------------------------------+
// | PM Plugin for glFusion CMS                                               |
// +--------------------------------------------------------------------------+
// | index.php                                                                |
// |                                                                          |
// | PM plugin main index page                                                |
// +--------------------------------------------------------------------------+
// | $Id::                                                                   $|
// +--------------------------------------------------------------------------+
// | Copyright (C) 2009 by the following authors:                             |
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

require_once $_CONF['path'].'plugins/pm/include/lib-pm.php';

function PM_processMarked()
{
    global $_CONF, $_PM_CONF, $_TABLES, $_USER;

    $markedMessages = array();

    $validOperations = array('delete_marked','archive_marked');

    $operation = $_POST['mark_option'];
    if ( !in_array($operation,$validOperations) ) {
        return;
    }
    $current_folder = COM_applyFilter($_POST['current_folder']);
    if ( !in_array($current_folder,array('inbox','sent','archive','outbox') ) ) {
        return;
    }

    $markedMessages = $_POST['marked_msg_id'];
    if (is_array($markedMessages) ) {
        foreach( $markedMessages AS $msg_id ) {
            if ( $operation == 'delete_marked' ) {
                if ( $current_folder == 'outbox' ) {
                    // we know no one has read it yet or it wouldn't be in our out box
                    DB_query("DELETE FROM {$_TABLES['pm_dist']} WHERE (msg_id = ".intval($msg_id).")");
                    DB_query("DELETE FROM {$_TABLES['pm_msg']} WHERE (msg_id = ".intval($msg_id).")");
                } else {
                    // must be in our in, sent, or archive folder
                    DB_query("DELETE FROM {$_TABLES['pm_dist']} WHERE msg_id = ".intval($msg_id)." AND user_id=".$_USER['uid']." AND folder_name='".$current_folder."'");
                    $msgCount = DB_count($_TABLES['pm_dist'],'msg_id',intval($msg_id));
                    if ( $msgCount == 0 ) {
                        DB_query("DELETE FROM {$_TABLES['pm_msg']} WHERE (msg_id = ".intval($msg_id).")");
                    }
                }
            }
            if ( $operation == 'archive_marked' ) {
                DB_query("UPDATE {$_TABLES['pm_dist']} SET folder_name='archive' WHERE msg_id=".intval($msg_id)." AND user_id=".$_USER['uid']." AND folder_name='".$current_folder."'");
            }
        }
    }
    if ( $operation == 'delete_marked' ) {
        $return_msg = 2;
    } else {
        $return_msg = 3;
    }
    return $return_msg;
}


/*
 * Start of main code
 */

$display = '';

if ( isset($_POST['submit_mark']) ) {
    $msg_header = PM_processMarked();
}

if ( isset($_GET['mode']) && $_GET['mode'] == 'delete' ) {
    if ( isset($_GET['msgid']) && isset($_GET['folder']) ) {
        $msg_id = COM_applyFilter($_GET['msgid'],true);
        $folder = COM_applyFilter($_GET['folder']);
        PM_deleteMessage($msg_id, $folder);
    }
}

$folder = PM_getFolder( 'folder' );

if ( isset($_GET['st']) ) {
    $st = COM_applyFilter($_GET['st'],true);
} elseif (isset($_POST['st'])) {
    $st = COM_applyFilter($_POST['st'],true);
} else {
    $st = 0;
}

if ( !in_array($st,array(0,1,7,14,30,90,180,365))) {
    $st = 0;
}

if ( isset($_GET['sk'] ) ) {
    $sk = COM_applyFilter($_GET['sk']);
} elseif ( isset($_POST['sk']) ) {
    $sk = COM_applyFilter($_POST['sk']);
} else {
    $sk = 't';
}

if ( !in_array($sk,array('a','t','s') ) ) {
    $sk = 't';
}

if ( isset($_GET['sd'] ) ) {
    $sd = COM_applyFilter($_GET['sd']);
} elseif (isset($_POST['sd']) ) {
    $sd = COM_applyFilter($_POST['sd']);
} else {
    $sd = 'd';
}

if ( !in_array($sd,array('a','d') ) ) {
    $sd = 'd';
}

if ( isset($_GET['page']) ) {
    $page = COM_applyFilter($_GET['page'],true);
} elseif (isset($_POST['page']) ) {
    $page = COM_applyFilter($_POST['page'],true);
} else {
    $page = 1;
}

$page = $page-1;
if ( $page < 0 ) {
    $page = 0;
}

if ( isset($_PM_CONF['messages_per_page']) ) {
    $recordPerScreen = $_PM_CONF['messages_per_page'];
} else {
    $recordPerScreen = 15;
}

$retval = '';

$T = new Template($_CONF['path'] . 'plugins/pm/templates/');
$T->set_file (array (
    'list'      =>  'pm_box.thtml',
    'msg_record' => 'pm_message_record.thtml'
));

// build folder selection list
$folderSelect = '<select id="folder" name="folder">'.LB;
$folderSelect .= '<option'. ($folder == 'inbox'   ? ' selected="selected" ' : ' ') . 'value="inbox">'   . $LANG_PM00['inbox']   . '</option>'.LB;
$folderSelect .= '<option'. ($folder == 'outbox'  ? ' selected="selected" ' : ' ') . 'value="outbox">'  . $LANG_PM00['outbox']  . '</option>'.LB;
$folderSelect .= '<option'. ($folder == 'sent'    ? ' selected="selected" ' : ' ') . 'value="sent">'    . $LANG_PM00['sent']    . '</option>'.LB;
$folderSelect .= '<option'. ($folder == 'archive' ? ' selected="selected" ' : ' ') . 'value="archive">' . $LANG_PM00['archive'] . '</option>'.LB;
$folderSelect .= '</select>'.LB;

// build time select
$timeSelect  = '<select id="st" name="st">'.LB;
$timeSelect .= '<option'. ($st == 0 ? ' selected="selected" ' : ' ')   . ' value="0">'. $LANG_PM00['all_messages'].'</option>'.LB;
$timeSelect .= '<option'. ($st == 1 ? ' selected="selected" ' : ' ')   . ' value="1">'. $LANG_PM00['one_day'].'</option>'.LB;
$timeSelect .= '<option'. ($st == 7 ? ' selected="selected" ' : ' ')   . ' value="7">'. $LANG_PM00['seven_days'].'</option>'.LB;
$timeSelect .= '<option'. ($st == 14 ? ' selected="selected" ' : ' ')  . ' value="14">'. $LANG_PM00['two_weeks'].'</option>'.LB;
$timeSelect .= '<option'. ($st == 30 ? ' selected="selected" ' : ' ')  . ' value="30">'.$LANG_PM00['one_month'].'</option>'.LB;
$timeSelect .= '<option'. ($st == 90 ? ' selected="selected" ' : ' ')  . ' value="90">'. $LANG_PM00['three_months'].'</option>'.LB;
$timeSelect .= '<option'. ($st == 180 ? ' selected="selected" ' : ' ') . ' value="180">'.$LANG_PM00['six_months'].'</option>'.LB;
$timeSelect .= '<option'. ($st == 365 ? ' selected="selected" ' : ' ') . ' value="365">'.$LANG_PM00['one_year'].'</option>'.LB;
$timeSelect .= '</select>'.LB;

$sortSelect  = '<select id="sk" name="sk">'.LB;
$sortSelect .= '<option'. ($sk == 'a' ? ' selected="selected" ' : ' ') . ' value="a">'.$LANG_PM00['author'].'</option>'.LB;
$sortSelect .= '<option'. ($sk == 't' ? ' selected="selected" ' : ' ') . ' value="t">'.$LANG_PM00['post_time'].'</option>'.LB;
$sortSelect .= '<option'. ($sk == 's' ? ' selected="selected" ' : ' ') . ' value="s">'.$LANG_PM00['subject'].'</option>'.LB;
$sortSelect .= '</select>';

$sortDirSelect  = '<select id="sd" name="sd">'.LB;
$sortDirSelect .= '<option'. ($sd == 'a' ? ' selected="selected" ' : ' ') . ' value="a">'.$LANG_PM00['ascending'].'</option>'.LB;
$sortDirSelect .= '<option'. ($sd == 'd' ? ' selected="selected" ' : ' ') . ' value="d">'.$LANG_PM00['descending'].'</option>'.LB;
$sortDirSelect .= '</select>';

switch ( $folder ) {
    case 'inbox' :
        $totalRecs = DB_count($_TABLES['pm_dist'],array('user_id','folder_name'),array($_USER['uid'],'inbox') );
        $sql  = "SELECT * ";
        $sql .= "FROM {$_TABLES['pm_msg']} msg " ;
        $sql .= "LEFT JOIN {$_TABLES['pm_dist']} dist ON msg.msg_id=dist.msg_id ";
        $sql .= "WHERE dist.user_id=".$_USER['uid']." AND dist.folder_name='inbox' ";
        break;
    case 'sent' :
        $result = DB_query("SELECT count(*) as count FROM {$_TABLES['pm_msg']} msg LEFT JOIN {$_TABLES['pm_dist']} dist ON msg.msg_id=dist.msg_id WHERE msg.author_uid=".$_USER['uid']." AND dist.folder_name='sent'");
        list($totalRecs) = DB_fetchArray($result);
        $sql  = "SELECT * ";
        $sql .= "FROM {$_TABLES['pm_msg']} msg ";
        $sql .= "LEFT JOIN {$_TABLES['pm_dist']} dist ON msg.msg_id=dist.msg_id ";
        $sql .= "WHERE msg.author_uid=".$_USER['uid']." AND dist.folder_name='sent' ";
        break;
    case 'archive' :
        $result = DB_query("SELECT count(*) as count FROM {$_TABLES['pm_msg']} msg LEFT JOIN {$_TABLES['pm_dist']} dist ON  msg.msg_id = dist.msg_id WHERE dist.author_uid =".$_USER['uid']." AND dist.folder_name='archive' ");
        list($totalRecs) = DB_fetchArray($result);
        $sql  = "SELECT * ";
        $sql .= "FROM {$_TABLES['pm_msg']} msg ";
        $sql .= "LEFT JOIN {$_TABLES['pm_dist']} dist ON msg.msg_id=dist.msg_id ";
        $sql .= "WHERE dist.user_id=".$_USER['uid']." AND dist.folder_name='archive' ";
        break;
    case 'outbox' :
        $result = DB_query("SELECT COUNT(*) as count FROM {$_TABLES['pm_msg']} msg LEFT JOIN {$_TABLES['pm_dist']} dist ON  msg.msg_id = dist.msg_id WHERE (msg.author_uid=".$_USER['uid']." AND dist.folder_name='outbox') ");
        list($totalRecs) = DB_fetchArray($result);
        $sql  = "SELECT * ";
        $sql .= "FROM {$_TABLES['pm_msg']} msg ";
        $sql .= "LEFT JOIN {$_TABLES['pm_dist']} dist ON msg.msg_id=dist.msg_id ";
        $sql .= "WHERE msg.author_uid=".$_USER['uid']." AND dist.folder_name='outbox' ";
        break;
}

// calculate limits

$start = $page * $recordPerScreen;
$end   = $recordPerScreen;
$numpages = ceil($totalRecs / $recordPerScreen);
$limit = ' LIMIT '.$start.','.$end;

// query range
$date_range = '';

// sort by

switch ($sk) {
    case 'a' :
        if ( $folder == 'inbox' || $folder == 'archive') {
            $orderby = ' ORDER BY author_name';
        } else {
            $orderby = ' ORDER BY username';
        }
        break;
    case 't' :
        $orderby = ' ORDER BY message_time';
        break;
    case 's' :
        $orderby = ' ORDER BY message_subject';
        break;
    default :
        $orderby = ' ORDER BY message_time';
        break;
}

// direction

switch ($sd) {
    case 'a' :
        $orderby = $orderby .' ASC';
        break;
    case 'd' :
        $orderby = $orderby .' DESC';
        break;
    default :
        $orderby = $orderby .' DESC';
        break;
}

// time
switch ($st) {
    case 0 :
        $timeLimit = '';
        break;
    default :
        $min_post_time = time() - ($st * 86400);
        $timeLimit = " AND msg.message_time >= ". $min_post_time;
        break;
}

$sql = $sql . $timeLimit . $orderby . $limit;

$result = DB_query($sql);
$x = 0;
$msgCounter = 0;
while ($msg = DB_fetchArray($result) ) {
    $userDate = COM_getUserDateTimeFormat($msg['message_time'] );

    if ( $msg['author_name'] == '' ) {
        $fromUserName = 'unknown';
    } else {
        $fromUserName = $msg['author_name'];
    }

    if ( $msg['pm_unread'] == 1 ) {
        $icon_style = 'pm-new';
    } else {
        $icon_style = 'pm-read';

        if ( $msg['pm_replied'] ) {
            $icon_style = 'pm-reply';
        }

        if ( $msg['pm_forwarded'] ) {
            $icon_style = 'pm-forward';
        }
    }

    $printDate = @strftime('%b %d %Y @ %H:%M', $msg['message_time'] );
    if ( $folder == 'outbox' || $folder == 'sent' ) {
        $toArray = array();
        $toArray = explode(',',$msg['to_address']);
        $to_address = '';
        if ( is_array($toArray) ) {
            foreach ($toArray AS $to ) {
                $to_address .= $to . ', ';
            }
        } else {
            $to_address = $msg['to_address'];
        }
        $T->set_var(array(
            'from'          => $to_address
        ));
    } else {
        $T->set_var(array(
            'from'          => '<a href="'.$_CONF['site_url'].'/users.php?mode=profile&amp;uid='.$msg['author_uid'].'">'.$fromUserName.'</a>',
        ));
    }
    $T->set_var(array(
        'subject'       => $msg['message_subject'] == '' ? $LANG_PM00['no_subject'] : $msg['message_subject'],
        'lastdate'      => @strftime('%a - %b %d %Y %H:%M',$msg['message_time']),
        'msg_id'        => $msg['msg_id'],
        'csscounter'    => ($x % 2) + 1,
        'icon_style'    => $icon_style,
        'msg_class'     => $msg['pm_unread'] == 1 ? 'unread' : 'read',
        'found_on_page' => $page+1,
        'folder'        => $folder,
        'lang_by'       => $msg['author_uid'] == $_USER['uid'] ? $LANG_PM00['to'] : $LANG_PM00['by'],
    ));
    $T->parse ('msg_records', 'msg_record',true);
    $x++;
    $msgCounter++;
}

if ( $msgCounter == 0 ) {
    $T->set_var('msg_records','<tr><td style="font-size:1.2em;font-weight:bold;text-align:center;padding-top:10px;padding-bottom:10px;">'.$LANG_PM00['no_messages'].'</td></tr>',false);
}

if ( $folder == 'inbox' ) {
    $result = DB_query("SELECT COUNT(msg_id) AS unread FROM {$_TABLES['pm_dist']} WHERE user_id=".$_USER['uid']." AND pm_unread=1 AND folder_name='inbox'");
    if ( DB_numRows($result) > 0 ) {
        list($unReadCount) = DB_fetchArray($result);
    } else {
        $unReadCount = 0;
    }
    $unReadText = '('.$unReadCount.')';
} else {
    $unReadText = '';
}


$T->set_var(array(
    'pm_home'       => $LANG_PM00['pm_index'],
    'folder'        => $folder,
    'folder_name'   => $LANG_PM00[$folder],
    'newpost_link'  => $LANG_PM00['compose_msg'],
    'pagination'    => COM_printPageNavigation($_CONF['site_url'].'/pm/index.php?folder='.$folder.'&amp;st='.$st.'&amp;sk='.$sk.'&amp;sd='.$sd,$page+1, $numpages),
    'lang_inbox'    => $LANG_PM00['inbox'],
    'lang_sent'     => $LANG_PM00['sent'],
    'lang_archive'  => $LANG_PM00['archive'],
    'lang_outbox'   => $LANG_PM00['outbox'],
    'folder_select' => $folderSelect,
    'time_select'   => $timeSelect,
    'sort_select'   => $sortSelect,
    'sort_dir_select' => $sortDirSelect,
    'current_folder'  => $folder,
    'not_archive'   => ($folder == 'archive' || $folder == 'outbox' ? '' : 'archive'),
    'unread_text'   => $unReadText,

));

if ( !isset($_PM_CONF['messages_per_folder']) ) {
    $allowedMessages = 50;
} else {
    $allowedMessages = $_PM_CONF['messages_per_folder'];
}
if ( $allowedMessages < 1 ) {
    $allowedMessages = 50;
}

$T->set_var('page_messages',$msgCounter);
$T->set_var('total_messages',$totalRecs);
$T->set_var('percent_full', intval(($totalRecs / $allowedMessages)*100));

$T->parse ('output', 'list');
$retval .= $T->finish ($T->get_var('output'));

$styleLink = '<link rel="stylesheet" type="text/css" href="'.$_CONF['site_url'].'/pm/style.css" />'.LB;

$display = PM_siteHeader($LANG_PM00['title'],$styleLink);
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