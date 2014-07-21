<?php
// +--------------------------------------------------------------------------+
// | PM Plugin for glFusion CMS                                               |
// +--------------------------------------------------------------------------+
// | import_messenger.php                                                     |
// |                                                                          |
// | PM plugin import gl_messenger records                                    |
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

require_once '../../../lib-common.php';

if (!in_array('pm', $_PLUGINS)) {
    COM_404();
    exit;
}

if (!SEC_inGroup('Root')) {
    // Someone is trying to illegally access this page
    COM_errorLog("Someone has tried to illegally access the PM conversioin page.  User id: {$_USER['uid']}, Username: {$_USER['username']}, IP: $REMOTE_ADDR",1);
    $display = COM_siteHeader ('menu', $LANG_ACCESS['accessdenied'])
             . COM_startBlock ($LANG_ACCESS['accessdenied'])
             . $LANG_ACCESS['plugin_access_denied_msg']
             . COM_endBlock ()
             . COM_siteFooter ();
    echo $display;
    exit;
}

USES_lib_user();

// define the gl_messenger tables we care about...

$_TABLES['messenger_msg']        =  $_DB_table_prefix . 'messenger_msg';
$_TABLES['messenger_dist']       =  $_DB_table_prefix . 'messenger_dist';

// grab some data we'll need over and over....

$sql = "SELECT uid,username FROM {$_TABLES['users']}";
$result = DB_query($sql);
while ( $userRow = DB_fetchArray($result) ) {
    $userInfo[$userRow['uid']] = $userRow['username'];
}

$sql = "SELECT * FROM {$_TABLES['messenger_msg']}";

$result = DB_query($sql);
$index = 0;
while ($msg = DB_fetchArray($result) ) {
    $pm[$index]['msg_id']          = $msg['id'];
    $pm[$index]['parent_id']       = $msg['reply_msgid'];
    $pm[$index]['author_uid']      = $msg['source_uid'];
    $pm[$index]['author_name']     = $userInfo[$msg['source_uid']];
    $pm[$index]['message_time']    = $msg['datetime'];
    $pm[$index]['message_subject'] = $msg['subject'];
    $tmessage = str_replace(array('<br />','<br>'),'',$msg['message']);
    $pm[$index]['message_text']    = $tmessage;
    $pm[$index]['to_address']      = '';

    // grab the dist records

    $subCount = 0;
    $distResult = DB_query("SELECT * FROM {$_TABLES['messenger_dist']} WHERE msg_id=".(int) $msg['id']);
    while ($distRow = DB_fetchArray($distResult) ) {
        $pm[$index]['dist'][$subCount]['user_id'] = $distRow['target_uid'];
        $pm[$index]['to_address'] .= $userInfo[$distRow['target_uid']].',';
        $pm[$index]['username'] .= $userInfo[$distRow['target_uid']];
        if ( $distRow['read_date'] > 0 ) {
            $pm[$index]['dist'][$subCount]['pm_unread'] = 0;
        } else {
            $pm[$index]['dist'][$subCount]['pm_unread'] = 1;
        }
        $pm[$index]['dist'][$subCount]['author_uid'] = $msg['source_uid'];
        $pm[$index]['dist'][$subCount]['folder_name'] = 'inbox';
        $subCount++;
    }
    // create sender record....
    $pm[$index]['dist'][$subCount]['user_id'] = $msg['source_uid'];
    $pm[$index]['dist'][$subCount]['pm_unread'] = 1;
    $pm[$index]['dist'][$subCount]['author_uid'] = $msg['source_uid'];
    $pm[$index]['dist'][$subCount]['folder_name'] = 'sent';

    $index++;
}
// we should now have them in memory....

foreach ( $pm AS $pmRec ) {
    $sql  = "INSERT INTO {$_TABLES['pm_msg']} ";
    $sql .= "(parent_id,author_uid,author_name,author_ip,message_time,message_subject,message_text,to_address,bcc_address) ";
    $sql .= "VALUES( ".'0'.","
                      .(int) $pmRec['author_uid'].",'"
                      .DB_escapeString($pmRec['author_name'])."','',"
                      .(int) $pmRec['message_time'].",'"
                      .DB_escapeString($pmRec['message_subject'])."','"
                      .DB_escapeString($pmRec['message_text'])."','"
                      .DB_escapeString($pmRec['to_address'])."','')";

    DB_query($sql);

    $lastmsg_id = DB_insertID();

    foreach($pmRec['dist'] AS $dist) {
        $sql  = "INSERT INTO {$_TABLES['pm_dist']} ";
        $sql .= "(msg_id,user_id,username,author_uid,pm_unread,folder_name) ";
        $sql .= "VALUES ('".DB_escapeString($lastmsg_id)."','"
                         .(int) $dist['user_id']."',"
                         ."'".DB_escapeString($dist['username'])."',"
                         .(int) $dist['author_uid'].","
                         .(int) $dist['pm_unread'].","
                         ."'".DB_escapeString($dist['folder_name'])."'".")";
        DB_query($sql);
    }
}

$display = '';
$display .= COM_siteHeader('menu',$LANG_PM00['title'],$styleLink);
$display .= 'Conversion complete!';
$display .= COM_siteFooter();
echo $display;

?>