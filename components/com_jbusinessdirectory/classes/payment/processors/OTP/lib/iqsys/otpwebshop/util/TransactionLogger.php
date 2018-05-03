<?php

if (!defined('WEBSHOP_LIB_DIR')) define('WEBSHOP_LIB_DIR', dirname(dirname(dirname( __FILE__ ))));


require_once WEBSHOP_LIB_DIR . '/iqsys/otpwebshop/model/WebShopFizetesAdatokLista.php';
require_once WEBSHOP_LIB_DIR . '/iqsys/otpwebshop/model/WebShopFizetesAdatok.php';
require_once WEBSHOP_LIB_DIR . '/iqsys/otpwebshop/model/WebShopFizetesValasz.php';
require_once WEBSHOP_LIB_DIR . '/iqsys/otpwebshop/model/WebShopJovairasValasz.php';
require_once WEBSHOP_LIB_DIR . '/iqsys/otpwebshop/util/WebShopXmlUtils.php';

/**
 * Tranzakciуnkйnti naplу fбjl kйszнtйse a hбrom- йs kйtszereplхs
 * fizetйsi tranzakciуkhoz.
 * 
 * @version 4.0
 */

class TransactionLogger {

    var $logDir;
    var $logDirSuccess;
    var $logDirFailed;
    
    var $logger;
    
    /**
     * Tranzakciуs naplуzу lйtrehozбsa
     */
    function TransactionLogger($logDir, $logger) {

        reset($logDir);
        $this->logDir = (is_null(current($logDir)) ? "" : trim(current($logDir)));
        $this->logDirSuccess = (next($logDir) && !is_null(current($logDir)) 
            ? trim(current($logDir)) : $this->logDir);
        $this->logDirFailed = (next($logDir) && !is_null(current($logDir)) 
            ? trim(current($logDir)) : $this->logDir);
        $this->logger = $logger;
    }
    
    /**
     * @desc A tranzakciуs log бllomбny nevйnek йs elйrйsi ъtvonalбnak
     * meghatбrozбsa. Az бllomбny neve utal a tranzakciу azonosнtуjбra
     * йs az indнtу bolt azonosнtуjбra. 
     * Ha tranzakciу indнtбsrуl van szу, ъj fбjlnйv kerьl kйpzйsre, 
     * esetleges _x postfix generбlбsбval, ahol x egйsz szбm.
     * Ha tranzakciу befejezхdйsrхl van szу, akkor a tranzakciу 
     * indнtбsбhoz tartozу adatokat tartalmazу fбjl neve kerьl meghatбrozбsra. 
     * 
     * @param string $azonosito fizetйsi tranzakciу azonosнtу
     * @param string $posId	shopId
     * @param string $logFileName a lйtrehozandу log file neve. Null, ha a 
     *               metуdus hatбrozza meg az $azonosito йs $posId alapjбn
     * @param strgin $uj igaz, ha ъj fбjl lйtrehozбsбrуl van szу, pйldбl 
     *                   a fizetйsi tranzakciу indнtбsбnбl vagy 
     *                   mozgatбsnбl	
     * @param string $logDir a cйlkцnyvtбr neve
     * @return string a "generбlt" fбjl nйv
     */
    function getLogFileName($azonosito, $posId, $logFileName, $uj, $logDir) {
        
    	/* Kцnyvtбr lйrtehozбsa, ha szьksйges */
        if (file_exists($logDir)) {
        	if (!is_dir($logDir)) {
        		$this->logger->warn(
                    "Ervenytelen tranzakcio log konyvtar: " . $logDir);
            }
        }
        else {
            $this->logger->warn(
                "A tranzakcio log konyvtar nem letezik: " . $logDir);

            $success = mkdir($logDir, 0710);
            if (!success) {
                $this->logger->warn(
                    "A tranzakcio log konyvtar nem hozhato letre: " . $logDir);
            }            
        }
        
        if (is_null($logFileName)) {
            $logFileName = 
                $posId . "_" . $azonosito . ".log";
        }
                
        /* Fel kell kйszьlni arra, hogy az adott nйven mбr lйtezik fбjl */
        $logFile = $logDir . "/" . $logFileName;
        $i = 0;
        while ($uj && file_exists($logFile)) {
        	$i++;
            $logFile = $logDir . "/" . $logFileName . "_" . $i;
        }

        return $i == 0 ? $logFileName : $logFileName . "_" . $i;
    }
   
