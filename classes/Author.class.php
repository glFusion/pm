<?php
/**
 * Class to represent message authors.
 * Depends on the core glFusion User class (glFusion 2.0+).
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
 * Class to represent authors.
 * @package pm
 */
class Author extends \glFusion\User
{
    /** Array of Friend objects for friends.
     * @var array */
    private $Friends = NULL;

    /** Array of Friend objects for blocked users.
     * @var array */
    private $Blocked = NULL;


    /**
     * See if a given user ID is on the friends list for this author.
     *
     * @param   integer $uid    User ID of potential friend
     * @return  boolean     True if a friend, False if not
     */
    public function hasFriend(int $uid) : bool
    {
        if ($this->Friends === NULL) {
            $this->Friends = Friend::getByUser($this->uid);
        }
        return array_key_exists($uid, $this->Friends);
    }


    /**
     * See if a given user ID is on the friends list for this author.
     *
     * @param   integer $uid    User ID of potential friend
     * @return  boolean     True if a friend, False if not
     */
    public function hasBlocked(int $uid) : bool
    {
        if ($this->Blocked === NULL) {
            $this->Blocked  = Friend::getByUser($this->uid, false);
        }
        return array_key_exists($uid, $this->Blocked);
    }


    /**
     * Check if this author is a PM admin.
     *
     * @return  boolean     True for admins, False for regular users
     */
    public function isAdmin() : bool
    {
        return $this->hasRights('pm.admin');
    }

}

