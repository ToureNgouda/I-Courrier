#!/bin/bash
Mypath='/var/www/html/oem/modules/convert/batch'
cd $Mypath
ConfigPath='/var/www/html/oem/modules/convert/batch/config'

rm convert_attachments_coll_config_only_indexes.lck
rm convert_attachments_coll_config_only_indexes_error.lck

php $Mypath/fill_stack.php -c $ConfigPath/config_only_indexes.xml -coll attachments_coll