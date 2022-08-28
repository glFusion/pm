<?php
// +--------------------------------------------------------------------------+
// | PM Plugin for glFusion CMS                                               |
// +--------------------------------------------------------------------------+
// | friends.php                                                              |
// |                                                                          |
// | PM plugin friend management                                              |
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
use glFusion\Database\Database;
PM_checkAccess();

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

    $T = new Template(pm_get_template_path());
    $T->set_file (array (
        'list' => 'friends.thtml',
        'menu' => 'menu.thtml',
    ));

    $uid = (int)$_USER['uid'];
    $have_selected = array($uid);   // track all previous selections

    // Build friends list
    $Friends = PM\Friend::getByUser($uid);
    $friendselect = '';
    foreach ($Friends as $Friend) {
        $have_selected[] = $Friend->getFriendId();
        $friendselect .= '<option value="'.$Friend->getFriendId() . '">' .
            $Friend->getFriendName() . '</option>' . LB;
    }

    // Build blocked list
    $Enemies = PM\Friend::getByUser($uid, false);
    $enemyselect = '';
    foreach ($Enemies as $Enemy) {
        $have_selected[] = $Enemy->getFriendId();
        $enemyselect .= '<option value="'.$Enemy->getFriendId() . '">' .
            $Enemy->getFriendName() . '</option>' . LB;
    }

    $have_selected = implode(',', $have_selected);
    $Feature = glFusion\Feature::getByName('pm.user');
    $Groups = $Feature->getGroups();
    $groupList = implode(',', $Groups);
    $userselect = '';
    $sql = "SELECT DISTINCT u.uid, u.username, u.fullname
        FROM {$_TABLES['group_assignments']} ga
        LEFT JOIN {$_TABLES['users']} u
        ON u.uid = ga.ug_uid
        WHERE u.uid > 1 AND u.status=3
        AND u.uid NOT IN (?)
        AND ga.ug_main_grp_id IN (?)
        ORDER BY username ASC";
    $db = Database::getInstance();
    try {
        $stmt = $db->conn->executeQuery(
            $sql,
            array($have_selected, $groupList),
            array(DATABASE::STRING, DATABASE::STRING)
        );
    } catch(\Throwable $e) {
    }
    $data = $stmt->fetchAll(Database::ASSOCIATIVE);
    if (is_array($data)) {
        foreach ($data as $userRow) {
            $userselect .= '<option value="'.$userRow['uid'].'">'.$userRow['username'].'</option>' .LB;
        }
    }

    $error_message = '';
    if ( count($errors) > 0 ) {
        foreach($errors AS $error) {
            $error_message .= $error .'<br />';
        }
    }

    $T->set_var(array(
        'pm_home'       => $LANG_PM00['pm_index'],
        'friend_options' => $friendselect,
        'user_options'  => $userselect,
        'blocked_options' => $enemyselect,
        'newfriends'    => $newfriends,
        'error_message' => $error_message,
        'gltoken'       => SEC_createToken(),
        'gltoken_name'  => CSRF_TOKEN,
    ) );
    $T->parse('menu', 'menu');
    $T->parse('output', 'list');
    $retval .= $T->finish ($T->get_var('output'));
    return $retval;
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
    PM\Friend::processAddressBook($_POST);
    echo COM_refresh($_CONF['site_url'] . '/pm/index.php');
    break;
default :
    $retval = PM\Views\AddressBook::manage();
    break;
}

$display = COM_siteHeader('menu',$LANG_PM00['title']);
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
