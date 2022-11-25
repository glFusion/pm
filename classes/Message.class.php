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
use glFusion\Database\Database;
use glFusion\Log\Log;


/**
 * Private Message class.
 * @package pm
 */
class Message
{
    /** Main message record ID.
     * @var integer */
    private $msg_id = 0;

    /** Parent message record ID, to track replies.
     * @var integer */
    private $parent_id = 0;

    /** Author's user ID.
     * @var integer */
    private $author_uid = 0;

    /** Author's name, tracked in case of name change or deletion.
     * @var string */
    private $author_name = '';

    /** Remote IP address of the author.
     * @var string */
    private $author_ip = '';

    /** Message timestamp.
     * @var intgeer */
    private $message_time = 0;

    /** Message subject text.
     * @var string */
    private $message_subject = '';

    /** Message body text.
     * @var string */
    private $message_text = '';

    /** Comma-separated list of To addresses.
     * @var string */
    private $to_address = '';

    /** Comma-separated list of BCC addresses.
     * @var string */
    private $bcc_address = '';

    /** Array of To and BCC user IDs.
     * @var array */
    private $toUsers = array();

    /** Array of error messages accumulated.
     * @var array */
    private $_errors = array();

    /** Current folder being viewed.
     * @var string */
    private $_folder = 'inbox';

    /** Message Unread flag.
     * @var boolean */
    private $_pm_unread = 1;

    /** Current user ID.
     * @var integer */
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
    public static function getInstance(int $msg_id, string $folder='inbox', int $uid=0) : self
    {
        global $_TABLES, $_USER;

        if ($uid == 0) {
            $uid = $_USER['uid'];
        }
        $msg_id = (int)$msg_id;
        $uid = (int)$uid;

        $PM = new self;
        $db = Database::getInstance();
        $qb = $db->conn->createQueryBuilder();
        try {
            $qb->select('msg.*', 'dist.*')
               ->from($_TABLES['pm_msg'], 'msg')
               ->leftJoin('msg', $_TABLES['pm_dist'], 'dist', 'msg.msg_id=dist.msg_id')
               ->where('dist.msg_id = :msg_id')
               ->andWhere('dist.folder_name = :folder')
               ->setParameter('msg_id', $msg_id, Database::INTEGER)
               ->setParameter('folder', $folder, Database::STRING)
               ->setParameter('uid', $uid, Database::INTEGER);
            if ($folder == 'outbox' || $folder == 'sent') {
                $qb->andWhere('dist.author_uid = :uid');
            } else {
                $qb->andWhere('dist.user_id = :uid');
            }
            $data = $qb->execute()->fetchAssociative();
            $PM->setVars($data);
        } catch (\Throwable $e) {
            Log::write('system', Log::ERROR, __METHOD__ . ': ' . $e->getMessage());
        }
        return $PM;
    }


    public static function fromPost(array $post) : self
    {
        $Message = new self;
        if (!isset($post['author_ip'])) {
            $post['author_ip'] = $_SERVER['REMOTE_ADDR'];
        }
        $post['pm_unread'] = 1;
        $post['message_time'] = time();
        $Message->setVars($post);
        return $Message;
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
        if (isset($A['to_address'])) {
            $this->to_address = $A['to_address'];
        }
        if (isset($A['bcc_address'])) {
            $this->bcc_address = $A['bcc_address'];
        }
        if (isset($A['author_uid'])) {
            $this->author_uid = (int)$A['author_uid'];
        }
        if (isset($A['author_name'])) {
            $this->author_name = $A['author_name'];
        }
        if (isset($A['folder_name'])) {
            $this->foler_name = $A['folder_name'];
        }
        if (isset($A['user_id'])) {
            $this->uid = (int)$A['user_id'];
        }
        if (isset($A['pm_unread'])) {
            $this->_pm_unread = (int)$A['pm_unread'];
        }
        $this->withParentId((int)$A['parent_id'])
              ->withSubject($A['message_subject'])
              ->withComment($A['message_text']);
        return $this;
    }


