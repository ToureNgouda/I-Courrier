<?php

/**
 * Copyright Maarch since 2008 under licence GPLv3.
 * See LICENCE.txt file at the root folder for more details.
 * This file is part of Maarch software.
 *
 */

/**
* @brief process fulltext class
*
* <ul>
* <li>Services to process the fulltext of resources</li>
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
use Convert\Models\ProcessFulltextModel;
use Docserver\models\DocserverModel;
use Docserver\models\ResDocserverModel;
use SrcCore\controllers\LogsController;
use SrcCore\controllers\StoreController;
use SrcCore\models\TextFormatModel;


class ProcessFulltextController
{
    protected $pdftotext;

    public function __construct($pdftotext = 'pdftotext')
    {
        // Storing text in lucene index
        set_include_path('apps/maarch_entreprise/tools/' 
            . PATH_SEPARATOR . get_include_path()
        );

        //if(!@include('Zend/Search/Lucene.php')) {
            set_include_path($GLOBALS['MaarchDirectory'] 
                . 'apps/maarch_entreprise/tools/' 
                . PATH_SEPARATOR . get_include_path()
            );

            require_once("Zend/Search/Lucene.php");
        //}

        $this->pdftotext = $pdftotext;
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

        if ($data['createZendIndex']) {
            $countColl = count($_SESSION['collections']);
            for ($i=0;$i<$countColl;$i++) {
                if ($_SESSION['collections'][$i]['id'] == 'letterbox_coll') {
                    $pathToLucene = $_SESSION['collections'][$i]['path_to_lucene_index'];
                }
            }

            $data['zendIndex'] = ProcessFulltextController::createZendIndexObject(
                $pathToLucene
            );
        }

        $return = ProcessFulltextController::fulltext($data);

        if (empty($return) || !empty($return['errors'])) {
            return $response->withStatus(500)->withJson(['errors' => '[ProcessFulltextController create] ' . $return['errors']]);
        }

        return $response->withJson($return);
    }

    /**
     * Ask for fulltext
     *
     * @param string $collId collection
     * @param string $resTable resource table
     * @param string $adrTable adr table
     * @param long $resId res_id
     * @param string $tmpDir path to tmp
     * @param array $tgtfmt array of target format
     * @return array $returnArray the result
     */
    public function fulltext(array $args=[])
    {
        $timestart = microtime(true);
        $returnArray = array();
        if (empty($args['collId'])) {
            $returnArray = array(
                'status' => '1',
                'value' => '',
                'error' => 'collId empty for fulltext',
            );
            return $returnArray;
        } else {
            $collId = $args['collId'];
        }
        if (empty($args['resTable'])) {
            $returnArray = array(
                'status' => '1',
                'value' => '',
                'error' => 'resTable empty for fulltext',
            );
            return $returnArray;
        } else {
            $resTable = $args['resTable'];
        }
        if (empty($args['adrTable'])) {
            $returnArray = array(
                'status' => '1',
                'value' => '',
                'error' => 'adrTable empty for fulltext',
            );
            return $returnArray;
        } else {
            $adrTable = $args['adrTable'];
        }
        if (empty($args['resId'])) {
            $returnArray = array(
                'status' => '1',
                'value' => '',
                'error' => 'resId empty for fulltext',
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

        if (isset($args['path_to_lucene']) && !empty($args['path_to_lucene'])) {
            $indexFileDirectory = $args['path_to_lucene'];
        } else {
            $countColl = count($_SESSION['collections']);
            for ($i=0;$i<$countColl;$i++) {
                if ($_SESSION['collections'][$i]['id'] == $collId) {
                    $indexFileDirectory 
                        = $_SESSION['collections'][$i]['path_to_lucene_index'];
                }
            }
        }
        if ($args['createZendIndex']) {
            $countColl = count($_SESSION['collections']);
            for ($i=0;$i<$countColl;$i++) {
                if ($_SESSION['collections'][$i]['id'] == 'letterbox_coll') {
                    $pathToLucene = $_SESSION['collections'][$i]['path_to_lucene_index'];
                }
            }

            $args['zendIndex'] = ProcessFulltextController::createZendIndexObject(
                $pathToLucene
            );
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
                    'adrType' => 'CONV'
                ]
            );
        }
        if (!file_exists($resourcePath)) {
            $returnArray = array(
                'status' => '1',
                'value' => '',
                'error' => 'file not already converted in pdf for fulltext. path :' 
                    . $resourcePath . ", adrType : CONV, adr_table : " . $adrTable,
            );
            ProcessFulltextController::manageErrorOnDb(
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
                'error' => 'copy on tmp failed for fulltext. Copy ' . $resourcePath . ' to ' . $fileNameOnTmp,
            );
            ProcessFulltextController::manageErrorOnDb(
                ['resTable' => $resTable, 'resId' => $resId, 'result' => '-1']
            );
            return $returnArray;
        }

        //now do the fulltext !
        if (!empty($args['zendIndex'])) {
            $resultOfConversion = $this->launchFulltext(
                $fileNameOnTmp, 
                $resId, 
                $indexFileDirectory, 
                $tmpDir,
                $args['zendIndex']
            );
        } else {
            $resultOfConversion = $this->launchFulltext(
                $fileNameOnTmp, 
                $resId, 
                $indexFileDirectory, 
                $tmpDir
            );
        }

        if ($resultOfConversion['status'] <> '0') {
            ProcessFulltextController::manageErrorOnDb(
                ['resTable' => $resTable, 'resId' => $resId, 'result' => '-1']
            );
            LogsController::executionTimeLog(
                $timestart, 
                '', 
                'debug', 
                '[TIMER] Convert_ProcessFulltextAbstract_Service::fulltext aucunContenuAIndexer'
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
                'format'        => 'TXT',
                'tmpFileName'   => pathinfo($fileNameOnTmp, PATHINFO_FILENAME) . '.txt',
            ],
            'docserverTypeId'   => 'FULLTEXT'
        ]);

        if (empty($storeResult)) {
            $returnArray = array(
                'status' => '1',
                'value' => '',
                'error' => 'Ds of collection and ds type not found for fulltext:' 
                    . $collId . ' FULLTEXT',
            );
            ProcessFulltextController::manageErrorOnDb(
                ['resTable' => $resTable, 'resId' => $resId, 'result' => '-1']
            );
            return $returnArray;
        }

        $targetDs = DocserverModel::getById(['id' => $storeResult['docserver_id']]);

        // LogsController::info(['message'=>'avant update', 'code'=>19, ]);
        //update the Database
        $resultOfUpDb = ProcessFulltextModel::updateDatabase(
            [
                'collId'     => $collId,
                'resTable'   => $resTable, 
                'adrTable'   => $adrTable, 
                'resId'      => $resId,
                'docserver'  => $targetDs,
                'path'       => $storeResult['destination_dir'],
                'fileName'   => $storeResult['file_destination_name'],
                'zendIndex'  => $args['zendIndex']
            ]
        );

        if ($resultOfUpDb['status'] <> '0') {
            ProcessFulltextModel::manageErrorOnDb(
                ['resTable' => $resTable, 'resId' => $resId, 'result' => '-1']
            );
            return $resultOfUpDb;
        }

        unlink($fileNameOnTmp);
        unlink($fileNameOnTmp . '.txt');

        $returnArray = array(
            'status' => '0',
            'value' => '',
            'error' => '',
        );
        LogsController::executionTimeLog(
            $timestart, 
            '', 
            'debug', 
            '[TIMER] Convert_ProcessFulltextAbstract_Service::fulltext'
        );
        return $returnArray;
    }

    /**
     * Launch the fulltext process
     *
     * @param string $srcfile source file
     * @param string $tgtdir target dir
     * @param string $srcfmt source format
     * @return array $returnArray the result
     */
    private function launchFulltext(
        $srcfile, 
        $resId,
        $indexFileDirectory, 
        $tgtdir=false,
        $zendIndex=''
    ) {
        if (!empty($zendIndex)) {
            $return = $this->prepareIndexFullTextPdf(
                $srcfile, 
                $tgtdir, 
                $indexFileDirectory,
                $resId,
                $zendIndex
            );
        } else {
            $return = $this->prepareIndexFullTextPdf(
                $srcfile, 
                $tgtdir, 
                $indexFileDirectory,
                $resId
            );
        }
        
        if ($return === 0) {
            $returnArray = array(
                'status' => '0',
                'value' => '',
                'error' => '',
            );
            return $returnArray;
        } else {
            $returnArray = array(
                'status' => '1',
                'value' => '',
                'error' => $return . $output,
            );
            return $returnArray;
        }
    }

    /**
    * Read a txt file
    * @param  $file string path of the file to read
    * @return string contents of the file
    */
    private function readFileF($file)
    {
        $result = "";
        if (is_file($file)) {
            $fp = fopen($file, "r");
            $result = fread($fp, filesize($file));
            fclose($fp);
        }
        return $result;
    }

    private function prepareIndexFullTextPdf($pathToFile, $tmpDir, $indexFileDirectory, $resId, $zendIndex = "")
    {
        $timestart = microtime(true);
        if (is_file($pathToFile)) {
            $tmpFile = $tmpDir . basename($pathToFile) . ".txt";
            $timestart_fulltext = microtime(true);
            $resultExtraction = exec("pdftotext " . escapeshellarg($pathToFile)
                    . " " . escapeshellarg($tmpFile) 
                );
            LogsController::executionTimeLog(
                $timestart_fulltext, 
                '', 
                'debug', 
                '[TIMER] Convert_ProcessFulltextAbstract_Service::prepareIndexFullTextPdf__exec'
            );
            
            $fileContent = trim($this->readFileF($tmpFile));
            
            if (!empty($zendIndex)) {
                $result = $this->launchIndexFullTextWithZendIndex(
                        $fileContent, 
                        $indexFileDirectory, 
                        $resId, 
                        $zendIndex
                );
            } else {
                // TODO : will be done only by the batch convert in OnlyIndexes mode
                //$result = $this->launchIndexFullText($fileContent, $indexFileDirectory, $resId);
                $result = 0;
            }
            
        } else {
            $result = 'file not found ' . $pathToFile;
        }
        LogsController::executionTimeLog(
            $timestart, 
            '', 
            'debug', 
            '[TIMER] Convert_ProcessFulltextAbstract_Service::prepareIndexFullTextPdf'
        );
        return $result;
    }

    /**
    * Return zend index object for batch mode
    * @param  $indexFileDirectory string directory of the lucene index
    * @return zend index object
    */
    public function createZendIndexObject($tempIndexFileDirectory, $numberOfIndexes = 1000) 
    {
        //echo 'createZendIndexObject : ' . $numberOfIndexes . PHP_EOL;
        $indexFileDirectory = (string) $tempIndexFileDirectory; 
        // with version 1.12, we need a string, not an XML element
        
        if (!is_dir($indexFileDirectory)) {
            $index = \Zend_Search_Lucene::create($indexFileDirectory);
        } else {
            if ($this->isDirEmpty($indexFileDirectory)) {
                $index = \Zend_Search_Lucene::create($indexFileDirectory);
            } else {
                $index = \Zend_Search_Lucene::open($indexFileDirectory);
            }
        }
        $index->setFormatVersion(\Zend_Search_Lucene::FORMAT_2_3); 
        // we set the lucene format to 2.3
        \Zend_Search_Lucene_Analysis_Analyzer::setDefault(
            new \Zend_Search_Lucene_Analysis_Analyzer_Common_Utf8Num_CaseInsensitive()
        );

        //$index->MaxBufferedDocs();
        $index->setMaxBufferedDocs($numberOfIndexes);

        return $index;
    }

    /**
    *  Checks if a directory is empty
    *
    * @param  $dir string The directory to check
    * @return bool True if empty, False otherwise
    */
    function isDirEmpty($dir)
    {
        $dir = opendir($dir);
        $isEmpty = true;
        while (($entry = readdir($dir)) !== false) {
            if ($entry !== '.' && $entry !== '..'  && $entry !== '.svn') {
                $isEmpty = false;
                break;
            }
        }
        closedir($dir);
        return $isEmpty;
    }

    /**
    * Commit the zend index at the end of the batch
    * @return nothing
    */
    public function commitZendIndex($index) 
    {
        //echo 'the commit' . PHP_EOL;
        $index->commit();
    }

    /**
    * Retrieve the text of a pdftext and launch the lucene engine
    * @param  $pathToFile string path of the file to index
    * @param  $indexFileDirectory string directory of the lucene index
    * @param  $id integer id of the document to index
    * @return integer user exit code is stored in fulltext_result column of the
    * document in "res_x"
    */
    private function launchIndexFullText($fileContent, $tempIndexFileDirectory, $Id) 
    {
        // $IndexFileDirectory is replace by tempIndexFileDirectory
        $fileContent = TextFormatModel::normalize(['string' => $fileContent]);
        $fileContent = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $fileContent);
        $indexFileDirectory = (string) $tempIndexFileDirectory; 
        // with version 1.12, we need a string, not an XML element
        $result = -1;
        if (strlen($fileContent) > 2) {
            if (!is_dir($indexFileDirectory)) {
                //$_ENV['logger']->write($indexFileDirectory . " not exists !", "ERROR", 2);
                $index = Zend_Search_Lucene::create($indexFileDirectory);
            } else {
                if ($this->isDirEmpty($indexFileDirectory)) {
                    //$_ENV['logger']->write($indexFileDirectory . " empty !");
                    $index = Zend_Search_Lucene::create($indexFileDirectory);
                } else {
                    $index = Zend_Search_Lucene::open($indexFileDirectory);
                }
            }
            $index->setFormatVersion(Zend_Search_Lucene::FORMAT_2_3); 
            // we set the lucene format to 2.3
            Zend_Search_Lucene_Analysis_Analyzer::setDefault(
                new \Zend_Search_Lucene_Analysis_Analyzer_Common_Utf8Num_CaseInsensitive()
            );
            // we need utf8 for accents
            $term = new \Zend_Search_Lucene_Index_Term($Id, 'Id');
            foreach ($index->termDocs($term) as $id) {
                $index->delete($id);
            }
            //echo $fileContent;
            $doc = new \Zend_Search_Lucene_Document();
            $doc->addField(\Zend_Search_Lucene_Field::UnIndexed('Id', (integer) $Id));
            $doc->addField(\Zend_Search_Lucene_Field::UnStored(
                'contents', $fileContent)
            );
            $index->addDocument($doc);
            $index->commit();
            //$index->optimize();
            $result = 0;
        } else {
            $result = 1;
        }
        return $result;
    }

    /**
    * Retrieve the text of a pdftext and launch the lucene engine
    * @param  $pathToFile string path of the file to index
    * @param  $indexFileDirectory string directory of the lucene index
    * @param  $id integer id of the document to index
    * @return integer user exit code is stored in fulltext_result column of the
    * document in "res_x"
    */
    private function launchIndexFullTextWithZendIndex($fileContent, $tempIndexFileDirectory, $Id, $index) 
    {
        //echo 'launchIndexFullTextWithZendIndex' . PHP_EOL;
        // $IndexFileDirectory is replace by tempIndexFileDirectory
        $fileContent = TextFormatModel::normalize(['string' => $fileContent]);
        $fileContent = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $fileContent);

        // with version 1.12, we need a string, not an XML element
        $result = -1;
        if (strlen($fileContent) > 2) {
            try {
                // we need utf8 for accents
                $term = new \Zend_Search_Lucene_Index_Term($Id, 'Id');
                foreach ($index->termDocs($term) as $id) {
                    $index->delete($id);
                }
                //echo $fileContent;
                $doc = new \Zend_Search_Lucene_Document();
                $doc->addField(\Zend_Search_Lucene_Field::UnIndexed('Id', (integer) $Id));
                $doc->addField(\Zend_Search_Lucene_Field::UnStored(
                    'contents', $fileContent)
                );
                //$func->show_array($doc);
                $index->addDocument($doc);
                //$index->commit();
                //$func->show_array($index);
                //$index->optimize();
                $result = 0;
            } catch (Exception $e) {
                $result = $e->getMessage();
            }
            
        } else if (strlen($fileContent) >= 0){
            $result = 0;
        }
        return $result;
    }

    public static function optimizeLuceneIndex(array $args=[]){
        $timestart = microtime(true);
        // prerequisites
        self::checkRequired($args, ['collId']);
        self::checkString($args, ['collId']); 
        
        $collId = $args['collId'];

        $countColl = count($_SESSION['collections']);
        for ($i=0;$i<$countColl;$i++) {
            if ($_SESSION['collections'][$i]['id'] == $collId) {
                $path_to_lucene = $_SESSION['collections'][$i]['path_to_lucene_index'];
            }
        }

        if(!empty($path_to_lucene)){
            exec(
                'php '.$_SESSION['config']['corepath'] . 
                'modules/convert/optimizeLuceneIndex.php ' . 
                $path_to_lucene . ' ' . 
                $_SESSION['config']['corepath'] . ' > /dev/null 2>&1 &'
            );
        }
        LogsController::executionTimeLog(
            $timestart, 
            '', 
            'debug', 
            '[TIMER] Convert_ProcessFulltextAbstract_Service::optimizeLuceneIndex'
        );

        return true;
    }
}
