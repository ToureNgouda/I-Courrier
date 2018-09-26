<?php

/**
 * Copyright Maarch since 2008 under licence GPLv3.
 * See LICENCE.txt file at the root folder for more details.
 * This file is part of Maarch software.
 *
 */

/**
 * @brief Batch to process the stack
 *
 * @file
 * @author  Laurent Giovannoni  <dev@maarch.org>
 * @date $date$
 * @version $Revision$
 * @ingroup convert
 */

/**
 * *****   LIGHT PROBLEMS without an error semaphore
 *  101 : Configuration file missing
 *  102 : Configuration file does not exist
 *  103 : Error on loading config file
 *  104 : SQL Query Error
 *  105 : a parameter is missing
 *  106 : Maarch_CLITools is missing
 *  107 : Stack empty for the request
 *  108 : There are still documents to be processed
 *  109 : An instance of the batch for the required collection already
 *        in progress
 *  110 : Problem with collection parameter
 *  111 : Problem with the php include path
 *  112 : Problem with the setup of esign
 * ****   HEAVY PROBLEMS with an error semaphore
 *  12  : Docserver type not found
 *  13  : Docserver not found
 *  14  : ...
 *  15  : Error to copy file on docserver
 *  16  : ...
 *  17  : Tmp dir not exists
 *  18  : Problem to create path on docserver, maybe batch number 
 *        already exists
 *  19  : Tmp dir not empty
 *  20  : ...
 *  21  : Problem to create directory on the docserver
 *  22  : Problem during transfert of file (fingerprint control)
 *  23  : Problem with compression
 *  24  : Problem with extract
 *  25  : Pb with fingerprint of the source
 *  26  : File deletion impossible
 *  27  : Resource not found
 *  28  : The docserver will be full at 95 percent
 *  29  : Error persists
 *  30  : Esign problem
 */

date_default_timezone_set('Europe/Paris');

