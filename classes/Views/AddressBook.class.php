<?php
/**
 * Show the Address Book page.
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
use PM\Friend;
use glFusion\Feature;
use glFusion\Database\Database;


class AddressBook
{
    /**
     * Create the address book management page.
     *
     * @return  string      HTML for page
     */
    public static function manage() : string
    {
        global $_CONF, $_PM_CONF, $_USER, $_TABLES, $LANG_PM00;

        $retval = '';

        $T = new \Template(pm_get_template_path());
        $T->set_file (array (
            'list' => 'friends.thtml',
            'menu' => 'menu.thtml',
        ));

        $uid = (int)$_USER['uid'];
        $have_selected = array($uid);   // track all previous selections

        // Build friends list
        $Friends = Friend::getByUser($uid);
        $friendselect = '';
        foreach ($Friends as $Friend) {
            $have_selected[] = $Friend->getFriendId();
            $friendselect .= '<option value="'.$Friend->getFriendId() . '">' .
                $Friend->getFriendName() . '</option>' . LB;
        }

        // Build blocked list
        $Enemies = Friend::getByUser($uid, false);
        $enemyselect = '';
        foreach ($Enemies as $Enemy) {
            $have_selected[] = $Enemy->getFriendId();
            $enemyselect .= '<option value="'.$Enemy->getFriendId() . '">' .
                $Enemy->getFriendName() . '</option>' . LB;
        }

        $have_selected = implode(',', $have_selected);
        $Feature = Feature::getByName('pm.user');
        $Groups = $Feature->getGroups();
        $groupList = implode(',', $Groups);
        $userselect = '';
        $sql = "SELECT DISTINCT u.uid, u.username, u.fullname
            FROM {$_TABLES['group_assignments']} ga
            LEFT JOIN {$_TABLES['users']} u
            ON u.uid = ga.ug_uid
            WHERE u.uid > 1 AND u.status=3
            AND u.uid NOT IN (?)
            AND ga.ug_main_grp_id IN (?)
            ORDER BY username ASC";
        $db = Database::getInstance();
        try {
            $stmt = $db->conn->executeQuery(
                $sql,
                array($have_selected, $groupList),
                array(DATABASE::STRING, DATABASE::STRING)
            );
        } catch(\Throwable $e) {
        }
        $data = $stmt->fetchAll(Database::ASSOCIATIVE);
        if (is_array($data)) {
            foreach ($data as $userRow) {
                $userselect .= '<option value="'.$userRow['uid'].'">'.$userRow['username'].'</option>' .LB;
            }
        }

        $T->set_var(array(
            'pm_home'       => $LANG_PM00['pm_index'],
            'friend_options' => $friendselect,
            'user_options'  => $userselect,
            'blocked_options' => $enemyselect,
            'gltoken'       => SEC_createToken(),
            'gltoken_name'  => CSRF_TOKEN,
            'folder' => 'inbox',
            'return_link' => PM_URL . '/index.php',
        ) );
        $T->parse('menu', 'menu');
        $T->parse('output', 'list');
        $retval .= $T->finish ($T->get_var('output'));
        return $retval;
    }

}

