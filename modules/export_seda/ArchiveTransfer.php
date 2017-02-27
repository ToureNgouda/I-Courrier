<?php

/*
*   Copyright 2008-2017 Maarch
*
*   This file is part of Maarch Framework.
*
*   Maarch Framework is free software: you can redistribute it and/or modify
*   it under the terms of the GNU General Public License as published by
*   the Free Software Foundation, either version 3 of the License, or
*   (at your option) any later version.
*
*   Maarch Framework is distributed in the hope that it will be useful,
*   but WITHOUT ANY WARRANTY; without even the implied warranty of
*   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*   GNU General Public License for more details.
*
*   You should have received a copy of the GNU General Public License
*   along with Maarch Framework.  If not, see <http://www.gnu.org/licenses/>.
*/

require_once 'core/class/class_request.php';
require_once __DIR__.'/DOMTemplateProcessor.php';

class ArchiveTransfer {

	private $db;

	public function __construct() 
	{
		$this->db = new Database();
	}

	public function receive($listResId, $transferringAgency = "", $archivalAgency = "") {
		
		if (!$listResId) {
			return false;
		}

		$messageObject = new stdClass();
		$messageObject = $this->initMessage($messageObject, $transferringAgency, $archivalAgency);

		foreach ($listResId as $resId) {
			$letterbox = $this->getCourrier($resId);

			if ($letterbox->filename) {
				$messageObject->dataObjectPackage->descriptiveMetadata->archiveUnit[] = $this->getArchiveUnit($letterbox);
				$messageObject->dataObjectPackage->binaryDataObject[] = $this->getBinaryDataObject($letterbox);
			} else {
				$messageObject->dataObjectPackage->descriptiveMetadata->archiveUnit[] = $this->getArchiveUnit($letterbox);
			}
		}

		//TODO
		//$messageObject->dataObjectPackage->managementMetadata
		
		$this->insertMessage($messageObject);
		return $messageObject;
	}

	public function sendXml($messageObject)
	{
		$DOMTemplate = new DOMDocument();
		$DOMTemplate->load(__DIR__.DIRECTORY_SEPARATOR.'resources'.DIRECTORY_SEPARATOR.'ArchiveTransfer.xml');
		$DOMTemplateProcessor = new DOMTemplateProcessor($DOMTemplate);
		$DOMTemplateProcessor->setSource('ArchiveTransfer', $messageObject);
		$DOMTemplateProcessor->merge();
		$DOMTemplateProcessor->removeEmptyNodes();

        file_put_contents(__DIR__.DIRECTORY_SEPARATOR.'seda2'.DIRECTORY_SEPARATOR.$messageObject->messageIdentifier->value.'.xml', $DOMTemplate->saveXML());

		return $xml;
	}

	private function initMessage($messageObject, $transferringAgency =null, $archivalAgency = null)
	{
		$messageObject->date = date('Y-m-d h:i:s');
		$messageObject->messageIdentifier = new stdClass();
		$messageObject->messageIdentifier->value = $_SESSION['user']['UserId'] . "-" . date('Ymd-His');

		$messageObject->transferringAgency = new stdClass();
		$messageObject->transferringAgency->identifier = new stdClass();

		if ($transferringAgency) {
			$messageObject->transferringAgency->identifier->value = $transferringAgency;
		}else {
			foreach ($_SESSION['user']['entities'] as $entitie) {
				if ($entitie['ENTITY_TYPE'] == "Service") {
					$entitie = $this->getEntitie($entitie['ENTITY_ID']);
					if ($entitie) {
						$messageObject->transferringAgency->identifier->value = $entitie->business_id;
					} else {
						// TODO return error;
					}
				}
			}
		}

		$messageObject->archivalAgreement = new stdClass();
		$messageObject->archivalAgreement->value = "A COMPLETER";

		$messageObject->archivalAgency = new stdClass();
		$messageObject->archivalAgency->identifier = new stdClass();

		if ($archivalAgency) {
			$messageObject->archivalAgency->identifier->value = $archivalAgency;
		} else {
			$messageObject->archivalAgency->identifier->value = "A COMPLETER";
		}
		
		$messageObject->dataObjectPackage = new stdClass();
		$messageObject->dataObjectPackage->binaryDataObject = [];
		$messageObject->dataObjectPackage->descriptiveMetadata = new stdClass();
		$messageObject->dataObjectPackage->managementMetadata = new stdClass();
		$messageObject->dataObjectPackage->descriptiveMetadata->archiveUnit = [];

		return $messageObject;
	}

