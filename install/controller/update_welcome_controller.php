<?php
/**
* Copyright Maarch since 2008 under licence GPLv3.
* See LICENCE.txt file at the root folder for more details.
* This file is part of Maarch software.
*
*/

/**
* @brief class of install tools
*
* @file
* @author Arnaud Veber
* @date $date$
* @version $Revision$
* @ingroup install
*/

//CONTROLLER

    //TITLE
        $shortTitle = _UPDATE_WELCOME;
        $longTitle = _UPDATE_WELCOME;

    //ALLOWED SQL
        //$listSql = $Class_Install->getDataList();

    //PROGRESS
        $stepNb = 1;
        $stepNbTotal = 6;

//VIEW
    $view = 'update_welcome';
