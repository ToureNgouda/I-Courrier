CREATE TABLE EXT_REFERENCES
(
  REFERENCE_ID  VARCHAR2(255 CHAR)               DEFAULT '0',
  TYPE_LDAP     VARCHAR2(255 CHAR)               DEFAULT NULL,
  FIELD         VARCHAR2(255 CHAR)               DEFAULT NULL,
  VALUE         VARCHAR2(255 CHAR)               DEFAULT NULL
)
TABLESPACE SYSAUX
PCTUSED    0
PCTFREE    10
INITRANS   1
MAXTRANS   255
STORAGE    (
            INITIAL          64K
            MINEXTENTS       1
            MAXEXTENTS       2147483645
            PCTINCREASE      0
            BUFFER_POOL      DEFAULT
           );


CREATE UNIQUE INDEX EXT_REFERENCES_PKEY ON EXT_REFERENCES
(REFERENCE_ID, TYPE_LDAP)
TABLESPACE SYSAUX
PCTFREE    10
INITRANS   2
MAXTRANS   255
STORAGE    (
            INITIAL          64K
            MINEXTENTS       1
            MAXEXTENTS       2147483645
            PCTINCREASE      0
            BUFFER_POOL      DEFAULT
           );


ALTER TABLE EXT_REFERENCES ADD (
  CONSTRAINT EXT_REFERENCES_PKEY
 PRIMARY KEY
 (REFERENCE_ID, TYPE_LDAP)
    USING INDEX 
    TABLESPACE SYSAUX
    PCTFREE    10
    INITRANS   2
    MAXTRANS   255
    STORAGE    (
                INITIAL          64K
                MINEXTENTS       1
                MAXEXTENTS       2147483645
                PCTINCREASE      0
               ));
