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

    /** Friend's username.
     * @var string */
    private $friend_name = '';

    /** Flag indicating a friend (1) or blocked user (0).
     * @var boolean */
    private $is_friend = 1;


    /**
     * Get all the friends or blocked users by user ID.
     *
     * @param   integer $uid    User ID
     * @param   boolean $friends    True to get friends, False for blocked
     * @return  array       Array of friends or blocked users
     */
    public static function getByUser(int $uid, bool $friends=true) : array
    {
        global $_TABLES;

        $is_friend = $friends ? 1 : 0;
        $retval = array();
        $sql = "SELECT * FROM {$_TABLES['pm_friends']}
            WHERE uid = $uid"; // AND is_friend = $is_friend";
        $res = DB_query($sql);
        if ($res && DB_numRows($res) > 0) {
            while ($A = DB_fetchArray($res, false)) {
                $retval[$A['friend_id']] = self::fromArray($A);
            }
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

}

