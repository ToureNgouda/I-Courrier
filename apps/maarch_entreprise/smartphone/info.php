<?php
/**
* Copyright Maarch since 2008 under licence GPLv3.
* See LICENCE.txt file at the root folder for more details.
* This file is part of Maarch software.

*
* @brief   info
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

echo '<div id="info" title="Nous Contacter" class="panel">';
echo '<div class="dis">';
echo '<p id="logo" align="center">';
echo "<img src='{$_SESSION['config']['businessappurl']}smartphone/img/logo_docimsol.png' alt='DIS' />";
echo '</p>';

echo '<div class="row">';
echo '<table align="center">';
echo '<tr><td colspan="2" align="left"><b>Document Image Solutions</b></td></tr>';
echo '<tr><td colspan="2" align="left"><b>2 Terrasses Claude Shannon</b></td></tr>';
echo '<tr><td colspan="2" align="left"><b>64210 Bidart</b></td></tr>';
echo '<tr><td align="left"><a href="mailto:contact@docimsol.com">contact@docimsol.com</a></td></tr>';
echo '<tr><td colspan="2" align="left"><a href="tel: +33 5 59 23 73 21">tel : +33 5 59 23 73 21</a></td></tr>';
echo '<tr><td><br></td></tr>';
echo '<tr><td colspan="2" align="center"><b>Site officiel :</b></td></tr>';
echo '<tr><td colspan="2" align="center"><a href="http://wwww.docimsol.eu/">http://www.docimsol.eu/</a></td></tr>';
echo '</table>';
echo '</div>';
echo '</div>';

echo '<div class="edissyum">';
echo '<p id="logo" align="center">';
echo "<img src='{$_SESSION['config']['businessappurl']}smartphone/img/logo_edissyum.gif' alt='Maarch' />";
echo '</p>';
echo '<div class="row">';
echo '<table align="center">';
echo '<tr><td colspan="2" align="left"><b>Edissyum</b></td></tr>';
echo '<tr><td colspan="2" align="left"><b>129 Boulevard Louis Giraud</b></td></tr>';
echo '<tr><td colspan="2" align="left"><b>84200 Carpentras</b></td></tr>';
echo '<tr><td align="left"><a href="mailto:contact@edissyum.com">contact@edissyum.com</a></td></tr>';
echo '<tr><td colspan="2" align="left"><a href="tel: +33 4 90 40 91 86">tel : +33 4 90 40 91 86</a></td></tr>';
echo '<tr><td><br></td></tr>';
echo '<tr><td colspan="2" align="center"><b>Site officiel :</b></td></tr>';
echo '<tr><td colspan="2" align="center"><a href="http://wwww.edissyum.com/">http://www.edissyum.com/</a></td></tr>';
echo '</table>';
echo '</div>';
echo '</div>';
echo '<hr/>';

echo '<div class="maarch">';
echo '<p id="logo" align="center">';
echo "<img src='{$_SESSION['config']['businessappurl']}static.php?filename=logo.svg' alt='Maarch' />";
echo '</p>';
echo '<div class="row">';
echo '<table align="center">';
echo '<tr><td colspan="2" align="left"><b>Maarch</b></td></tr>';
echo '<tr><td colspan="2" align="left"><b>11 bd du Sud-Est</b></td></tr>';
echo '<tr><td colspan="2" align="left"><b>92000 Nanterre</b></td></tr>';
echo '<tr><td align="left"><a href="mailto:info@maarch.org">info@maarch.org</a></td></tr>';
echo '<tr><td colspan="2" align="left"><a href="tel: +33 1 47 24 51 59">tel : +33 1 47 24 51 59</a></td></tr>';
echo '<tr><td><br></td></tr>';
echo '<tr><td colspan="2" align="center"><b>Site officiel :</b></td></tr>';
echo '<tr><td colspan="2" align="center"><a href="http://www.maarch.com">http://www.maarch.com</a></td></tr>';
echo '<tr><td colspan="2" align="center"><b>Documentation :</b></td></tr>';
echo '<tr><td colspan="2" align="center"><a href="http://wiki.maarch.org/Accueil">http://wiki.maarch.org/Accueil</a></td></tr>';
echo '</table>';
echo '</div>';
echo '</div>';
echo '</div>';
