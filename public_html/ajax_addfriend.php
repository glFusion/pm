<?php
// +--------------------------------------------------------------------------+
// | PM Plugin for glFusion CMS                                               |
// +--------------------------------------------------------------------------+
// | ajax_addfriend.php                                                       |
// |                                                                          |
// | PM plugin add friend                                                     |
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

if ( COM_isAnonUser() || !SEC_hasRights('pm.user')) {
    exit;
}

$uid  = intval(COM_applyFilter($_GET['uid'],true));
$auid = intval(COM_applyFilter($_GET['auid'],true));

$returnHTML = '';

if ( $_USER['uid'] != $uid ) {
    return;
}
if ( $_USER['uid'] == $auid ) {
    return;
}

$friendUserName = DB_getItem($_TABLES['users'],'username','uid='.$auid);
if ( $friendUserName != '' ) {
    $sql  = "INSERT INTO {$_TABLES['pm_friends']} ";
    $sql .= "(uid,friend_id,friend_name) ";
    $sql .= "VALUES (".$_USER['uid'].",'$auid','$friendUserName')";
    DB_query($sql,1);

    $returnHTML = '<br /><strong>'.$LANG_PM00['in_friends_list'].'</strong><br />';
} else {
    $returnHTML = 'User name found';
}

$html = htmlentities ($returnHTML);

$retval = "<result>";
$retval .= "<auid>$auid</auid>";
$retval .= "<html>$html</html>";
$retval .= "</result>";
header("Cache-Control: no-store, no-cache, must-revalidate");
header("content-type: text/xml");
print $retval;
?>