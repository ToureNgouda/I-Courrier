<?php
/*
*   Copyright 2014-2017 Maarch
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
*
*   @brief Displays thumbnails if exists
*
*   @file
*   @author <dev@maarch.org>
*   @date $date$
*   @version $Revision$
*/

require_once 'modules/thumbnails/class/class_modules_tools.php';

$error = false;

if (isset($_REQUEST['resId'])) {
    $resId = $_REQUEST['resId'];
} else {
    $error = 'RES_ID' . _EMPTY;
}

if (isset($_REQUEST['collId'])) {
    $collId = $_REQUEST['collId'];
} else {
    $error = 'COLL_ID' . _EMPTY;
}

if (!$error) {
    $tnl = new thumbnails();
    $path = $tnl->getPathTnl($resId, 'letterbox_coll');
    
    if (is_file($path)) {
        $return .= '<img src="index.php?page=doc_thumb&module=thumbnails&res_id=' . $resId . '&coll_id=letterbox_coll&display=true">';
    $status = 0;
    } else {
        $return .= '<span id="no_doc"><i class="fa fa-ban" style="font-size: 460px;color: grey;opacity: 0.2;margin-top: 30px;"></i></span>';
        $status = 1;
    }
} else {
    $return .= '<span id="no_doc"><i class="fa fa-ban" style="font-size: 460px;color: grey;opacity: 0.2;margin-top: 30px;"></i></span>';
    $status = 1;
}

echo "{status : " . $status . ", toShow : '" . addslashes($return) . "'}";
exit ();
