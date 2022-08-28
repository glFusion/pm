<?php

namespace PM;

class Folder
{
    private static $folderNames = array(
        'inbox',
        'outbox',
        'sent',
        'archive',
    );

    public static function fromParams(string $varname='folder') : string
    {

        if (isset($_GET[$varname])) {
            $folder = strtolower( COM_applyFilter($_GET[$varname]) );
        } elseif (isset($_POST[$varname]) ) {
            $folder = strtolower( COM_applyFilter($_POST[$varname]) );
        } else {
            $folder = 'inbox';
        }

        if (!in_array($folder,self::$folderNames)) {
            $folder = 'inbox';
        }
        return $folder;
    }
    

    public static function makeSelection(string $folder='') : string
    {
        global $LANG_PM00;

        $options = array();
        $folderSelect = FieldList::select(array(
            'id' => 'folder',
            'name' => 'folder',
            'options' => array(
                $LANG_PM00['inbox'] => array(
                    'value' => 'inbox',
                    'selected' => $folder == 'inbox',
                ),
                $LANG_PM00['outbox'] => array(
                    'value' => 'outbox',
                    'selected' => $folder == 'outbox',
                ),
                $LANG_PM00['sent'] => array(
                    'value' => 'sent',
                    'selected' => $folder == 'sent',
                ),
                $LANG_PM00['archive'] => array(
                    'value' => 'archive',
                    'selected' => $folder == 'archive',
                ),
            ),
        ) );
        return $folderSelect;
    } 

}