    /**
     * Get the message main record ID.
     *
     * @return  integer     Message record ID
     */
    public function getMsgId() : int
    {
        return (int)$this->msg_id;
    }


    /**
     * Set the message's parent record ID.
     *
     * @param   integer $id     Parent message record ID
     * @return  object  $this
     */
    public function withParentId(int $id) : self
    {
        $this->parent_id = (int)$id;
        return $this;
    }


    /**
     * Get the message's parent record ID.
     *
     * @return  integer     Parent message record ID
     */
    public function getParentId() : int
    {
        return (int)$this->parent_id;
    }


    /**
     * Set the message author's user ID.
     * Also sets the author name if a user ID > 0 is given.
     *
     * @param   integer $uid    Author user ID
     * @return  object  $this
     */
    public function withAuthorUid(int $uid) : self
    {
        global $_USER, $_TABLES;

        $this->author_uid = (int)$uid;
        if ($uid == $_USER['uid']) {
            $this->author_name = $_USER['username'];
        } elseif ($uid > 0) {
            $this->author_name = Database::getInstance()->getItem(
                $_TABLES['users'],
                'username',
                array('uid' => $uid)
            );
        }
        return $this;
    }


    /**
     * Get the message author's user ID.
     *
     * @return  integer     Author's user ID
     */
    public function getAuthorUid() : int
    {
        return (int)$this->author_uid;
    }


    /**
     * Get the message author's name.
     *
     * @return  string      Author's name
     */
    public function getAuthorName() : string
    {
        return $this->author_name;
    }


    /**
     * Set the message author's name.
     *
     * @param   string  $name   Author's name
     * @return  object  $this
     */
    public function withAuthorName(string $name) : self
    {
        $this->author_name = $name;
        return $this;
    }


    /**
     * Set the author's IP address.
     *
     * @param   string  $ip     Author's IP address
     * @return  object  $this
     */
    public function withAuthorIP(string $ip) : self
    {
        $this->author_ip = $ip;
        return $this;
    }


    /**
     * Set the message subject.
     *
     * @param   string  $subject    Message subjet
     * @return  objec   $this
     */
    public function withSubject(string $subject) : self
    {
        $this->message_subject = $subject;
        return $this;
    }


    /**
     * Set the message text.
     *
     * @param   string  $msg    Message text
     * @return  object  $this
     */
    public function withComment(string $msg) : self
    {
        $this->message_text = $msg;
        return $this;
    }


    /**
     * Add a single user ID to the To list.
     *
     * @param   intger  $uid    User ID
     * @return  object  $this
     */
    public function withToUser(int $uid) : self
    {
        return $this->_withUsers(array($uid), false);
    }


    /**
     * Add users to the To list by user ID.
     *
     * @param   array   $uids   Array of user IDs
     * @return  object  $this
     */
    public function withToUsers(array $uids) : self
    {
        return $this->_withUsers($uids, false);
    }


    /**
     * Get the list of To users as a comma-separated string
     *
     * @return  string      Comma-separate string of To users
     */
    public function getToUsersCSV() : string
    {
        $names = array();
        foreach ($this->toUsers as $user) {
            if ($user['pm_bcc'] == 0) {
                $names[] = $user['username'];
            }
        }
        return implode(',', $names);
    }


    public function getToAddress() : string
    {
        return $this->to_address;
    }


    public function getBccAddress() : string
    {
        return $this->bcc_address;
    }


    /**
     * Add a comma-separated list of usernames to the To list.
     *
     * @param   string  $namelist   Comma-separated list of names
     * @return  object  $this
     */
    public function withToUsersCSV(string $namelist) : self
    {
        $names = explode(',', $namelist);
        return $this->withToUserNames($names);
    }


    /**
     * Add a comma-separated list of usernames to the BCC list.
     *
     * @param   string  $namelist   Comma-separated list of names
     * @return  object  $this
     */
    public function withBccUsersCSV(string $namelist) : self
    {
        $names = explode(',', $namelist);
        return $this->withBccUserNames($names);
    }


    /**
     * Add users to the To list by name.
     *
     * @param   array   $names      User names
     * @return  object  $this
     */
    public function withToUserNames(array $names) : self
    {
        return $this->_withUserNames($names, false);
    }


