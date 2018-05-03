<?php

/**
* @desc A fizet�s j�v��r�s tranzakci� v�laszadatait
* tartalmaz� bean / value object.
* 
* @version 4.0
*/
class WebShopJovairasValasz {

    /**
    * @desc A fizet�s j�v��r�s tranzakci� bank oldali egyedi tranzakci� azonos�t�ja.
    * 
    * @var string
    */
    var $mwTransactionId;

    /**
    * @desc A v�laszk�d a j�v��r�si tranzakci� �eredm�nye�. 
    * Sikeres j�v��r�s eset�n egy h�romjegy� numerikus k�d a 000-010 �rt�ktartom�nyb�l. 
    * Sikertelen v�s�rl�s eset�n, amennyiben a hiba (vagy elutas�t�s) a j�v��r�s m�velete sor�n t�rt�nik 
    * (a k�rtyavezet� rendszerben), szint�n egy h�romjegy� numerikus k�d jelenik meg, mely a 010 �rt�kn�l nagyobb. 
    * Egy�b hiba (vagy elutas�t�s) eset�n a v�laszk�d egy olyan alfanumerikus "olvashat�" k�d, 
    * mely a hiba (vagy elutas�t�s) ok�t adja meg.
    * 
    * @var string
    */
    var $valaszKod;
    
    /**
    * @desc Authoriz�ci�s k�d, a POS-os j�v��r�shoz tartoz� authoriz�ci�s enged�ly sz�m. 
    * Csak sikereses j�v��r�si tranzakci�k eset�n ker�l kit�lt�sre. 
    * Az adat a k�rtyavezet� rendszer v�lasza a  j�v��r�shoz tartoz� k�rtyaj�v��r�si m�velethez, 
    * egyfajta azonos�t� / hiteles�t� k�d, s mint ilyen, a bolt is megkapja v�laszadatk�nt.
    */
    var $authorizaciosKod;
    
    var $posId; // peti 2011-03-28
    var $azonosito; // peti 2011-03-28
    var $teljesites; // peti 2011-03-28
    

    function getMwTransactionId() {
        return $this->mwTransactionId;
    }

    function setMwTransactionId($mwTransactionId) {
        $this->mwTransactionId = $mwTransactionId;
    }

    //
    // peti 2011-03-28
    function getPosId() { 
        return $this->posId;
    }
    // peti 2011-03-28
    function setPosId($pos) {
        $this->posId = $pos;
    }
    //
    //
    // peti 2011-03-28
    function getAzonosito() {
        return $this->azonosito;
    }
    // peti 2011-03-28
    function setAzonosito($azon) {
        $this->azonosito = $azon;
    }

    // peti 2011-03-28
    function getTeljesites() {
        return $this->teljesites;
    }

    // peti 2011-03-28
    function setTeljesites($teljesites) {
        $this->teljesites = $teljesites;
    }



    //

    function getValaszKod() {
        return $this->valaszKod;
    }

    function setValaszKod($valaszKod) {
        $this->valaszKod = $valaszKod;
    }

    function getAuthorizaciosKod() {
        return $this->authorizaciosKod;
    }

    function setAuthorizaciosKod($authorizaciosKod) {
        $this->authorizaciosKod = $authorizaciosKod;
    }

}

?>
