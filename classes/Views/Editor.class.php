<?php
/**
 * Class to create a message editor view.
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
use PM\Friend;
use PM\User;


/**
 * Message editor class.
 * @package pm
 */
class Editor
{
    /** Message ID, zero for a new message.
     * @var integer */
    private $msgid = 0;

    /** ID of the message being replied to.
     * @var integer */
    private $reply_msgid = 0;

    /** To address string.
     * @var string */
    private $to = '';

    /** Message subject.
     * @var string */
    private $subject = '';

    /** Preview text.
     * @var string */
    private $preview_text = '';

    /** Message object.
     * @var object */
    private $Message = NULL;


    /**
     * Load the message object if a message ID is provided.
     *
     * @param   integer $msg_id     Message ID
     */
    public function __construct(int $msgid = 0)
    {
        if ($msgid > 0) {
            $this->Message = Message::getInstance($msgid);
        }
    }


    /**
     * Set the message object.
     *
     * @param   object  $Msg    Message
     * @return  object  $this
     */
    public function withMessage(Message $Msg) : self
    {
        $this->Message = $Msg;
        return $this;
    }


    /**
     * Create a message editor from the contents of $_POST.
     *
     * @param   array   $post       $_POST array
     * @return  object  $this
     */
    public function fromPost(array $post) : self
    {
        $this->Message = Message::fromPost($post);
        if (isset($post['username_list']) && !empty($post['username_list'])) {
            $this->Message->withToUsersCSV($post['username_list']);
        }
        return $this;
    }


    /**
     * Set the To username.
     *
     * @param   string  $to     Username
     * @return  object  $this
     */
    public function withToName(string $to) : self
    {
        $this->to = $to;
        return $this;
    }


    /**
     * Create the editor form.
     *
     * @return  string      HTML for editor
     */
    public function render() : string
    {
        global $_CONF, $_TABLES, $_USER, $LANG_PM00;

        $retval = '';

        $errors = array();
        $T = new \Template(pm_get_template_path());
        $T->set_file (array (
            'compose'    =>  'compose.thtml',
        ));

        $friendselect_options = Friend::getSelectOptions();
        $userselect_options = User::getCurrent()->getAddressOptions();

        $additionalCodes = array();
        $bbcodeEditor = BBC_editor(
            $this->Message->getComment(),
            'compose_form',
            'comment',
            $additionalCodes
        );

        $T->set_var(array(
            'to'    => htmlentities($this->Message->getToUsersCSV(), ENT_QUOTES, COM_getEncodingt()),
            'message_subject' => htmlentities($this->Message->getSubject(), ENT_QUOTES, COM_getEncodingt()),
            'friendselect_options' => $friendselect_options,
            'userselect_options' => $userselect_options,
            'parent_id' => $this->Message->getParentId(),
            'msg_id'    => $this->msgid,
            'message_text' => $this->Message->getComment(),
            'editor'      => $bbcodeEditor,
            'LANG_bhelp'   => $LANG_PM00['b_help'],
            'LANG_ihelp'   => $LANG_PM00['i_help'],
            'LANG_uhelp'   => $LANG_PM00['u_help'],
            'LANG_qhelp'   => $LANG_PM00['q_help'],
            'LANG_chelp'   => $LANG_PM00['c_help'],
            'LANG_lhelp'   => $LANG_PM00['l_help'],
            'LANG_ohelp'   => $LANG_PM00['o_help'],
            'LANG_phelp'   => $LANG_PM00['p_help'],
            'LANG_whelp'   => $LANG_PM00['w_help'],
            'LANG_ahelp'   => $LANG_PM00['a_help'],
            'LANG_shelp'   => $LANG_PM00['s_help'],
            'LANG_fhelp'   => $LANG_PM00['f_help'],
            'LANG_hhelp'   => $LANG_PM00['h_help'],
            'LANG_thelp'   => $LANG_PM00['t_help'],
            'LANG_ehelp'   => $LANG_PM00['e_help'],
            'LANG_fontcolor'    => $LANG_PM00['FONTCOLOR'],
            'LANG_fontsize'     => $LANG_PM00['FONTSIZE'],
            'LANG_closetags'    => $LANG_PM00['CLOSETAGS'],
            'LANG_codetip'      => $LANG_PM00['CODETIP'],
            'LANG_tiny'         => $LANG_PM00['TINY'],
            'LANG_small'        => $LANG_PM00['SMALL'],
            'LANG_normal'       => $LANG_PM00['NORMAL'],
            'LANG_large'        => $LANG_PM00['LARGE'],
            'LANG_huge'         => $LANG_PM00['HUGE'],
            'LANG_default'      => $LANG_PM00['DEFAULT'],
            'LANG_dkred'        => $LANG_PM00['DKRED'],
            'LANG_red'          => $LANG_PM00['RED'],
            'LANG_orange'       => $LANG_PM00['ORANGE'],
            'LANG_brown'        => $LANG_PM00['BROWN'],
            'LANG_yellow'       => $LANG_PM00['YELLOW'],
            'LANG_green'        => $LANG_PM00['GREEN'],
            'LANG_olive'        => $LANG_PM00['OLIVE'],
            'LANG_cyan'         => $LANG_PM00['CYAN'],
            'LANG_blue'         => $LANG_PM00['BLUE'],
            'LANG_dkblue'       => $LANG_PM00['DKBLUE'],
            'LANG_indigo'       => $LANG_PM00['INDIGO'],
            'LANG_violet'       => $LANG_PM00['VIOLET'],
            'LANG_white'        => $LANG_PM00['WHITE'],
            'LANG_black'        => $LANG_PM00['BLACK'],
        ));

        $errors = $this->Message->getErrors();
        if (count($errors) > 0) {
            $T->set_var('error_messages', '<ul><li>' . implode('</li><li>', $errors) . '</li></ul>');
        }

        if ( $this->preview_text != '' ) {
            $T->set_var('preview_text',$this->preview_text);
        }

        if ( function_exists('msg_showsmilies') ) {
            $T->set_var('smilies',msg_showsmilies());
            $T->set_var('smilies_enabled',true);
        }

        $T->set_var('gltoken', SEC_createToken());
        $T->set_var('gltoken_name', CSRF_TOKEN);

        $T->parse ('output', 'compose');
        $retval .= $T->finish ($T->get_var('output'));
        return $retval;
    }

}

