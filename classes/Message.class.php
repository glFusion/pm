<?php
/**
 * Class to represent private messages.
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


/**
 * Private Message class.
 * @package pm
 */
class Message
{
    private $msg_id = 0;
    private $parent_id = 0;
    private $author_uid = 0;
    private $author_name = '';
    private $author_ip = '';
    private $message_time = 0;
    private $message_subject = '';
    private $message_text = '';
    private $to_address = '';
    private $bcc_address = '';

    private $toUsers = array(); // all users, "to" and "bcc"
    private $_errors = array();
    private $_folder = 'inbox';
    private $_pm_unread = 1;
    private $_uid = 0;


    /**
     * Set defaults for outgoing messages.
     */
    public function __construct()
    {
        global $_USER;

        $this->author_uid = (int)$_USER['uid'];
        $this->author_name = $_USER['username'];
    }


    /**
     * Get a specific message from the database.
     *
     * @param   integer $msg_id     Message to get
     * @param   integer $uid        User ID, to validate
     * @return  object      Message object
     */
    public static function getInstance(int $msg_id, int $uid=0) : Message
    {
        global $_TABLES;

        $PM = new self;
        $sql  = "SELECT msg.*, dist.* FROM {$_TABLES['pm_msg']} msg 
            LEFT JOIN {$_TABLES['pm_dist']} dist ON msg.msg_id=dist.msg_id
            WHERE msg.msg_id = $msg_id";
        if ($uid > 0) {
            $sql .= " AND dist.user_id = $uid";
        }
       // AND dist.folder_name='".DB_escapeString($folder)."'";
        $res = DB_query($sql);
        if ($res && DB_numRows($res) == 1) {
            $A = DB_fetchArray($res, false);
            $PM->setVars($A);
        }
        return $PM;
    }


    /**
     * Set all the object properties from a database record.
     *
     * @param   array   $A      Array of DB record fields
     * @return  object  $this
     */
    public function setVars(array $A) : self
    {
        $this->msg_id = (int)$A['msg_id'];
        $this->author_ip = $A['author_ip'];
        $this->message_time = (int)$A['message_time'];
        $this->to_address = $A['to_address'];
        $this->bcc_address = $A['bcc_address'];
        $this->_pm_unread = (int)$A['pm_unread'];
        $this->withParentId((int)$A['parent_id'])
              ->withAuthorUid((int)$A['author_uid'])
              ->withAuthorName($A['author_name'])
              ->withSubject($A['message_subject'])
              ->withMessage($A['message_text'])
              ->withFolder($A['folder_name'])
              ->withUid($A['user_id']);
        return $this;
    }

    public function getMsgId() : int
    {
        return (int)$this->msg_id;
    }

    public function withParentId(int $id) : self
    {
        $this->parent_id = (int)$id;
        return $this;
    }

    public function withAuthorUid(int $uid) : self
    {
        $this->author_uid = (int)$uid;
        return $this;
    }

    public function getAuthorUid() : int
    {
        return (int)$this->author_uid;
    }


    public function withAuthorName(string $name) : self
    {
        $this->author_name = $name;
        return $this;
    }


    public function withAuthorIP(string $ip) : self
    {
        $this->author_ip = $ip;
        return $this;
    }

    public function withSubject(string $subject) : self
    {
        $this->message_subject = $subject;
        return $this;
    }

    public function withMessage(string $msg) : self
    {
        $this->message_text = $msg;
        return $this;
    }


    public function withToUser(int $uid) : self
    {
        return $this->_withUsers(array($uid), false);
    }

    public function withToUsers(array $uids) : self
    {
        return $this->_withUsers($uids, false);
    }

    public function withToUserNames(array $names) : self
    {
        return $this->_withUserNames($names, false);
    }