	private function getArchiveUnit($letterbox)
	{
		$messageArchiveUnit = new stdClass();

		$messageArchiveUnit->content = new stdClass();
		$messageArchiveUnit->content->receivedDate = $letterbox->admission_date;
		$messageArchiveUnit->content->sentDate = $letterbox->doc_date;
		$messageArchiveUnit->content->receivedDate = $letterbox->admission_date;
		$messageArchiveUnit->content->receivedDate = $letterbox->admission_date;

		$messageArchiveUnit->content->addressee = [];
		$messageArchiveUnit->content->keyword = [];

		if ($letterbox->exp_contact_id) {
			
			$contact = $this->getContact($letterbox->exp_contact_id);
			$entitie = $this->getEntitie($letterbox->destination);

			$messageArchiveUnit->content->keyword[] = $this->getKeyword($contact);
			$messageArchiveUnit->content->addressee[] = $this->getAddresse($entitie,"entitie");
		} else if ($letterbox->dest_contact_id) {
			$contact = $this->getContact($letterbox->dest_contact_id);
			$entitie = $this->getEntitie($letterbox->destination);

			$messageArchiveUnit->content->addressee[] = $this->getAddresse($contact);
			$messageArchiveUnit->content->keyword[] = $this->getKeyword($entitie,"entitie");
		} else if ($letterbox->exp_user_id) {
			$user = $this->getUserInformation($letterbox->exp_user_id);
			$entitie = $this->getEntitie($letterbox->initiator);
			//$entitie = $this->getEntitie($letterbox->destination);

			$messageArchiveUnit->content->keyword[] = $this->getKeyword($user);
			$messageArchiveUnit->content->addressee[] = $this->getAddresse($entitie,"entitie");
		}
		
		$messageArchiveUnit->content->source = $_SESSION['mail_nature'][$letterbox->nature_id];

		$messageArchiveUnit->content->documentType = $letterbox->type_label;
		$messageArchiveUnit->content->originatingAgencyArchiveIdentifier = $letterbox->alt_identifier;
		$messageArchiveUnit->content->originatingSystemId = $letterbox->res_id;
		$messageArchiveUnit->content->title = [];
		$messageArchiveUnit->content->title[] = $letterbox->subject;
		$messageArchiveUnit->content->description = [];
		$messageArchiveUnit->content->description[] = " ";
		$messageArchiveUnit->content->endDate = $letterbox->process_limit_date;

		$notes = $this->getNotes($letterbox->res_id);
		$messageArchiveUnit->content->custodialHistory = new stdClass();
		$messageArchiveUnit->content->custodialHistory->custodialHistoryItem = [];

		foreach ($notes as $note) {
			$messageArchiveUnit->content->custodialHistory->custodialHistoryItem[] = $this->getCustodialHistoryItem($note);
		}

		if ($dataObjectReferenceId) {
			$messageArchiveUnit->dataObjectReference = new stdClass();
			$messageArchiveUnit->dataObjectReference->dataObjectReferenceId = $letterbox->res_id;
		}

		return $messageArchiveUnit;
	}

	private function getBinaryDataObject($letterbox)
	{
		$binaryDataObject = new stdClass();
		$binaryDataObject->id = $letterbox->res_id;
		$binaryDataObject->messageDigest = new stdClass();
		$binaryDataObject->messageDigest->value = $letterbox->fingerprint;

		$binaryDataObject->size = new stdClass();
		$binaryDataObject->size->value = $letterbox->filesize;

		$uri = str_replace("##", DIRECTORY_SEPARATOR, $letterbox->path);
		$uri =  str_replace("#", DIRECTORY_SEPARATOR, $uri);
		$uri .= $letterbox->filename;
		$binaryDataObject->uri = $uri;

		return $binaryDataObject;
	}

	private function getKeyword($informations, $type = null)
	{
		$keyword = new stdClass();
		$keyword->keywordContent = new stdClass();

		if ($type == "entitie") {
			$keyword->keywordType = "corpname";
			$keyword->keywordContent = $informations->business_id;
		} else if ($informations->is_corporate_person == "Y") {
			$keyword->keywordType = "corpname";
			$keyword->keywordContent->value = $informations->society;
		} else {
			$keyword->keywordType = "personname";
			$keyword->keywordContent->value = $informations->lastname . " " . $informations->firstname;
		}

		return $keyword;
	}

