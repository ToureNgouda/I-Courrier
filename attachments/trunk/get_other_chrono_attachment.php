<?php

/*
*   Copyright 2015 Maarch
*
*   This file is part of Maarch Framework.
*
*   Maarch Framework is free software: you can redistribute it and/or modify
*   it under the terms of the GNU General Public License as published by
*   the Free Software Foundation, either version 3 of the License, or
*   (at your option) any later version.
*
*   Maarch Framework is distributed in the hope that it will be useful,
*   but WITHOUT ANY WARRANTY; without even the implied warranty of
*   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*   GNU General Public License for more details.
*
*   You should have received a copy of the GNU General Public License
*   along with Maarch Framework.  If not, see <http://www.gnu.org/licenses/>.
*/

/**
* @brief   Other chrono for attachments
*
* Open a modal box to displays the indexing form, make the form checks and loads
* the result in database. Used by the core (manage_action.php page).
*
* @file
* @author <dev@maarch.org>
* @date $date$
* @version $Revision$
* @ingroup apps
*/

    require_once 'core' . DIRECTORY_SEPARATOR . 'class' . DIRECTORY_SEPARATOR . 'class_request.php';
    require_once 'apps' . DIRECTORY_SEPARATOR . $_SESSION['config']['app_id']
        . DIRECTORY_SEPARATOR . 'class' . DIRECTORY_SEPARATOR . 'class_chrono.php';
    require_once("core".DIRECTORY_SEPARATOR."class".DIRECTORY_SEPARATOR."class_security.php");

    $core = new core_tools();
    $core->test_user();
    $db = new Database();

    $stmt = $db->query("SELECT identifier FROM res_view_attachments WHERE res_id_master = ? and attachment_type = ?", array($_SESSION['doc_id'], $_SESSION['attachment_types_get_chrono'][$_REQUEST['type_id']]));
    
    $listIdentifier = array();

    while ($res = $stmt->fetchObject()) {
         array_push($listIdentifier,$res->identifier);
    }

    $countIdentifier = count($listIdentifier);
    $listChrono .= '<option value="">S&eacute;lectionner le num&eacute;ro chrono</option>';

    for ($cptsIdentifier = 0;$cptsIdentifier < $countIdentifier;$cptsIdentifier++) {
        $listChrono .= '<option value="'.functions::show_string($listIdentifier[$cptsIdentifier]).'">'
            .  functions::show_string($listIdentifier[$cptsIdentifier])
        . '</option>';
    }

    echo "{status: 1, chronoList: '".$listChrono."'}";
