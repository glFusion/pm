<?php
/**
 * Main index view for the Private Message plugin.
 *
 * @author      Mark R. Evans <mark AT glfusion DOT org>
 * @copyright   Copyright (c) 2009-2016 Mark R. Evans <mark AT glfusion DOT org>
 * @package     pm
 * @version     v3.0.0
 * @since       v3.0.0
 * @license     http://opensource.org/licenses/gpl-2.0.php
 *              GNU Public License v2 or later
 * @filesource
 */
require_once '../lib-common.php';

if (!in_array('pm', $_PLUGINS)) {
    COM_404();
    exit;
}

PM_checkAccess();
$expected = array(
    // Actions
    'delete', 'delete_marked', 'archive_marked', 'block_marked',
    // Views
    'mailbox',
);
$action = 'mailbox';    // default view
foreach($expected as $provided) {
    if (isset($_POST[$provided])) {
        $action = $provided;
        $actionval = $_POST[$provided];
        break;
    } elseif (isset($_GET[$provided])) {
        $action = $provided;
        $actionval = $_GET[$provided];
        break;
    }
}

/*
 * Start of main code
 */

$display = '';
$msg_header = '';

if ( isset($_POST['delete_marked_x']) ) {
    $msg_header = PM_processMarked('delete_marked');
}

if ( isset($_POST['archive_marked_x']) ) {
    $msg_header = PM_processMarked('archive_marked');
}

$self = $_CONF['site_url'] . '/pm/index.php';
$folder = PM\Folder::fromParams();
switch ($action) {
case 'delete_marked':
    PM\Message::delete($_POST['marked_msg_id'], $folder);
    echo COM_refresh($self . '?folder=' . $folder);
    break;
case 'archive_marked':
    PM\Message::archive($_POST['marked_msg_id'], $folder);
    echo COM_refresh($self . '?folder=' . $folder);
    break;
case 'block_marked':
    PM\Message::block($_POST['marked_msg_id'], $folder);
    echo COM_refresh($self . '?folder=' . $folder);
    break;
case 'delete':
    PM\Message::delete($actionval, $folder);
    echo COM_refresh($self . '?folder=' . $folder);
    break;
case 'archive':
    PM\Message::archive($actionval, $folder);
    echo COM_refresh($self);
    break;
case 'mailbox':
default:
    break;
}

$content = '';

$T = new Template(pm_get_template_path());
$T->set_file(array(
    'list' => 'pm_box.thtml',
    'menu' => 'menu.thtml',
));

// build folder selection list
$MsgList = new PM\Views\Mailbox;
$MsgList->withFolder($folder);
$T->set_var(array(
    'compose_link'  => $_CONF['site_url'] . '/pm/compose.php?new',
    'lang_compose'  => $LANG_PM00['compose'],
    'ab_link'       => $_CONF['site_url'].'/pm/friends.php',
    'lang_ab'       => $LANG_PM00['address_book'],
    'pm_home'       => $LANG_PM00['pm_index'],
    'folder'        => $folder,
    'folder_name'   => $LANG_PM00[$folder],
    'newpost_link'  => $LANG_PM00['compose_msg'],
    'lang_inbox'    => $LANG_PM00['inbox'],
    'lang_sent'     => $LANG_PM00['sent'],
    'lang_archive'  => $LANG_PM00['archive'],
    'lang_outbox'   => $LANG_PM00['outbox'],
    'current_folder'  => $folder,
    'not_archive'   => ($folder == 'archive' || $folder == 'outbox' ? '' : 'archive'),
    'message_list' => $MsgList->render(),
    'on_mailbox' => true,
));
$T->parse('menu', 'menu');
$T->parse('output', 'list');
$content .= $T->finish ($T->get_var('output'));

$display = PM_siteHeader($LANG_PM00['title']);
if ( isset($_GET['msg']) ) {
    $msg_header = COM_applyFilter ($_GET['msg'], true);
}
if ( $msg_header > 0 ) {
    $display .= COM_showMessage ($msg_header, 'pm');
}
$display .= $content;
$display .= PM_siteFooter();
echo $display;