    /**
     * Objektum string reprezentбlбsa.
     * Annyiban tйr el a toString() бltal visszaadott adattуl, hogy
     * null йrtйk esetйn ьres string a visszatйrйsi йrtйk, йs nem
     * a "null" szцveg
     * 
     * @param value йrtйk
     * @return string reprezentбciу
     */
    function nvl($value) {
        return (is_null($value) ? "" : $value);
    }

    
    /**
    * @desc Szцveg kiнrбsa fбjlba.
    */
    function filePutContents($fileName, $data, $flags, $fileDir) {
        $resource=@fopen($fileDir . "/" . $fileName, $flags);   
        if (!$resource) {
            return false;
        }
        else {
            $success = fwrite($resource, $data);
            fclose($resource);
            return $success;   
        }
    }
   
  /**
   * @desc Hбromszereplхs fizetйsi tranzakciу indнtбsбnak naplуzбsa.
   *
   * @param posId webshop azonosнtу
   * @param azonosito fizetйsi tranzakciу azonosнtу
   * @param osszeg fizetendц цsszeg 
   * @param devizanem fizetendц devizanem
   * @param nyelvkod a megjelenнtendц vevц oldali felьlet nyelve
   * @param nevKell a megjelenнtendц vevц oldali felьleten be kell kйrni a vevц nevйt
   * @param orszagKell a megjelenнtendц vevц oldali felьleten be kell kйrni a vevц cнmйnek "orszбg rйszйt"
   * @param megyeKell a megjelenнtendц vevц oldali felьleten be kell kйrni a vevц cнmйnek "megye rйszйt"
   * @param telepulesKell a megjelenнtendц vevц oldali felьleten be kell kйrni a vevц cнmйnek "telepьlйs rйszйt"
   * @param iranyitoszamKell a megjelenнtendц vevц oldali felьleten be kell kйrni  a vevц cнmйnek "irбnyнtуszбm rйszйt"
   * @param utcaHazszamKell a megjelenнtendц vevц oldali felьleten be kell  kйrni a vevц cнmйnek "utca/hбzszбm rйszйt"
   * @param mailCimKell a megjelenнtendц vevц oldali felьleten be kellыkйrni a vevц e-mail cнmйt
   * @param kozlemenyKell a megjelenнtendц vevц oldali felьleten fel kell kнnбlni a kцzlemйny megadбsбnak lehetцsйgйt
   * @param vevoVisszaigazolasKell a tranzakciу eredmйnyйt a vevц oldalon meg kell jelenнteni (azaz nem a backURL-re kell irбnyнtani)
   * @param ugyfelRegisztracioKell ha a regisztraltUgyfelId йrtйke nem ьres, akkor megadja, hogy a megadott azonosнtу ъjonnan regisztrбlandу-e, vagy mбr regisztrбlбsra kerьlt az OTP Internetes Fizetх felьletйn. 
   * @param regisztraltUgyfelId az OTP fizetхfelьleten regisztrбlandу vagy regisztrбlt  ьgyfйl azonosнtу kуdja. 
   * @param shopMegjegyzes a webshop megjegyzйse a tranzakciуhoz a vevц rйszйre
   * @param backURL a tranzakciу vйgrehajtбsa utбn erre az internet cнmre kell irбnyнtani a vevц oldalon az ьgyfelet (ha a vevoVisszaigazolasKell hamis)
   * @param $zsebAzonosito a cafeteria kбrtya zseb azonosнtуja
   * @param string $logFileName a lйtrehozandу log file neve. Null, ha a metуdus hatбrozza meg az $azonosito йs $posId alapjбn
   * 
   * @access public
   */
    function logHaromszereplosFizetesInditas(
            $posId,
            $azonosito,
            $osszeg,
            $devizanem,
            $nyelvkod,
            $nevKell,
            $orszagKell,
            $megyeKell,
            $telepulesKell,
            $iranyitoszamKell,
            $utcaHazszamKell,
            $mailCimKell,
            $kozlemenyKell,
            $vevoVisszaigazolasKell,
            $ugyfelRegisztracioKell,
            $regisztraltUgyfelId,
            $shopMegjegyzes,
            $backURL,
            $ketlepcsosFizetes,
            $zsebAzonosito,
            $logFileName = null) {
    	
       if (!is_null($azonosito) && (trim($azonosito) != "")) {
            $logFileName = $this->getLogFileName($azonosito, $posId, $logFileName, true, $this->logDir); 

            $logContent = "Haromszereplos fizetesi tranzakcio" . "\n"
                . "\nInditas: " . date(LOG_DATE_FORMAT, time()) . "\n" 
                . "\nIndito adatok" . "\n"
                . "  posId: " . $posId . "\n"
                . "  azonosito: " . $azonosito . "\n"
                . "  osszeg: " . $osszeg . "\n"
                . "  devizanem: " . $devizanem . "\n"
                . "  nyelvkod: " . $nyelvkod . "\n"
                . "  nevKell: " . $nevKell . "\n"
                . "  orszagKell: " . $orszagKell . "\n"
                . "  megyeKell: " . $megyeKell . "\n"
                . "  telepulesKell: " . $telepulesKell . "\n"
                . "  iranyitoszamKell: " . $iranyitoszamKell . "\n"
                . "  utcaHazszamKell: " . $utcaHazszamKell . "\n"
                . "  mailCimKell: " . $mailCimKell . "\n"
                . "  kozlemenyKell: " . $kozlemenyKell . "\n"
                . "  vevoVisszaigazolasKell: " . $vevoVisszaigazolasKell . "\n"
                . "  ugyfelRegisztracioKell: " . $ugyfelRegisztracioKell . "\n"
                . "  regisztraltUgyfelId: " . $regisztraltUgyfelId . "\n"
                . "  shopMegjegyzes: " . $shopMegjegyzes . "\n"
                . "  backURL: " . $backURL . "\n"
                . "  zsebAzonosito: " . $zsebAzonosito . "\n"
                . "  ketlepcsosFizetes: " . $ketlepcsosFizetes . "\n";	

            if (!$this->filePutContents($logFileName, $logContent, "w+b", $this->logDir)) {
                $this->logger->warn("Hiba tortent a tranzakcios naplo fajl letrehozasa " 
                    . "vagy irasa kozben: " . $logFileName);
            }
        }
        else {
        	$this->logger->warn("A tranzakcio adatai nem naplozhatoak, a fizetesi azonosito nincs megadva.");
        }
    }

