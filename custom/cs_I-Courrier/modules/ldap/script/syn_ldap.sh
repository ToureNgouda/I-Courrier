#!/bin/bash
cd /var/www/html/MonProjet/I-Courrier/modules/ldap/script/

#generation des fichiers xml
php /var/www/html/MonProjet/I-Courrier/modules/ldap/process_ldap_to_xml.php /var/www/html/MonProjet/I-Courrier/custom/cs_I-Courrier/modules/ldap/xml/config.xml

#mise a jour bdd
php /var/www/html/MonProjet/I-Courrier/modules/ldap/process_entities_to_maarch.php /var/www/html/MonProjet/I-Courrier/custom/cs_I-Courrier/modules/ldap/xml/config.xml
php /var/www/html/MonProjet/I-Courrier/modules/ldap/process_users_to_maarch.php /var/www/html/MonProjet/I-Courrier/custom/cs_I-Courrier/modules/ldap/xml/config.xml
php /var/www/html/MonProjet/I-Courrier/modules/ldap/process_users_entities_to_maarch.php /var/www/html/MonProjet/I-Courrier/custom/cs_I-Courrier/modules/ldap/xml/config.xml