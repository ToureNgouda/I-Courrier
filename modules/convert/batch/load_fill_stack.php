<?php

/**
 * Copyright Maarch since 2008 under licence GPLv3.
 * See LICENCE.txt file at the root folder for more details.
 * This file is part of Maarch software.
 *
 */

/**
 * @brief Library to convert
 *
 * @file
 * @author  Laurent Giovannoni  <dev@maarch.org>
 * @date $date$
 * @version $Revision$
 * @ingroup convert
 */

/**
* @brief Class to include the file error
*
* @ingroup convert
*/
class IncludeFileError extends Exception
{
    public function __construct($file) 
    {
        $this->file = $file;
        parent :: __construct('Include File \'$file\' is missing!', 1);
    }
}

try {
    include('Maarch_CLITools/ArgsParser.php');
    include('LoggerLog4php.php');
    include('Maarch_CLITools/FileHandler.php');
    include('Maarch_CLITools/ConsoleHandler.php');
} catch (IncludeFileError $e) {
    echo 'Maarch_CLITools required ! \n (pear.maarch.org)\n';
    exit(106);
}
include('batch_tools.php');
// Globals variables definition
$state = '';
$configFile = '';
$MaarchDirectory = '';
$batchDirectory = '';
$batchName = 'convert';
$TmpDirectory = '';
$table = '';
$adrTable = '';
$view = '';
$coll = '';
$creationDateClause = '';
$currentStep = '';
$docservers = Array();
$docserverSourcePath = '';
$docserverSourceFingerprint = '';
$databasetype = '';
$exitCode = 0;
$running_date = date('Y-m-d H:i:s');
$func = '';
$db = '';
$db2 = '';
$db3 = '';
$docserverControler = '';
$wb = '';
$wbCompute = '';
$stackSizeLimit = '';
$docserversFeatures = array();
$lckFile = '';
$errorLckFile = '';
$totalProcessedResources = 0;
$apacheUserAndGroup =  '';
$regExResId = 'false';
$startDateRecovery = 'false';
$currentDate = 'false';
$endCurrentDate = 'false';
$currentMonthOnly = 'false';
$OnlyIndexes = 'false';
$ProcessIndexesSize = 1000;
$whereRegex = '';
$log4PhpEnabled = false;

// Defines scripts arguments
$argsparser = new ArgsParser();
// The config file
$argsparser->add_arg(
    'config', 
    array(
        'short' => 'c',
        'long' => 'config',
        'mandatory' => true,
        'help' => 'Config file path is mandatory.',
    )
);
// The res collection target
$argsparser->add_arg(
    'collection', 
    array(
        'short' => 'coll',
        'long' => 'collection',
        'mandatory' => true,
        'help' => 'Collection target is mandatory.',
    )
);
// The path of the log directory
$argsparser->add_arg(
    'logs', 
    array(
        'short' => 'logs',
        'long' => 'logs',
        'mandatory' => false,
        'help' => '',
    )
);
// Parsing script options
try {
    $options = $argsparser->parse_args($GLOBALS['argv']);
    // If option = help then options = false and the script continues ...
    if ($options == false) {
        exit(0);
    }
} catch (MissingArgumentError $e) {
    if ($e->arg_name == 'config') {
        $GLOBALS['logger']->write('Configuration file missing', 'ERROR', 101);
        exit(101);
    }
    if ($e->arg_name == 'collection') {
        $GLOBALS['logger']->write('Collection missing', 'ERROR', 1);
        exit(105);
    }
}
// Log management
$GLOBALS['logger'] = new Logger4Php();
$GLOBALS['logger']->set_threshold_level('DEBUG');
$console = new ConsoleHandler();
$GLOBALS['logger']->add_handler($console);
if (!empty($options['logs'])) {
    $logFile = $options['logs'] . '/' . date('Y-m-d_H-i-s') . '.log';
} else {
    $logFile = 'logs' . '/' . date('Y-m-d_H-i-s') . '.log';
}
$file = new FileHandler($logFile);
$GLOBALS['logger']->add_handler($file);
$GLOBALS['logger']->write('STATE:INIT', 'INFO');
$txt = '';
foreach (array_keys($options) as $key) {
    if (isset($options[$key]) && $options[$key] == false) {
        $txt .= $key . '=false,';
    } else {
        $txt .= $key . '=' . $options[$key] . ',';
    }
}
$GLOBALS['logger']->write($txt, 'DEBUG');
$GLOBALS['configFile'] = $options['config'];
$GLOBALS['collection'] = $options['collection'];
$GLOBALS['logger']->write($txt, 'INFO');
// Tests existence of config file
if (!file_exists($GLOBALS['configFile'])) {
    $GLOBALS['logger']->write('Configuration file ' . $GLOBALS['configFile'] 
                              . ' does not exist', 'ERROR', 102);
    exit(102);
}
// Loading config file
$GLOBALS['logger']->write('Load xml config file:' . $GLOBALS['configFile'], 
                          'INFO');
