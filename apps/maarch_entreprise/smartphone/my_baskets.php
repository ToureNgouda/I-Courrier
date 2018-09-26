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
$core = new core_tools();
$core->load_lang();
$core_tools = new core_tools();
$core_tools->test_user();

if (!isset($_REQUEST['noinit']))
{
    $_SESSION['current_basket'] = array();
}
require_once('modules' . DIRECTORY_SEPARATOR . 'basket' . DIRECTORY_SEPARATOR . 'class' . DIRECTORY_SEPARATOR . 'class_modules_tools.php');
/************/
$bask = new basket();
$db = new Database();
?>
<?php 
if ($core_tools->test_service('display_basket_list', 'basket', false)) {
    if (isset($_SESSION['user']['baskets']) && count($_SESSION['user']['baskets']) > 0) {
        ?>
        <ul class="basket_elem" selected="true">
        <?php
        $abs_basket = false;
        for ($i=0;$i<count($_SESSION['user']['baskets']);$i++) {
            if ($_SESSION['user']['baskets'][$i]['abs_basket'] == true && !$abs_basket) {
                echo '</ul><h3>'._OTHER_BASKETS.' :</h3><ul class="basket_elem">';
                $abs_basket = true;
            }
            if($_SESSION['user']['baskets'][$i]['is_visible'] === 'Y') {
                $nb = '';
                if (preg_match('/^CopyMailBasket/', $_SESSION['user']['baskets'][$i]['id']) && !empty($_SESSION['user']['baskets'][$i]['view'])) {
                    $stmt = $db->query('select res_id from '.$_SESSION['user']['baskets'][$i]['view']." where ".$_SESSION['user']['baskets'][$i]['clause']);
                    if($stmt != false) $nb = $stmt->rowCount();
                }
                elseif (!empty($_SESSION['user']['baskets'][$i]['table'])) {
                    if ( trim($_SESSION['user']['baskets'][$i]['clause']) <> '' && $_SESSION['user']['baskets'][$i]['view']!='view_folders') {
                        $stmt = $db->query('select res_id from '.$_SESSION['user']['baskets'][$i]['view']." where ".$_SESSION['user']['baskets'][$i]['clause'], null, true);
                        if($stmt != false) $nb = $stmt->rowCount();
                    }
                    else{
                        $stmt = $db->query('select folder_id from '.$_SESSION['user']['baskets'][$i]['view']." where ".$_SESSION['user']['baskets'][$i]['clause'], null, true);
                        if($stmt != false) $nb = $stmt->rowCount();
                    }
                }
                if (!preg_match('/^IndexingBasket/', $_SESSION['user']['baskets'][$i]['id'])) {
                    echo '<li><a href="view_baskets.php?baskets='.$_SESSION['user']['baskets'][$i]['id'].'"><span class="fa fa-tasks"></span>'.$_SESSION['user']['baskets'][$i]['name'].' <span class="bubble">'.$nb.'</span> </a></li>';
                }
            }
        }
        ?>
        </ul>
        <?php
    }
}
?>
