<?php

/**
 * Copyright Maarch since 2008 under licence GPLv3.
 * See LICENCE.txt file at the root folder for more details.
 * This file is part of Maarch software.
 *
 */

/**
* @brief process thumbnails class
*
* <ul>
* <li>Services to process the thumbnails of resources</li>
* </ul>
*
* @file
* @author Laurent Giovannoni <dev@maarch.org>
* @date $date$
* @version $Revision$
* @ingroup convert
*/

namespace Convert\Controllers;

use Attachment\models\AttachmentModel;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Resource\models\ResModel;
use Respect\Validation\Validator;
use Convert\Models\ProcessThumbnailsModel;
use Docserver\models\DocserverModel;
use Docserver\models\ResDocserverModel;
use SrcCore\controllers\LogsController;
use SrcCore\controllers\StoreController;

class ProcessThumbnailsController
{
    protected $tnlExecutable;

    public function __construct($tnlExecutable = 'convert')
    {
        $this->tnlExecutable = $tnlExecutable;
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

        $return = ProcessThumbnailsController::thumbnails($data);

        if (empty($return) || !empty($return['errors'])) {
            return $response->withStatus(500)->withJson(['errors' => '[ProcessThumbnailsController create] ' . $return['errors']]);
        }

        return $response->withJson($return);
    }

