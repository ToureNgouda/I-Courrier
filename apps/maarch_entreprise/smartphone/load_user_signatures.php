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
require_once('core/core_tables.php');
require_once('apps/maarch_entreprise/apps_tables.php');
require_once('core/class/class_security.php');
require_once('core/class/class_history.php');
require_once('apps/' . $_SESSION['config']['app_id'] . '/class/class_types.php');

require_once('core/class/class_request.php');
require_once('core/class/class_resource.php');
require_once('core/class/docservers_controler.php');


if ($_SESSION['collection_id_choice'] == 'res_coll') {
    $catPhp = 'definition_mail_categories_invoices.php';
} else {
    $catPhp =    'definition_mail_categories.php';
}
if (file_exists(
    $_SESSION['config']['corepath'] . 'custom'. DIRECTORY_SEPARATOR
    . $_SESSION['custom_override_id'] . DIRECTORY_SEPARATOR . 'apps'
    . DIRECTORY_SEPARATOR . $_SESSION['config']['app_id'] . DIRECTORY_SEPARATOR
    . $catPhp
)
) {
    $path = $_SESSION['config']['corepath'] . 'custom'. DIRECTORY_SEPARATOR
          . $_SESSION['custom_override_id'] . DIRECTORY_SEPARATOR . 'apps'
          . DIRECTORY_SEPARATOR . $_SESSION['config']['app_id']
          . DIRECTORY_SEPARATOR . $catPhp;
} else {
    $path = 'apps' . DIRECTORY_SEPARATOR . $_SESSION['config']['app_id']
          . DIRECTORY_SEPARATOR . $catPhp;
}
include_once $path;
$core->load_lang();
$users = new history();
$sec = new security();
$type = new types();
$coll_id = $_SESSION['collection_id_choice'];
if (empty($coll_id)) $_SESSION['collection_id_choice'] = 'letterbox_coll';
$view = $sec->retrieve_view_from_coll_id($_SESSION['collection_id_choice']);
$s_id = $_REQUEST['id'];
$att_id = $_REQUEST['res_id_attach'];
$_SESSION['doc_id'] = $s_id;
//to change
$right = true;
if (isset($_SESSION['origin']) && $_SESSION['origin'] <> "basket") {
    $right = $sec->test_right_doc($coll_id, $s_id);
} else {
    $right = true;
}
if (!$right) {
    ?>
    <script type="text/javascript">
        window.top.location.href = "<?php  echo $_SESSION['config']['businessappurl'];?>index.php?page=no_right";
    </script>
    <?php
    exit();
}

$db = new Database();


$res_db = $db->query("SELECT * FROM " . $view . " WHERE res_id = ? ", array($s_id));

$res = $res_db->fetchObject();
$subject = $res->subject;
//echo "<pre>".print_r($_SESSION,true)."</pre>";
?>
<div id="load_user_signatures" title="<?php functions::xecho($subject);?>" class="panel" style="height:90%;"> 
    <?php
      /*echo "<pre>".print_r($_SESSION['user'],true)."</pre>";
      $db = new Database();
     
      echo '<table>';
      
      echo '</table>';*/
      $_SESSION['tab_copy_sign'] = array();
      foreach ($_SESSION['user']['pathToSignature'] as $key=>$sign) {
        $fileNameOnTmp = 'tmp_file_' . $_SESSION['user']['UserId']
                        . '_' . rand() . '.' . strtolower(pathinfo($sign,PATHINFO_EXTENSION));
        $filePathOnTmp = $_SESSION['config']['tmppath'] . $fileNameOnTmp;

        if (copy($sign, $filePathOnTmp)) {
          $_SESSION['tab_copy_sign'][$key] = $_SESSION['config']['businessappurl']. '/tmp/' . $fileNameOnTmp;
          echo '<a href="signature_main_panel.php?id='.$s_id.'&collId='.$_SESSION['collection_id_choice'].'&tableName='.$_SESSION['res_table'].'&keySign='.$key.'&res_id_attach='.$att_id.'">';

          echo '<img src="'.$_SESSION['config']['businessappurl']. '/tmp/' . $fileNameOnTmp.'" alt="signature" style="width:20%;margin:10px;float:left;border:1px solid black;cursor:pointer;" />';
          echo '</a>';

        } else {
            echo _COPY_ERROR;
        }       
      }
    ?>
    
    <div class="error">
        <?php
        if (isset($_SESSION['error'])) {
            functions::xecho($_SESSION['error']);
        }
        $_SESSION['error'] = '';
        ?>
    </div>
</div>
