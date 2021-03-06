<?php

/*
*
*   Copyright 2015 Maarch
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

//Loads the required class
try {
    require_once 'core/class/class_request.php';
    require_once 'core/core_tables.php';
    require_once 'core/class/ObjectControlerAbstract.php';
    require_once 'core/class/ObjectControlerIF.php';
} catch (Exception $e) {
    echo $e->getMessage() . ' // ';
}

/**
 * Class for controling docservers objects from database
 */
abstract class contacts_controler_Abstract extends ObjectControler implements ObjectControlerIF
{

    /**
     * Save given object in database.
     * Return true if succeeded.
     * @param unknown_type $object
     * @return boolean
     */
    function save($object)
    {
        return true;
    }

    /**
     * Return object with given id
     * if found.
     * @param $object_id
     */
    function get($object_id)
    {
        return true;
    }

    /**
     * Delete given object from
     * database.
     * Return true if succeeded.
     * @param unknown_type $object
     * @return boolean
     */
    function delete($object)
    {
        return true;
    }

    public function CreateContact($data)
    {

        //try {
            $func = new functions();
            $data = $func->object2array($data);
            $db = new Database();
            $queryContactFields = '(';
            $queryContactValues = '(';
            $queryAddressFields = '(';
            $queryAddressValues = '(';
            $iContact = 0;
            $iAddress = 0;
            $currentContactId = "0";
            $currentAddressId = "0";

            $enabled = "Y";
            foreach ($data as $key => $value) {
                if (strtoupper($value['table']) == strtoupper('contact_addresses') && strtoupper($value['column']) == strtoupper('enabled')) {
                    $enabled = strtoupper($value['value']);
                    break;
                }
            }

            $countData = count($data);

            for ($i=0;$i<$countData;$i++) {

                if(strtoupper($data[$i]['column']) == strtoupper('email') && ($data[$i]['value'] == "" || $data[$i]['value'] == null)){
                    $returnResArray = array(
                        'returnCode' => (int) 0,
                        'contactId' => '',
                        'addressId' => '',
                        'contactInfo' => 'No email attached to contact, skipped ...',
                        'error' => '',
                    ); 
                    return $returnResArray;
                }

                if (strtoupper($data[$i]['column']) == strtoupper('email') && ($data[$i]['value'] <> "" || $data[$i]['value'] <> null)) {
                    $theString = str_replace(">", "", $data[$i]['value']);
                    $mail = explode("<", $theString);
                    $mail[0] = trim($mail[0]);
                    try {
                        $stmt = $db->query("SELECT contact_id, ca_id FROM view_contacts WHERE email = ? and enabled = ?", 
                            array($mail[0], $enabled)
                        );
                        $res = $stmt->fetchObject();
                        if ($res->ca_id <> "") {
                            $contact_exists = true;
                            
                        } else {
                            $contact_exists = false;
                        }
                    } catch (Exception $e) {
                        $returnResArray = array(
                            'returnCode' => (int) -1,
                            'contactId' => '',
                            'addressId' => '',
                            'contactInfo' => '',
                            'error' => 'unknown error: ' . $e->getMessage(),
                        );  
                        return $returnResArray;
                    }
                    
                }

                $data[$i]['column'] = strtolower($data[$i]['column']);
                $data[$i]['value'] = str_replace("'", "''", $data[$i]['value']);

                if ($data[$i]['table'] == "contacts_v2") {
                    //COLUMN
                    $queryContactFields .= $data[$i]['column'] . ',';
                    //VALUE
                    if ($data[$i]['type'] == 'string' || $data[$i]['type'] == 'date') {
                        $queryContactValues .= "'" . $data[$i]['value'] . "',";
                    } else {
                        $queryContactValues .= $data[$i]['value'] . ",";
                    }
                } else if ($data[$i]['table'] == "contact_addresses") {
                    //COLUMN
                    $queryAddressFields .= $data[$i]['column'] . ',';
                    //VALUE
                    if ($data[$i]['type'] == 'string' || $data[$i]['type'] == 'date') {
                        $queryAddressValues .= "'" . $data[$i]['value'] . "',";
                    } else {
                        $queryAddressValues .= $data[$i]['value'] . ",";
                    }
                }
            }

            $queryContactFields .= "user_id, entity_id, creation_date)";
            $queryContactValues .= "'superadmin', 'SUPERADMIN', current_timestamp)";

            if (!$contact_exists) {
                try {
                    $queryContact = " INSERT INTO contacts_v2 " . $queryContactFields
                       . ' values ' . $queryContactValues ;

                    $db->query($queryContact);

                    $currentContactId = $db->lastInsertId('contact_v2_id_seq');

                } catch (Exception $e) {
                    $returnResArray = array(
                        'returnCode' => (int) -1,
                        'contactId' => 'ERROR',
                        'addressId' => 'ERROR',
                        'contactInfo' => '',
                        'error' => 'contact creation error : '. $e->getMessage(),
                    );
                    
                    return $returnResArray;
                }
                try {
                    $queryAddressFields .= "contact_id, user_id, entity_id)";
                    $queryAddressValues .=  $currentContactId . ", 'superadmin', 'SUPERADMIN')";

                    $queryAddress = " INSERT INTO contact_addresses " . $queryAddressFields
                           . ' values ' . $queryAddressValues ;

                    $db->query($queryAddress);
                    $currentAddressId = $db->lastInsertId('contact_addresses_id_seq');
                } catch (Exception $e) {
                    $returnResArray = array(
                        'returnCode' => (int) -1,
                        'contactId' => $currentContactId,
                        'addressId' => 'ERROR',
                        'contactInfo' => '',
                        'error' => 'address creation error : '. $e->getMessage(),
                    );
                    
                    return $returnResArray;
                }
            }else{
                $returnResArray = array(
                    'returnCode' => (int) 0,
                    'contactId' => $currentContactId,
                    'addressId' => $currentAddressId,
                    'contactInfo' => 'contact already exist, attached to doc ... '.$queryContactValues,
                    'error' => '',
                );
                
                return $returnResArray;
            }

            $returnResArray = array(
                'returnCode' => (int) 0,
                'contactId' => $currentContactId,
                'addressId' => $currentAddressId,
                'contactInfo' => 'contact created and attached to doc ... '.$queryContactValues,
                'error' => '',
            );
            
            return $returnResArray;
    }
}
