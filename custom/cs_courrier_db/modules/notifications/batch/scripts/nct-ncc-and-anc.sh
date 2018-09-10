#!/bin/bash
eventStackPath='/var/www/html/I-CourrierV1.0/modules/notifications/batch/process_event_stack.php'
cd /var/www/html/I-CourrierV1.0/modules/notifications/batch/
#php $eventStackPath -c /var/www/html/I-CourrierV1.0/custom/cs_courrier_db/modules/notifications/batch/config/config.xml -n NCT
#php $eventStackPath -c /var/www/html/I-CourrierV1.0/custom/cs_courrier_db/modules/notifications/batch/config/config.xml -n NCC
php $eventStackPath -c /var/www/html/I-CourrierV1.0/custom/cs_courrier_db/modules/notifications/batch/config/config.xml -n ANC
php $eventStackPath -c /var/www/html/I-CourrierV1.0/custom/cs_courrier_db/modules/notifications/batch/config/config.xml -n AND
php $eventStackPath -c /var/www/html/I-CourrierV1.0/custom/cs_courrier_db/modules/notifications/batch/config/config.xml -n RED