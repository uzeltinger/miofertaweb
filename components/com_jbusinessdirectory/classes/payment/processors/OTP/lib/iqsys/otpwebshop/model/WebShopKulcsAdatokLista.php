<?php

/**
* A WEBSHOPKULCSLEKERDEZES tranzakci�s v�lasz xml-t
* reprezent�l� value object.
* 
* @author L�szl�k Zsolt
* @version 4.0
*/
class WebShopKulcsAdatokLista {

    /**
    * Gener�lt kulcs adatok zip-elt, base64 k�dolt form�tumban
    * 
    * @var string
    */
    var $privateKey;

    /**
    * A lek�rdezett kulcsadatokat reprezent�l�
    * WebShopKulcsAdatok objektumok list�ja.
    * 
    * @var array
    */
    var $webShopKulcsAdatok;

    function getPrivateKey() {
        return $this->privateKey;
    }

    function setPrivateKey($privateKey) {
        $this->privateKey = $privateKey;
    }

    function getWebShopKulcsAdatok() {
        return $this->webShopKulcsAdatok;
    }

    /**
    * @desc Kulcs adatok t�mb t�rol�sa
    */
    function setWebShopKulcsAdatok(&$webShopKulcsAdatok) {
        $this->webShopKulcsAdatok = $webShopKulcsAdatok;
    }

}

?>