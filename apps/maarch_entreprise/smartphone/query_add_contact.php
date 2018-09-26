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

if(($_REQUEST['lastname'] == "") && ($_REQUEST['firstname'] == "")){
	$_REQUEST['corporate_person'] = "Y";
}
else {
	$_REQUEST['corporate_person'] = "N";
}

$query = "INSERT INTO " .APPS_CONTACTS_V2. " (";
if($_REQUEST['lastname'] != ""){
	$query .= "lastname, ";
	$query2 = "'". $_REQUEST['lastname'] . "', ";
}
if($_REQUEST['firstname'] != ""){
	$query .= "firstname, ";
	$query2 .= "'". $_REQUEST['firstname'] . "', ";
}
if($_REQUEST['society'] != ""){
	$query .= "society, ";
	$query2 .= "'". $_REQUEST['society'] . "', ";
}
if($_REQUEST['other'] != ""){
	$query .= "other_data, ";
	$query2 .= "'". $_REQUEST['other'] . "', ";
}
if($_REQUEST['corporate_person'] != ""){
	$query .= "is_corporate_person, ";
	$query2 .= "'". $_REQUEST['corporate_person'] . "', ";
}
if($_REQUEST['function'] != ""){
	$query .= "function, ";
	$query2 .= "'". $_REQUEST['function'] . "', ";
}
if($_REQUEST['contact_type'] != ""){
	$query .= "contact_type, ";
	$query2 .= "'". $_REQUEST['contact_type'] . "', ";
}
if($_REQUEST['title'] != ""){
	$query .= "title, ";
	$query2 .= "'". $_REQUEST['title'] . "', ";
}
if($_REQUEST['entity_id'] != ""){
    $query .= "entity_id, ";
    $query2 .= "'". $_REQUEST['entity_id'] . "', ";
}
if(!isset($_REQUEST['creation_date'])){
    $query .= "creation_date, ";
    $query2 .= "'". date('Y-m-d H:i:u') . "', ";
}


$query .= "user_id) VALUES (";
$query2 .= "'". $_SESSION['user']['UserId'] . "')";

$db->query($query . $query2);


$contact_id = "SELECT contact_id FROM " .APPS_CONTACTS_V2 ." WHERE lastname = ? ";          // Récupération de l'ID de l'utilisation
$stmt = $db->query($contact_id,array($_REQUEST['lastname']));       // Afin de rajouter les informations dans la base contact_adresses
$id_contact = $stmt->fetchColumn(0);


$query = "INSERT INTO " .APPS_CONTACTS_ADDRESSES. " (";
if(isset($id_contact)) {
    $query .= "contact_id,";
    $query2 = "'" . $id_contact . "', ";
}
if($_REQUEST['entity_id'] != ""){
    $query .= "entity_id, ";
    $query2 .= "'". $_REQUEST['entity_id'] . "', ";
}
if($_REQUEST['email'] != ""){
    $query .= "email, ";
    $query2 .= "'". $_REQUEST['email'] . "', ";
}
if($_REQUEST['phone'] != ""){
    $query .= "phone, ";
    $query2 .= "'". $_REQUEST['phone'] . "', ";
}
if($_REQUEST['number'] != ""){
    $query .= "address_num, ";
    $query2 .= "'". $_REQUEST['number'] . "', ";
}
if($_REQUEST['street'] != ""){
    $query .= "address_street, ";
    $query2 .= "'". $_REQUEST['street'] . "', ";
}
if($_REQUEST['complement'] != ""){
    $query .= "address_complement, ";
    $query2 .= "'". $_REQUEST['complement'] . "', ";
}
if($_REQUEST['town'] != ""){
    $query .= "address_town, ";
    $query2 .= "'". $_REQUEST['town'] . "', ";
}
if($_REQUEST['postal_code'] != ""){
    $query .= "address_postal_code, ";
    $query2 .= "'". $_REQUEST['postal_code'] . "', ";
}
if($_REQUEST['country'] != ""){
    $query .= "address_country, ";
    $query2 .= "'". $_REQUEST['country'] . "', ";
}

$query .= "user_id) VALUES (";
$query2 .= "'". $_SESSION['user']['UserId'] . "')";
	//var_dump($_SESSION['user']['UserId']);

$db->query($query . $query2);

//$db->show();

header('Location:' . $_SESSION['config']['businessappurl']. 'smartphone/index.php?page=welcome#_contacts');
?>