   /**
    * Hбromszereplхs fizetйsi tranzakciу befejezхdйsйnek naplуzбsa.
    *
    * @param string $azonosito fizetйsi tranzakciу azonosнtу
    * @param string $posId shopID bolt azonosнt
    * @param WResponse $response a fizetйsi tranzakciу vбlasza
    * @param boolean $moveFile mozgassa-e a fбjlt a vйgrehajtбs utбn
    * @param string $logFileName a lйtrehozandу log file neve. 
    *        Null, ha a metуdus hatбrozza meg az $azonosito йs $posId alapjбn
    */
    function logHaromszereplosFizetesBefejezes(
            $azonosito,
            $posId,
    		$response,
            $moveFile = true,
            $logFileName = null) {

       if (is_null($response) || !$response->isFinished()) {
            $this->logger->warn(
                "A tranzakcio adatai nem naplozhatoak, a valasz ures: " . $azonosito);
       }
       else if (!is_null($azonosito) && (trim($azonosito) != "")) {
            $logFileName = $this->getLogFileName($azonosito, $posId, $logFileName, false, $this->logDir); 

            $logContent = 
                "\nBefejezes: " . date(LOG_DATE_FORMAT, time()) . "\n" 
                . "\nValasz: " . implode(", " , $response->getMessages()) . "\n";

            $fizetesAdatok = $response->getAnswer();

            if (!is_null($fizetesAdatok)) {

                $logContent .=
                    "\nValasz adatok" . "\n"
                    . "  posId: " . $fizetesAdatok->getPosId() . "\n"
                    . "  azonosito: " . $fizetesAdatok->getAzonosito() . "\n"
                    . "  posValaszkod: " . $fizetesAdatok->getPosValaszkod() . "\n"
                    . "  authorizaciosKod: " . $fizetesAdatok->getAuthorizaciosKod() . "\n"
                    . "  statuszKod: " . $fizetesAdatok->getStatuszKod() . "\n"
                    . "  teljesites: " . $fizetesAdatok->getTeljesites() . "\n"
                    . "  nev: " . $fizetesAdatok->getNev() . "\n"
                    . "  orszag: " . $fizetesAdatok->getOrszag() . "\n"
                    . "  megye: " . $fizetesAdatok->getMegye() . "\n"
                    . "  varos: " . $fizetesAdatok->getVaros() . "\n"
                    . "  iranyitoszam: " . $fizetesAdatok->getIranyitoszam() . "\n"
                    . "  utcaHazszam: " . $fizetesAdatok->getUtcaHazszam() . "\n"
                    . "  mailCim: " . $fizetesAdatok->getMailCim() . "\n"
                    . "  kozlemeny: " . $fizetesAdatok->getKozlemeny() . "\n";
            }


            if (!$this->filePutContents($logFileName, $logContent, "a+b", $this->logDir)) {
                $this->logger->warn("Hiba tortent a tranzakcios naplo fajl letrehozasa " 
                    . "vagy irasa kozben: " . $logFileName);
            }
            else if ($moveFile){
                $newLoc = $response->isSuccessful() ? $this->logDirSuccess : $this->logDirFailed;
                if (!is_null($newLoc)) {
                    rename($this->logDir . "/" . $logFileName, 
                        $newLoc . "/" . $this->getLogFileName($azonosito, $posId, $logFileName, true, $newLoc));
                }
            }
        }
        else {
            $this->logger->warn("A tranzakcio adatai nem naplozhatoak," 
                . " az azonosito nincs megadva.");
            
        }
    }
    
