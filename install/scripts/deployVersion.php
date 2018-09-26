<?php
/**
* Copyright Maarch since 2008 under licence GPLv3.
* See LICENCE.txt file at the root folder for more details.
* This file is part of Maarch software.
*
*/

require_once 'core/init.php';
require_once 'install/class/Class_Install.php';

$Class_Install = new Install();
$listLang = $Class_Install->loadLang();

$versionPath = 'NEW_VERSION';
if (!is_dir($versionPath)) {
    $return['status'] = 0;
    $return['text'] = 'NEW_VERSION ' . _NOT_A_DIRECTORY;

    $jsonReturn = json_encode($return);

    echo $jsonReturn;
    exit;
}

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
    $return['text'] = 'finalVersionPath ' . _EMPTY;

    $jsonReturn = json_encode($return);

    echo $jsonReturn;
    exit;
}

$excludeExt = ['xml','sh'];
if (!$Class_Install->copy_dir(
        $finalVersionPath . DIRECTORY_SEPARATOR,
        //'/opt/maarch/test' . DIRECTORY_SEPARATOR
        $_SESSION['config']['corepath'],
        $excludeExt
    )
) {
    $return['status'] = 0;
    $return['text'] = _CAN_NOT_COPY_TO . ':' . $_SESSION['config']['corepath'];

    $jsonReturn = json_encode($return);

    echo $jsonReturn;
    exit;
} else {
    require_once "core/class/class_functions.php";
    require_once "core/class/class_db_pdo.php";
    $db = new Database();
    $query = "UPDATE parameters SET param_value_string = ? where id = 'database_version'";
    $stmt = $db->query($query, [$_SESSION['lastTagVersion']]);

    include_once 'core/docservers_tools.php';
    Ds_washTmp($versionPath);
    echo '{"status":1}';
    exit;
}
exit;
