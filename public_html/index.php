<?php
// +--------------------------------------------------------------------------+
// | PM Plugin for glFusion CMS                                               |
// +--------------------------------------------------------------------------+
// | index.php                                                                |
// |                                                                          |
// | PM plugin main index page                                                |
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

require_once '../lib-common.php';

if (!in_array('pm', $_PLUGINS)) {
    COM_404();
    exit;
}

USES_lib_admin();

require_once $_CONF['path'].'plugins/pm/include/lib-pm.php';

function _pm_getListField_mailbox($fieldname, $fieldvalue, $A, $icon_arr)
{
    global $_CONF, $_USER, $folder;

    $retval = '';

    $dt = new Date('now',$_USER['tzid']);

    switch ($fieldname) {
        case 'message_time' :
            $dt->setTimestamp($fieldvalue);
            $retval = $dt->format($dt->getUserFormat(),true);
            if ( $A['pm_unread'] == 1 ) {
                $retval = '<strong>'.$retval.'</strong>';
            }
            break;
        case 'msg_id' :
            $retval = '<input class="msg_checkbox" type="checkbox" name="marked_msg_id[]" value="'.$fieldvalue.'">';
            break;
        case 'message_subject' :
        case 'author_name' :
            $retval = '<a href="'.$_CONF['site_url'].'/pm/view.php?msgid='.$A['msg_id'].'&amp;folder='.$folder.'">'.$fieldvalue.'</a>';
            if ( $A['pm_unread'] == 1 ) {
                $retval = '<strong>'.$retval.'</strong>';
            }
            break;
        default :
            $retval = $fieldvalue;
            break;
    }

    return $retval;
}

