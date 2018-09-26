<?php
/**
* File : details_cases.php
*
* Detailed informations on an selected cases
*
* @package  Maarch Entreprise 1.0
* @version 1.0
* @since 10/2005
* @license GPL
* @author  Loïc Vinet  <dev@maarch.org>
*/
$core_tools = new core_tools();
$core_tools->test_user();
$core_tools->load_lang();
$detailsExport = '';
require_once("core".DIRECTORY_SEPARATOR."class".DIRECTORY_SEPARATOR."class_request.php");
require_once("core".DIRECTORY_SEPARATOR."class".DIRECTORY_SEPARATOR."class_security.php");
require_once("apps".DIRECTORY_SEPARATOR.$_SESSION['config']['app_id'].DIRECTORY_SEPARATOR."class".DIRECTORY_SEPARATOR."class_list_show.php");
require_once("core".DIRECTORY_SEPARATOR."class".DIRECTORY_SEPARATOR."class_history.php");
$init = false;
if (isset($_REQUEST['reinit']) && $_REQUEST['reinit'] == "true") {
    $init = true;
}
$level = "";
if (isset($_REQUEST['level']) && ($_REQUEST['level'] == 2
    || $_REQUEST['level'] == 3 || $_REQUEST['level'] == 4
    || $_REQUEST['level'] == 1)
) {
    $level = $_REQUEST['level'];
}
$page_path = $_SESSION['config']['businessappurl']
           . 'index.php?page=details&dir=indexing_searching'
           . '&id=' . $_REQUEST['id'];
if (isset($_REQUEST['coll_id'])) {
    $page_path .= '&coll_id=' . $_REQUEST['coll_id'];
}
$page_label = _DETAILS_CASES;
$page_id = "details_cases";
$core_tools->manage_location_bar($page_path, $page_label, $page_id, $init, $level);

if (isset($_GET['id'])) {
    $_SESSION['cases']['actual_case_id'] = $_GET['id'];
}

?>
<div id="details_div" style="display:none;">
    <h1 class="titdetail">
        <i class="fa fa-info-circle fa-2x"></i>&nbsp;<?php echo _CASE_DETAIL." ".strtolower(_NUM);?>
        <?php functions::xecho($_SESSION['cases']['actual_case_id']);?> <span></span>
    </h1>
    <div id="inner_content" class="clearfix">
        <div class="whole-panel">
            <?php $detailsExport .= "<h1>"._DOCUMENTS_LIST_IN_THIS_CASE."</h1>";?>
            <div class="detailsCasesButton detailsCasesClicked" id="index-cases" onclick="tabClickedCases('index-cases','indexCases')"><?php echo _INDEX_CASES;?></div>
            <div class="detailsCasesButton" id="documents-list" onclick="tabClickedCases('documents-list','listDocument')"><?php echo _DOCUMENTS_LIST_IN_THIS_CASE;?></div>
            <div class="detailsCasesButton" id="notes-cases" onclick="tabClickedCases('notes-cases','listNotes')"><?php echo _NOTES_FOR_THIS_CASES;?></div>
            <div class="detailsCasesButton" id="history-cases" onclick="tabClickedCases('history-cases','historyCases')"><?php echo _HISTORY_CASES;?></div>

            <div class="detailsCasesIframe" id="indexCases">
                <iframe  name="index_cases" src="index.php?display=true&module=cases&page=detail_index_cases" frameborder="0" width="100%" height="520px"></iframe>
            </div>
            <div class="detailsCasesIframe" id="listDocument" style="display : none;"> 
                <h2><?php echo _DOCUMENTS_LIST_IN_THIS_CASE;?></h2>
                <iframe  name="list_document"  src="index.php?display=true&module=cases&page=cases_documents_list" frameborder="0" width="100%" height="520px"></iframe>
            </div>
            <div class="detailsCasesIframe" id="listNotes" style="display : none;">
                    <h2><?php echo _NOTES_FOR_THIS_CASES;?></h2>
                    <iframe  name="list_notes"  src="index.php?display=true&module=cases&page=cases_notes_list&size=full" frameborder="0" width="100%" height="520px"></iframe>
            </div>
            <div class="detailsCasesIframe" id="historyCases" style="display : none;">
                    <h2><?php echo _HISTORY_CASES;?></h2>
                    <iframe  name="history_cases"  src="index.php?display=true&module=cases&page=cases_history_list" frameborder="1" width="100%" height="520px"></iframe>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
 var item  = $('details_div');
  if(item)
    {
     item.style.display='block';
    }
</script>
