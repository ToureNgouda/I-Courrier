<?php
/**
* Copyright Maarch since 2008 under licence GPLv3.
* See LICENCE.txt file at the root folder for more details.
* This file is part of Maarch software.

* @brief   DoctypeControllerTest
* @author  dev <dev@maarch.org>
* @ingroup core
*/

use PHPUnit\Framework\TestCase;
use SrcCore\models\DatabaseModel;

class DoctypeControllerTest extends TestCase
{
    private static $firstLevelId  = null;
    private static $secondLevelId = null;
    private static $doctypeId     = null;

    public function testReadList()
    {
        //  READ LIST
        $environment  = \Slim\Http\Environment::mock(['REQUEST_METHOD' => 'GET']);
        $request      = \Slim\Http\Request::createFromEnvironment($environment);

        $firstLevelController = new \Doctype\controllers\FirstLevelController();
        $response          = $firstLevelController->getTree($request, new \Slim\Http\Response());
        $responseBody      = json_decode((string)$response->getBody());

        $this->assertNotNull($responseBody->structure);
        $this->assertNotNull($responseBody->structure[0]->doctypes_first_level_id);
        $this->assertInternalType('int', $responseBody->structure[0]->doctypes_first_level_id);
        $this->assertNotNull($responseBody->structure[0]->doctypes_first_level_label);
        $this->assertNotNull($responseBody->structure[0]->enabled);
    }

    public function testinitFirstLevel()
    {
        $environment  = \Slim\Http\Environment::mock(['REQUEST_METHOD' => 'GET']);
        $request      = \Slim\Http\Request::createFromEnvironment($environment);

        $firstLevelController = new \Doctype\controllers\FirstLevelController();
        $response          = $firstLevelController->initFirstLevel($request, new \Slim\Http\Response());
        $responseBody      = json_decode((string)$response->getBody());

        $this->assertNotNull($responseBody->folderType);
        $this->assertNotNull($responseBody->folderType[0]->foldertype_id);
        $this->assertNotNull($responseBody->folderType[0]->foldertype_label);
    }

    public function testinitSecondLevel()
    {
        $environment  = \Slim\Http\Environment::mock(['REQUEST_METHOD' => 'GET']);
        $request      = \Slim\Http\Request::createFromEnvironment($environment);

        $secondLevelController = new \Doctype\controllers\SecondLevelController();
        $response          = $secondLevelController->initSecondLevel($request, new \Slim\Http\Response());
        $responseBody      = json_decode((string)$response->getBody());

        $this->assertNotNull($responseBody->firstLevel);
        $this->assertNotNull($responseBody->firstLevel[0]->doctypes_first_level_id);
        $this->assertNotNull($responseBody->firstLevel[0]->doctypes_first_level_label);
    }

    public function testinitDoctype()
    {
        $environment  = \Slim\Http\Environment::mock(['REQUEST_METHOD' => 'GET']);
        $request      = \Slim\Http\Request::createFromEnvironment($environment);

        $doctypeController = new \Doctype\controllers\DoctypeController();
        $response          = $doctypeController->initDoctype($request, new \Slim\Http\Response());
        $responseBody      = json_decode((string)$response->getBody());

        $this->assertNotNull($responseBody->secondLevel);
        $this->assertNotNull($responseBody->processModes);
        $this->assertNotNull($responseBody->processModes->processing_modes);
        $this->assertNotNull($responseBody->processModes->process_mode_priority);
        $this->assertNotNull($responseBody->models);
        $this->assertNotNull($responseBody->models[0]->template_id);
        $this->assertNotNull($responseBody->models[0]->template_label);
        $this->assertNotNull($responseBody->models[0]->template_comment);
    }

