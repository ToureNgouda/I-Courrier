#!/bin/bash

cd /var/www/html/MonProjet/I-Courrier/modules/thumbnails/

php /var/www/html/MonProjet/I-Courrier/modules/thumbnails/create_tnl.php /var/www/html/MonProjet/I-Courrier/custom/cs_I-Courrier/modules/thumbnails/xml/config_batch_letterbox.xml

php /var/www/html/MonProjet/I-Courrier/modules/thumbnails/create_tnl.php /var/www/html/MonProjet/I-Courrier/custom/cs_I-Courrier/modules/thumbnails/xml/config_batch_attachments.xml