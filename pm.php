<?php
/**
 * Key variable definitions for the Private Message plugin.
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
    die ('This file can not be used on its own.');
}

$_PM_CONF['pi_name']           = 'pm';
$_PM_CONF['pi_display_name']   = 'Private message';
$_PM_CONF['pi_version']        = '3.0.0';
$_PM_CONF['gl_version']        = '2.1.0';
$_PM_CONF['pi_url']            = 'https://www.glfusion.org';

$_PM_table_prefix = $_DB_table_prefix . 'pm_';

$_TABLES['pm_dist']         = $_PM_table_prefix . 'dist';
$_TABLES['pm_msg']          = $_PM_table_prefix . 'msg';
$_TABLES['pm_friends']      = $_PM_table_prefix . 'friends';
$_TABLES['pm_userprefs']    = $_PM_table_prefix . 'userprefs';