    public function testCreateFirstLevel()
    {
        $firstLevelController = new \Doctype\controllers\FirstLevelController();

        //  CREATE
        $environment = \Slim\Http\Environment::mock(['REQUEST_METHOD' => 'POST']);
        $request     = \Slim\Http\Request::createFromEnvironment($environment);

        $aArgs = [
            'doctypes_first_level_label' => 'testTUfirstlevel',
            'foldertype_id'              => [1],
            'css_style'                    => '#99999',
            'enabled'                    => 'Y',
        ];
        $fullRequest = \httpRequestCustom::addContentInBody($aArgs, $request);

        $response     = $firstLevelController->create($fullRequest, new \Slim\Http\Response());
        $responseBody = json_decode((string)$response->getBody());

        self::$firstLevelId = $responseBody->firstLevel;

        $this->assertInternalType('int', self::$firstLevelId);

        // CREATE FAIL
        $aArgs = [
            'doctypes_first_level_label' => '',
            'foldertype_id'              => [],
            'css_style'                  => '#7777',
            'enabled'                    => 'gaz',
        ];
        $fullRequest = \httpRequestCustom::addContentInBody($aArgs, $request);

        $response     = $firstLevelController->create($fullRequest, new \Slim\Http\Response());
        $responseBody = json_decode((string)$response->getBody());

        $this->assertSame('Invalid doctypes_first_level_label', $responseBody->errors[0]);
        $this->assertSame('Invalid foldertype_id', $responseBody->errors[1]);
    }

    public function testCreateSecondLevel()
    {
        $secondLevelController = new \Doctype\controllers\SecondLevelController();

        //  CREATE
        $environment = \Slim\Http\Environment::mock(['REQUEST_METHOD' => 'POST']);
        $request     = \Slim\Http\Request::createFromEnvironment($environment);

        $aArgs = [
            'doctypes_second_level_label' => 'testTUsecondlevel',
            'doctypes_first_level_id'     => self::$firstLevelId,
            'css_style'                   => '#99999',
            'enabled'                     => 'Y',
        ];
        $fullRequest = \httpRequestCustom::addContentInBody($aArgs, $request);

        $response     = $secondLevelController->create($fullRequest, new \Slim\Http\Response());
        $responseBody = json_decode((string)$response->getBody());

        self::$secondLevelId = $responseBody->secondLevel;

        $this->assertInternalType('int', self::$secondLevelId);

        // CREATE FAIL
        $aArgs = [
            'doctypes_second_level_label' => '',
            'doctypes_first_level_id'     => '',
            'css_style'                  => '#7777',
            'enabled'                    => 'gaz',
        ];
        $fullRequest = \httpRequestCustom::addContentInBody($aArgs, $request);

        $response     = $secondLevelController->create($fullRequest, new \Slim\Http\Response());
        $responseBody = json_decode((string)$response->getBody());

        $this->assertSame('Invalid doctypes_second_level_label', $responseBody->errors[0]);
        $this->assertSame('Invalid doctypes_first_level_id', $responseBody->errors[1]);
    }

    public function testCreateDoctype()
    {
        $doctypeController = new \Doctype\controllers\DoctypeController();

        //  CREATE
        $environment = \Slim\Http\Environment::mock(['REQUEST_METHOD' => 'POST']);
        $request     = \Slim\Http\Request::createFromEnvironment($environment);

        $aArgs = [
            'description'                 => 'testUDoctype',
            'doctypes_first_level_id'     => self::$firstLevelId,
            'doctypes_second_level_id'    => self::$secondLevelId,
            'retention_final_disposition' => 'destruction',
            'retention_rule'              => 'compta_3_03',
            'duration_current_use'        => '10',
            'process_delay'               => '18',
            'delay1'                      => '10',
            'delay2'                      => '5',
            'process_mode'                => 'NORMAL',
            'template_id'                 => '',
            'is_generated'                => 'N',
            'indexes'                     => [
                                            'custom_t1' => 'N',
                                            'custom_t2' => 'Y',
                                        ],
        ];
        $fullRequest = \httpRequestCustom::addContentInBody($aArgs, $request);

        $response     = $doctypeController->create($fullRequest, new \Slim\Http\Response());
        $responseBody = json_decode((string)$response->getBody());

        self::$doctypeId = $responseBody->doctype;

        $this->assertInternalType('int', self::$doctypeId);

        // CREATE FAIL
        $aArgs = [
            'description'                 => '',
            'doctypes_first_level_id'     => '',
            'doctypes_second_level_id'    => '',
            'retention_final_disposition' => '',
            'retention_rule'              => 'compta_3_03',
            'duration_current_use'        => '3',
            'process_delay'               => '',
            'delay1'                      => '',
            'delay2'                      => '',
            'process_mode'                => '',
            'template_id'                 => '',
            'is_generated'                => 'N',
        ];
        $fullRequest = \httpRequestCustom::addContentInBody($aArgs, $request);

        $response     = $doctypeController->create($fullRequest, new \Slim\Http\Response());
        $responseBody = json_decode((string)$response->getBody());

        $this->assertSame('Invalid description', $responseBody->errors[0]);
        $this->assertSame('Invalid doctypes_first_level_id', $responseBody->errors[1]);
        $this->assertSame('Invalid doctypes_second_level_id value', $responseBody->errors[2]);
        $this->assertSame('Invalid process_delay value', $responseBody->errors[3]);
        $this->assertSame('Invalid delay1 value', $responseBody->errors[4]);
        $this->assertSame('Invalid delay2 value', $responseBody->errors[5]);
    }

