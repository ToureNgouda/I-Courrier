<?php
/**
 *​ ​Copyright​ ​Maarch​ since ​2008​ under licence ​GPLv3.
 *​ ​See​ LICENCE​.​txt file at the root folder ​for​ more details.
 *​ ​This​ file ​is​ part of ​Maarch​ software.
 *
 */


$core = new core_tools();
$core->test_user();
$core->load_js();
$db = new Database();

// Get informations from config.xml
$multiple_res_id = $_SESSION['modules_loaded']['attachments']['reconciliation']['multiple_res_id'];
$attach_to_empty = $_SESSION['modules_loaded']['attachments']['reconciliation']['attach_to_empty'];
$attachmentTable = $_SESSION['tablename']['reconciliation']['attachment'];
$letterboxTable = $_SESSION['tablename']['reconciliation']['letterbox'];
$contactsV2Table = $_SESSION['tablename']['reconciliation']['contacts_v2'];
$contactsAddressesTable = $_SESSION['tablename']['reconciliation']['contact_addresses'];

// If no documents are choosen, force the user to modify his choice by going back in the modal window
// If multiple documents are choose, the user have to enter manually the chrono number, the title and the recipient
// If the possiblity to choose multiple document is 'false', force the user to modify his choice by going back in the modal window
if(count($_GET['field']) == 0){
    ?>
    <script type="text/javascript">
        historyBack("<?php echo _ERROR_EMPTY_RES_ID ;?>");
    </script>
    <?php
    exit();
}else if(count($_GET['field']) > 1 && $multiple_res_id == 'false'){
    ?>
    <script type="text/javascript">
        historyBack("<?php echo _MULTIPLE_RES_ID_ERROR ;?>")
    </script>
    <?php
    exit();
}

// Check if at least one of the choosen documents isn't the current document
for ($i = 0; $i < count($_GET['field']); $i++){
    if($_GET['field'][$i] == $_SESSION['doc_id']){
        ?>
        <script type="text/javascript">
            historyBack("<?php echo _SAME_RES_ID_ERROR;?>");
        </script>
        <?php
        exit();
    }
}

// Get the informations of the current document in case there is more than one response project
$defaultInfos = \Resource\models\ResModel::getById(['resId' => $_SESSION['doc_id'], 'select' => ['subject']]);

