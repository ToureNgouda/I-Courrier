<?php
$targetTab = $_REQUEST['targetTab'];
$res_id = $_REQUEST['resId'];
$coll_id = $_REQUEST['collId'];

require_once 'modules/cases/class/class_modules_tools.php';

$cases = new cases();
$case_id = $cases->get_case_id($res_id);

if ($case_id == false){
    $class = 'nbResZero';
    $style2 = 'display:none;';
    $style = '0.5';
    $styleDetail = '#9AA7AB';
}
else{
    $class = 'nbRes';
    $style = '';
    $style2 = 'display:inherit;';
    $styleDetail = '#666';
}

if($_SESSION['req'] == 'details'){
    if($_REQUEST['origin'] == 'parent'){
        $js .= 'window.opener.parent.$(\''.$targetTab.'\').style.color=\''.$styleDetail.'\';';

    }else {

        if(strpos($_SERVER['HTTP_USER_AGENT'], 'Chrome')){
            $nav = 'cases_tab';
            $style2 = 'visibility:hidden;';
            $js .= '$(\''.$targetTab.'\').style.color=\''.$styleDetail.'\';$(\''.$targetTab.'_badge\').innerHTML = \'<span id="nb_'.$targetTab.'" style="'.$style2.'font-size: 10px;" class="'.$class.'">0</span>\'';

        }else{

            $js .= '$(\''.$targetTab.'\').style.color=\''.$styleDetail.'\';';

        }
    }
}else{
    if($_REQUEST['origin'] == 'parent'){
        $js .= 'window.opener.parent.$(\''.$targetTab.'_img\').style.opacity=\''.$style.'\';window.opener.parent.$(\''.$targetTab.'_badge\').innerHTML = \'&nbsp;<sup></sup>\'';

    }else {
       $js .= '$(\''.$targetTab.'_img\').style.opacity=\''.$style.'\';$(\''.$targetTab.'_badge\').innerHTML = \'&nbsp;<sup></sup>\'';

    }
}
      
echo "{status : 0, nav : '".$nav."',content : '', error : '', exec_js : '".addslashes($js)."'}";
exit ();