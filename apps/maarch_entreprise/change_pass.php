<?php
/**
* Copyright Maarch since 2008 under licence GPLv3.
* See LICENCE.txt file at the root folder for more details.
* This file is part of Maarch software.

*
* @brief   change_pass
*
* @author  dev <dev@maarch.org>
* @ingroup apps
*/
$core_tools = new core_tools();
$core_tools->load_lang();
$core_tools->load_html();
//here we building the header
$core_tools->load_header(_MODIFICATION_PSW);
$time = $core_tools->get_session_time_expire();

$html = "<body onload='setTimeout(window.close, {$time}*60*1000);'>";
$html .= "<div id='container'>";
$html .= "<div id='content'>";

if (!empty($_SESSION['error'])) {
    $html .= "<div class='error' style='display:block;'>{$_SESSION['error']}</div>";
}
$_SESSION['error'] = '';

$html .= '<div id="inner_content" class="clearfix">';
$html .= '<h2 class="tit">';
$html .= "<img style='width:300px;height:200px;' src='{$_SESSION['config']['businessappurl']}static.php?filename=logoInnov.png'>
<p style='margin-left:150px;color:#DF7401;font-size:40px;'> I-Courrier </p>";
$html .= '<br/><br/>';

if ($_SESSION['user']['cookie_date']) {
    $html .= _MODIFICATION_PSW;
} else {
    $html .= _FIRST_CONN;
}
$html .= '</h2>';

$html .= '<div class="block">';

if (!$_SESSION['user']['cookie_date']) {
    $html .= '<h3>'._YOUR_FIRST_CONNEXION.', '._PLEASE_CHANGE_PSW.'<br/>'._ASKED_ONLY_ONCE.'</h3>';
} else {
    $html .= '<h3>'._PSW_REINI.'</h3>';
}

$html .= '<div class="blank_space">&nbsp;</div>';

$html .= "<form name='frmuser' method='post'  action='{$_SESSION['config']['businessappurl']}index.php?display=true&page=verif_pass' class='forms' >";
$html .= '<input type="hidden" name="display" value="true" />';
$html .= '<input type="hidden" name="page" value="verif_pass" />';

$html .= '<p>';
$html .= '<label>'._ID.' : </label>';
$html .= "<input type='text' readonly='readonly' class='readonly' value='{$_SESSION['user']['UserId']}'/>";
$html .= '</p>';

$html .= '<p>';
$html .= '<label>'._PASSWORD.' : </label>';
$html .= '<input name="pass1"  type="password" id="pass1" value="" /> <span class="red_asterisk"><i class="fa fa-star"></i></span>';
$html .= '</p>';

$html .= '<p>';
$html .= '<label>'._REENTER_PSW.' : </label>';
$html .= '<input name="pass2"  type="password" id="pass2" value="" /> <span class="red_asterisk"><i class="fa fa-star"></i></span>';
$html .= '</p>';

$html .= '<p>';
$html .= '<label>'._LASTNAME.' : </label>';
$html .= "<input name='LastName'  type='text' id='LastName'  value='{$_SESSION['user']['LastName']}' /> <span class='red_asterisk'><i class='fa fa-star'></i></span>";
$html .= '</p>';

$html .= '<p>';
$html .= '<label>'._FIRSTNAME.' : </label>';
$html .= "<input name='FirstName' type='text' id='FirstName' value='{$_SESSION['user']['FirstName']}' /> <span class='red_asterisk'><i class='fa fa-star'></i></span>";
$html .= '</p>';

if (!$core_tools->is_module_loaded('entities')) {
    $html .= '<p>';
    $html .= '<label>'._DEPARTMENT.' : </label>';
    $html .= "<input name='Department'  type='text' id='Department'  value='{$_SESSION['user']['department']}' />";
    $html .= '</p>';
}

$html .= '<p>';
$html .= '<label>'._PHONE_NUMBER.' : </label>';
$html .= "<input name='Phone'  type='text' id='Phone' value='{$_SESSION['user']['Phone']}' />";
$html .= '</p>';

$html .= '<p>';
$html .= '<label>'._MAIL.' : </label>';
$html .= "<input name='Mail'   type='text' id='Mail'  value='{$_SESSION['user']['Mail']}'/> <span class='red_asterisk'><i class='fa fa-star'></i></span>";
$html .= '</p>';

$html .= '<p class="buttons">';
$html .= '<input type="submit" name="Submit" value="'._VALIDATE.'" class="button" />';
$html .= ' <input type="button" name="cancel" value="'._CANCEL.'" class="button" onclick="window.location.href=\''.$_SESSION['config']['businessappurl'].'index.php?display=true&page=login\';" />';
$html .= '</p>';

$html .= '</form>';
$html .= '</div>';

$html .= '<div class="block_end">&nbsp;</div>';
$html .= '</div>';

$html .= '</div>';
$html .= '<div id="footer">&nbsp;</div>';
$html .= '</div>';

$html .= '</body>';
$html .= '</html>';

echo $html;
