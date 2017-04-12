<?php
// +--------------------------------------------------------------------------+
// | PM Plugin for glFusion CMS                                               |
// +--------------------------------------------------------------------------+
// | upgrade.php                                                              |
// |                                                                          |
// | Upgrade routines                                                         |
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

// this file can't be used on its own
if (!defined ('GVERSION')) {
    die ('This file can not be used on its own.');
}

function pm_upgrade()
{
    global $_TABLES, $_CONF, $_PM_CONF, $_DB_table_prefix;

    $currentVersion = DB_getItem($_TABLES['plugins'],'pi_version',"pi_name='pm'");

    require_once $_CONF['path'] . 'system/classes/config.class.php';
    $c = config::get_instance();

    switch ($currentVersion) {
        case '0.5.0' :
        case '0.6.0' :
        case '0.6.1' :
            $_SQL['pm_friends'] = "CREATE TABLE IF NOT EXISTS {$_TABLES['pm_friends']} (
              uid mediumint(9) NOT NULL,
              friend_id mediumint(9) NOT NULL,
              friend_name varchar(255) NOT NULL,
              PRIMARY KEY (uid,friend_id),
              KEY uid (uid)
            ) ENGINE=MyISAM;";
            DB_query($_SQL['pm_friends'],1);
        case '0.8.0' :
        case '0.8.1' :
            $loggedinusers = DB_getItem($_TABLES['groups'],'grp_id','grp_name="Logged-in Users"');
            $pmusers       = DB_getItem($_TABLES['groups'],'grp_id','grp_name="PM Users"');
            DB_query("INSERT INTO {$_TABLES['group_assignments']} (ug_main_grp_id,ug_grp_id) VALUES ($pmusers,$loggedinusers)");
            DB_query("DELETE FROM {$_TABLES['group_assignments']} WHERE ug_main_grp_id=$loggedinusers AND ug_grp_id=$pmusers");
        case '0.8.2' :
        case '0.8.3' :
        case '0.8.4' :
            $_SQL['pm_userprefs'] = "CREATE TABLE {$_TABLES['pm_userprefs']} (
              uid mediumint(9) NOT NULL,
              notify tinyint(4) NOT NULL DEFAULT '1',
              PRIMARY KEY (uid)
            ) ENGINE=MyISAM;";
            DB_query($_SQL['pm_userprefs'],1);
        case '0.9.0' :
        case '1.0.0' :
        case '1.0.1' :
        case '1.1.0' :
        case '1.1.1' :
            DB_query("ALTER TABLE {$_TABLES['pm_userprefs']} ADD `block` INT NOT NULL DEFAULT '0'");
            $c->add('displayblocks',0,'select', 0, 0, 2, 40, true, 'pm');
            DB_query("UPDATE {$_TABLES['groups']} SET grp_gl_core=2 WHERE grp_name='PM Admin'",1);
        case '1.2.0' :
        case '1.2.1' :
        case '1.2.2' :
        case '1.2.3' :
        case '1.2.4' :
        case '2.0.0' :
        case '2.1.0' :
        case '2.1.1' :
            DB_query("ALTER TABLE {$_TABLES['pm_dist']} CHANGE `folder_name` `folder_name` VARCHAR(128) NOT NULL default 'inbox'",1);
            DB_query("ALTER TABLE {$_TABLES['pm_msg']} CHANGE `author_ip` `author_ip` VARCHAR(128) NULL DEFAULT NULL;");
            DB_query("ALTER TABLE {$_TABLES['pm_dist']} DROP INDEX `usr_flder_id`, ADD INDEX `usr_flder_id` (user_id,folder_name);");
            DB_query("ALTER TABLE {$_TABLES['pm_msg']} DROP INDEX `author_ip`, ADD INDEX `author_ip` (author_ip);");

        case '2.1.2' :
            DB_query("ALTER TABLE {$_TABLES['pm_dist']} CHANGE `folder_name` `folder_name` VARCHAR(128) NOT NULL default 'inbox'",1);

        case '2.1.3' :
        case '2.1.4' :
        case '2.1.5' :

        default:
            DB_query("UPDATE {$_TABLES['plugins']} SET pi_version='".$_PM_CONF['pi_version']."',pi_gl_version='".$_PM_CONF['gl_version']."' WHERE pi_name='pm' LIMIT 1");
            break;
    }

    CTL_clearCache();

    if ( DB_getItem($_TABLES['plugins'],'pi_version',"pi_name='pm'") == $_PM_CONF['pi_version']) {
        return true;
    } else {
        return false;
    }
}
?>