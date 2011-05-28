<?php
// +--------------------------------------------------------------------------+
// | PM Plugin for glFusion CMS                                               |
// +--------------------------------------------------------------------------+
// | autoinstall.php                                                          |
// |                                                                          |
// | glFusion Auto Installer module                                           |
// +--------------------------------------------------------------------------+
// | $Id::                                                                   $|
// +--------------------------------------------------------------------------+
// | Copyright (C) 2009-2011 by the following authors:                        |
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
    die ('This file can not be used on its own.');
}

global $_DB_dbms;

require_once $_CONF['path'].'plugins/pm/pm.php';
require_once $_CONF['path'].'plugins/pm/sql/mysql_install.php';

// +--------------------------------------------------------------------------+
// | Plugin installation options                                              |
// +--------------------------------------------------------------------------+

$INSTALL_plugin['pm'] = array(
    'installer' => array('type' => 'installer', 'version' => '1', 'mode' => 'install'),
    'plugin' => array('type' => 'plugin', 'name' => $_PM_CONF['pi_name'], 'ver' => $_PM_CONF['pi_version'], 'gl_ver' => $_PM_CONF['gl_version'], 'url' => $_PM_CONF['pi_url'], 'display' => $_PM_CONF['pi_display_name']),
    array('type' => 'table', 'table' => $_TABLES['pm_dist'], 'sql' => $_SQL['pm_dist']),
    array('type' => 'table', 'table' => $_TABLES['pm_msg'], 'sql' => $_SQL['pm_msg']),
    array('type' => 'table', 'table' => $_TABLES['pm_friends'], 'sql' => $_SQL['pm_friends']),
    array('type' => 'table', 'table' => $_TABLES['pm_userprefs'], 'sql' => $_SQL['pm_userprefs']),
    array('type' => 'group', 'group' => 'PM Admin', 'desc' => 'Administrators of the PM Plugin',
            'variable' => 'admin_group_id', 'addroot' => true, 'admin' => true),
    array('type' => 'group', 'group' => 'PM Users', 'desc' => 'Users of the PM Plugin',
            'variable' => 'user_group_id', 'addroot' => true, 'default' => true),
    array('type' => 'feature', 'feature' => 'pm.admin', 'desc' => 'Ability to administer the PM plugin', 'variable' => 'admin_feature_id'),
    array('type' => 'feature', 'feature' => 'pm.user', 'desc' => 'PM User', 'variable' => 'user_feature_id'),
    array('type' => 'mapping', 'group' => 'admin_group_id', 'feature' => 'admin_feature_id', 'log' => 'Adding PM feature to the PM admin group'),
    array('type' => 'mapping', 'group' => 'user_group_id', 'feature' => 'user_feature_id', 'log' => 'Adding PM feature to the PM user group'),
);

/**
* Puts the datastructures for this plugin into the glFusion database
*
* Note: Corresponding uninstall routine is in functions.inc
*
* @return   boolean True if successful False otherwise
*
*/
function plugin_install_pm()
{
    global $INSTALL_plugin, $_PM_CONF;

    $pi_name            = $_PM_CONF['pi_name'];
    $pi_display_name    = $_PM_CONF['pi_display_name'];
    $pi_version         = $_PM_CONF['pi_version'];

    COM_errorLog("Attempting to install the $pi_display_name plugin", 1);

    $ret = INSTALLER_install($INSTALL_plugin[$pi_name]);
    if ($ret > 0) {
        return false;
    }

    return true;
}

/**
* Loads the configuration records for the Online Config Manager
*
* @return   boolean     true = proceed with install, false = an error occured
*
*/
function plugin_load_configuration_pm()
{
    global $_CONF;

    require_once $_CONF['path'] . 'plugins/pm/install_defaults.php';

    return plugin_initconfig_pm();
}

/**
* Automatic uninstall function for plugins
*
* @return   array
*
* This code is automatically uninstalling the plugin.
* It passes an array to the core code function that removes
* tables, groups, features and php blocks from the tables.
* Additionally, this code can perform special actions that cannot be
* foreseen by the core code (interactions with other plugins for example)
*
*/

function plugin_autouninstall_pm ()
{
    $out = array (
        /* give the name of the tables, without $_TABLES[] */
        'tables' => array ( 'pm_dist',
						    'pm_msg',
						    'pm_friends',
						    'pm_userprefs'),
        /* give the full name of the group, as in the db */
        'groups' => array('PM Admin','PM Users'),
        /* give the full name of the feature, as in the db */
        'features' => array('pm.admin','pm.user'),
        /* give the full name of the block, including 'phpblock_', etc */
        'php_blocks' => array(),
        /* give all vars with their name */
        'vars'=> array()
    );
    return $out;
}
?>