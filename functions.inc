<?php
// +--------------------------------------------------------------------------+
// | PM Plugin for glFusion CMS                                               |
// +--------------------------------------------------------------------------+
// | functions.inc                                                            |
// |                                                                          |
// | Implement the glFusion Plugin API                                        |
// +--------------------------------------------------------------------------+
// | Copyright (C) 2009-2017 by the following authors:                        |
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

if (!defined ('GVERSION')) {
    die ('This file can not be used on its own!');
}

// Initialize the configuration

require_once $_CONF['path_system'] . 'classes/config.class.php';
$pm_config = config::get_instance();
$_PM_CONF = $pm_config->get_config('pm');
require_once $_CONF['path'].'plugins/pm/pm.php';

$langfile = $_CONF['path'] . 'plugins/pm/language/' . $_CONF['language'] . '.php';
if (file_exists ($langfile)) {
    require_once $langfile;
} else {
    require_once $_CONF['path'].'plugins/pm/language/english.php';
}

if (version_compare(GVERSION,'2.0.0','lt')) {
    if (isset($LANG_configSelect['pm']) && !isset($LANG_configselects['pm'])) {
        foreach ($LANG_configSelect['pm'] AS $cfgItem) {
            $LANG_configselects['pm'][] = array_flip($cfgItem);
        }
    }
}

function plugin_chkVersion_pm() {
    global $_PM_CONF;

    return $_PM_CONF['pi_version'];
}

function plugin_geticon_pm() {
    global $_CONF;

    $retval = $_CONF['site_url'].'/pm/images/pm48.png';
    return $retval;
}

function plugin_upgrade_pm(){
    global $_CONF, $_TABLES, $_PM_CONF, $_DB_table_prefix;

    if ( @file_exists($_CONF['path'].'plugins/pm/upgrade.php') ) {
        require_once $_CONF['path'].'plugins/pm/upgrade.php';
        return pm_upgrade();
    } else {
        COM_errorLog("PM plugin upgrade function not implemented");
        return 3001;
    }
}

function plugin_getuseroption_pm() {
    global $_CONF, $_PM_CONF, $_TABLES, $_USER, $LANG_PM00;

    if ( !COM_isAnonUser() && SEC_hasRights('pm.user')) {
        $unread = DB_count($_TABLES['pm_dist'],array('user_id','folder_name','pm_unread'),array((int) $_USER['uid'],'inbox',1));
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
    global $_TABLES;

    DB_save($_TABLES['pm_userprefs'],'uid,notify',"'".(int)$uid."',1");
}

function plugin_user_delete_pm($uid)
{
    global $_TABLES;

    $sql  = "SELECT * FROM {$_TABLES['pm_msg']} msg ";
    $sql .= "LEFT JOIN {$_TABLES['pm_dist']} dist ON msg.msg_id=dist.msg_id ";
    $sql .= "WHERE dist.user_id=".(int) $uid;

    $result = DB_query($sql);
    while ($msg = DB_fetchArray($result) ) {
        $msg_id = $msg['msg_id'];
        DB_delete($_TABLES['pm_dist'],array('msg_id','user_id'),array((int) $msg_id,(int) $uid));
        if ( DB_count($_TABLES['pm_dist'],'msg_id',(int) $msg_id) == 0 ) {
            DB_delete($_TABLES['pm_msg'],'msg_id',(int) $msg_id);
        }
    }
    DB_query("DELETE FROM {$_TABLES['pm_friends']} WHERE uid=".(int) $uid,1);
}

function plugin_templatesetvars_pm($templatename, &$template)
{
    global $_CONF, $_USER, $_TABLES, $LANG_PM00;

    if ( COM_isAnonUser() ) return;

    if ( !COM_isAnonUser() && $templatename == 'header' && SEC_hasRights('pm.user')) {
        $unread = DB_count($_TABLES['pm_dist'],array('user_id','folder_name','pm_unread'),array((int) $_USER['uid'],'inbox',1));
        if ( $unread > 0 ) {
            $msg = $template->get_var('welcome_msg');
            $msg .= '</a> - ' . sprintf($LANG_PM00['newpm'], $unread );
            $template->set_var('welcome_msg',$msg);
        }
    }
}

function plugin_userpreferencesedit_pm($panel,$uid)
{
    global $_CONF, $_TABLES, $_PM_CONF, $LANG_PM_NOTIFY;

    $retval = '';

    switch ($panel) {
        case 'privacy' :
            $retval =  '<tr><td><label>'.$LANG_PM_NOTIFY['pm_notify'].'</label></td><td><input type="checkbox"></td></tr>';
            $retval .= '<tr><td><label>'.$LANG_PM_NOTIFY['pm_block'].'</label></td><td><input type="checkbox"></td></tr>';
            break;
    }
    return $retval;
}

function plugin_profileedit_pm($uid,$panel,$fieldset)
{
    global $_CONF, $_TABLES, $_PM_CONF, $_USER, $LANG_PM_NOTIFY;

    $retval = '';

    switch ($panel) {
        case 'privacy' :
            switch ($fieldset) {
                case 'privacy' :
                    $result = DB_query("SELECT * FROM {$_TABLES['pm_userprefs']} WHERE uid=".(int) $uid);
                    $nrows = DB_numRows($result);
                    if ($nrows == 0) {
                        // Insert a new blank record. Defaults are set in SQL Defintion for table.
                        DB_query("INSERT INTO {$_TABLES['pm_userprefs']} (uid) VALUES (".(int) $uid.")");
                        $result = DB_query("SELECT * FROM {$_TABLES['pm_userprefs']} WHERE uid=".(int) $uid);
                    }
                    $A = DB_fetchArray($result);
                    $notify = $A['notify'];
                    $block  = $A['block'];

                    $T = new Template(pm_get_template_path());
                    $T->set_file (array ('privacy'=>'profile_privacy.thtml'));
                    $T->set_var(array(
                        'lang_pmnotify'     => $LANG_PM_NOTIFY['pm_notify'],
                        'pmnotify_checked'  => $notify ? ' checked="checked"' : '',
                        'lang_pmblock'      => $LANG_PM_NOTIFY['pm_block'],
                        'pmblock_checked'   => $block ? ' checked="checked"' : '',
                    ));
                    $T->parse ('output', 'privacy');
                    $retval .= $T->finish ($T->get_var('output'));
                    break;
            }
            break;

        default :
            $retval = '';
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

    if ( isset($_POST['pmnotify']) ) {
        $notify = 1;
    } else {
        $notify = 0;
    }
    if ( isset($_POST['pmblock']) ) {
        $block = 1;
    } else {
        $block = 0;
    }

    DB_save($_TABLES['pm_userprefs'],'uid,notify,block',"'".(int) $uid."',".(int) $notify.','.(int) $block);
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

?>