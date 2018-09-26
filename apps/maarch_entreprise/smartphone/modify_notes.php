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
require_once('core/class/class_history.php');
require_once('modules/notes/notes_tables.php');
require_once('core/core_tables.php');
require_once('apps/maarch_entreprise/apps_tables.php');

$core = new core_tools();
$core->load_lang();
if (isset($_REQUEST['oldtext']) && isset($_REQUEST['notes_id'])) { 
	$_REQUEST['oldtext'] = str_replace("<br>", "\n", $_REQUEST['oldtext']);?> 
	<div id="notes" title="Notes" class="panel">
		<form id="editnotes" title="EditNotes" class="panel" action="modify_notes.php" method="POST" selected="true">
			<fieldset>
				<h4>
					<?php
					echo functions::xssafe($_SESSION['user']['FirstName']) . ' '
						. functions::xssafe($_SESSION['user']['LastName'])
						. ' le ' . date('d/m/Y'); 
					?>
				</h4>
				<div class="row">
					<textarea cols="30" rows="3" name="newtext" style="width: 99%;"><?php functions::xecho($_REQUEST['oldtext']); ?></textarea>
					<input type="hidden" name="oldnote" value="<?php functions::xecho($_REQUEST['oldtext']);?>"/>
					<input type="hidden" name="notes_id" value="<?php functions::xecho($_REQUEST['notes_id']);?>"/>
				</div>
				<a type="submit"  href="#"><input class="whiteButton" type="submit" value="modifier"></a>
			</fieldset>
		</form>
	</div>
<?php
}

if (isset($_REQUEST['newtext']) && isset($_REQUEST['notes_id'])) {
	$_REQUEST['newtext'] = str_replace("\n", "<br>", $_REQUEST['newtext']);
	echo 'bonjour';
	$db = new Database();
	$date = $db->current_datetime();
	$query = " UPDATE " . NOTES_TABLE . " SET 
	note_text='" .$_REQUEST['newtext']. "'
	 , date_note=" .$date. "
	  WHERE note_text= ? AND id= ? ";
	if($db->query($query,array($_REQUEST['oldnote'],$_REQUEST['notes_id']))){
		//echo 'query ' . $query;
		$result = _NOTES_MODIFIED;
	} else {
		$result = _NOTES_NOT_MODIFIED;
	}
}
?>
<div id="<?php echo _MODIFY_NOTE;?>" title="<?php echo _MODIFY_NOTE;?>" class="panel">
	<fieldset>
		<h4 align="center">
			<?php
				echo $result;
			?>
		</h4>
	</fieldset>
	<a class="whiteButton" type="submit" href="index.php?page=welcome">Retour</a>
</div>


