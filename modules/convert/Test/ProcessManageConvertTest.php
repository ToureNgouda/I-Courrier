<?php

/**
* Copyright Maarch since 2008 under licence GPLv3.
* See LICENCE.txt file at the root folder for more details.
* This file is part of Maarch software.
*
*/

namespace MaarchTest;
use PHPUnit\Framework\TestCase;

class ManageProcessConvertTest extends TestCase
{
    public function testmanageConvert ()
    {
        $action = new \Resource\controllers\ResController();

        $environment = \Slim\Http\Environment::mock(
            [
                'REQUEST_METHOD' => 'POST',
            ]
        );

        $samplePath = 'modules/convert/Test/Samples/';

        //SAMPLE TXT
        $fileSource = 'test.txt';
        $fileFormat = 'txt';

        $fileContent = file_get_contents($samplePath . $fileSource, FILE_BINARY);
        $encodedFile = base64_encode($fileContent);
        //echo $encodedFile . PHP_EOL;exit;
        
        $data = [];

        array_push(
            $data,
            array(
                'column' => 'subject',
                'value' => 'UNIT TEST CONVERT ALL from slim with ' . $fileFormat . ' file',
                'type' => 'string',
            )
        );

        array_push(
            $data,
            array(
                'column' => 'type_id',
                'value' => 110,
                'type' => 'integer',
            )
        );

        array_push(
            $data,
            array(
                'column' => 'custom_t1',
                'value' => 'TEST',
                'type' => 'string',
            )
        );

        array_push(
            $data,
            array(
                'column' => 'custom_t10',
                'value' => 'lgi@maarch.org',
                'type' => 'string',
            )
        );

        $aArgs = [
            'encodedFile'   => $encodedFile,
            'data'          => $data,
            'collId'        => 'letterbox_coll',
            'table'         => 'res_letterbox',
            'fileFormat'    => $fileFormat,
            'status'        => 'new',
        ];

        $request = \Slim\Http\Request::createFromEnvironment($environment);
        $response = new \Slim\Http\Response();
        $fullRequest = \httpRequestCustom::addContentInBody($aArgs, $request);

        $response = $action->create($fullRequest, $response);
        $responseBody = json_decode((string)$response->getBody());
        
        $resIdTxt = $responseBody->resId;

        //SAMPLE PDF
        $fileSource = 'test.pdf';
        $fileFormat = 'pdf';

        $fileContent = file_get_contents($samplePath . $fileSource, FILE_BINARY);
        $encodedFile = base64_encode($fileContent);
        //echo $encodedFile . PHP_EOL;exit;
        
        $data = [];

        array_push(
            $data,
            array(
                'column' => 'subject',
                'value' => 'UNIT TEST CONVERT ALL from slim with ' . $fileFormat . ' file',
                'type' => 'string',
            )
        );

        array_push(
            $data,
            array(
                'column' => 'type_id',
                'value' => 110,
                'type' => 'integer',
            )
        );

        array_push(
            $data,
            array(
                'column' => 'custom_t1',
                'value' => 'TEST',
                'type' => 'string',
            )
        );

        array_push(
            $data,
            array(
                'column' => 'custom_t10',
                'value' => 'lgi@maarch.org',
                'type' => 'string',
            )
        );

        $aArgs = [
            'encodedFile'   => $encodedFile,
            'data'          => $data,
            'collId'        => 'letterbox_coll',
            'table'         => 'res_letterbox',
            'fileFormat'    => $fileFormat,
            'status'        => 'new',
        ];

        $request = \Slim\Http\Request::createFromEnvironment($environment);
        $response = new \Slim\Http\Response();
        $fullRequest = \httpRequestCustom::addContentInBody($aArgs, $request);

        $response = $action->create($fullRequest, $response);
        $responseBody = json_decode((string)$response->getBody());
        
        $resIdPdf = $responseBody->resId;

        //SAMPLE ODT
        $fileSource = 'test.odt';
        $fileFormat = 'odt';

        $fileContent = file_get_contents($samplePath . $fileSource, FILE_BINARY);
        $encodedFile = base64_encode($fileContent);
        //echo $encodedFile . PHP_EOL;exit;
        
        $data = [];

        array_push(
            $data,
            array(
                'column' => 'subject',
                'value' => 'UNIT TEST CONVERT ALL from slim with ' . $fileFormat . ' file',
                'type' => 'string',
            )
        );

        array_push(
            $data,
            array(
                'column' => 'type_id',
                'value' => 110,
                'type' => 'integer',
            )
        );

        array_push(
            $data,
            array(
                'column' => 'custom_t1',
                'value' => 'TEST',
                'type' => 'string',
            )
        );

        array_push(
            $data,
            array(
                'column' => 'custom_t10',
                'value' => 'lgi@maarch.org',
                'type' => 'string',
            )
        );

        $aArgs = [
            'encodedFile'   => $encodedFile,
            'data'          => $data,
            'collId'        => 'letterbox_coll',
            'table'         => 'res_letterbox',
            'fileFormat'    => $fileFormat,
            'status'        => 'new',
        ];

        $request = \Slim\Http\Request::createFromEnvironment($environment);
        $response = new \Slim\Http\Response();
        $fullRequest = \httpRequestCustom::addContentInBody($aArgs, $request);

        $response = $action->create($fullRequest, $response);
        $responseBody = json_decode((string)$response->getBody());
        
        $resIdOdt = $responseBody->resId;

        //SAMPLE HTML
        $fileSource = 'test.html';
        $fileFormat = 'html';

        $fileContent = file_get_contents($samplePath . $fileSource, FILE_BINARY);
        $encodedFile = base64_encode($fileContent);
        //echo $encodedFile . PHP_EOL;exit;
        
        $data = [];

        array_push(
            $data,
            array(
                'column' => 'subject',
                'value' => 'UNIT TEST CONVERT ALL from slim with ' . $fileFormat . ' file',
                'type' => 'string',
            )
        );

        array_push(
            $data,
            array(
                'column' => 'type_id',
                'value' => 110,
                'type' => 'integer',
            )
        );

        array_push(
            $data,
            array(
                'column' => 'custom_t1',
                'value' => 'TEST',
                'type' => 'string',
            )
        );

        array_push(
            $data,
            array(
                'column' => 'custom_t10',
                'value' => 'lgi@maarch.org',
                'type' => 'string',
            )
        );

        $aArgs = [
            'encodedFile'   => $encodedFile,
            'data'          => $data,
            'collId'        => 'letterbox_coll',
            'table'         => 'res_letterbox',
            'fileFormat'    => $fileFormat,
            'status'        => 'new',
        ];

        $request = \Slim\Http\Request::createFromEnvironment($environment);
        $response = new \Slim\Http\Response();
        $fullRequest = \httpRequestCustom::addContentInBody($aArgs, $request);

        $response = $action->create($fullRequest, $response);
        $responseBody = json_decode((string)$response->getBody());
        
        $resIdHtml = $responseBody->resId;

        /***************************************************************************/

        //test TXT
        $request = \Slim\Http\Request::createFromEnvironment($environment);
        $action = new \Convert\Controllers\ProcessManageConvertController();

        $aArgs = [
            'collId' => 'letterbox_coll', 
            'resTable' => 'res_letterbox', 
            'adrTable' => 'adr_letterbox', 
            'resId' => $resIdTxt, 
            'tmpDir' => $_SESSION['config']['tmppath'],
            'createZendIndex' => true
        ];

        $response = new \Slim\Http\Response();
        $fullRequest = \httpRequestCustom::addContentInBody($aArgs, $request);

        $response = $action->create($fullRequest, $response);
        $responseBody = json_decode((string)$response->getBody());
        $status = $responseBody->status;
        
        $this->assertEquals('0', $status);

        //test PDF
        $request = \Slim\Http\Request::createFromEnvironment($environment);
        $action = new \Convert\Controllers\ProcessManageConvertController();

        $aArgs = [
            'collId' => 'letterbox_coll', 
            'resTable' => 'res_letterbox', 
            'adrTable' => 'adr_letterbox', 
            'resId' => $resIdPdf, 
            'tmpDir' => $_SESSION['config']['tmppath'],
            'createZendIndex' => true
        ];

        $response = new \Slim\Http\Response();
        $fullRequest = \httpRequestCustom::addContentInBody($aArgs, $request);

        $response = $action->create($fullRequest, $response);
        $responseBody = json_decode((string)$response->getBody());
        $status = $responseBody->status;
        
        $this->assertEquals('0', $status);

        //test ODT
        $request = \Slim\Http\Request::createFromEnvironment($environment);
        $action = new \Convert\Controllers\ProcessManageConvertController();

        $aArgs = [
            'collId' => 'letterbox_coll', 
            'resTable' => 'res_letterbox', 
            'adrTable' => 'adr_letterbox', 
            'resId' => $resIdOdt, 
            'tmpDir' => $_SESSION['config']['tmppath'],
            'createZendIndex' => true
        ];

        $response = new \Slim\Http\Response();
        $fullRequest = \httpRequestCustom::addContentInBody($aArgs, $request);

        $response = $action->create($fullRequest, $response);
        $responseBody = json_decode((string)$response->getBody());
        $status = $responseBody->status;
        
        $this->assertEquals('0', $status);

        //test HTML
        $request = \Slim\Http\Request::createFromEnvironment($environment);
        $action = new \Convert\Controllers\ProcessManageConvertController();

        $aArgs = [
            'collId' => 'letterbox_coll', 
            'resTable' => 'res_letterbox', 
            'adrTable' => 'adr_letterbox', 
            'resId' => $resIdHtml, 
            'tmpDir' => $_SESSION['config']['tmppath'],
            'createZendIndex' => true
        ];

        $response = new \Slim\Http\Response();
        $fullRequest = \httpRequestCustom::addContentInBody($aArgs, $request);

        $response = $action->create($fullRequest, $response);
        $responseBody = json_decode((string)$response->getBody());
        $status = $responseBody->status;
        
        $this->assertEquals('0', $status);
    }
}
