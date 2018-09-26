

ALTER TABLE adr_x DROP COLUMN IF EXISTS adr_type;
ALTER TABLE adr_x ADD COLUMN adr_type character varying(32) NOT NULL DEFAULT 'DOC';
ALTER TABLE adr_attachments DROP COLUMN IF EXISTS adr_type;
ALTER TABLE adr_attachments ADD COLUMN adr_type character varying(32) NOT NULL DEFAULT 'DOC';


--convert result
ALTER TABLE res_attachments DROP COLUMN IF EXISTS convert_result;
ALTER TABLE res_attachments ADD COLUMN convert_result character varying(10) DEFAULT NULL::character varying;
ALTER TABLE res_letterbox DROP COLUMN IF EXISTS convert_result;
ALTER TABLE res_letterbox ADD COLUMN convert_result character varying(10) DEFAULT NULL::character varying;
ALTER TABLE res_x DROP COLUMN IF EXISTS convert_result;
ALTER TABLE res_x ADD COLUMN convert_result character varying(10) DEFAULT NULL::character varying;
ALTER TABLE res_version_attachments DROP COLUMN IF EXISTS convert_result;
ALTER TABLE res_version_attachments ADD COLUMN convert_result character varying(10) DEFAULT NULL::character varying;


--convert attempts
ALTER TABLE res_attachments DROP COLUMN IF EXISTS convert_attempts;
ALTER TABLE res_attachments ADD COLUMN convert_attempts integer DEFAULT NULL::integer;
ALTER TABLE res_letterbox DROP COLUMN IF EXISTS convert_attempts;
ALTER TABLE res_letterbox ADD COLUMN convert_attempts integer DEFAULT NULL::integer;
ALTER TABLE res_x DROP COLUMN IF EXISTS convert_attempts;
ALTER TABLE res_x ADD COLUMN convert_attempts integer DEFAULT NULL::integer;
ALTER TABLE res_version_attachments DROP COLUMN IF EXISTS convert_attempts;
ALTER TABLE res_version_attachments ADD COLUMN convert_attempts integer DEFAULT NULL::integer;

--fulltext attempts
ALTER TABLE res_attachments DROP COLUMN IF EXISTS fulltext_attempts;
ALTER TABLE res_attachments ADD COLUMN fulltext_attempts integer DEFAULT NULL::integer;
ALTER TABLE res_letterbox DROP COLUMN IF EXISTS fulltext_attempts;
ALTER TABLE res_letterbox ADD COLUMN fulltext_attempts integer DEFAULT NULL::integer;
ALTER TABLE res_x DROP COLUMN IF EXISTS fulltext_attempts;
ALTER TABLE res_x ADD COLUMN fulltext_attempts integer DEFAULT NULL::integer;
ALTER TABLE res_version_attachments DROP COLUMN IF EXISTS fulltext_attempts;
ALTER TABLE res_version_attachments ADD COLUMN fulltext_attempts integer DEFAULT NULL::integer;

--tnl attempts
ALTER TABLE res_attachments DROP COLUMN IF EXISTS tnl_attempts;
ALTER TABLE res_attachments ADD COLUMN tnl_attempts integer DEFAULT NULL::integer;
ALTER TABLE res_letterbox DROP COLUMN IF EXISTS tnl_attempts;
ALTER TABLE res_letterbox ADD COLUMN tnl_attempts integer DEFAULT NULL::integer;
ALTER TABLE res_x DROP COLUMN IF EXISTS tnl_attempts;
ALTER TABLE res_x ADD COLUMN tnl_attempts integer DEFAULT NULL::integer;
ALTER TABLE res_version_attachments DROP COLUMN IF EXISTS tnl_attempts;
ALTER TABLE res_version_attachments ADD COLUMN tnl_attempts integer DEFAULT NULL::integer;