    public function testUpdateFirstLevel()
    {
        $firstLevelController = new \Doctype\controllers\FirstLevelController();

        //  UPDATE
        $environment = \Slim\Http\Environment::mock(['REQUEST_METHOD' => 'PUT']);
        $request     = \Slim\Http\Request::createFromEnvironment($environment);

        $aArgs = [
            'doctypes_first_level_label' => 'testTUfirstlevelUPDATE',
            'foldertype_id'              => [1],
            'css_style'                  => '#7777',
            'enabled'                    => 'Y',
        ];
        $fullRequest = \httpRequestCustom::addContentInBody($aArgs, $request);
        $response     = $firstLevelController->update($fullRequest, new \Slim\Http\Response(), ["id" => self::$firstLevelId]);
        $responseBody = json_decode((string)$response->getBody());

        $this->assertSame(self::$firstLevelId, $responseBody->firstLevel->doctypes_first_level_id);
        $this->assertSame('testTUfirstlevelUPDATE', $responseBody->firstLevel->doctypes_first_level_label);
        $this->assertSame('#7777', $responseBody->firstLevel->css_style);
        $this->assertSame('Y', $responseBody->firstLevel->enabled);

        // UPDATE FAIL
        $aArgs = [
            'doctypes_first_level_label' => '',
            'foldertype_id'              => [],
            'css_style'                  => '#7777',
            'enabled'                    => 'gaz',
        ];
        $fullRequest = \httpRequestCustom::addContentInBody($aArgs, $request);

        $response     = $firstLevelController->update($fullRequest, new \Slim\Http\Response(), ["id" => 'gaz']);
        $responseBody = json_decode((string)$response->getBody());

        $this->assertSame('Id is not a numeric', $responseBody->errors[0]);
        $this->assertSame('Id gaz does not exists', $responseBody->errors[1]);
        $this->assertSame('Invalid doctypes_first_level_label', $responseBody->errors[2]);
        $this->assertSame('Invalid foldertype_id', $responseBody->errors[3]);
    }

    public function testUpdateSecondLevel()
    {
        $secondLevelController = new \Doctype\controllers\SecondLevelController();

        //  UPDATE
        $environment = \Slim\Http\Environment::mock(['REQUEST_METHOD' => 'PUT']);
        $request     = \Slim\Http\Request::createFromEnvironment($environment);

        $aArgs = [
            'doctypes_second_level_label' => 'testTUsecondlevelUPDATE',
            'doctypes_first_level_id'     => self::$firstLevelId,
            'css_style'                   => '#7777',
            'enabled'                     => 'Y',
        ];
        $fullRequest = \httpRequestCustom::addContentInBody($aArgs, $request);

        $response     = $secondLevelController->update($fullRequest, new \Slim\Http\Response(), ["id" => self::$secondLevelId]);
        $responseBody = json_decode((string)$response->getBody());

        $this->assertSame(self::$secondLevelId, $responseBody->secondLevel->doctypes_second_level_id);
        $this->assertSame('testTUsecondlevelUPDATE', $responseBody->secondLevel->doctypes_second_level_label);
        $this->assertSame(self::$firstLevelId, $responseBody->secondLevel->doctypes_first_level_id);
        $this->assertSame('#7777', $responseBody->secondLevel->css_style);
        $this->assertSame('Y', $responseBody->secondLevel->enabled);

        // UPDATE FAIL
        $aArgs = [
            'doctypes_second_level_label' => '',
            'doctypes_first_level_id'     => '',
            'css_style'                  => '#7777',
            'enabled'                    => 'gaz',
        ];
        $fullRequest = \httpRequestCustom::addContentInBody($aArgs, $request);

        $response     = $secondLevelController->update($fullRequest, new \Slim\Http\Response(), ["id" => 'gaz']);
        $responseBody = json_decode((string)$response->getBody());

        $this->assertSame('Id is not a numeric', $responseBody->errors[0]);
        $this->assertSame('Id gaz does not exists', $responseBody->errors[1]);
        $this->assertSame('Invalid doctypes_second_level_label', $responseBody->errors[2]);
        $this->assertSame('Invalid doctypes_first_level_id', $responseBody->errors[3]);
    }

