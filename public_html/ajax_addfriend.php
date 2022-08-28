<?php
/**
 * Take action when a friend is added or removed.
 *
 * @author      Mark Evans <mark AT glfusion DOT org>
 * @copyright   Copyright (c) 2022 Mark Evans <mark AT glfusion DOT org>
 * @package     pm
 * @version     v3.0.0
 * @license     http://opensource.org/licenses/gpl-2.0.php
 *              GNU Public License v2 or later
 * @filesource
 */
require_once '../lib-common.php';

if (
    !in_array('pm', $_PLUGINS) ||
    COM_isAnonUser() ||
    !SEC_hasRights('pm.user')
) {
    COM_404();
    exit;
}

$action = $_POST['action'];
$uid = COM_applyFilter($_POST['uid'],true);
$stat = COM_applyFilter($_POST['stat'], true);
$retval = array(
    'status' => 0,
    'message' => '',
);

if ($_USER['uid'] == $uid) {
    return json_encode($retval);
}

switch ($action) {
case 'addfriend':
    switch ($stat) {
    case 0:     // removing friend
        PM\Friend::remFriend($uid);
        $retval['message'] = $LANG_PM00['friend_removed'];
        break;
    case 1:
        PM\Friend::addFriend($uid);
        $retval['message'] = $LANG_PM00['friend_added'];
        break;
    }
    break;
case 'blockuser':
    switch ($stat) {
    case 0:
        PM\Friend::unblockUser($uid);
        $retval['message'] = $LANG_PM00['user_unblocked'];
        break;
    case 1:
        PM\Friend::blockUser($uid);
        $retval['message'] = $LANG_PM00['user_blocked'];
        break;
    }
}
echo json_encode($retval);

