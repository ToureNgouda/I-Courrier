#!/bin/bash
cd /var/www/html/MonProjet/I-Courrier/modules/notifications/batch/
emailStackPath='/var/www/html/MonProjet/I-Courrier/modules/notifications/batch/process_email_stack.php'
php $emailStackPath -c /var/www/html/MonProjet/I-Courrier/custom/cs_I-Courrier/modules/notifications/batch/config/config.xml