    /**
     * Get the sending user's ID.
     *
     * @return  integer     Sender's user ID
     */
    public function getUserId() : int
    {
        return (int)$this->user_id;
    }


    /**
     * Get the message comment text
     *
     * @return  string      Message text
     */
    public function getComment() : string
    {
        return $this->message_text;
    }


    /**
     * Get the message subject.
     *
     * @return  string      Message subject
     */
    public function getSubject() : string
    {
        return $this->message_subject;
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
        $db = Database::getInstance();
        try {
            $res = $db->conn->executeQuery(
                "SELECT uid FROM {$_TABLES['users']}
                WHERE username IN (?)",
                array($names),
                array(Database::PARAM_STR_ARRAY)
            );
            $data = $res->fetchAll(Database::ASSOCIATIVE);
        } catch (\Throwable $e) {
            Log::write('system', Log::ERROR, __METHOD__ . ': ' . $e->getMessage());
            Log::write('system', Log::ERROR, 'Invalid names: ' . var_export($names,true));
            $data = false;
        }
        if (is_array($data)) {
            foreach ($data as $A) {
                $userids[] = $A['uid'];
            }
        }
        if (!empty($userids)) {
            $this->_withUsers($userids, $bcc);
        }
        return $this;
    }


    /**
     * Add a single BCC user ID.
     *
     * @param   integer $uid    User ID
     * @return  object  $this
     */
    public function withBccUser(int $uid) : self
    {
        return $this->_withUsers(array($uid), true);
    }


    /**
     * Set the BCC user ids.
     *
     * @param   array   $uids   Array of user IDs
     * @return  object  $this
     */
    public function withBccUsers(array $uids) : self
    {
        return $this->_withUsers($uids, true);
    }


    /**
     * Set the BCC user ids.
     *
     * @param   array   $uids   Array of user IDs
     * @return  object  $this
     */
    public function withBccUserNames(array $names) : self
    {
        return $this->_withUserNames($names, true);
    }


    /**
     * Add the supplied user IDs to either the To or BCC list.
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
            $db = Database::getInstance();
            $qb = $db->conn->createQueryBuilder();
            try {
                $to_users = $qb->select(
                    'u.uid', 'u.username', 'u.email',
                    'up.block', 'up.notify', "$bcc AS pm_bcc"
                )
                ->from($_TABLES['users'], 'u')
                ->leftJoin('u', $_TABLES['pm_userprefs'], 'up', 'u.uid=up.uid')
                ->where('u.uid IN (?)')
                ->setParameter(0, $uids, Database::PARAM_INT_ARRAY)
                ->execute()
                ->fetchAllAssociative();
            } catch (\Throwable $e) {
                Log::write('system', Log::ERROR, __METHOD__ . ': ' . $e->getMessage());
                $to_users = false;
            }
            if (is_array($to_users)) {
                foreach ($to_users as $user) {
                    if ($user['uid'] > 1) {
                        $this->toUsers[$user['uid']] = $user;
                    }
                }
            }
        }
        return $this;
    }


    /**
     * Add an error message to the array for later use.
     *
     * @param   string  $msg    Error message text
     */
    public function addError(string $msg) : void
    {
        $this->_errors[] = $msg;
    }


    /**
     * Get all the error messages that have been accumulated.
     *
     * @return  array       Array of error messages
     */
    public function getErrors() : array
    {
        return $this->_errors;
    }


    /**
     * Set the current folder being viewed.
     *
     * @param   string  $folder     Folder name
     * @return  object  $this
     */
    public function withFolder(string $folder) : self
    {
        $this->_folder = $folder;
        return $this;
    }


    /**
     * Set the current user ID.
     *
     * @param   integer $uid        User ID
     * @return  object  $this
     */
    public function withUid(int $uid) : self
    {
        $this->_uid = (int)$uid;
        return $this;
    }


