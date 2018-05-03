<?php

if (!defined('WEBSHOP_LIB_DIR')) define('WEBSHOP_LIB_DIR', dirname(dirname(dirname( __FILE__ ))));

require_once WEBSHOP_LIB_DIR . '/iqsys/otpwebshop/factory/WAnswerOfWebShopFizetes.php';
require_once WEBSHOP_LIB_DIR . '/iqsys/otpwebshop/factory/WAnswerOfWebShopFizetesKetszereplos.php';
require_once WEBSHOP_LIB_DIR . '/iqsys/otpwebshop/factory/WAnswerOfWebShopTranzAzonGeneralas.php';
require_once WEBSHOP_LIB_DIR . '/iqsys/otpwebshop/factory/WAnswerOfWebShopTrazakcioLekerdezes.php';
require_once WEBSHOP_LIB_DIR . '/iqsys/otpwebshop/factory/WAnswerOfWebShopJovairas.php';
require_once WEBSHOP_LIB_DIR . '/iqsys/otpwebshop/factory/WAnswerOfWebShopKulcsLekerdezes.php';

/**
* A tranzakci�s v�lasz XML-eket reprezent�l� value object 
* �s azt el��ll�t� WAnswerOf... oszt�lyok �sszerendel�se.
* 
* @access private
* 
* @version 4.0
*/
class WSAnswerFactory  {

    /**
    * Adott tranzakci�s v�lasz XML-t reprezent�l� value object-et 
    * el��ll�t� WAnswerOf... objektum el��ll�t�sa.
    *  
    * @param string a tranzakci� k�dja
    * @return mixed a megfelel� WAnswerOf... objektum
    */
    function getAnswerFactory($workflowName) {
        switch ($workflowName) {
           case 'WEBSHOPTRANZAZONGENERALAS':
                return new WAnswerOfWebShopTranzAzonGeneralas();
           case 'WEBSHOPTRANZAKCIOLEKERDEZES':
                return new WAnswerOfWebShopTrazakcioLekerdezes();
           case 'WEBSHOPFIZETES':
                return new WAnswerOfWebShopFizetes();
           case 'WEBSHOPFIZETESKETSZEREPLOS':
                return new WAnswerOfWebShopFizetesKetszereplos();
           case 'WEBSHOPFIZETESLEZARAS':
                return new WAnswerOfWebShopFizetesKetszereplos();    
           case 'WEBSHOPFIZETESJOVAIRAS':
                return new WAnswerOfWebShopJovairas();
           case 'WEBSHOPKULCSLEKERDEZES':
                return new WAnswerOfWebShopKulcsLekerdezes();
        }        
        return NULL;
    }

}

?>