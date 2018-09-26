<?php
include_once '../../core/init.php';
require_once('core/class/class_functions.php');
require_once('core/class/class_core_tools.php');
$core = new core_tools();
$core->load_lang();
$customId = 'cs_' . $_SESSION['config']['databasename'];
//print_r($_SESSION);
$destDir = $_SESSION['config']['corepath'] . 'custom' .
    DIRECTORY_SEPARATOR . $customId .
    DIRECTORY_SEPARATOR . 'apps' . DIRECTORY_SEPARATOR .
    'maarch_entreprise' . DIRECTORY_SEPARATOR
    . 'img' . DIRECTORY_SEPARATOR;
$destUrl = '../custom/' . $customId . '/apps/maarch_entreprise/img/';

$pathToImg = 'install/img/background/' . $_REQUEST['imgSelected'] . '.jpg';

if (file_exists($pathToImg)) {
    $imgExt = pathinfo($pathToImg, PATHINFO_EXTENSION);
    if (strtoupper($imgExt) <> 'JPG' && strtoupper($imgExt) <> 'JPEG') {
        echo 'error:' . _FILE_FORMAT_NOT_ALLOWED;
    } else {
        $returnSize = getimagesize($pathToImg);
        if ($returnSize[0] < 1920 && $returnSize[1] < 1000) {
            echo 'error:' . _IMG_SIZE_NOT_ALLOWED . ' : ' . $returnSize[0] . 'x' . $returnSize[1];
        } else {
            if (file_exists($_SESSION['config']['tmppath'] . basename($pathToImg))) {
                unlink($_SESSION['config']['tmppath'] . basename($pathToImg));
            }
            if (!is_dir($destDir)) {
                mkdir($destDir, 0777);
            }
            copy($pathToImg, $destDir . 'bodylogin.jpg');
            echo $destUrl . 'bodylogin.jpg';
        }
    }
} else {
    echo 'error:'._SELECT_IMG_FIRST;
}

