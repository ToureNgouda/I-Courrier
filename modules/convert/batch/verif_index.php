<?php

/**
 * Copyright Maarch since 2008 under licence GPLv3.
 * See LICENCE.txt file at the root folder for more details.
 * This file is part of Maarch software.
 *
 */

date_default_timezone_set('Europe/Paris');

// load the config and prepare to process
include('load_fill_stack.php');


/******************************************************************************/
$GLOBALS['zendIndex'] =
        $GLOBALS['processIndexes']->createZendIndexObject(
                $GLOBALS['path_to_lucene'], $GLOBALS['ProcessIndexesSize']
        );
unlink($GLOBALS['lckFile']);
exit($GLOBALS['zendIndex']->numDocs());

