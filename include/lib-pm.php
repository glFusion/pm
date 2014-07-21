<?php
// +--------------------------------------------------------------------------+
// | PM Plugin for glFusion CMS                                               |
// +--------------------------------------------------------------------------+
// | lib-pm.php                                                               |
// |                                                                          |
// | PM plugin support functions                                              |
// +--------------------------------------------------------------------------+
// | Copyright (C) 2009-2014 by the following authors:                        |
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

/*
 * Only allow logged-in users or users who have the pm.user permission
 */
 if ( COM_isAnonUser()  )  {
    $display = COM_siteHeader();
    $display .= SEC_loginRequiredForm();
    $display .= COM_siteFooter();
    echo $display;
    exit;
}

if ( !SEC_hasRights('pm.user') ) {
    echo COM_refresh($_CONF['site_url']);
    exit;
}

function PM_alertMessage( $msgText = '' )
{
    global $_CONF, $_PM_CONF, $LANG_PM00;

    $display = COM_siteHeader('menu',$LANG_PM00['title']);

    $T = new Template($_CONF['path'] . 'plugins/pm/templates/');
    $T->set_file (array ('message'=>'pm_alertmsg.thtml'));
    $T->set_var(array(
        'message_title' =>  $LANG_PM00['title'] . $LANG_PM00['error'],
        'message_text'  =>  $msgText,
    ));
    $T->parse ('output', 'message');
    $display .= $T->finish ($T->get_var('output'));

    $display .= COM_siteFooter();
    echo $display;
    exit;
}


function PM_getFolder( $varname='folder' ) {

    if ( isset($_GET[$varname]) ) {
        $folder = strtolower( COM_applyFilter($_GET[$varname]) );
    } elseif (isset($_POST[$varname]) ) {
        $folder = strtolower( COM_applyFilter($_POST[$varname]) );
    } else {
        $folder = 'inbox';
    }

    if ( !in_array($folder,array('inbox','sent','archive','outbox') ) ) {
        $folder = 'inbox';
    }
    return $folder;
}


/*
 * Show the history for a message group
 */
function PM_showHistory( $msg_id = 0, $compose = 0 )
{
    global $_CONF, $_USER, $_TABLES, $LANG_PM00;

    $retval = '';

    $msg = array();

    if ( $msg_id == 0 ) {
        return;
    }

    $dt = new Date('now',$_USER['tzid']);

    $T = new Template($_CONF['path'] . 'plugins/pm/templates/');
    $T->set_file (array ('message'=>'message_history.thtml'));
    $retval .= '<h1>'.$LANG_PM00['message_history'].'</h1>';

    $parent_id = DB_getItem($_TABLES['pm_msg'],'parent_id','msg_id='.intval($msg_id));

    $sql = 'SELECT t.*, p.*, u.*
        FROM ' . $_TABLES['pm_msg'] . ' p, ' . $_TABLES['pm_dist'] . ' t, ' . $_TABLES['users'] . ' u
        WHERE t.msg_id = p.msg_id
            AND p.author_uid = u.uid
            AND t.user_id = '.(int) $_USER['uid'];

    if ( $compose ) {
        $extraWhere = ' AND t.folder_name NOT IN ("outbox", "sent")';
    } else {
        $extraWhere = '';
    }
    $sql  = $sql . $extraWhere;

    if (!$parent_id) {
        $sql .= " AND (p.parent_id = $msg_id OR (p.parent_id = 0 AND p.msg_id = ".(int) $msg_id."))";
    } else {
        $sql .= " AND (p.parent_id = " . (int) $parent_id . ' OR p.msg_id = ' . (int) $parent_id . ')';
    }
    $sql .= ' ORDER BY p.message_time DESC ';

    $result = DB_query($sql);
    if ( DB_numRows($result) < 1 ) {
        return;
    }
    $parsers = array();
    $parsers[] = array(array('block','inline','link','listitem'), '_bbc_replacesmiley');

    $counter = 0;
    $prevmsgtime = 0;
    while ( $msg = DB_fetchArray($result) ) {
        if ($compose == 0 && $msg['msg_id'] == $msg_id) {
            continue;
        }
        if ( $prevmsgtime == $msg['message_time'] ) {
            continue;
        }
        $prevmsgtime = $msg['message_time'];
        $subject = htmlentities($msg['message_subject'], ENT_QUOTES, COM_getEncodingt());
        $dt->setTimestamp($msg['message_time']);

        $formatted_msg_text = BBC_formatTextBlock($msg['message_text'],'text', $parsers);
        $T->set_var(array(
            'from'        => $msg['author_uid'],
            'to'          => $msg['user_id'],
            'subject'     => $subject,
            'date'        => $dt->format($dt->getUserFormat(),true),
            'msg_text'    => $formatted_msg_text,
            'from_name'   => $msg['author_name'],
            'to_name'     => $msg['to_address'],
        ));

        $T->parse ('output', 'message',true);
        $counter++;
    }

    if ( $counter == 0 ) {
        return;
    }

    $retval .= $T->finish ($T->get_var('output'));
    return $retval;
}