    /**
     * Send the message.
     * Creates the database records, and calles notify() to send emails to
     * recipients who have notifications enabled.
     *
     * @return  boolean     True on success
     */
    public function send() : bool
    {
        global $LANG_PM_ERROR, $_TABLES, $_USER;

        $this->_checkBlocks();

        if (empty($this->toUsers)) {
            $this->addError($LANG_PM_ERROR['no_to_address']);
            return false;
        }

        $db = Database::getInstance();

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
            $this->addError($LANG_PM_ERROR['no_subject']);
        }

        if (strlen($this->message_text) < 4) {
            $this->addError = $LANG_PM_ERROR['no_message'];
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
        $reply_msgid = 0;
        $pm_replied = 0;

        // Add the message record
        $qb = $db->conn->createQueryBuilder();
        try {
            $qb->insert($_TABLES['pm_msg'])
               ->values(array(
                   'parent_id' => '?',
                   'author_uid' => '?',
                   'author_name' => '?',
                   'author_ip' => '?',
                   'message_time' => '?',
                   'message_subject' => '?',
                   'message_text' => '?',
                   'to_address' => '?',
                   'bcc_address' => '?',
               ))
               ->setParameter(0, $this->parent_id, Database::INTEGER)
               ->setParameter(1, $this->author_uid, Database::INTEGER)
               ->setParameter(2, $this->author_name, Database::STRING)
               ->setParameter(3, $remote_addr, Database::STRING)
               ->setParameter(4, time(), Database::INTEGER)
               ->setParameter(5, $subject, Database::STRING)
               ->setParameter(6, $this->message_text, Database::STRING)
               ->setParameter(7, $to_address, Database::STRING)
               ->setParameter(8, $bcc_address, Database::STRING)
               ->execute();
        } catch (\Throwable $e) {
            Log::write('system', Log::ERROR, __METHOD__ . ': ' . $e->getMessage());
            $this->addError($LANG_PM_ERROR['send_error']);
            return false;
        }
        $lastmsg_id = $db->conn->lastInsertID();

        // Add a mailbox record for each recipient and notify
        $qb = $db->conn->createQueryBuilder();
        $qb->insert($_TABLES['pm_dist'])
           ->values(array(
                'msg_id' => '?',
                'user_id' => '?',
                'username' => '?',
                'author_uid' => '?',
                'pm_bcc' => '?'
            ));
        foreach ($this->toUsers as $user) {
            try {
                $qb->setParameter(0, $lastmsg_id, Database::INTEGER)
                   ->setParameter(1, $user['uid'], Database::INTEGER)
                   ->setParameter(2, $user['username'], Database::STRING)
                   ->setParameter(3, $this->author_uid, Database::INTEGER)
                   ->setParameter(4, $user['pm_bcc'], Database::INTEGER)
                   ->execute();
            } catch (\Throwable $e) {
                Log::write('system', Log::ERROR, __METHOD__ . ': ' . $e->getMessage());
                $this->addError($LANG_PM_ERROR['send_error']);
            }
        }
        $this->notify();

        // Add an outbox message for the author, if not a system-generated message.
        if ($this->author_uid > 0) {
            $qb = $db->conn->createQueryBuilder();
            $qb->insert($_TABLES['pm_dist'])
               ->values(array(
                   'msg_id' => '?',
                   'user_id' => '?',
                   'username' => '?',
                   'author_uid' => '?',
                   'folder_name' => '?',
                   'pm_unread' => 0,
                   'pm_replied' => 0,
               ))
               ->setParameter(0, $lastmsg_id, Database::INTEGER)
               ->setParameter(1, $user['uid'], Database::INTEGER)
               ->setParameter(2, '', Database::STRING)
               ->setParameter(3, $this->author_uid, Database::INTEGER)
               ->setParameter(4, 'outbox', Database::STRING)
               ->execute();

            // update original record to show it has been replied...
            $qb = $db->conn->createQueryBuilder();
            $qb->update($_TABLES['pm_dist'])
               ->set('pm_replied', ':flag')
               ->where('msg_id = :msg_id')
               ->andWhere('user_id = :uid')
               ->andWhere("folder_name NOT IN ('outbox', 'sent')")
               ->setParameter('flag', 1, Database::INTEGER)
               ->setParameter('msg_id', $reply_msgid, Database::INTEGER)
               ->setParameter('uid', $this->author_uid, Database::INTEGER)
               ->execute();

            COM_updateSpeedlimit ('pm');
        }
        CACHE_remove_instance('stmenu');
        return true;
    }