$xmlconfig = simplexml_load_file($GLOBALS['configFile']);
if ($xmlconfig == FALSE) {
    $GLOBALS['logger']->write('Error on loading config file:' 
                              . $GLOBALS['configFile'], 'ERROR', 103);
    exit(103);
}
// Load the config vars
$CONFIG = $xmlconfig->CONFIG;
$lang = (string) $CONFIG->Lang;
$GLOBALS['MaarchDirectory'] = (string) $CONFIG->MaarchDirectory;
$GLOBALS['batchDirectory'] = $GLOBALS['MaarchDirectory'] . 'modules/convert/batch';
$GLOBALS['tmpDirectoryRoot'] = (string) $CONFIG->TmpDirectory;
$MaarchApps = (string) $CONFIG->MaarchApps;
$logLevel = (string) $CONFIG->LogLevel;
$GLOBALS['logger']->set_threshold_level($logLevel);
$DisplayedLogLevel = (string) $CONFIG->DisplayedLogLevel;
$GLOBALS['apacheUserAndGroup'] = (string) $CONFIG->ApacheUserAndGroup;
$GLOBALS['stackSizeLimit'] = (string) $CONFIG->StackSizeLimit;
$GLOBALS['databasetype'] = (string) $xmlconfig->CONFIG_BASE->databasetype;
$GLOBALS['unoconvPath'] = (string) $CONFIG->UnoconvPath;
$GLOBALS['openOfficePath'] = (string) $CONFIG->OpenOfficePath;
$GLOBALS['unoconvOptions'] = (string) $CONFIG->UnoconvOptions;

$GLOBALS['regExResId'] = (string) $CONFIG->RegExResId;
$GLOBALS['startDateRecovery'] = (string) $CONFIG->StartDateRecovery;
$GLOBALS['currentMonthOnly'] = (string) $CONFIG->CurrentMonthOnly;
$GLOBALS['OnlyIndexes'] = (string) $CONFIG->OnlyIndexes;

if ($GLOBALS['OnlyIndexes'] == 'true') {
    $GLOBALS['OnlyIndexes'] = true;
} else {
    $GLOBALS['OnlyIndexes'] = false;
}

$GLOBALS['ProcessIndexesSize'] = (string) $CONFIG->ProcessIndexesSize;

if ($GLOBALS['regExResId'] <> 'false') {
    if ($GLOBALS['databasetype'] == 'POSTGRESQL') {
        $GLOBALS['whereRegex'] = " and cast(res_id as character varying(255)) ~ '" 
            . $GLOBALS['regExResId'] . "' ";
    } elseif ($GLOBALS['databasetype'] == 'ORACLE') {
        $GLOBALS['whereRegex'] = " and REGEXP_LIKE (to_char(res_id), '" 
            . $GLOBALS['regExResId'] . "') ";
    }
}

$arrayOfInputs = array();
$GLOBALS['convertFormats'] = array();
$i = 0;
foreach ($xmlconfig->CONVERT as $convert) {
    $outputFormat = (string) $convert->OutputFormat;
    $arrayOfInputs = explode(',', (string) $convert->InputFormat);
    $cptInputs = count($arrayOfInputs);
    for ($j=0;$j<$cptInputs;$j++) {
        if (!empty($GLOBALS['convertFormats'][$arrayOfInputs[$j]])) {
            $GLOBALS['convertFormats'][$arrayOfInputs[$j]] .= "_" . $outputFormat;
        } else {
            $GLOBALS['convertFormats'][$arrayOfInputs[$j]] .= $outputFormat;
        }
    }
    $i++;
}

