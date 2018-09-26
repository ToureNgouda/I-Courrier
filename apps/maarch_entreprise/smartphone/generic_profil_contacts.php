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
	contact_addresses.phone as phone,
	contact_addresses.address_num as address_num, 
    contact_addresses.address_street as address_street, 
    contact_addresses.address_complement as address_complement, 
    contact_addresses.address_town as address_town, 
    contact_addresses.address_postal_code as address_postal_code, 
    contact_addresses.address_country as address_country
	FROM " .APPS_CONTACTS_V2. "
	INNER JOIN ".APPS_CONTACTS_ADDRESSES."
	ON contacts_v2.contact_id = contact_addresses.contact_id
	WHERE contacts_v2.contact_id = ?",array($_REQUEST['contact_id']));
//$db->show();
$notesList = '';
$_SESSION['contact_id'] = $_REQUEST['contact_id'];
if ($stmt->rowCount() < 1) {
    $notesList = 'No contact or error query';
}
else {
    while($line = $stmt->fetchObject()) {
		?> <div id="settings" title="Profile" class="panel">
            <h2><span class="fa fa-user"></span>&nbsp;</a>Infos</h2>
			<fieldset>
				<?php if( $_REQUEST['is_corporate_person']  == 'N') { ?>
					<?php if( $line->lastname  != "") { ?>
						<div class="row">
							<table>
								<tr>
									<td width="50%" align="left">
										<label><b><?php echo _LASTNAME ?></b></label>
									</td>
									<td width="50%" align="right">
										<input name="lastname" readonly="readonly" value="<?php functions::xecho($line->lastname); ?>"/>
									</td>
								</tr>
							</table>
						</div>
					<?php } ?>
					<?php if( $line->firstname  != "") { ?>
						<div class="row">
							<table>
								<tr>
									<td width="50%" align="left">
										<label><b><?php echo _FIRSTNAME ?></b></label>
									</td>
									<td width="50%" align="right">
										<input name="firstname" readonly="readonly" value="<?php functions::xecho($line->firstname); ?>"/>
									</td>
								</tr>
							</table>
						</div>
					<?php } ?>
				<?php } ?>
				<?php if( $line->society  != "") { ?>
					<div class="row">
						<table>
							<tr>
								<td width="50%" align="left">
									<label><b><?php echo _SOCIETY ?></b></label>
								</td>
								<td width="50%" align="right">
									<input name="society" readonly="readonly" value="<?php functions::xecho($line->society); ?>"/>
								</td>
							</tr>
						</table>
					</div>
				<?php } ?>
				<?php if( $line->phone  != "") { ?>
					<div class="row">
						<table>
							<tr>
								<td width="50%" align="left">
									<label><b><?php echo "Téléphone";?></b></label>
								</td>
								<td width="50%" align="right">
									<input name="Phone" readonly="readonly" value="<?php functions::xecho($line->phone); ?>"/>
								</td>
							</tr>
						</table>
					</div>
				<?php } ?>
				<?php if( $line->email  != "") { ?>
					<div class="row">
						<table>
							<tr>
								<td width="50%" align="left">
									<label><b><?php echo _EMAIL ?></b></label>
								</td>
								<td width="50%" align="right">
									<input type="email" name="Mail" readonly="readonly" value="<?php functions::xecho($line->email); ?>"/>
								</td>
							</tr>
						</table>
					</div>
				<?php } ?>
				<div class="row">
					<table>
						<tr>
							<td width="50%" align="left">
								<label><b><?php echo _ADDRESS ?></b></label>
							</td>
							<td width="50%" align="right">
								<textarea name="Address" readonly="readonly" rows="4" cols="20"><?php functions::xecho($line->address_num . ' '
																						. $line->address_street . ' '
																						.$line->address_complement . ' '
																						. $line->address_postal_code . ' '
																						. $line->address_town . ' '
																						. $line->address_country); ?>
								</textarea>
							</td>
						</tr>
					</table>
				</div>
				<?php if( $line->other_data  != "") { ?>
					<div class="row">
						<table>
							<tr>
								<td width="50%" align="left">
									<label><b><?php echo _OTHER ?></b></label>
								</td>
								<td width="50%" align="right">
                                    <textarea name="Other" readonly="readonly" rows="4" cols="25">
                                        <?php functions::xecho($line->other_data); ?>
                                    </textarea>
								</td>
							</tr>
						</table>
					</div>
				<?php } ?>
			</fieldset>
			<form selected="true" title="modifyContact" class="panel" method="post" action="modify_contact.php">
				<a href="#" type="submit" style="text-decoration:none"><input type="submit" class="whiteButton" value="<?php echo _MODIFY ?>"></a>
				<input type="hidden" name="contact_id" class="whiteButton" value="<?php functions::xecho($_REQUEST['contact_id']); ?>">
			</form>
			<form selected="true" title="deleteContact" class="panel" method="post" action="query_delete_contact.php">
				<input type="hidden" name="contact_id" class="whiteButton" value="<?php functions::xecho($_REQUEST['contact_id']); ?>">
				<input type="submit" class="whiteButton" value="<?php echo _DELETE ?>">
			</form>
		</div>
						
	<?php
    }
}
?>
