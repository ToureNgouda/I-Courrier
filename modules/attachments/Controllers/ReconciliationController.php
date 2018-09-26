<?php


class ReconciliationController{
    public function storeAttachmentResource($aArgs)
    {
        if (empty($aArgs['data'])) {

            return ['errors' => 'data ' . _EMPTY];
        }

        if (empty($aArgs['collId'])) {

            return ['errors' => 'collId ' . _EMPTY];
        }

        if (empty($aArgs['collIdMaster'])) {

            return ['errors' => 'collIdMaster ' . _EMPTY];
        }

        if (empty($aArgs['table'])) {

            return ['errors' => 'table ' . _EMPTY];
        }

        if (empty($aArgs['fileFormat'])) {

            return ['errors' => 'fileFormat ' . _EMPTY];
        }

        $resId = $aArgs['resId'];
        $resIdMaster = $aArgs['resIdMaster'];
        $collId = $aArgs['collId'];
        $collIdMaster = $aArgs['collIdMaster'];
        $table = $aArgs['table'];
        $fileFormat = $aArgs['fileFormat'];
        $filename = $aArgs['filename'];
        $path = $aArgs['path'];
        $docserverPath = $aArgs['docserverPath'];
        $docserverId = $aArgs['docserverId'];

        $aArgs = [
            'data'   => $aArgs['data'],
            'collIdMaster' => $collIdMaster,
            'resId'  => $resId,
            'resIdMaster'  => $resIdMaster,
        ];
        $returnPrepare = $this -> prepareStorage($aArgs);

        $aArgs = [
            'data'          => $returnPrepare['data'],
            'collId'        => $collId,
            'table'         => $table,
            'fileFormat'    => $fileFormat,
            'status'        => $returnPrepare['status'],
            'path'          => $path,
            'filename'      => $filename,
            'docserverPath' => $docserverPath,
            'docserverId'   => $docserverId
        ];

        $response = $this -> loadIntoDb($aArgs);
        //return $response;
        if (!$response) {
            return ['errors' => 'Pb with SQL insertion : ' . $response[0]];
        } else {
            require_once 'core/class/class_history.php';
            require_once 'core/class/class_security.php';
            $hist = new \history();
            $sec = new \security();
            $view = $sec->retrieve_view_from_coll_id($collIdMaster);

            $hist->add(
                $view, $resId, "ADD", 'attachadd',
                ucfirst(_DOC_NUM) . $response[0] . ' '
                . _NEW_ATTACH_ADDED . ' ' . _TO_MASTER_DOCUMENT
                . $resId,
                $_SESSION['config']['databasetype'],
                'apps'
            );
            $hist->add(
                $table, $response[0], "ADD",'attachadd',
                _NEW_ATTACH_ADDED,
                $_SESSION['config']['databasetype'],
                'attachments'
            );
        }

        return $response;
    }

    public function prepareStorage($aArgs)
    {
        if (empty($aArgs['data'])) {

            return ['errors' => 'data ' . _EMPTY];
        }

        if (empty($aArgs['collIdMaster'])) {

            return ['errors' => 'collIdMaster ' . _EMPTY];
        }

        $statusFound = false;
        $typistFound = false;
        $typeIdFound = false;
        $attachmentTypeFound = false;

        $data = $aArgs['data'];

        $countD = count($data);
        for ($i=0;$i<$countD;$i++) {

            if (
                strtoupper($data[$i]['type']) == 'INTEGER' ||
                strtoupper($data[$i]['type']) == 'FLOAT'
            ) {
                if ($data[$i]['value'] == '') {
                    $data[$i]['value'] = '0';
                }
            }

            if (strtoupper($data[$i]['type']) == 'STRING') {
                $data[$i]['value'] = $data[$i]['value'];
                $data[$i]['value'] = str_replace(";", "", $data[$i]['value']);
                $data[$i]['value'] = str_replace("--", "", $data[$i]['value']);
            }
            if (strtoupper($data[$i]['column']) == strtoupper('status')) {
                $statusFound = true;
                $status = $data[$i]['value'];
            }

            if (strtoupper($data[$i]['column']) == strtoupper('typist')) {
                $typistFound = true;
            }

            if (strtoupper($data[$i]['column']) == strtoupper('type_id')) {
                $typeIdFound = true;
            }

            if (strtoupper($data[$i]['column']) == strtoupper('attachment_type')) {
                $attachmentTypeFound = true;
            }
        }

        if (!$typistFound) {
            array_push(
                $data,
                array(
                    'column' => 'typist',
                    'value' => $_SESSION['user']['UserId'],
                    'type' => 'string',
                )
            );
        }

        if (!$typeIdFound) {
            array_push(
                $data,
                array(
                    'column' => 'type_id',
                    'value' => 0,
                    'type' => 'int',
                )
            );
        }

        if (!$statusFound) {
            array_push(
                $data,
                array(
                    'column' => 'status',
                    'value' => 'NEW',
                    'type' => 'string',
                )
            );
            $status = 'NEW';
        }

        if (!$attachmentTypeFound) {
            array_push(
                $data,
                array(
                    'column' => 'attachment_type',
                    'value' => 'response_project',
                    'type' => 'string',
                )
            );
        }

        //BASICS
        array_push(
            $data,
            array(
                'column' => "coll_id",
                'value' => $aArgs['collIdMaster'],
                'type' => "string",
            )
        );

        array_push(
            $data,
            array(
                'column' => "res_id_master",
                'value' => $aArgs['resIdMaster'],
                'type' => "integer",
            )
        );

        array_push(
            $data,
            array(
                'column' => "relation",
                'value' => 1,
                'type' => "integer",
            )
        );

        $return = [
            'data'   => $data,
            'status' => $status,
        ];

        return $return;
    }

