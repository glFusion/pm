<?php
// +--------------------------------------------------------------------------+
// | PM Plugin for glFusion CMS                                               |
// +--------------------------------------------------------------------------+
// | friends.php                                                              |
// |                                                                          |
// | PM plugin friend management                                              |
// +--------------------------------------------------------------------------+
// | $Id::                                                                   $|
// +--------------------------------------------------------------------------+
// | Copyright (C) 2009-2011 by the following authors:                        |
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

/*
 * Only allow logged-in users or users who have the pm.user permission
 */
if ( COM_isAnonUser() ) {
    echo COM_refresh($_CONF['site_url'].'/users.php');
    exit;
}
if ( !SEC_hasRights('pm.user') ) {
    echo COM_refresh($_CONF['site_url']);
}

function PM_friendMaintenance($newfriends='', $errors = array())
{
    global $_CONF, $_PM_CONF, $_USER, $_TABLES, $LANG_PM00;

    $retval = '';

    $T = new Template($_CONF['path'] . 'plugins/pm/templates/');
    $T->set_file (array (
        'list'      =>  'friends.thtml',
    ));

    // build friend list

    $friendselect = '<select id="combo_friends" name="current_friends[]"  multiple="multiple" size="10" style="width:25%;" onfocus="ml_autocomplete.populate(event)" onkeydown="ml_autocomplete.setSelection(event)" onkeypress="javascript:return false"> ';
    $sql = "SELECT * FROM {$_TABLES['pm_friends']} WHERE uid=".(int) $_USER['uid']." ORDER BY friend_name ASC";
    $result = DB_query($sql);
    while ($friendRow = DB_fetchArray($result) ) {
        $friendselect .= '<option value="'.$friendRow['friend_id'].'">'.$friendRow['friend_name'].'</option>' .LB;
    }
    $friendselect .= '</select>';




    $groupList = '';

    $pm_users_grp_id = DB_getItem($_TABLES['groups'],'grp_id','grp_name="PM Users"');

    $groupList .= $pm_users_grp_id;

    // get all the groups that belong to this group:

    $result = DB_query ("SELECT ug_grp_id FROM {$_TABLES['group_assignments']} WHERE ug_main_grp_id = ".(int) $pm_users_grp_id." AND ug_uid IS NULL");
    $numrows = DB_numRows ($result);
    while ($A = DB_fetchArray($result) ) {
        $groupList .= ','.(int) $A['ug_grp_id'];
    }

    $sql = "SELECT DISTINCT {$_TABLES['users']}.uid,username,fullname "
          ."FROM {$_TABLES['group_assignments']},{$_TABLES['users']} "
          ."WHERE {$_TABLES['users']}.uid > 1 AND {$_TABLES['users']}.status=3 "
          ."AND {$_TABLES['users']}.uid<>".(int) $_USER['uid']." "
          ."AND {$_TABLES['users']}.uid = {$_TABLES['group_assignments']}.ug_uid "
          ."AND ({$_TABLES['group_assignments']}.ug_main_grp_id IN ({$groupList})) "
          ."ORDER BY username ASC";

    $userselect = '<select id="combo_user" name="combo_user"> ';
    $result = DB_query($sql);
    while ($userRow = DB_fetchArray($result) ) {
        $userselect .= '<option value="'.$userRow['username'].'">'.$userRow['username'].'</option>' .LB;
    }
    $userselect .= '</select>';

    $T->set_var(array(
        'pm_home'       => $LANG_PM00['pm_index'],
        'friend_select' => $friendselect,
        'user_select'   => $userselect,
        'newfriends'    => $newfriends,
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

    $T->parse ('output', 'list');
    $retval .= $T->finish ($T->get_var('output'));

    return $retval;
}

function PM_friendProcess()
{
    global $_CONF, $_PM_CONF, $_TABLES, $_USER, $LANG_PM_ERROR;

    if ( !SEC_checkToken() ) {
        $errArray[] = $LANG_PM_ERROR['token_failure'];
        return array(false,$errArray);
    }

    /*
     * Process deletions
     */

    $cfriends = array();

    if ( isset($_POST['current_friends']) ) {
        $cfriends = $_POST['current_friends'];
        if (is_array($cfriends) ) {
            foreach ($cfriends AS $friend) {
                DB_query("DELETE FROM {$_TABLES['pm_friends']} WHERE (uid = ".(int) $_USER['uid']." AND friend_id=".(int) $friend.")");
            }
        }
    }

    /*
     * Process additions
     */

    $newFriendsList = $_POST['newfriends'];

    $friendArray = explode(',',$newFriendsList);

    $counter = 0;

    $errArray = array();
    $distributionList = array();

    $friendList = '';
    foreach ( $friendArray AS $friend ) {
        $to = trim(COM_applyFilter($friend));
        if ( $friend == '' ) {
            continue;
        }
        $friendUID = DB_getItem($_TABLES['users'],'uid','username="'.DB_escapeString($friend).'"');
        if ( $friendUID == '' || $friendUID == 0 ) {
            $errArray[] = $LANG_PM_ERROR['unknown_user']. ': '.$friend;
        } else {
            $distributionList[$counter]['uid'] = $friendUID;
            $distributionList[$counter]['username'] = $friend;
            if ( $counter > 0 ) {
                $friendList .= ','.$friend;
            } else {
                $friendList .= $friend;
            }
            $counter++;
        }
    }
    if ( count($errArray) > 0 ) {
        return array(false,$errArray);
    }

    $friendCount = count($distributionList);
    for($x=0;$x<$friendCount;$x++) {
        $friendUID = (int) $distributionList[$x]['uid'];
        $friendUserName = DB_escapeString($distributionList[$x]['username']);
        $sql  = "INSERT INTO {$_TABLES['pm_friends']} ";
        $sql .= "(uid,friend_id,friend_name) ";
        $sql .= "VALUES (".$_USER['uid'].",".(int) $friendUID.",'$friendUserName')";
        DB_query($sql,1);
    }
    return array(true,'');
}


/*
 * Start of main code
 */

$display = '';

$retval = '';

if ( isset($_POST['submit']) ) {
    $mode = 'process';
} elseif (isset($_POST['cancel']) ) {
    $mode = 'cancel';
} else {
    $mode = '';
}

switch ( $mode ) {
    case 'process' :
        // perform any deletions
        // perform any additions
        list($rc,$errors) = PM_friendProcess();
        if ( !$rc ) {
            $friendList  = $_POST['newfriends'];
            $retval     .= PM_friendMaintenance($friendList,$errors);
        } else {
            echo COM_refresh($_CONF['site_url'].'/pm/index.php?msg=5');
            exit;
        }
        break;
    case 'cancel' :
        echo COM_refresh($_CONF['site_url'].'/pm/index.php');
        exit;
        break;
    default :
        $retval = PM_friendMaintenance();
        break;
}

$styleLink = '<link rel="stylesheet" type="text/css" href="'.$_CONF['site_url'].'/pm/style.css" />'.LB;

$display = COM_siteHeader('menu',$LANG_PM00['title'],$styleLink);
if ( isset($_GET['msg']) ) {
    $msg_header = COM_applyFilter ($_GET['msg'], true);
} else {
    $msg_header = 0;
}
if ( $msg_header > 0 ) {
    $display .= COM_showMessage ($msg_header, 'pm');
}
$display .= $retval;
$display .= COM_siteFooter();
echo $display;
?>