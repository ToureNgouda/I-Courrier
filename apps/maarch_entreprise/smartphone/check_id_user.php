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

$sec->generateRaCode($_SESSION['user']['UserId'], '', false);

$res_db = $db->query("SELECT * FROM " . $view . " WHERE res_id = ? ", array($s_id));

$res = $res_db->fetchObject();
$subject = $res->subject;

$ra_code = $_SESSION['recup_user']['ra_code'];
//echo "<pre>".print_r($_SESSION,true)."</pre>";
?>
<div id="check_id_user" title="<?php functions::xecho($subject);?>" class="panel" style="height:90%;"> 
    <?php
    if (empty($_SESSION['error'])){
    ?>
    <fieldset>
        <div class="row">
          <p>Pour signer, veuillez saisir votre code d'accès (envoi par courriel à <?php functions::xecho($_SESSION['user']['Mail']);?>)</p>
          <label for="code_session">CODE</label>
          <input type="text" id="code_session" name="code_session" />
        </div>
        <div align="center">
          <input type="button" class="whiteButton" onclick="valid_sign(<?php echo functions::xecho($s_id);?>);" value="Valider la signature" />
        </div>
    </fieldset>
    <a href="signature_recap.php?id=<?php echo $s_id;?>&res_id_attach=<?php functions::xecho($att_id);?>" id="link_recap" style="display:none;" />
    <?php
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
