#!/bin/bash
file='/var/www/MaarchCourrier/modules/full_text/lucene_full_text_engine.php'
cd /var/www/MaarchCourrier/modules/full_text/
php $file /var/www/MaarchCourrier/modules/full_text/xml/config_batch_letterbox.xml