    public function testUpdateDoctype()
    {
        $doctypeController = new \Doctype\controllers\DoctypeController();

        //  UPDATE
        $environment = \Slim\Http\Environment::mock(['REQUEST_METHOD' => 'PUT']);
        $request     = \Slim\Http\Request::createFromEnvironment($environment);

        $aArgs = [
            'description'                 => 'testUDoctypeUPDATE',
            'doctypes_first_level_id'     => self::$firstLevelId,
            'doctypes_second_level_id'    => self::$secondLevelId,
            'retention_final_disposition' => 'destruction',
            'retention_rule'              => 'compta_3_03',
            'duration_current_use'        => '12',
            'process_delay'               => '17',
            'delay1'                      => '11',
            'delay2'                      => '6',
            'process_mode'                => 'SVR',
            'template_id'                 => '',
            'is_generated'                => 'N',
            'indexes'                     => [
                                            'custom_t1' => 'N',
                                        ],
        ];
        $fullRequest = \httpRequestCustom::addContentInBody($aArgs, $request);

        $response     = $doctypeController->update($fullRequest, new \Slim\Http\Response(), ["id" => self::$doctypeId]);
        $responseBody = json_decode((string)$response->getBody());

        $this->assertSame(self::$doctypeId, $responseBody->doctype);

        // UPDATE FAIL
        $aArgs = [
            'description'                 => '',
            'doctypes_first_level_id'     => '',
            'doctypes_second_level_id'    => '',
            'retention_final_disposition' => '',
            'retention_rule'              => 'compta_3_03',
            'duration_current_use'        => '3',
            'process_delay'               => '',
            'delay1'                      => '',
            'delay2'                      => '',
            'process_mode'                => '',
            'template_id'                 => '',
            'is_generated'                => 'N',
        ];
        $fullRequest = \httpRequestCustom::addContentInBody($aArgs, $request);

        $response     = $doctypeController->update($fullRequest, new \Slim\Http\Response(), ["id" => 'gaz']);
        $responseBody = json_decode((string)$response->getBody());

        $this->assertSame('type_id is not a numeric', $responseBody->errors[0]);
        $this->assertSame('Id gaz does not exists', $responseBody->errors[1]);
        $this->assertSame('Invalid description', $responseBody->errors[2]);
        $this->assertSame('Invalid doctypes_first_level_id', $responseBody->errors[3]);
        $this->assertSame('Invalid doctypes_second_level_id value', $responseBody->errors[4]);
        $this->assertSame('Invalid process_delay value', $responseBody->errors[5]);
        $this->assertSame('Invalid delay1 value', $responseBody->errors[6]);
        $this->assertSame('Invalid delay2 value', $responseBody->errors[7]);
    }