   /**
    * Kйtszereplхs fizetйsi tranzakciу indнtбsi adatainak naplуzбsa.
    *
    * @param string $posId tranzakciу egyedi azonosнtуja 
    * @param string $azonosito a shop azonosнtуja 
    * @param string $osszeg vбsбrlбs цsszege 
    * @param string $devizanem vбsбrlбs devizaneme 
    * @param string $nyelvkod nyelvkуd 
    * @param string $regisztraltUgyfelId 
    * az OTP fizetхfelьleten regisztrбlt ьgyfйl azonosнtу kуdja. 
    * @param string $kartyaszam    kбrtyaszбm 
    * @param string $cvc2cvv2      CVC2/CVV2 kуd 
    * @param string $kartyaLejarat kбrtya lejбrati dбtuma, MMyy formбban
    * @param string $vevoNev       vevх neve 
    * @param string $vevoPostaCim  vevх postai cнme 
    * @param string $vevoIPCim     vevх gйpйnek IP cнme 
    * @param string $ertesitoMail  vevх kiйrtesнtйsi mailcнme 
    * @param string $ertesitoTel   vevх kiйrtesнtйsi telefonszбma 
    * @param string $logFileName a lйtrehozandу log file neve. Null, ha a metуdus hatбrozza meg az $azonosito йs $posId alapjбn
    * @param $zsebAzonosito a cafeteria kбrtya zseb azonosнtуja
    */
    function logKetszereplosFizetesInditas(
            $posId,
            $azonosito,
            $osszeg,
            $devizanem,
            $nyelvkod,
            $regisztraltUgyfelId,
            $kartyaszam,
            $cvc2cvv2,
            $kartyaLejarat,
            $vevoNev,
            $vevoPostaCim,
            $vevoIPCim,
            $ertesitoMail,
            $ertesitoTel,
            $ketlepcsosFizetes,
            $logFileName = null,
            $zsebAzonosito) {

       if (!is_null($azonosito) && (trim($azonosito) != "")) {
            $logFileName = $this->getLogFileName($azonosito, $posId, $logFileName, true, $this->logDir); 
            
            $logContent = 
                "Ketszereplos fizetesi tranzakcio" . "\n"
                . "\nInditas: " . date(LOG_DATE_FORMAT, time()) . "\n" 
                . "\nIndito adatok" . "\n"
                . "  posId: " . $posId . "\n"
                . "  azonosito: " . $azonosito . "\n"
                . "  osszeg: " . $osszeg . "\n"
                . "  devizanem: " . $devizanem . "\n"
                . "  nyelvkod: " . $nyelvkod . "\n"
                . "  regisztraltUgyfelId: " . $regisztraltUgyfelId . "\n"
                . "  vevoNev: " . $vevoNev . "\n"
                . "  vevoPostaCim: " . $vevoPostaCim . "\n"
                . "  vevoIPCim: " . $vevoIPCim . "\n"
                . "  ertesitoMail: " . $ertesitoMail . "\n"
                . "  ertesitoTel: " . $ertesitoTel . "\n"
                . "  ketlepcsos: " . $ketlepcsosFizetes . "\n"	
                . "  zsebAzonosito: " . $zsebAzonosito . "\n";
                
            if (!$this->filePutContents($logFileName, $logContent, "w+b", $this->logDir)) {
                $this->logger->warn("Hiba tortent a tranzakcios naplo fajl letrehozasa " 
                    . "vagy irasa kozben: " . $logFileName);
            }
       }
        else {
            $this->loggerwarn("A tranzakcio adatai nem naplozhatoak," 
                . " az azonosito nincs megadva.");
            
        }
        
    }

