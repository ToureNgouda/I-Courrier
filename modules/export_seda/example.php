require_once __DIR__.'/DOMTemplateProcessor.php';
$DOMTemplate = new DOMDocument();$DOMTemplate->load(__DIR__.'/test.xml');$DOMTemplateProcessor = new DOMTemplateProcessor($DOMTemplate);$DOMTemplateProcessor->setSource('bar', 'bazbull');$DOMTemplateProcessor->merge();$xml = $DOMTemplate->saveXML();