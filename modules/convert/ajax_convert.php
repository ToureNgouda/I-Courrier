<?php

// sample for attachments in allMode :
// http://urltomaarch/apps/maarch_entreprise/index.php?page=ajax_convert&module=convert&id=1&collId=attachments_coll

$func = new functions();

for ($i=0;$i<count($_SESSION['collections']);$i++) {
    if ($_SESSION['collections'][$i]['id'] == $_REQUEST['collId']) {
        $resTable = $_SESSION['collections'][$i]['table'];
        $adrTable = $_SESSION['collections'][$i]['adr'];
    }
}

// echo $_REQUEST['collId'] . '<br />';
// echo $resTable . PHP_EOL . '<br />';
// echo $adrTable . PHP_EOL . '<br />';
// echo $_REQUEST['id'] . PHP_EOL . '<br />';

$params = array(
    'collId' => $_REQUEST['collId'], 
    'resTable' => $resTable, 
    'adrTable' => $adrTable, 
    'resId' => $_REQUEST['id'],
    'tmpDir' => $_SESSION['config']['tmppath']
);

require_once 'core/services/ManageDocservers.php';
$ManageDocservers = new Core_ManageDocservers_Service();

require_once 'modules/convert/services/ManageConvert.php';
$ManageConvertService = new Convert_ManageConvert_Service();
$resultOfConversion = $ManageConvertService->convertAll($params);

//var_dump($resultOfConversion);
