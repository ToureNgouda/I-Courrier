<?php

/**
 * Copyright Maarch since 2008 under licence GPLv3.
 * See LICENCE.txt file at the root folder for more details.
 * This file is part of Maarch software.
 *
 */

/**
* @brief process manageConvert class
*
* <ul>
* <li>Services to process the management of convertion of resources</li>
* </ul>
*
* @file
* @author Laurent Giovannoni <dev@maarch.org>
* @date $date$
* @version $Revision$
* @ingroup convert
*/

namespace Convert\Controllers;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Respect\Validation\Validator;
use Convert\Controllers\ProcessConvertController;
use Convert\Controllers\ProcessFulltextController;
use Convert\Controllers\ProcessThumbnailsController;
use SrcCore\controllers\LogsController;

class ProcessManageConvertController
{
    protected $libreOfficeExecutable;

    //public function __construct($libreOfficeExecutable = 'cloudooo')
    public function __construct($libreOfficeExecutable = 'soffice')
    //public function __construct($libreOfficeExecutable = 'unoconv')
    {
        $this->libreOfficeExecutable = $libreOfficeExecutable;
    }

    public function create(RequestInterface $request, ResponseInterface $response)
    {
        $data = $request->getParams();

        $check = Validator::notEmpty()->validate($data['collId']);
        $check = $check && Validator::stringType()->notEmpty()->validate($data['resTable']);
        $check = $check && Validator::stringType()->notEmpty()->validate($data['adrTable']);
        $check = $check && Validator::intType()->notEmpty()->validate($data['resId']);
        $check = $check && Validator::stringType()->notEmpty()->validate($data['tmpDir']);
        if (!$check) {
            return $response->withStatus(400)->withJson(['errors' => 'Bad Request']);
        }

        $return = ProcessManageConvertController::convertAll($data);

        if (empty($return) || !empty($return['errors'])) {
            return $response->withStatus(500)->withJson(['errors' => '[ProcessConvertController create] ' . $return['errors']]);
        }

        return $response->withJson($return);
    }

    /**
     * Ask for conversion in all mode
     *
     * @param string $collId collection
     * @param string $resTable resource table
     * @param string $adrTable adr table
     * @param long $resId res_id
     * @param string $tmpDir path to tmp
     * @throws Exception Check des valeurs d'entrées
     * @return array $returnArray the result
     */
    public function convertAll(array $args=[])
    {
        $timestart = microtime(true);
        // prerequisites
        $returnArray = array();
        if (empty($args['collId'])) {
            $returnArray = array(
                'status' => '1',
                'value' => '',
                'error' => 'collId empty for manage convert',
            );
            return $returnArray;
        } else {
            $collId = $args['collId'];
        }
        if (empty($args['resTable'])) {
            $returnArray = array(
                'status' => '1',
                'value' => '',
                'error' => 'resTable empty for manage convert',
            );
            return $returnArray;
        } else {
            $resTable = $args['resTable'];
        }
        if (empty($args['adrTable'])) {
            $returnArray = array(
                'status' => '1',
                'value' => '',
                'error' => 'adrTable empty for manage convert',
            );
            return $returnArray;
        } else {
            $adrTable = $args['adrTable'];
        }
        if (empty($args['resId'])) {
            $returnArray = array(
                'status' => '1',
                'value' => '',
                'error' => 'resId empty for manage convert',
            );
            return $returnArray;
        } else {
            $resId = $args['resId'];
        }
        

        if (!isset($args['tmpDir']) || $args['tmpDir'] == '') {
            $tmpDir = $_SESSION['config']['tmppath'];
        } else {
            $tmpDir = $args['tmpDir'];
        }

        $path_to_lucene = '';
        if (isset($args['path_to_lucene']) && !empty($args['path_to_lucene'])){
            $path_to_lucene = $args['path_to_lucene'];
        }

        $params = array(
            'collId'         => $collId, 
            'resTable'       => $resTable, 
            'adrTable'       => $adrTable, 
            'resId'          => $resId,
            'tmpDir'         => $tmpDir,
            'path_to_lucene' => $path_to_lucene
        );

        //CONV
        $ProcessConvertService = new ProcessConvertController();
        $resultOfConversion = $ProcessConvertService->convert($params);
        
        if ($resultOfConversion['status'] <> '0') {
            $returnArray = array(
                'status' => '1',
                'value' => '',
                'error' => 'CONV:' . $resultOfConversion['error'],
            );
            return $returnArray;
        }

        //TNL
        $ProcessConvertService = new ProcessThumbnailsController();
        $resultOfConversion = $ProcessConvertService->thumbnails($params);
        if ($resultOfConversion['status'] <> '0') {
            $returnArray = array(
                'status' => '1',
                'value' => '',
                'error' => 'TNL:' . $resultOfConversion['error'],
            );
            return $returnArray;
        }

        //FULLTEXT
        if ($args['createZendIndex']) {
            $params = array(
                'collId'         => $collId, 
                'resTable'       => $resTable, 
                'adrTable'       => $adrTable, 
                'resId'          => $resId,
                'tmpDir'         => $tmpDir,
                'path_to_lucene' => $path_to_lucene,
                'createZendIndex' => true
            );
        } else {
            $params = array(
                'collId'         => $collId, 
                'resTable'       => $resTable, 
                'adrTable'       => $adrTable, 
                'resId'          => $resId,
                'tmpDir'         => $tmpDir,
                'path_to_lucene' => $path_to_lucene
            );
        }
        $ProcessConvertServiceFulltext = new ProcessFulltextController();

        $resultOfConversion = $ProcessConvertServiceFulltext->fulltext($params);

        if ($resultOfConversion['status'] <> '0') {
            $returnArray = array(
                'status' => '1',
                'value' => '',
                'error' => 'TXT:' . $resultOfConversion['error'],
            );
            LogsController::executionTimeLog(
                $timestart, 
                '', 
                'debug', 
                '[TIMER] Convert_ManageConvertAbstract_Service::convertAll aucun contenu a indexer dans fulltext'
            );
            return $returnArray;
        }
        
        $returnArray = array(
            'status' => '0',
            'value' => '',
            'error' => '',
        );
        LogsController::executionTimeLog(
            $timestart, 
            '', 
            'debug', 
            '[TIMER] Convert_ManageConvertAbstract_Service::convertAll'
        );

        return $returnArray;
    }
}