//var_dump($GLOBALS['convertFormats']);

$i = 0;
foreach ($xmlconfig->COLLECTION as $col) {
    $GLOBALS['collections'][$i] = array (
        'id'             => (string) $col->Id, 
        'table'          => (string) $col->Table, 
        'view'           => (string) $col->View, 
        'adr'            => (string) $col->Adr,
        'path_to_lucene' => (string) $col->path_to_lucene_index
    );
    if ($GLOBALS['collections'][$i]['id'] == $GLOBALS['collection']) {
        $GLOBALS['table']          = $GLOBALS['collections'][$i]['table'];
        $GLOBALS['adrTable']       = $GLOBALS['collections'][$i]['adr'];
        $GLOBALS['view']           = $GLOBALS['collections'][$i]['view'];
        $GLOBALS['path_to_lucene'] = $GLOBALS['collections'][$i]['path_to_lucene'];
    }
    $i++;
}

set_include_path(get_include_path() . PATH_SEPARATOR 
    . $GLOBALS['MaarchDirectory']);
//log4php params
$log4phpParams = $xmlconfig->LOG4PHP;
if ((string) $log4phpParams->enabled == 'true') {
    $GLOBALS['logger']->set_log4PhpLibrary(
        $GLOBALS['MaarchDirectory'] 
            . 'apps/maarch_entreprise/tools/log4php/Logger.php'
    );
    $GLOBALS['logger']->set_log4PhpLogger((string) $log4phpParams->Log4PhpLogger);
    $GLOBALS['logger']->set_log4PhpBusinessCode((string) $log4phpParams->Log4PhpBusinessCode);
    $GLOBALS['logger']->set_log4PhpConfigPath((string) $log4phpParams->Log4PhpConfigPath);
    $GLOBALS['logger']->set_log4PhpBatchName('convert');
}

if ($GLOBALS['table'] == '' 
    || $GLOBALS['adrTable'] == '' 
    || $GLOBALS['view'] == ''
) {
    $GLOBALS['logger']->write('Collection:' . $GLOBALS['collection'].' unknow'
                              , 'ERROR', 110);
    exit(110);
}
if (file_exists($GLOBALS['MaarchDirectory'] . 'modules/convert/lang/' . $lang . '.php')
) {
    include($GLOBALS['MaarchDirectory'] . 'modules/convert/lang/' . $lang . '.php');
}
/*if ($logLevel == 'DEBUG') {
    error_reporting(E_ALL);
}*/
$GLOBALS['logger']->change_handler_log_level($file, $logLevel);
$GLOBALS['logger']->change_handler_log_level($console, $DisplayedLogLevel);
unset($xmlconfig);

// Include library
try {
    Bt_myInclude($GLOBALS['MaarchDirectory'] . 'vendor/autoload.php');
    Bt_myInclude($GLOBALS['MaarchDirectory'] . 'core/class/class_functions.php');
    Bt_myInclude($GLOBALS['MaarchDirectory'] . 'core/class/class_db.php');
    Bt_myInclude($GLOBALS['MaarchDirectory'] . 'core/class/class_db_pdo.php');
    Bt_myInclude($GLOBALS['MaarchDirectory'] . 'core/class/class_core_tools.php');
    Bt_myInclude($GLOBALS['MaarchDirectory'] . 'core/core_tables.php');
    Bt_myInclude($GLOBALS['MaarchDirectory'] . 'core/class/docservers_controler.php');
    Bt_myInclude($GLOBALS['MaarchDirectory'] . 'core/docservers_tools.php');
    Bt_myInclude($GLOBALS['MaarchDirectory'] . 'core/class/docserver_types_controler.php');
    //Bt_myInclude($GLOBALS['MaarchDirectory'] . 'modules/convert/services/ManageConvert.php');
} catch (IncludeFileError $e) {
    $GLOBALS['logger']->write(
        'Problem with the php include path:' 
        . get_include_path(), 'ERROR', 111
    );
    exit(111);
}
if (!is_dir($GLOBALS['tmpDirectoryRoot'])) {
    mkdir($GLOBALS['tmpDirectoryRoot']);
    // echo PHP_EOL.'tmpDirectoryRoot '.$GLOBALS['tmpDirectoryRoot'].PHP_EOL;
    // $GLOBALS['logger']->write(
    //     'Problem with the tmp dir:' . $GLOBALS['tmpDirectoryRoot'], 'ERROR', 17
    // );
    // exit(17);
}

