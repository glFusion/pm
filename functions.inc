<?php
/**
 * API functions for the Private Message plugin.
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
if (!defined ('GVERSION')) {
    die ('This file can not be used on its own!');
}

spl_autoload_register(function ($class)
{
    // project-specific namespace prefix
    $prefix = 'PM\\';

    // does the class use the namespace prefix?
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        // no, move to the next registered autoloader
        return;
    }

    // base directory for the namespace prefix
    $base_dir = __DIR__ . '/classes/';

    // get the relative class name
    $relative_class = substr($class, $len);

    // replace the namespace prefix with the base directory, replace namespace
    // separators with directory separators in the relative class name, append
    // with .php
    $file = $base_dir . str_replace('\\', '/', $relative_class) . '.class.php';

    // if the file exists, require it
    if (file_exists($file)) {
        require $file;
    }
});
use glFusion\Database\Database;

// Initialize the configuration
require_once $_CONF['path_system'] . 'classes/config.class.php';
$pm_config = config::get_instance();
$_PM_CONF = $pm_config->get_config('pm');
require_once __DIR__ . '/pm.php';

define('PM_URL', $_CONF['site_url'] . '/pm');

$langfile = $_CONF['path'] . 'plugins/pm/language/' . $_CONF['language'] . '.php';
if (file_exists ($langfile)) {
    require_once $langfile;
} else {
    require_once $_CONF['path'].'plugins/pm/language/english_utf-8.php';
}

function plugin_chkVersion_pm()
{
    global $_PM_CONF;

    return $_PM_CONF['pi_version'];
}

function plugin_geticon_pm()
{
    global $_CONF;

    $retval = $_CONF['site_url'].'/pm/images/pm48.png';
    return $retval;
}

function plugin_upgrade_pm()
{
    if (@file_exists(__DIR__ . '/upgrade.php')) {
        require_once __DIR__ . '/upgrade.php';
        return pm_upgrade();
    } else {
        return false;
    }
}

function plugin_getuseroption_pm()
{
    global $_CONF, $_PM_CONF, $_TABLES, $_USER, $LANG_PM00;

    if (!COM_isAnonUser() && SEC_hasRights('pm.user')) {
        $db = Database::getInstance();
        $unread = $db->getCount(
            $_TABLES['pm_dist'],
            array('user_id', 'folder_name', 'pm_unread'),
            array($_USER['uid'], 'inbox', 1),
            array(Database::INTEGER, Database::STRING, Database::INTEGER)
        );
        return array($LANG_PM00['user_menu'], $_CONF['site_url'] . '/pm/index.php', $unread);
    }
}

function plugin_autotags_pm($op, $content = '', $autotag = '')
{
    if ($op == 'tagusage') {
        $tagUsage = array(
            array('namespace' => 'pm','usage' => 'message'),
            array('namespace' => 'pm','usage' => 'subject')
        );
        return $tagUsage;
    }
    return;
}

function plugin_user_create_pm($uid)
{
    $User = PM\User::getInstance($uid);
    $User->save();
}

function plugin_user_delete_pm($uid)
{
    global $_TABLES;

    $db = Database::getInstance();
    $qb = $db->conn->createQueryBuilder();
    try {
        $data = $qb->select('*')
           ->from($_TABLES['pm_msg'], 'msg')
           ->leftJoin('msg', $_TABLES['pm_dist'], 'dist', 'msg.msg_id=dist.msg_id')
           ->where('dist.user_id = ?')
           ->setParameter(0, $uid, Database::INTEGER)
           ->execute()
           ->fetchAll(Database::ASSOCIATIVE);
    } catch (\Throwable $e) {
        Log::write('system', Log::ERROR, __FUNCTION__ . ': error ' . $e->getMessage());
        $data = array();
    }
    foreach ($data as $msg) {
        $msg_id = $msg['msg_id'];
        $db->conn->delete(
            $_TABLES['pm_dist'],
            array('msg_id', 'user_id'),
            array($msg_id, $uid),
            array(Database::INTEGER, Database::INTEGER)
        );
        if ($db->getCount(
            $_TABLES['pm_dist'],
            array('msg_id'),
            array($msg_id),
            array(Database::INTEGER)
        ) == 0 ) {
            $db->conn->delete(
                $_TABLES['pm_msg'],
                array('msg_id'),
                array($msg_id),
                array(Database::INTEGER)
            );
        }
    }
    $db->conn->delete(
        $_TABLES['pm_friends'],
        array('uid'),
        array($uid),
        array(Database::INTEGER)
    );
}

function plugin_templatesetvars_pm($templatename, &$template)
{
    global $_CONF, $_USER, $_TABLES, $LANG_PM00;

    if ( COM_isAnonUser() ) return;

    if ( $templatename == 'header' && SEC_hasRights('pm.user')) {
        $db = Database::getInstance();
        $unread = $db->getCount(
            $_TABLES['pm_dist'],
            array('user_id', 'folder_name', 'pm_unread'),
            array($_USER['uid'], 'inbox', 1),
            array(Database::INTEGER, Database::STRING, Database::INTEGER)
        );
        if ( $unread > 0 ) {
            $msg = $template->get_var('welcome_msg');
            $msg .= '</a> - ' . sprintf($LANG_PM00['newpm'], $unread );
            $template->set_var('welcome_msg',$msg);
        }
    }
}


function plugin_profileedit_pm($uid,$panel,$fieldset)
{
    global $_CONF, $_TABLES, $_PM_CONF, $_USER, $LANG_PM_NOTIFY;

    $retval = '';

    $User = PM\User::getInstance($uid);
    switch ($panel) {
        case 'privacy' :
            switch ($fieldset) {
            case 'privacy' :
                $T = new Template(pm_get_template_path());
                $T->set_file (array ('privacy'=>'profile_privacy.thtml'));
                $T->set_var(array(
                    'lang_pmnotify'     => $LANG_PM_NOTIFY['pm_notify'],
                    'pmnotify_checked'  => $User->getNotify() ? ' checked="checked"' : '',
                    'lang_pmblock'      => $LANG_PM_NOTIFY['pm_block'],
                    'pmblock_checked'   => $User->getBlock() ? ' checked="checked"' : '',
                ));
                $T->parse ('output', 'privacy');
                $retval .= $T->finish ($T->get_var('output'));
                break;

            default :
                $retval = '';
                break;
            }
        break;
    }
    return $retval;
}

function plugin_profilesave_pm( $uid = 0 )
{
    global $_CONF, $_TABLES, $_PM_CONF, $_USER;

    if ( COM_isAnonUser() ) {
        return;
    }

    if ( $uid == 0 ) $uid = $_USER['uid'];

    $User = PM\User::getInstance($uid);

    if ( isset($_POST['pmnotify']) ) {
        $User->withNotify(1);
    } else {
        $User->withNotify(0);
    }
    if ( isset($_POST['pmblock']) ) {
        $User->withBlock(1);
    } else {
        $User->withBlock(0);
    }

    $User->save();
}

/*
 * Return a link to the help element for this configuration option
 *
 */