--thumbnails result
ALTER TABLE res_attachments DROP COLUMN IF EXISTS tnl_result;
ALTER TABLE res_attachments ADD COLUMN tnl_result character varying(10) DEFAULT NULL::character varying;
ALTER TABLE res_letterbox DROP COLUMN IF EXISTS tnl_result;
ALTER TABLE res_letterbox ADD COLUMN tnl_result character varying(10) DEFAULT NULL::character varying;
ALTER TABLE res_x DROP COLUMN IF EXISTS tnl_result;
ALTER TABLE res_x ADD COLUMN tnl_result character varying(10) DEFAULT NULL::character varying;
ALTER TABLE res_version_attachments DROP COLUMN IF EXISTS tnl_result;
ALTER TABLE res_version_attachments ADD COLUMN tnl_result character varying(10) DEFAULT NULL::character varying;

-- adr_letterbox
DROP TABLE IF EXISTS adr_letterbox;
CREATE TABLE adr_letterbox
(
  res_id bigint NOT NULL,
  docserver_id character varying(32) NOT NULL,
  path character varying(255) DEFAULT NULL::character varying,
  filename character varying(255) DEFAULT NULL::character varying,
  offset_doc character varying(255) DEFAULT NULL::character varying,
  fingerprint character varying(255) DEFAULT NULL::character varying,
  adr_priority integer NOT NULL,
  adr_type character varying(32) NOT NULL DEFAULT 'DOC'::character varying,
  CONSTRAINT adr_letterbox_pkey PRIMARY KEY (res_id, docserver_id)
)
WITH (OIDS=FALSE);

-- adr_attachments
DROP TABLE IF EXISTS adr_attachments;
CREATE TABLE adr_attachments
(
  res_id bigint NOT NULL,
  docserver_id character varying(32) NOT NULL,
  path character varying(255) DEFAULT NULL::character varying,
  filename character varying(255) DEFAULT NULL::character varying,
  offset_doc character varying(255) DEFAULT NULL::character varying,
  fingerprint character varying(255) DEFAULT NULL::character varying,
  adr_priority integer NOT NULL,
  adr_type character varying(32) NOT NULL DEFAULT 'DOC'::character varying,
  CONSTRAINT adr_attachments_pkey PRIMARY KEY (res_id, docserver_id)
)
WITH (OIDS=FALSE);

-- adr_attachments_version
DROP TABLE IF EXISTS adr_attachments_version;
CREATE TABLE adr_attachments_version
(
  res_id bigint NOT NULL,
  docserver_id character varying(32) NOT NULL,
  path character varying(255) DEFAULT NULL::character varying,
  filename character varying(255) DEFAULT NULL::character varying,
  offset_doc character varying(255) DEFAULT NULL::character varying,
  fingerprint character varying(255) DEFAULT NULL::character varying,
  adr_priority integer NOT NULL,
  adr_type character varying(32) NOT NULL DEFAULT 'DOC'::character varying,
  CONSTRAINT adr_attachments_version_pkey PRIMARY KEY (res_id, docserver_id)
)
WITH (OIDS=FALSE);

-- convert working table
DROP TABLE IF EXISTS convert_stack;
CREATE TABLE convert_stack
(
  coll_id character varying(32) NOT NULL,
  res_id bigint NOT NULL,
  convert_format character varying(32) NOT NULL DEFAULT 'pdf'::character varying,
  cnt_retry integer,
  status character(1) NOT NULL,
  work_batch bigint,
  regex character varying(32),
  CONSTRAINT convert_stack_pkey PRIMARY KEY (coll_id, res_id, convert_format)
)
WITH (OIDS=FALSE);


-- ************************************************************************* --
--                               DATAS                             --
-- ************************************************************************* --

-- docservers
UPDATE docservers set docserver_type_id = 'DOC' where docserver_type_id <> 'TEMPLATES' and docserver_type_id <> 'TNL';


DELETE FROM docserver_types where docserver_type_id = 'DOC';
INSERT INTO docserver_types (docserver_type_id, docserver_type_label, enabled, is_container, container_max_number, is_compressed, compression_mode, is_meta, meta_template, is_logged, log_template, is_signed, fingerprint_mode) 
VALUES ('DOC', 'Documents', 'Y', 'N', 0, 'N', 'NONE', 'N', 'NONE', 'N', 'NONE', 'Y', 'SHA512');

