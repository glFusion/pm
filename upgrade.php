<?php
// +--------------------------------------------------------------------------+
// | PM Plugin for glFusion CMS                                               |
// +--------------------------------------------------------------------------+
// | upgrade.php                                                              |
// |                                                                          |
// | Upgrade routines                                                         |
// +--------------------------------------------------------------------------+
// | Copyright (C) 2009-2018 by the following authors:                        |
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

use glFusion\Database\Database;
use glFusion\Log\Log;
use Doctrine\DBAL\Schema\Schema;


function pm_upgrade()
{
    global $_TABLES, $_CONF, $_PM_CONF;

    $db = Database::getInstance();
    $currentVersion = $db->getItem(
        $_TABLES['plugins'],
        'pi_version',
        array('pi_name' => 'pm'),
        array(Database::STRING)
    );

    require_once $_CONF['path'] . 'system/classes/config.class.php';
    $c = config::get_instance();

    switch ($currentVersion) {
        case '0.5.0' :
        case '0.6.0' :
        case '0.6.1' :
            try {
                $schema = new Schema();
                $table = $schema->createTable($_TABLES['pm_friends']);
                $table->addColumn("uid", "integer", ["unsigned" => true]);
                $table->addColumn("friend_id", "integer");
                $table->addColumn("friend_name", "string", ["length"=>255]);
                $table->setPrimaryKey(["uid", "friend_id"]);
                $queries = $schema->toSql($db->conn->getDatabasePlatform());
                $db->conn->executeQuery($queries[0]);
            } catch (\Throwable $e) {
                Log::write('system', Log::ERROR, $e->getMessage());
            }
        case '0.8.0' :
        case '0.8.1' :
            $loggedinusers = $db->getItem(
                $_TABLES['groups'],
                'grp_id',
                array('grp_name' => 'Logged-in Users'),
                array(Database::STRING)
            );
            $pmusers = $db->getItem(
                $_TABLES['groups'],
                'grp_id',
                array('grp_name' => 'PM Users'),
                array(Database::STRING)
            );
            try {
                $db->conn->executeUpdate(
                    "INSERT INTO {$_TABLES['group_assignments']}
                    (ug_main_grp_id,ug_grp_id) VALUES ($pmusers,$loggedinusers)",
                array($pmusers, $loggedinusers),
                array(Database::INTEGER, Database::INTEGER)
                );
            } catch (\Throwable $e) {
                Log::write('system', Log::ERROR, $e->getMessage());
            }
            try {
                $db->conn->delete(
                    $_TABLES['group_assignments'],
                    array(
                        'ug_main_grp_id' => $loggedinusers,
                        'ug_grp_id' => $pmusers
                    ),
                    array(Database::INTEGER, Database::INTEGER)
                );
            } catch (\Throwable $e) {
                Log::write('system', Log::ERROR, $e->getMessage());
            }

        case '0.8.2' :
        case '0.8.3' :
        case '0.8.4' :
            try {
                $schema = new Schema();
                $table = $schema->createTable($_TABLES['pm_userprefs']);
                $table->addColumn("uid", "integer", ["unsigned" => true]);
                $table->addColumn("notify", "smallint");
                $table->setPrimaryKey(["uid"]);
                $queries = $schema->toSql($db->conn->getDatabasePlatform());
                $db->conn->executeQuery($queries[0]);
            } catch (\Throwable $e) {
                Log::write('system', Log::ERROR, $e->getMessage());
            }
        case '0.9.0' :
        case '1.0.0' :
        case '1.0.1' :
        case '1.1.0' :
        case '1.1.1' :
            try {
                $db->conn->executeQuery(
                    "ALTER TABLE {$_TABLES['pm_userprefs']} ADD `block` INT NOT NULL DEFAULT '0'"
                );
                $c->add('displayblocks',0,'select', 0, 0, 2, 40, true, 'pm');
                $db->conn->executeUpdate(
                    "UPDATE {$_TABLES['groups']} SET grp_gl_core=2 WHERE grp_name='PM Admin'"
                );
            } catch (\Throwable $e) {
                Log::write('system', Log::ERROR, $e->getMessage());
            }
        case '1.2.0' :
        case '1.2.1' :
        case '1.2.2' :
        case '1.2.3' :
        case '1.2.4' :
        case '2.0.0' :
        case '2.1.0' :
        case '2.1.1' :
            try {
                $db->conn->executeQuery(
                    "ALTER TABLE {$_TABLES['pm_dist']} CHANGE `folder_name` `folder_name` VARCHAR(128) NOT NULL default 'inbox'"
                );
                $db->conn->executeQuery(
                    "ALTER TABLE {$_TABLES['pm_msg']} CHANGE `author_ip` `author_ip` VARCHAR(128) NULL DEFAULT NULL;"
                );
                $db->conn->executeQuery(
                    "ALTER TABLE {$_TABLES['pm_dist']} DROP INDEX `usr_flder_id`, ADD INDEX `usr_flder_id` (user_id,folder_name);"
                );
                $db->conn->executeQuery(
                    "ALTER TABLE {$_TABLES['pm_msg']} DROP INDEX `author_ip`, ADD INDEX `author_ip` (author_ip);"
                );
            } catch (\Throwable $e) {
                Log::write('system', Log::ERROR, $e->getMessage());
            }

        case '2.1.2' :
            try {
                $db->conn->executeQuery(
                    "ALTER TABLE {$_TABLES['pm_dist']} CHANGE `folder_name` `folder_name` VARCHAR(128) NOT NULL default 'inbox'"
                );
            } catch (\Throwable $e) {
                Log::write('system', Log::ERROR, $e->getMessage());
            }

        case '2.1.3' :
        case '2.1.4' :
        case '2.1.5' :
        case '2.1.6' :

        case '2.2.0' :
            // no changes

        case '2.2.1':
            try {
                $db->conn->executeQuery(
                    "ALTER TABLE {$_TABLES['pm_friends']} ADD `is_friend` tinyint(1) NOT NULL default 1"
                );
            } catch (\Throwable $e) {
                Log::write('system', Log::ERROR, $e->getMessage());
            }

        default:
            try {
                $db->conn->executeUpdate(
                    "UPDATE {$_TABLES['plugins']} SET pi_version = ?, pi_gl_version='?
                    WHERE pi_name='pm' LIMIT 1",
                    array($_PM_CONF['pi_version'], $_PM_CONF['gl_versoin']),
                    array(Database::STRING, Database::STRING)
                );
            break;
            } catch (\Throwable $e) {
                Log::write('system', Log::ERROR, $e->getMessage());
            }
    }

    PM_clean_files();
    CTL_clearCache();

    if ($db->getItem(
        $_TABLES['plugins'],
        'pi_version',
        array('pi_name' => 'pm')
    ) == $_PM_CONF['pi_version']) {
        return true;
    } else {
        return false;
    }
}


