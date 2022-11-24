<?php
/**
 * Compose a new private message.
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

USES_lib_user();
USES_lib_bbcode();

/*
 * Start of main code
 */

$display = '';

function PM_msgSend( )
{
    global $_CONF, $_PM_CONF, $_TABLES, $_USER, $LANG_PM_ERROR;

    if ( !SEC_checkToken() ) {
        $errArray[] = 'Security Token Failure';
        return array(false,$errArray);
    }

    $toList = $_POST['username_list'];
    $toArray = explode(',',$toList);
    $PM = new PM\Message;
    $PM->withToUserNames($toArray)
       ->withSubject($_POST['message_subject'])
       ->withComment($_POST['message_text'])
       ->withParentId($_POST['parent_id']);
    $status = $PM->send();

    COM_updateSpeedlimit ('pm');
    CACHE_remove_instance('stmenu');
    return array($status, $PM->getErrors());
}

$expected = array(
    // views
    'new', 'reply', 'edit', 'quote', 'send',
    // actions
    'preview', 'cancel', 'mode',
);
$action = 'new';    // default view
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

$message = '';
$body = '';
switch ($action) {
case 'new' :
    COM_clearSpeedlimit ($_PM_CONF['post_speedlimit'], 'pm');
    $last = COM_checkSpeedlimit ('pm');
    if ($last > 0) {
        echo COM_refresh($_CONF['site_url'].'/pm/index.php?msg=4');
        exit;
    }
    $Msg = new PM\Message;
    if ( isset($_GET['uid']) ) {
        $Msg->withToUser((int)$_GET['uid']);
    } elseif (isset($_GET['username'])) {
        $to_name = COM_applyFilter($_GET['username']);
        $Msg->withToUserNames(array($to_name));
    }
    $Editor = new PM\Views\Editor;
    $Editor->withMessage($Msg);
    $body = $Editor->render();
    break;
/*case 'edit' :
    $Editor = new PM\Views\Editor;
    $Editor->withMessage($Msg);
    $body = $Editor->render();
    break;*/
case 'quote' :
case 'reply' :
    COM_clearSpeedlimit ($_PM_CONF['post_speedlimit'], 'pm');
    $reply_msgid = COM_applyFilter($_GET['msgid'],true);
    $last = COM_checkSpeedlimit ('pm');
    if ($last > 0) {
        echo COM_refresh($_CONF['site_url'].'/pm/view.php?msgid='.(int) $reply_msgid.'&amp;msg=4');
        exit;
    }
    $Msg = new PM\Message;
    $Parent = PM\Message::getInstance($reply_msgid);
    if ($action == 'quote') {
        $message = '[quote][u]Quote by: '. $Parent->getAuthorName() .
            '[/u][p]' . $Parent->getComment() . '[/p][/quote]';
    }
    $subject = $Parent->getSubject();
    if (substr($subject, 0, 3) != 'Re:') {
        $subject = 'Re: ' . $subject;
    }
    $parent_id = $Parent->getParentId();
    if ($parent_id == 0) {
        $parent_id = $Parent->getMsgId();
    }
    $Msg->withSubject($subject)
        ->withComment($message)
        ->withParentId($parent_id)
        ->withToUserNames(array($Parent->getAuthorName()));
    $Editor = new PM\Views\Editor;
    $Editor->withMessage($Msg);
    $body = $Editor->render();
    $body .= PM\Views\History::render($reply_msgid,true);
    break;
case 'preview' :
    $preview_text = PM\Views\Preview::render($_POST);
    $Editor = new PM\Views\Editor;
    $body = $Editor->fromPost($_POST)->render();
    $body .= $preview_text;
    $body .= PM\Views\History::render($_POST['parent_id'], true);
    break;
case 'send' :
    COM_clearSpeedlimit ($_PM_CONF['post_speedlimit'], 'pm');
    $last = COM_checkSpeedlimit ('pm');
    if ($last > 0) {
        echo COM_refresh($_CONF['site_url'].'/pm/index.php?msg=4');
        exit;
    }
    /*if (!SEC_checkToken()) {
        $errArray[] = 'Security Token Failure';
        return array(false,$errArray);
    }*/

    $toList = $_POST['username_list'];
    $toArray = explode(',',$toList);
    $PM = new PM\Message;
    $PM->withToUserNames($toArray)
       ->withSubject($_POST['message_subject'])
       ->withComment($_POST['message_text'])
       ->withParentId($_POST['parent_id']);
    $status = $PM->send();
    if (!$status) {
        $Editor = new PM\Views\Editor;
        $Editor->withMessage($PM);
        $body = $Editor->render();
    } else {
        COM_updateSpeedlimit ('pm');
        CACHE_remove_instance('stmenu');
        echo COM_refresh($_CONF['site_url'].'/pm/index.php?msg=1');
        exit;
    }
    break;
}

$display = PM_siteHeader($LANG_PM00['title']);
$display .= $body;
$display .= PM_siteFooter();
echo $display;