    /**
     * Ask for thumbnails
     *
     * @param string $collId collection
     * @param string $resTable resource table
     * @param string $adrTable adr table
     * @param long $resId res_id
     * @param string $tmpDir path to tmp
     * @param array $tgtfmt array of target format
     * @return array $returnArray the result
     */
    public function thumbnails(array $args=[])
    {
        $timestart = microtime(true);
        $returnArray = array();
        if (empty($args['collId'])) {
            $returnArray = array(
                'status' => '1',
                'value' => '',
                'error' => 'collId empty for thumbnails',
            );
            return $returnArray;
        } else {
            $collId = $args['collId'];
        }
        if (empty($args['resTable'])) {
            $returnArray = array(
                'status' => '1',
                'value' => '',
                'error' => 'resTable empty for thumbnails',
            );
            return $returnArray;
        } else {
            $resTable = $args['resTable'];
        }
        if (empty($args['adrTable'])) {
            $returnArray = array(
                'status' => '1',
                'value' => '',
                'error' => 'adrTable empty for thumbnails',
            );
            return $returnArray;
        } else {
            $adrTable = $args['adrTable'];
        }
        if (empty($args['resId'])) {
            $returnArray = array(
                'status' => '1',
                'value' => '',
                'error' => 'resId empty for thumbnails',
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

        if ($args['resTable'] == 'res_letterbox') {
            $res = ResModel::getById(['resId' => $resId]);
        } elseif ($args['resTable'] == 'res_attachments') {
            $res = AttachmentModel::getById(['id' => $resId, 'isVersion' => 'false']);
        } else {
            $res = AttachmentModel::getById(['id' => $resId, 'isVersion' => 'true']);
        }

        if ($res['res_id'] <> '') {
            $adrType = 'CONV';
            if (
                strtoupper($res['format']) == 'HTML' ||
                strtoupper($res['format']) == 'MAARCH'
            ) {
                $adrType = 'DOC';
            }
            $resourcePath = ResDocserverModel::getSourceResourcePath(
                [
                    'resTable' => $resTable,
                    'adrTable' => $adrTable,
                    'resId'    => $res['res_id'],
                    'adrType'  => $adrType
                ]
            );
        }
        if (!file_exists($resourcePath)) {
            $returnArray = array(
                'status' => '1',
                'value' => '',
                'error' => 'file not already converted in pdf for thumbnails. path :'
                    . $resourcePath . ", adrType : CONV, adr_table : " . $adrTable,
            );
            ProcessThumbnailsModel::manageErrorOnDb(
                ['resTable' => $resTable, 'resId' => $resId, 'result' => '-1']
            );
            return $returnArray;
        }

        //copy the resource on tmp directory
        $fileNameOnTmp = $tmpDir . rand() . rand();
        if (!copy($resourcePath, $fileNameOnTmp)) {
            $returnArray = array(
                'status' => '1',
                'value' => '',
                'error' => 'copy on tmp failed for thumbnails. Copy ' . $resourcePath . ' to ' . $fileNameOnTmp,
            );
            ProcessThumbnailsModel::manageErrorOnDb(
                ['resTable' => $resTable, 'resId' => $resId, 'result' => '-1']
            );
            return $returnArray;
        }

        //now do the thumbnails !
        $resultOfConversion = $this->launchThumbnails(
            $fileNameOnTmp,
            $tmpDir,
            pathinfo($resourcePath, PATHINFO_EXTENSION)
        );


        if ($resultOfConversion['status'] <> '0') {
            ProcessThumbnailsModel::manageErrorOnDb(
                ['resTable' => $resTable, 'resId' => $resId, 'result' => '-1']
            );
            LogsController::executionTimeLog(
                $timestart,
                '',
                'debug',
                '[TIMER] Convert_ProcessThumbnailsAbstract_Service::thumbnails aucunContenuAIndexer'
            );
            return $resultOfConversion;
        }

        //copy the result on docserver
        // LogsController::info(['message'=>'avant cp ds', 'code'=>1112, ]);
        $storeResult = StoreController::storeResourceOnDocServer([
            'collId'    => $collId,
            'fileInfos' => [
                'tmpDir'        => $tmpDir,
                'size'          => filesize($fileNameOnTmp),
                'format'        => 'PNG',
                'tmpFileName'   => pathinfo($fileNameOnTmp, PATHINFO_FILENAME) . '.png',
            ],
            'docserverTypeId'   => 'TNL'
        ]);

        if (empty($storeResult)) {
            $returnArray = array(
                'status' => '1',
                'value' => '',
                'error' => 'Ds of collection and ds type not found for thumbnails:'
                    . $collId . ' THUMBNAILS',
            );
            ProcessThumbnailsModel::manageErrorOnDb(
                ['resTable' => $resTable, 'resId' => $resId, 'result' => '-1']
            );
            return $returnArray;
        }

        $targetDs = DocserverModel::getById(['id' => $storeResult['docserver_id']]);

        // LogsController::info(['message'=>'avant update', 'code'=>19, ]);
        //update the Database
        $resultOfUpDb = ProcessThumbnailsModel::updateDatabase(
            [
                'collId'     => $collId,
                'resTable'   => $resTable,
                'adrTable'   => $adrTable,
                'resId'      => $resId,
                'docserver'  => $targetDs,
                'path'       => $storeResult['destination_dir'],
                'fileName'   => $storeResult['file_destination_name']
            ]
        );

        if ($resultOfUpDb['status'] <> '0') {
            ProcessThumbnailsModel::manageErrorOnDb(
                ['resTable' => $resTable, 'resId' => $resId, 'result' => '-1']
            );
            return $resultOfUpDb;
        }

        unlink($fileNameOnTmp);
        unlink($fileNameOnTmp . '.png');

        $returnArray = array(
            'status' => '0',
            'value' => '',
            'error' => '',
        );
        LogsController::executionTimeLog(
            $timestart,
            '',
            'debug',
            '[TIMER] Convert_ProcessThumbnailsAbstract_Service::thumbnails'
        );
        return $returnArray;
    }

    /**
     * Launch the thumbnails process
     *
     * @param string $srcfile source file
     * @param string $tgtdir target dir
     * @param string $srcfmt source format
     * @return array $returnArray the result
     */
    private function launchThumbnails(
        $srcfile,
        $tgtdir=false,
        $srcfmt
    ) {
        $timestart = microtime(true);
        if (!$tgtdir) {
            $tgtdir = dirname($srcfile);
        }

        $output = array();
        $return = null;
        $this->errors = array();

        //wkhtmltoimage must be installed with compiled sources
        if (strtoupper($srcfmt) == 'MAARCH' || strtoupper($srcfmt) == 'HTML') {
            copy($srcfile, str_ireplace('.maarch', '.', $srcfile) . '.html');
            if (file_exists('/usr/bin/mywkhtmltoimage')) {
                $command = "mywkhtmltoimage  --width 164 --height 105 --quality 100 --zoom 0.2 "
                    . escapeshellarg(str_ireplace('.maarch', '.', $srcfile) . '.html') . " "
                    . escapeshellarg($tgtdir . basename(str_ireplace('.maarch', '.', $srcfile)) . '.png');
            } else {
                $envVar = "export DISPLAY=FRPAROEMINT:0.0 ; ";
                $command = $envVar . "wkhtmltoimage --width 164 --height 105 --quality 100 --zoom 0.2 "
                    . escapeshellarg(str_ireplace('.maarch', '.', $srcfile) . '.html') . " "
                    . escapeshellarg($tgtdir . basename(str_ireplace('.maarch', '.', $srcfile)) . '.png');
            }
        } else {
            $command = "convert -thumbnail 200x300 -background white -alpha remove "
                . escapeshellarg($srcfile) . "[0] "
                . escapeshellarg($tgtdir . basename($srcfile) . '.png');
        }
        //echo $command . PHP_EOL;exit;
        $timestart_command = microtime(true);
        exec($command, $output, $return);
        // LogsController::debug(['message'=>'[TIMER] Commande : ' . $command]);
        LogsController::executionTimeLog($timestart_command, '', 'debug', '[TIMER] Convert_ProcessThumbnailsAbstract_Service::launchThumbnails__exec');

        if ($return === 0) {
            $returnArray = array(
                'status' => '0',
                'value' => '',
                'error' => '',
            );
        } else {
            $returnArray = array(
                'status' => '1',
                'value' => '',
                'error' => $return . $output,
            );
        }
        if (strtoupper($srcfmt) == 'MAARCH' || strtoupper($srcfmt) == 'HTML') {
            $returnArray = array();
            unlink(str_ireplace('.maarch', '.', $srcfile) . '.html');
            $returnArray = array(
                'status' => '0',
                'value' => '',
                'error' => '',
            );
        }
        LogsController::executionTimeLog(
            $timestart,
            '',
            'debug',
            '[TIMER] Convert_ProcessThumbnailsAbstract_Service::launchThumbnails
        ');
        return $returnArray;
    }
}
