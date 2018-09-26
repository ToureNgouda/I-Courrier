<?php
/**
* Copyright Maarch since 2008 under licence GPLv3.
* See LICENCE.txt file at the root folder for more details.
* This file is part of Maarch software.

*
* @brief   about
*
* @author  dev <dev@maarch.org>
* @ingroup smartphone
*/
if (file_exists('../../../core/init.php')) {
    include_once '../../../core/init.php';
}
if (!isset($_SESSION['config']['corepath'])) {
    header('location: ../../../');
}
require_once 'core/class/class_functions.php';
require_once 'core/class/class_core_tools.php';
$core = new core_tools();
$core->load_lang();

$html = '<div id="about" title="'._MAARCH_CREDITS.'" class="panel">';
$html .= '<p id="logo" align="center">';
$html .= "<img src='{$_SESSION['config']['businessappurl']}static.php?filename=logo.svg' alt='Maarch' />";
$html .= '</p>';

$html .= '<p>';
$html .= 'Maarch is a French software editor specialised in optimising document flows.';
$html .= 'Maarch solutions are born from the need to provide our customers with easy and fast to build proofs of concept. The solution has quickly reach a fully operational level. Based on web technologies, it is now able to manage a document form its creation or digitization until its end-of-life.';
$html .= '</p>';

$html .= '<p>';
$html .= 'According to their beliefs that DMS and archiving solutions require open and standardised solutions to make sure of the continuity of data, Maarch solutions are all released under the terms of the open source license GNU GPL.';
$html .= '</p>';

$html .= '<p>';
$html .= 'Maarch is the main developer of Maarch Solutions and finance the core team.';
$html .= '</p>';

$html .= '</div>';

echo $html;
