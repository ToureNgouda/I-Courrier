<?php
switch ($request) {
case 'form_content':
	$form_content .= '<p class="sstit">' . _NOTIFICATIONS_DEST_USER_DIFF_TYPE . '</p>';
	break;

case 'recipients':
    $recipients = array();
    $dbRecipients = new dbquery();
    $dbRecipients->connect();
    
    $select = "SELECT distinct us.*";
	$from = " FROM listinstance li JOIN users us ON li.item_id = us.user_id";
    $where = " WHERE li.coll_id = 'letterbox_coll' AND li.listinstance_type='DOC' AND li.item_mode = 'dest'";
    
    switch($event->table_name) {
    case 'notes':
        $from .= " JOIN notes ON notes.coll_id = li.coll_id AND notes.identifier = li.res_id";
		$where .= " AND notes.id = " . $event->record_id . " AND li.item_id != notes.user_id";
        break;
    
    case 'res_letterbox':
    case 'res_view_letterbox':
        $from .= " JOIN res_letterbox lb ON lb.res_id = li.res_id";
        $where .= " AND lb.res_id = " . $event->record_id;
        break;
    
    case 'listinstance':
    default:
        $where .= " AND listinstance_id = " . $event->record_id;
    }

    $query = $select . $from . $where;
    
    if($GLOBALS['logger']) {
        $GLOBALS['logger']->write($query , 'DEBUG');
    }
    
	$dbRecipients->query($query);
	
	while($recipient = $dbRecipients->fetch_object()) {
		$recipients[] = $recipient;
	}
	break;

case 'attach':
	$attach = false;
	break;

case 'res_id':
    $select = "SELECT li.res_id";
    $from = " FROM listinstance li";
    $where = " WHERE li.coll_id = 'letterbox_coll' AND li.listinstance_type='DOC' ";
    
    switch($event->table_name) {
    case 'notes':
        $from .= " JOIN notes ON notes.coll_id = li.coll_id AND notes.identifier = li.res_id";
		$where .= " AND notes.id = " . $event->record_id . " AND li.item_id != notes.user_id";
        break;
        
    case 'res_letterbox':
    case 'res_view_letterbox':
        $from .= " JOIN res_letterbox lb ON lb.res_id = li.res_id";
        $where .= " AND lb.res_id = " . $event->record_id;
        break;
    
    case 'listinstance':
    default:
        $where .= " AND listinstance_id = " . $event->record_id;
    }
    
    $query = $query = $select . $from . $where;
    
    if($GLOBALS['logger']) {
        $GLOBALS['logger']->write($query , 'DEBUG');
    }
    
	$dbResId = new dbquery();
    $dbResId->connect();
	$dbResId->query($query);
	$res_id_record = $dbResId->fetch_object();
    $res_id = $res_id_record->res_id;
    break;
    
}
?>
