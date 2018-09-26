<?php

/**
* Copyright Maarch since 2008 under licence GPLv3.
* See LICENCE.txt file at the root folder for more details.
* This file is part of Maarch software.
*
*/

namespace MaarchTest;
use PHPUnit\Framework\TestCase;

class ProcessThumbnailsTest extends TestCase
{

    public function testthumbnails ()
    {
        
        if (!defined("_RES_ID_TEST_CONVERT")) {
            define("_RES_ID_TEST_CONVERT", 100);
        }

        $action = new \Convert\Controllers\ProcessThumbnailsController();

        $environment = \Slim\Http\Environment::mock(
            [
                'REQUEST_METHOD' => 'POST',
            ]
        );

        $request = \Slim\Http\Request::createFromEnvironment($environment);

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
        //var_dump($responseBody);
        $status = $responseBody->status;
        
        $this->assertEquals('0', $status);
    }
}
