<?php

class SchemaXRefs
	extends DOMDocument
{
	private $xpath;
	
    public function SchemaXRefs()
    {
        parent::__construct();
        
        $XRefs = $this->createElement('XRefs');
        $this->appendChild($XRefs);
        
        $this->xpath = new DOMXpath($this);
    }
    
	public function xpath($query) 
    {
        return $this->xpath->query($query, $this->documentElement);
    }
    
    public function getXRefNode($refNode)
    {
        $XRefs = $this->xpath('./XRefs/XRef[@path="'.$refNode->getNodePath().'"]');
        if($XRefs->length == 0) {
            $XRefNode = $this->createElement('XRef');
            $XRefNode->setAttribute('path', $refNode->getNodePath());
            $XRefNode->setAttribute('name', $refNode->tagName);
            $this->documentElement->appendChild($XRefNode);
        } else {
           $XRefNode = $XRefs->item(0);
        }
        return $XRefNode;
    }
        
    public function getXRefPath($refNode, $reqName)
    {
        $XRefPath = $this->xpath('//XRef[@path="'.$refNode->getNodePath().'"]/@'.$reqName);
        if($XRefPath->length == 0) {
            return null;  
        } else {
            return $XRefPath->item(0)->nodeValue;
        }
    }
    
    public function addXRefPath($refNode, $targetNode)
    {
        $XRefNode = $this->getXRefNode($refNode);
        $reqName = $targetNode->tagName;
        if($reqName == 'complexType' || $reqName == 'simpleType') $reqName = 'type';
        $XRefNode->setAttribute($reqName, $targetNode->getNodePath());
    }
    
    public function getXRefData($refNode, $reqName)
    {
        $XData = $this->xpath('//XRef[@path="'.$refNode->getNodePath().'"]/*[@name="'.$reqName.'"]');
        if($XData->length == 0) {
            return null;  
        } else {
            return $XData->item(0)->nodeValue;
        }
    }
    
    public function addXRefData($refNode, $targetName, $targetData)
    {
        $XRefNode = $this->getXRefNode($refNode);
        $XData = $this->createElement('XData', $targetData);
        $XData->setAttribute('name', $targetName);
        $XRefNode->appendChild($XData);
    }
    
    public function getXRefElement($refNode, $reqName)
    {
        $XElement = $this->xpath('//XRef[@path="'.$refNode->getNodePath().'"]/*[@name="'.$reqName.'"]/*');
        if($XElement->length == 0) {
            return null;  
        } else {
            return $XElement->item(0);
        }
    }
    
    public function addXRefElement($refNode, $targetName, $targetElement)
    {
        $XRefNode = $this->getXRefNode($refNode);
        $XData = $this->createElement('XData');
        $XData->setAttribute('name', $targetName);
        $XData->appendChild($this->importNode($targetElement,true));
        $XRefNode->appendChild($XData);
    }
    
    
}