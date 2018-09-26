#!/bin/bash
eventStackPath='/var/www/html/MonProjet/I-Courrier/modules/notifications/batch/process_event_stack.php'
cd /var/www/html/MonProjet/I-Courrier/modules/notifications/batch/
#php $eventStackPath -c /var/www/html/MonProjet/I-Courrier/custom/cs_I-Courrier/modules/notifications/batch/config/config.xml -n NCT
#php $eventStackPath -c /var/www/html/MonProjet/I-Courrier/custom/cs_I-Courrier/modules/notifications/batch/config/config.xml -n NCC
php $eventStackPath -c /var/www/html/MonProjet/I-Courrier/custom/cs_I-Courrier/modules/notifications/batch/config/config.xml -n ANC
php $eventStackPath -c /var/www/html/MonProjet/I-Courrier/custom/cs_I-Courrier/modules/notifications/batch/config/config.xml -n AND
php $eventStackPath -c /var/www/html/MonProjet/I-Courrier/custom/cs_I-Courrier/modules/notifications/batch/config/config.xml -n RED