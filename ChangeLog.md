## v2.1.3 (December 4, 2016)
  - Fixed selecting messages for delete / archive
  - Fixed typos
  - Fixed folder list to always display correct set of folders
  - Fixed returning from action (i.e.; delete/archive) to wrong folder
  - Cleaned up user interface a bit

## v2.1.2 (unreleased)
  - Updated field / index sizes to appropriate lengths

## v2.1.1 (August 23, 2016)
  - Fixed error where smileys did not show up when enabled

## v2.1.0 (August 1, 2016)
  - Updated for UIKIT based themes

## v2.0.0
  - Fixed error where user menu would not reflect proper unread message count
  - Updated styles to match new glFusion v1.5.0+ UIKIT theme

## v1.2.4
  - Fixed incorrect date display on index page

## v1.2.3
  - Support for glFusion v1.4.x

## v1.2.1
  - Quotes in subjects did not display properly on reply
  - Improved error message when recipient blocks PM messages
  - General code / security clean up
  - Support for glFusion v1.3.0

## v1.2.0
  - Implemented new glFusion admin authentication
  - Configuration option to specify which blocks display
  - Add ability to block users from sending you a PM
  - Full text notification - include the actual PM in the notification email

## v1.1.1
  - New Dutch Translation
  - New Swedish Translation

## v1.1.0
  - Override quotmain style (Mark)
  - Add support for smileys plugin (Mark)
  - Improve speedlimit handling (Mark)
  - Added German translation (Tony Kluever)

## v1.0.0
  - Do not expose user's email, use glFusion mail link instead.
  - Modifed style to use the core glFusion styles, this allows improved integration into other themes.

## v0.8.3
  - Fixed userlist building to only pull users who are authorized to use the PM plugin.
  - Fixed several E_ALL warning messages
  - Improved variable filtering to ensure no monkey business (XSS type stuff)