	private function getAddresse($informations, $type = null)
	{
		$addressee = new stdClass();
		if ($type == "entitie") {
			$addressee->corpname = $informations->entity_label;
			$addressee->identifier = $informations->business_id;
		} else if ($informations->is_corporate_person == "Y") {
			$addressee->corpname = $informations->society;
			$addressee->identifier = $informations->contact_id;
		} else {
			$addressee->firstName = $informations->firstname;
			$addressee->birthName = $informations->lastname;
		}
			

		return $addressee;
	}

	private function getCustodialHistoryItem($note) 
	{
		$custodialHistoryItem = new stdClass();

		$custodialHistoryItem->value = $note->note_text;
		$custodialHistoryItem->when = $note->date_note;

		return $custodialHistoryItem;
	}

	private function getCourrier($resId) 
	{
		$queryParams = [];

		$queryParams[] = $resId;

		$query = "SELECT * FROM res_view_letterbox WHERE res_id = ?";

		$smtp = $this->db->query($query,$queryParams);
		
		$letterbox = $smtp->fetchObject();

		return $letterbox;
	}

	private function getUserInformation($userId) 
	{
		$queryParams = [];

		$queryParams[] = $userId;

		$query = "SELECT * FROM users WHERE user_id = ?";

		$smtp = $this->db->query($query,$queryParams);
		
		$user = $smtp->fetchObject();

		return $user;
	}

	private function getNotes($letterboxId) 
	{
		$queryParams = [];

		$queryParams[] = $letterboxId;

		$query = "SELECT * FROM notes WHERE identifier = ?";

		$smtp = $this->db->query($query,$queryParams);

		$notes = [];
		while ($res = $smtp->fetchObject()) {
			$notes[] = $res;
		}

		return $notes;
	}

	private function getEntitie($entityId)
	{
		$queryParams = [];

		$queryParams[] = $entityId;

		$query = "SELECT * FROM entities WHERE entity_id = ?";

		$smtp = $this->db->query($query,$queryParams);
		
		$entitie = $smtp->fetchObject();

		return $entitie;
	}

	private function getContact($contactId)
	{
		$queryParams = [];

		$queryParams[] = $contactId;

		$query = "SELECT * FROM contacts_v2 WHERE contact_id = ?";

		$smtp = $this->db->query($query,$queryParams);
		
		$contact = $smtp->fetchObject();

		return $contact;
	}

	private function insertMessage($messageObject) 
	{
		$queryParams = [];
		$messageId = uniqid();

		try {
			$query = ("INSERT INTO seda (
				message_id,
				schema,
				type,
				status,
				date,
				reference,
	            account_id ,
				sender_org_identifier_id,
				sender_org_name,
				recipient_org_identifier_id,
				recipient_org_name,
				archival_agreement_reference,
				reply_code,
				size,
				data,
				active,
				archived)
				VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)");

			$queryParams[] = $messageId; // Message Id
			$queryParams[] = "2.1"; //Schema
			$queryParams[] = "ArchiveTransfer"; // Type
			$queryParams[] = "sent"; // Status
			$queryParams[] = $messageObject->date; // Date
			$queryParams[] = $messageObject->messageIdentifier->value; // Reference
			$queryParams[] = $_SESSION['user']['UserId']; // Account Id
			$queryParams[] = $messageObject->transferringAgency->identifier->value; // Sender org identifier id
			$queryParams[] = ""; //SenderOrgNAme
			$queryParams[] = $messageObject->archivalAgency->identifier->value; // Recipient org identifier id
			$queryParams[] = ""; //RecipientOrgNAme
			$queryParams[] = $messageObject->archivalAgreement->value; // Archival agreement reference
			$queryParams[] = ""; //ReplyCode
			$queryParams[] = 0; // size
			$queryParams[] = ""; // Data
			$queryParams[] = 1; // active
			$queryParams[] = 0; // archived

			$res = $this->db->query($query,$queryParams);

			//var_dump($messageObject);
			foreach ($messageObject->dataObjectPackage->binaryDataObject as $binaryDataObject) {
				$this->insertUnitIdentifier($messageId, "res_letterbox", $binaryDataObject->id);
			}
		} catch (Exception $e) {
			// return error
		}
	}

	private function insertUnitIdentifier($messageId, $tableName, $resId) {
		$query = ("INSERT INTO unit_identifier VALUES (?,?,?)");
		$queryParams = [];

		$queryParams[] = $messageId;
		$queryParams[] = $tableName;
		$queryParams[] = $resId;

		$res = $this->db->query($query,$queryParams);

		return $res;
	}
}