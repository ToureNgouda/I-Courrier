<?php

/**
* Copyright Maarch since 2008 under licence GPLv3.
* See LICENCE.txt file at the root folder for more details.
* This file is part of Maarch software.
*
*/

use PHPUnit\Framework\TestCase;

class ContactControllerTest extends TestCase
{
    private static $id = null;
    private static $addressId = null;

    public function testCreate()
    {
        $contactController = new \Contact\controllers\ContactController();

        //  CREATE
        $environment    = \Slim\Http\Environment::mock(['REQUEST_METHOD' => 'POST']);
        $request        = \Slim\Http\Request::createFromEnvironment($environment);

        $aArgs = [
            'firstname'         => 'Hal',
            'lastname'          => 'Jordan',
            'contactType'       => '106',
            'contactPurposeId'  => '3',
            'isCorporatePerson' => 'N',
            'email'             => 'hal.jordan@glc.com',
            'society'           => 'Green Lantern Corps',
            'societyShort'      => 'GLC',
            'title'             => 'title1',
            'function'          => 'member',
            'addressNum'        => '1',
            'addressStreet'     => 'somewhere',
            'addressCountry'    => 'OA',
            'phone'             => '911',
        ];
        $fullRequest = \httpRequestCustom::addContentInBody($aArgs, $request);

        $response     = $contactController->create($fullRequest, new \Slim\Http\Response());
        $responseBody = json_decode((string)$response->getBody());

        $this->assertInternalType('int', $responseBody->contactId);
        $this->assertInternalType('int', $responseBody->addressId);

        self::$id = $responseBody->contactId;
        self::$addressId = $responseBody->addressId;


        //  READ
        $contact = \Contact\models\ContactModel::getById(['id' => self::$id]);

        $this->assertInternalType('array', $contact);
        $this->assertSame(self::$id, $contact['contact_id']);
        $this->assertSame(106, $contact['contact_type']);
        $this->assertSame('Green Lantern Corps', $contact['society']);
        $this->assertSame('GLC', $contact['society_short']);
        $this->assertSame('Hal', $contact['firstname']);
        $this->assertSame('Jordan', $contact['lastname']);
        $this->assertSame('title1', $contact['title']);
        $this->assertSame('member', $contact['function']);
        $this->assertSame('superadmin', $contact['user_id']);
        $this->assertSame('SUPERADMIN', $contact['entity_id']);
        $this->assertSame('Y', $contact['enabled']);

        $contact = \Contact\models\ContactModel::getByAddressId(['addressId' => self::$addressId]);

        $this->assertInternalType('array', $contact);
        $this->assertSame(self::$addressId, $contact['id']);
        $this->assertSame(self::$id, $contact['contact_id']);
        $this->assertSame(3, $contact['contact_purpose_id']);
        $this->assertSame(null, $contact['firstname']);
        $this->assertSame(null, $contact['lastname']);
        $this->assertSame('hal.jordan@glc.com', $contact['email']);
        $this->assertSame('1', $contact['address_num']);
        $this->assertSame(null, $contact['address_town']);
        $this->assertSame('somewhere', $contact['address_street']);
        $this->assertSame('OA', $contact['address_country']);
        $this->assertSame('911', $contact['phone']);
        $this->assertSame('superadmin', $contact['user_id']);
        $this->assertSame('SUPERADMIN', $contact['entity_id']);
        $this->assertSame('Y', $contact['enabled']);
    }

    public function testGetContactCommunicationByContactId()
    {
        $environment    = \Slim\Http\Environment::mock(['REQUEST_METHOD' => 'GET']);
        $request        = \Slim\Http\Request::createFromEnvironment($environment);

        $contactController = new \Contact\controllers\ContactController();
        $response          = $contactController->getCommunicationByContactId($request, new \Slim\Http\Response(), ['contactId' => (string)self::$id]);
        $responseBody      = json_decode((string)$response->getBody());

        $this->assertInternalType('array', $responseBody);
    }

    public function testDelete()
    {
        //  DELETE
        \SrcCore\models\DatabaseModel::delete([
            'table' => 'contacts_v2',
            'where' => ['contact_id = ?'],
            'data'  => [self::$id]
        ]);
        \SrcCore\models\DatabaseModel::delete([
            'table' => 'contact_addresses',
            'where' => ['id = ?'],
            'data'  => [self::$addressId]
        ]);

        //  READ
        $contact = \Contact\models\ContactModel::getById(['id' => self::$id]);
        $this->assertInternalType('array', $contact);
        $this->assertEmpty($contact);

        $contact = \Contact\models\ContactModel::getByAddressId(['addressId' => self::$addressId]);
        $this->assertInternalType('array', $contact);
        $this->assertEmpty($contact);
    }

    public function testControlLengthNameAfnor()
    {
        $name = \Contact\controllers\ContactController::controlLengthNameAfnor(['title' => 'title1', 'fullName' => 'Prénom NOM', 'strMaxLength' => 38]);

        $this->assertSame('Monsieur Prénom NOM', $name);

        $name = \Contact\controllers\ContactController::controlLengthNameAfnor(['title' => 'title3', 'fullName' => 'Prénom NOM TROP LOOOOOOOOOOOOONG', 'strMaxLength' => 38]);

        $this->assertSame('Mlle Prénom NOM TROP LOOOOOOOOOOOOONG', $name);
    }
}
