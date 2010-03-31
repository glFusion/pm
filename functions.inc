<?php
// +--------------------------------------------------------------------------+
// | PM Plugin for glFusion CMS                                               |
// +--------------------------------------------------------------------------+
// | functions.inc                                                            |
// |                                                                          |
// | Implement the glFusion Plugin API                                        |
// +--------------------------------------------------------------------------+
// | $Id::                                                                   $|
// +--------------------------------------------------------------------------+
// | Copyright (C) 2009 by the following authors:                             |
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
        $unread = DB_count($_TABLES['pm_dist'],array('user_id','folder_name','pm_unread'),array($_USER['uid'],'inbox',1));
        return array($LANG_PM00['user_menu'], $_CONF['site_url'] . '/pm/index.php', $unread);
    }
}

function plugin_user_create_pm($uid)
{
    global $_TABLES;

    DB_save($_TABLES['pm_userprefs'],'uid,notify',"'".$uid."',1");
}

function plugin_user_delete_pm($uid)
{
    global $_TABLES;

    $sql  = "SELECT * FROM {$_TABLES['pm_msg']} msg ";
    $sql .= "LEFT JOIN {$_TABLES['pm_dist']} dist ON msg.msg_id=dist.msg_id ";
    $sql .= "WHERE dist.user_id=".intval($uid);

    $result = DB_query($sql);
    while ($msg = DB_fetchArray($result) ) {
        $msg_id = $msg['msg_id'];
        DB_delete($_TABLES['pm_dist'],array('msg_id','user_id'),array($msg_id,$uid));
        if ( DB_count($_TABLES['pm_dist'],'msg_id',$msg_id) == 0 ) {
            DB_delete($_TABLES['pm_msg'],'msg_id',$msg_id);
        }
    }
    DB_query("DELETE FROM {$_TABLES['pm_friends']} WHERE uid=".intval($uid),1);
}

/**
* glFusion is asking us to provide any items that show up in the type
* drop-down on search.php.  Let's users search for events.
*
* @return   array   (plugin name/entry title) pair for the dropdown
*
*/
function plugin_searchtypes_pm()
{
    global $LANG_PM00;

    $tmp = '';

    if ( !COM_isAnonUser() && SEC_hasRights('pm.user')) {
        $tmp['pm'] = $LANG_PM00['pm'];
    }
    return $tmp;
}

/**
* This searches for events matching the user query and returns an array for the
* header and table rows back to search.php where it will be formated and printed
*
* @param    string  $query      Keywords user is looking for
* @param    date    $datestart  Start date to get results for
* @param    date    $dateend    End date to get results for
* @param    string  $topic      The topic they were searching in
* @param    string  $type       Type of items they are searching, or 'all' (deprecated)
* @param    int     $author     Get all results by this author
* @param    string  $keyType    search key type: 'all', 'phrase', 'any'
* @param    int     $page       page number of current search (deprecated)
* @param    int     $perpage    number of results per page (deprecated)
* @return   object              search result object
*
*/
function plugin_dopluginsearch_pm($query, $datestart, $dateend, $topic, $type, $author, $keyType, $page, $perpage)
{
    global $_TABLES, $LANG_PM00, $_USER;

    if ( COM_isAnonUser() || !SEC_hasRights('pm.user') ) {
        return;
    } else {
        $uid = $_USER['uid'];
    }

    // Make sure the query is SQL safe
    $query = DB_escapeString(trim(htmlspecialchars($query)));

    $sql  = "SELECT msg.msg_id AS id, msg.message_subject AS title,
                    message_text AS description, message_time AS date,
                    dist.user_id AS owner_id, ";
    $sql .= "CONCAT('/pm/view.php?msgid=',msg.msg_id,'&amp;folder=',dist.folder_name) AS url ";
    $sql .= "FROM {$_TABLES['pm_msg']} msg LEFT JOIN {$_TABLES['pm_dist']} dist ON msg.msg_id=dist.msg_id WHERE dist.user_id=$uid ";

    if (!empty ($author)) {
        $sql .= "AND (msg.author_uid = ".intval($author).") ";
    }

    // Search the public events
    $search = new SearchCriteria('pm', array($LANG_PM00['pm']));
    $columns = array('message_text','message_subject');
    $sql .= $search->getDateRangeSQL('AND', 'message_time', $datestart, $dateend);
    list($sql_tmp,$ftsql_tmp) = $search->buildSearchSQL($keyType, $query, $columns, $sql);
    $search->setSQL($sql_tmp);
    $search->setFTSQL($ftsql_tmp);
    $search->setRank(2);

    return array($search);
}

function plugin_templatesetvars_pm($templatename, &$template)
{
    global $_CONF, $_USER, $_TABLES, $LANG_PM00;

    if ( COM_isAnonUser() ) return;

    if ( !COM_isAnonUser() && $templatename == 'header' && SEC_hasRights('pm.user')) {
        $unread = DB_count($_TABLES['pm_dist'],array('user_id','folder_name','pm_unread'),array($_USER['uid'],'inbox',1));
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
                    $result = DB_query("SELECT * FROM {$_TABLES['pm_userprefs']} WHERE uid={$_USER['uid']}");
                    $nrows = DB_numRows($result);
                    if ($nrows == 0) {
                        // Insert a new blank record. Defaults are set in SQL Defintion for table.
                        DB_query("INSERT INTO {$_TABLES['pm_userprefs']} (uid) VALUES ({$_USER['uid']})");
                        $result = DB_query("SELECT * FROM {$_TABLES['pm_userprefs']} WHERE uid={$_USER['uid']}");
                    }
                    $A = DB_fetchArray($result);
                    $notify = $A['notify'];
                    $block  = $A['block'];

                    $T = new Template($_CONF['path'] . 'plugins/pm/templates/');
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

function plugin_profilesave_pm( )
{
    global $_CONF, $_TABLES, $_PM_CONF, $_USER;

    if ( COM_isAnonUser() ) {
        return;
    }

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

    DB_save($_TABLES['pm_userprefs'],'uid,notify,block',"'".$_USER['uid']."',".$notify.','.$block);
}

/*
 * Return a link to the help element for this configuration option
 *
 */

function plugin_getconfigelementhelp_pm($element)
{
    return array('http://www.glfusion.org/wiki/doku.php/pm:start#configuration_settings',2);
}

function plugin_profileicondisplay_pm($uid)
{
    global $_CONF, $_USER, $LANG_PM00;

    $retval = array();

    if ( $uid > 1 && !COM_isAnonUser() && SEC_hasRights('pm.user') ) {

        $retval = array(
            'url' => $_CONF['site_url'].'/pm/compose.php?mode=new&amp;uid='.$uid,
            'text' => $LANG_PM00['title'],
            'icon' => $_CONF['site_url'].'/pm/images/pm-profile.png',
        );
    }
    return $retval;
}
?>