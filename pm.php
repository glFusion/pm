<?php
// +--------------------------------------------------------------------------+
// | PM Plugin for glFusion CMS                                               |
// +--------------------------------------------------------------------------+
// | pm.php                                                                   |
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
    die ('This file can not be used on its own.');
}

$_PM_CONF['pi_name']           = 'pm';
$_PM_CONF['pi_display_name']   = 'Private message';
$_PM_CONF['pi_version']        = '1.2.0';
$_PM_CONF['gl_version']        = '1.2.0';
$_PM_CONF['pi_url']            = 'http://www.glfusion.org';

$_PM_table_prefix = $_DB_table_prefix . 'pm_';

$_TABLES['pm_dist']         = $_PM_table_prefix . 'dist';
$_TABLES['pm_msg']          = $_PM_table_prefix . 'msg';
$_TABLES['pm_friends']      = $_PM_table_prefix . 'friends';
$_TABLES['pm_userprefs']    = $_PM_table_prefix . 'userprefs';
?>