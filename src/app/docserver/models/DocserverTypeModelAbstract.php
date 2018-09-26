<?php

/**
* Copyright Maarch since 2008 under licence GPLv3.
* See LICENCE.txt file at the root folder for more details.
* This file is part of Maarch software.
*
*/

/**
* @brief DocserverType Model
* @author dev@maarch.org
* @ingroup core
*/

namespace Docserver\models;

use SrcCore\models\DatabaseModel;
use SrcCore\models\ValidatorModel;

class DocserverTypeModelAbstract
{
    public static function get(array $aArgs = [])
    {
        ValidatorModel::arrayType($aArgs, ['select']);

        $aDocserverTypes = DatabaseModel::select([
            'select'    => empty($aArgs['select']) ? ['*'] : $aArgs['select'],
            'table'     => ['docserver_types'],
        ]);

        return $aDocserverTypes;
    }

    public static function getById(array $aArgs)
    {
        ValidatorModel::notEmpty($aArgs, ['id']);
        ValidatorModel::stringType($aArgs, ['id']);

        $aDocserverType = DatabaseModel::select([
            'select'    => empty($aArgs['select']) ? ['*'] : $aArgs['select'],
            'table'     => ['docserver_types'],
            'where'     => ['docserver_type_id = ?'],
            'data'      => [$aArgs['id']]
        ]);

        if (empty($aDocserverType[0])) {
            return [];
        }

        return $aDocserverType[0];
    }

    public static function create(array $aArgs = [])
    {
        ValidatorModel::notEmpty($aArgs, ['docserver_type_id']);
        ValidatorModel::stringType($aArgs, ['docserver_type_id']);

        DatabaseModel::insert([
            'table'         => 'docserver_types',
            'columnsValues' => $aArgs
        ]);

        return true;
    }

    public static function update(array $aArgs = [])
    {
        ValidatorModel::notEmpty($aArgs, ['docserver_type_id']);
        ValidatorModel::stringType($aArgs, ['docserver_type_id']);

        DatabaseModel::update([
            'table'     => 'docserver_types',
            'set'       => $aArgs,
            'where'     => ['docserver_type_id = ?'],
            'data'      => [$aArgs['docserver_type_id']]
        ]);

        return true;
    }

    public static function delete(array $aArgs)
    {
        ValidatorModel::notEmpty($aArgs, ['id']);
        ValidatorModel::stringType($aArgs, ['id']);

        DatabaseModel::delete([
            'table' => 'docserver_types',
            'where' => ['docserver_type_id = ?'],
            'data'  => [$aArgs['id']]
        ]);

        return true;
    }
}