function plugin_getconfigelementhelp_pm($element)
{
    return array('http://www.glfusion.org/wiki/pm:start',2);
}

function plugin_profileicondisplay_pm($uid)
{
    global $_CONF, $_USER, $LANG_PM00;

    $retval = array();

    if ( $uid > 1 && !COM_isAnonUser() && SEC_hasRights('pm.user') ) {

        $retval = array(
            'url' => $_CONF['site_url'].'/pm/compose.php?mode=new&amp;uid='.(int)$uid,
            'text' => $LANG_PM00['title'],
            'icon' => $_CONF['site_url'].'/pm/images/pm-profile.png',
        );
    }
    return $retval;
}


/**
 * Get additional code to place in the header.
 * This is used only to register the notification method to glFusion.
 *
 * @return  string      Header code (not used)
 */
function plugin_getheadercode_pm() : string
{
    global $_PM_CONF;

    if (GVERSION > '2.0.2') {
        glFusion\Notifier::Register($_PM_CONF['pi_name'], 'PM\\Notifier', $_PM_CONF['pi_display_name']);
    }
    return '';
}


/**
 * Get additional CSS into the site header.
 * Checks first for layout-specific version, gets the plugin default if none.
 *
 * @return  array   List of paths to CSS files
 */
function plugin_getheadercss_pm()
{
    global $_CONF;

    $styles = array();
    $pi_css = __DIR__ . '/css/';
    $layout_css = "{$_CONF['path_layout']}plugins/pm/";

    $files = array('style.css');

    foreach ($files as $file) {
        $styles[] = $pi_css . $file;
        if (@file_exists($pi_css . 'custom/' . $file)) {
            $styles[] = $pi_css . 'custom/' . $file;
        }
        if (@file_exists($layout_css . $file)) {
            $styles[] = $layout_css . $file;
        }
    }
    return $styles;
}


