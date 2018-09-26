<?php
$targetTab = $_REQUEST['targetTab'];
$res_id = $_REQUEST['resId'];
$coll_id = $_REQUEST['collId'];

require_once 'modules/notes/class/class_modules_tools.php';
$notes_tools    = new notes();
//Count notes
$nbr_notes = $notes_tools->countUserNotes($res_id, $coll_id);
if ($nbr_notes == 0){
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
    
    if($nbr_notes == 0 && strpos($_SERVER['HTTP_USER_AGENT'], 'Chrome')){
            $nav = 'notes_tab';
            $style2 = 'visibility:hidden;';

        }

    if($_REQUEST['origin'] == 'parent'){
        $js .= 'parent.$(\''.$targetTab.'\').style.color=\''.$styleDetail.'\';parent.$(\''.$targetTab.'_badge\').innerHTML = \'<span id="nb_'.$targetTab.'" style="'.$style2.'font-size: 10px;" class="'.$class.'">'.$nbr_notes.'</span>\'';

    }else {
        
       $js .= '$(\''.$targetTab.'\').style.color=\''.$styleDetail.'\';$(\''.$targetTab.'_badge\').innerHTML = \'<span id="nb_'.$targetTab.'" style="'.$style2.'font-size: 10px;" class="'.$class.'">'.$nbr_notes.'</span>\'';

    }
}else{
    if($_REQUEST['origin'] == 'parent'){
        $js .= 'parent.$(\''.$targetTab.'_img\').style.opacity=\''.$style.'\';parent.$(\''.$targetTab.'_badge\').innerHTML = \'&nbsp;<sup><span id="nb_'.$targetTab.'" style="'.$style2.'" class="'.$class.'">'.$nbr_notes.'</span></sup>\'';

    }else {
       $js .= '$(\''.$targetTab.'_img\').style.opacity=\''.$style.'\';$(\''.$targetTab.'_badge\').innerHTML = \'&nbsp;<sup><span id="nb_'.$targetTab.'" style="'.$style2.'" class="'.$class.'">'.$nbr_notes.'</span></sup>\'';

    }
}
      
//echo '{status : 0, nav : "'.$nav.'",content : "", error : "", exec_js : "'.addslashes($js).'"}';
//echo "{status : 0, nav : '".$nav."',content : '', error : '', exec_js : '".addslashes($js)."'}";
//echo '{"status" : 0, "nav" : "'.$nav.'","content" : "", "error" : "", "exec_js" : "'.addslashes($js).'"}';
echo "{status : 0, nav : '".$nav."',content : '', error : '', exec_js : '".addslashes($js)."'}";
exit ();