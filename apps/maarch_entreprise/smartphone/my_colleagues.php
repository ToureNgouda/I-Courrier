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
    "SELECT users.lastname as lastname, users.firstname as firstname, 
	users.phone as phone, users.mail as mail, users.user_id as user_id, 
	users.department as department FROM " .USERS_TABLE. " ORDER BY users.lastname
	");
//$db->show();
$notesList = '';
if ($stmt->rowCount() < 1) {
    $notesList = 'No colleagues';
} else {
    while ($line = $stmt->fetchObject()) {
		if($line->user_id != $_SESSION['user']['UserId']) {
			$line->phone = str_replace(' ','',$line->phone);
			$notesList .= '<div class="row">
							<a href="generic_profil_colleagues.php?
								lastname=' .functions::xssafe($line->lastname). '
								&firstname=' .functions::xssafe($line->firstname). '
								&mail=' .functions::xssafe($line->mail). '
								&phone=' .functions::xssafe($line->phone). '
								&user_id=' .functions::xssafe($line->user_id). '">
							
								<label>' 
									.functions::xssafe($line->lastname). ' '
									.functions::xssafe($line->firstname). '
								</label>
							</a>
							<table>
								<tr>
									<td align="left" width="70%"><a href="mailto:' .functions::xssafe($line->mail). '">' .functions::xssafe($line->mail). '</a></td>
									<td align="right"><a href="tel:' .functions::xssafe($line->phone). '">' .functions::xssafe($line->phone). '</a></td>
								</tr>
							</table>
							</div>
			';
		}
    }
}
?>
<div id="colleagues" title="<?php echo _MY_COLLEAGUES;?>" class="panel">
    <input name="colleague" type="search" id="colleague" placeholder="Rechercher" onKeyUp="searchColleagues($('colleague').value);"/>
    <br/><br/>
    <fieldset>
       <div id="allColleagues">
            <?php
                echo $notesList;
            ?>
        </div>
    </fieldset>
</div>