/**
 * Remove a file, or recursively remove a directory.
 *
 * @param   string  $dir    Directory name
 */
function PM_rmdir($dir)
{
    if (is_dir($dir)) {
        $objects = scandir($dir);
        foreach ($objects as $object) {
            if ($object != "." && $object != "..") {
                if (is_dir($dir . '/' . $object)) {
                    PM_rmdir($dir . '/' . $object);
                } else {
                    @unlink($dir . '/' . $object);
                }
            }
        }
        @rmdir($dir);
    } elseif (is_file($dir)) {
        @unlink($dir);
    }
}


/**
 * Remove deprecated files.
 * Errors in unlink() and rmdir() are ignored.
 */
function PM_clean_files()
{
    global $_CONF;

    $paths = array(
        // private/plugins/pm
        __DIR__ => array(
            // 3.0.0
            'include',
        ),
        // public_html/pm
        $_CONF['path_html'] . 'pm' => array(
        ),
        // admin/plugins/pm
        $_CONF['path_html'] . 'admin/plugins/pm' => array(
            'import_messenger.php',
        ),
    );

    foreach ($paths as $path=>$files) {
        foreach ($files as $file) {
            Log::write('system', Log::INFO, "removing $path/$file");
            PM_rmdir("$path/$file");
        }
    }
}

