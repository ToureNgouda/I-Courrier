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

/**
* User Class
*
*  Contains all the functions to manage users
*
* @package  Maarch
* @version 2.1
* @since 10/2005
* @license GPL
*
*/

require_once 'core/core_tables.php';

abstract class class_users_Abstract extends Database
{
    /**
    * Redefinition of the user object constructor : configure the SQL argument
    *  order by
    */
    public function __construct()
    {
        parent::__construct();
    }

    /**
    * Return a array of user informations
    *
    */
    public function get_user($user_id) {
        require_once "modules" . DIRECTORY_SEPARATOR . "visa" . DIRECTORY_SEPARATOR. "class" . DIRECTORY_SEPARATOR. "class_user_signatures.php";
        $us = new UserSignatures();
        if (!empty($user_id)) {
            $db = new Database();
            $stmt = $db->query(
                "SELECT user_id, firstname, lastname, mail, phone, status, thumbprint/*, signature_path, signature_file_name*/ FROM " 
                . USERS_TABLE . " WHERE user_id = ?",
                array($user_id)
            );
            if ($stmt->rowCount() >0) {
                $line = $stmt->fetchObject();
                /* MODIFICATION POUR LES SIGNATURES */

                $query = "SELECT path_template FROM " 
                    . _DOCSERVERS_TABLE_NAME 
                    . " WHERE docserver_id = 'TEMPLATES'";
                $stmt = $db->query($query);
                $resDs = $stmt->fetchObject();
                $pathToDs = $resDs->path_template;

                $tab_sign = $us->getForUser($line->user_id);
                $pathToSignature = array();
                foreach ($tab_sign as $sign) {
                    $path = $pathToDs . str_replace(
                        "#", 
                        DIRECTORY_SEPARATOR, 
                        $sign['signature_path']
                    )
                    . $sign['signature_file_name'];
                    array_push($pathToSignature, $path);
                }

                $user = array(
                    'id' => $line->user_id,
                    'firstname' => $this->show_string($line->firstname),
                    'lastname' => $this->show_string($line->lastname),
                    'mail' => $line->mail,
                    'phone' => $line->phone,
                    'status' => $line->status,
                    'thumbprint' => $line->thumbprint,
                    'signature_path' => $line->signature_path,
                    'signature_file_name' => $line->signature_file_name,
                    'pathToSignature' => $pathToSignature,
                );
                return $user;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    /**
    * Return where clause security for user(include baskets)
    *
    */
    public function get_global_security() {
        if (!empty($_SESSION['user']['UserId'])) {
            require_once 'core'.DIRECTORY_SEPARATOR.'class'.DIRECTORY_SEPARATOR.'class_security.php';
            $sec = new security();
            $coll_id = 'letterbox_coll';

            //group clause
            $group_clause = $sec->get_where_clause_from_coll_id($coll_id);

            //baskets clause
            $basketQuery = '';
            for (
                $ind_bask = 0;
                $ind_bask < count($_SESSION['user']['baskets']);
                $ind_bask++
            ) {
                if (
                    $_SESSION['user']['baskets'][$ind_bask]['coll_id'] == $coll_id
                ) {
                    if(
                        isset($_SESSION['user']['baskets'][$ind_bask]['clause']) 
                        && trim($_SESSION['user']['baskets'][$ind_bask]['clause']
                        ) <> '' 
                        && $_SESSION['user']['baskets'][$ind_bask]['is_folder_basket'] == 'N'
                    ) {
                        $basketQuery .= ' or (' 
                            . $_SESSION['user']['baskets'][$ind_bask]['clause'] 
                            . ')';
                    }
                 }
            }
            if ($basketQuery <> '') {
                $basketQuery = preg_replace('/^ or/', '', $basketQuery);
            }

            $global_clause = $group_clause . ' OR ' . $basketQuery;

            return $global_clause;

        } else {
            return false;
        }
    }
}

