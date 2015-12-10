<?php

/*
*   Copyright 2008-2015 Maarch
*
*   This file is part of Maarch Framework.
*
*   Maarch Framework is free software: you can redistribute it and/or modify
*   it under the terms of the GNU General Public License as published by
*   the Free Software Foundation, either version 3 of the License, or
*   (at your option) any later version.
*
*   Maarch Framework is distributed in the hope that it will be useful,
*   but WITHOUT ANY WARRANTY; without even the implied warranty of
*   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*   GNU General Public License for more details.
*
*   You should have received a copy of the GNU General Public License
*   along with Maarch Framework.  If not, see <http://www.gnu.org/licenses/>.
*/

require_once "core/class/class_security.php";
require_once "core/class/class_request.php";
require_once "core/class/class_resource.php";
require_once "core/class/class_history.php";
require_once 'modules/attachments/attachments_tables.php';
require_once 'modules/attachments/class/attachments_controler.php';
$core = new core_tools();
$core->load_lang();

$func = new functions();
$ac = new attachments_controler();

$db = new Database();

if ($_REQUEST['relation'] == 1) {
    $stmt = $db->query("UPDATE " . RES_ATTACHMENTS_TABLE . " SET status = 'DEL' WHERE res_id = ?", array($_REQUEST['id']) );
	$pdf_id = $ac->getCorrespondingPdf($_REQUEST['id']);
	if (isset($pdf_id) && $pdf_id != 0) $stmt = $db->query("UPDATE " . RES_ATTACHMENTS_TABLE . " SET status = 'DEL' WHERE res_id = ?", array($pdf_id));
	$document_id = $ac->getCorrespondingDocument($_REQUEST['id']);
	if (isset($document_id) && $document_id != 0) $stmt = $db->query("UPDATE " . RES_ATTACHMENTS_TABLE . " SET status = 'A_TRA' WHERE res_id = ?", array($document_id));
	
} else {
    $stmt = $db->query("SELECT attachment_id_master FROM res_version_attachments WHERE res_id = ?", array($_REQUEST['id']) );
    $res=$stmt->fetchObject();
    $stmt = $db->query("UPDATE res_version_attachments SET status = 'DEL' WHERE attachment_id_master = ?", array($res->attachment_id_master) );
    $stmt = $db->query("UPDATE res_attachments SET status = 'DEL' WHERE res_id = ?", array($res->attachment_id_master));
	
	$pdf_id = $ac->getCorrespondingPdf($_REQUEST['id']);
	if (isset($pdf_id) && $pdf_id != 0) $stmt = $db->query("UPDATE " . RES_ATTACHMENTS_TABLE . " SET status = 'DEL' WHERE res_id = ?", array($pdf_id));
	$document_id = $ac->getCorrespondingDocument($_REQUEST['id']);
	if (isset($document_id) && $document_id != 0) $stmt = $db->query("UPDATE " . RES_ATTACHMENTS_TABLE . " SET status = 'A_TRA' WHERE res_id = ?", array($document_id));
}

if ($_SESSION['history']['attachdel'] == "true") {
    $hist = new history();
    if (! isset($_SESSION['collection_id_choice'])
        || empty($_SESSION['collection_id_choice'])
    ) {
        $_SESSION['collection_id_choice'] = $_SESSION['user']['collections'][0];
    }
    $sec = new security();
    $view = $sec->retrieve_view_from_coll_id($_SESSION['collection_id_choice']);
    if ($_REQUEST['relation'] == 1) {
        $stmt = $db->query("SELECT res_id_master FROM " . RES_ATTACHMENTS_TABLE . " WHERE res_id = ?", array($_REQUEST['id']));
    } else {
        $stmt = $db->query("SELECT res_id_master FROM res_version_attachments WHERE res_id = ?", array($_REQUEST['id']));
    }

    $res = $stmt->fetchObject();
    $resIdMaster = $res->res_id_master;
    $hist->add(
        $view, $resIdMaster, "DEL", 'attachdel', _ATTACH_DELETED . ' ' . _ON_DOC_NUM
        . $resIdMaster . "  (" . $_REQUEST['id'] . ')',
        $_SESSION['config']['databasetype'], "attachments"
    );
    $hist->add(
        RES_ATTACHMENTS_TABLE, $_REQUEST['id'], "DEL", 'attachdel', _ATTACH_DELETED . " : "
        . $_REQUEST['id'], $_SESSION['config']['databasetype'], "attachments"
    );
}