    public function loadIntoDb($aArgs)
    {
        $db = new \Database();
        if (empty($aArgs['table'])) {
            return ['errors' => 'table ' . _EMPTY];
        }

        if (empty($aArgs['path'])) {
            return ['errors' => 'path ' . _EMPTY];
        }

        if (empty($aArgs['filename'])) {
            return ['errors' => 'filename ' . _EMPTY];
        }

        if (empty($aArgs['docserverPath'])) {
            return ['errors' => 'docserverPath ' . _EMPTY];
        }

        if (empty($aArgs['docserverId'])) {
            return ['errors' => 'docserverId ' . _EMPTY];
        }

        if (empty($aArgs['data'])) {
            return ['errors' => 'data ' . _EMPTY];
        }
        $table = $aArgs['table'];
        $path = $aArgs['path'];
        $filename = $aArgs['filename'];
        $docserverPath = $aArgs['docserverPath'];
        $docserverId = $aArgs['docserverId'];
        $data = $aArgs['data'];

        $filetmp = $docserverPath;
        $tmp = $path;
        $tmp = str_replace('#', DIRECTORY_SEPARATOR, $tmp);
        $filetmp .= $tmp;
        $filetmp .= $filename;

        $docserver = \Docserver\models\DocserverModel::getById([
            'id' => $docserverId
        ]);
        $docserverType = \Docserver\models\DocserverTypeModel::getById([
            'id' => $docserver['docserver_type_id']
        ]);

        if (empty($docserverType['fingerprint_mode']) || $docserverType['fingerprint_mode'] == 'NONE') {
            $fingerprint =  '0';
        } else {
            $fingerprint = hash_file(strtolower($docserverType['fingerprint_mode']), $filetmp);
        }

        $filesize = filesize($filetmp);
        array_push(
            $data,
            array(
                'column' => "fingerprint",
                'value' => $fingerprint,
                'type' => "string"
            )
        );
        array_push(
            $data,
            array(
                'column' => "filesize",
                'value' => $filesize,
                'type' => "int"
            )
        );
        array_push(
            $data,
            array(
                'column' => "path",
                'value' => $path,
                'type' => "string"
            )
        );
        array_push(
            $data,
            array(
                'column' => "filename",
                'value' => $filename,
                'type' => "string"
            )
        );
        array_push(
            $data,
            array(
                'column' => 'creation_date',
                'value' => $db->current_datetime(),
                'type' => "function"
            )
        );

        $testBasicFields = $this->checkBasicFields($data);

        if (!$testBasicFields['status']) {
            return ['error' => $testBasicFields['error']];
        } else {
            $prepareData = [];
            $countD = count($data);
            for ($i = 0; $i < $countD; $i++) {
                //COLUMN
                $data[$i]['column'] = strtolower($data[$i]['column']);
                //VALUE
                $prepareData[$data[$i]['column']] = $data[$i]['value'];
            }

            unset($prepareData['res_id']); // NCH01
            \Attachment\models\AttachmentModel::create($prepareData);

            return true;
        }
    }

