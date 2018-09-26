<?php

namespace MaarchTest;

/**
* Copyright Maarch since 2008 under licence GPLv3.
* See LICENCE.txt file at the root folder for more details.
* This file is part of Maarch software.
*
*/

use PHPUnit\Framework\TestCase;

class DocserverControllerTest extends TestCase
{
    public function testGetList()
    {
        $action = new \Docserver\controllers\DocserverController();

        $environment = \Slim\Http\Environment::mock(
            [
                'REQUEST_METHOD' => 'GET',
                //'REQUEST_URI' => '/status',
                //'QUERY_STRING'=>'foo=bar',
            ]
        );

        $request = \Slim\Http\Request::createFromEnvironment($environment);
        $response = new \Slim\Http\Response();
        $response = $action->getList($request, $response, []);

        $this->assertNotNull((string)$response->getBody());
    }

    /*public function testGetById()
    {
        $action = new \Docserver\controllers\DocserverController();

        $environment = \Slim\Http\Environment::mock(
            [
                'REQUEST_METHOD' => 'GET',
            ]
        );

        $aArgs = [
            'docserver_id'=> 'NEW'
        ];

        $request = \Slim\Http\Request::createFromEnvironment($environment);
        $response = new \Slim\Http\Response();
        $response = $action->getById($request, $response, $aArgs);
        $compare = '[{"status":[{"id":"NEW","label_status":"Nouveau",'
            . '"is_system":"Y","is_folder_status":"N","img_filename":'
            . '"fm-letter-status-new","maarch_module":"apps",'
            . '"can_be_searched":"Y","can_be_modified":"Y"}]}]';

        $this->assertSame((string)$response->getBody(), $compare);
    }*/

    // public function testCreate()
    // {
    //     $action = new \Docserver\controllers\DocserverController();

    //     $query  = 'id=TEST&';
    //     $query .= 'label_status=TEST';

    //     $environment = \Slim\Http\Environment::mock(
    //         [
    //             'REQUEST_METHOD' => 'POST',
    //             'QUERY_STRING'=> $query,
    //         ]
    //     );
        
    //     $aArgs = [
    //         'id'=> 'NEW'
    //     ];

    //     $request = \Slim\Http\Request::createFromEnvironment($environment);
    //     $response = new \Slim\Http\Response();
    //     $response = $action->create($request, $response, $aArgs);

    //     $compare = '[{"status":[{"id":"TEST","label_status":"TEST",'
    //         . '"is_system":"Y","is_folder_status":"N","img_filename":null,'
    //         . '"maarch_module":"apps","can_be_searched":"Y",'
    //         . '"can_be_modified":"Y"}]}]';
        
    //     $this->assertSame((string)$response->getBody(), $compare);
    // }

    // public function testUpdate()
    // {
    //     $action = new \Docserver\controllers\DocserverController();

    //     $query  = 'id=TEST&';
    //     $query .= 'label_status=TEST AFTER UP';

    //     $environment = \Slim\Http\Environment::mock(
    //         [
    //             'REQUEST_METHOD' => 'PUT',
    //             'QUERY_STRING'=> $query,
    //         ]
    //     );
        
    //     $aArgs = [
    //         'id'=> 'NEW'
    //     ];

    //     $request = \Slim\Http\Request::createFromEnvironment($environment);
    //     $response = new \Slim\Http\Response();
    //     $response = $action->update($request, $response, $aArgs);

    //     $compare = '[{"status":[{"id":"TEST","label_status":"TEST AFTER UP",'
    //         . '"is_system":"Y","is_folder_status":"N","img_filename":null,'
    //         . '"maarch_module":"apps","can_be_searched":"Y",'
    //         . '"can_be_modified":"Y"}]}]';
        
    //     $this->assertSame((string)$response->getBody(), $compare);
    // }

    // public function testDelete()
    // {
    //     $action = new \Docserver\controllers\DocserverController();

    //     $environment = \Slim\Http\Environment::mock(
    //         [
    //             'REQUEST_METHOD' => 'DELETE',
    //         ]
    //     );

    //     $aArgs = [
    //         'id'=> 'TEST'
    //     ];

    //     $request = \Slim\Http\Request::createFromEnvironment($environment);
    //     $response = new \Slim\Http\Response();
    //     $response = $action->delete($request, $response, $aArgs);
        
    //     $this->assertSame((string)$response->getBody(), '[{"status":true}]');
    // }

    public function testGetDocserverToInsert()
    {
        $action = new \Docserver\controllers\DocserverController();

        $aArgs = [
            'collId' => 'letterbox_coll'
        ];

        $response = $action->getDocserverToInsert($aArgs);

        $this->assertSame(
            $response[0]['coll_id'],
            $aArgs['collId']
        );
    }

    public function testCheckSize()
    {
        $action = new \Docserver\controllers\DocserverController();

        $aArgs = [
            'collId' => 'letterbox_coll'
        ];
        
        $ds = $action->getDocserverToInsert($aArgs);

        $aArgs = [
            'docserver' => $ds[0],
            'filesize'  => 1090900,
        ];

        $response = $action->checkSize($aArgs);
        
        $this->assertGreaterThan(0, $response['newDsSize']);
    }

    public function testSetSize()
    {
        $action = new \Docserver\controllers\DocserverController();

        $aArgs = [
            'collId' => 'letterbox_coll'
        ];
        
        $ds = $action->getDocserverToInsert($aArgs);

        $aArgs = [
            'docserver_id' => $ds[0]['docserver_id'],
            'actual_size_number'  => 1,
        ];

        $response = $action->setSize($aArgs);
        
        $this->assertTrue($response['setSize']);
    }

    public function testGetNextFileNameInDocserver()
    {
        $action = new \Docserver\controllers\DocserverController();

        $aArgs = [
            'pathOnDocserver' => '/opt/maarch/new_docservers/MaarchCourrierGit/manual/2017/02/'
        ];

        $response = $action->getNextFileNameInDocserver($aArgs);

        $this->assertNotNull($response['fileDestinationName']);
    }

    public function testStoreResourceOnDocserver()
    {
        $action = new \Docserver\controllers\DocserverController();

        $path = $_SESSION['config']['tmppath'] . '/test/';

        if (!is_dir($path)) {
            mkdir($path);
        }

        $fileSource = 'test_source.txt';

        $fp = fopen($path . $fileSource, 'a');
        fwrite($fp, 'a unit test');
        fclose($fp);

        $aArgs = [
            'collId' => 'letterbox_coll',
            'fileInfos' =>
                [
                    'tmpDir' => $path,
                    'size' => 122345,
                    'format' => 'txt',
                    'tmpFileName' => $fileSource,
                ]
        ];

        $response = $action->storeResourceOnDocserver($aArgs);

        //print_r($response);

        $this->assertArrayHasKey(
            'path_template',
            $response
        );
    }
}
