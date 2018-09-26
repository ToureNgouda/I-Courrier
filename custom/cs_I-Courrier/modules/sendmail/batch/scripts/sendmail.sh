#!/bin/bash
cd /var/www/html/MonProjet/I-Courrier/modules/sendmail/batch/
emailStackPath='/var/www/html/MonProjet/I-Courrier/modules/sendmail/batch/process_emails.php'
php $emailStackPath -c /var/www/html/MonProjet/I-Courrier/custom/cs_I-Courrier/modules/sendmail/batch/config/config.xml