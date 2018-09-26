<?php
if (file_exists('../../../core/init.php')) {
    include_once '../../../core/init.php';
}
if (!isset($_SESSION['config']['corepath'])) {
    header('location: ../../../');
}
require_once('core/class/class_functions.php');
require_once('core/class/class_core_tools.php');
require_once('core/class/class_db_pdo.php');
require_once('core/core_tables.php');
require_once('apps/maarch_entreprise/apps_tables.php');
require_once('core/class/class_security.php');
require_once('core/class/class_history.php');

require_once 'core/class/docservers_controler.php';
require_once 'core/docservers_tools.php';
require_once 'core/class/class_resource.php';

require_once('apps/' . $_SESSION['config']['app_id'] . '/class/class_types.php');
if ($_SESSION['collection_id_choice'] == 'res_coll') {
    $catPhp = 'definition_mail_categories_invoices.php';
} else {
    $catPhp =    'definition_mail_categories.php';
}
if (file_exists(
    $_SESSION['config']['corepath'] . 'custom'. DIRECTORY_SEPARATOR
    . $_SESSION['custom_override_id'] . DIRECTORY_SEPARATOR . 'apps'
    . DIRECTORY_SEPARATOR . $_SESSION['config']['app_id'] . DIRECTORY_SEPARATOR
    . $catPhp
)
) {
    $path = $_SESSION['config']['corepath'] . 'custom'. DIRECTORY_SEPARATOR
          . $_SESSION['custom_override_id'] . DIRECTORY_SEPARATOR . 'apps'
          . DIRECTORY_SEPARATOR . $_SESSION['config']['app_id']
          . DIRECTORY_SEPARATOR . $catPhp;
} else {
    $path = 'apps' . DIRECTORY_SEPARATOR . $_SESSION['config']['app_id']
          . DIRECTORY_SEPARATOR . $catPhp;
}
include_once $path;
$core->load_lang();
$users = new history();
$sec = new security();

/* SAUVEGARDE DE L'IMAGE */
//echo $_POST['imageData'];
$data = str_replace("data:image/png;base64", "",$_POST['imageData']);
//echo $data;
$data = str_replace(' ', '+', $data);
$fileData = base64_decode($data);

//echo $idtrans.' - ';
$fileName_sign = $_SESSION['config']['tmppath']."tmp_sign_test.png";
$fileName_sign_resized = $_SESSION['config']['tmppath']."tmp_sign_test_resized.png";
//echo $fileName;
file_put_contents($fileName_sign, $fileData);

$res_id_master = $_POST['res_id'];
$res_id_attach = $_POST['res_id_attach'];

$_SESSION['tmpFilenameSign'] =$_SESSION['config']['businessappurl']. DIRECTORY_SEPARATOR ."tmp". DIRECTORY_SEPARATOR .basename($fileName_sign);
//echo $fileName_sign."\n";
//echo $res_id_master."\n";
//echo "<pre>".print_r($_SESSION['modules_loaded'],true)."</pre>";
$_SESSION['doc_id'] = $res_id_master;

$db = new Database();
$stmt = $db->query("SELECT * from res_view_attachments WHERE res_id = ? AND status <> 'SIGN' AND attachment_type IN ('response_project','outgoing_mail','sva') ORDER BY relation desc", array($res_id_attach));

if ($_SESSION['modules_loaded']['visa']['confirm_sign_by_email'] == 'true') {
    $codeSession = $_SESSION['user']['code_session'];
}


while($line = $stmt->fetchObject()){
	$objectId = $line->res_id;
	
	$_SESSION['visa']['last_resId_signed']['res_id'] = $line->res_id_master;
	$_SESSION['visa']['last_resId_signed']['title'] = $line->title;
	$_SESSION['visa']['last_resId_signed']['identifier'] = $line->identifier;
	$_SESSION['visa']['last_resId_signed']['type_id'] = $line->type_id;
	
	$_SESSION['visa']['last_resId_signed']['dest_contact'] = $line->dest_contact_id;
	$_SESSION['visa']['last_resId_signed']['dest_address'] = $line->dest_address_id;

	$_SESSION['visa']['repSignRel'] = $line->relation;


	include 'modules/visa/retrieve_attachment_from_cm.php';

	if (!file_exists($fileOnDs)){
		echo "{status:1, error : 'Fichier $fileOnDs non present'}";
		exit();
	}
	list($new_width, $height, $type, $attr) = getimagesize($fileName_sign);
	$new_heigh = round($new_width/($_SESSION['modules_loaded']['visa']['width_blocsign']/$_SESSION['modules_loaded']['visa']['height_blocsign']));

	$cmd_resize = "convert ".escapeshellarg($fileName_sign)." -resize ".$new_width."x".$new_heigh." -size ".$new_width."x".$new_heigh." xc:white +swap -gravity center -composite ".escapeshellarg($fileName_sign_resized);

	
	exec($cmd_resize);

	$cmd = "java -jar " 
			. escapeshellarg($_SESSION['config']['corepath'] . "modules/visa/dist/SignPdf.jar") . " " 
			. escapeshellarg($fileOnDs) . " " 
			. escapeshellarg($fileName_sign_resized) . " " 
			. escapeshellarg($_SESSION['modules_loaded']['visa']['width_blocsign']) . " " 
			. escapeshellarg($_SESSION['modules_loaded']['visa']['height_blocsign']) . " " 
			. escapeshellarg($_SESSION['config']['tmppath']);

	exec($cmd);
	
	$tmpFileName = pathinfo($fileOnDs, PATHINFO_BASENAME);
	$fileExtension = "pdf";
	
	
	
    if ($_SESSION['modules_loaded']['visa']['confirm_sign_by_email'] == 'true') {
        if (empty($codeSession)) {
            $statusSign = 'TMP';
            include 'modules/visa/save_attach_res_from_cm.php'; 
        }
    } else {
        include 'modules/visa/save_attach_res_from_cm.php'; 
    }
}

if ($_SESSION['modules_loaded']['visa']['confirm_sign_by_email'] == 'true') {
    if (empty($codeSession)) echo "{status:0}";
    else echo "{status:1}";
} else {
    echo "{status:1}";
}

exit;
