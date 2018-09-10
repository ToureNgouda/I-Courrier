#!/bin/bash

cd /var/www/html/I-CourrierV1.0/modules/full_text/
php /var/www/html/I-CourrierV1.0/modules/full_text/lucene_full_text_engine.php /var/www/html/I-CourrierV1.0/custom/cs_courrier_db/modules/full_text/xml/config_batch_letterbox.xml
