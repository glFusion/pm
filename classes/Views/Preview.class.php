<?php
/**
 * Create a message preview.
 *
 * @author      Lee Garner <lee@leegarner.com>
 * @copyright   Copyright (c) 2022 Lee Garner <lee@leegarner.com>
 * @package     pm
 * @version     v3.0.0
 * @since       v3.0.0
 * @license     http://opensource.org/licenses/gpl-2.0.php
 *              GNU Public License v2 or later
 * @filesource
 */
namespace PM\Views;
use PM\Message;
use PM\Author;


class Preview
{

    /**
     * Create a message preview.
     *
     * @param   array   $msg    Array of message properties
     * @return  string      HTML for message preview
     */
    public static function render(array $msg) : string
    {
        global $_CONF, $_USER, $_TABLES;
        $retval = '';

        $Author = new Author($_USER['uid']);

        // clean things up a little...
        $subject = htmlentities($msg['message_subject'], ENT_QUOTES, COM_getEncodingt());

        $T = new \Template(pm_get_template_path());
        $T->set_file (array ('message'=>'message_preview.thtml'));

        $parsers = array(
            array(
                array(
                    'block','inline','link','listitem'
                ),
                '_bbc_replacesmiley',
            ),
        );
        $dt = new \Date('now', $_USER['tzid']);
        $T->set_var(array(
            'from'      => $Author->uid,
            'to'        => $Author->uid,
            'subject'   => $subject,
            'date'      => $dt->format($dt->getUserFormat(),true),
            'msg_text'  => Message::formatTextBlock($msg['message_text'],'text',$parsers),
            'avatar'    => $Author->getPhoto('', '', 128),
            'from_name' => $Author->username,
            'to_name'   => htmlentities($msg['username_list'], ENT_QUOTES, COM_getEncodingt()),
            'rank'      => SEC_inGroup('Root',$Author->uid) ? 'Site Admin' : 'User',
            'registered' => $Author->regdate,
            'signature' => nl2br($Author->sig),
            'homepage'  => $Author->homepage,
            'location'  => $Author->location,
            'email'     => $Author->emailfromuser ? $Author->email : '',
        ) );

        if ( function_exists('msg_showsmilies') ) {
            $T->set_var('smilies',msg_showsmilies());
            $T->set_var('smilies_enabled',true);
        }

        $T->parse ('output', 'message');
        $retval .= $T->finish ($T->get_var('output'));
        return $retval;
    }

}

