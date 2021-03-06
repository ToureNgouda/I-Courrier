<?php
/**
* Copyright Maarch since 2008 under licence GPLv3.
* See LICENCE.txt file at the root folder for more details.
* This file is part of Maarch software.

*
* @brief   copy_list
*
* @author  dev <dev@maarch.org>
* @ingroup notification
*/
switch ($request) {
    case 'recipients':
        $recipients = array();
        $dbRecipients = new Database();

        // Copy to users
        $select = 'SELECT distinct us.*';
        $from = ' FROM listinstance li '
            .' JOIN users us ON li.item_id = us.user_id';
        $where = " WHERE li.coll_id = 'letterbox_coll'   AND li.item_mode = 'cc'"
            ." AND item_type='user_id'";

        $arrayPDO = array(':recordid' => $event->record_id);

        switch ($event->table_name) {
            case 'notes':
                $from .= ' JOIN notes ON notes.coll_id = li.coll_id AND notes.identifier = li.res_id';
                $where .= ' AND notes.id = :recordid AND li.item_id != notes.user_id'
                    .' AND ('
                        .' notes.id not in (SELECT DISTINCT note_id FROM note_entities) '
                        .' OR us.user_id IN (SELECT ue.user_id FROM note_entities ne JOIN users_entities ue ON ne.item_id = ue.entity_id WHERE ne.note_id = :recordid)'
                    .')'
                ;
                break;

            case 'res_letterbox':
            case 'res_view_letterbox':
                $from .= ' JOIN res_letterbox lb ON lb.res_id = li.res_id';
                $where .= ' AND lb.res_id = :recordid';
                break;

            case 'listinstance':
            default:
                $from .= ' JOIN res_letterbox lb ON lb.res_id = li.res_id';
                $where .= " AND listinstance_id = :recordid AND lb.status not in ('INIT', 'AVAL') AND li.item_id <> :userid";
                $arrayPDO = array_merge($arrayPDO, array(':userid' => $event->user_id));
        }

        $query = $select.$from.$where;

        if ($GLOBALS['logger']) {
            $GLOBALS['logger']->write($query, 'DEBUG');
        }

        $stmt = $dbRecipients->query($query, $arrayPDO);

        while ($recipient = $stmt->fetchObject()) {
            $recipients[] = $recipient;
        }

        $arrayPDO = array(':recordid' => $event->record_id);
        // Copy to entities
        $select = 'SELECT distinct us.*';
        $from = ' FROM listinstance li '
            .' LEFT JOIN users_entities ue ON li.item_id = ue.entity_id '
            .' JOIN users us ON ue.user_id = us.user_id';
        $where = " WHERE li.coll_id = 'letterbox_coll'   AND li.item_mode = 'cc'"
            ." AND item_type='entity_id'";

        switch ($event->table_name) {
            case 'notes':
                $from .= ' JOIN notes ON notes.coll_id = li.coll_id AND notes.identifier = li.res_id';
                $where .= ' AND notes.id = :recordid AND li.item_id != notes.user_id'
                    .' AND ('
                        .' notes.id not in (SELECT DISTINCT note_id FROM note_entities) '
                        .' OR us.user_id IN (SELECT ue.user_id FROM note_entities ne JOIN users_entities ue ON ne.item_id = ue.entity_id WHERE ne.note_id = :recordid)'
                    .')'
                ;
                break;

            case 'res_letterbox':
            case 'res_view_letterbox':
                $from .= ' JOIN res_letterbox lb ON lb.res_id = li.res_id';
                $where .= ' AND lb.res_id = :recordid';
                break;

            case 'listinstance':
            default:
                $where .= ' AND listinstance_id = :recordid';
        }

        $query = $select.$from.$where;

        if ($GLOBALS['logger']) {
            $GLOBALS['logger']->write($query, 'DEBUG');
        }

        $stmt = $dbRecipients->query($query, $arrayPDO);

        while ($recipient = $stmt->fetchObject()) {
            $recipients[] = $recipient;
        }
        break;

    case 'attach':
        $attach = false;
        break;

    case 'res_id':
        $arrayPDO = array(':recordid' => $event->record_id);
        $select = 'SELECT li.res_id';
        $from = ' FROM listinstance li';
        $where = " WHERE li.coll_id = 'letterbox_coll'   ";

        switch ($event->table_name) {
            case 'notes':
                $from .= ' JOIN notes ON notes.coll_id = li.coll_id AND notes.identifier = li.res_id';
                $where .= ' AND notes.id = :recordid AND li.item_id != notes.user_id';
                break;

            case 'res_letterbox':
            case 'res_view_letterbox':
                $from .= ' JOIN res_letterbox lb ON lb.res_id = li.res_id';
                $where .= ' AND lb.res_id = :recordid';
                break;

            case 'listinstance':
            default:
                $where .= ' AND listinstance_id = :recordid';
        }

        $query = $query = $select.$from.$where;

        if ($GLOBALS['logger']) {
            $GLOBALS['logger']->write($query, 'DEBUG');
        }

        $dbResId = new Database();
        $stmt = $dbResId->query($query, $arrayPDO);
        $res_id_record = $stmt->fetchObject();
        $res_id = $res_id_record->res_id;
        break;
}