$coreTools = new core_tools();
$coreTools->load_lang($lang, $GLOBALS['MaarchDirectory'], $MaarchApps);
session_start();
$_SESSION['modules_loaded']    = array();
$_SESSION['user']['UserId']    = 'BOT_CONVERT';
$GLOBALS['func']               = new functions();
$GLOBALS['db']                 = new Database($GLOBALS['configFile']);
$GLOBALS['db2']                = new Database($GLOBALS['configFile']);
$GLOBALS['db3']                = new Database($GLOBALS['configFile']);
$GLOBALS['dbLog']              = new Database($GLOBALS['configFile']);
$GLOBALS['docserverControler'] = new docservers_controler();
$GLOBALS['processConvert']     = new \Convert\Controllers\ProcessManageConvertController($GLOBALS['openOfficePath']);
$GLOBALS['processIndexes']     = new \Convert\Controllers\ProcessFulltextController();

$configFileName = basename($GLOBALS['configFile'], '.xml');
$GLOBALS['errorLckFile'] = $GLOBALS['batchDirectory'] . '/' 
                         . $GLOBALS['batchName'] . '_' . $GLOBALS['collection']  
                         . '_' .  $configFileName
                         . '_error.lck';
$GLOBALS['lckFile'] = $GLOBALS['batchDirectory'] . '/' 
                    . $GLOBALS['batchName'] . '_' . $GLOBALS['collection'] 
                    . '_' . $configFileName
                    . '.lck';
if (file_exists($GLOBALS['errorLckFile'])) {
    $GLOBALS['logger']->write(
        'Error persists, please solve this before launching a new batch', 
        'ERROR', 29
    );
    exit(29);
}
if (file_exists($GLOBALS['lckFile'])) {
    $GLOBALS['logger']->write(
        'An instance of the batch :' . $GLOBALS['batchName'] . '_' 
            . $GLOBALS['collection'] . '_' . $configFileName 
            . ' is already in progress',
        'ERROR', 109
    );
    exit(109);
}

if ($GLOBALS['currentMonthOnly'] == 'true') {
    $GLOBALS['currentDate'] = date(
        "d/m/Y", 
        mktime(0, 0, 0, date("m"), 1, date("Y"))
    );
    Bt_getEndCurrentDateToProcess();
    Bt_computeCreationDateClause();
    $GLOBALS['logger']->write('current begin date to process : ' 
        . $GLOBALS['currentDate'], 'INFO');
} elseif ($GLOBALS['startDateRecovery'] <> 'false') {
    Bt_getCurrentDateToProcess();
    Bt_updateCurrentDateToProcess();
    Bt_getEndCurrentDateToProcess();
    Bt_computeCreationDateClause();
    $GLOBALS['logger']->write('current begin date to process : ' 
        . $GLOBALS['currentDate'], 'INFO');
}

$semaphore = fopen($GLOBALS['lckFile'], 'a');
fwrite($semaphore, '1');
fclose($semaphore);
Bt_getWorkBatch();
$GLOBALS['wb'] = rand() . $GLOBALS['wbCompute'];
Bt_updateWorkBatch();
$GLOBALS['logger']->write('Batch number:' . $GLOBALS['wb'], 'INFO');
$GLOBALS['tmpDirectory'] = $GLOBALS['tmpDirectoryRoot'] . '/' 
                         . $GLOBALS['wb'] . '/';
if (!is_dir($GLOBALS['tmpDirectory'])) {
    mkdir($GLOBALS['tmpDirectory'], 0777);
}