function PM_processMarked()
{
    global $_CONF, $_PM_CONF, $_TABLES, $_USER;

    $markedMessages = array();

    $validOperations = array('delete_marked','archive_marked');

    $operation = isset($_POST['mark_option']) ? $_POST['mark_option'] : '';
    if ( !in_array($operation,$validOperations) ) {
        return;
    }
    $current_folder = COM_applyFilter($_POST['current_folder']);
    if ( !in_array($current_folder,array('inbox','sent','archive','outbox') ) ) {
        return;
    }

    $markedMessages = isset($_POST['marked_msg_id']) ? $_POST['marked_msg_id'] : '';
    if (is_array($markedMessages) ) {
        foreach( $markedMessages AS $msg_id ) {
            if ( $operation == 'delete_marked' ) {
                if ( $current_folder == 'outbox' ) {
                    // we know no one has read it yet or it wouldn't be in our out box
                    DB_query("DELETE FROM {$_TABLES['pm_dist']} WHERE (msg_id = ".(int) $msg_id.")");
                    DB_query("DELETE FROM {$_TABLES['pm_msg']} WHERE (msg_id = ".(int) $msg_id.")");
                } else {
                    // must be in our in, sent, or archive folder
                    DB_query("DELETE FROM {$_TABLES['pm_dist']} WHERE msg_id = ".(int) $msg_id." AND user_id=".(int) $_USER['uid']." AND folder_name='".DB_escapeString($current_folder)."'");
                    $msgCount = DB_count($_TABLES['pm_dist'],'msg_id',(int) $msg_id);
                    if ( $msgCount == 0 ) {
                        DB_query("DELETE FROM {$_TABLES['pm_msg']} WHERE (msg_id = ".(int) $msg_id.")");
                    }
                }
            }
            if ( $operation == 'archive_marked' ) {
                DB_query("UPDATE {$_TABLES['pm_dist']} SET folder_name='archive' WHERE msg_id=".(int) $msg_id." AND user_id=".(int) $_USER['uid']." AND folder_name='".DB_escapeString($current_folder)."'");
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

if ( isset($_POST['mark_option']) ) {
    $msg_header = PM_processMarked();
}

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

$retval = '';

$T = new Template($_CONF['path'] . 'plugins/pm/templates/');
$T->set_file (array (
    'list'      =>  'pm_box.thtml',
));

// build folder selection list
$folderSelect = '<select id="folder" name="folder">'.LB;
$folderSelect .= '<option'. ($folder == 'inbox'   ? ' selected="selected" ' : ' ') . 'value="inbox">'   . $LANG_PM00['inbox']   . '</option>'.LB;
$folderSelect .= '<option'. ($folder == 'outbox'  ? ' selected="selected" ' : ' ') . 'value="outbox">'  . $LANG_PM00['outbox']  . '</option>'.LB;
$folderSelect .= '<option'. ($folder == 'sent'    ? ' selected="selected" ' : ' ') . 'value="sent">'    . $LANG_PM00['sent']    . '</option>'.LB;
$folderSelect .= '<option'. ($folder == 'archive' ? ' selected="selected" ' : ' ') . 'value="archive">' . $LANG_PM00['archive'] . '</option>'.LB;
$folderSelect .= '</select>'.LB;


switch ( $folder ) {
    case 'inbox' :
        $sql  = "SELECT * ";
        $sql .= "FROM {$_TABLES['pm_msg']} msg " ;
        $sql .= "LEFT JOIN {$_TABLES['pm_dist']} dist ON msg.msg_id=dist.msg_id ";
        $sql .= "WHERE dist.user_id=".$_USER['uid']." AND dist.folder_name='inbox' ";
        break;
    case 'sent' :
        $sql  = "SELECT * ";
        $sql .= "FROM {$_TABLES['pm_msg']} msg ";
        $sql .= "LEFT JOIN {$_TABLES['pm_dist']} dist ON msg.msg_id=dist.msg_id ";
        $sql .= "WHERE msg.author_uid=".(int) $_USER['uid']." AND dist.folder_name='sent' ";
        break;
    case 'archive' :
        $sql  = "SELECT * ";
        $sql .= "FROM {$_TABLES['pm_msg']} msg ";
        $sql .= "LEFT JOIN {$_TABLES['pm_dist']} dist ON msg.msg_id=dist.msg_id ";
        $sql .= "WHERE dist.user_id=".(int) $_USER['uid']." AND dist.folder_name='archive' ";
        break;
    case 'outbox' :
        $sql  = "SELECT * ";
        $sql .= "FROM {$_TABLES['pm_msg']} msg ";
        $sql .= "LEFT JOIN {$_TABLES['pm_dist']} dist ON msg.msg_id=dist.msg_id ";
        $sql .= "WHERE msg.author_uid=".(int) $_USER['uid']." AND dist.folder_name='outbox' ";
        break;
}


$T->set_var(array(
    'compose_link'  => $_CONF['site_url'] . '/pm/compose.php?mode=new',
    'lang_compose'  => $LANG_PM00['compose'],
    'ab_link'       => $_CONF['site_url'].'/pm/friends.php',
    'lang_ab'       => $LANG_PM00['address_book'],
));

$menu_arr = array (
    array('url' => $_CONF['site_url'] . '/pm/compose.php?mode=new',
          'text' => $LANG_PM00['compose']),
    array('url' => $_CONF['site_url'].'/pm/friends.php',
          'text' => $LANG_PM00['address_book']),
);

$msg_menu = ADMIN_createMenu($menu_arr,
                $LANG_PM00['folder']. ' ' . $folderSelect . '<input type="submit" value="'.$LANG_PM00['go'].'">',
                $_CONF['site_url'].'/pm/images/pm48.png');

$header_arr = array(      # display 'text' and use table field 'field'
    array('text' => '<input type="checkbox" id="selectall">', 'field' => 'msg_id', 'sort' => false, 'align' => 'center'),
    array('text' => $LANG_PM00['from'], 'field' => 'author_name', 'sort' => true, 'align' => 'left'),
    array('text' => $LANG_PM00['subject'], 'field' => 'message_subject', 'sort' => true, 'align' => 'left'),
    array('text' => $LANG_PM00['date'],    'field' => 'message_time', 'sort'=> true, 'align' => 'right'),
);
$defsort_arr = array('field' => 'message_time', 'direction' => 'desc');

$text_arr = array(
    'has_extras' => true,
    'form_url'   => $_CONF['site_url'] . '/pm/index.php'
);

$oselect = '
    <div style="margin-top:10px;">
    <input type="hidden" name="current_folder" value="'.$folder.'">
	<select name="mark_option" onchange="this.form.submit();">
		<option selected="selected" disalbed="disabled" value="">'.$LANG_PM00['options'].'</option>
		<option value="delete_marked" onclick="return confirm(\''.$LANG_PM00['delete_confirm'].'\');">'.$LANG_PM00['delete_marked'].'</option>
		<option value="archive_marked">'.$LANG_PM00['archive_marked'].'</option>
	</select></div>';

$form_arr = array(
    'bottom' => $oselect
);

$query_arr = array(
    'table' => $_TABLES['pm_msg'],
    'sql' => $sql,
    'query_fields' => array('message_subject'),
    'default_filter' => ''
);

$msg_list =  ADMIN_list('mailbox','_pm_getListField_mailbox',
                      $header_arr, $text_arr, $query_arr, $defsort_arr,'','','',$form_arr);

$T->set_var(array(
    'pm_home'       => $LANG_PM00['pm_index'],
    'folder'        => $folder,
    'folder_name'   => $LANG_PM00[$folder],
    'newpost_link'  => $LANG_PM00['compose_msg'],
    'lang_inbox'    => $LANG_PM00['inbox'],
    'lang_sent'     => $LANG_PM00['sent'],
    'lang_archive'  => $LANG_PM00['archive'],
    'lang_outbox'   => $LANG_PM00['outbox'],
    'folder_select' => $folderSelect,
    'current_folder'  => $folder,
    'not_archive'   => ($folder == 'archive' || $folder == 'outbox' ? '' : 'archive'),
    'message_menu' => $msg_menu,
    'message_list' => $msg_list,
));


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