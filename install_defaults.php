<?php
// +--------------------------------------------------------------------------+
// | PM Plugin for glFusion CMS                                               |
// +--------------------------------------------------------------------------+
// | install_defaults.php                                                     |
// |                                                                          |
// | Initial Installation Defaults used when loading the online configuration |
// | records. These settings are only used during the initial installation    |
// | and not referenced any more once the plugin is installed.                |
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
    die('This file can not be used on its own!');
}

/*
 * PM default settings
 *
 * Initial Installation Defaults used when loading the online configuration
 * records. These settings are only used during the initial installation
 * and not referenced any more once the plugin is installed
 *
 */

global $CONF_PM_DEFAULT;
$CONF_PM_DEFAULT = array();

$CONF_PM_DEFAULT['messages_per_page'] = 15;
$CONF_PM_DEFAULT['post_speedlimit']   = 30;
$CONF_PM_DEFAULT['max_recipients']    = 5;

/**
* Initialize PM plugin configuration
*
* Creates the database entries for the configuation if they don't already
* exist.
*
* @return   boolean     true: success; false: an error occurred
*
*/
function plugin_initconfig_pm()
{
    global $_PM_CONF, $CONF_PM_DEFAULT;

    if (is_array($_PM_CONF) && (count($_PM_CONF) > 1)) {
        $CONF_PM_DEFAULT = array_merge($CONF_PM_DEFAULT, $_PM_CONF);
    }
    $c = config::get_instance();
    if (!$c->group_exists('pm')) {

        $c->add('sg_main', NULL, 'subgroup', 0, 0, NULL, 0, true, 'pm');
        $c->add('pm_general', NULL, 'fieldset', 0, 0, NULL, 0, true, 'pm');

        $c->add('messages_per_page',$CONF_PM_DEFAULT['messages_per_page'], 'text',
                0, 0, NULL, 10, true, 'pm');

        $c->add('post_speedlimit',$CONF_PM_DEFAULT['post_speedlimit'], 'text',
                0, 0, NULL, 20, true, 'pm');

        $c->add('max_recipients',$CONF_PM_DEFAULT['max_recipients'], 'text',
                0, 0, NULL, 30, true, 'pm');

    }
    return true;
}
?>