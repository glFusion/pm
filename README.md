# Private Message Plugin for the glFusion CMS

## OVERVIEW
The PM plugin allows members of your site to send private messages to each
other.

## SYSTEM REQUIREMENTS

PM has the following system requirements:

* PHP 5.3.3 and higher.
* MySQL v5.0.15 or newer
* glFusion v1.5.0 or newer

## INSTALLATION

The PM Plugin uses the glFusion automated plugin installer.
Simply upload the distribtuion using the glFusion plugin installer located in
the Plugin Administration page.

## UPGRADING

The upgrade process is identical to the installation process, simply upload
the distribution from the Plugin Administration page.

## CONFIGURATION SETTINGS

Message Per Page?

Set this to the number of messages to display in the message list.

Posting Speedlimit (seconds)

Set this to the number of seconds a user must wait before sending additional messages.

Max Number of recipients per message

Set this to the maximum number of recipients a user can send a message to at one time.

## USAGE

The PM plugin supports 4 folders for messages:

 - Inbox     where new messages are stored

 - Outbox    where sent messages are intially stored until the recipient
             has read the message.

 - Sent      where sent messages that have been read by the recipient are
             stored.

 - Archive   A folder where each user can save messages for future
             reference.

If a message is in a user's Outbox, they can delete the message, this will
retract (delete from the recipients inbox) the message.  Once a recipient
has read a message, it cannot be retracted by the sender.

The PM Plugin supports 'Friend Lists'. Friends enable you quick access to
members you communicate with frequently.  You can add friends by clicking
the 'Add to Friends' icon under the user's information when viewing a message,
or you can use the 'Manage Friends' link to add / remove friends.

## RESTRICTING ACCESS TO THE PM PLUGIN
During the installation of the PM plugin, a glFusion group called 'PM Users'
is created.  The existing glFusion group 'Logged in Users' is automatically
added to the PM Users group, allowing all logged in site members access to
the PM plugin.

If you would like to restrict the use of the PM plugin to a sub-set of your
users, you will need to remove the 'Logged In Users' group from the PM Users
group.  This can be done in the glFusion Group Administration screen.

You will now need to add user individually to the PM Users group, or add
another group on your site to the PM Users group to grant the subset of users
access to the PM plugin.

## FORUM - PM INTEGRATION
The PM Plugin will integrate with the glFusion Forum plugin and provide
a PM link in the footer of the forum posts. You must be running glFusion v1.1.5
or later.

## LICENSE

This program is free software; you can redistribute it and/or modify it under
the terms of the GNU General Public License as published by the Free Software
Foundation; either version 2 of the License, or (at your option) any later
version.