<?php
require_once 'core' . DIRECTORY_SEPARATOR . 'class' . DIRECTORY_SEPARATOR . 'class_request.php';
require_once 'core' . DIRECTORY_SEPARATOR . 'class' . DIRECTORY_SEPARATOR . 'class_security.php';

$res_id = $_REQUEST["resId"];
$coll_id = $_REQUEST["collId"];

$security = new security();
$right = $security->test_right_doc($coll_id, $res_id);

if(!$right){
    exit(_NO_RIGHT_TXT);
}

$core_tools = new core_tools();
$core_tools->load_lang();
$core_tools->load_html();
$core_tools->load_header('', true, false);
$core_tools->load_js();

if ($core->test_service('view_doc_history', 'apps', false)) {
    $frm_str .= '<iframe src="'
            . $_SESSION['config']['businessappurl']
            . 'index.php?display=true&dir=indexing_searching&page=document_workflow_history&id='
            . $res_id . '&coll_id=' . $coll_id . '&load&size=medium" '
            . 'name="hist_wf_doc_process" id="hist_wf_doc_process" width="100%" height="500px" align="center" '
            . 'scrolling="auto" frameborder="0" style="height: 500px; max-height: 500px; overflow: scroll;"></iframe>';

    $frm_str .= '<br/>';
}
if ($core->test_service('view_full_history', 'apps', false)) {
    $frm_str .= '<span style="cursor: pointer;" onmouseover="this.style.cursor=\'pointer\';" onclick="new Effect.toggle(\'hist_doc_process\', \'blind\', {delay:0.2});whatIsTheDivStatus(\'hist_doc_process\', \'divStatus_all_history_div\');return false;">';
    if ($core->test_service('view_doc_history', 'apps', false)) {
        $frm_str .= '<span id="divStatus_all_history_div" style="color:#1C99C5;"><i class="fa fa-plus-square-o"></i></span>';
    } else {
        $frm_str .= '<span id="divStatus_all_history_div" style="color:#1C99C5;"><i class="fa fa-minus-square-o"></i></span>';
    }
    $frm_str .= '<b>&nbsp;' . _ALL_HISTORY . '</b>' . '</span>';
    $frm_str .= '<iframe src="'
            . $_SESSION['config']['businessappurl']
            . 'index.php?display=true&dir=indexing_searching&page=document_history&id='
            . $res_id . '&coll_id=' . $coll_id . '&load&size=medium" '
            . 'name="hist_doc_process" id="hist_doc_process" width="100%" height="650px" align="center" '
            . 'scrolling="auto" frameborder="0" style="height: 650px; max-height: 650px; overflow: scroll;';
    if ($core->test_service('view_doc_history', 'apps', false)) {
        $frm_str .= 'display: none" ></iframe>';
    } else {
        $frm_str .= '" ></iframe>';
    }
}

echo $frm_str;