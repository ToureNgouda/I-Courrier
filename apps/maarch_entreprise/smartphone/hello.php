<?php
/**
* Copyright Maarch since 2008 under licence GPLv3.
* See LICENCE.txt file at the root folder for more details.
* This file is part of Maarch software.

*
* @brief hello
*
* @author  dev <dev@maarch.org>
* @ingroup apps
*/
include_once '../../../core/init.php';

if (!isset($_SESSION['config']['corepath']) || !isset($_SESSION['config']['databasename'])) {
    header('location: ../../../');
}

require_once 'core/class/class_functions.php';
require_once 'core/class/class_core_tools.php';
$core = new core_tools();
$core->load_lang();

echo '<html>';
$core->loadSmartphoneHeader();
echo '<body>';
echo '<div class="toolbar">';
echo '<h1 id="pageTitle"></h1>';
echo '</div>';

echo '<form selected="true" title="BIENVENUE" class="panel" method="post" action="login.php" target="_self">';
echo '<fieldset>';
echo '<div class="row">';
echo '<p id="maarchLogo" align="center">';
echo '<br><br><br>';
echo "<img src='{$_SESSION['config']['businessappurl']}static.php?filename=logo.svg' alt='Maarch' usemap='#maarch'>";
echo '<map name="maarch">';
echo '<area shape="rect" coords="0,0,240,80" href="login.php">';
echo '</map>';
echo '<br><br><br><br>';
echo '</p>';
echo '</div>';
echo '</fieldset>';
echo '<input class="whiteButton" type="submit" value="Continuer" width="50px">';
echo '</form>';
echo '</body>';
echo '</html>';
