<?php
/**
* File : load_diffusiontype_formcontent
*
* Script called by an ajax object to process the diffusion type change during
*
* @package  Maarch Entreprise Notifiation Modules
* @version 1.3
* @since 10/2005
* @license GPL v3
* @author Loïc Vinet  <dev@maarch.org>
*/

require_once 'modules' . DIRECTORY_SEPARATOR . 'notifications' . DIRECTORY_SEPARATOR
    . 'class' . DIRECTORY_SEPARATOR . 'diffusion_type_controler.php';




if ((! isset($_REQUEST['id_type']) || empty($_REQUEST['id_type']))) {
        $_SESSION['error'] = _TYPE_EMPTY;
    
    echo "{status : 1, error_txt : '" . addslashes($_SESSION['error']) . "'}";
    exit();
}

if (empty($_REQUEST['origin'])) {
    $_SESSION['error'] = _ORIGIN . ' ' . _UNKNOWN;
    echo "{status : 2, error_txt : '" . addslashes($_SESSION['error']) . "'}";
    exit();
}
//--------------------------------------------------



$db = new dbquery();
$core = new core_tools();
$core->load_lang();
$dType = new diffusion_type_controler();
$diffType = array();
$diffType = $dType->getAllDiffusion();


foreach($diffType as $loadedType)
{
	if ($loadedType -> id == $_REQUEST['id_type']){
		
		if ($loadedType -> script <> '')
		{
			include($loadedType -> script);
			$content = getContent();
			
			echo "{status : 0, div_content : '" . addslashes($content) . "'}";
		}
		else
		{
			 echo "{status : 1, error_txt : '" . addslashes($_SESSION['error']) . "'}";
		}
	}
}	
exit();


$origin = $_REQUEST['origin'];




if (isset($_SESSION[$origin]['diff_list']['dest']['user_id'])
    && ! empty($_SESSION[$origin]['diff_list']['dest']['user_id'])
) {
    if (! $onlyCC) {
        $content .= '<p class="sstit">' . _RECIPIENT . '</p>';
        $content .= '<table cellpadding="0" cellspacing="0" border="0" class="listing3">';
        $content .= '<tr class="col">';
        $content .= '<td><img src="' . $_SESSION['config']['businessappurl']
                 . 'static.php?filename=manage_users_entities_b_small.gif'
                 . '&module=entities" alt="' . _USER . '" title="'
                 . _USER . '" /></td>';
        $content .= '<td >' . $_SESSION[$origin]['diff_list']['dest']['firstname']
                 . '</td>';
        $content .= '<td >' . $_SESSION[$origin]['diff_list']['dest']['lastname']
                 .'</td>';
        $content .= '<td>' . $_SESSION[$origin]['diff_list']['dest']['entity_label']
                 .'</td>';
        $content .= '</tr>';
        $content .= '</table><br/>';
    }
    if (count($_SESSION[$origin]['diff_list']['copy']['users']) > 0
        || count($_SESSION[$origin]['diff_list']['copy']['entities']) > 0
    ) {
        if (! $onlyCC) {
            $content .= '<p class="sstit">' . _TO_CC . '</p>';
        }
        $content .= '<table cellpadding="0" cellspacing="0" border="0" class="listing3">';
        $color = ' class="col"';
        for ($i = 0; $i < count(
            $_SESSION[$origin]['diff_list']['copy']['entities']
        ); $i ++
        ) {
            if ($color == ' class="col"') {
                $color = '';
            } else {
                $color = ' class="col"';
            }
            $content .= '<tr ' . $color . ' >';
            $content .= '<td><img src="' . $_SESSION['config']['businessappurl']
                     . 'static.php?filename=manage_entities_b_small.gif&module='
                     . 'entities" alt="' . _ENTITY . '" title="' . _ENTITY
                     . '" /></td>';
            $content .= '<td >' . $_SESSION[$origin]['diff_list']['copy']['entities'][$i]['entity_id']
                     .'</td>';
            $content .= '<td colspan="2">'
                     . $_SESSION[$origin]['diff_list']['copy']['entities'][$i]['entity_label']
                     .'</td>';
            $content .= '</tr>';
        }
        for ($i = 0; $i < count(
            $_SESSION[$origin]['diff_list']['copy']['users']
        ); $i ++
        ) {
            if ($color == ' class="col"') {
                $color = '';
            } else {
                $color = ' class="col"';
            }
            $content .= '<tr ' . $color . ' >';
            $content .= '<td><img src="' . $_SESSION['config']['businessappurl']
                     . 'static.php?filename=manage_users_entities_b_small.gif'
                     . '&module=entities" alt="' . _USER . '" title="' . _USER
                     . '" /></td>';
            $content .= '<td >'
                     . $_SESSION[$origin]['diff_list']['copy']['users'][$i]['firstname']
                     . '</td>';
            $content .= '<td >'
                     . $_SESSION[$origin]['diff_list']['copy']['users'][$i]['lastname']
                     . '</td>';
            $content .= '<td>'
                     . $_SESSION[$origin]['diff_list']['copy']['users'][$i]['entity_label']
                     . '</td>';
            $content .= '</tr>';
        }
        $content .= '</table>';
    }
    $labelButton = _MODIFY_LIST;
    $arg = '&mode=up';
} else {
    $content .= '<p>' . _NO_DIFF_LIST_ASSOCIATED . '</p>';
    $labelButton = _CREATE_LIST;
    $arg = '&mode=add';
}
if ($onlyCC) {
    $arg .= '&only_cc';
}
$content .= '<p class="button" >';
$content .= '<img src="' . $_SESSION['config']['businessappurl']
         . 'static.php?filename=modif_liste.png&module=entities" alt="" />'
         . '<a href="javascript://" onclick="window.open(\''
         . $_SESSION['config']['businessappurl'] . 'index.php?display=true'
         . '&module=entities&page=manage_listinstance&origin=' . $origin . $arg
         . '\', \'\', \'scrollbars=yes,menubar=no,toolbar=no,status=no,'
         . 'resizable=yes,width=1024,height=650,location=no\');">'
         . $labelButton . '</a>';
$content .= '</p>';

echo "{status : 0, div_content : '" . addslashes($content) . "'}";
exit();