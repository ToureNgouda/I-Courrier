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
	WHERE contacts_v2.contact_id = ? ", array($_SESSION['contact_id']));
//$db->show();
$notesList = '';
if ($stmt->rowCount() < 1) {
    $notesList = 'No contact or error query';
}
else {
    while($line = $stmt->fetchObject()) {
		?> <div id="modifyContact" title="<?php echo _MODIFY_CONTACT ?>" class="panel">
				<h2><span class="fa fa-user"></span></a>Infos</h2>
				<form selected="true" class="panel" method="post" action="query_modify_contact.php" target="_self">
					<fieldset> <?php
						if($line->is_corporate_person == "N"){ ?>
							<div class="row">
								<label><?php echo _IS_CORPORATE_PERSON ?></label>
								<div class="toggle" onclick="cacher('person', 'lastname', 'firstname');"><span class="thumb"></span><span class="toggleOn">ON</span><span class="toggleOff">OFF</span></div>
							</div>
							<div id="person" class="row" style="display: block;">
								<div class="row">
									<table>
										<tr>
											<td width="50%" align="left">
												<label><b><?php echo _LASTNAME ?></b></label>
											</td>
											<td width="50%" align="right">
												<input id="lastname" name="lastname" value="<?php functions::xecho($line->lastname); ?>"/>
											</td>
										</tr>
									</table>
								</div>
								<div class="row">
									<table>
										<tr>
											<td width="50%" align="left">
												<label><b><?php echo _FIRSTNAME ?></b></label>
											</td>
											<td width="50%" align="right">
												<input id="firstname" name="firstname"  value="<?php functions::xecho($line->firstname); ?>"/>
											</td>
										</tr>
									</table>
								</div>
							</div>
						<?php
						} else { ?>
							<div class="row">
								<label><b><?php echo _IS_CORPORATE_PERSON ?></b></label>
								<div id="toggle" class="toggle" toggled="true" onclick="cacher('person2', 'lastname2', 'firstname2);"><span class="thumb"></span><span class="toggleOn">ON</span><span class="toggleOff">OFF</span></div>
							</div>
							<div id="person2" class="row" style="display: none;">
								<div class="row">
									<table>
										<tr>
											<td width="50%" align="left">
												<label><b><?php echo _LASTNAME ?></b></label>
											</td>
											<td width="50%" align="right">
												<input id="lastname2" name="lastname" value="<?php functions::xecho($line->lastname); ?>"/>
											</td>
										</tr>
									</table>
								</div>
								<div class="row">
									<table>
										<tr>
											<td width="50%" align="left">
												<label><b><?php echo _FIRSTNAME ?></b></label>
											</td>
											<td width="50%" align="right">
												<input id="firstname2" name="firstname"  value="<?php functions::xecho($line->firstname); ?>"/>
											</td>
										</tr>
									</table>
								</div>
							</div>
						<?php	
						}
						 ?>
						<div class="row">
							<table>
								<tr>
									<td width="50%" align="left">
										<label><b><?php echo _SOCIETY ?></b></label>
									</td>
									<td width="50%" align="right">
										<input name="society"  value="<?php functions::xecho($line->society); ?>"/>
									</td>
								</tr>
							</table>
						</div>
						<div class="row">
							<table>
								<tr>
									<td width="50%" align="left">
										<label><b><?php echo _PHONE_NUMBER ?></b></label>
									</td>
									<td width="50%" align="right">
										<input type="tel" name="phone"  value="<?php functions::xecho($line->phone); ?>"/>
									</td>
								</tr>
							</table>
						</div>
						<div class="row">
							<table>
								<tr>
									<td width="50%" align="left">
										<label><b><?php echo _EMAIL ?></b></label>
									</td>
									<td width="50%" align="right">
                                        <input type="email" name="mail"  value="<?php functions::xecho($line->email); ?>"/>
									</td>
								</tr>
							</table>
						</div>
						<div class="row">
							<table>
								<tr>
									<td width="50%" align="center" colspan='2'>
										<label><b><?php echo _ADDRESS ?></b></label>
									</td>
								</tr>
								<tr>
									<td width="50%" align="left">
										<label><b><?php echo _NUMBER ?></b></label>
									</td>
									<td width="50%" align="right">
										<input type="text" name="number"  value="<?php functions::xecho($line->address_num); ?>"/>
									</td>
								</tr>
								<tr>
									<td width="50%" align="left">
										<label><b><?php echo _STREET ?></b></label>
									</td>
									<td width="50%" align="right">
                                        <input type="text" name="street" value="<?php functions::xecho($line->address_street); ?>">
									</td>
								</tr>
								<tr>
									<td width="50%" align="left">
										<label><b><?php echo _COMPLEMENT ?></b></label>
									</td>
									<td width="50%" align="right">
                                        <input type="text" name="complement" value="<?php functions::xecho($line->address_complement); ?>">
									</td>
								</tr>
								<tr>
									<td width="50%" align="left">
										<label><b><?php echo _TOWN ?></b></label>
									</td>
									<td width="50%" align="right">
                                        <input type="text" name="town" value="<?php functions::xecho($line->address_town); ?>">
									</td>
								</tr>
								<tr>
									<td width="50%" align="left">
										<label><b><?php echo _POSTAL_CODE ?></b></label>
									</td>
									<td width="50%" align="right">
										<input type="text" name="postal_code" value="<?php functions::xecho($line->address_postal_code); ?>"/>
									</td>
								</tr>
								<tr>
									<td width="50%" align="left">
										<label><b><?php echo _COUNTRY ?></b></label>
									</td>
									<td width="50%" align="right">
										<input name="country" value="<?php functions::xecho($line->address_country); ?>"/>
									</td>
								</tr>
							</table>
						</div>
						<div class="row">
							<table>
								<tr>
									<td width="50%" align="left">
										<label><b><?php echo _OTHER ?></b></label>
									</td>
									<td width="50%" align="right">
/										<textarea name="other" rows="4"><?php functions::xecho($line->other_data); ?></textarea>
									</td>
								</tr>
							</table>
						</div>
					</fieldset>
					<input type="hidden" id="contact_id" name="contact_id" value="<?php functions::xecho($_REQUEST['contact_id']); ?>">
					<input type="submit" value="<?php echo _MODIFY ?>" class="whiteButton">
				</form>
			</div>
	<?php
	}
}
	?>