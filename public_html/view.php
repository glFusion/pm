<?php
/**
 * View a private message.
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

if ( isset($_GET['msgid']) ) {
    $msg_id = COM_applyFilter($_GET['msgid'],true);
} elseif (isset($_POST['msgid']) ) {
    $msg_id = COM_applyFIlter($_POST['msgid'],true);
} else {
    $msg_id = 0;
}

$folder = PM\Folder::fromParams();

if ( isset($_GET['p']) ) {
    $page = COM_applyFilter($_GET['p'],true);
} elseif (isset($_POST['page']) ) {
    $page = COM_applyFilter($_POST['p'],true);
} else {
    $page = 1;
}

if ( $msg_id == 0 ) {
    PM_alertMessage( $LANG_PM_ERROR['invalid_msg_id'] );
}

$PM = PM\Message::getInstance($msg_id, $folder);
$display = PM_siteHeader($LANG_PM00['title'],);
if ( isset($_GET['msg']) ) {
    $msg_header = COM_applyFilter ($_GET['msg'], true);
} else {
    $msg_header = 0;
}
if ( $msg_header > 0 ) {
    $display .= COM_showMessage ($msg_header, 'pm');
}
$display .= $PM->render($page);
$display .= PM_siteFooter();
echo $display;
exit;

