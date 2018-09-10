<?php

// var_dump($_FILES['file']);
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

if (0 < $_FILES['file']['error']) {
    echo 'error:' . $_FILES['file']['error'];
} else {
    if (file_exists($_FILES['file']['tmp_name'])) {
        $imgExt = pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION);
        if (strtoupper($imgExt) <> 'JPG' && strtoupper($imgExt) <> 'JPEG') {
            echo 'error:' . _FILE_FORMAT_NOT_ALLOWED;
        } else {
            $returnSize = getimagesize($_FILES['file']['tmp_name']);
            if ($returnSize[0] < 1920 && $returnSize[1] < 1000) {
                echo 'error:' . _IMG_SIZE_NOT_ALLOWED . ' : ' . $returnSize[0] . 'x' . $returnSize[1];
            } else {
                if (file_exists($_SESSION['config']['tmppath'] . $_FILES['file']['name'])) {
                    unlink($_SESSION['config']['tmppath'] . $_FILES['file']['name']);
                }
                move_uploaded_file(
                    $_FILES['file']['tmp_name'],
                    $_SESSION['config']['tmppath'] . $_FILES['file']['name']
                );
                if (!is_dir($destDir)) {
                    mkdir($destDir, 0777);
                }
                copy($_SESSION['config']['tmppath'] . $_FILES['file']['name'], $destDir . 'bodylogin.jpg');
                echo $destUrl . 'bodylogin.jpg';
            }
        }
    } else {
        echo 'error:'._SELECT_IMG_FIRST;
    }
}
