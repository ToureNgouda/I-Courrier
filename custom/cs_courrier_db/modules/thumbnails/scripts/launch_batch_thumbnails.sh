#!/bin/bash

cd /var/www/html/I-CourrierV1.0/modules/thumbnails/

php /var/www/html/I-CourrierV1.0/modules/thumbnails/create_tnl.php /var/www/html/I-CourrierV1.0/custom/cs_courrier_db/modules/thumbnails/xml/config_batch_letterbox.xml

php /var/www/html/I-CourrierV1.0/modules/thumbnails/create_tnl.php /var/www/html/I-CourrierV1.0/custom/cs_courrier_db/modules/thumbnails/xml/config_batch_attachments.xml