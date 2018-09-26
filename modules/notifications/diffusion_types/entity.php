<?php
/**
* Copyright Maarch since 2008 under licence GPLv3.
* See LICENCE.txt file at the root folder for more details.
* This file is part of Maarch software.

*
* @brief   entity
*
* @author  dev <dev@maarch.org>
* @ingroup notifications
*/
require_once 'core/core_tables.php';
require_once 'core/class/class_request.php';
require_once 'modules/entities/class/EntityControler.php';

switch ($request) {
    case 'recipients':
        $entities = "'".str_replace(',', "','", $notification->diffusion_properties)."'";
        $query = 'SELECT distinct us.*'
            .' FROM users_entities ue '
            .' LEFT JOIN users us ON us.user_id = ue.user_id '
            .' WHERE ue.entity_id in ('.$entities.')';
        $dbRecipients = new Database();
        $stmt = $dbRecipients->query($query);
        $recipients = array();
        while ($recipient = $stmt->fetchObject()) {
            $recipients[] = $recipient;
        }
        break;

    case 'attach':
        $attach = false;
        if ($notification->diffusion_type === 'dest_entity') {
            $tmp_entities = explode(',', $notification->attachfor_properties);
            $attach = in_array($user_id, $tmp_entities);
        } else {
            $entities = "'".str_replace(',', "','", $notification->attachfor_properties)."'";
            $query = 'SELECT user_id'
                .' FROM users_entities'
                .' WHERE entity_id in ('.$entities.')'
                .' AND user_id = ?';
            $dbAttach = new Database();
            $stmt = $dbAttach->query($query, array($user_id));
            if ($stmt->rowCount() > 0) {
                $attach = true;
            }
        }
        break;
}
