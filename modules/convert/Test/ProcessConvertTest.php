<?php

/**
* Copyright Maarch since 2008 under licence GPLv3.
* See LICENCE.txt file at the root folder for more details.
* This file is part of Maarch software.
*
*/

namespace MaarchTest;
use PHPUnit\Framework\TestCase;

class ProcessConvertTest extends TestCase
{
    public function testconvert ()
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
                'value' => 'UNIT TEST CONVERT from slim',
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
        
        $resId = $responseBody->resId;
        
        if (!defined("_RES_ID_TEST_CONVERT")) {
            define("_RES_ID_TEST_CONVERT", $resId);
        }

        //real test
        $request = \Slim\Http\Request::createFromEnvironment($environment);
        $action = new \Convert\Controllers\ProcessConvertController();

        $aArgs = [
            'collId' => 'letterbox_coll', 
            'resTable' => 'res_letterbox', 
            'adrTable' => 'adr_letterbox', 
            'resId' => _RES_ID_TEST_CONVERT, 
            'tmpDir' => $_SESSION['config']['tmppath']
        ];

        $response = new \Slim\Http\Response();
        $fullRequest = \httpRequestCustom::addContentInBody($aArgs, $request);

        $response = $action->create($fullRequest, $response);
        //var_dump($response);
        $responseBody = json_decode((string)$response->getBody());
        $status = $responseBody->status;
        
        $this->assertEquals('0', $status);
    }
}
