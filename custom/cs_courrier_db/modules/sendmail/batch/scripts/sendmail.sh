#!/bin/bash
cd /var/www/html/I-CourrierV1.0/modules/sendmail/batch/
emailStackPath='/var/www/html/I-CourrierV1.0/modules/sendmail/batch/process_emails.php'
php $emailStackPath -c /var/www/html/I-CourrierV1.0/custom/cs_courrier_db/modules/sendmail/batch/config/config.xml