function PM_deleteMessage( $msg_id=0, $folder='' )
{
    global $_CONF, $_PM_CONF, $_TABLES, $_USER;

    if ( $msg_id == 0 || $folder == '' ) {
        return;
    }

    $msg_id = intval($msg_id);

    if ( !in_array($folder,array('inbox','sent','archive','outbox') ) ) {
        return;
    }

    $sql = "SELECT * FROM {$_TABLES['pm_msg']} msg LEFT JOIN {$_TABLES['pm_dist']} dist ON msg.msg_id=dist.msg_id WHERE msg.msg_id=".(int) $msg_id." AND dist.user_id=".(int) $_USER['uid']." AND folder_name='".DB_escapeString($folder)."'";
    $result = DB_query($sql);
    if ( DB_numRows($result) < 1 ) {
        return;
    }

    if ( $folder == 'outbox' ) {
        // we know no one has read it yet or it wouldn't be in our out box
        DB_query("DELETE FROM {$_TABLES['pm_dist']} WHERE (msg_id = ".(int) $msg_id.")");
        DB_query("DELETE FROM {$_TABLES['pm_msg']} WHERE (msg_id = ".(int) $msg_id.")");
    } else {
        // must be in our in, sent, or archive folder
        DB_query("DELETE FROM {$_TABLES['pm_dist']} WHERE msg_id = ".(int) $msg_id." AND user_id=".(int) $_USER['uid']." AND folder_name='".DB_escapeString($folder)."'");
        $msgCount = DB_count($_TABLES['pm_dist'],'msg_id',(int) $msg_id);
        if ( $msgCount == 0 ) {
            DB_query("DELETE FROM {$_TABLES['pm_msg']} WHERE (msg_id = ".(int) $msg_id.")");
        }
    }
}

// bbcode extensions...

function PM_BBC_formatTextBlock($str,$postmode='text',$parsers=array())
{

    $codes = array();

//-sample code
//    $codes[] = array('youtube', 'usecontent?', '_bbc_youtube', array ('usecontent_param' => 'default'),
//                      'link', array ('listitem', 'block', 'inline'), array ('link'));

    return BBC_formatTextBlock($str,'text',$parsers,$codes);
}


function _bbc_replacesmiley($str) {
    global $_CONF,$_TABLES,$CONF_FORUM;

    if (function_exists('msg_showsmilies') ) {
        $str = msg_replaceEmoticons($str);
    }

    return $str;
}

//-sample code
/*---
function _bbc_youtube ($action, $attributes, $content, $params, $node_object) {

    if ($action == 'validate') {
        return true;
    }
    $retval = '<div><div><object width="425" height="344"><param name="movie" value="http://www.youtube.com/v/'.$attributes['default'].'&hl=en_US&fs=1&"></param><param name="allowFullScreen" value="true"></param><param name="allowscriptaccess" value="always"></param><embed src="http://www.youtube.com/v/'.$attributes['default'].'&hl=en_US&fs=1&" type="application/x-shockwave-flash" allowscriptaccess="always" allowfullscreen="true" width="425" height="344"></embed></object></div>'.$content.'</div>';
    if ( $content == '' ) {
        $content = 'Check out this video';
    }
    return $retval;
    return '<a href="http://www.youtube.com/watch?v='.$attributes['default'].'">'.$content.'</a>';
}
*/

function PM_siteHeader($title='', $meta='')
{
    global $_PM_CONF;

    $retval = '';

    switch( $_PM_CONF['displayblocks'] ) {
        case 0 : // left only
        case 2 :
            $retval .= COM_siteHeader('menu',$title,$meta);
            break;
        case 1 : // right only
        case 3 :
            $retval .= COM_siteHeader('none',$title,$meta);
            break;
        default :
            $retval .= COM_siteHeader('menu',$title,$meta);
            break;
    }
    return $retval;
}

function PM_siteFooter() {
    global $_CONF, $_PM_CONF;

    $retval = '';

    switch( $_PM_CONF['displayblocks'] ) {
        case 0 : // left only
        case 3 : // none
            $retval .= COM_siteFooter();
            break;
        case 1 : // right only
        case 2 : // left and right
            $retval .= COM_siteFooter( true );
            break;
        default :
            $retval .= COM_siteFooter();
            break;
    }
    return $retval;
}
?>