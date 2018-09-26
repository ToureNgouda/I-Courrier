#!/bin/bash
cd /var/www/html/I-CourrierV1.0/modules/notifications/batch/
emailStackPath='/var/www/html/I-CourrierV1.0/modules/notifications/batch/process_email_stack.php'
php $emailStackPath -c /var/www/html/I-CourrierV1.0/custom/cs_courrier_db/modules/notifications/batch/config/config.xml