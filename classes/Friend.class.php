<?php
/**
 * Class to represent friends and blocked users.
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
 * Friend or Blocked User class.
 * @package pm
 */
class Friend
{
    /** User ID having the friends.
     * @var integer */
    private $uid = 0;

    /** Friend's user ID.
     * @var integer */
    private $friend_id = 0;

    /** Flag indicating a friend (1) or blocked user (0).
     * @var boolean */
    private $is_friend = 1;

    /** Friend's username. Taken from the Users table, not in the address book.
     * @var string */
    private $friend_name = '';


    /**
     * Get all the friends or blocked users by user ID.
     *
     * @param   integer $uid    User ID
     * @param   boolean $friends    True to get friends, False for blocked
     * @return  array       Array of friends or blocked users
     */
    public static function getByUser(int $uid=0, bool $friends=true) : array
    {
        global $_TABLES, $_USER;

        if ($uid == 0) {
            $uid = $_USER['uid'];
        }
        $uid = (int)$uid;
        $is_friend = $friends ? 1 : 0;
        $retval = array();
        $qb = Database::getInstance()->conn->createQueryBuilder();
        try {
            $data = $qb->select('friend.uid', 'friend.friend_id', 'user.username as friend_name')
               ->from($_TABLES['pm_friends'], 'friend')
               ->leftJoin('friend', $_TABLES['users'], 'user', 'user.uid = friend.friend_id')
               ->where('friend.uid = :uid')
               ->andWhere('friend.is_friend = :is_friend')
               ->setParameter('uid', $uid, Database::INTEGER)
               ->setParameter('is_friend', $is_friend, Database::INTEGER)
               ->orderBy('user.username', 'ASC')
               ->execute()
               ->fetchAll(Database::ASSOCIATIVE);
        } catch (\Throwable $e) {
            Log::write('system', Log::ERROR, $e->getMessage());
            $data = array();
        }
        foreach ($data as $A) {
            $retval[$A['friend_id']] = self::fromArray($A);
        }
        return $retval;
    }


    /**
     * Load all the properties from an array of database fields.
     *
     * @param   array   $A      Database record
     * @return  object  Friend object
     */
    public static function fromArray(array $A) : self
    {
        $F = new self;
        $F->withUid((int)$A['uid'])
          ->withFriendId((int)$A['friend_id'])
          ->withFriendName($A['friend_name']);
        return $F;
    }


    public function withUid(int $uid) : self
    {
        $this->uid = (int)$uid;
        return $this;
    }

    public function getUid() : int
    {
        return (int)$this->uid;
    }

    public function withFriendId(int $fid) : self
    {
        $this->friend_id = $fid;
        return $this;
    }

    public function getFriendId() : int
    {
        return (int)$this->friend_id;
    }

    public function withFriendName(string $name) : self
    {
        $this->friend_name = $name;
        return $this;
    }

    public function getFriendName() : string
    {
        return $this->friend_name;
    }


    public static function getSelectOptions(int $uid=0)
    {
        $options = '';
        foreach (self::getByUser($uid) as $Friend) {
            $options .= '<option value="' . $Friend->getFriendName() . '">' .
                $Friend->getFriendName() . '</option>' .LB;
        }
        return $options;
    }


    /**
     * Add a friend ID to a user's friends or blocked list.
     *
     * @param   integer $friend_id  ID of friend or enemy
     * @param   boolean $is_friend  True if friend, False to block
     * @param   integer $uid        ID of user adding or blocking, default = current
     */
    private static function _addUser(int $friend_id, bool $is_friend, int $uid=0) : void
    {
        global $_TABLES, $_USER;

        if ($uid == 0) {
            $uid = $_USER['uid'];
        }
        $uid = (int)$uid;
        $friend_id = (int)$friend_id;
        $is_friend = $is_friend ? 1 : 0;
        // TODO: disable blocking admins
        if ($friend_id == $uid) {   // can't add yourself
            return;
        }
COM_errorLog("Adding $friend_id as a friend to $uid with status $is_friend");
        $db = Database::getInstance();
        try {
            $db->conn->executeUpdate(
                "INSERT INTO {$_TABLES['pm_friends']}
                (uid, friend_id, is_friend)
                VALUES (?, ?, ?)",
                array($uid, $friend_id, $is_friend),
                array(Database::INTEGER, Database::INTEGER, Database::INTEGER)
            );
        } catch (\Doctrine\DBAL\Exception\UniqueConstraintViolationException $k) {
            try {
                $db->conn->executeUpdate(
                    "UPDATE {$_TABLES['pm_friends']} SET
                    is_friend = ?
                    WHERE uid = ? AND friend_id = ?",
                    array($is_friend, $uid, $friend_id),
                    array(Database::INTEGER, Database::INTEGER, Database::INTEGER)
                );
            } catch (\Throwable $e) {
                Log::write('system', Log::ERROR, $e->getMessage());
            }
        } catch (\Throwable $e) {
            Log::write('system', Log::ERROR, $e->getMessage());
        }
    }


