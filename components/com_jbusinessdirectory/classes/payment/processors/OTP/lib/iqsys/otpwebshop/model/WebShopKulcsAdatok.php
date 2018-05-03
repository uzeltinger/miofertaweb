<?php

/**
* Kulcslek�rdez� k�r�s v�lasz adatait tartalmaz�
* value object. A WEBSHOPKULCSLEKERDEZES tranzakci�s v�lasz xml
* feldolgoz�sakor keletkezik, v�lasz t�telenk�nt egy darab.
* 
* @author L�szl�k Zsolt
* @version 4.0
*/
class WebShopKulcsAdatok {

	/**
    * Lej�rat d�tuma, vagy hiba�zenet
    * 
    * @var string
    */
    var $lejarat;
    
    function getLejarat() {
        return $this->lejarat;
    }

    function setLejarat($lejarat) {
        $this->lejarat = $lejarat;
    }
}

?>