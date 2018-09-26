<?php
/**
* Copyright Maarch since 2008 under licence GPLv3.
* See LICENCE.txt file at the root folder for more details.
* This file is part of Maarch software.

*
* @brief   user
*
* @author  dev <dev@maarch.org>
* @ingroup notifications
*/
require_once 'core/core_tables.php';
require_once 'core/class/class_request.php';
require_once 'core/class/users_controler.php';

switch ($request) {
    case 'recipients':
        $users = "'".str_replace(',', "','", $notification->diffusion_properties)."'";
        $query = 'SELECT us.*'
            .' FROM users us'
            .' WHERE us.user_id in ('.$users.')';
        $dbRecipients = new Database();
        $stmt = $dbRecipients->query($query);
        $recipients = array();
        while ($recipient = $stmt->fetchObject()) {
            $recipients[] = $recipient;
        }
        break;

    case 'attach':
        $users = "'".str_replace(',', "','", (string) $notification->attachfor_properties)."'";
        $query = 'SELECT user_id'
            .' FROM users'
            ." WHERE '".$user_id."' in (".$users.')';
        $attach = false;
        $dbAttach = new Database();
        $stmt = $dbAttach->query($query);
        if ($stmt->rowCount() > 0) {
            $attach = true;
        }
        break;
}
