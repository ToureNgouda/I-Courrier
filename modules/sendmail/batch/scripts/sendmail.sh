#!/bin/sh
cd /var/www/MaarchCourrier/modules/sendmail/batch/
emailsPath='/var/www/MaarchCourrier/modules/sendmail/batch/process_emails.php'
php $emailsPath -c /var/www/MaarchCourrier/modules/sendmail/batch/config/config.xml