function pm_get_template_path()
{
    global $_CONF, $_SYSTEM;

    if ( isset($_SYSTEM['framework']) && $_SYSTEM['framework'] == 'uikit') {
        $template_path = $_CONF['path'] . 'plugins/pm/templates/';
    } else {
        $template_path = $_CONF['path'] . 'plugins/pm/legacy/';
    }
    return $template_path;
}


/**
 * Send a private message, either by a system event or from a user.
 *
 * Parameter array consists of:
 *   - to : (int)Single or array of user IDs
 *   - bcc : (int)Single or array of user IDs
 *   - subject : (string)
 *   - message : (string)
 *   - author_id : (int) Sending user ID, zero for a system message
 * @param   array   $A  Array of message properties
 */
function plugin_sendmessage_pm(array $A)
{
    $PM = new PM\Message;
    $PM->withSubject($A['subject'])
        ->withMessage($A['message']);

    if (isset($A['to'])) {
        $toList = $A['to'];
        if (!is_array($toList)) {
            $toList = array($toList);
        }
        $PM->withToUsers($toList);
    }

    if (isset($A['bcc'])) {
        $bccList = $A['bcc'];
        if (!is_array($bccList)) {
            $bccList = array($bccList);
        }
        $PM->withBccUsers($bccList);
    }

    if (isset($A['author_id'])) {
        $PM->withAuthorUid((int)$A['author_id']);
    }
    $PM->send();
    return;
}


/**
 * Check that the current user has access to use the PM plugin.
 * Redirects to the login page or home page if access is denied.
 *
 * @return  void
 */
function PM_checkAccess() : void
{
    global $_CONF;

    if ( COM_isAnonUser()  )  {
        $display = COM_siteHeader();
        $display .= SEC_loginRequiredForm();
        $display .= COM_siteFooter();
        echo $display;
        exit;
    }

    if ( !SEC_hasRights('pm.user') ) {
        echo COM_refresh($_CONF['site_url']);
        exit;
    }
}

function PM_siteHeader($title='', $meta='')
{
    global $_PM_CONF;

    $retval = '';
    $retval .= COM_siteHeader('menu',$title,$meta);
    return $retval;
}

function PM_siteFooter() {
    global $_CONF, $_PM_CONF;

    $retval = '';
    $retval .= COM_siteFooter();
    return $retval;
}

function PM_alertMessage( $msgText = '' )
{
    global $_CONF, $_PM_CONF, $LANG_PM00;

    $display = COM_siteHeader('menu',$LANG_PM00['title']);

    $T = new Template(pm_get_template_path());
    $T->set_file (array ('message'=>'pm_alertmsg.thtml'));
    $T->set_var(array(
        'message_title' =>  $LANG_PM00['title'] . $LANG_PM00['error'],
        'message_text'  =>  $msgText,
    ));
    $T->parse ('output', 'message');
    $display .= $T->finish ($T->get_var('output'));

    $display .= COM_siteFooter();
    echo $display;
    exit;
}