    public function testRead()
    {
        //  READ FIRST LEVEL
        $environment  = \Slim\Http\Environment::mock(['REQUEST_METHOD' => 'GET']);
        $request      = \Slim\Http\Request::createFromEnvironment($environment);

        $firstLevelController = new \Doctype\controllers\FirstLevelController();
        $response          = $firstLevelController->getById($request, new \Slim\Http\Response(), ["id" => self::$firstLevelId]);
        $responseBody      = json_decode((string)$response->getBody());
 
        $this->assertSame(self::$firstLevelId, $responseBody->firstLevel->doctypes_first_level_id);
        $this->assertSame('testTUfirstlevelUPDATE', $responseBody->firstLevel->doctypes_first_level_label);
        $this->assertSame('#7777', $responseBody->firstLevel->css_style);
        $this->assertSame(true, $responseBody->firstLevel->enabled);
        $this->assertNotNull($responseBody->folderTypeSelected);
        $this->assertNotNull($responseBody->folderTypes);

        // READ FIRST LEVEL FAIL
        $response          = $firstLevelController->getById($request, new \Slim\Http\Response(), ["id" => 'GAZ']);
        $responseBody      = json_decode((string)$response->getBody());
 
        $this->assertSame('wrong format for id', $responseBody->errors);

        //  READ SECOND LEVEL
        $secondLevelController = new \Doctype\controllers\SecondLevelController();
        $response     = $secondLevelController->getById($request, new \Slim\Http\Response(), ["id" => self::$secondLevelId]);
        $responseBody = json_decode((string)$response->getBody());

        $this->assertSame(self::$secondLevelId, $responseBody->secondLevel->doctypes_second_level_id);
        $this->assertSame('testTUsecondlevelUPDATE', $responseBody->secondLevel->doctypes_second_level_label);
        $this->assertSame(self::$firstLevelId, $responseBody->secondLevel->doctypes_first_level_id);
        $this->assertSame(true, $responseBody->secondLevel->enabled);

        // READ SECOND LEVEL FAIL
        $response          = $secondLevelController->getById($request, new \Slim\Http\Response(), ["id" => 'GAZ']);
        $responseBody      = json_decode((string)$response->getBody());
 
        $this->assertSame('wrong format for id', $responseBody->errors);

        // READ DOCTYPE
        $doctypeController = new \Doctype\controllers\DoctypeController();
        $response     = $doctypeController->getById($request, new \Slim\Http\Response(), ["id" => self::$doctypeId]);
        $responseBody = json_decode((string)$response->getBody());

        $this->assertSame(self::$doctypeId, $responseBody->doctype->type_id);
        $this->assertSame('testUDoctypeUPDATE', $responseBody->doctype->description);
        $this->assertSame(self::$firstLevelId, $responseBody->doctype->doctypes_first_level_id);
        $this->assertSame(self::$secondLevelId, $responseBody->doctype->doctypes_second_level_id);
        $this->assertSame('destruction', $responseBody->doctype->retention_final_disposition);
        $this->assertSame('compta_3_03', $responseBody->doctype->retention_rule);
        $this->assertSame(12, $responseBody->doctype->duration_current_use);
        $this->assertSame(17, $responseBody->doctypeExt->process_delay);
        $this->assertSame(11, $responseBody->doctypeExt->delay1);
        $this->assertSame(6, $responseBody->doctypeExt->delay2);
        $this->assertSame('SVR', $responseBody->doctypeExt->process_mode);
        $this->assertNotNull($responseBody->secondLevel);
        $this->assertNotNull($responseBody->processModes);
        $this->assertSame(null, $responseBody->modelSelected->template_id);
        $this->assertSame('N', $responseBody->modelSelected->is_generated);
        $this->assertSame(self::$doctypeId, $responseBody->modelSelected->type_id);
        $this->assertNotNull($responseBody->models);
        $this->assertNotNull($responseBody->indexes);
        $this->assertSame(self::$doctypeId, $responseBody->indexesSelected[0]->type_id);
        $this->assertSame('custom_t1', $responseBody->indexesSelected[0]->field_name);
        $this->assertSame('N', $responseBody->indexesSelected[0]->mandatory);

        // READ DOCTYPE FAIL
        $response          = $doctypeController->getById($request, new \Slim\Http\Response(), ["id" => 'GAZ']);
        $responseBody      = json_decode((string)$response->getBody());
 
        $this->assertSame('wrong format for id', $responseBody->errors);
    }

