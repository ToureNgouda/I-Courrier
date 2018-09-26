<?php
/**
* Copyright Maarch since 2008 under licence GPLv3.
* See LICENCE.txt file at the root folder for more details.
* This file is part of Maarch software.

*
* @brief   contact
*
* @author  dev <dev@maarch.org>
* @ingroup notifications
*/
switch ($request) {
    case 'recipients':
        $query = 'SELECT contact_id as user_id, contact_email as mail'
            .' FROM res_view_letterbox '
            ." WHERE (contact_email is not null or contact_email <> '') and res_id = ?";
        $dbRecipients = new Database();
        $stmt = $dbRecipients->query($query, array($event->record_id));
        $recipients = array();
        while ($recipient = $stmt->fetchObject()) {
            $recipients[] = $recipient;
        }
        break;

    case 'attach':
        $query = 'SELECT contact_id as user_id, contact_email as mail'
            .' FROM res_view_letterbox '
            ." WHERE (contact_email is not null or contact_email <> '') and res_id = ?";
        $attach = false;
        $dbAttach = new Database();
        $stmt = $dbAttach->query($query, array($event->record_id));
        if ($stmt->rowCount() > 0) {
            $attach = true;
        }
        break;
}