    /**
     * Notify recipient by email, if they have notifications enabled.
     */
    public function notify() : void
    {
        global $_CONF, $_USER, $_TABLES, $LANG_PM_NOTIFY;

        $from = array($_CONF['noreply_mail'],$_CONF['site_name']);
        $subject =  $_CONF['site_name'] .' - '. $LANG_PM_NOTIFY['new_pm_notification'];

        // Format the message once for email
        USES_lib_bbcode();
        $parsers = array(
            array(
                array('block','inline','link','listitem',
                )
            )
        );

        $message = self::formatTextBlock($this->message_text, 'text', $parsers);
        // we don't have a stylesheet for email, so replace our div with the style...
        $message = str_replace(
            '<div class="quotemain">',
            '<div style="border: 1px dotted #000;border-left: 4px solid #8394B2;color:#465584;  padding: 4px;  margin: 5px auto 8px auto;">',
            $message
        );

        $Email = \glFusion\Notifier::getProvider('email');
        // build the template...
        $send = false;
        foreach ($this->toUsers as $user) {
            if ($user['notify']) {
                $send = true;
                $Email->addBCC((int)$user['uid'], $user['username'], $user['email']);
            }
        }
        if ($send) {
            $T = new \Template(pm_get_template_path());
            $T->set_file ('email', 'pm_notify.thtml');
            $T->set_var(array(
                'from_username'     => $this->author_name,
                'msg_subject'       => $this->message_subject,
                'site_name'         => $_CONF['site_name'] . ' - ' . $_CONF['site_slogan'],
                'site_url'          => $_CONF['site_url'],
                'pm_text'           => $message,
            ) );
            $T->parse('output','email');
            $messageHtml = $T->finish($T->get_var('output'));
            $Email->setMessage($messageHtml, true);
            $html2txt = new \Html2Text\Html2Text($message, false);
            $messageText = $html2txt->get_text();
            $Email->setMessage($messageText, false);
            $Email->setSubject($subject);
            $Email->send();
        }
    }


    /**
     * Mark this message as read.
     */
    public function markRead() : void
    {
        global $_TABLES, $_USER;

        $db = Database::getInstance();
        // Mark the message as read
        $db->conn->update(
            $_TABLES['pm_dist'],
            array('pm_unread' => 0),
            array(
                'msg_id' => $this->msg_id,
                'user_id' => $_USER['uid'],
            ),
            array(
                Database::INTEGER,
                Database::INTEGER,
                Database::INTEGER,
            )
        );

        $db->conn->update(
            $_TABLES['pm_dist'],
            array('folder_name' => 'sent'),
            array(
                'msg_id' => $this->msg_id,
                'user_id' => $this->author_uid,
                'folder_name' => 'outbox',
            ),
            array(
                Database::STRING,
                Database::INTEGER,
                Database::INTEGER,
                Database::STRING,
            )
        );
        CACHE_remove_instance('menu');
    }