    public function testDeleteRedirectDoctype()
    {
        $doctypeController = new \Doctype\controllers\DoctypeController();

        //  CREATE
        $environment = \Slim\Http\Environment::mock(['REQUEST_METHOD' => 'POST']);
        $request     = \Slim\Http\Request::createFromEnvironment($environment);

        $aArgs = [
            'description'                 => 'testUDoctype',
            'doctypes_first_level_id'     => self::$firstLevelId,
            'doctypes_second_level_id'    => self::$secondLevelId,
            'retention_final_disposition' => 'destruction',
            'retention_rule'              => 'compta_3_03',
            'duration_current_use'        => '10',
            'process_delay'               => '18',
            'delay1'                      => '10',
            'delay2'                      => '5',
            'process_mode'                => 'NORMAL',
            'template_id'                 => '',
            'is_generated'                => 'N',
            'indexes'                     => [
                                            'custom_t1' => 'N',
                                            'custom_t2' => 'Y',
                                        ],
        ];
        $fullRequest = \httpRequestCustom::addContentInBody($aArgs, $request);

        $response     = $doctypeController->create($fullRequest, new \Slim\Http\Response());
        $responseBody = json_decode((string)$response->getBody());

        $doctypeId = $responseBody->doctype;

        $resController = new \Resource\controllers\ResController();

        //  CREATE
        $environment    = \Slim\Http\Environment::mock(['REQUEST_METHOD' => 'POST']);
        $request        = \Slim\Http\Request::createFromEnvironment($environment);

        $fileContent = file_get_contents('modules/convert/Test/Samples/test.txt');
        $encodedFile = base64_encode($fileContent);

        $data = [
            [
                'column'    => 'subject',
                'value'     => 'subject value test U',
                'type'      => 'string',
            ],
            [
                'column'    => 'type_id',
                'value'     => $doctypeId,
                'type'      => 'integer',
            ],
            [
                'column'    => 'typist',
                'value'     => 'LLane',
                'type'      => 'string',
            ]
        ];

        $aArgs = [
            'collId'        => 'letterbox_coll',
            'table'         => 'res_letterbox',
            'status'        => 'NEW',
            'encodedFile'   => $encodedFile,
            'fileFormat'    => 'txt',
            'data'          => $data
        ];

        $fullRequest = \httpRequestCustom::addContentInBody($aArgs, $request);
        $response     = $resController->create($fullRequest, new \Slim\Http\Response());

        $responseBody = json_decode((string)$response->getBody());

        $resId = $responseBody->resId;

        //  CAN NOT DELETE
        $environment = \Slim\Http\Environment::mock(['REQUEST_METHOD' => 'DELETE']);
        $request     = \Slim\Http\Request::createFromEnvironment($environment);

        $aArgs = [];
        $fullRequest = \httpRequestCustom::addContentInBody($aArgs, $request);

        $response     = $doctypeController->delete($fullRequest, new \Slim\Http\Response(), ["id" => $doctypeId]);
        $responseBody = json_decode((string)$response->getBody());

        $this->assertSame(false, $responseBody->deleted);

        $aArgs = [
            "new_type_id" => self::$doctypeId
        ];
        $fullRequest = \httpRequestCustom::addContentInBody($aArgs, $request);
        $response     = $doctypeController->deleteRedirect($fullRequest, new \Slim\Http\Response(), ["id" => $doctypeId]);
        $responseBody = json_decode((string)$response->getBody());

        $this->assertSame(true, $responseBody->doctype);

        $res = \Resource\models\ResModel::getById(['resId' => $resId]);
        $this->assertSame(self::$doctypeId, $res['type_id']);

        DatabaseModel::delete([
            'table' => 'res_letterbox',
            'where' => ['type_id = ?'],
            'data'  => [self::$doctypeId]
        ]);

        // DELETE REDIRECT FAIL
        $aArgs = [
            "new_type_id" => 'gaz'
        ];
        $fullRequest = \httpRequestCustom::addContentInBody($aArgs, $request);
        $response     = $doctypeController->deleteRedirect($fullRequest, new \Slim\Http\Response(), ["id" => $doctypeId]);
        $responseBody = json_decode((string)$response->getBody());

        $this->assertSame('wrong format for new_type_id', $responseBody->errors);
    }

    public function testDeleteDoctype()
    {
        $doctypeController = new \Doctype\controllers\DoctypeController();

        //  DELETE
        $environment = \Slim\Http\Environment::mock(['REQUEST_METHOD' => 'DELETE']);
        $request     = \Slim\Http\Request::createFromEnvironment($environment);

        $aArgs = [];
        $fullRequest = \httpRequestCustom::addContentInBody($aArgs, $request);

        $response     = $doctypeController->delete($fullRequest, new \Slim\Http\Response(), ["id" => self::$doctypeId]);
        $responseBody = json_decode((string)$response->getBody());

        $this->assertSame(true, $responseBody->deleted);
        $this->assertNotNull($responseBody->doctypes);

        //  DELETE FAIL
        $environment = \Slim\Http\Environment::mock(['REQUEST_METHOD' => 'DELETE']);
        $request     = \Slim\Http\Request::createFromEnvironment($environment);

        $aArgs = [];
        $fullRequest = \httpRequestCustom::addContentInBody($aArgs, $request);

        $response     = $doctypeController->delete($fullRequest, new \Slim\Http\Response(), ["id" => 'gaz']);
        $responseBody = json_decode((string)$response->getBody());

        $this->assertSame('Id is not a numeric', $responseBody->errors);
    }

