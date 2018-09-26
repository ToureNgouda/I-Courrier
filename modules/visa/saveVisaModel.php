<?php

/*
 *   Copyright 2008-2015 Maarch and Document Image Solutions
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
 * @brief   Save the visa diffusion list
 *
 * Save the visa diffusion list
 *
 * @file
 * @author Nicolas Couture <couture@docimsol.com>
 * @date $date$
 * @version $Revision$
 * @ingroup apps
 */
require_once "modules" . DIRECTORY_SEPARATOR . "visa" . DIRECTORY_SEPARATOR
        . "class" . DIRECTORY_SEPARATOR
        . "class_modules_tools.php";


$title = $_REQUEST['title'];
$id_list = 'VISA_CIRCUIT_'. strtoupper(base_convert(date('U'), 10, 36));


$userList = json_decode($_REQUEST['userList']);

$visa = new visa();
$_SESSION['visa_wf']['diff_list']['visa']['users'] = array();
$_SESSION['visa_wf']['diff_list']['sign']['users'] = array();

$i = 0;
foreach ($userList as $user) {
    if (++$i === count($userList)) {
        array_push(
                $_SESSION['visa_wf']['diff_list']['sign']['users'], array(
            'user_id' => $user->userId,
            'process_comment' => $user->userConsigne,
            'process_date' => $user->userVisaDate,
            'viewed' => 0,
            'visible' => 'Y',
            'difflist_type' => 'VISA_CIRCUIT'
                )
        );
    } else {
        array_push(
                $_SESSION['visa_wf']['diff_list']['visa']['users'], array(
            'user_id' => $user->userId,
            'process_comment' => $user->userConsigne,
            'process_date' => $user->userVisaDate,
            'viewed' => 0,
            'visible' => 'Y',
            'difflist_type' => 'VISA_CIRCUIT'
                )
        );
    }
}
$visa->saveModelWorkflow($id_list, $_SESSION['visa_wf']['diff_list'], 'VISA_CIRCUIT', $title);

echo "{\"status\" : 0}";

exit();
?>