   /**
    * Kйtszereplхs fizetйsi tranzakciу befejezхdйsйnek naplуzбsa.
    *
    * @param string $azonosito fizetйsi tranzakciу azonosнtу
    * @param string $posId shopID bolt azonosнtу
    * @param WResponse $response a fizetйsi tranzakciу vбlasza
    * @param boolean $moveFile mozgassa-e a fбjlt a vйgrehajtбs utбn
    * @param string $logFileName a lйtrehozandу log file neve. Null, ha a metуdus hatбrozza meg az $azonosito йs $posId alapjбn
    */
    function logKetszereplosFizetesBefejezes(
            $azonosito,
            $posId,
            $response,
            $moveFile = true,
            $logFileName = null) {

       if (is_null($response) || !$response->isFinished()) {
            $this->logger->warn(
                "A tranzakcio adatai nem naplozhatoak, a valasz ures: " . $azonosito);
       }
       else if (!is_null($azonosito) && (trim($azonosito) != "")) {
            $logFileName = $this->getLogFileName($azonosito, $posId, $logFileName, false, $this->logDir); 

            $valasz = $response->getAnswer();                         
            $logContent = 
                "\nBefejezes: " . date(LOG_DATE_FORMAT, time()) . "\n" 
                . "\nValasz: " . implode(", " , $response->getMessages()) . "\n"
                . "\nValasz adatok" . "\n"
                . "  posId: " . $valasz->getPosId() . "\n"
                . "  azonosito: " . $valasz->getAzonosito() . "\n"
                . "  posValaszkod: " . $valasz->getValaszKod() . "\n"
                . "  authorizaciosKod: " . $valasz->getAuthorizaciosKod() . "\n"
                . "  teljesites: " . $valasz->getTeljesites() . "\n";
                
            if (!$this->filePutContents($logFileName, $logContent, "a+b", $this->logDir)) {
                $this->logger->warn("Hiba tortent a tranzakcios naplo fajl letrehozasa " 
                    . "vagy irasa kozben: " . $logFileName);
            }
            else if ($moveFile) {
                $newLoc = $response->isSuccessful() ? $this->logDirSuccess : $this->logDirFailed;
                if (!is_null($newLoc) && $newLoc != $this->logDir) {
                    $targetFile = $newLoc . "/" . $this->getLogFileName($azonosito, $posId, $logFileName, true, $newLoc);
                    if (file_exists($targetFile) && filesize($targetFile) === 0) {
                        // lockolбs vйgett hoztuk lйtre, tцrцlhetjьk
                        delete($targetFile);
                    }
                    rename($this->logDir . "/" . $logFileName, $targetFile );
                }
            }
        }
        else {
            $this->loggerwarn(
                "A tranzakcio adatai nem naplozhatoak, az azonosito nincs megadva.");
        }
    }