    /**
     * Get the user IDs for a "to" or "bcc" list from usernames.
     * TODO: need to use only user IDs
     *
     * @param   array   $names      Usernames
     * @param   boolean $bcc        True to add to BCC, False for To
     * @return  object  $this
     */
    private function _withUserNames(array $names, $bcc=false) : self
    {
        global $_TABLES;

        // Need to convert usernames to user IDs
        $userids = array();
        foreach ($names as $name) {
            $usernames[] = "'" . DB_escapeString($name) . "'";
        }
        $sql = "SELECT uid FROM {$_TABLES['users']}
            WHERE username IN (" . implode(',', $usernames) . ')';
        $res = DB_query($sql);
        if ($res && DB_numRows($res) > 0) {
            while ($A = DB_fetchArray($res, false)) {
                $userids[] = $A['uid'];
            }
        }
        if (!empty($userids)) {
            $this->_withUsers($userids, $bcc);
        }

        return $this;
    }

    public function withBccUser(int $uid) : self
    {
        return $this->_withUsers(array($uid), true);
    }


    public function withBccUsers(array $uids) : self
    {
        return $this->_withUsers($uids, true);
    }

    public function withBccUserNames(array $names) : self
    {
        return $this->_withUserNames($names, true);
    }


    /**
     * Add the supplied user IDs to either the To or BCC list
     *
     * @param   array   $uids   Array of user IDs
     * @param   boolean $bcc    True for BCC, False for To
     * @return  object  $this
     */
    private function _withUsers(array $uids, bool $bcc=false) : self
    {
        global $_TABLES;

        $bcc = $bcc ? 1 : 0;  // convert to pm_bcc flag
        if (!empty($uids)) {
            $to_str = DB_escapeString(implode(',', $uids));
            $sql = "SELECT u.uid, u.username, u.email, up.block, up.notify, $bcc AS pm_bcc
                FROM {$_TABLES['users']} AS u
                LEFT JOIN {$_TABLES['pm_userprefs']} AS up
                ON u.uid=up.uid
                WHERE u.uid IN ($to_str)";
            $result = DB_query($sql);
            if (DB_numRows($result) > 0) {
                $to_users = DB_fetchAll($result, false);
            }
            foreach ($to_users as $user) {
                $this->toUsers[$user['uid']] = $user;
            }
        }
        return $this;
    }


    private function _addError(string $msg) : void
    {
        $this->_errors[] = $msg;
    }

    public function getErrors() : array
    {
        return $this->_errors;
    }

    public function withFolder(string $folder) : self
    {
        $this->_folder = $folder;
        return $this;
    }

    public function withUid(int $uid) : self
    {
        $this->_uid = (int)$uid;
        return $this;
    }


    /**
     * Send the current message data.
     *
     * @return  boolean     True on success
     */
    public function send() : bool
    {
        global $LANG_PM_ERROR, $_TABLES, $_USER;

        if (empty($this->toUsers)) {
            $this->_addError($LANG_PM_ERROR['no_to_address']);
            return false;
        }

        $to_address = array();
        $bcc_address = array();
        foreach ($this->toUsers as $user) {
            if ($user['pm_bcc']) {
                $bcc_address[] = $user['username'];
            } else {
                $to_address[] = $user['username'];
            }
        }
        $to_address = implode(',', $to_address);
        $bcc_address = implode(',', $bcc_address);

        $subject = $this->message_subject;  // save for strip_tags later
        if (strlen($subject) < 4) {
            $this->_addError($LANG_PM_ERROR['no_subject']);
        }

        if (strlen($this->message_text) < 4) {
            $this->_addError = $LANG_PM_ERROR['no_message'];
        }
        if (count($this->_errors) > 0 ) {
            return false;
        }

        if (!empty($this->author_uid)) {
            $remote_addr = $_SERVER['REMOTE_ADDR'];
        } else {
            $remote_addr = '0.0.0.0';
            $this->author_name = 'System Message';
        }

        // do a little cleaning...
        $subject = strip_tags($subject);
        $parent_id = 0;
        $reply_msgid = 0;
        $pm_replied = 0;

        // Add the message record
        $sql  = "INSERT INTO {$_TABLES['pm_msg']} SET
            parent_id = $parent_id,
            author_uid = {$this->author_uid},
            author_name = '" . DB_escapeString($this->author_name) . "',
            author_ip = '" . DB_escapeString($remote_addr) . "',
            message_time = UNIX_TIMESTAMP(),
            message_subject = '" . DB_escapeString($subject) . "',
            message_text = '" . DB_escapeString($this->message_text) . "',
            to_address = '" . DB_escapeString($to_address) . "',
            bcc_address = '" . DB_escapeString($bcc_address) . "'";
        DB_query($sql);
        $lastmsg_id = DB_insertID();

        // Add a mailbox record for each recipient and notify
        foreach ($this->toUsers as $user) {
            $sql = "INSERT INTO {$_TABLES['pm_dist']} SET
                msg_id = $lastmsg_id,
                user_id = {$user['uid']},
                username = '" . DB_escapeString($user['username']) . "',
                author_uid = {$this->author_uid},
                pm_bcc = {$user['pm_bcc']}";
            DB_query($sql);
        }
        $this->notify();

        // Add an outbox message for the author, if not a system-generated message.
        if ($this->author_uid > 0) {
            $sql  = "INSERT INTO {$_TABLES['pm_dist']} SET
                msg_id = $lastmsg_id,
                user_id = $this->author_uid,
                username = '" . DB_escapeString($_USER['username']) . "',
                author_uid = {$this->author_uid},
                folder_name = 'outbox',
                pm_unread = 0,
                pm_replied = 0";
            DB_query($sql);

            // update original record to show it has been replied...
            DB_query(
                "UPDATE {$_TABLES['pm_dist']} SET pm_replied=1
                WHERE msg_id = " . (int)$reply_msgid .
                " AND user_id= {$this->author_uid}
                AND folder_name NOT IN ('outbox','sent')"
            );
            COM_updateSpeedlimit ('pm');
        }
        CACHE_remove_instance('stmenu');
        return true;
    }


    /**
     * Notify each recipient of the new message, if desired.
     */
    public function notify() : void
    {
        global $_CONF, $_USER, $_TABLES, $LANG_PM_NOTIFY;

        $from = array($_CONF['noreply_mail'],$_CONF['site_name']);
        $subject =  $_CONF['site_name'] .' - '. $LANG_PM_NOTIFY['new_pm_notification'];

        // Format the message once for email
        require_once __DIR__ . '/../include/lib-pm.php';
        USES_lib_bbcode();
        $parsers = array(
            array(
                array('block','inline','link','listitem',
                )
            )
        );
        $message = PM_BBC_formatTextBlock($this->message_text, 'text', $parsers);
        // we don't have a stylesheet for email, so replace our div with the style...
        $message = str_replace(
            '<div class="quotemain">',
            '<div style="border: 1px dotted #000;border-left: 4px solid #8394B2;color:#465584;  padding: 4px;  margin: 5px auto 8px auto;">',
            $message
        );

        foreach ($this->toUsers as $user) {
            if ($user['notify']) {
                // build the template...
                $T = new \Template(pm_get_template_path());
                $T->set_file ('email', 'pm_notify.thtml');

                $T->set_var(array(
                    'to_username'       => $user['username'],
                    'from_username'     => $this->author_name,
                    'msg_subject'       => $this->message_subject,
                    'site_name'         => $_CONF['site_name'] . ' - ' . $_CONF['site_slogan'],
                    'site_url'          => $_CONF['site_url'],
                    'pm_text'           => $message,
                ));
                $T->parse('output','email');
                $messageHtml = $T->finish($T->get_var('output'));

                $html2txt = new \Html2Text\Html2Text($message, false);
                $messageText = $html2txt->get_text();

                $to = array($user['email'] ,$user['username']);
                COM_mail($to, $subject, $messageHtml, $from, true, 0, '', $messageText);
            }
        }
    }


    /**
     * Mark this message as read.
     */
    public function markRead() : void
    {
        global $_TABLES, $_USER;

        // Mark the message as read
        DB_query(
            "UPDATE {$_TABLES['pm_dist']} SET pm_unread=0
            WHERE msg_id = {$this->msg_id}
            AND user_id = " . (int)$_USER['uid']
        );
        DB_query(
            "UPDATE {$_TABLES['pm_dist']} SET folder_name='sent'
            WHERE msg_id = {$this->msg_id}
            AND user_id = {$this->author_uid}
            AND folder_name='outbox'"
        );
        CACHE_remove_instance('menu');
    }


    /**
     * Create the display view for the message.
     *
     * @param   integer $page   Page number
     * @return  string      HTML for display
     */
    public function Render(int $page=1) : string
    {
        global $_USER, $_CONF, $LANG_PM00;

        if ($this->msg_id == 0) {
            return $LANG_PM_ERROR['message_not_found'];
        }

        USES_lib_bbcode();

        if (
            ($this->_folder == 'inbox' || $this->_folder == 'archive') &&
            $this->_pm_unread == 1
        ) {
            $this->markRead();
        }

        $Author = new Author($this->author_uid);

        $T = new \Template(pm_get_template_path());
        $T->set_file (array ('message'=>'message.thtml'));

        // are they a friend?
        if ($this->author_uid != $_USER['uid'] ) {
            echo "here";die;
            if ($Author->hasFriend($_USER['uid'])) {

                $T->set_var('add_friend','<br /><strong>'.$LANG_PM00['in_friends_list'].'</strong><br />');
            } else {
                $addFriend = '<a href="#" onclick="ajax_addfriend('.$_USER['uid'].','.$this->author_uid.',-1,1);return false;">
              <img src="'.$_CONF['site_url'].'/pm/images/addfriend.gif" alt="Add Friend" />
                </a>';

                $friendHTML = '<div id="u'.$this->author_uid.'"><span id="friend'.$this->author_uid.'">'
                          .'<br />'.$addFriend.'<br />'
                          .'</span></div>';
                $T->set_var('add_friend',$friendHTML);
            }
        } else {
            $T->set_var('add_friend','');
        }

        $parsers = array();
        $parsers[] = array(array ('block', 'inline', 'listitem'), '_bbc_replacesmiley');

        $message_history = PM_showHistory( $this->msg_id, 0 );

        $formatted_msg_text = BBC_formatTextBlock($this->message_text,'text', $parsers);
        $dt = new \Date($this->message_time, $_USER['tzid']);
        $T->set_var(array(
            'from'          => $this->author_uid,
            'to'            => $this->_uid,
            'subject'       => $this->message_subject,
            'date'          => $dt->format($dt->getUserFormat(),true),
            'msg_text'      => $formatted_msg_text,
            'return_link'   => $_CONF['site_url'].'/pm/index.php?folder='.$this->_folder.'&amp;page='.$page.'#msg'.$this->msg_id,
            'folder'        => isset($LANG_PM00[$this->_folder]) ? $LANG_PM00[$this->_folder] : 'Unknown',
            'folder_id'     => $this->_folder,
            'avatar'        => $Author->getPhoto(),
            'from_name'     => $Author->username,
            'from_uid'      => $this->author_uid,
            'to_name'       => $this->to_address,
            'msg_id'        => $this->msg_id,
            'rank'          => SEC_inGroup('Root',$this->author_uid) ? 'Site Admin' : 'User',
            'registered'    => $Author->regdate,
            'signature'     => nl2br($sig),
            'homepage'      => $Author->homepage,
            'location'      => $Author->location,
            'email'         => $emailfromuser ? $_CONF['site_url'].'/profiles.php?uid='.$this->author_uid : '',
            'message_history'   => $message_history,
            'can_reply'     => $this->author_uid > 0,
        ));

        $T->parse ('output', 'message');
        return $T->finish ($T->get_var('output'));
    }
}

