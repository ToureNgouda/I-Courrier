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
require_once('modules/notes/notes_tables.php');
require_once('modules/notes/lang/fr.php');
$core = new core_tools();
$core->load_lang();
$db = new Database();

$stmt = $db->query(
    "SELECT contacts_v2.lastname as lastname, 
	contacts_v2.firstname as firstname, 
	contacts_v2.contact_id as contact_id, 
	contacts_v2.society as society, 
	contacts_v2.function as function,  
	contacts_v2.other_data as other_data, 
	contacts_v2.is_corporate_person as is_corporate_person, 
	contacts_v2.user_id as user_id, 
	contacts_v2.title as title, 
	contacts_v2.enabled as enabled, 
	contact_addresses.email as email,
	contact_addresses.phone as phone
	FROM " .APPS_CONTACTS_V2. "
	INNER JOIN ".APPS_CONTACTS_ADDRESSES."
	ON contacts_v2.contact_id = contact_addresses.contact_id
	 ORDER BY contacts_v2.lastname, contacts_v2.society
");
//$db->show();
$i=0;
$notesList = '';
if ($stmt->rowCount() < 1) {
    $notesList .= '<span id="noContacts">no contacts</span>';
} else {
    while ($line = $stmt->fetchObject()) {
		if(($line->user_id == $_SESSION['user']['UserId']) || ($line->user_id == "")) {
			if($line->is_corporate_person == "N") {
				$notesList .=		'<div class="row"  id="ViewContacts">
									<a href="generic_profil_contacts.php?
										&is_corporate_person=' .functions::xssafe($line->is_corporate_person). '
										&contact_id=' .functions::xssafe($line->contact_id). '
										&user_id=' .functions::xssafe($line->user_id). '">
																	
										<label>'
												.functions::xssafe($line->lastname). ' '
												.functions::xssafe($line->firstname). '
										</label>
									</a>
									<table>
										<tr>
											<td align="left" width="70%"><a href="mailto:' .functions::xssafe($line->email). '">' .functions::xssafe($line->email). '</a></td>
											<td align="right"><a href="tel:' .functions::xssafe($line->phone). '">' .functions::xssafe($line->phone). '</a></td>
										</tr>
									</table>
								</div>
				';
			} else {
				$notesList .=		'<div class="row"  id="ViewContacts">
									<a href="generic_profil_contacts.php?
										&is_corporate_person=' .functions::xssafe($line->is_corporate_person). '
										&contact_id=' .functions::xssafe($line->contact_id). '
										&user_id=' .functions::xssafe($line->user_id). '">
																	
										<label>'
												.functions::xssafe($line->society). '
										</label>
									</a>
									<table>
										<tr>
											<td align="left" width="70%"><a href="mailto:' .functions::xssafe($line->email). '">' .functions::xssafe($line->email). '</a></td>
											<td align="right"><a href="tel:' .functions::xssafe($line->phone). '">' .functions::xssafe($line->phone). '</a></td>
										</tr>
									</table>
								</div>
				';
			}
		}
    }
}
?>

<div id="contacts" title="<?php echo _MY_CONTACTS;?>" class="panel">
		<form selected="true" title="Addcontact" class="panel" method="post" action="add_contact.php">
			<a href="#" type="submit" style="text-decoration:none"><input type="submit" class="whiteButton" value="<?php echo _ADD_CONTACT ?>"></a>
		</form>
		<input name="contact" type="search" id="contact" placeholder="Rechercher" onKeyUp="searchContacts($('contact').value);"/>
		<br/><br/>
	<fieldset>
        <div id="allContacts">
            <?php
                echo $notesList;
            ?>
        </div>
    </fieldset>
</div>


