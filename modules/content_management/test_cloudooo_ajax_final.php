<?php

require_once 'core/class/class_functions.php';
require_once 'core/class/class_core_tools.php';

$fileNameOnTmp = 'tmp_file_cloudooo_' . $_SESSION['user']['UserId']
    . '_' . rand() . '.docy';
$filePathOnTmp = $_SESSION['config']['tmppath'] . $fileNameOnTmp;
//var_dump($_FILES);
if (!is_uploaded_file($_FILES['file']['tmp_name'])) {
    echo 'error uploaded file';
} else {
    move_uploaded_file($_FILES['file']['tmp_name'], $filePathOnTmp);
}

// TODO ONLY FOR SIMPLE TEST
//$serverAddress = "https://cloudooo.erp5.net/";
$serverAddress = "http://192.168.21.24:8002/";
$file = $filePathOnTmp;
if (!file_exists($file)) {
    echo 'file not exists : ' . $file . PHP_EOL;
    exit;
}

//convert in docx

$fileContent = file_get_contents($file, FILE_BINARY);
$encodedContent = base64_encode($fileContent);

require_once 'apps/maarch_entreprise/tools/phpxmlrpc/lib/xmlrpc.inc';
require_once 'apps/maarch_entreprise/tools/phpxmlrpc/lib/xmlrpcs.inc';
require_once 'apps/maarch_entreprise/tools/phpxmlrpc/lib/xmlrpc_wrappers.inc';

$outputFormat = 'docx';

$params = array();
array_push($params, new PhpXmlRpc\Value($encodedContent));
array_push($params, new PhpXmlRpc\Value('docy'));
array_push($params, new PhpXmlRpc\Value($outputFormat));
array_push($params, new PhpXmlRpc\Value(false));

$v = new PhpXmlRpc\Value($params, "array");

$req = new PhpXmlRpc\Request('convertFile', $v);
$client = new PhpXmlRpc\Client($serverAddress);
$resp = $client->send($req);
if (!$resp->faultCode()) {
    $encoder = new PhpXmlRpc\Encoder();
    $value = $encoder->decode($resp->value());
    if (!is_dir('apps/maarch_entreprise/tmp/cloudooo_results')) {
        mkdir('apps/maarch_entreprise/tmp/cloudooo_results');
    }
    $fileName = 'apps/maarch_entreprise/tmp/cloudooo_results/' . rand() . '.' . $outputFormat;
    $theFile = fopen($fileName, 'w+');
    fwrite($theFile, base64_decode($value));
    fclose($theFile);
} else {
    //echo "{status : 1, pdf : 'no file', content : 'empty', error : '" . addslashes(htmlspecialchars($resp->faultCode()) . " Reason: '" . htmlspecialchars($resp->faultString())) . "'}";
}

//convert in pdf too
$fileContent = file_get_contents($fileName, FILE_BINARY);
$encodedContent = base64_encode($fileContent);
$outputFormat = 'pdf';
$params = array();
array_push($params, new PhpXmlRpc\Value($encodedContent));
array_push($params, new PhpXmlRpc\Value('docx'));
array_push($params, new PhpXmlRpc\Value($outputFormat));
array_push($params, new PhpXmlRpc\Value(false));
$v = new PhpXmlRpc\Value($params, "array");
$req = new PhpXmlRpc\Request('convertFile', $v);
$client = new PhpXmlRpc\Client($serverAddress);
$resp = $client->send($req);
if (!$resp->faultCode()) {
    $encoder = new PhpXmlRpc\Encoder();
    $value = $encoder->decode($resp->value());
    if (!is_dir('apps/maarch_entreprise/tmp/cloudooo_results')) {
        mkdir('apps/maarch_entreprise/tmp/cloudooo_results');
    }
    //$fileName = 'apps/maarch_entreprise/tmp/cloudooo_results/' . rand() . '.' . $outputFormat;
    $fileName = 'apps/maarch_entreprise/tmp/cloudooo_results/final.' . $outputFormat;
    $theFile = fopen($fileName, 'w+');
    fwrite($theFile, base64_decode($value));
    fclose($theFile);
    //echo $fileName;
    header('Pragma: public');
    header('Expires: 0');
    header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
    header('Cache-Control: public');
    header('Content-Description: File Transfer');
    header('Content-Transfer-Encoding: binary');
    readfile($fileName);
} else {
    //echo "{status : 1, pdf : 'no file', content : 'empty', error : '" . addslashes(htmlspecialchars($resp->faultCode()) . " Reason: '" . htmlspecialchars($resp->faultString())) . "'}";
}

exit ();
