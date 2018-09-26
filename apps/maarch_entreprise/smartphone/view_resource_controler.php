<?php
require_once('core/class/class_request.php');
require_once('core/class/class_security.php');
require_once('core/class/class_resource.php');
require_once('core/class/docservers_controler.php');
$core_tools = new core_tools();
$core_tools->test_user();
$core_tools->load_lang();
$function = new functions();
$sec = new security();
$mode = '';
$calledByWS = false;

if ($s_id == '') {
    $_SESSION['error'] = _THE_DOC . ' ' . _IS_EMPTY;
    header('location: ' . $_SESSION['config']['businessappurl'] . 'index.php');
    exit();
} else {
    $table = '';
    if (isset($_REQUEST['collid']) && $_REQUEST['collid'] <> '') {
        $_SESSION['collection_id_choice'] = $_REQUEST['collid'];
    }
    if (isset($_SESSION['collection_id_choice']) 
        && !empty($_SESSION['collection_id_choice'])
    ) {
        $table = $sec->retrieve_view_from_coll_id(
            $_SESSION['collection_id_choice']
        );
        if (!$table) {
            $table = $sec->retrieve_table_from_coll(
                $_SESSION['collection_id_choice']
            );
        }
    } else {
        if (isset($_SESSION['collections'][0]['view']) 
            && !empty($_SESSION['collections'][0]['view'])
        ) {
            $table = $_SESSION['collections'][0]['view'];
        } else {
            $table = $_SESSION['collections'][0]['table'];
        }
    }
    for ($cptColl = 0;$cptColl < count($_SESSION['collections']);$cptColl++) {
        if ($table == $_SESSION['collections'][$cptColl]['table'] 
            || $table == $_SESSION['collections'][$cptColl]['view']
        ) {
            $adrTable = $_SESSION['collections'][$cptColl]['adr'];
        }
    }
    if ($adrTable == '') {
        $adrTable = $_SESSION['collections'][0]['adr'];
    }
    $docserverControler = new docservers_controler();
    $viewResourceArr = array();
    $docserverLocation = array();
    $docserverLocation =
        $docserverControler->retrieveDocserverNetLinkOfResource(
            $s_id, $table, $adrTable
        );
    $viewResourceArr = $docserverControler->viewResource(
            $s_id, 
            $table,
            $adrTable, 
            false
        );
    if ($viewResourceArr['error'] <> '') {
        //...
    } else {
        //$core_tools->show_array($viewResourceArr);
        if ($viewResourceArr['called_by_ws']) {
            $fileContent = base64_decode($viewResourceArr['file_content']);
            $fileNameOnTmp = 'tmp_file_' . rand() . '_' 
                . md5($fileContent) . '.' 
                . strtolower($viewResourceArr['ext']);
            $filePathOnTmp = $_SESSION['config']['tmppath'] 
                . DIRECTORY_SEPARATOR . $fileNameOnTmp;
            $inF = fopen($filePathOnTmp, 'w');
            fwrite($inF, $fileContent);
            fclose($inF);
        } else {
            $filePathOnTmp = $viewResourceArr['file_path'];
        }
        if (strtolower(
            $viewResourceArr['mime_type']
        ) == 'application/maarch'
        ) {
            $myfile = fopen($filePathOnTmp, 'r');
            $data = fread($myfile, filesize($filePathOnTmp));
            fclose($myfile);
            $content = stripslashes($data);
            $newFileName = rand() . '.html';
            $newFilePathOnTmp = $viewResourceArr['tmp_path'] . $newFileName;
            rename($filePathOnTmp, $newFilePathOnTmp);
            $filePathOnTmp = $newFilePathOnTmp;
            $viewResourceArr['file_path'] = $filePathOnTmp;
            $viewResourceArr['ext'] = 'html';
        }
    }
    //include('view_resource.php');
    $_SESSION['viewResourceArr'] = $viewResourceArr;
    $_SESSION['content'] = $content;
    $_SESSION['filePathOnTmp'] = $filePathOnTmp;
    if ($_SESSION['custom_override_id'] <> '') {
        $fileUrl =  $_SESSION['config']['corepath'] . 'custom/'
                . $_SESSION['custom_override_id'] . '/apps/maarch_entreprise/tmp/' 
                . basename($_SESSION['filePathOnTmp']);
    } else {
        $fileUrl =  $_SESSION['config']['businessappurl'] . 'tmp/' 
                . basename($_SESSION['filePathOnTmp']);
    }
    if (!file_exists($fileUrl)) {
        $fileUrl =  $_SESSION['config']['businessappurl'] . 'tmp/'
                . basename($_SESSION['filePathOnTmp']);
    }
    $_SESSION['fileUrl'] = $fileUrl;
}
