-- *************************************************************************--
--                                                                          --
--                                                                          --
--        THIS SCRIPT IS USE TO PASS FROM MAARCH 1.6 TO MAARCH 1.7          --
--                                                                          --
--                                                                          --
-- *************************************************************************--
CREATE FUNCTION order_alphanum(text) RETURNS text AS $$
  SELECT regexp_replace(regexp_replace(regexp_replace(regexp_replace($1,
    E'(^|\\D)(\\d{1,3}($|\\D))', E'\\1000\\2', 'g'),
      E'(^|\\D)(\\d{4,6}($|\\D))', E'\\1000\\2', 'g'),
        E'(^|\\D)(\\d{7}($|\\D))', E'\\100\\2', 'g'),
          E'(^|\\D)(\\d{8}($|\\D))', E'\\10\\2', 'g');
$$ LANGUAGE SQL;




/* MIGRATION NOUVEL STRUCT MOTS CLES*/
DROP SEQUENCE IF EXISTS tag_id_seq CASCADE;
CREATE SEQUENCE tag_id_seq
  INCREMENT 1
  MINVALUE 1
  MAXVALUE 9223372036854775807
  START 7
  CACHE 1;

ALTER TABLE tags DROP COLUMN IF EXISTS tag_id;
ALTER TABLE tags ADD tag_id bigint NOT NULL DEFAULT nextval('tag_id_seq'::regclass);

ALTER TABLE tags DROP COLUMN IF EXISTS entity_id_owner;
ALTER TABLE tags ADD entity_id_owner character varying(32);

DROP SEQUENCE IF EXISTS tmp_tag_id_seq;
CREATE SEQUENCE tmp_tag_id_seq
  INCREMENT 1
  MINVALUE 1
  MAXVALUE 9223372036854775807
  START 7
  CACHE 1;

DROP TABLE IF EXISTS tmp_tags;
CREATE TABLE tmp_tags
(
  tag_id bigint NOT NULL DEFAULT nextval('tmp_tag_id_seq'::regclass),
  tag_label character varying(255) NOT NULL
)
WITH (
  OIDS=FALSE
);

INSERT INTO tmp_tags (tag_label)
SELECT distinct(lower(tag_label)) from tags;

DROP TABLE IF EXISTS tag_res;
CREATE TABLE tag_res
(
  res_id bigint NOT NULL,
  tag_id bigint NOT NULL,
  CONSTRAINT tag_res_pkey PRIMARY KEY (res_id,tag_id)
)
WITH (
  OIDS=FALSE
);

DO $$ 
    BEGIN
        BEGIN
            ALTER TABLE tags ADD res_id bigint;
        EXCEPTION
            WHEN duplicate_column THEN RAISE NOTICE 'column res_id already exists in tags. skipping...';
        END;
    END;
$$;
INSERT INTO tag_res (res_id,tag_id)
SELECT tags.res_id, tmp_tags.tag_id FROM tags, tmp_tags WHERE tmp_tags.tag_label = lower(tags.tag_label) AND tags.res_id IS NOT NULL;

TRUNCATE TABLE tags;

ALTER TABLE tags DROP CONSTRAINT IF EXISTS tagsjoin_pkey;
ALTER TABLE tags DROP COLUMN IF EXISTS res_id;

INSERT INTO tags (tag_label, coll_id, tag_id)
SELECT tag_label, 'letterbox_coll', tag_id FROM tmp_tags;


DROP TABLE IF EXISTS tmp_tags;
DROP SEQUENCE IF EXISTS tmp_tag_id_seq;

DROP TABLE IF EXISTS tags_entities;
CREATE TABLE tags_entities
(
  tag_id bigint,
  entity_id character varying(32),
  CONSTRAINT tags_entities_pkey PRIMARY KEY (tag_id,entity_id)
)
WITH (
  OIDS=FALSE
);



DROP TABLE IF EXISTS seda;

CREATE TABLE seda
(
  "message_id" text NOT NULL,
  "schema" text,
  "type" text NOT NULL,
  "status" text NOT NULL,
  
  "date" timestamp NOT NULL,
  "reference" text NOT NULL,
  
  "account_id" text,
  "sender_org_identifier" text NOT NULL,
  "sender_org_name" text,
  "recipient_org_identifier" text NOT NULL,
  "recipient_org_name" text,

  "archival_agreement_reference" text,
  "reply_code" text,
  "operation_date" timestamp,
  "reception_date" timestamp,
  
  "related_reference" text,
  "request_reference" text,
  "reply_reference" text,
  "derogation" boolean,
  
  "data_object_count" integer,
  "size" numeric,
  
  "data" text,
  
  "active" boolean,
  "archived" boolean,

  PRIMARY KEY ("message_id")
)
WITH (
  OIDS=FALSE
);

DROP TABLE IF EXISTS unit_identifier;

CREATE TABLE unit_identifier
(
  "message_id" text NOT NULL,
  "tablename" text NOT NULL,
  "res_id" text NOT NULL
);


