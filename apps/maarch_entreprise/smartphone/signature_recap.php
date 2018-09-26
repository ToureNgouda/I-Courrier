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
$view = $sec->retrieve_view_from_coll_id($_SESSION['collection_id_choice']);
$s_id = $_REQUEST['id'];
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
<div id="signature_recap" title="<?php functions::xecho($subject);?>" class="panel" style="height:90%;"> 
    <?php
      // echo "<pre>".print_r($_SESSION['config'],true)."</pre>";
      $db = new Database();
      //$query = "SELECT * from res_view_attachments WHERE res_id_master = ? AND attachment_type IN ('signed_response') ORDER BY creation_date desc LIMIT 1";
      $where = "res_id_master = ? AND attachment_type IN ('signed_response')";
      $order = " ORDER by creation_date DESC";
      $query = $db->limit_select(0, 1, '*', 'res_view_attachments', $where, '', '', $order);
      
      $stmt = $db->query($query, array($_SESSION['doc_id']));
        //$_SESSION['tmpFilenameSign']
      echo '<table>';
      while($line = $stmt->fetchObject()){
        $objectId = $line->res_id;        
        echo '<tr>';
        echo '<td>'.$line->title.'</td><td><a href="'.$_SESSION['config']['businessappurl'].'index.php?display=true&module=attachments&page=view_attachment&res_id_master='.$_SESSION['doc_id'].'&id='.$objectId.'" target="_blank"/><i class="fa fa-eye"></i></a></td>';
        echo '<tr>';
      }
      //$paramsTab['viewDocumentLink'] = $_SESSION['config']['businessappurl'].'index.php?display=true&module=attachments&page=view_attachment&res_id_master='.$_SESSION['doc_id'];
      echo '</table>';
    ?>
    <img id="thum_sign" src="<?php  echo $_SESSION['tmpFilenameSign'];?>" style="width:20%;"/>
    <hr/>
    <span class="btn_recap" onclick="save_sign();" style="margin-left:10px;cursor:pointer;" id="linkSaveSign"><i class="fa fa-save"></i> Enregistrer signature</span>
    <span class="btn_recap"><i class="fa fa-ellipsis-v"></i> Détails signature</span>
    <span class="btn_recap"><a href="view_attachments.php?id=<?php echo $_SESSION['doc_id'];?>"><i class="fa fa-arrow-circle-right" aria-hidden="true"></i> Signer un autre courrier</a></span>
    <input type="hidden" id="current_basket" value="<?php echo $_SESSION['current_basket']['id']; ?>" />
    <div class="error">
        <?php
        if (isset($_SESSION['error'])) {
            functions::xecho($_SESSION['error']);
        }
        $_SESSION['error'] = '';
        ?>
    </div>
</div>
