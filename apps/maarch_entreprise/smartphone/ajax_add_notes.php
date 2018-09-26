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

$db = new Database();
$sec = new security();
$table = $sec->retrieve_table_from_coll($_REQUEST['coll_id']);
$date = $db->current_datetime();
$query = "INSERT INTO " . NOTES_TABLE . "(identifier, note_text, date_note, user_id, coll_id, tablename) 
          VALUES(".$_REQUEST['id'] . ", '" . $_REQUEST['fieldNotes']. "', " . $date . ", '"
	. functions::xssafe($_SESSION['user']['UserId']) . "', '"
	. functions::xssafe($_REQUEST['coll_id']) . "', '"
	. functions::xssafe($table) . "')";

$returnId = $db->query($query);	

if (!$returnId) {
	$return['status'] = 0;
	$return['msg']    = 'fail';
	echo json_encode($return);
	exit;
}

$return['status']  = 1;
$return['msg']     = 'note ajoutée';
$return['newNote'] = '<div id="newNote" class="row" style="display: none;">';
$return['newNote'] .= '</div>';
$return['newNote'] .= '<div class="row">';
$return['newNote'] .= '<div>'.functions::xssafe($_SESSION['user']['FirstName']).' '.functions::xssafe($_SESSION['user']['LastName']).' le '.date('Y-m-d').'</div>';
$return['newNote'] .= '<table>
							<tr>
								<td>
									<form class="panel" methode="POST" action="delete_notes.php" selected="true">
										<input type="hidden" name="id" value="' . $line->notes_id . '"/>
										<a type="submit" href="#"><input type="button" style="width: 100px;" value="' . _DELETE . '"></a>&nbsp;<br>
									</form>
									<form class="panel" methode="POST" action="modify_notes.php" selected="true">
										<input type="hidden" name="oldtext" value="' . $_REQUEST['fieldNotes'] . '"/>
										<a type="submit" href="#"><input type="button" style="width: 100px;" value="' . _MODIFY . '"></a>&nbsp;<br>
									</form>
								</td>
								<td align="left">
									' . $_REQUEST['fieldNotes'] . '&nbsp;&nbsp;
								</td>
							</tr>
						</table>';
$return['newNote'] .= '</div>';
echo json_encode($return);