<?php

if (!defined('WEBSHOP_LIB_DIR')) define('WEBSHOP_LIB_DIR', dirname(dirname(dirname( __FILE__ ))));

require_once WEBSHOP_LIB_DIR . '/iqsys/otpwebshop/factory/WAnswerOfWebShopFizetes.php';
require_once WEBSHOP_LIB_DIR . '/iqsys/otpwebshop/factory/WAnswerOfWebShopFizetesKetszereplos.php';
require_once WEBSHOP_LIB_DIR . '/iqsys/otpwebshop/factory/WAnswerOfWebShopTranzAzonGeneralas.php';
require_once WEBSHOP_LIB_DIR . '/iqsys/otpwebshop/factory/WAnswerOfWebShopTrazakcioLekerdezes.php';
require_once WEBSHOP_LIB_DIR . '/iqsys/otpwebshop/factory/WAnswerOfWebShopJovairas.php';
require_once WEBSHOP_LIB_DIR . '/iqsys/otpwebshop/factory/WAnswerOfWebShopKulcsLekerdezes.php';

/**
* A tranzakciós válasz XML-eket reprezentáló value object 
* és azt elõállító WAnswerOf... osztályok összerendelése.
* 
* @access private
* 
* @version 4.0
*/
class WSAnswerFactory  {

    /**
    * Adott tranzakciós válasz XML-t reprezentáló value object-et 
    * elõállító WAnswerOf... objektum elõállítása.
    *  
    * @param string a tranzakció kódja
    * @return mixed a megfelelõ WAnswerOf... objektum
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