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
$core = new core_tools();
$core->load_lang();
$db = new Database();

$stmt = $db->query(
    "SELECT u.lastname as lastname, 
            u.firstname as firstname, 
            notes.note_text, 
            notes.date_note as date_note, 
            notes.id as notes_id 
      FROM " . NOTES_TABLE . " notes, " . $_SESSION['tablename']['users'] ." u 
      WHERE notes.user_id = u.user_id 
      AND notes.identifier = ? 
      AND notes.coll_id = ? 
      ORDER BY notes.date_note DESC",array($_REQUEST['id'],$_REQUEST['collId']));
//$db->show();
$notesList = '<div id="newNote" class="row" style="display: none;"></div>';
if ($stmt->rowCount()< 1) {
    $notesList .= '<span id="noNotes">no notes</span>';
} else {
    while ($line = $stmt->fetchObject()) {
		$line->note_text = str_replace("\n", "<br>", $line->note_text);
        $notesList .= '<div class="row" id="'.functions::xssafe($line->notes_id).'">
        <div>' . functions::xssafe($line->firstname) . ' '
        . functions::xssafe($line->lastname) . ' le ' . functions::xssafe($line->date_note) . '</div>
		<table>
			<tr>
				<td>
					<form>
						<input type="button" onClick="delNotes('.functions::xssafe($line->notes_id).');" style="width: 100px;" value="' . _DELETE . '"><br>
					</form>
					<form class="panel" methode="POST" action="modify_notes.php" selected="true">
						<input type="hidden" name="oldtext" value="' . functions::xssafe($line->note_text) . '"/>
						<input type="hidden" name="notes_id" value="' . functions::xssafe($line->notes_id) . '"/>
						<a type="submit" href="#"><input type="button" style="width: 100px;" value="' . _MODIFY . '"></a>&nbsp;<br>
					</form>
				</td>
				<td align="left">
					' . functions::xssafe($line->note_text)
					. '&nbsp;&nbsp;
					
				</td>
			</tr>
		</table>
        </div>';
    }
}
?>

<div id="notes" title="Notes" class="panel">
    <fieldset>
        <h4>
            <?php
            echo functions::xssafe($_SESSION['user']['FirstName']) . ' '
                . functions::xssafe($_SESSION['user']['LastName'])
                . ' le ' . date('d/m/Y'); 
            ?>
        </h4>
        <div class="row">
            <textarea cols="30" rows="3" id="fieldNotes" name="fieldNotes"></textarea>
            <input type="hidden" id='collId' name="collId" value="<?php functions::xecho($_REQUEST['collId']);?>"/>
            <input type="hidden" id='id' name="id" value="<?php functions::xecho( $_REQUEST['id']);?>"/>
        </div>
    </fieldset>
	<div align="center">
		<input type="button" class="whiteButton" onclick="addNotes($('fieldNotes').value.replace('\n', '<br>'), $('collId').value, $('id').value);" value="<?php echo _ADD_NOTE;?>" />
	</div>
    <br/>
    <fieldset>
        <?php

        echo $notesList;
        ?>
    </fieldset>
</div>
