<?php

//FOR ADD NEW ATTACHMENTS



require_once 'modules/attachments/attachments_tables.php';

//new attachment from a template
if (isset($_SESSION['cm']['resMaster']) && $_SESSION['cm']['resMaster'] <> '') {
   $objectId = $_SESSION['cm']['resMaster'];
}

$_SESSION['cm']['resMaster'] = '';

$collId =  $_SESSION['current_basket']['coll_id'];

$docserverControler = new docservers_controler();
$docserver = $docserverControler->getDocserverToInsert(
   $collId
);

if (empty($docserver)) {
    $_SESSION['error'] = _DOCSERVER_ERROR . ' : '
        . _IMG_SIGN_MISSING . '. ' . _MORE_INFOS;
} else {
    // some checking on docserver size limit
    if(!is_file($_SESSION['config']['tmppath'] . $tmpFileName)){
        echo "{\"status\":1, \"error\" : \"". _TMP_SIGNED_FILE_FAILED . ': ' ._FILE . ' ' . _ENCRYPTED .' ' . _OR .' ' . _MISSING . ' ' . _OR .' ' . strtolower(NO_PLACE_SIGNATURE) ."\"}";
        exit;
    }
    
    $newSize = $docserverControler->checkSize(
        $docserver, filesize($_SESSION['config']['tmppath'] . $tmpFileName)
    );
    if ($newSize == 0) {
        $_SESSION['error'] = _DOCSERVER_ERROR . ' : '
            . _NOT_ENOUGH_DISK_SPACE . '. ' . _MORE_INFOS . '.';
    } else {
        $fileInfos = array(
            'tmpDir'      => $_SESSION['config']['tmppath'],
            'size'        => filesize($_SESSION['config']['tmppath'] . $tmpFileName),
            'format'      => strtoupper($fileExtension),
            'tmpFileName' => $tmpFileName,
        );
        $storeResult = array();
        $storeResult = $docserverControler->storeResourceOnDocserver(
            $collId, $fileInfos
        );
        if (isset($storeResult['error']) && $storeResult['error'] <> '') {
            $_SESSION['error'] = $storeResult['error'];
        } else {
			require_once "core/class/class_request.php";
			$db = new Database();
			$signatoryUser = \User\models\UserModel::getByUserId(['userId' => $_SESSION['user']['UserId'], 'select' => ['id']]);
			if ($_SESSION['visa']['repSignRel'] > 1) {
                //$target_table = 'res_version_attachments';
                $db->query("UPDATE res_version_attachments set status = 'SIGN', signatory_user_serial_id = ? WHERE res_id = ?",[$signatoryUser['id'], $_SESSION['visa']['repSignId']]);
            } else {
                //$target_table = 'res_attachments';
				$db->query("UPDATE res_attachments set status = 'SIGN', signatory_user_serial_id = ? WHERE res_id = ?",[$signatoryUser['id'], $_SESSION['visa']['repSignId']]);
            }
			unset($_SESSION['visa']['repSignRel']);
			if (isset($_SESSION['visa']['repSignId'])) unset($_SESSION['visa']['repSignId']);
			
            $resAttach = new resource();
            $_SESSION['data'] = array();
            array_push(
                $_SESSION['data'],
                array(
                    'column' => 'typist',
                    'value' => $_SESSION['user']['UserId'],
                    'type' => 'string',
                )
            );
            array_push(
                $_SESSION['data'],
                array(
                    'column' => 'format',
                    'value' => $fileExtension,
                    'type' => 'string',
                )
            );
            array_push(
                $_SESSION['data'],
                array(
                    'column' => 'docserver_id',
                    'value' => $storeResult['docserver_id'],
                    'type' => 'string',
                )
            );
			
            if (!isset($statusSign) || empty($statusSign)) $statusSign = 'TRA';
            array_push(
                $_SESSION['data'],
                array(
                    'column' => 'status',
                    'value' => $statusSign,
                    'type' => 'string',
                )
            );
            array_push(
                $_SESSION['data'],
                array(
                    'column' => 'offset_doc',
                    'value' => ' ',
                    'type' => 'string',
                )
            );
            array_push(
                $_SESSION['data'],
                array(
                    'column' => 'logical_adr',
                    'value' => ' ',
                    'type' => 'string',
                )
            );
            array_push(
                $_SESSION['data'],
                array(
                    'column' => 'title',
                    'value' => $_SESSION['visa']['last_resId_signed']['title'],
                    'type' => 'string',
                )
            );
			array_push(
                $_SESSION['data'],
                array(
                    'column' => 'relation',
                    'value' => 1,
                    'type' => 'integer',
                )
            );
            $_SESSION['cm']['templateStyle'] = '';
            array_push(
                $_SESSION['data'],
                array(
                    'column' => 'coll_id',
                    'value' => $collId,
                    'type' => 'string',
                )
            );
            array_push(
                $_SESSION['data'],
                array(
                    'column' => 'res_id_master',
                    'value' => $_SESSION['visa']['last_resId_signed']['res_id'],
                    'type' => 'integer',
                )
            );
			
            array_push(
                $_SESSION['data'],
                array(
                    'column' => 'type_id',
                    'value' => $_SESSION['visa']['last_resId_signed']['type_id'],
                    'type' => 'int',
                )
            );
            array_push(
                $_SESSION['data'],
                array(
                    'column' => 'identifier',
                    'value' => $_SESSION['visa']['last_resId_signed']['identifier'],
                    'type' => 'string',
                )
            );
			array_push(
                $_SESSION['data'],
                array(
                    'column' => 'attachment_type',
                    'value' => 'signed_response',
                    'type' => 'string',
                )
            );

            array_push(
                $_SESSION['data'],
                array(
                    'column' => 'dest_contact_id',
                    'value' => $_SESSION['visa']['last_resId_signed']['dest_contact'],
                    'type' => 'int',
                )
            );
            array_push(
                $_SESSION['data'],
                array(
                    'column' => 'dest_address_id',
                    'value' => $_SESSION['visa']['last_resId_signed']['dest_address'],
                    'type' => 'int',
                )
            );
            array_push(
                $_SESSION['data'],
                array(
                    'column' => 'in_signature_book',
                    'value' => 1,
                    'type' => 'bool',
                )
            );
            $_SESSION['data'][] = [
                'column' => 'dest_user',
                'value' => $_SESSION['visa']['last_resId_signed']['dest_user'],
                'type' => 'string'
            ];

            if (empty($_REQUEST['id'])) $id_origin = $objectId;
            else $id_origin = $_REQUEST['id'];
            if (empty($target_table)) $target_table = "res_attachments";
            array_push(
                $_SESSION['data'],
                array(
                    'column' => 'origin',
                    'value' => $id_origin.','.$target_table,
                    'type' => 'string',
                )
            );
			
			unset($_SESSION['visa']['last_resId_signed']);
			
            $id = $resAttach->load_into_db(
                RES_ATTACHMENTS_TABLE,
                $storeResult['destination_dir'],
                $storeResult['file_destination_name'] ,
                $storeResult['path_template'],
                $storeResult['docserver_id'], 
                $_SESSION['data'],
                $_SESSION['config']['databasetype']
            );
			
			$_SESSION['visa']['last_ans_signed'] = $id;
            if ($id == false) {
                $_SESSION['error'] = $resAttach->get_error();
                //echo $resource->get_error();
                //$resource->show();
                //exit();
            } else {
                if ($_SESSION['history']['attachadd'] == "true") {
                    $hist = new history();
                    $sec = new security();
                    $view = $sec->retrieve_view_from_coll_id(
                        $collId
                    );
                    $hist->add(
                        $view, $objectId, 'ADD', 'attachadd',
                        ucfirst(_DOC_NUM) . $id . ' '
                        . _NEW_ATTACH_ADDED . ' ' . _TO_MASTER_DOCUMENT
                        . $objectId,
                        $_SESSION['config']['databasetype'],
                        'apps'
                    );
                    $hist->add(
                        RES_ATTACHMENTS_TABLE, $id, 'ADD','attachadd',
                        $_SESSION['error'] 
                        . _NEW_ATTACHMENT,
                        $_SESSION['config']['databasetype'],
                        'attachments'
                    );
                }
            }
        }
    }
}