DELETE FROM docserver_types where docserver_type_id = 'CONVERT';
INSERT INTO docserver_types (docserver_type_id, docserver_type_label, enabled, is_container, container_max_number, is_compressed, compression_mode, is_meta, meta_template, is_logged, log_template, is_signed, fingerprint_mode) 
VALUES ('CONVERT', 'Conversions', 'Y', 'N', 0, 'N', 'NONE', 'N', 'NONE', 'N', 'NONE', 'Y', 'SHA256');

DELETE FROM docservers where docserver_id = 'CONVERT_MLB';
INSERT INTO docservers (docserver_id, docserver_type_id, device_label, is_readonly, enabled, size_limit_number, actual_size_number, path_template, ext_docserver_info, chain_before, chain_after, creation_date, closing_date, coll_id, priority_number, docserver_location_id, adr_priority_number) 
VALUES ('CONVERT_MLB', 'CONVERT', 'Server for mlb documents conversion', 'N', 'Y', 50000000000, 0, '/opt/maarch/docservers/convert_mlb/', NULL, NULL, NULL, '2015-03-16 14:47:49.197164', NULL, 'letterbox_coll', 13, 'NANTERRE', 4);

DELETE FROM docservers where docserver_id = 'FASTHD_ATTACH';
INSERT INTO docservers (docserver_id, docserver_type_id, device_label, is_readonly, enabled, size_limit_number, actual_size_number, path_template, ext_docserver_info, chain_before, chain_after, creation_date, closing_date, coll_id, priority_number, docserver_location_id, adr_priority_number) 
VALUES ('FASTHD_ATTACH', 'FASTHD', 'Fast internal disc bay for attachments', 'N', 'Y', 50000000000, 1, '/opt/maarch/docservers/manual_attachments/', NULL, NULL, NULL, '2011-01-13 14:47:49.197164', NULL, 'attachments_coll', 2, 'NANTERRE', 3);

DELETE FROM docservers where docserver_id = 'FASTHD_ATTACH_VERSION';
INSERT INTO docservers (docserver_id, docserver_type_id, device_label, is_readonly, enabled, size_limit_number, actual_size_number, path_template, ext_docserver_info, chain_before, chain_after, creation_date, closing_date, coll_id, priority_number, docserver_location_id, adr_priority_number) 
VALUES ('FASTHD_ATTACH_VERSION', 'FASTHD', 'Fast internal disc bay for attachments version', 'N', 'Y', 50000000000, 1, '/opt/maarch/docservers/manual_attachments_version/', NULL, NULL, NULL, '2011-01-13 14:47:49.197164', NULL, 'attachments_version_coll', 100, 'NANTERRE', 100);

DELETE FROM docservers where docserver_id = 'CONVERT_ATTACH';
INSERT INTO docservers (docserver_id, docserver_type_id, device_label, is_readonly, enabled, size_limit_number, actual_size_number, path_template, ext_docserver_info, chain_before, chain_after, creation_date, closing_date, coll_id, priority_number, docserver_location_id, adr_priority_number) 
VALUES ('CONVERT_ATTACH', 'CONVERT', 'Server for attachments documents conversion', 'N', 'Y', 50000000000, 0, '/opt/maarch/docservers/convert_attachments/', NULL, NULL, NULL, '2015-03-16 14:47:49.197164', NULL, 'attachments_coll', 14, 'NANTERRE', 5);

DELETE FROM docservers where docserver_id = 'CONVERT_ATTACH_VERSION';
INSERT INTO docservers (docserver_id, docserver_type_id, device_label, is_readonly, enabled, size_limit_number, actual_size_number, path_template, ext_docserver_info, chain_before, chain_after, creation_date, closing_date, coll_id, priority_number, docserver_location_id, adr_priority_number) 
VALUES ('CONVERT_ATTACH_VERSION', 'CONVERT', 'Server for attachments version documents conversion', 'N', 'Y', 50000000000, 0, '/opt/maarch/docservers/convert_attachments_version/', NULL, NULL, NULL, '2015-03-16 14:47:49.197164', NULL, 'attachments_version_coll', 101, 'NANTERRE', 101);

