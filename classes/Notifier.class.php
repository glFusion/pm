<?php
/**
 * Class to send notifications to site members via private message.
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
namespace PM;
use glFusion\User;


/**
 * Email notification class.
 * @package shop
 */
class Notifier extends \glFusion\Notifier
{

    /**
     * Send a private message notification to one or more recipients.
     *
     * @return  boolean     True on success, False on error
     */
    public function Send() : bool
    {
        $retval = false;

        if (!empty($this->recipients)) {
            $PM = new Message;
            $PM->withToUsers(array_keys($this->recipients))
               ->withBccUsers(array_keys($this->bcc))
               ->withAuthorUid($this->from_uid)
               ->withSubject($this->subject)
               ->withComment($this->textmessage);
            $retval = $PM->send();
        }
        return $retval;
    }

}