    private function checkBasicFields($data)
    {
        $error = '';
        $db = new \Database();
        $find_format = false;
        $find_typist = false;
        $find_creation_date = false;
        $find_docserver_id = false;
        $find_path = false;
        $find_filename = false;
        $find_offset = false;
        $find_logical_adr = false;
        $find_fingerprint = false;
        $find_filesize = false;
        $find_status = false;
        for ($i=0; $i < count($data); $i++) {
            //var_dump($data[$i]);
            if ($data[$i]['column'] == 'format') {
                $find_format = true;
                // must be tested in the file_index.php file (module = indexing_searching)
            } elseif ($data[$i]['column'] == 'typist') {
                $find_typist = true;
            } elseif ($data[$i]['column'] == 'creation_date') {
                $find_creation_date = true;
                if ($data[$i]['value'] <> $db->current_datetime()) {
                    $error .= _CREATION_DATE_ERROR;
                }
            } elseif ($data[$i]['column'] == 'docserver_id') {
                $find_docserver_id =  true;
            } elseif ($data[$i]['column'] == 'path') {
                $find_path = true;
                if (empty($data[$i]['value'])) {
                    $error .= _PATH_ERROR;
                }
            } elseif ($data[$i]['column'] == 'filename') {
                $find_filename = true;
                if (!preg_match(
                    "/^[\w-.]+.([a-zA-Z-0-9][a-zA-Z-0-9][a-zA-Z-0-9][a-zA-Z-0-9]?|maarch)$/",
                    $data[$i]['value']
                )
                ) {
                    $error .= _FILENAME_ERROR . ' ' . $data[$i]['value'] . '<br/>';
                }
            } elseif ($data[$i]['column'] == "offset_doc") {
                $find_offset = true;
            } elseif ($data[$i]['column'] == 'logical_adr') {
                $find_logical_adr = true;
            } elseif ($data[$i]['column'] == 'fingerprint') {
                $find_fingerprint  = true;
                if (!preg_match("/^[0-9A-Fa-f]+$/", $data[$i]['value'])) {
                    $error .= _FINGERPRINT_ERROR;
                }
            } elseif ($data[$i]['column'] == 'filesize') {
                $find_filesize = true;
                if ($data[$i]['value'] <= 0) {
                    $error .= _FILESIZE_ERROR;
                }
            } elseif ($data[$i]['column'] == 'status') {
                $find_status = true;
            }
        }

        if ($find_format == false) {
            $error .= _MISSING_FORMAT;
        }
        if ($find_typist == false) {
            $error .= _MISSING_TYPIST;
        }
        if ($find_creation_date == false) {
            $error .= _MISSING_CREATION_DATE;
        }
        if ($find_docserver_id == false) {
            $error .= _MISSING_DOCSERVER_ID;
        }
        if ($find_path == false) {
            $error .= _MISSING_PATH;
        }
        if ($find_filename == false) {
            $error .= _MISSING_FILENAME;
        }
        if ($find_offset == false) {
            $error .= _MISSING_OFFSET;
        }
        if ($find_logical_adr == false) {
            $error .= _MISSING_LOGICAL_ADR;
        }
        if ($find_fingerprint == false) {
            $error .= _MISSING_FINGERPRINT;
        }
        if ($find_filesize == false) {
            $error .= _MISSING_FILESIZE;
        }
        if ($find_status == false) {
            $error .= _MISSING_STATUS;
        }

        if (!empty($error)) {
            $status = false;
        } else {
            $status = true;
        }

        return [
            'status' => $status,
            'error'  => $error
        ];
    }

    function get_values_in_array($val){
        $tab = explode('$$',$val);
        $values = array();
        for($i=0; $i<count($tab);$i++)
        {
            $tmp = explode('#', $tab[$i]);

            $val_tmp=array();
            for($idiese=1;$idiese<count($tmp);$idiese++){
                $val_tmp[]=$tmp[$idiese];
            }
            $valeurDiese = implode("#",$val_tmp);
            if(isset($tmp[1]))
            {
                array_push($values, array('ID' => $tmp[0], 'VALUE' => $valeurDiese));
            }
        }
        return $values;
    }
}