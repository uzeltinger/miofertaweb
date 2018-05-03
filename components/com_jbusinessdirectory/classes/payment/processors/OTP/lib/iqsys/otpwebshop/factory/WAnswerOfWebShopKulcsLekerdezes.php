<?php

if (!defined('WEBSHOP_LIB_DIR')) define('WEBSHOP_LIB_DIR', dirname(dirname(dirname( __FILE__ ))));

require_once WEBSHOP_LIB_DIR . '/iqsys/otpwebshop/model/WebShopKulcsAdatokLista.php';
require_once WEBSHOP_LIB_DIR . '/iqsys/otpwebshop/model/WebShopKulcsAdatok.php';
require_once WEBSHOP_LIB_DIR . '/iqsys/otpwebshop/util/WebShopXmlUtils.php';

/**
* Kulcs lek�rdez�s v�lasz XML-j�nek feldolgoz�s�sa �s
* a megfelel� value object el��ll�t�sa.
* 
* @author L�szl�k Zsolt
* @version 4.0
*/
class WAnswerOfWebShopKulcsLekerdezes {

    /**
    * @desc A banki fel�let �ltal visszaadott sz�veges logikai
    * �rt�kb�l boolean t�pus� �rt�k el��ll�t�sa.
    * 
    * A k�pz�s m�dja:
    * "TRUE" sz�veges �rt�k => true logikai �rt�k
    * minden m�s �rt�k => false logikai �rt�k
    */
    function getBooleanValue($value) {
      $result = false;
      
      if (!is_null($value) && strcasecmp("TRUE", $value) == 0) {
        $result = true;
      }
      
      return $result;
    }

    /**
    * Kulcs lek�rdez�s v�lasz XML-j�nek feldolgoz�s�sa �s
    * a megfelel� value object el��ll�t�sa.
    * 
    * @param DomDocument $answer A tranzakci�s v�lasz xml
    * @return WebShopKulcsAdatokLista a v�lasz tartalma, 
    *         vagy NULL �res/hib�s v�lasz eset�n
    */
    function load($answer) {
    	    	
    	$webShopKulcsAdatokLista = new WebShopKulcsAdatokLista();
        $resultSet = WebShopXmlUtils::getNodeByXPath($answer, '//answer/resultset');
                
        if(!empty($resultSet)) {
        	$webShopKulcsAdatokLista->setPrivateKey(WebShopXmlUtils::getElementText($resultSet, 'privateKey'));
        }
        
        $recordList = WebShopXmlUtils::getNodeArrayByXPath($answer, '//answer/resultset/record');
        $lista = array();
        
        foreach ($recordList as $record) {
        	
            $webShopKulcsAdatok = new WebShopKulcsAdatok();
            
            $webShopKulcsAdatok->setLejarat(WebShopXmlUtils::getElementText($record, 'lejarat'));
            
            $lista[] = $webShopKulcsAdatok;
        }
        
        $webShopKulcsAdatokLista->setWebShopKulcsAdatok($lista);
        
        return $webShopKulcsAdatokLista;
    }

}

?>