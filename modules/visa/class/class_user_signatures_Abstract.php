<?php
/*
*    Copyright 2008-2016 Maarch
*
*  This file is part of Maarch Framework.
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
*    along with Maarch Framework.  If not, see <http://www.gnu.org/licenses/>.
*/

require_once 'core/core_tables.php';

abstract class UserSignaturesAbstract extends Database
{

    public function storeSignOnDocserver($tmpSourceCopy,$coll_id){
        require_once('core' . DIRECTORY_SEPARATOR . 'class'
            . DIRECTORY_SEPARATOR . 'docservers_controler.php');
        $ds_ctrl = new docservers_controler();
        $db = new Database();
        $query_ds = "select priority_number, docserver_id, path_template  from docservers where is_readonly = 'N' and "
        . " enabled = 'Y' and coll_id = ? and docserver_type_id = 'TEMPLATES' order by priority_number";
        $stmt = $db->query($query_ds,[$coll_id]);

        if($res = $stmt->fetchObject()){
            $docserverIdSign = $res->docserver_id;
            $docserver_pathSign = $res->path_template;
        }else{
            $docserverIdSign='';
        }

        if($docserverIdSign == ''){
            echo "{status:0,error:'Docserver not found'}";
            exit();
        }

        $pathOnDocserverSign = Ds_createPathOnDocServer(
            $docserver_pathSign
        );
        
        $copyResultArray = array();
        $docinfo = $ds_ctrl->getNextFileNameInDocserver(
                $pathOnDocserverSign['destinationDir']
        );
        if ($docinfo['error'] <> '') {
            $_SESSION['error'] = _FILE_SEND_ERROR . '. '._TRY_AGAIN . '. '
                                . _MORE_INFOS . ' : <a href=\'mailto:'
                                . $_SESSION['config']['adminmail'] . '\'>'
                                . $_SESSION['config']['adminname'] . '</a>';
            echo "{status:0,error:'".$_SESSION['error']."'}";
            exit();
        }


        require_once('core' . DIRECTORY_SEPARATOR . 'class'
            . DIRECTORY_SEPARATOR . 'docserver_types_controler.php');
        $docserverTypeControlerSign = new docserver_types_controler();
        $docserverTypeObjectSign = $docserverTypeControlerSign->get('TEMPLATES');
        
        $docinfo['fileDestinationName'] .= '.'
                . strtolower(pathinfo($tmpSourceCopy,PATHINFO_EXTENSION));
        /*echo "<pre>".print_r($tmpSourceCopy,true)."</pre>";
        echo "<pre>".print_r($docinfo,true)."</pre>";
        echo "<pre>".print_r($docserverTypeObject,true)."</pre>";*/
        $copyResultArray = Ds_copyOnDocserver(
            $tmpSourceCopy,
            $docinfo,
            $docserverTypeObjectSign->fingerprint_mode
        );
        if (isset($copyResultArray['error']) && $copyResultArray['error'] <> '') {
            $storeInfos = array('error' => $copyResultArray['error']);
            return $storeInfos;
        }

        $destinationDir = $copyResultArray['destinationDir'];
        $fileDestinationName = $copyResultArray['fileDestinationName'];
        $destinationDir = substr(
            $destinationDir,
            strlen($docserver_pathSign)
        ) . DIRECTORY_SEPARATOR;
        $destinationDir = str_replace(
            DIRECTORY_SEPARATOR,
            '#',
            $destinationDir
        );
        $storeInfos = array(
            'path_template' => $docserver_pathSign,
            'destination_dir' => $destinationDir,
            'docserver_id' => $docserverId,
            'file_destination_name' => $fileDestinationName,
        );
        return $storeInfos;
    }

    public function createForCurrentUser($tmpSourceCopy) {
        $db = new Database();

        require_once('core' . DIRECTORY_SEPARATOR . 'class'
            . DIRECTORY_SEPARATOR . 'docserver_types_controler.php');
        $docserverTypeControlerSign = new docserver_types_controler();
        $docserverTypeObjectSign = $docserverTypeControlerSign->get('TEMPLATES');


        $storeInfos = $this->storeSignOnDocserver($tmpSourceCopy,'templates');

        $userInfos = \User\models\UserModel::get(['select' => ['id'], 'where' => ['user_id = ?'], 'data' => [$_SESSION['user']['UserId']]]); 
 
        $db->query('INSERT INTO ' . USER_SIGNATURES_TABLE . ' (user_serial_id, signature_path, signature_file_name,fingerprint) VALUES (?, ?, ?, ?)', 
            [$userInfos[0]['id'], 
             $storeInfos['destination_dir'],
             $storeInfos['file_destination_name'],
             Ds_doFingerprint($tmpSourceCopy,$docserverTypeObjectSign->fingerprint_mode)
            ]
        );
        echo "{status:1}";
    }

    public function getForUser($user_id) {
//        $db = new Database();
//
//        $stmt = $db->query('SELECT * FROM ' .USER_SIGNATURES_TABLE. ' WHERE user_id = ? ',
//            [$user_id]
//        );
//        $userSignatures = [];
//        while($res = $stmt->fetchObject())
//            $userSignatures[] = ['id' => $res->id, 'signature_label' => $res->signature_label, 'signature_path' => $res->signature_path, 'signature_file_name' => $res->signature_file_name];
//
        return [];
    }

    public function deleteForCurrentUser($id) {
        $db = new Database();

        $db->query('DELETE FROM ' . USER_SIGNATURES_TABLE . ' WHERE user_id = ? AND id = ?',
            [$_SESSION['user']['UserId'], $id]
        );
    }
}