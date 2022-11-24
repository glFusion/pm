<?php
/**
 * Class to display a mailbox listing.
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
use PM\FieldList;


/**
 * Class to display a mailbox listing.
 * @package pm
 */
class Mailbox
{
    /** Folder being displayed.
     * @var string */
    private $folder = 'inbox';


    /**
     * Set the folder to display.
     *
     * @param   string  $folder Folder name
     * @return  object  $this
     */
    public function withFolder(string $folder='inbox') : self
    {
        $this->folder = $folder;
        return $this;
    }


    /**
     * Display the mailbox listing.
     *
     * @return  string      HTML for listing
     */
    public function render() : string
    {
        global $_USER, $LANG_PM00, $_TABLES, $_CONF;

        USES_lib_admin();
        
        $uid = (int)$_USER['uid'];  // probably unnecessary sanitization
        switch ($this->folder) {
        case 'inbox' :
        case 'archive':
            $tofrom = $LANG_PM00['from'];
            $tofrom_field = 'author_name';
            $sql  = "SELECT msg.*, dist.pm_unread, u.username
                FROM {$_TABLES['pm_dist']} dist
                LEFT JOIN {$_TABLES['pm_msg']} msg ON msg.msg_id=dist.msg_id
                LEFT JOIN {$_TABLES['users']} u ON dist.author_uid = u.uid
                WHERE dist.user_id = $uid AND dist.folder_name = '{$this->folder}'";
            break;
        case 'sent' :
        case 'outbox':
            $tofrom = $LANG_PM00['to'];
            $tofrom_field = 'to_address';
            $sql  = "SELECT msg.*, dist.pm_unread FROM {$_TABLES['pm_msg']} msg
                LEFT JOIN {$_TABLES['pm_dist']} dist ON msg.msg_id=dist.msg_id
                WHERE msg.author_uid = $uid AND dist.folder_name = '{$this->folder}'";
            break;
        }

        $header_arr = array(
            array(
                'text' => $tofrom,
                'field' => $tofrom_field,
                'sort' => true,
                'align' => 'left',
            ),
            array(
                'text' => $LANG_PM00['subject'],
                'field' => 'message_subject',
                'sort' => true,
                'align' => 'left',
            ),
            array(
                'text' => $LANG_PM00['date'],
                'field' => 'message_time',
                'sort'=> true,
                'align' => 'right',
            ),
        );
        $defsort_arr = array('field' => 'message_time', 'direction' => 'desc');

        $text_arr = array(
            'form_url'      => $_CONF['site_url'] . '/pm/index.php',
            'help_url'      => '',
            'has_search'    => true,
            'has_limit'     => true,
            'has_paging'    => true,
        );

        $form_arr = array(
            'top' => '<input type="hidden" name="current_folder" value="' . $this->folder . '">' . LB .
                '<input type="hidden" name="folder" value="'.$this->folder.'">',
        );

        $arc_action = FieldList::archiveButton(array(
            'name' => 'archive_marked',
            'text' => $LANG_PM00['archive_marked'],
            'attr' => array(
                'onclick' => "return confirm('{$LANG_PM00['archive_confirm']}');",
            ),
        ) );
        $del_action = FieldList::deleteButton(array(
            'name' => 'delete_marked',
            'text' => $LANG_PM00['delete_marked'],
            'attr' => array(
                'onclick' => "return confirm('{$LANG_PM00['delete_confirm']}');",
            ),
        ) );
        $blk_action = FieldList::blockButton(array(
            'name' => 'block_marked',
            'text' => $LANG_PM00['block_user'],
            'attr' => array(
                'onclick' => "return confirm('{$LANG_PM00['block_confirm']}');",
            ),
        ) );
        $option_arr = array(
            'chkselect' => true,
            'chkfield' => 'msg_id',
            'chkname' => 'marked_msg_id',
            'chkminimum' => 0,
            'chkall' => true,
            'chkactions' => $arc_action . '&nbsp;&nbsp;' . $del_action . '&nbsp;&nbsp;' . $blk_action,
        );
        
        $query_arr = array(
            'table' => $_TABLES['pm_msg'],
            'sql' => $sql,
            'query_fields' => array(
                'message_subject', 'message_text', 'author_name', 'to_address',
            ),
            'default_filter' => ''
        );

        $msg_list = ADMIN_list(
            'mailbox',
            array(__CLASS__, 'getListField'),
            $header_arr, $text_arr, $query_arr, $defsort_arr,'','',$option_arr,$form_arr
        );
        return $msg_list;
    }


    /**
     * Get a list field for the mailbox listing.
     *
     * @param   string  $fieldname  Name of field
     * @param   mixed   $fieldvalue Field value
     * @param   array   $A          Array of all field-value pairs
     * @param   array   $icon_arr   Array of icon images (not used)
     */
    public static function getListField($fieldname, $fieldvalue, $A, $icon_arr)
    {
        global $_CONF, $_USER, $folder;

        static $dt = NULL;
        $retval = '';

        if ($dt === NULL) {
            $dt = new \Date('now',$_USER['tzid']);
        }

        switch ($fieldname) {
        case 'message_time' :
            $dt->setTimestamp($fieldvalue);
            $retval = $dt->format($dt->getUserFormat(),true);
            if ( $A['pm_unread'] == 1 ) {
                $retval = '<strong>'.$retval.'</strong>';
            }
            break;
        case 'msg_id' :
            return $fieldvalue;
            break;
        case 'author_name' :
            if (!empty($A['username'])) {
                // Message from a valid user, override the author name
                // in case the sender's username was changed.
                $fieldvalue = $A['username'];
            }
        case 'message_subject' :
            $retval = '<a href="'.$_CONF['site_url'].'/pm/view.php?msgid='.$A['msg_id'].'&amp;folder='.$folder.'">'.$fieldvalue.'</a>';
            if ( $A['pm_unread'] == 1 ) {
                $retval = '<strong>'.$retval.'</strong>';
            }
            break;
        case 'to_address':
            $retval = COM_truncate($fieldvalue, 15, '...');
            break;
        default :
            $retval = $fieldvalue;
            break;
        }

        return $retval;
    }

}

