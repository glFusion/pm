<?php
// +--------------------------------------------------------------------------+
// | PM Plugin                                                                |
// +--------------------------------------------------------------------------+
// | mysql_install.php                                                        |
// |                                                                          |
// | Contains all the SQL necessary to install the PM plugin                  |
// +--------------------------------------------------------------------------+
// | Copyright (C) 2009-2016 by the following authors:                        |
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

$_SQL['pm_msg'] = "CREATE TABLE {$_TABLES['pm_msg']} (
  msg_id mediumint(8) NOT NULL auto_increment,
  parent_id mediumint(8) NOT NULL default '0',
  author_uid mediumint(8) NOT NULL default '0',
  author_name varchar(254) NOT NULL default '',
  author_ip varchar(128) NOT NULL default '',
  message_time int(11) unsigned NOT NULL DEFAULT '0',
  message_subject varchar(254) NOT NULL default '',
  message_text mediumtext NOT NULL,
  to_address text NOT NULL,
  bcc_address text NOT NULL,
  PRIMARY KEY (msg_id),
  KEY author_ip (author_ip),
  KEY message_time (message_time),
  KEY author_uid (author_uid),
  KEY parent_id (parent_id)
) ENGINE=MyISAM;";

$_SQL['pm_dist'] = "CREATE TABLE {$_TABLES['pm_dist']} (
  msg_id mediumint(8) NOT NULL default '0',
  user_id mediumint(8) NOT NULL default '0',
  username varchar(254) NOT NULL default '',
  author_uid mediumint(8) NOT NULL default '0',
  pm_bcc tinyint(1) unsigned NOT NULL DEFAULT '0',
  pm_unread tinyint(1) unsigned NOT NULL DEFAULT '1',
  pm_replied tinyint(1) unsigned NOT NULL DEFAULT '0',
  pm_forwarded tinyint(1) unsigned NOT NULL DEFAULT '0',
  folder_name varchar(128) NOT NULL default 'inbox',
  KEY msg_id (msg_id),
  KEY author_uid (author_uid),
  KEY usr_flder_id (user_id,folder_name)
) ENGINE=MyISAM;";

$_SQL['pm_friends'] = "CREATE TABLE {$_TABLES['pm_friends']} (
  uid mediumint(9) NOT NULL,
  friend_id mediumint(9) NOT NULL,
  friend_name varchar(255) NOT NULL,
  PRIMARY KEY (uid,friend_id),
  KEY uid (uid)
) ENGINE=MyISAM;";

$_SQL['pm_userprefs'] = "CREATE TABLE {$_TABLES['pm_userprefs']} (
  uid mediumint(9) NOT NULL,
  notify tinyint(4) NOT NULL DEFAULT '1',
  block tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (uid)
) ENGINE=MyISAM;";
?>