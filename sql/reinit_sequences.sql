
Select setval('notifications_seq', (select max(notification_sid)+1 from notifications), false);
Select setval('res_id_mlb_seq', (select max(res_id)+1 from res_letterbox), false);
Select setval('templates_association_seq', (select max(system_id)+1 from templates_association), false);
Select setval('user_signatures_seq', (select max(id)+1 from user_signatures), false);
Select setval('templates_seq', (select max(template_id)+1 from templates), false);
Select setval('tag_id_seq', (select max(tag_id)+1 from tags), false);
Select setval('folders_system_id_seq', (select max(folders_system_id)+1 from folders), false);
Select setval('foldertype_id_id_seq', (select max(foldertype_id)+1 from foldertypes), false);
Select setval('groupbasket_status_system_id_seq', (select max(system_id)+1 from groupbasket_status), false);
Select setval('groupbasket_redirect_system_id_seq', (select max(system_id)+1 from groupbasket_redirect), false);
Select setval('actions_id_seq', (select max(id)+1 from actions), false);
Select setval('contact_addresses_id_seq', (select max(id)+1 from contact_addresses), false);
Select setval('contact_v2_id_seq', (select max(contact_id)+1 from contacts_v2), false);
Select setval('contact_purposes_id_seq', (select max(id)+1 from contact_purposes), false);
select setval('contact_types_id_seq', (select max(id)+1 from contact_types), false);
select setval('doctypes_type_id_seq', (select max(type_id)+1 from doctypes), false);
select setval('doctypes_first_level_id_seq', (select max(doctypes_first_level_id)+1 from doctypes_first_level), false);
select setval('doctypes_second_level_id_seq', (select max(doctypes_second_level_id)+1 from doctypes_second_level), false);
select setval('res_attachment_res_id_seq', (select max(res_id)+1 from res_attachments), false);
select setval('res_linked_mlb_seq', (select max(id)+1 from res_linked), false);
select setval('notif_email_stack_seq', (select max(email_stack_sid)+1 from notif_email_stack), false);
select setval('notif_event_stack_seq', (select max(event_stack_sid)+1 from notif_event_stack), false);
select setval('notes_seq', (select max(id)+1 from notes), false);
select setval('notes_entities_id_seq', (select max(id)+1 from note_entities), false);
select setval('listinstance_id_seq', (select max(listinstance_id)+1 from listinstance), false);
select setval('listinstance_history_id_seq', (select max(listinstance_history_id)+1 from listinstance_history), false);
select setval('listinstance_history_details_id_seq', (select max(listinstance_history_details_id)+1 from listinstance_history_details), false);
