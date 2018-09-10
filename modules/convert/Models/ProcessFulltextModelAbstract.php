<?php

/**
* Copyright Maarch since 2008 under licence GPLv3.
* See LICENCE.txt file at the root folder for more details.
* This file is part of Maarch software.
*
*/

/**
* @brief ProcessFulltext Model
* @author dev@maarch.org
* @ingroup convert
*/

namespace Convert\Models;

use History\controllers\HistoryController;
use SrcCore\models\DatabaseModel;
use SrcCore\models\ValidatorModel;

class ProcessFulltextModelAbstract
{
    /**
     * Updating the database with the location information of the document on the
     * new docserver
     * @param string $collId collection
     * @param string $resTable res table
     * @param string $adrTable adr table
     * @param bigint $resId Id of the resource to process
     * @param docserver $docserver docserver object
     * @param string $path location of the resource on the docserver
     * @param string $fileName file name of the resource on the docserver
     * @param complex $zendIndex zend index object
     * @return array $returnArray the result
     */
    public static function updateDatabase(array $aArgs = [])
    {
        try {
            ValidatorModel::notEmpty($aArgs, ['collId']);
            ValidatorModel::notEmpty($aArgs, ['resTable']);
            ValidatorModel::notEmpty($aArgs, ['adrTable']);
            ValidatorModel::intVal($aArgs, ['resId']);
            ValidatorModel::notEmpty($aArgs, ['docserver']);
            ValidatorModel::notEmpty($aArgs, ['path']);
            ValidatorModel::notEmpty($aArgs, ['fileName']);

            $aArgs['docserver']['path_template'] = str_replace(
                DIRECTORY_SEPARATOR, 
                '#', 
                $aArgs['docserver']['path_template']
            );
            $aArgs['path'] = str_replace(
                $aArgs['docserver']['path_template'], 
                '', 
                $aArgs['path']
            );

            DatabaseModel::update([
                'table'     => 'convert_stack',
                'set'       => [
                    'status'    => 'P'
                ],
                'where'     => ['coll_id = ?', 'res_id = ?'],
                'data'      => [$aArgs['collId'], $aArgs['resId']]
            ]);

            $returnAdr = DatabaseModel::select([
                'select'    => ['*'],
                'table'     => [$aArgs['adrTable']],
                'where'     => ['res_id = ?'],
                'data'      => [$aArgs['resId']],
                'order'      => ['adr_priority'],
            ]);

            if (empty($returnAdr)) {
                $returnRes = DatabaseModel::select([
                    'select'    => ['docserver_id, path, filename, offset_doc, fingerprint'],
                    'table'     => [$aArgs['resTable']],
                    'where'     => ['res_id = ?'],
                    'data'      => [$aArgs['resId']]
                ]);
                $returnRes = $returnRes[0];
                // LogsController::info(['message'=>$returnRes, 'code'=>8, ]);
                $resDocserverId = $returnRes['docserver_id'];
                $resPath = $returnRes['path'];
                $resFilename = $returnRes['filename'];
                $resOffsetDoc = $returnRes['offset_doc'];
                $fingerprintInit = $returnRes['fingerprint'];

                $returnDs = DatabaseModel::select([
                    'select'    => ['adr_priority_number'],
                    'table'     => ['docservers'],
                    'where'     => ['docserver_id = ?'],
                    'data'      => [$resDocserverId]
                ]);

                DatabaseModel::insert([
                    'table'         => $aArgs['adrTable'],
                    'columnsValues' => [
                        'res_id'        => $aArgs['resId'],
                        'docserver_id'  => $resDocserverId,
                        'path'          => $resPath,
                        'filename'      => $resFilename,
                        'offset_doc'    => $resOffsetDoc,
                        'fingerprint'   => $fingerprintInit,
                        'adr_priority'  => $returnDs[0]['adr_priority_number'],
                    ]
                ]);
            }

            $returnAdr = DatabaseModel::select([
                'select'    => ['*'],
                'table'     => [$aArgs['adrTable']],
                'where'     => ['res_id = ?', 'adr_type= ?'],
                'data'      => [$aArgs['resId'], 'TXT'],
            ]);
            
            if (empty($returnAdr)) {
                DatabaseModel::insert([
                    'table'         => $aArgs['adrTable'],
                    'columnsValues' => [
                        'res_id'        => $aArgs['resId'],
                        'docserver_id'  => $aArgs['docserver']['docserver_id'],
                        'path'          => $aArgs['path'],
                        'filename'      => $aArgs['fileName'],
                        'offset_doc'    => '',
                        'fingerprint'   => '',
                        'adr_priority'  => $aArgs['docserver']['adr_priority_number'],
                        'adr_type'      => 'TXT',
                    ]
                ]);
            } else {
                DatabaseModel::update([
                    'table'     => $aArgs['adrTable'],
                    'set'       => [
                        'docserver_id'  => $aArgs['docserver']['docserver_id'],
                        'path'          => $aArgs['path'],
                        'filename'      => $aArgs['fileName'],
                        'offset_doc'    => '',
                        'fingerprint'   => '',
                        'adr_priority'  => $aArgs['docserver']['adr_priority_number'],
                    ],
                    'where'     => ['res_id = ?', "adr_type = ?"],
                    'data'      => [$aArgs['resId'], 'TXT']
                ]);
            }

            HistoryController::add([
                'tableName' => $aArgs['resTable'],
                'recordId'  => (string) $aArgs['resId'],
                'eventType' => 'ADD',
                'info'      => 'process fulltext done',
                'moduleId'  => 'convert',
                'eventId'   => 'fulltext',
            ]);

            $queryCpt = DatabaseModel::select([
                'select'    => ["fulltext_attempts"],
                'table'     => [$aArgs['resTable']],
                'where'     => ['res_id = ?'],
                'data'      => [$aArgs['resId']],
            ]);

            $cptFulltext = $queryCpt[0]['fulltext_attempts'] + 1;

            if (!empty($aArgs['zendIndex'])) {
                $fResult = 1;
            } else {
                $fResult = 0;
            }

            DatabaseModel::update([
                'table'     => $aArgs['resTable'],
                'set'       => [
                    'fulltext_result'     => $fResult,
                    'is_multi_docservers' => 'Y',
                    'fulltext_attempts'   => $cptFulltext,
                ],
                'where'     => ['res_id = ?'],
                'data'      => [$aArgs['resId']]
            ]);

            $returnArray = array(
                'status' => '0',
                'value' => '',
                'error' => '',
            );
            
            return $returnArray;
        } catch (Exception $e) {
            $returnArray = array(
                'status' => '1',
                'value' => '',
                'error' => $e->getMessage(),
            );

            return $returnArray;
        }
    }

