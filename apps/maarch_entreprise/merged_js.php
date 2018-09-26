<?php
/**
* Copyright Maarch since 2008 under licence GPLv3.
* See LICENCE.txt file at the root folder for more details.
* This file is part of Maarch software.

* @brief   merged_js
* @author  dev <dev@maarch.org>
* @ingroup apps
*/

require_once '../../core/init.php';
require_once $_SESSION['config']['corepath'].DIRECTORY_SEPARATOR.'apps/maarch_entreprise'.DIRECTORY_SEPARATOR.'merged_jsAbstract.php';

class MergedJs extends MergedJsAbstract
{
    // Your stuff her
}

$oMergedJs = new MergedJs();
$oMergedJs->merge();
