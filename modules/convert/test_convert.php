<?php

// sample for letterbox :
// http:/urltomaarch/apps/maarch_entreprise/index.php?page=test_convert&module=convert&id=1931&collId=letterbox_coll

// sample for attachments :
// http:/urltomaarch/apps/maarch_entreprise/index.php?page=test_convert&module=convert&id=1&collId=attachments_coll

// sample for thumbnails :
// http:/urltomaarch//apps/maarch_entreprise/index.php?page=test_convert&module=convert&id=1939&collId=letterbox_coll&convertMode=thumbnails

// sample for fulltext :
// http:/urltomaarch//apps/maarch_entreprise/index.php?page=test_convert&module=convert&id=1989&collId=letterbox_coll&convertMode=fulltext

// sample for letterbox in allMode :
// http:/urltomaarch/apps/maarch_entreprise/index.php?page=test_convert&module=convert&id=1931&collId=letterbox_coll&convertMode=allMode

// sample for attachments in allMode :
// http:/urltomaarch/apps/maarch_entreprise/index.php?page=test_convert&module=convert&id=1&collId=attachments_coll&convertMode=allMode

$func = new functions();

for ($i=0;$i<count($_SESSION['collections']);$i++) {
    if ($_SESSION['collections'][$i]['id'] == $_REQUEST['collId']) {
        $resTable = $_SESSION['collections'][$i]['table'];
        $adrTable = $_SESSION['collections'][$i]['adr'];
    }
}

if (empty($_REQUEST['convertMode'])) {
    $convertMode = 'convert';
} else {
    $convertMode = $_REQUEST['convertMode'];
}

echo $_REQUEST['convertMode'] . '<br />';
echo $_REQUEST['collId'] . '<br />';
echo $resTable . PHP_EOL . '<br />';
echo $adrTable . PHP_EOL . '<br />';
echo $_REQUEST['id'] . PHP_EOL . '<br />';

$params = array(
    'collId' => $_REQUEST['collId'], 
    'resTable' => $resTable, 
    'adrTable' => $adrTable, 
    'resId' => $_REQUEST['id'],
    'tmpDir' => $_SESSION['config']['tmppath']
);

require_once 'core/services/ManageDocservers.php';
$ManageDocservers = new Core_ManageDocservers_Service();

if ($convertMode == 'allMode') {
	require_once 'modules/convert/services/ManageConvert.php';
    $ManageConvertService = new Convert_ManageConvert_Service();
    $resultOfConversion = $ManageConvertService->convertAll($params);
    $adrType = 'CONV';
} elseif ($convertMode == 'thumbnails') {
	$adrType = 'TNL';
    require_once 'modules/convert/services/ProcessThumbnails.php';
    $ProcessConvertService = new Convert_ProcessThumbnails_Service();
    $resultOfConversion = $ProcessConvertService->thumbnails($params);
    $resourcePath = $ManageDocservers->getSourceResourcePath(
        $resTable, 
        $adrTable, 
        $_REQUEST['id'], 
        $adrType
    );
} elseif ($convertMode == 'fulltext') {
	$adrType = 'TXT';
    require_once 'modules/convert/services/ProcessFulltext.php';
    $ProcessConvertService = new Convert_ProcessFulltext_Service();
    $resultOfConversion = $ProcessConvertService->fulltext($params);
    $resourcePath = $ManageDocservers->getSourceResourcePath(
        $resTable, 
        $adrTable, 
        $_REQUEST['id'], 
        $adrType
    );
} else {
	$adrType = 'CONV';
    require_once 'modules/convert/services/ProcessConvert.php';
    $ProcessConvertService = new Convert_ProcessConvert_Service();
    $resultOfConversion = $ProcessConvertService->convert($params);
    $resourcePath = $ManageDocservers->getSourceResourcePath(
        $resTable, 
        $adrTable, 
        $_REQUEST['id'], 
        $adrType
    );
}

echo $resourcePath . '<br />';

$func->show_array($resultOfConversion);

$link .= $_SESSION['config']['businessappurl']
    . 'index.php?display=true'
    . '&dir=indexing_searching'
    . '&page=ViewRes'
    . '&collId=' . $_REQUEST['collId']
    . '&id=' . $_REQUEST['id']
    . '&adrType=' . $adrType;
$linkToRes = '<a href="' . $link . '" target="_blank">clic to view the new resource !</a>';

echo '<br />' . $linkToRes;