if ($_REQUEST['relation'] == 1) {
    $stmt = $db->query("SELECT res_id_master FROM " . RES_ATTACHMENTS_TABLE . " WHERE res_id = ?",array($_REQUEST['id']));
} else {
    $stmt = $db->query("SELECT res_id_master FROM res_version_attachments WHERE res_id = ?", array($_REQUEST['id']));
}

$res = $stmt->fetchObject();
$resIdMaster = $res->res_id_master;
$query = "SELECT title FROM res_view_attachments WHERE status <> 'DEL' and status <> 'OBS' and res_id_master = ?";
    if (isset($_REQUEST['fromDetail']) && $_REQUEST['fromDetail'] == 'attachments') {
        $query .= " and (attachment_type <> 'response_project' and attachment_type <> 'outgoing_mail_signed' and attachment_type <> 'converted_pdf' and attachment_type <> 'print_folder')";
    } else if (isset($_REQUEST['fromDetail']) && $_REQUEST['fromDetail'] == 'response'){
        $query .= " and (attachment_type = 'response_project' or attachment_type = 'outgoing_mail_signed' or attachment_type = 'outgoing_mail')";
    }
	else{
		$query .= " and (attachment_type <> 'converted_pdf' and attachment_type <> 'print_folder')";
	}
$stmt = $db->query($query, array($resIdMaster));
if ($stmt->rowCount() > 0) {
    $new_nb_attach = $stmt->rowCount();
} else {
    $new_nb_attach = 0;
}
?>
<script type="text/javascript">

	if (window.parent.top.document.getElementById('cur_resId')){
		function get_num_rep(res_id){
			trig_elements = window.parent.top.document.getElementsByClassName('trig');
			for (i=0; i<trig_elements.length; i++){
				var id = trig_elements[i].id;
				var splitted_id = id.split("_");
				if (splitted_id.length == 3 && splitted_id[0] == 'ans' && splitted_id[2] == res_id) return splitted_id[1];
			}
			return 0;
		}
		
		var res_id_doc = <?php functions::xecho($_REQUEST['id']); ?>;
		var num_rep = get_num_rep(res_id_doc);
		
		if(window.parent.top.document.getElementById('ans_'+num_rep+'_'+res_id_doc)) {
			var tab = window.parent.top.document.getElementById('tabricatorRight');
			tab.removeChild(window.parent.top.document.getElementById('ans_'+num_rep+'_'+res_id_doc));
		}
		if(window.parent.top.document.getElementById('content_'+num_rep+'_'+res_id_doc)) {
			var tab = window.parent.top.document.getElementById('tabricatorRight');
			tab.removeChild(window.parent.top.document.getElementById('content_'+num_rep+'_'+res_id_doc));
		}
		
	}
	
	var eleframe1 =  window.top.document.getElementsByName('list_attach');
	var nb_attach = '<?php functions::xecho($new_nb_attach);?>';
	<?php if (isset($_REQUEST['fromDetail']) && $_REQUEST['fromDetail'] == 'attachments') { ?>
		eleframe1[0].src = "<?php echo $_SESSION['config']['businessappurl'].'index.php?display=true&module=attachments&page=frame_list_attachments&load&attach_type_exclude=response_project,outgoing_mail_signed,converted_pdf,print_folder&fromDetail=attachments';?>";
		window.parent.top.document.getElementById('nb_attach').innerHTML = " ("+nb_attach+")";
	<?php } else if (isset($_REQUEST['fromDetail']) && $_REQUEST['fromDetail'] == 'response'){ ?>
		eleframe1[1].src = "<?php echo $_SESSION['config']['businessappurl'].'index.php?display=true&module=attachments&page=frame_list_attachments&load&attach_type=response_project,outgoing_mail_signed,outgoing_mail&fromDetail=response';?>";
		window.parent.top.document.getElementById('answer_number').innerHTML = " ("+nb_attach+")";
	<?php } else { ?>
		eleframe1[0].src = "<?php echo $_SESSION['config']['businessappurl'].'index.php?display=true&module=attachments&page=frame_list_attachments&load&attach_type_exclude=converted_pdf,print_folder';?>";
		window.parent.top.document.getElementById('nb_attach').innerHTML = nb_attach;
	<?php } ?>
    // window.parent.top.document.getElementById('nb_attach').innerHTML = nb_attach;

</script>
