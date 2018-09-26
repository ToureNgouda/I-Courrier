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


$res_db = $db->query("SELECT * FROM " . $view . " WHERE res_id = ? ", array($s_id));

$res = $res_db->fetchObject();
$subject = $res->subject;
//echo "<pre>".print_r($_SESSION,true)."</pre>";
?>
<div id="sign_main_panel" title="<?php functions::xecho($subject);?>" class="panel" style="height:100%;"> 
    <input type="hidden" value="<?php functions::xecho($s_id)?>" id="res_id_master" name="res_id_master" />
    <input type="hidden" value="<?php functions::xecho($att_id)?>" id="res_id_attach" name="res_id_attach" />
    <p id="info_landscape">Passez en mode paysage</p>
    <div id="signature-pad" class="m-signature-pad" >
        <div class="m-signature-pad--left">
        <!--<?php
        if (count($_SESSION['user']['pathToSignature']) > 0){
        ?>
            <span class="action_but_sign chooseSignBut" data-action="chooseSignBut"><a href="load_user_signatures.php?id=<?php functions::xecho($s_id);?>&res_id_attach=<?php functions::xecho($att_id);?>"><i class="fa fa-bars fa-3x" aria-hidden="true"></i></a></span>
        <?php
        }
        ?>-->
            
            <div id="list_tnl_sign">
            <?php
                if (count($_SESSION['user']['pathToSignature']) > 0){
                    foreach ($_SESSION['user']['pathToSignature'] as $key=>$sign) {
                        $fileNameOnTmp = 'tmp_file_' . $_SESSION['user']['UserId']
                                        . '_' . rand() . '.' . strtolower(pathinfo($sign,PATHINFO_EXTENSION));
                        $filePathOnTmp = $_SESSION['config']['tmppath'] . $fileNameOnTmp;

                        if (copy($sign, $filePathOnTmp)) {
                            $_SESSION['tab_copy_sign'][$key] = $_SESSION['config']['businessappurl']. '/tmp/' . $fileNameOnTmp;
                            echo '<img src="'.$_SESSION['config']['businessappurl']. '/tmp/' . $fileNameOnTmp.'" alt="signature" style="width:99px;background:#FFF;" onclick="loadImgSign(this);"/>';
                        }
                    }
                }
            ?>
            </div>
        </div>
        
        <div class="m-signature-pad--body">
          <canvas id="canvasSign"></canvas>
        </div>
        <div class="m-signature-pad--footer">
          <!--<button class="action_but_sign redPen" data-action="redPen">Rouge</button>
          <button class="action_but_sign blackPen" data-action="blackPen">Noir</button>
          <button class="action_but_sign clearBut" data-action="clearBut">Effacer</button>
          <button class="action_but_sign addBut" data-action="addBut">Ajouter</button>
           <button class="saveBut" data-action="saveBut">Signer</button> -->

           <span class="action_but_sign colRed colorBut" data-action="redPen"></span>
           <span class="action_but_sign colBlue colorBut" data-action="bluePen"></span>
           <span class="action_but_sign colGreen colorBut" data-action="greenPen"></span>
           <span class="action_but_sign colBlack colorBut selected_but" data-action="blackPen"></span> | 
           <span class="action_but_sign smallPen colBlack sizeBut" data-action="smallPen"></span>
           <span class="action_but_sign midPen colBlack sizeBut selected_but" data-action="midPen"></span>
           <span class="action_but_sign bigPen colBlack sizeBut" data-action="bigPen"></span><!-- |
           <span class="action_but_sign stampBut" data-action="stampBut" id="stampBut"><i class="fa fa-certificate fa-2x" aria-hidden="true"></i></span>-->
           <span class="action_but_sign clearBut disabled_but" data-action="clearBut" id="clearBut"><i class="fa fa-eraser fa-2x" aria-hidden="true"></i>Effacer</span>
           <!-- <span class="action_but_sign addBut" data-action="addBut"><i class="fa fa-bookmark fa-2x" aria-hidden="true"></i>Ajouter</span> -->
          <span class="action_but_sign saveBut disabled_but" data-action="saveBut" id="saveBut"><i class="fa fa-hand-o-up fa-2x" aria-hidden="true"></i>Signer <span id="loading_sign" style="display:none;"><i class="fa fa-spinner fa-pulse fa-2x fa-fw"></i></span></span>
        </div>
    </div>  

    <?php 
    
    /*if (isset($_GET['keySign'])){
        $sign = $_SESSION['tab_copy_sign'][$_GET['keySign']];

        ?>
        <img src="<?php echo $sign; ?>" id="myLoadedSign" style="display:none;" />
       
        <!--<script type="text/javascript" >
            var canvas = document.getElementById('canvasSign');
            var ctx = canvas.getContext('2d');
            var img = new Image();
            img.src = <?php echo "'".$_SESSION['user']['pathToSignature'][$_GET['keySign']]."'"; ?>;
            img.addEventListener('load', function() {
                ctx.drawImage(img, 0, 0);
            });
            
        </script>-->
        <?
    }*/
    ?>
    <a href="signature_recap.php?id=<?php functions::xecho($s_id);?>&res_id_attach=<?php functions::xecho($att_id);?>" id="link_recap" style="display:none;" />
    <a href="check_id_user.php?id=<?php functions::xecho($s_id);?>&res_id_attach=<?php functions::xecho($att_id);?>" id="link_check_user" style="display:none;" />
    <div class="error">
        <?php
        if (isset($_SESSION['error'])) {
            functions::xecho($_SESSION['error']);
        }
        $_SESSION['error'] = '';
        ?>
    </div>
</div>
