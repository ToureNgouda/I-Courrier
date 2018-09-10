<?php
/**
* Copyright Maarch since 2008 under licence GPLv3.
* See LICENCE.txt file at the root folder for more details.
* This file is part of Maarch software.

* @brief   FirstLevelModelAbstract
* @author  dev <dev@maarch.org>
* @ingroup core
*/

namespace Doctype\models;

use SrcCore\models\ValidatorModel;
use SrcCore\models\CoreConfigModel;
use SrcCore\models\DatabaseModel;

class FirstLevelModelAbstract
{
    public static function get(array $aArgs = [])
    {
        ValidatorModel::arrayType($aArgs, ['select']);

        $firstLevel = DatabaseModel::select([
            'select'   => empty($aArgs['select']) ? ['*'] : $aArgs['select'],
            'table'    => ['doctypes_first_level'],
            'where'    => ['enabled = ?'],
            'data'     => ['Y'],
            'order_by' => ['doctypes_first_level_id asc']
        ]);

        return $firstLevel;
    }

    public static function getById(array $aArgs = [])
    {
        ValidatorModel::notEmpty($aArgs, ['id']);
        ValidatorModel::intVal($aArgs, ['id']);

        $aReturn = DatabaseModel::select(
            [
            'select' => empty($aArgs['select']) ? ['*'] : $aArgs['select'],
            'table'  => ['doctypes_first_level'],
            'where'  => ['doctypes_first_level_id = ?'],
            'data'   => [$aArgs['id']]
            ]
        );

        if (empty($aReturn[0])) {
            return [];
        }

        $aReturn = $aReturn[0];
       
        return $aReturn;
    }

    public static function create(array $aArgs)
    {
        ValidatorModel::notEmpty($aArgs, ['doctypes_first_level_label']);

        $aArgs['doctypes_first_level_id'] = DatabaseModel::getNextSequenceValue(['sequenceId' => 'doctypes_first_level_id_seq']);
        DatabaseModel::insert([
            'table'         => 'doctypes_first_level',
            'columnsValues' => $aArgs
        ]);

        return $aArgs['doctypes_first_level_id'];
    }

    public static function update(array $aArgs)
    {
        ValidatorModel::notEmpty($aArgs, ['doctypes_first_level_id']);
        ValidatorModel::intVal($aArgs, ['doctypes_first_level_id']);
        
        DatabaseModel::update([
            'table'     => 'doctypes_first_level',
            'set'       => $aArgs,
            'where'     => ['doctypes_first_level_id = ?'],
            'data'      => [$aArgs['doctypes_first_level_id']]
        ]);

        return true;
    }
}
