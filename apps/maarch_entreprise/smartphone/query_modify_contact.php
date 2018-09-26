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
require_once('core/class/class_security.php');
require_once('core/core_tables.php');
require_once('apps/maarch_entreprise/apps_tables.php');
require_once('modules/notes/notes_tables.php');
require_once('modules/notes/lang/fr.php');
$core = new core_tools();
$core->load_lang();
$db = new Database();

if(($_REQUEST['lastname'] && $_REQUEST['firstname']) == ""){
	$_REQUEST['corporate_person'] = "Y";
}
else {
	$_REQUEST['corporate_person'] = "N";
}

$query = "UPDATE " .APPS_CONTACTS_V2. " SET ";
$query .= "lastname = '" . $_REQUEST['lastname'] . "', ";
$query .= "firstname = '" . $_REQUEST['firstname'] . "', ";
$query .= "society = '" . $_REQUEST['society'] . "', ";
$query .= "other_data = '" . $_REQUEST['other'] . "', ";
$query .= "is_corporate_person = '" . $_REQUEST['corporate_person'] . "', ";
$query .= "user_id = '" . $_SESSION['user']['UserId'] . "' WHERE contact_id = ?";
$db->query($query,array($_REQUEST['contact_id']));


$query = "UPDATE " .APPS_CONTACTS_ADDRESSES. " SET ";
$query .= "phone = '" . $_REQUEST['phone'] . "', ";
$query .= "email = '" . $_REQUEST['mail'] . "', ";
$query .= "address_num = '" . $_REQUEST['number'] . "', ";
$query .= "address_street = '" . $_REQUEST['street'] . "', ";
$query .= "address_complement = '" . $_REQUEST['complement'] . "', ";
$query .= "address_town = '" . $_REQUEST['town'] . "', ";
$query .= "address_postal_code = '" . $_REQUEST['postal_code'] . "', ";
$query .= "address_country = '" . $_REQUEST['country'] . "', ";
$query .= "user_id = '" . $_SESSION['user']['UserId'] . "' WHERE contact_id = ? ";

$db->query($query, array($_REQUEST['contact_id']));

//$db->show();

header('Location:' . $_SESSION['config']['businessappurl']. 'smartphone/index.php?page=welcome');?>