/******************************************************************************/
/* beginning */
$GLOBALS['state'] = "CONTROL_STACK";
while ($GLOBALS['state'] <> "END") {
    if (isset($GLOBALS['logger'])) {
        $GLOBALS['logger']->write("STATE:" . $GLOBALS['state'], 'DEBUG');
    }
    switch($GLOBALS['state']) {
        /**********************************************************************/
        /*                          CONTROL_STACK                             */
        /* Checking the stack is empty for the required parameters            */
        /**********************************************************************/
        case "CONTROL_STACK" :
            $query = "select * from convert_stack"
                   . " where coll_id = ? and work_batch = ?";
            $stmt = Bt_doQuery(
                $GLOBALS['db'], 
                $query,
                array(
                    $GLOBALS['collection'],
                    $GLOBALS['wb']
                )
            );
            Bt_updateWorkBatch();
            $GLOBALS['logger']->write("Batch number:" . $GLOBALS['wb'], 'INFO');
            $query = "update convert_stack" 
                   . " set status = 'I' where status = 'W'"
                   . " and work_batch = ?";
            $stmt = Bt_doQuery($GLOBALS['db'], $query, array($GLOBALS['wb']));
            if ($GLOBALS['OnlyIndexes']) {
                //echo 'avant createZendIndexObject : ' . $GLOBALS['ProcessIndexesSize'] . PHP_EOL;
                $GLOBALS['zendIndex'] = 
                    $GLOBALS['processIndexes']->createZendIndexObject(
                        $GLOBALS['path_to_lucene'], $GLOBALS['ProcessIndexesSize']
                    );
                //$GLOBALS['zendIndex']->setMergeFactor(10);
                //print_r($GLOBALS['zendIndex']);
            }
            $GLOBALS['state'] = "GET_DOCSERVERS";
            break;
        /**********************************************************************/
        /*                          GET_DOCSERVERS                            */
        /* Get the list of the docservers of the collection                   */
        /**********************************************************************/
        case "GET_DOCSERVERS" :
            //retrieve docservers of the collection to process
            $query = "select * from docservers " 
                           . " where coll_id = ?";
            $stmt = Bt_doQuery(
                $GLOBALS['db2'], 
                $query, 
                array($GLOBALS['collection'])
            );
            $stmtCpt = $stmt;
            if ($stmtCpt->fetchObject()->docserver_id == '') {
                 Bt_exitBatch(13, 'Docserver not found');
                break;
            } else {
                while($docserversRecordset = $stmt->fetchObject()) {
                    $GLOBALS['docservers'][$docserversRecordset->docserver_id] 
                        = $GLOBALS['func']->object2array($docserversRecordset);
                }
            }
            $GLOBALS['state'] = "A_RECORD";
            break;
        /**********************************************************************/
        /*                          A_RECORD                                  */
        /* Process a record                                                   */
        /**********************************************************************/
        case "A_RECORD" :
            $GLOBALS['totalProcessedResources']++;
            $query = "select * from convert_stack "
                   . " where coll_id = ? "
                   . " and status = 'I' "
                   . " and work_batch = ? limit 1";
            $stmt = Bt_doQuery(
                $GLOBALS['db'], 
                $query,
                array(
                    $GLOBALS['collection'],
                    $GLOBALS['wb']
                )
            );
            $stackRecordset = $stmt->fetchObject();
            if (!($stackRecordset->res_id)) {
                if ($GLOBALS['OnlyIndexes']) {
                    $GLOBALS['processIndexes']->commitZendIndex($GLOBALS['zendIndex']);
                }
                $GLOBALS['state'] = "END";
                $GLOBALS['logger']->write('No more records to process', 'INFO');
                break;
            } else {
                $currentRecordInStack = array();
                $currentRecordInStack = $GLOBALS['func']->object2array(
                    $stackRecordset
                );
                $GLOBALS['logger']->write(
                    "current record:" . $currentRecordInStack['res_id'],
                    'DEBUG'
                );
                $GLOBALS['state'] = "CONVERT_IT";
            }
            break;
        /**********************************************************************/
        /*                          CONVERT_IT                                */
        /* Removes the address of the resource in the database                */
        /**********************************************************************/
        case "CONVERT_IT" :
            if ($GLOBALS['OnlyIndexes']) {
                $resultConvert = $GLOBALS['processIndexes']->fulltext(
                    array(
                        'collId'         => $GLOBALS['collection'], 
                        'resTable'       => $GLOBALS['table'], 
                        'adrTable'       => $GLOBALS['adrTable'], 
                        'resId'          => $currentRecordInStack['res_id'],
                        'tmpDir'         => $GLOBALS['tmpDirectory'],
                        'path_to_lucene' => $GLOBALS['path_to_lucene'],
                        'zendIndex'      => $GLOBALS['zendIndex']
                    )
                );
            } else {
                $resultConvert = $GLOBALS['processConvert']->convertAll(
                    array(
                        'collId'         => $GLOBALS['collection'], 
                        'resTable'       => $GLOBALS['table'], 
                        'adrTable'       => $GLOBALS['adrTable'], 
                        'resId'          => $currentRecordInStack['res_id'],
                        'tmpDir'         => $GLOBALS['tmpDirectory'],
                        'path_to_lucene' => $GLOBALS['path_to_lucene'],
                        //'createZendIndex'      => false
                    )
                );
            }
            
            $logInfo = "Problem with the record:" . $currentRecordInStack['res_id']
                    . " details " . $resultConvert['error'];
                    
            if ($resultConvert['status'] == '2') {
                $GLOBALS['logger']->write($logInfo, 'WARNING');

            } elseif ($resultConvert['status'] <> '0') {
                $GLOBALS['logger']->write($logInfo, 'ERROR');
            }
            $GLOBALS['state'] = "UPDATE_DATABASE";
            break;
        
        /**********************************************************************/
        /*                          UPDATE_DATABASE                           */
        /* Updating the database                                              */
        /**********************************************************************/
        case "UPDATE_DATABASE" :
            $query = "delete from convert_stack "
                   . " where coll_id = ? "
                   . " and res_id = ?";
            $stmt = Bt_doQuery(
                $GLOBALS['db'], 
                $query,
                array(
                    $GLOBALS['collection'],
                    $currentRecordInStack['res_id']
                )
            );
            $GLOBALS['state'] = "A_RECORD";
            break;
    }
}

$GLOBALS['logger']->write('End of process', 'INFO');
Bt_logInDataBase(
    $GLOBALS['totalProcessedResources'], 0, 'process without error'
);
Ds_washTmp($GLOBALS['tmpDirectory']);
unlink($GLOBALS['lckFile']);
exit($GLOBALS['exitCode']);
