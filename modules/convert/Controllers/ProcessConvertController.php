<?php

/**
 * Copyright Maarch since 2008 under licence GPLv3.
 * See LICENCE.txt file at the root folder for more details.
 * This file is part of Maarch software.
 *
 */

/**
* @brief process convert class
*
* <ul>
* <li>Services to process the convertion of resources</li>
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
use Convert\Models\ProcessConvertModel;
use Docserver\models\DocserverModel;
use Docserver\models\ResDocserverModel;
use SrcCore\controllers\LogsController;
use SrcCore\controllers\StoreController;

class ProcessConvertController
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

        $return = ProcessConvertController::convert($data);

        if (empty($return) || !empty($return['errors'])) {
            return $response->withStatus(500)->withJson(['errors' => '[ProcessConvertController create] ' . $return['errors']]);
        }

        return $response->withJson($return);
    }

    /**
     * Ask for conversion
     *
     * @param string $collId collection
     * @param string $resTable resource table
     * @param string $adrTable adr table
     * @param long $resId res_id
     * @param string $tmpDir path to tmp
     * @param array $tgtfmt array of target format
     * @return array $returnArray the result
     */
    public function convert(array $args=[])
    {
        $timestart = microtime(true);
        // LogsController::info(['message'=>'debut convert', 'code'=>111, ]);
        $returnArray = array();
        if (empty($args['collId'])) {
            $returnArray = array(
                'status' => '1',
                'value' => '',
                'error' => 'collId empty',
            );
            return $returnArray;
        } else {
            $collId = $args['collId'];
        }
        if (empty($args['resTable'])) {
            $returnArray = array(
                'status' => '1',
                'value' => '',
                'error' => 'resTable empty',
            );
            return $returnArray;
        } else {
            $resTable = $args['resTable'];
        }
        if (empty($args['adrTable'])) {
            $returnArray = array(
                'status' => '1',
                'value' => '',
                'error' => 'adrTable empty',
            );
            return $returnArray;
        } else {
            $adrTable = $args['adrTable'];
        }
        if (empty($args['resId'])) {
            $returnArray = array(
                'status' => '1',
                'value' => '',
                'error' => 'resId empty',
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
            $resourcePath = ResDocserverModel::getSourceResourcePath(
                [
                    'resTable' => $resTable,
                    'adrTable' => $adrTable,
                    'resId' => $res['res_id'],
                    'adrType' => 'DOC'
                ]
            );
        }
        if (!file_exists($resourcePath)) {
            $returnArray = array(
                'status' => '1',
                'value' => '',
                'error' => 'file not exists : ' . $resourcePath,
            );
            ProcessConvertModel::manageErrorOnDb(
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
                'error' => 'copy on tmp failed',
            );
            ProcessConvertModel::manageErrorOnDb(
                ['resTable' => $resTable, 'resId' => $resId, 'result' => '-1']
            );
            return $returnArray;
        }
        //now do the conversion !
        if (strtoupper($res['format']) <> 'PDF') {
            $resultOfConversion = $this->launchConvert(
                $fileNameOnTmp,
                'pdf',
                $tmpDir,
                pathinfo($resourcePath, PATHINFO_EXTENSION)
            );
        } else {
            copy($fileNameOnTmp, $fileNameOnTmp . '.pdf');
            $resultOfConversion = array(
                'status' => '0',
                'value' => '',
                'error' => '',
            );
        }
        if ($resultOfConversion['status'] <> '0') {
            ProcessConvertModel::manageErrorOnDb(
                ['resTable' => $resTable, 'resId' => $resId, 'result' => '-1']
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
                'format'        => 'PDF',
                'tmpFileName'   => pathinfo($fileNameOnTmp, PATHINFO_FILENAME) . '.pdf',
            ],
            'docserverTypeId'   => 'CONVERT'
        ]);

        if (empty($storeResult)) {
            $returnArray = array(
                'status' => '1',
                'value' => '',
                'error' => 'Ds of collection and ds type not found for convert:'
                    . $collId . ' CONVERT',
            );
            ProcessConvertModel::manageErrorOnDb(
                ['resTable' => $resTable, 'resId' => $resId, 'result' => '-1']
            );
            return $returnArray;
        }

        if (!empty($storeResult['errors'])) {
            $returnArray = array(
                'status' => '1',
                'value' => '',
                'error' => $storeResult['errors'] . ' error for convert:'
                    . $fileNameOnTmp,
            );
            ProcessConvertModel::manageErrorOnDb(
                ['resTable' => $resTable, 'resId' => $resId, 'result' => '-1']
            );
            return $returnArray;
        }

        $targetDs = DocserverModel::getById(['id' => $storeResult['docserver_id']]);

        // LogsController::info(['message'=>'avant update', 'code'=>19, ]);
        //update the \Database
        $resultOfUpDb = ProcessConvertModel::updateDatabase(
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
        // LogsController::info(['message'=>var_export($resultOfUpDb, true), 'code'=>111111, ]);
        // LogsController::info(['message'=>$collId, 'code'=>2, ]);
        // LogsController::info(['message'=>$resTable, 'code'=>3, ]);
        // LogsController::info(['message'=>$adrTable, 'code'=>4, ]);
        // LogsController::info(['message'=>$resId, 'code'=>5, ]);
        // LogsController::info(['message'=>'apres res_id', 'code'=>6, ]);
        // LogsController::info(['message'=>$targetDs, 'code'=>6, ]);
        // LogsController::info(['message'=>var_export($resultCopyDs, true), 'code'=>7, ]);

        if ($resultOfUpDb['status'] <> '0') {
            ProcessConvertModel::manageErrorOnDb(
                ['resTable' => $resTable, 'resId' => $resId, 'result' => '-1']
            );
            return $resultOfUpDb;
        }

        unlink($fileNameOnTmp);
        unlink($fileNameOnTmp . '.pdf');

        $returnArray = array(
            'status' => '0',
            'value' => '',
            'error' => '',
        );
        LogsController::executionTimeLog(
            $timestart,
            '',
            'debug',
            '[TIMER] Convert_ProcessConvertAbstract_Service::convert'
        );
        return $returnArray;
    }

    /**
     * Launch the conversion
     *
     * @param string $srcfile source file
     * @param string $tgtfmt target format
     * @param string $tgtdir target dir
     * @param string $srcfmt source format
     * @return array $returnArray the result
     */
    public function launchConvert(
        $srcfile,
        $tgtfmt,
        $tgtdir=false,
        $srcfmt=null
    ) {
        $timestart=microtime(true);

        $processHtml = false;
        $executable='';
        
        // LogsController::info(['message'=>'[TIMER] Debut Convert_ProcessConvertAbstract_Service::launchConvert']);
        if (strtoupper($srcfmt) == 'MAARCH' || strtoupper($srcfmt) == 'HTML') {
            $processHtml = true;
            // LogsController::info(['message'=>'[TIMER] srcfmt ' . $srcfmt]);
            copy($srcfile, str_ireplace('.maarch', '.', $srcfile) . '.html');
            if (file_exists('/usr/bin/mywkhtmltopdf')) {
                $command = "mywkhtmltopdf "
                    . escapeshellarg(str_ireplace('.maarch', '.', $srcfile) . '.html') . " "
                    . escapeshellarg($tgtdir . basename(str_ireplace('.maarch', '.', $srcfile)) . '.pdf');
            } else {
                $envVar = "export DISPLAY=FRPAROEMINT:0.0 ; ";
                $command = $envVar . "wkhtmltopdf "
                    . escapeshellarg(str_ireplace('.maarch', '.', $srcfile) . '.html') . " "
                    . escapeshellarg($tgtdir . basename(str_ireplace('.maarch', '.', $srcfile)) . '.pdf');
            }
            $executable='wkhtmltopdf';
        } else {
            $executable='soffice';
            // LogsController::info(['message'=>'[TIMER] let LO do it ' . $this->libreOfficeExecutable]);
            if ($this->libreOfficeExecutable == "cloudooo") {
                $serverAddress = "http://192.168.21.40:8011";
                $tokens = array();
                require_once 'apps/maarch_entreprise/tools/phpxmlrpc/lib/xmlrpc.inc';
                require_once 'apps/maarch_entreprise/tools/phpxmlrpc/lib/xmlrpcs.inc';
                require_once 'apps/maarch_entreprise/tools/phpxmlrpc/lib/xmlrpc_wrappers.inc';
                $fileContent = file_get_contents($srcfile, FILE_BINARY);
                $encodedContent = base64_encode($fileContent);
                $params = array();
                array_push($params, new PhpXmlRpc\Value($encodedContent));
                array_push($params, new PhpXmlRpc\Value($srcfmt));
                array_push($params, new PhpXmlRpc\Value($tgtfmt));
                array_push($params, new PhpXmlRpc\Value(false));
                $v = new PhpXmlRpc\Value($params, "array");
            } elseif ($this->libreOfficeExecutable == "unoconv") {
                $tokens = array('"' . $this->libreOfficeExecutable . '"');
                $tokens[] = "-f";
                $tokens[] = $tgtfmt;
                $tokens[] = '-o "' . $srcfile . '.' . $tgtfmt . '"';
                $tokens[] = '"' . $srcfile . '"';
            } else {
                $tokens = array('"' . $this->libreOfficeExecutable . '"');
                $tokens[] = "--headless";
                $tokens[] = "--convert-to";
                $tokens[] = $tgtfmt;
                $tokens[] = '"' . $srcfile . '"';
                if (!$tgtdir) {
                    $tgtdir = dirname($srcfile);
                }
                $tokens[] = '--outdir "' . $tgtdir . '"';
            }
            
            if (!$srcfmt) {
                $tokens[] = $srcfmt;
            }

            $command = implode(' ', $tokens);

            $output = array();
            $return = null;
            $this->errors = array();
        }
        //echo $command . '<br />';exit;
        if ($this->libreOfficeExecutable == "cloudooo" && !$processHtml) {
            // LogsController::info(['message'=>'[TIMER] commande : cloudooo url ' . $serverAddress]);
            // LogsController::info(['message'=>'[TIMER] Debut Convert_ProcessConvertAbstract_Service::launchConvert__exec']);
            $req = new PhpXmlRpc\Request('convertFile', $v);
            //LogsController::info(['message'=>'[TIMER] commande : cloudooo url ' . $serverAddress]);
            // LogsController::info(['message'=>'[TIMER] Fin Convert_ProcessConvertAbstract_Service::launchConvert__exec']);
            $client = new PhpXmlRpc\Client($serverAddress);
            $resp = $client->send($req);
            if (!$resp->faultCode()) {
                $encoder = new PhpXmlRpc\Encoder();
                $value = $encoder->decode($resp->value());
                $theFile = fopen($srcfile . '.' . $tgtfmt, 'w+');
                fwrite($theFile, base64_decode($value));
                fclose($theFile);
                $returnArray = array(
                    'status' => '0',
                    'value' => '',
                    'error' => '',
                );
            } else {
                //print "An error occurred: ";
                //print "Code: " . htmlspecialchars($resp->faultCode())
                //    . " Reason: '" . htmlspecialchars($resp->faultString()) . "'\n";
                $returnArray = array(
                    'status' => '1',
                    'value' => '',
                    'error' => "Code: " . htmlspecialchars($resp->faultCode())
                        . " Reason: '" . htmlspecialchars($resp->faultString()),
                );
            }
        } else {
            $timestart_command = microtime(true);
            exec("timeout -k 5m 3m " . $command, $output, $return);
            // LogsController::debug(['message'=>'[TIMER] commande : ' . $command]);
            LogsController::executionTimeLog($timestart_command, '', 'info', '[TIMER] ' . $executable . ' - Convert_ProcessConvertAbstract_Service::launchConvert__exec');
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
            'info',
            '[TIMER] Fin Convert_ProcessConvertAbstract_Service::launchConvert'
        );
        return $returnArray;
    }
}
