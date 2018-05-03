<?php

if (!defined('WEBSHOP_LIB_DIR')) define('WEBSHOP_LIB_DIR', dirname(dirname(dirname( __FILE__ ))));

require_once WEBSHOP_LIB_DIR . '/iqsys/otpwebshop/util/RequestUtils.php';

/**
* Konfigur�ci�s param�terek kezel�s�nek t�mogat�sa.
* Egyar�nt kezeli a simpleshop �s multishop k�rnyezet szerint kialak�tott
* .config �llom�nyokat.
* 
* @version 4.0
*/
class ConfigUtils {

    /**
    * @desc Konfigur�ci�s param�ter kiolvas�sa simpleshop
    * vagy multishop k�rnyezet szerint kialak�tott
    * .config �llom�nyb�l
    * 
    * @param Array config az konfigur�ci�s f�jl tartalma
    * @param String paramName a keresett konfigur�ci�s param�ter neve
    * @return string a param�ter �rt�ke vagy null
    */
    function safeConfigParam($config, $paramName) {
        return array_key_exists($paramName, $config) ? $config[$paramName] : null;
    }
        
    /**
    * @desc Konfigur�ci�s param�ter kiolvas�sa simpleshop
    * vagy multishop k�rnyezet szerint kialak�tott
    * .config �llom�nyb�l
    * 
    * @param Array config az konfigur�ci�s f�jl tartalma
    * @param String paramName a keresett konfigur�ci�s param�ter neve
    * @param String posId a vonatkoz� posId
    * @param mixed config a keresett konfigur�ci�s param�ter alap�rtelmezett �rt�ke 
    *   (arra az esetre, ha nem l�tezik a megfelel� param�ter)
    * @return mixed a param�ter �rt�ke string vagy $defaultValue-val megegyez� t�pusban
    */
    function getConfigParam($config, $paramName, $posId = NULL, $defaultValue = NULL) {
        $paramValue = NULL;
        if (!is_null($posId)) {
            $paramValue = ConfigUtils::safeConfigParam($config, $paramName . '_' . $posId);
        }
        if (is_null($paramValue)) {
            $paramValue = ConfigUtils::safeConfigParam($config, $paramName);
        }
        if (is_null($paramValue)) {
            $paramValue = $defaultValue;
        }
        return $paramValue;
    }

    /**
    * @desc Logikai �rt�k� konfigur�ci�s param�ter kiolvas�sa simpleshop
    * vagy multishop k�rnyezet szerint kialak�tott
    * .config �llom�nyb�l
    * 
    * @param Array $config az konfigur�ci�s f�jl tartalma
    * @param String $paramName a keresett konfigur�ci�s param�ter neve
    * @param String $posId a vonatkoz� posId
    * @param mixed $defaultValue a keresett konfigur�ci�s param�ter alap�rtelmezett �rt�ke 
    *   (arra az esetre, ha nem l�tezik a megfelel� param�ter)
    * 
    * @return boolean a param�ter (vagy de$faultValue) logikai �rt�ke
    */
    function getConfigParamBool($config, $paramName, $posId = NULL, $defaultValue = NULL) {
        $paramValue = ConfigUtils::getConfigParam($config, $paramName, $posId = NULL, $defaultValue = NULL);
        return RequestUtils::getBooleanValue($paramValue);
    }
    
    /**
    * @desc A Java-ra jellemz� konfigur�ci�s param�ter helyettes�t�s 
    * megval�s�t�sa: a $paramValue string-ben szerepl� {key} �rt�kek
    * lecser�l�se a $values[key] sz�vegre.
    * 
    * @param string paramValue a helyettes�t�sre v�r� sz�veg
    * @param array a helyettes�t�sek key/value p�rban$
    * 
    * @return string a helyettes�tett sz�veg 
    */
    function substConfigValue($paramValue, $values) {
        foreach ($values as $key => $value) {
            $paramValue = str_replace("{" . $key ."}", $value, $paramValue);
        }
        return $paramValue;       
    }
    
}

?>