    /**
     * Kйtlйpcsхs fizetйsi tranzakciу lezбrбsa indнtбsi adatainak naplуzбsa.
     *
     * @param string $posId tranzakciу egyedi azonosнtуja 
     * @param string $azonosito a shop azonosнtуja 
     * @param mixed $jovahagyo jуvбhagyу-e a lezбrбs
     * @param string $osszeg komplettнrozбs цsszege 
     * @param string $logFileName a lйtrehozandу log file neve. Null, ha a metуdus hatбrozza meg az $azonosito йs $posId alapjбn
     */
     function logFizetesLezarasInditas(
             $posId,
             $azonosito,
             $jovahagyo,
             $osszeg,
             $logFileName = null) {
         
         if (!is_null($azonosito) && (trim($azonosito) != "")) {
            $logFileName = $this->getLogFileName($azonosito, $posId, $logFileName, false, $this->logDir); 

            $logContent = 
                 "Ketlepcsos fizetes lezaras tranzakcio" . "\n"
                 . "\nInditas: " . date(LOG_DATE_FORMAT, time()) . "\n" 
                 . "\nIndito adatok" . "\n"
                 . "  posId: " . $posId . "\n"
                 . "  azonosito: " . $azonosito . "\n"
                 . "  jovahagyo: " . $jovahagyo . "\n"
                 . "  osszeg: " . $osszeg . "\n";

            if (!$this->filePutContents($logFileName, $logContent, "a+b", $this->logDir)) {
                $this->logger->warn("Hiba tortent a tranzakcios naplo fajl letrehozasa " 
                    . "vagy irasa kozben: " . $logFileName);
            }
         }
         else {
             $this->logger->warn("A tranzakcio adatai nem naplozhatoak," 
                             . " az azonosito nincs megadva.");
             
         }
         
     }

    /**
     * Kйtlйpcsхs fizetйsi tranzakciу lezбrбsa befejezхdйsйnek naplуzбsa.
     *
    * @param string $azonosito fizetйsi tranzakciу azonosнtу
    * @param string $posId shopID bolt azonosнtу
    * @param WResponse $response a fizetйsi tranzakciу vбlasza
    * @param boolean $moveFile mozgassa-e a fбjlt a vйgrehajtбs utбn
    * @param string $logFileName a lйtrehozandу log file neve. Null, ha a metуdus hatбrozza meg az $azonosito йs $posId alapjбn
     */
     function logFizetesLezarasBefejezes(
             $azonosito,
             $posId,
             $response,
             $moveFile = true,
             $logFileName = null) {

       if (is_null($response) || !$response->isFinished()) {
            $this->logger->warn(
                "A tranzakcio adatai nem naplozhatoak, a valasz ures: " . $azonosito);
       }
       else if (!is_null($azonosito) && (trim($azonosito) != "")) {
            $logFileName = $this->getLogFileName($azonosito, $posId, $logFileName, false, $this->logDir); 

            $valasz = $response->getAnswer();                         
            $logContent = 
                "\nBefejezes: " . date(LOG_DATE_FORMAT, time()) . "\n" 
                . "\nValasz: " . implode(", " , $response->getMessages()) . "\n"
                . "\nValasz adatok" . "\n"
                . "  posId: " . $valasz->getPosId() . "\n"
                . "  azonosito: " . $valasz->getAzonosito() . "\n"
                . "  posValaszkod: " . $valasz->getValaszKod() . "\n"
                . "  authorizaciosKod: " . $valasz->getAuthorizaciosKod() . "\n"
                . "  teljesites: " . $valasz->getTeljesites() . "\n";
                
            if (!$this->filePutContents($logFileName, $logContent, "a+b", $this->logDir)) {
                $this->logger->warn("Hiba tortent a tranzakcios naplo fajl letrehozasa " 
                    . "vagy irasa kozben: " . $logFileName);
            }
            else if ($moveFile) {
                $newLoc = $response->isSuccessful() ? $this->logDirSuccess : $this->logDirFailed;
                if (!is_null($newLoc) && $newLoc != $this->logDir) {
                    $targetFile = $newLoc . "/" . $this->getLogFileName($azonosito, $posId, $logFileName, true, $newLoc);
                    if (file_exists($targetFile) && filesize($targetFile) === 0) {
                        // lockolбs vйgett hoztuk lйtre, tцrцlhetjьk
                        delete($targetFile);
                    }
                    rename($this->logDir . "/" . $logFileName, $targetFile );
                }
            }
        }
        else {
            $this->loggerwarn(
                "A tranzakcio adatai nem naplozhatoak, az azonosito nincs megadva.");
        }
                 
     }
    