    public function testDeleteSecondLevel()
    {
        $secondLevelController = new \Doctype\controllers\SecondLevelController();

        //  DELETE
        $environment = \Slim\Http\Environment::mock(['REQUEST_METHOD' => 'DELETE']);
        $request     = \Slim\Http\Request::createFromEnvironment($environment);

        $aArgs = [];
        $fullRequest = \httpRequestCustom::addContentInBody($aArgs, $request);

        $response     = $secondLevelController->delete($fullRequest, new \Slim\Http\Response(), ["id" => self::$secondLevelId]);
        $responseBody = json_decode((string)$response->getBody());

        $this->assertSame(self::$secondLevelId, $responseBody->secondLevel->doctypes_second_level_id);
        $this->assertSame(self::$firstLevelId, $responseBody->secondLevel->doctypes_first_level_id);
        $this->assertSame('testTUsecondlevelUPDATE', $responseBody->secondLevel->doctypes_second_level_label);
        $this->assertSame('#7777', $responseBody->secondLevel->css_style);
        $this->assertSame('N', $responseBody->secondLevel->enabled);

        //  DELETE FAIL
        $environment = \Slim\Http\Environment::mock(['REQUEST_METHOD' => 'DELETE']);
        $request     = \Slim\Http\Request::createFromEnvironment($environment);

        $aArgs = [];
        $fullRequest = \httpRequestCustom::addContentInBody($aArgs, $request);

        $response     = $secondLevelController->delete($fullRequest, new \Slim\Http\Response(), ["id" => 'gaz']);
        $responseBody = json_decode((string)$response->getBody());

        $this->assertSame('Id is not a numeric', $responseBody->errors);
    }

    public function testDeleteFirstLevel()
    {
        $firstLevelController = new \Doctype\controllers\FirstLevelController();

        //  DELETE
        $environment = \Slim\Http\Environment::mock(['REQUEST_METHOD' => 'DELETE']);
        $request     = \Slim\Http\Request::createFromEnvironment($environment);

        $aArgs = [];
        $fullRequest = \httpRequestCustom::addContentInBody($aArgs, $request);

        $response     = $firstLevelController->delete($fullRequest, new \Slim\Http\Response(), ["id" => self::$firstLevelId]);
        $responseBody = json_decode((string)$response->getBody());

        $this->assertSame(self::$firstLevelId, $responseBody->firstLevel->doctypes_first_level_id);
        $this->assertSame('testTUfirstlevelUPDATE', $responseBody->firstLevel->doctypes_first_level_label);
        $this->assertSame('#7777', $responseBody->firstLevel->css_style);
        $this->assertSame('N', $responseBody->firstLevel->enabled);

        //  DELETE FAIL
        $environment = \Slim\Http\Environment::mock(['REQUEST_METHOD' => 'DELETE']);
        $request     = \Slim\Http\Request::createFromEnvironment($environment);

        $aArgs = [];
        $fullRequest = \httpRequestCustom::addContentInBody($aArgs, $request);

        $response     = $firstLevelController->delete($fullRequest, new \Slim\Http\Response(), ["id" => 'gaz']);
        $responseBody = json_decode((string)$response->getBody());

        $this->assertSame('Id is not a numeric', $responseBody->errors);
    }

    public function testDeleteSQL()
    {
        DatabaseModel::delete([
            'table' => 'doctypes_first_level',
            'where' => ['doctypes_first_level_id = ?'],
            'data'  => [self::$firstLevelId]
        ]);
        DatabaseModel::delete([
            'table' => 'doctypes_second_level',
            'where' => ['doctypes_second_level_id = ?'],
            'data'  => [self::$secondLevelId]
        ]);
    }
}