//If there is one res_id, we get the recipient information, the chrono number and the title
if(count($_GET['field']) == 1){

    // Check if there is a response project and retrieve the infos about it
    $queryProjectResponse = \Attachment\models\AttachmentModel::getOnView([
        'select'    =>  ['identifier, title, dest_contact_id, dest_address_id'],
        'where'     =>  ["res_id_master = (?) AND attachment_type = 'response_project' AND status <> 'DEL'"],
        'data'      =>  [$_GET['field']]
    ]);

    // Get the informations from res_view_letterbox, in order to get the contact infos if there is no project response
    $queryResViewLetterbox = \Resource\models\ResModel::getOnView([
        'select'    =>  ['contact_id, address_id'],
        'where'     =>  ['res_id = (?)'],
        'data'      =>  [$_GET['field']]
    ]);

    // If the selected document doesn't have a response project and attach_to_empty parameter is false, send the user back in the modal to select a document with a response project
    if (!$queryProjectResponse && $attach_to_empty == 'true') {   // If there is no project response and attach_to_empty parameters is true, generate a chrono number and get the contact from parent document
    ?>
        <script>
            var defaultTitle = <?php echo json_encode($defaultInfos[0]['subject']);?>;
            var contactid = <?php echo json_encode($queryResViewLetterbox[0]['contact_id'])?>;
            var addressid = <?php echo json_encode($queryResViewLetterbox[0]['address_id'])?>;
            var placeholder = "<?php echo _PLEASE_GENERATE_CHRONO_NUMBER; ?>";
            var res_id = <?php echo json_encode($_GET['field']);?>;
            displayInfos(false, defaultTitle, contactid, addressid, placeholder, false, false, res_id);
        </script>
        <?php
    }else if (!$queryProjectResponse && $attach_to_empty == 'false') {  // If there is no response project and the attach_to_empty parameters is set at false
        ?>
        <script type="text/javascript">
            historyBack("<?php echo _ATTACH_TO_EMPTY_ERROR;?>");
        </script>
        <?php
        exit();
    } else if(count($queryProjectResponse) > 1){    // If there is more than one response project --> list all the chrono number with linked contact informations
	    $i = 0;
	    // Create a select to list all the project response
	    $str = '<select id="listProjectResponse" name="chrono_number_list" onchange="fillHiddenInput(this.options[this.selectedIndex].value)">';
	    $str .= "<option value=''>" . _CHOOSE_CHRONO_NUMBER . "</option>";
	    foreach($queryProjectResponse as $projectResponseInfo){
	        // This array is use to fill an hidden input, in order to choose the contact informations automatically depending to the chrono number choosen via the <select>
            $tab[$i] = $projectResponseInfo['identifier'] . "#" . $projectResponseInfo['dest_contact_id'] . "#" . $projectResponseInfo['dest_address_id'] . "#" . $projectResponseInfo['title'];
            $i++;

            // Create the select with all the chrono number
            $str .= "<option value='" . $projectResponseInfo['identifier'] . "'>" . $projectResponseInfo['identifier'] ." - " . $projectResponseInfo['title'] . "</option>";
        }
        $str .= '</select>';
        ?>
	    <script>
            var defaultTitle = <?php echo json_encode($defaultInfos -> subject);?>;
            var placeholder = "<?php echo _PLEASE_GENERATE_CHRONO_NUMBER; ?>";
            var listChronoNumber = <?php echo json_encode($str) ;?>;
            var hiddenData = <?php echo json_encode($tab); ?>;
            var res_id = <?php echo json_encode($_GET['field']);?>;
            displayInfos(false, defaultTitle, false, false, placeholder, listChronoNumber, hiddenData,res_id);
        </script>
        <?php

    } else if(count($queryProjectResponse) == 1){ // If there is one response project, we get the informations of this one
        foreach($queryProjectResponse as $projectResponseInfo){
	        $identifier = $projectResponseInfo['identifier'];
	        $title = $projectResponseInfo['title'];
	        $contactId = $projectResponseInfo['dest_contact_id'];   // Get the contact info
	        $addressId = $projectResponseInfo['dest_address_id'];
        }
        ?>
	    <script>
            var chronoNumber = <?php echo json_encode($identifier);?>;
            var defaultTitle = <?php echo json_encode($title);?>;
            var contactid = <?php echo json_encode($contactId)?>;
            var addressid = <?php echo json_encode($addressId)?>;
            var res_id = <?php echo json_encode($_GET['field']);?>;
            displayInfos(chronoNumber, defaultTitle, contactid, addressid, false, false, false,res_id);
        </script>
        <?php
    }
}else{  // If there is more than one document selected --> list all the chrono number with linked contact informations
    // Get all the response project and create the select
	$j = 0;
	$str = '<select id="listProjectResponse" name="chrono_number_list" onchange="fillHiddenInput(this.options[this.selectedIndex].value)">';
	$str .= "<option value=''>" . _CHOOSE_CHRONO_NUMBER . "</option>";
    for($i = 0; $i< count($_GET['field']); $i++){
        $queryAllProjectReponse = \Attachment\models\AttachmentModel::getOnView([
            'select'    =>  ['title,identifier, dest_contact_id, dest_address_id'],
            'where'     =>  ["res_id_master = (?) AND attachment_type = 'response_project'"],
            'data'      =>  [$_GET['field'][$i]]
        ]);


        // Check if one of the selected document own a response projet, if attach_to_empty parameter is false
	    if($attach_to_empty == 'false' && !$queryAllProjectReponse){
            ?>
            <script type="text/javascript">
                historyBack("<?php echo _ATTACH_TO_EMPTY_ERROR;?>");
            </script>
            <?php
            exit();
	    }

	    // Create a select to list all the project response
	    foreach($queryAllProjectReponse as $projectResponseInfo){
		    // This array is use to fill an hidden input, in order to choose the contact informations automatically depending to the chrono number choosen via the <select>
		    $tab[$j] = $projectResponseInfo['identifier'] . "#" . $projectResponseInfo['dest_contact_id'] . "#" . $projectResponseInfo['dest_address_id'] . "#" . $projectResponseInfo['title'];
		    $j++;

		    // Create the select with all the chrono number
		    $str .= "<option value='" . $projectResponseInfo['identifier'] . "'>" . $projectResponseInfo['identifier'] ." - " . $projectResponseInfo['title'] . "</option>";
	    }
    }
	$str .= '</select>';
	?>
        <script>
            var defaultTitle = <?php echo json_encode($defaultInfos -> subject);?>;
            var placeholder = "<?php echo _PLEASE_GENERATE_CHRONO_NUMBER; ?>";
            var listChronoNumber = <?php echo json_encode($str) ;?>;
            var hiddenData = <?php echo json_encode($tab); ?>;
            var res_id = <?php echo json_encode($_GET['field']);?>;
            displayInfos(false, defaultTitle, false, false, placeholder, listChronoNumber, hiddenData,res_id);
        </script>
    <?php
}?>