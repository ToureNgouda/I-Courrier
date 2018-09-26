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

$query = "SELECT contacts_v2.is_corporate_person, contacts_v2.society, contacts_v2.lastname, contacts_v2.firstname, 
          contact_addresses.phone, contact_addresses.email, contacts_v2.user_id, contacts_v2.contact_id 
          FROM ".APPS_CONTACTS_V2." 
          INNER JOIN ".APPS_CONTACTS_ADDRESSES." 
          ON contacts_v2.contact_id = contact_addresses.contact_id 
          WHERE (lower(contacts_v2.lastname) like lower(?)  or lower(contacts_v2.firstname) like lower(?) or lower(contacts_v2.society) like lower(?)) 
          AND contacts_v2.enabled = 'Y' AND contacts_v2.user_id = ? 
          ORDER BY contacts_v2.lastname, contacts_v2.society";
$returnId = $db->query($query,array($_REQUEST['searchValue'].'%',$_REQUEST['searchValue'].'%',$_REQUEST['searchValue'].'%',$_SESSION['user']['UserId']));


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
    if($line->is_corporate_person == "N") {
        $return['rechercheContact'] .=
                        '<div id="rechercheContact" class="row" style="display: none;">
                        </div>
                        <div class="row"  id="contacts">
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
        $return['rechercheContact'] .=
                        '<div id="rechercheContact" class="row" style="display: none;">
                        </div>
                        <div class="row"  id="contacts">
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

echo json_encode($return);