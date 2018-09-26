<?php

/**
* Copyright Maarch since 2008 under licence GPLv3.
* See LICENCE.txt file at the root folder for more details.
* This file is part of Maarch software.
*
*/

$targetTab = $_REQUEST['targetTab'];
$res_id    = $_REQUEST['resId'];
$coll_id   = $_REQUEST['collId'];

require_once "modules" . DIRECTORY_SEPARATOR . "sendmail" . DIRECTORY_SEPARATOR . "class" . DIRECTORY_SEPARATOR . "class_modules_tools.php";

$sendmail_tools = new sendmail();

//Count mails
$nbr_emails = $sendmail_tools->countUserEmails($res_id, $coll_id);
if($nbr_emails == 0){
    $class       = 'nbResZero';
    $style2      = 'display:none;';
    $style       = '0.5';
    $styleDetail = '#9AA7AB';
}else{
    $class       = 'nbRes';
    $style       = '';
    $style2      = 'display:inherit;';
    $styleDetail = '#666';
}
if($_SESSION['req'] == 'details'){
    if($_REQUEST['origin'] == 'parent'){
        if($nbr_emails == 0 && strpos($_SERVER['HTTP_USER_AGENT'], 'Chrome')){
            $nav = 'sendmail_tab';
            $style2 = 'visibility:hidden;';

        }
        $js .= 'parent.$(\''.$targetTab.'\').style.color=\''.$styleDetail.'\';parent.$(\''.$targetTab.'_badge\').innerHTML = \'<span id="nb_'.$targetTab.'" style="'.$style2.'font-size: 10px;" class="'.$class.'">'.$nbr_emails.'</span>\'';

    }else {
        if($nbr_emails == 0 && strpos($_SERVER['HTTP_USER_AGENT'], 'Chrome')){
            $nav = 'sendmail_tab';
            $style2 = 'visibility:hidden;';

        }        
       $js .= '$(\''.$targetTab.'\').style.color=\''.$styleDetail.'\';$(\''.$targetTab.'_badge\').innerHTML = \'<span id="nb_'.$targetTab.'" style="'.$style2.'font-size: 10px;" class="'.$class.'">'.$nbr_emails.'</span>\'';

    }
}else{
    if($_REQUEST['origin'] == 'parent'){
        $js .= 'parent.$(\''.$targetTab.'_img\').style.opacity=\''.$style.'\';parent.$(\''.$targetTab.'_badge\').innerHTML = \'&nbsp;<sup><span id="nb_'.$targetTab.'" style="'.$style2.'" class="'.$class.'">'.$nbr_emails.'</span></sup>\'';

    } else {
       $js .= '$(\''.$targetTab.'_img\').style.opacity=\''.$style.'\';$(\''.$targetTab.'_badge\').innerHTML = \'&nbsp;<sup><span id="nb_'.$targetTab.'" style="'.$style2.'" class="'.$class.'">'.$nbr_emails.'</span></sup>\'';

    }
}

echo "{status : 0, nav : '".$nav."',content : '', error : '', exec_js : '".addslashes($js)."'}";
exit ();
