<?php
/**
 * Show the conversation history for a message group.
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
use glFusion\Database\Database;
use glFusion\Log\Log;


class History
{
    /*
     * Show the history for a message group.
     *
     * @param   integer $msg_id     Main message ID
     * @param   boolean $compose    Flag indication "compose" mode
     * @return  string      HTML output for message history
     */
    public static function render(int $msg_id = 0, bool $compose = false) : string
    {
        global $_CONF, $_USER, $_TABLES, $LANG_PM00;

        $retval = '';
        $db = Database::getInstance();

        $msg = array();
        $msg_id = (int)$msg_id;
        if ( $msg_id == 0 ) {
            return '';
        }

        USES_lib_bbcode();

        $dt = new \Date('now',$_USER['tzid']);
        $uid = (int)$_USER['uid'];

        $T = new \Template(pm_get_template_path());
        $T->set_file (array ('message'=>'message_history.thtml'));
        $retval .= '<h1>'.$LANG_PM00['message_history'].'</h1>';

        $Msg = Message::getInstance($msg_id);
        $parent_id = $Msg->getParentId();
        if ($parent_id == 0) {
            $parent_id = $msg_id;
        }

        $parsers = array();
        $parsers[] = array(array('block','inline','link','listitem'), '_bbc_replacesmiley');

        try {
            $data = $db->conn->executeQuery(
                "SELECT * FROM {$_TABLES['pm_msg']}
                WHERE msg_id = ? OR parent_id = ?
                ORDER BY message_time DESC",
                array($parent_id, $parent_id),
                array(Database::INTEGER, Database::INTEGER)
            )->fetchAll(Database::ASSOCIATIVE);
        } catch (\Throwable $e) {
            Log::write('system', Log::ERROR, $e->getMessage());
            $data = array();
        }
        $counter = 0;
        $prevmsgtime = 0;
        foreach ($data as $msg) {
            if (!$compose && $msg['msg_id'] == $msg_id) {
                continue;
            }
            if ($prevmsgtime == $msg['message_time']) {
                continue;
            }
            $prevmsgtime = $msg['message_time'];
            $subject = htmlentities($msg['message_subject'], ENT_QUOTES, COM_getEncodingt());
            $dt->setTimestamp($msg['message_time']);

            $formatted_msg_text = BBC_formatTextBlock($msg['message_text'],'text', $parsers);
            $T->set_var(array(
                'from'        => $msg['author_uid'],
                'to'          => $msg['to_address'],
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
            return '';
        }

        $retval .= $T->finish ($T->get_var('output'));
        return $retval;
    }

}