    /**
     * Create the display view for the message.
     *
     * @param   integer $page   Page number
     * @return  string      HTML for display
     */
    public function render(int $page=1) : string
    {
        global $_USER, $_CONF, $LANG_PM00, $LANG_PM_ERROR;

        if ($this->msg_id == 0) {
            return $LANG_PM_ERROR['message_not_found'];
        }

        if (
            ($this->_folder == 'inbox' || $this->_folder == 'archive') &&
            $this->_pm_unread == 1
        ) {
            $this->markRead();
        }

        $Author = new Author($this->author_uid);
        $User = new Author($_USER['uid']);
        if ($this->author_uid == 0) {
            $Author->username = $LANG_PM00['system_message'];
        }

        $T = new \Template(pm_get_template_path());
        $T->set_file (array(
            'message'=>'message.thtml',
            'menu' => 'menu.thtml',
        ));

        // are they a friend?
        if ($this->author_uid > 0 && $this->author_uid != $_USER['uid'] ) {
            $T->set_var(array(
                'can_block' => !$Author->isAdmin(),
                'is_friend' => $User->hasFriend($this->author_uid) ? 1 : 0,
                'is_blocked' => $User->hasBlocked($this->author_uid) ? 1 : 0,
            ) );
        }

        $parsers = array(
            array(
                array('block', 'inline', 'listitem'),
                '_bbc_replacesmiley',
            ),
        );

        $message_history = Views\History::render($this->msg_id, 0);

        $formatted_msg_text = self::formatTextBlock($this->message_text,'text', $parsers);
        $dt = new \Date($this->message_time, $_USER['tzid']);
        if ($this->author_uid > 0) {
            $rank = \glFusion\Badges\Badge::getSingle($this->author_uid)->getHTML();
        } else {
            $rank = '';
        }
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
            'from_name'     => $this->author_name,
            'from_uid'      => $this->author_uid,
            'to_name'       => $this->to_address,
            'msg_id'        => $this->msg_id,
            'rank'          => $rank,
            'registered'    => $Author->regdate,
            'signature'     => nl2br($Author->sig),
            'homepage'      => $Author->homepage,
            'location'      => $Author->location,
            'email'         => $Author->emailfromuser ? $_CONF['site_url'].'/profiles.php?uid='.$this->author_uid : '',
            'message_history'   => $message_history,
            'can_reply'     => $this->author_uid > 0 && $this->_folder == 'inbox',
            'can_delete'    => true,
        ));

        $T->parse('menu', 'menu');
        $T->parse ('output', 'message');
        return $T->finish ($T->get_var('output'));
    }


    /**
     * Use the bbcode library to format text.
     *
     * @param   string  $str    Text to format
     * @param   string  $postmode   Text or HTML
     * @param   array   $parsers    Array of parsers
     * @return  string      Formatted text string
     */
    public static function formatTextBlock(string $str, string $postmode='text', array $parsers=array()): string
    {
        USES_lib_bbcode();
            $codes = array();

            //-sample code
            //    $codes[] = array('youtube', 'usecontent?', '_bbc_youtube', array ('usecontent_param' => 'default'),
            //                      'link', array ('listitem', 'block', 'inline'), array ('link'));
            return BBC_formatTextBlock($str,'text',$parsers,$codes);
    }


    /**
     * Delete a message.
     * If deleting from the Outbox, then messages are deleted from the Inbox
     * of any recipients that haven't read the message.
     *
     * @param   array|integer   $msgs   One or an array of message IDs
     * @param   string          $folder Folder name
     */
    public static function delete($msgs, $folder) : void
    {
        global $_TABLES, $_USER;

        $uid = (int)$_USER['uid'];
        if (!is_array($msgs)) {
            $msgs = array($msgs);
        }
        $db = Database::getInstance();
        if ($folder == 'outbox') {
            foreach ($msgs as $msg_id) {
                // We know no one has read it yet or it wouldn't be in our out box.
                // Delete from both the sender and recipient.
                $values = array('msg_id' => $msg_id);
                $types = array(Database::INTEGER);
                $db->conn->delete($_TABLES['pm_dist'], $values, $types);
                $db->conn->delete($_TABLES['pm_msg'], $values, $types);
            }
        } else {
            foreach ($msgs as $msg_id) {
                // Must be in our in, sent, or archive folder. Delete only our copy.
                $db->conn->delete(
                    $_TABLES['pm_dist'],
                    array(
                        'msg_id' => $msg_id,
                        'user_id' => $uid,
                        'folder_name' => $folder,
                    ),
                    array(
                        Database::INTEGER,
                        Database::INTEGER,
                        Database::STRING,
                    )
                );
                // Then delete the message only if no one else has it in their inbox.
                $msgCount = $db->getCount($_TABLES['pm_dist'],'msg_id', $msg_id, Database::INTEGER);
                if ($msgCount == 0) {
                    $db->conn->delete(
                        $_TABLES['pm_msg'],
                        array('msg_id' => $msg_id),
                        array(Database::INTEGER)
                    );
                }
            }
        }
    }


    /**
     * Archive one or more messages.
     *
     * @param   array|integer   $msgs   Single or an array of message IDs
     * @param   string          $folder Current folder name
     */
    public static function archive($msgs, $folder) : void
    {
        global $_TABLES, $_USER;

        $db = Database::getInstance();
        $qb = $db->conn->createQueryBuilder();
        $qb->update($_TABLES['pm_dist'])
           ->set('folder_name', 'archive')
           ->where('msg_id IN (:msgs)')
           ->andWhere('user_id = :uid')
           ->andWhere('folder_name = :folder')
           ->setParameter('uid', $_USER['uid'], Database::INTEGER)
           ->setParameter('folder', $folder, Database::STRING)
           ->setParameter('msgs', $msgs, Database::PARAM_INT_ARRAY)
           ->execute();
    }


    /**
     * Checked for users in the To array who have blocked this user.
     */
    private function _checkBlocks() : void
    {
        foreach ($this->toUsers as $to_uid=>$data) {
            $User = new Author($to_uid);
            if ($User->hasBlocked($this->author_uid)) {
                unset($this->toUsers[$to_uid]);
            }
        }
    }


    /**
     * Add the authors of the specified messages to the current user's block list.
     *
     * @param   array   $msg_ids    Array of message IDs
     */
    public static function block(array $msg_ids) : void
    {
        foreach ($msg_ids as $msg_id) {
            $Msg = self::getInstance($msg_id);
            Friend::blockUser($Msg->getAuthorUId());
        }
    }


    /**
     * Get all messages for a user in a given folder.
     *
     * @param   string  $folder     Folder name
     * @param   integer $uid        User ID, current user by default
     * @return  array       Array of Message objects
     */
    public static function getByFolder(string $folder, ?int $uid=NULL) : array
    {
        global $_TABLES, $_USER;

        if (empty($uid)) {
            $uid = $_USER['uid'];
        }

        $retval = array();
        $db = Database::getInstance();
        $qb = $db->conn->createQueryBuilder();
        $qb->select('msg.*')
           ->from($_TABLES['pm_dist'], 'dist')
           ->leftJoin('dist', $_TABLES['pm_msg'], 'msg', 'msg.msg_id = dist.msg_id')
           ->where('dist.folder_name = :folder')
           ->setParameter('folder', $folder, Database::STRING)
           ->setParameter('uid', $uid, Database::INTEGER);

        switch ($folder) {
        case 'inbox' :
        case 'archive':
            // Limit to the target user in the dist table and get 
            $qb->addSelect('u.username')
               ->leftJoin('dist', $_TABLES['users'], 'u', 'dist.author_uid = u.uid')
               ->andWhere('dist.user_id = :uid');
            break;
        case 'sent' :
        case 'outbox':
            $qb->andWhere('msg.author_uid = :uid');
            break;
        }

        try {
            $stmt = $qb->execute();
        } catch (\Throwable $e) {
            Log::write('system', Log::ERROR, __METHOD__ . ': ' . $e->getMessage());
            $stmt = false;
        }
        if ($stmt) {
            while ($A = $stmt->fetchAssociative()) {
                $retval[$A['msg_id']] = new self;
                $retval[$A['msg_id']]->setVars($A);
            }
        }
        return $retval;
    }


    /**
     * Format message data as XML for the privacy export.
     *
     * @return  string      XML for one message
     */
    public function toXML() : string
    {
        $retval = "<message>\n";
        $retval .= '<timestamp>' . $this->message_time . "</timestamp>\n";
        $retval .= '<from>' . $this->author_name . "</from>\n";
        $retval .= '<to>' . $this->to_address . "</to>\n";
        if (!empty($this->bcc_address)) {
            $retval .= '<bcc>' . $this->bcc_address . "</bcc>\n";
        }
        $retval .= '<subject>' . $this->message_subject . "</subject>\n";
        $retval .= '<content>' . $this->message_text . "</content>\n";
        $retval .= "</message>\n";
        return $retval;
    }

}

