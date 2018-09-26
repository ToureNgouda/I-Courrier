<?php

/*
*    Copyright 2008-2015 Maarch
*
*  This file is part of Maarch Framework.
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
*    along with Maarch Framework.  If not, see <http://www.gnu.org/licenses/>.
*/

require_once 'core/core_tables.php';
require_once 'core/class/class_request.php';
require_once 'core/class/usergroups_controler.php';

switch ($request) {
    case 'recipients':
        $groups = "'".str_replace(',', "','", $notification->diffusion_properties)."'";
        $query = 'SELECT distinct us.*'
            .' FROM usergroup_content ug '
            .'	LEFT JOIN users us ON us.user_id = ug.user_id'
            .' WHERE ug.group_id in ('.$groups.')';
        $dbRecipients = new Database();
        $stmt = $dbRecipients->query($query);
        $recipients = array();
        while ($recipient = $stmt->fetchObject()) {
            $recipients[] = $recipient;
        }
        break;

    case 'attach':
        $groups = "'".str_replace(',', "','", $notification->attachfor_properties)."'";
        $query = 'SELECT user_id'
            .' FROM usergroup_content'
            .' WHERE group_id in ('.$groups.')'
            .' AND user_id = ?';
        $attach = false;
        $dbAttach = new Database();
        $stmt = $dbAttach->query($query, array($user_id));
        if ($stmt->rowCount() > 0) {
            $attach = true;
        }
        break;
}