-- for thumbnails, attachments and fulltext :
DELETE FROM docservers where docserver_id = 'TNL_ATTACH';
INSERT INTO docservers (docserver_id, docserver_type_id, device_label, is_readonly, enabled, size_limit_number, actual_size_number, path_template, ext_docserver_info, chain_before, chain_after, creation_date, closing_date, coll_id, priority_number, docserver_location_id, adr_priority_number) 
VALUES ('TNL_ATTACH', 'TNL', 'Server for thumbnails of attachments', 'N', 'Y', 50000000000, 0, '/opt/maarch/docservers/thumbnails_attachments/', NULL, NULL, NULL, '2015-03-16 14:47:49.197164', NULL, 'attachments_coll', 15, 'NANTERRE', 6);

DELETE FROM docservers where docserver_id = 'TNL_ATTACH_VERSION';
INSERT INTO docservers (docserver_id, docserver_type_id, device_label, is_readonly, enabled, size_limit_number, actual_size_number, path_template, ext_docserver_info, chain_before, chain_after, creation_date, closing_date, coll_id, priority_number, docserver_location_id, adr_priority_number) 
VALUES ('TNL_ATTACH_VERSION', 'TNL', 'Server for thumbnails of attachments version', 'N', 'Y', 50000000000, 0, '/opt/maarch/docservers/thumbnails_attachments_version/', NULL, NULL, NULL, '2015-03-16 14:47:49.197164', NULL, 'attachments_version_coll', 102, 'NANTERRE', 102);

update docservers set docserver_id = 'TNL_MLB', priority_number = 12 where docserver_id = 'TNL';

DELETE FROM docserver_types where docserver_type_id = 'FULLTEXT';
INSERT INTO docserver_types (docserver_type_id, docserver_type_label, enabled, is_container, container_max_number, is_compressed, compression_mode, is_meta, meta_template, is_logged, log_template, is_signed, fingerprint_mode) 
VALUES ('FULLTEXT', 'FULLTEXT', 'Y', 'N', 0, 'N', 'NONE', 'N', 'NONE', 'N', 'NONE', 'Y', 'SHA256');

DELETE FROM docservers where docserver_id = 'FULLTEXT_MLB';
INSERT INTO docservers (docserver_id, docserver_type_id, device_label, is_readonly, enabled, size_limit_number, actual_size_number, path_template, ext_docserver_info, chain_before, chain_after, creation_date, closing_date, coll_id, priority_number, docserver_location_id, adr_priority_number) 
VALUES ('FULLTEXT_MLB', 'FULLTEXT', 'Server for mlb documents fulltext', 'N', 'Y', 50000000000, 0, '/opt/maarch/docservers/fulltext_mlb/', NULL, NULL, NULL, '2015-03-16 14:47:49.197164', NULL, 'letterbox_coll', 16, 'NANTERRE', 7);

DELETE FROM docservers where docserver_id = 'FULLTEXT_ATTACH';
INSERT INTO docservers (docserver_id, docserver_type_id, device_label, is_readonly, enabled, size_limit_number, actual_size_number, path_template, ext_docserver_info, chain_before, chain_after, creation_date, closing_date, coll_id, priority_number, docserver_location_id, adr_priority_number) 
VALUES ('FULLTEXT_ATTACH', 'FULLTEXT', 'Server for attachments documents fulltext', 'N', 'Y', 50000000000, 0, '/opt/maarch/docservers/fulltext_attachments/', NULL, NULL, NULL, '2015-03-16 14:47:49.197164', NULL, 'attachments_coll', 17, 'NANTERRE', 8);

DELETE FROM docservers where docserver_id = 'FULLTEXT_ATTACH_VERSION';
INSERT INTO docservers (docserver_id, docserver_type_id, device_label, is_readonly, enabled, size_limit_number, actual_size_number, path_template, ext_docserver_info, chain_before, chain_after, creation_date, closing_date, coll_id, priority_number, docserver_location_id, adr_priority_number) 
VALUES ('FULLTEXT_ATTACH_VERSION', 'FULLTEXT', 'Server for attachments version documents fulltext', 'N', 'Y', 50000000000, 0, '/opt/maarch/docservers/fulltext_attachments_version/', NULL, NULL, NULL, '2015-03-16 14:47:49.197164', NULL, 'attachments_version_coll', 103, 'NANTERRE', 103);

