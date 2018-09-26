<?php
/**
* Copyright Maarch since 2008 under licence GPLv3.
* See LICENCE.txt file at the root folder for more details.
* This file is part of Maarch software.
*
*/

require_once 'core/init.php';
require_once 'install/class/Class_Install.php';
include_once 'core/docservers_tools.php';

$Class_Install = new Install();
$listLang = $Class_Install->loadLang();

if (!empty($_REQUEST['myVar'])) {
    $version = $_REQUEST['myVar'];
    $_SESSION['lastTagVersion'] = $version;
} else {
    $return['status'] = 0;
    $return['text'] = _VERSION_EMPTY;

    $jsonReturn = json_encode($return);

    echo $jsonReturn;
    exit;
}
//retrieve required sources for Maarch labs
$versionPath = 'NEW_VERSION';
if (is_dir($versionPath)) {
    Ds_washTmp($versionPath);
}

$versionFile = $version . '.zip';
if(!file_exists($versionFile)) {
    file_put_contents(
        $versionFile, 
        fopen("https://labs.maarch.org/maarch/MaarchCourrier/repository/archive.zip?ref=" . $version, 'r')
    );
}

if (!file_exists($versionFile)) {
    $return['status'] = 0;
    $return['text'] = _VERSION_NOT_DOWNLOADED;

    $jsonReturn = json_encode($return);

    echo $jsonReturn;
    exit;
}

umask(0022);
if (!is_dir($versionPath)) {
    mkdir($versionPath, 0770);
}

$zip = new ZipArchive;
if ($zip->open($versionFile) === TRUE) {
    $zip->extractTo($versionPath);
    $zip->close();
} else {
    $return['status'] = 0;
    $return['text'] = _CANNOT_EXTRACT;

    $jsonReturn = json_encode($return);

    echo $jsonReturn;
    exit;
}
//$phar = new PharData($versionFile);
//$phar->extractTo($versionPath, null, true);

$fileTab = scandir($versionPath);
$nbFiles = count($fileTab);
for ($n = 0;$n < count($fileTab);$n++) {
    $currentFileName = array();
    if (
        $fileTab[$n] <> '.' 
        && $fileTab[$n] <> '..'
        && is_dir($versionPath . '/' . $fileTab[$n])
    ) {
        $finalVersionPath = $versionPath . '/' . $fileTab[$n];
    }
}

if (empty($finalVersionPath)) {
    $return['status'] = 0;
    $return['text'] = _VERSION_NOT_EXTRACTED;

    $jsonReturn = json_encode($return);

    echo $jsonReturn;
    exit;
} else {
    Ds_washTmp($finalVersionPath . '/install');
    unlink($versionFile);
    echo '{"status":1}';
    exit;
}
exit;
