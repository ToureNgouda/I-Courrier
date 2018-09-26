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

$res_id_master = $_POST['res_id'];
$code_session = $sec->getPasswordHash($_POST['code_session']);

$db = new Database();
$stmt = $db->query("SELECT ra_code, ra_expiration_date FROM users WHERE user_id = ?", array($_SESSION['user']['UserId']));
$res = $stmt->fetchObject();
$ra_code = $res->ra_code;
$ra_expiration_date = $res->ra_expiration_date;

if ($ra_code == $code_session){
	$db->query("UPDATE res_attachments SET status = 'TRA' WHERE res_id_master = ? AND status = 'TMP' AND attachment_type = 'signed_response'", array($res_id_master));
	$_SESSION['user']['code_session'] = $_POST['code_session'];
	echo "{status:1}";
}
else echo "{status:0, ra_code:'$ra_code', ra_expiration_date:'$ra_expiration_date', res_id_master:'$res_id_master', code_session:'$code_session'}";
exit;
?>
