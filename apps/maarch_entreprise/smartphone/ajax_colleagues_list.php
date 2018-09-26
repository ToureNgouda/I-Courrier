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

$query = "SELECT lastname, firstname, phone, mail, user_id 
          FROM ".$_SESSION['tablename']['users']."
          WHERE (lower(lastname) like lower(?)  or lower(firstname) like lower(?))
          ORDER BY lastname, firstname";
$returnId = $db->query($query,array($_REQUEST['searchValue'].'%',$_REQUEST['searchValue'].'%'));


// $db->show();

//$returnId = $db->query($query);    

if (!$returnId) {
    $return['status'] = 0;
    $return['msg']    = 'fail';
    echo json_encode($return);
    exit;
}

$return['status']  = 1;
$return['msg']     = 'Recherche effectué';
$return['query'] = $query;
$return['searchValue'] = $_REQUEST['searchValue'];
while ($line = $returnId->fetchObject()) {
    if($line->user_id != $_SESSION['user']['UserId']) {
			$line->phone = str_replace(' ','',$line->phone);
			$return['rechercheColleague'] .= 
                                            '<div class="row">
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

echo json_encode($return);