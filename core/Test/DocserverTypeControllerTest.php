<?php

/**
* Copyright Maarch since 2008 under licence GPLv3.
* See LICENCE.txt file at the root folder for more details.
* This file is part of Maarch software.
*
*/

namespace MaarchTest;
use PHPUnit\Framework\TestCase;

class DocserverTypeControllerTest extends TestCase
{
    public function testGetList()
    {
        $action = new \Docserver\controllers\DocserverTypeController();

        $environment = \Slim\Http\Environment::mock(
            [
                'REQUEST_METHOD' => 'GET',
                //'REQUEST_URI' => '/docserverType',
                //'QUERY_STRING'=>'foo=bar',
            ]
        );

        $request = \Slim\Http\Request::createFromEnvironment($environment);
        $response = new \Slim\Http\Response();
        $response = $action->getList($request, $response, []);

        $this->assertNotNull((string)$response->getBody());
    }

    public function testGetById()
    {
        $action = new \Docserver\controllers\DocserverTypeController();

        $environment = \Slim\Http\Environment::mock(
            [
                'REQUEST_METHOD' => 'GET',
            ]
        );

        $aArgs = [
            'docserver_type_id'=> 'FASTHD'
        ];

        $request = \Slim\Http\Request::createFromEnvironment($environment);
        $response = new \Slim\Http\Response();
        $response = $action->getById($request, $response, $aArgs);
        $compare = '[{"DocserverType":[{"docserver_type_id":"FASTHD",'
            . '"docserver_type_label":"FASTHD","enabled":"Y",'
            . '"is_container":"N","container_max_number":0,'
            . '"is_compressed":"N","compression_mode":"NONE",'
            . '"is_meta":"N","meta_template":"NONE",'
            . '"is_logged":"N","log_template":"NONE",'
            . '"is_signed":"Y","fingerprint_mode":"SHA256"}]}]';

        $this->assertSame((string)$response->getBody(), $compare);
    }

    public function testCreate()
    {
        $action = new \Docserver\controllers\DocserverTypeController();

        $query  = 'docserver_type_id=TEST&';
        $query .= 'docserver_type_label=TEST&';
        $query .= 'enabled=Y&';
        $query .= 'is_container=N&';
        $query .= 'is_compressed=N&';
        $query .= 'is_meta=N&';
        $query .= 'is_logged=N&';
        $query .= 'is_signed=N&';
        $query .= 'fingerprint_mode=SHA256';

        $environment = \Slim\Http\Environment::mock(
            [
                'REQUEST_METHOD' => 'POST',
                'QUERY_STRING'=> $query,
            ]
        );
        
        $aArgs = [
            'docserver_type_id'=> 'TEST'
        ];

        $request = \Slim\Http\Request::createFromEnvironment($environment);
        $response = new \Slim\Http\Response();
        $response = $action->create($request, $response, $aArgs);

        $compare = '[{"DocserverType":[{"docserver_type_id":"TEST",'
            . '"docserver_type_label":"TEST","enabled":"Y",'
            . '"is_container":"N","container_max_number":0,'
            . '"is_compressed":"N","compression_mode":null,'
            . '"is_meta":"N","meta_template":null,'
            . '"is_logged":"N","log_template":null,'
            . '"is_signed":"N","fingerprint_mode":"SHA256"}]}]';
        
        $this->assertSame((string)$response->getBody(), $compare);
    }

    public function testUpdate()
    {
        $action = new \Docserver\controllers\DocserverTypeController();

        $query  = 'docserver_type_id=TEST&';
        $query .= 'docserver_type_label=TEST&';
        $query .= 'enabled=Y&';
        $query .= 'is_container=N&';
        $query .= 'is_compressed=N&';
        $query .= 'is_meta=N&';
        $query .= 'is_logged=N&';
        $query .= 'is_signed=N&';
        $query .= 'fingerprint_mode=SHA512';

        $environment = \Slim\Http\Environment::mock(
            [
                'REQUEST_METHOD' => 'PUT',
                'QUERY_STRING'=> $query,
            ]
        );
        
        $aArgs = [
            'docserver_type_id'=> 'TEST'
        ];

        $request = \Slim\Http\Request::createFromEnvironment($environment);
        $response = new \Slim\Http\Response();
        $response = $action->update($request, $response, $aArgs);

        $compare = '[{"DocserverType":[{"docserver_type_id":"TEST",'
            . '"docserver_type_label":"TEST","enabled":"Y",'
            . '"is_container":"N","container_max_number":0,'
            . '"is_compressed":"N","compression_mode":null,'
            . '"is_meta":"N","meta_template":null,'
            . '"is_logged":"N","log_template":null,'
            . '"is_signed":"N","fingerprint_mode":"SHA512"}]}]';
        
        $this->assertSame((string)$response->getBody(), $compare);
    }

    public function testDelete()
    {
        $action = new \Docserver\controllers\DocserverTypeController();

        $environment = \Slim\Http\Environment::mock(
            [
                'REQUEST_METHOD' => 'DELETE',
            ]
        );

        $aArgs = [
            'docserver_type_id'=> 'TEST'
        ];

        $request = \Slim\Http\Request::createFromEnvironment($environment);
        $response = new \Slim\Http\Response();
        $response = $action->delete($request, $response, $aArgs);
        
        $this->assertSame((string)$response->getBody(), '[{"DocserverType":true}]');
    }
}
