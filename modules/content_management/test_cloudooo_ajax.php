<?php

require_once 'core/class/class_functions.php';
require_once 'core/class/class_core_tools.php';

/************************************************************************************/
//                                BEGIN TESTS
/************************************************************************************/

// TODO ONLY FOR SIMPLE TEST
//$serverAddress = "https://cloudooo.erp5.net/";
$serverAddress = "http://192.168.21.24:8002/";
$file = 'modules/content_management/test_cloudooo.docx';
//$file = '/var/www/html/maarch_trunk_git/modules/content_management/test_cloudooo.docx';
if (!file_exists($file)) {
    echo 'file not exists : ' . $file . PHP_EOL;
    exit;
}

$fileContent = file_get_contents($file, FILE_BINARY);
$encodedContent = base64_encode($fileContent);

require_once 'apps/maarch_entreprise/tools/phpxmlrpc/lib/xmlrpc.inc';
require_once 'apps/maarch_entreprise/tools/phpxmlrpc/lib/xmlrpcs.inc';
require_once 'apps/maarch_entreprise/tools/phpxmlrpc/lib/xmlrpc_wrappers.inc';

$outputFormat = 'docy';
//$outputFormat = 'pdf';

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
    $fileName = 'apps/maarch_entreprise/tmp/cloudooo_results/' . rand() . '.' . $outputFormat;
    $theFile = fopen($fileName, 'w+');
    fwrite($theFile, base64_decode($value));
    fclose($theFile);
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

