<?php
if (file_exists('../../../core/init.php')) {
    include_once '../../../core/init.php';
}
if (!isset($_SESSION['config']['corepath'])) {
    header('location: ../../../');
}
require_once('core/class/class_functions.php');
require_once('core/class/class_core_tools.php');
require_once('modules/basket/class/class_modules_tools.php');
$core = new core_tools();
$core->test_user();
$core->load_lang();
$core->test_service('view_baskets', 'basket');
if (! isset($_REQUEST['noinit'])) {
    $_SESSION['current_basket'] = array();
}
$bask = new basket();
if (isset($_REQUEST['baskets']) && ! empty($_REQUEST['baskets'])) {
    $bask->load_current_basket(trim($_REQUEST['baskets']));
}
$_SESSION['collection_id_choice'] = $_SESSION['current_basket']['coll_id'];
$whereRequest = $_SESSION['current_basket']['clause'];
$basket_label = $_SESSION['current_basket']['label'];
$view = $_SESSION['current_basket']['view'];
if (
    isset($_SESSION['current_basket']['page_include'])
    && !empty($_SESSION['current_basket']['page_include'])
) {
    //$bask->show_array($_SESSION['current_basket']);
    include_once('apps/' . $_SESSION['config']['app_id']
        . '/smartphone/list_result.php'
    );
}
