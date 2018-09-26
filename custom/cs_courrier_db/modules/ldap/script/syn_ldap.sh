#!/bin/bash
cd /var/www/html/I-CourrierV1.0/modules/ldap/script/

#generation des fichiers xml
php /var/www/html/I-CourrierV1.0/modules/ldap/process_ldap_to_xml.php /var/www/html/I-CourrierV1.0/custom/cs_courrier_db/modules/ldap/xml/config.xml

#mise a jour bdd
php /var/www/html/I-CourrierV1.0/modules/ldap/process_entities_to_maarch.php /var/www/html/I-CourrierV1.0/custom/cs_courrier_db/modules/ldap/xml/config.xml
php /var/www/html/I-CourrierV1.0/modules/ldap/process_users_to_maarch.php /var/www/html/I-CourrierV1.0/custom/cs_courrier_db/modules/ldap/xml/config.xml
php /var/www/html/I-CourrierV1.0/modules/ldap/process_users_entities_to_maarch.php /var/www/html/I-CourrierV1.0/custom/cs_courrier_db/modules/ldap/xml/config.xml