   /**
    * Fizetйs jуvбнrбs tranzakciу indнtбsi adatainak naplуzбsa.
    *
    * @param string $posId tranzakciу egyedi azonosнtуja 
    * @param string $azonosito a shop azonosнtуja 
    * @param string $osszeg jуvбнrбs цsszege 
    * @param string $logFileName a lйtrehozandу log file neve. Null, ha a metуdus hatбrozza meg az $azonosito йs $posId alapjбn
    */
    function logFizetesJovairasInditas(
            $posId,
            $azonosito,
            $osszeg,
            $logFileName = null) {

       if (!is_null($azonosito) && (trim($azonosito) != "")) {
            $logFileName = $this->getLogFileName($azonosito, $posId, $logFileName, true, $this->logDir); 
            
            $logContent = 
                "Fizetes jovairas tranzakcio" . "\n"
                . "\nInditas: " . date(LOG_DATE_FORMAT, time()) . "\n" 
                . "\nIndito adatok" . "\n"
                . "  posId: " . $posId . "\n"
                . "  azonosito: " . $azonosito . "\n"
                . "  osszeg: " . $osszeg . "\n";
                
            if (!$this->filePutContents($logFileName, $logContent, "w+b", $this->logDir)) {
                $this->logger->warn("Hiba tortent a tranzakcios naplo fajl letrehozasa " 
                    . "vagy irasa kozben: " . $logFileName);
            }
       }
        else {
            $this->loggerwarn("A tranzakcio adatai nem naplozhatoak," 
                . " az azonosito nincs megadva.");
            
        }
        
    }

   /**
    * Fizetйs jуvбнrбs tranzakciу befejezхdйsйnek naplуzбsa.
    *
    * @param string $azonosito fizetйsi tranzakciу azonosнtу
    * @param string $posId shopID bolt azonosнtу
    * @param WResponse $response a fizetйsi tranzakciу vбlasza
    * @param boolean $moveFile mozgassa-e a fбjlt a vйgrehajtбs utбn
    * @param string $logFileName a lйtrehozandу log file neve. Null, ha a metуdus hatбrozza meg az $azonosito йs $posId alapjбn
    */
    function logFizetesJovairasBefejezes(
            $azonosito,
            $posId,
            $response,
            $moveFile = true,
            $logFileName = null) {

       if (is_null($response) || !$response->isFinished()) {
            $this->logger->warn(
                "A tranzakcio adatai nem naplozhatoak, a valasz ures: " . $azonosito);
       }
       else if (!is_null($azonosito) && (trim($azonosito) != "")) {
            $logFileName = $this->getLogFileName($azonosito, $posId, $logFileName, false, $this->logDir); 

            $valasz = $response->getAnswer();                         
            $logContent = 
                "\nBefejezes: " . date(LOG_DATE_FORMAT, time()) . "\n" 
                . "\nValasz: " . implode(", " , $response->getMessages()) . "\n"
                . "\nValasz adatok" . "\n"
                . "  posId: " . $posId . "\n"
                . "  azonosito: " . $azonosito . "\n"
                . "  posValaszkod: " . $valasz->getValaszKod() . "\n"
                . "  authorizaciosKod: " . $valasz->getAuthorizaciosKod() . "\n"
                . "  mwTransactionId: " . $valasz->getMwTransactionId() . "\n";
                
            if (!$this->filePutContents($logFileName, $logContent, "a+b", $this->logDir)) {
                $this->logger->warn("Hiba tortent a tranzakcios naplo fajl letrehozasa " 
                    . "vagy irasa kozben: " . $logFileName);
            }
            else if ($moveFile) {
                $newLoc = $response->isSuccessful() ? $this->logDirSuccess : $this->logDirFailed;
                if (!is_null($newLoc) && $newLoc != $this->logDir) {
                    $targetFile = $newLoc . "/" . $this->getLogFileName($azonosito, $posId, $logFileName, true, $newLoc);
                    if (file_exists($targetFile) && filesize($targetFile) === 0) {
                        // lockolбs vйgett hoztuk lйtre, tцrцlhetjьk
                        delete($targetFile);
                    }
                    rename($this->logDir . "/" . $logFileName, $targetFile );
                }
            }
        }
        else {
            $this->loggerwarn(
                "A tranzakcio adatai nem naplozhatoak, az azonosito nincs megadva.");
        }
    }
}

?>