    /**
     * Delete a friend from the address book without blocking.
     *
     * @param   integer $uid    User to remove
     * @return  boolean     True on success, False on error
     */
    public static function remFriend(int $friend_id, int $uid = 0) : void
    {
        global $_TABLES, $_USER;

        if ($uid == 0) {
            $uid = $_USER['uid'];
        }
        $db = Database::getInstance();
        try {
            $db->conn->delete(
                $_TABLES['pm_friends'],
                array(
                    'uid' => $uid,
                    'friend_id' => $friend_id,
                ),
                array(Database::INTEGER, Database::INTEGER)
            );
        } catch (\Throwable $e) {
            Log::write('system', Log::ERROR, $e->getMessage());
        }
    }


    /**
     * Block a user from sending messages or appearing in the address lists.
     *
     * @uses    self::_addUser()
     * @param   integer $friend_id  ID of user to be blocked
     * @param   integer $uid        ID of user blocking, default = current user
     */
    public static function blockUser(int $friend_id, int $uid=0) : void
    {
        self::_addUser($friend_id, 0, $uid);
    }


    /**
     * Unblock a user by removing from the address book.
     *
     * @param   integer $friend_id  User to unblock
     * @param   integer $uid        User unblocking, default is current user
     * @return  boolean     True on success, False on error
     */
    public static function unblockUser(int $friend_id, int $uid = 0) : void
    {
        global $_TABLES, $_USER;

        if ($uid == 0) {
            $uid = $_USER['uid'];
        }
        $db = Database::getInstance();
        try {
            $db->conn->delete(
                $_TABLES['pm_friends'],
                array(
                    'uid' => $uid,
                    'friend_id' => $friend_id,
                    'is_friend' => 0,
                ),
                array(Database::INTEGER, Database::INTEGER, Database::INTEGER)
            );
        } catch (\Throwable $e) {
            Log::write('system', Log::ERROR, $e->getMessage());
        }
    }


    /**
     * Add a user to the friends list.
     *
     * @uses    self::_addUser()
     * @param   integer $friend_id  ID of user to be added as a friend
     * @param   integer $uid        ID of user adding, default = current user
     */
    public static function addFriend(int $friend_id, int $uid=0) : void
    {
        self::_addUser($friend_id, 1, $uid);
    }


    public static function processAddressBook(array $post) : void
    {
        global $_USER, $_TABLES;

        $uid = (int)$_USER['uid'];
        $vals = array();
        $db = Database::getInstance();
        $db->conn->delete(
            $_TABLES['pm_friends'],
            array('uid' => $uid),
            array(Database::INTEGER)
        );

        if (isset($post['selected_friends']) && !empty($post['selected_friends'])) {
            $friend_ids = explode('|', $post['selected_friends']);
            foreach ($friend_ids as $friend_id) {
                $friend_id = (int)$friend_id;
                if ($friend_id > 0) {
                    $vals[] = "($uid, $friend_id, 1)";
                }
            }
        }
        if (isset($post['selected_enemies']) && !empty($post['selected_enemies'])) {
            $friend_ids = explode('|', $post['selected_enemies']);
            foreach ($friend_ids as $friend_id) {
                $friend_id = (int)$friend_id;
                if ($friend_id > 0) {
                    $vals[] = "($uid, $friend_id, 0)";
                }
            }
        }

        if (!empty($vals)) {
            $vals = implode(',', $vals);
            try {
                $db->conn->executeUpdate(
                    "INSERT IGNORE INTO {$_TABLES['pm_friends']}
                        (uid, friend_id, is_friend)
                        VALUES $vals
                        ON DUPLICATE KEY UPDATE is_friend = VALUES(is_friend)"
                );
            } catch (\Throwable $e) {
                Log::write('system', Log::ERROR, $e->getMessage());
            }
        }
    }

}