ALTER TABLE doctypes DROP COLUMN IF EXISTS retention_final_disposition;
ALTER TABLE doctypes ADD COLUMN retention_final_disposition character varying(255) NOT NULL DEFAULT 'destruction';

ALTER TABLE doctypes DROP COLUMN IF EXISTS retention_rule;
ALTER TABLE doctypes ADD COLUMN retention_rule character varying(15) NOT NULL DEFAULT 'P10Y';


ALTER TABLE entities DROP COLUMN IF EXISTS archival_agency;
ALTER TABLE entities ADD COLUMN archival_agency character varying(255);

ALTER TABLE entities DROP COLUMN IF EXISTS archival_agreement;
ALTER TABLE entities ADD COLUMN archival_agreement character varying(255);

DELETE FROM docservers where docserver_id = 'FASTHD_ATTACH';
INSERT INTO docservers (docserver_id, docserver_type_id, device_label, is_readonly, enabled, size_limit_number, actual_size_number, path_template, ext_docserver_info, chain_before, chain_after, creation_date, closing_date, coll_id, priority_number, docserver_location_id, adr_priority_number) 
VALUES ('FASTHD_ATTACH', 'FASTHD', 'Fast internal disc bay for attachments', 'N', 'Y', 50000000000, 1, '/opt/maarch/docservers/manual_attachments/', NULL, NULL, NULL, '2011-01-13 14:47:49.197164', NULL, 'attachments_coll', 2, 'NANTERRE', 3);

ALTER TABLE basket_persistent_mode ALTER COLUMN user_id TYPE character varying(128);
ALTER TABLE res_mark_as_read ALTER COLUMN user_id TYPE character varying(128);

-- EXPORT SEDA
DROP TABLE IF EXISTS seda;
CREATE TABLE seda
(
  "message_id" text NOT NULL,
  "schema" text,
  "type" text NOT NULL,
  "status" text NOT NULL,
  
  "date" timestamp NOT NULL,
  "reference" text NOT NULL,
  
  "account_id" text,
  "sender_org_identifier" text NOT NULL,
  "sender_org_name" text,
  "recipient_org_identifier" text NOT NULL,
  "recipient_org_name" text,

  "archival_agreement_reference" text,
  "reply_code" text,
  "operation_date" timestamp,
  "reception_date" timestamp,
  
  "related_reference" text,
  "request_reference" text,
  "reply_reference" text,
  "derogation" boolean,
  
  "data_object_count" integer,
  "size" numeric,
  
  "data" text,
  
  "active" boolean,
  "archived" boolean,

  PRIMARY KEY ("message_id")
)
WITH (
  OIDS=FALSE
);

DROP TABLE IF EXISTS unit_identifier;
CREATE TABLE unit_identifier
(
  "message_id" text NOT NULL,
  "tablename" text NOT NULL,
  "res_id" text NOT NULL
);

/*************DIS UPDATE***************/
DROP SEQUENCE IF EXISTS allowed_ip_id_seq CASCADE;
CREATE SEQUENCE allowed_ip_id_seq
  INCREMENT 1
  MINVALUE 1
  MAXVALUE 9223372036854775807
  START 1
  CACHE 1;

DROP TABLE IF EXISTS allowed_ip;
CREATE TABLE allowed_ip
(
  id integer NOT NULL DEFAULT nextval('allowed_ip_id_seq'::regclass),
  ip character varying(50) NOT NULL,
  CONSTRAINT allowed_ip_pkey PRIMARY KEY (id)
)
WITH (OIDS=FALSE);

DROP SEQUENCE IF EXISTS user_signatures_seq CASCADE;
CREATE SEQUENCE user_signatures_seq
  INCREMENT 1
  MINVALUE 1
  MAXVALUE 9223372036854775807
  START 1
  CACHE 1;

DROP TABLE IF EXISTS user_signatures;
CREATE TABLE user_signatures
(
  id bigint NOT NULL DEFAULT nextval('user_signatures_seq'::regclass),
  user_id character varying(128) NOT NULL,
  signature_label character varying(255) DEFAULT NULL::character varying,
  signature_path character varying(255) DEFAULT NULL::character varying,
  signature_file_name character varying(255) DEFAULT NULL::character varying,
  fingerprint character varying(255) DEFAULT NULL::character varying,
  CONSTRAINT user_signatures_pkey PRIMARY KEY (id)
)
WITH (OIDS=FALSE);

ALTER TABLE users DROP COLUMN IF EXISTS ra_code;
ALTER TABLE users ADD ra_code character varying(255);
ALTER TABLE users DROP COLUMN IF EXISTS ra_expiration_date;
ALTER TABLE users ADD ra_expiration_date timestamp without time zone;

/** Add new service for group which have view_doc_history service **/
INSERT INTO usergroups_services SELECT group_id, 'view_full_history' FROM usergroups_services WHERE service_id = 'view_doc_history';

/** Migrate signatures to the new table **/
INSERT INTO user_signatures (user_id, signature_label, signature_path, signature_file_name) SELECT user_id, '', signature_path, signature_file_name FROM users WHERE signature_path is not null and signature_file_name is not null;

UPDATE parameters SET param_value_int = '170' WHERE id = 'database_version';