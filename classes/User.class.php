<?php
/**
 * Class to represent site users.
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
 * Site user class.
 * @package pm
 */
class User extends \glFusion\User
{
    /** User ID having the friends.
     * @var integer */
    private $uid = 0;

    /** Block all users from sending PMs?
     * @var integer */
    private $block = 0;

    /** Notify by email when a PM is received?
     * @var integer */
    private $notify = 1;

    /** Flag indicating this is a new record.
     * @var boolean */
    private $_isNew = true;


    /**
     * Get a User object for the specified user ID.
     *
     * @param   integer $uid    User ID
     * @return  object      User Object
     */
    public static function getInstance(?int $uid = NULL) : self
    {
        global $_TABLES, $_USER;

        if (empty($uid)) {
            $uid = $_USER['uid'];
        }

        $db = Database::getInstance();
        try {
            $data = $db->conn->executeQuery(
                "SELECT * FROM {$_TABLES['pm_userprefs']} WHERE uid = ?",
                array($uid),
                array(Database::INTEGER)
            )->fetch(Database::ASSOCIATIVE);
        } catch (\Throwable $e) {
            Log::write('system', Log::ERROR, __METHOD__ . ': ' . $e->getMessage());
            $data = false;
        }

        $User = new self;
        if (is_array($data) && !empty($data)) {
            $User->setFound(true);
            $User->setMyVars($data);
        } else {
            $User->withUid($uid);
        }
        return $User;
    }


    /**
     * Get a user object for the current user.
     *
     * @return  object  User object
     */
    public static function getCurrent() : self
    {
        return self::getInstance();
        /*global $_USER;
        static $User = NULL;

        if ($User === NULL) {
            $User = new self($_USER['uid']);
        }
        return $User;*/
    }


    /**
     * Set the flag indicating that the user record was found.
     *
     * @param   boolean $flag   True if found, False if not
     * @return  object  $this
     */
    public function setFound(bool $flag=true) : self
    {
        $this->_isNew = $flag ? false : true;
        return $this;
    }


    /**
     * Set this user's preference variables.
     *
     * @param   array   $A      Array of properties to set
     * @return  object  $this
     */
    public function setMyVars(array $A) : self
    {
        foreach (array('uid', 'block', 'notify') as $key) {
            if (isset($A[$key])) {
                $this->$key = (int)$A[$key];
            }
        }
        return $this;
    }


    /**
     * Set the user ID.
     *
     * @param   integer $uid    User ID
     * @return  object  $this
     */
    public function withUid(int $uid) : self
    {
        $this->uid = $uid;
        return $this;
    }

    public function withBlock(int $block) : self
    {
        $this->block = $block;
        return $this;
    }

    public function getBlock() : bool
    {
        return $this->block;
    }

    public function withNotify(int $notify) : self
    {
        $this->notify = $notify;
        return $this;
    }

    public function getNotify() : bool
    {
        return $this->notify;
    }


    /**
     * Save this user's preferences.
     *
     * @param   array   $A      Optional array
     * @return  object  $this
     */
    public function save(?array $A=NULL) : self
    {
        global $_TABLES;

        if (is_array($A)) {
            $this->setMyVars($A);
        }

        if (empty($this->uid)) {
            return $this;
        }

        $qb = Database::getInstance()->conn->createQueryBuilder();
        if ($this->_isNew) {
            $qb->insert($_TABLES['pm_userprefs'])
               ->setValue('uid', ':uid')
               ->setValue('block', ':block')
               ->setValue('notify', ':notify');
        } else {
            $qb->update($_TABLES['pm_userprefs'])
               ->set('block', ':block')
               ->set('notify', ':notify')
               ->where('uid = :uid');
        }
        try {
            $qb->setParameter('uid', $this->uid, Database::INTEGER)
               ->setParameter('block', $this->block, Database::INTEGER)
               ->setParameter('notify', $this->notify, Database::INTEGER)
               ->execute();
        } catch (\Exception $e) {
            Log::write('system', Log::ERROR, __METHOD__ . ': ' . $e->getMessage());
        }
        return $this;
    }
    
    
    /**
     * Get the options for the site user address list.
     * Blocks those in the Friends table who have blocked this user,
     * and users that this user has blocked.
     *
     * @return  string      Option elements
     */
    public function getAddressOptions() : string
    {
        global $_TABLES;

        $sql = "SELECT DISTINCT user.uid, user.username, user.fullname, pref.block
            FROM {$_TABLES['users']} user
            LEFT JOIN {$_TABLES['pm_userprefs']} pref
                ON user.uid = pref.uid
            LEFT JOIN {$_TABLES['pm_friends']} friend
                ON (
                    (friend.uid = :uid AND friend.friend_id = user.uid) OR
                    (friend.uid = user.uid AND friend.friend_id = :uid)
                )
            WHERE user.uid > 1 AND user.status=3
            AND (friend.is_friend = 1 OR friend.is_friend IS NULL)
            AND user.uid <> :uid
            ORDER BY user.username ASC";
        $db = Database::getInstance();
        try {
            $stmt = $db->conn->executeQuery(
                $sql,
                array(':uid' => $this->uid),
                array(Database::INTEGER)
            );
        } catch(\Throwable $e) {
        }
        $data = $stmt->fetchAll(Database::ASSOCIATIVE);
        $retval = '';
        if (is_array($data)) {
            foreach ($data as $userRow) {
                if ($userRow['block'] != 1) {
                    $retval .= '<option value="'.$userRow['username'].'">'.$userRow['username'].'</option>' .LB;
                }
            }
        }
        return $retval;
    }

}

