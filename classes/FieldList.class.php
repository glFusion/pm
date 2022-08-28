<?php
/**
 * Class to create custom admin list fields.
 *
 * @author      Lee Garner <lee@leegarner.com>
 * @copyright   Copyright (c) 2021 Lee Garner <lee@leegarner.com>
 * @package     membership
 * @version     v0.3.0
 * @license     http://opensource.org/licenses/gpl-2.0.php
 *              GNU Public License v2 or later
 * @filesource
 */
namespace PM;


/**
 * Class to handle custom fields.
 * @package membership
 */
class FieldList extends \glFusion\FieldList
{
    private static $t = NULL;

    protected static function init()
    {
        global $_CONF;

        static $t = NULL;
        if (self::$t === NULL) {
            $t = new \Template($_CONF['path'] .'/plugins/pm/templates/');
            $t->set_file('field','fieldlist.thtml');
        }
        return $t;
    }


    /**
     * Create a button to archive messages.
     *
     * @param   array   $args   Arguments for the button
     * @return  string      HTML for the button
     */
    public static function archiveButton($args)
    {
        $t = self::init();
        $t->set_block('field','field-archive-button');

        $t->set_var('button_name',$args['name']);
        $t->set_var('text',$args['text']);

        if (isset($args['attr']) && is_array($args['attr'])) {
            $t->set_block('field-archive-button','attr','attributes');
            foreach($args['attr'] AS $name => $value) {
                $t->set_var(array(
                    'name' => $name,
                    'value' => $value)
                );
                $t->parse('attributes','attr',true);
            }
        }
        $t->parse('output','field-archive-button',true);
        $t->clear_var('attributes');
        return $t->finish($t->get_var('output'));
    }

    /**
     * Create a button to block users.
     *
     * @param   array   $args   Arguments for the button
     * @return  string      HTML for the button
     */
    public static function blockButton($args)
    {
        $t = self::init();
        $t->set_block('field','field-block-button');

        $t->set_var('button_name',$args['name']);
        $t->set_var('text',$args['text']);

        if (isset($args['attr']) && is_array($args['attr'])) {
            $t->set_block('field-block-button','attr','attributes');
            foreach($args['attr'] AS $name => $value) {
                $t->set_var(array(
                    'name' => $name,
                    'value' => $value)
                );
                $t->parse('attributes','attr',true);
            }
        }
        $t->parse('output','field-block-button',true);
        $t->clear_var('attributes');
        return $t->finish($t->get_var('output'));
    }

}