    public static function getById(array $aArgs = [])
    {
        ValidatorModel::notEmpty($aArgs, ['resId']);
        ValidatorModel::intVal($aArgs, ['resId']);

        $aReturn = DatabaseModel::select([
            'select'    => empty($aArgs['select']) ? ['*'] : $aArgs['select'],
            'table'     => ['res_letterbox'],
            'where'     => ['res_id = ?'],
            'data'      => [$aArgs['resId']]
        ]);

        if (empty($aReturn[0])) {
            return [];
        }

        return $aReturn[0];
    }

    /**
     * Updating the database with the error code
     * @param string $resTable res table
     * @param bigint $resId Id of the resource to process
     * @param string $result error code
     * @return nothing
     */
    public static function manageErrorOnDb(array $aArgs = [])
    {
        $attemptsRecord = DatabaseModel::select([
            'select'    => ['fulltext_attempts'],
            'table'     => [$aArgs['resTable']],
            'where'     => ['res_id = ?'],
            'data'      => [$aArgs['resId']],
        ]);

        if (empty($attemptsRecord)) {
            $attempts = 0;
        } else {
            $attempts = $attemptsRecord[0]['fulltext_attempts'] + 1;
        }

        DatabaseModel::update([
            'table'     => $aArgs['resTable'],
            'set'       => [
                'fulltext_result' => $aArgs['result'],
                'fulltext_attempts' => $attempts,
            ],
            'where'     => ['res_id = ?'],
            'data'      => [$aArgs['resId']]
        ]);
    }
}
