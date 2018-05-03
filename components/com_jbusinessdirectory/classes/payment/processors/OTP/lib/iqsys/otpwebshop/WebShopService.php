<?php

error_reporting(E_ERROR | E_PARSE);

define('WEBSHOP_LIB_VER', '4.0');

if (!defined('WEBSHOP_LIB_DIR')) define('WEBSHOP_LIB_DIR', dirname(dirname(dirname(__FILE__))) );

if (!defined('WEBSHOP_CONF_DIR')) {
	define('WEBSHOP_CONF_DIR', dirname(dirname(dirname(dirname(__FILE__)))) . DIRECTORY_SEPARATOR . 'config');
}

define('WEBSHOPSERVICE_CONFIGURATION', WEBSHOP_CONF_DIR . DIRECTORY_SEPARATOR . 'otp_webshop_client.conf');

define('LOG4PHP_DIR', WEBSHOP_LIB_DIR . '/apache/log4php');
define('LOG4PHP_CONFIGURATION', WEBSHOPSERVICE_CONFIGURATION);

require_once WEBSHOP_LIB_DIR . '/iqsys/otpwebshop/factory/WResponse.php';
require_once WEBSHOP_LIB_DIR . '/iqsys/otpwebshop/util/RequestUtils.php';
require_once WEBSHOP_LIB_DIR . '/iqsys/otpwebshop/util/WebShopXmlUtils.php';
require_once WEBSHOP_LIB_DIR . '/iqsys/otpwebshop/util/SignatureUtils.php';
require_once WEBSHOP_LIB_DIR . '/iqsys/otpwebshop/util/SoapUtils.php';
require_once WEBSHOP_LIB_DIR . '/iqsys/otpwebshop/util/TransactionLogger.php';
require_once WEBSHOP_LIB_DIR . '/iqsys/otpwebshop/util/DefineConst.php';
require_once WEBSHOP_LIB_DIR . '/iqsys/otpwebshop/util/ConfigUtils.php';

require_once LOG4PHP_DIR . '/LoggerManager.php';

/**
 * WebShop szolg�ltat�sok megh�v�sa a Bank SOAP fel�let�nek k�zvetlen el�r�s�vel.
 *
 * A WebShop-ok r�sz�re k�sz�tett PHP elj�r�s gy�jtem�ny. 
 * 
 * A web-alkalmaz�st lok�kisal, WebShop-onk�nt kell telep�teni.Ezek az
 * alkalmaz�sok h�vj�k meg az OTP middleware rendszer�nek megfelel� WebShop
 * folyamatait: 
 * - ping 
 * - tranzakci� azonos�t� gener�l�s 
 * - h�romszerepl�s fizet�si folyamat ind�t�sa 
 * - k�tszerepl�s fizet�si folyamat ind�t�sa 
 * - tranzakci� adatok, tranzakci� st�tusz lek�rdez�se
 * - k�tl�pcs�s fizet�si tranzakci� lez�r�sa
 * - fizet�s j�v��r�s ind�t�sa
 *
 * A fenti szolg�ltat�sok k�zvetlen m�don, az �gynevezett OTP MWAccess fel�leten
 * is megh�vhat�ak, de ott l�nyegesen �sszetettebb feladat h�rul a WebShop
 * kliens oldali fej�eszt�kre. A kliens oldali WebShop szerver az al�bbi
 * funkci�k v�grehajt�s�val k�nny�ti a fejlszt�st: - nem kell folyamatokat
 * ind�t� xml-eket �sszell�tani �s nem kell v�lasz xml- eket �rtelmezni.
 * Egyszer�en mez�szinten kell megadni a bemen� adatokat, �s ugyancsak
 * mez�szinten �rhet�ek el a v�lasz adatai. - az alkalmaz�s automatikusan
 * legener�lja a digit�lis al��r�st azokn�l a m�veletekn�l, ahol ez sz�ks�ges. -
 * automatikusan napl�z�sra ker�lnek a kommunik�ci� elemei: a kapcsol�d�si
 * param�terek, a bej�v� �s kimen� SOAP k�r�sek, a folyamat input- �s answer
 * xml- jei. A napl�z�s r�szletess�ge konfigur�lhat�.
 *
 * @version 4.0
 */

class WebShopService {

    /**
    * Log4php napl�z� objektum.
    * 
    * @var mixed
    */
    var $logger;
    
    /**
    * Az otp_webshop_client.conf konfigur�ci�s f�jl tartalma.
    * 
    * @var array
    */
    var $property;

    /**
    * A bankkal kommunik�l� SOAP kliens.
    * 
    * $var mixed
    */
    var $soapClient;
    
    /**
    * Az utolj�ra ind�tott banki tranzakci�hoz tartoz� inputXml sz�veges tartalma
    * 
    * @var string
    */
    var $lastInputXml = NULL;

    /**
    * Az utolj�ra ind�tott banki tranzakci�hoz tartoz� outputXml sz�veges tartalma
    * 
    * @var string
    */
    var $lastOutputXml = NULL;

    /**
    * A banki tranzakci�khoz tartoz� "besz�des nevek", mellyekkel napl�z�sra ker�lnek
    * 
    * @var array
    */
    var $operationLogNames = array(
        'tranzakcioAzonositoGeneralas' => 'tranzakcioAzonositoGeneralas',
        'fizetesiTranzakcioKetszereplos' => 'fizetesiTranzakcioKetszereplos',
        'fizetesiTranzakcio' => 'fizetesiTranzakcio',
        'tranzakcioStatuszLekerdezes' => 'tranzakcioStatuszLekerdezes',
        'ketlepcsosFizetesLezaras' => 'ketlepcsosFizetesLezaras',
        'fizetesJovairas' => 'fizetesJovairas',
        'kulcsLekerdezes' => 'kulcsLekerdezes'
    );
        
    /**
    * Konstruktor.
    * 
    * - inicializ�l�dik a log4php
    * - beolvas�sra ker�lt a konfigur�ci�s �llom�ny
    * - p�ld�nyosodik a SOAP kliens
    */
    function __construct() {
        $this->logger =& LoggerManager::getLogger("WebShopClient");
        $this->logger->debug("OTPWebShopService (PHP) p�ld�nyos�t�s...");
        
        $this->property = parse_ini_file(WEBSHOPSERVICE_CONFIGURATION);

        $this->logger->debug("OTPMW szerver url: " . ConfigUtils::getConfigParam($this->property, PROPERTY_OTPMWSERVERURL));

        if (ConfigUtils::getConfigParam($this->property, PROPERTY_HTTPSPROXYHOST)) {
            $this->logger->debug("Kliens https proxy host: " . ConfigUtils::getConfigParam($this->property, PROPERTY_HTTPSPROXYHOST));
        }
        if (ConfigUtils::getConfigParam($this->property, PROPERTY_HTTPSPROXYPORT)) {
            $this->logger->debug("Kliens https proxy port: " . ConfigUtils::getConfigParam($this->property, PROPERTY_HTTPSPROXYPORT));
        }
        if (ConfigUtils::getConfigParam($this->property, PROPERTY_HTTPSPROXYUSER)) {
            $this->logger->debug("Kliens https proxy user: " . ConfigUtils::getConfigParam($this->property, PROPERTY_HTTPSPROXYUSER));
        }
        if (ConfigUtils::getConfigParam($this->property, PROPERTY_HTTPSPROXYPASSWORD)) {
            $this->logger->debug("Kliens https proxy password: " . "******");
        }
        
        $this->soapClient = SoapUtils::createSoapClient($this->property);
    }
    
    /**
     * Egy adott fizet�si tranzakci�hoz tartoz� priv�t kulcs �llom�ny el�r�si
     * �tvonal�nak be�ll�t�sa a konfigur�ci�s param�terek alapj�n. Ha adott
     * posId (bolt azonos�t�) eset�n a konfigur�ci�s �llom�ny tartalmaz
     * otp.webshop.PRIVATE_KEY_posId=[el�r�si �t] bejegyz�st, akkor az elj�r�s
     * ezt a bejegyz�st adja v�laszul. Egy�bk�nt a
     * otp.webshop.PRIVATE_KEY_FILE=[el�r�si �t] bejegyz�sben szerepl�t. Az els�
     * m�dszerrel lehet multishop-ot kialak�tani, vagyis olyan WebShop boolt
     * oldali szervert, amely t�bb bolt k�r�s�t is ki tudja szolg�lni, �s
     * boltonk�nt (sz�ks�gszer�en) elt�r� priv�t kulcs alapj�n t�rt�nik a
     * digit�lis al��r�s.
     *
     * @param properties
     *            A shop-hoz tartoz� konfigur�ci�s be�ll�t�sok (array)
     * @param posId
     *            A tranzakci�t ind�t� shop azonos�t�ja
     * @return A megadott shop-hoz tartoz� priv�t kulcs el�r�si �tvonala
     */
    function getPrivKeyFileName($properties, $posId) {
        $privKeyFileName = ConfigUtils::getConfigParam($properties, PROPERTY_PRIVATEKEYFILE, $posId);
        if (!file_exists($privKeyFileName)) {
            $this->logger->fatal("A priv�t kulcs f�jl nem tal�lhat�: " . $privKeyFileName);
        }
        return $privKeyFileName;
    }
    
    /**
     * Egy adott fizet�si tranzakci�hoz tartoz� tranzakci�s napl� �llom�ny
     * el�r�si �tvonalainak be�ll�t�sa a konfigur�ci�s param�terek alapj�n. Ha
     * adott posId (bolt azonos�t�) eset�n a konfigur�ci�s �llom�ny tartalmaz
     * otp.webshop.TRANSACTION_LOG_DIR_posId=[el�r�si �t] bejegyz�st, akkor az
     * elj�r�s ezt a bejegyz�st adja v�laszul. Egy�bk�nt a
     * otp.webshop.otp.webshop.TRANSACTION_LOG_DIR=[el�r�si �t] bejegyz�sben
     * szerepl�t el�r�si �tvonalat. Az els� m�dszerrel lehet multishop-ot
     * kialak�tani, vagyis olyan WebShop bolt oldali szervert, amely t�bb bolt
     * k�r�s�t is ki tudja szolg�lni, �s boltonk�nt m�s k�nyvt�rba t�rt�nik a
     * tranzakci�k napl�z�sa.
     * Ugyanez a k�pz�si szab�ly igaz a otp.webshop.transaction_log_dir.SUCCESS_DIR
     * �s otp.webshop.transaction_log_dir.FAILED_DIR param�terekre is.
     *
     * @param properties
     *            A shop-hoz tartoz� konfigur�ci�s be�ll�t�sok
     * @param posId
     *            A tranzakci�t ind�t� shop azonos�t�ja
     * @return A megadott shop-hoz tartoz� tranzakci�s napl� �llom�nyok
     *         k�nyvt�rainak el�r�si �tvonala: az alap�rtelmezett k�nyvt�r,
     *         a sikeres illetve sikertelen tranzakci�k k�nyvt�ra
     */
    function getTranLogDir($properties, $posId) {
        $tranLogDir = ConfigUtils::getConfigParam($properties, PROPERTY_TRANSACTIONLOGDIR, $posId);
        $tranLogSuccessDir = ConfigUtils::getConfigParam($properties, PROPERTY_TRANSACTIONLOG_SUCCESS_DIR, $posId);
        $tranLogFailedDir = ConfigUtils::getConfigParam($properties, PROPERTY_TRANSACTIONLOG_FAILED_DIR, $posId);
        return array ($tranLogDir, $tranLogSuccessDir, $tranLogFailedDir);
    }

    /**
     * @desc A banki fel�let Ping szolg�ltat�s�nak megh�v�sa. 
     * Mivel tranzakci� ind�t�s nem t�rt�nik, a sikeres ping
     * eset�n sem garant�lt az, hogy az egyes fizet�si tranzakci�k
     * sikeresen el is ind�that�k -  csup�n az biztos, hogy a
     * h�l�zati architekt�r�n kereszt�l sikeresen el�rhet� a
     * banki fel�let. 
     * 
     * Digit�lis al��r�s nem k�pz�dik.
     * 
     * @return boolean true sikeres ping-et�s eset�n, egy�bk�nt false.
     */
    function ping() {
        $this->logger->debug("ping indul...");
        $result = SoapUtils::ping($this->soapClient, $this->logger);
        $this->logger->debug("ping befejez�d�tt.");
        return $result;
    }
    
    /**
     * WEBSHOPTRANZAZONGENERALAS folyamat szinkron ind�t�sa. 
     * 
     * @param string $posId 
     *        webshop azonos�t�
     * 
     * @return WResponse a tranzakci� v�lasz�t reprezent�l� value object.
     *         Sikeres v�grehajt�s eset�n a v�lasz adatokat WebShopTranzAzon
     *         objektum reprezent�lja.
     *         Kommunik�ci�s hiba eset�n a finished flag false �rt�k� lesz!
     */
    function tranzakcioAzonositoGeneralas($posId) {
        $this->logger->debug($this->operationLogNames["tranzakcioAzonositoGeneralas"] . " indul...");
        
        $dom = WebShopXmlUtils::getRequestSkeleton(WF_TRANZAZONGENERALAS, $variables);
        WebShopXmlUtils::addParameter($dom, $variables, CLIENTCODE, CLIENTCODE_VALUE);
        WebShopXmlUtils::addParameter($dom, $variables, POSID, $posId);
        
        $signatureFields = array(0 => $posId);
        $signatureText = SignatureUtils::getSignatureText($signatureFields);

        $pkcs8PrivateKey = SignatureUtils::loadPrivateKey($this->getPrivKeyFileName($this->property, $posId));
        $signature = SignatureUtils::generateSignature($signatureText, $pkcs8PrivateKey, $this->property, $this->logger);
		
		$attrName = null;
		$attrValue = null;
		
		if (version_compare(PHP_VERSION, '5.4.8', '>=')) {
			$attrName = 'algorithm';
			$attrValue = 'SHA512';
		}
		
        WebShopXmlUtils::addParameter($dom, $variables, CLIENTSIGNATURE, $signature, $attrName, $attrValue);
        
        $this->lastInputXml = WebShopXmlUtils::xmlToString($dom);
        $this->logger->debug($this->operationLogNames["tranzakcioAzonositoGeneralas"] . " keres:\n" . WebShopXmlUtils::xmlToString($dom));     
                
        $workflowState = SoapUtils::startWorkflowSynch(WF_TRANZAZONGENERALAS, $this->lastInputXml, $this->soapClient, $this->logger);
        $response = new WResponse(WF_TRANZAZONGENERALAS, $workflowState);

	    $this->logger->info($this->operationLogNames["tranzakcioAzonositoGeneralas"] . " folyamat azonosito: " 
	        . $response->getInstanceId());

	    // a folyamat v�lasz�nak napl�z�sa
	    if ($response->isFinished()) {
	        $responseDom = $response->getResponseDOM(); 
            $this->lastOutputXml = WebShopXmlUtils::xmlToString($responseDom);                  
            $this->logger->debug($this->operationLogNames["tranzakcioAzonositoGeneralas"] . " valasz:\n" 
	            . trim($this->lastOutputXml));
	    }
	    else {
	        $this->logger->error($this->operationLogNames["tranzakcioAzonositoGeneralas"] . " hiba!");
            $this->logger->error($workflowState);      
	    }

	    $this->logger->debug($this->operationLogNames["tranzakcioAzonositoGeneralas"] . " befejezodott.");

        return $response;
    }

    /**
     * H�romszerepl�s fizet�si folyamat (WEBSHOPFIZETES) szinkron ind�t�sa.
     *
     * @param string $posId 
     *        webshop azonos�t�
     * @param string $tranzakcioAzonosito 
     *        fizet�si tranzakci� azonos�t�
     * @param mixed $osszeg 
     *        Fizetend� �sszeg, (num, max. 13+2), opcion�lis tizedesponttal.
     *        Nulla is lehet, ha a regisztraltUgyfelId param�ter ki van
     *        t�ltve, �s az ugyfelRegisztracioKell �rt�ke igaz. �gy kell
     *        ugyanis jelezni azt, hogy nem t�nyleges v�s�rl�si tranzakci�t
     *        kell ind�tani, hanem egy �gyf�l regisztr�l�st, vagyis az
     *        �gyf�l k�rtyaadatainak bek�r�st �s elt�rol�s�t a banki
     *        oldalon.
     * @param string $devizanem 
     *            fizetend� devizanem
     * @param string $nyelvkod 
     *            a megjelen�tend� vev� oldali fel�let nyelve
     * @param mixed $nevKell
     *            a megjelen�tend� vev� oldali fel�leten be kell k�rni a vev�
     *            nev�t
     * @param mixed $orszagKell
     *            a megjelen�tend� vev� oldali fel�leten be kell k�rni a vev�
     *            c�m�nek "orsz�g r�sz�t"
     * @param mixed $megyeKell
     *            a megjelen�tend� vev� oldali fel�leten be kell k�rni a vev�
     *            c�m�nek "megye r�sz�t"
     * @param mixed $telepulesKell
     *            a megjelen�tend� vev� oldali fel�leten be kell k�rni a vev�
     *            c�m�nek "telep�l�s r�sz�t"
     * @param mixed $iranyitoszamKell
     *            a megjelen�tend� vev� oldali fel�leten be kell k�rni a vev�
     *            c�m�nek "ir�ny�t�sz�m r�sz�t"
     * @param mixed $utcaHazszamKell
     *            a megjelen�tend� vev� oldali fel�leten be kell k�rni a vev�
     *            c�m�nek "utca/h�zsz�m r�sz�t"
     * @param mixed $mailCimKell
     *            a megjelen�tend� vev� oldali fel�leten be kell�k�rni a vev�
     *            e-mail c�m�t
     * @param mixed $kozlemenyKell
     *            a megjelen�tend� vev� oldali fel�leten fel kell k�n�lni a
     *            k�zlem�ny megad�s�nak lehet�s�g�t
     * @param mixed $vevoVisszaigazolasKell
     *            a tranzakci� eredm�ny�t a vev� oldalon meg kell jelen�teni
     *            (azaz nem a backURL-re kell ir�ny�tani)
     * @param mixed $ugyfelRegisztracioKell
     *            ha a regisztraltUgyfelId �rt�ke nem �res, akkor megadja, hogy
     *            a megadott azonos�t� �jonnan regisztr�land�-e, vagy m�r
     *            regisztr�l�sra ker�lt az OTP Internetes Fizet� fel�let�n.
     *            El�bbi esetben a kliens oldali b�ng�sz�ben olyan fizet� oldal
     *            fog megjelenni, melyen meg kell adni az azonos�t�hoz tartoz�
     *            jelsz�t, illetve a k�rtyaadatokat. Ut�bbi esetben csak az
     *            azonos�t�hoz tartoz� jelsz� ker�l beolvas�sra az �rtes�t�si
     *            c�men k�v�l. Ha a regisztraltUgyfelId �rt�ke �res, a pamar�ter
     *            �rt�ke nem ker�l felhaszn�l�sra.
     * @param string $regisztraltUgyfelId
     *            az OTP fizet�fel�leten regisztr�land� vagy regisztr�lt �gyf�l
     *            azonos�t� k�dja.
     * @param string $shopMegjegyzes
     *            a webshop megjegyz�se a tranzakci�hoz a vev� r�sz�re
     * @param string $backURL
     *            a tranzakci� v�grehajt�sa ut�n erre az internet c�mre kell
     *            ir�ny�tani a vev� oldalon az �gyfelet (ha a
     *            vevoVisszaigazolasKell hamis)
     * @param string $zsebAzonosito
     * 			  a cafeteria k�rtya zseb azonos�t�ja.       
     * @param mixed $ketlepcsosFizetes
     * 			  megadja, hogy k�tl�pcs�s fizet�s ind�tand�-e.
     *            True �rt�k eset�n a fizet�si tranzakci� k�tl�pcs�s lesz, 
     *            azaz a terhelend� �sszeg csup�n z�rol�sra ker�l, 
     *            s �gy is marad a bolt �ltal ind�tott lez�r� tranzakci� 
     *            ind�t�s�ig avagy a z�rol�s el�v�l�s�ig.
     *            Az alap�rtelmezett (�res) �rt�k a Bank oldalon r�gz�tett 
     *            alap�rtelmezett m�dot jel�li.       
     *
     * @return WResponse a tranzakci� v�lasz�t reprezent�l� value object.
     *         Sikeres v�grehajt�s eset�n a v�lasz adatokat WebShopFizetesAdatok
     *         objektum reprezent�lja.
     *         Kommunik�ci�s hiba eset�n a finished flag false �rt�k� lesz!
     */
    function fizetesiTranzakcio(
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
            $zsebAzonosito,
            $ketlepcsosFizetes = NULL) {

        $this->logger->debug($this->operationLogNames["fizetesiTranzakcio"] . " indul...");

        $dom = WebShopXmlUtils::getRequestSkeleton(WF_HAROMSZEREPLOSFIZETESINDITAS, $variables);

        // default �rt�kek feldolgoz�sa
        if (is_null($devizanem) || (trim($devizanem) == "")) {
            $devizanem = DEFAULT_DEVIZANEM;
        }

        /* param�terek beilleszt�se */
        WebShopXmlUtils::addParameter($dom, $variables, CLIENTCODE, CLIENTCODE_VALUE);
        WebShopXmlUtils::addParameter($dom, $variables, POSID, $posId);
        WebShopXmlUtils::addParameter($dom, $variables, TRANSACTIONID, $azonosito);
        WebShopXmlUtils::addParameter($dom, $variables, AMOUNT, $osszeg);
        WebShopXmlUtils::addParameter($dom, $variables, EXCHANGE, $devizanem);
        WebShopXmlUtils::addParameter($dom, $variables, LANGUAGECODE, $nyelvkod);

        WebShopXmlUtils::addParameter($dom, $variables, NAMENEEDED, RequestUtils::booleanToString($nevKell));
        WebShopXmlUtils::addParameter($dom, $variables, COUNTRYNEEDED, RequestUtils::booleanToString($orszagKell));
        WebShopXmlUtils::addParameter($dom, $variables, COUNTYNEEDED, RequestUtils::booleanToString($megyeKell));
        WebShopXmlUtils::addParameter($dom, $variables, SETTLEMENTNEEDED, RequestUtils::booleanToString($telepulesKell));
        WebShopXmlUtils::addParameter($dom, $variables, ZIPCODENEEDED, RequestUtils::booleanToString($iranyitoszamKell));
        WebShopXmlUtils::addParameter($dom, $variables, STREETNEEDED, RequestUtils::booleanToString($utcaHazszamKell));
        WebShopXmlUtils::addParameter($dom, $variables, MAILADDRESSNEEDED, RequestUtils::booleanToString($mailCimKell));
        WebShopXmlUtils::addParameter($dom, $variables, NARRATIONNEEDED, RequestUtils::booleanToString($kozlemenyKell));
        WebShopXmlUtils::addParameter($dom, $variables, CONSUMERRECEIPTNEEDED, RequestUtils::booleanToString($vevoVisszaigazolasKell));

        WebShopXmlUtils::addParameter($dom, $variables, BACKURL, $backURL);

        WebShopXmlUtils::addParameter($dom, $variables, SHOPCOMMENT, $shopMegjegyzes);

        WebShopXmlUtils::addParameter($dom, $variables, CONSUMERREGISTRATIONNEEDED, $ugyfelRegisztracioKell);
        WebShopXmlUtils::addParameter($dom, $variables, CONSUMERREGISTRATIONID, $regisztraltUgyfelId);

        WebShopXmlUtils::addParameter($dom, $variables, TWOSTAGED, RequestUtils::booleanToString($ketlepcsosFizetes, NULL));
        WebShopXmlUtils::addParameter($dom, $variables, CARDPOCKETID, $zsebAzonosito);

        /* al��r�s kisz�m�t�sa �s param�terk�nt besz�r�sa */
        $signatureFields = array(0 => 
            $posId, $azonosito, $osszeg, $devizanem, $regisztraltUgyfelId);
        $signatureText = SignatureUtils::getSignatureText($signatureFields);

        $pkcs8PrivateKey = SignatureUtils::loadPrivateKey($this->getPrivKeyFileName($this->property, $posId));
        $signature = SignatureUtils::generateSignature($signatureText, $pkcs8PrivateKey, $this->property, $this->logger);

        $attrName = null;
		$attrValue = null;
		
		if (version_compare(PHP_VERSION, '5.4.8', '>=')) {
			$attrName = 'algorithm';
			$attrValue = 'SHA512';
		}
		
        WebShopXmlUtils::addParameter($dom, $variables, CLIENTSIGNATURE, $signature, $attrName, $attrValue);

        $this->lastInputXml = WebShopXmlUtils::xmlToString($dom);
        $this->logger->info($this->operationLogNames["fizetesiTranzakcio"] . " keres: " . $posId . " / " . $azonosito);
        $this->logger->debug($this->operationLogNames["fizetesiTranzakcio"] . " keres:\n" . $this->lastInputXml);

        /* Tranzakci� adatainak napl�z�sa egy k�l�n f�jlba */
        $transLogger = new TransactionLogger(
                    $this->getTranLogDir($this->property, $posId), $this->logger);

        $transLogger->logHaromszereplosFizetesInditas($posId, $azonosito, 
                $osszeg, $devizanem, $nyelvkod, $nevKell, $orszagKell,
                $megyeKell, $telepulesKell, $iranyitoszamKell,
                $utcaHazszamKell, $mailCimKell, $kozlemenyKell,
                $vevoVisszaigazolasKell, $ugyfelRegisztracioKell,
                $regisztraltUgyfelId, $shopMegjegyzes, $backURL,
                $zsebAzonosito, $ketlepcsosFizetes);
        
        /* A tranzakci� ind�t�sa */
        $startTime = time();
        $workflowState = SoapUtils::startWorkflowSynch(WF_HAROMSZEREPLOSFIZETESINDITAS, $this->lastInputXml, $this->soapClient, $this->logger);
        
        if (!is_null($workflowState)) {
            $response = new WResponse(WF_HAROMSZEREPLOSFIZETES, $workflowState);
        }
        else {
            $this->logger->warn($this->operationLogNames["fizetesiTranzakcio"] . " folyamat megszakadt: " 
                . $azonosito . ", polloz�s indul..."); 
            // A tranzakci� megszakadt, a banki fel�let v�lasz�t nem
            // tudta a kliens fogadni
            $poll = true;
            $resendDelay = 20;
            do {
                $tranzAdatok = $this->tranzakcioPoll($posId, $azonosito, $startTime);
                if ($tranzAdatok === false) {
                    // nem siker�lt a lek�rdez�s, �jrapr�b�lkozunk
                    $poll = true;
                    $this->logger->error($this->operationLogNames["fizetesiTranzakcio"] . " poll hiba, azonosito: " . $azonosito);
                }
                else {
                    if ($tranzAdatok->isFizetesFeldolgozasAlatt()) {
                        // a tranzakci� feldolgoz�s alatt van
                        // mindenk�pp �rdemes kicsit v�rni, �s �jra pollozni
                    }
                    else {
                        // a tranzakci� feldolgoz�sa befejez�d�tt 
                        // (lehet sikeres vagy sikertelen az eredm�ny)
                        $poll = false;
                        $response = new WResponse(WF_HAROMSZEREPLOSFIZETES, null);
                        $this->logger->info($this->operationLogNames["fizetesiTranzakcio"] . " poll befejezve: " 
                            . $azonosito); 
                        // a folyamat v�lasz�nak napl�z�sa
                        $response->loadAnswerModel($tranzAdatok, $tranzAdatok->isSuccessful(), $tranzAdatok->getPosValaszkod());  
                        $transLogger->logHaromszereplosFizetesBefejezes($azonosito, $posId, $response);
                        return $response;
                    }
                }
                $retryCount++;
                sleep($resendDelay);
            } while ($poll && ($startTime + 660 > time()));
            // pollozunk, am�g van �rtelme, de legfeljebb 11 percig! 
            
            $this->logger->info($this->operationLogNames["fizetesiTranzakcio"] 
                . $azonosito . ", polloz�s befejezve..."); 
        }
  
        // a folyamat v�lasz�nak napl�z�sa
        if ($response->isFinished()) {
            $this->logger->info($this->operationLogNames["fizetesiTranzakcio"] . " folyamat azonosito: " 
                . $response->getInstanceId());
            $responseDom = $response->getResponseDOM();
            $this->lastOutputXml = WebShopXmlUtils::xmlToString($responseDom);
            $this->logger->debug($this->operationLogNames["fizetesiTranzakcio"] . " valasz:\n" 
                . trim($this->lastOutputXml));
            $transLogger->logHaromszereplosFizetesBefejezes($azonosito, $posId, $response);
        }
        else {
            $this->logger->error($this->operationLogNames["fizetesiTranzakcio"] . " hiba, azonosito: " . $azonosito);
            $this->logger->error($workflowState);
        }
                
        $this->logger->debug($this->operationLogNames["fizetesiTranzakcio"] . " befejezodott.");

        return $response;
    }
    
    /**
     * WEBSHOPTRANZAKCIOLEKERDEZES folyamat szinkron ind�t�sa.
     * 
     * @param string $posId webshop azonos�t�
     * @param string $azonosito lek�rdezend� tranzakci� azonos�t�
     * @param mixed $maxRekordSzam maxim�lis rekordsz�m (int / string)
     * @param mixed $idoszakEleje lek�rdezend� id�szak eleje 
     *        ����.HH.NN ��:PP:MM alak� string �rt�k vagy int timestamp
     * @param mixed $idoszakEleje lek�rdezend� id�szak v�ge
     *        ����.HH.NN ��:PP:MM alak� string �rt�k vagy int timestamp
     * 
     * @return WResponse a tranzakci� v�lasz�t reprezent�l� value object.
     *         Sikeres v�grehajt�s eset�n a v�lasz adatokat WebShopAdatokLista
     *         objektum reprezent�lja.
     *         Kommunik�ci�s hiba eset�n a finished flag false �rt�k� lesz!
     */
    function tranzakcioStatuszLekerdezes(
            $posId,
            $azonosito, 
            $maxRekordSzam, 
            $idoszakEleje,
            $idoszakVege) {
                
        $this->logger->debug($this->operationLogNames["tranzakcioStatuszLekerdezes"] . " indul...");

        $dom = WebShopXmlUtils::getRequestSkeleton(WF_TRANZAKCIOSTATUSZ, $variables);

        $idoszakEleje = RequestUtils::dateToString($idoszakEleje);
        $idoszakVege = RequestUtils::dateToString($idoszakVege);
        
        /* param�terek beilleszt�se */
        WebShopXmlUtils::addParameter($dom, $variables, CLIENTCODE, CLIENTCODE_VALUE);
        WebShopXmlUtils::addParameter($dom, $variables, POSID, $posId);
        WebShopXmlUtils::addParameter($dom, $variables, TRANSACTIONID, $azonosito);
        WebShopXmlUtils::addParameter($dom, $variables, QUERYMAXRECORDS, $maxRekordSzam);
        WebShopXmlUtils::addParameter($dom, $variables, QUERYSTARTDATE, $idoszakEleje);
        WebShopXmlUtils::addParameter($dom, $variables, QUERYENDDATE, $idoszakVege);

        /* al��r�s kisz�m�t�sa �s param�terk�nt besz�r�sa */
        $signatureFields = array(0 => 
            $posId, $azonosito, 
            $maxRekordSzam, $idoszakEleje, $idoszakVege );
        $signatureText = SignatureUtils::getSignatureText($signatureFields);

        $pkcs8PrivateKey = SignatureUtils::loadPrivateKey($this->getPrivKeyFileName($this->property, $posId));
        $signature = SignatureUtils::generateSignature($signatureText, $pkcs8PrivateKey, $this->property, $this->logger);

        $attrName = null;
		$attrValue = null;
		
		if (version_compare(PHP_VERSION, '5.4.8', '>=')) {
			$attrName = 'algorithm';
			$attrValue = 'SHA512';
		}
		
        WebShopXmlUtils::addParameter($dom, $variables, CLIENTSIGNATURE, $signature, $attrName, $attrValue);

        $this->lastInputXml = WebShopXmlUtils::xmlToString($dom);
        $this->logger->debug($this->operationLogNames["tranzakcioStatuszLekerdezes"] . " keres:\n" . $this->lastInputXml);

        /* a folyamat ind�t�sa */
        $workflowState = SoapUtils::startWorkflowSynch(WF_TRANZAKCIOSTATUSZ, $this->lastInputXml, $this->soapClient, $this->logger);
        $response = new WResponse(WF_TRANZAKCIOSTATUSZ, $workflowState);

        /* a folyamat v�lasz�nak napl�z�sa */
        if ($response->isFinished()) {
            $this->logger->info($this->operationLogNames["tranzakcioStatuszLekerdezes"] . " folyamat azonosito: " 
                . $response->getInstanceId());
            $responseDom = $response->getResponseDOM();
            $this->lastOutputXml = WebShopXmlUtils::xmlToString($responseDom);
            $this->logger->debug($this->operationLogNames["tranzakcioStatuszLekerdezes"] . " valasz:\n" 
                . trim($this->lastOutputXml));
        }
        else {
            $this->logger->error($this->operationLogNames["tranzakcioStatuszLekerdezes"] . " hiba!");
            $this->logger->error($workflowState);            
        }

        $this->logger->debug($this->operationLogNames["tranzakcioStatuszLekerdezes"] . " befejezodott.");

        return $response;
    }
    
    /**
     * K�tszerepl�s fizet�si tranzakci� (WEBSHOPFIZETESKETSZEREPLOS) ind�t�sa.
     *
     * @param string $posId
     *            tranzakci� egyedi azonos�t�ja (alfanum, max. 32, azonos a 3
     *            szerepl�sn�l bevezetettel)
     * @param string $azonosito
     *            a shop azonos�t�ja (num, max. 6, azonos a 3 szerepl�sn�l
     *            bevezetettel)
     * @param mixed $osszeg
     *            v�s�rl�s �sszege (num, max. 13+2), opcion�lis tizedesponttal
     * @param string $devizanem
     *            v�s�rl�s devizaneme (opcion�lis, azonos a 3 szerepl�sn�l
     *            bevezetettel)
     * @param stirng $nyelvkod
     *            nyelvk�d (azonos a 3 szerepl�sn�l bevezetettel)
     * @param string $regisztraltUgyfelId
     *            az OTP fizet�fel�leten regisztr�lt �gyf�l azonos�t� k�dja.
     *            Kit�lt�se eset�n a kartyaszam, cvc2cvv2, kartyaLejarat adatok
     *            nem ker�lnek feldolgoz�sra, hanem a banki oldalon az adott
     *            azonos�t�hoz elt�rolt k�rtyaadatok ker�lnek behelyettes�t�sre
     * @param string $kartyaszam
     *            k�rtyasz�m (azonos a 3 szerepl�sn�l bevezetettel)
     * @param string $cvc2cvv2
     *            CVC2/CVV2 k�d (azonos a 3 szerepl�sn�l bevezetettel)
     * @param string $kartyaLejarat
     *            k�rtya lej�rati d�tuma, MMyy form�ban
     * @param string $vevoNev
     *            vev� neve (alfanum, max. 50, opcion�lis, csak logozand�)
     * @param string $vevoPostaCim
     *            vev� postai c�me (alfanum, max. 100, opcion�lis)
     * @param string $vevoIPCim
     *            vev� g�p�nek IP c�me (alfanum, max. 15, opcion�lis)
     * @param string $ertesitoMail
     *            vev� ki�rtes�t�si mailc�me (alfanum, max. 50, opcion�lis, ha
     *            van, akkor mail k�ldend� a tranzakci� eredm�ny�r�l erre a
     *            c�mre)
     * @param string $ertesitoTel
     *            vev� ki�rtes�t�si telefonsz�ma (alfanum, max. 20, opcion�lis,
     *            ha van, akkor SMS k�ldend� a tranzakci� eredm�ny�r�l erre a
     *            telefonsz�mra)
     * @param mixed $ketlepcsosFizetes
     * 			  megadja, hogy k�tl�pcs�s fizet�s ind�tand�-e.
     *            True �rt�k eset�n a fizet�si tranzakci� k�tl�pcs�s lesz, 
     *            azaz a terhelend� �sszeg csup�n z�rol�sra ker�l, 
     *            s �gy is marad a bolt �ltal ind�tott lez�r� tranzakci� 
     *            ind�t�s�ig avagy a z�rol�s el�v�l�s�ig.
     *            Az alap�rtelmezett (�res) �rt�k a Bank oldalon r�gz�tett 
     *            alap�rtelmezett m�dot jel�li.       
     * @param string $zsebAzonosito
     * 			  a cafeteria k�rtya zseb azonos�t�ja.     
     * @return WResponse a tranzakci� v�lasz�t reprezent�l� value object.
     *         Sikeres v�grehajt�s eset�n a v�lasz adatokat WebShopFizetesValasz
     *         objektum reprezent�lja.
     *         Kommunik�ci�s hiba eset�n a finished flag false �rt�k� lesz!
     */
    function fizetesiTranzakcioKetszereplos(
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
            $ketlepcsosFizetes = NULL,
            $zsebAzonosito) {

        $this->logger->debug($this->operationLogNames["fizetesiTranzakcioKetszereplos"] . " indul...");

        $dom = WebShopXmlUtils::getRequestSkeleton(WF_KETSZEREPLOSFIZETES, $variables);

        // default �rt�kek feldolgoz�sa
        if (is_null($devizanem) || (trim($devizanem) == "")) {
            $devizanem = DEFAULT_DEVIZANEM;
        }

        /* param�terek beilleszt�se */
        WebShopXmlUtils::addParameter($dom, $variables, CLIENTCODE, CLIENTCODE_VALUE);
        WebShopXmlUtils::addParameter($dom, $variables, POSID, $posId);
        WebShopXmlUtils::addParameter($dom, $variables, TRANSACTIONID, $azonosito);
        WebShopXmlUtils::addParameter($dom, $variables, AMOUNT, $osszeg);
        WebShopXmlUtils::addParameter($dom, $variables, EXCHANGE, $devizanem);
        WebShopXmlUtils::addParameter($dom, $variables, LANGUAGECODE, $nyelvkod);
        WebShopXmlUtils::addParameter($dom, $variables, CONSUMERREGISTRATIONID, $regisztraltUgyfelId);
        WebShopXmlUtils::addParameter($dom, $variables, CARDNUMBER, $kartyaszam);
        WebShopXmlUtils::addParameter($dom, $variables, CVCCVV, $cvc2cvv2);
        WebShopXmlUtils::addParameter($dom, $variables, EXPIRATIONDATE, $kartyaLejarat);
        WebShopXmlUtils::addParameter($dom, $variables, NAME, $vevoNev);
        WebShopXmlUtils::addParameter($dom, $variables, FULLADDRESS, $vevoPostaCim);
        WebShopXmlUtils::addParameter($dom, $variables, IPADDRESS, $vevoIPCim);
        WebShopXmlUtils::addParameter($dom, $variables, MAILADDRESS, $ertesitoMail);
        WebShopXmlUtils::addParameter($dom, $variables, TELEPHONE, $ertesitoTel);
        WebShopXmlUtils::addParameter($dom, $variables, TWOSTAGED, RequestUtils::booleanToString($ketlepcsosFizetes));
		WebShopXmlUtils::addParameter($dom, $variables, CARDPOCKETID, $zsebAzonosito);
		
        /* al��r�s kisz�m�t�sa �s param�terk�nt besz�r�sa */
        $signatureFields = array(0 => 
            $posId, $azonosito, $osszeg, $devizanem,
            $kartyaszam, $cvc2cvv2, $kartyaLejarat, $regisztraltUgyfelId);
        $signatureText = SignatureUtils::getSignatureText($signatureFields);

        $pkcs8PrivateKey = SignatureUtils::loadPrivateKey($this->getPrivKeyFileName($this->property, $posId));
        $signature = SignatureUtils::generateSignature($signatureText, $pkcs8PrivateKey, $this->property, $this->logger);

        $attrName = null;
		$attrValue = null;
		
		if (version_compare(PHP_VERSION, '5.4.8', '>=')) {
			$attrName = 'algorithm';
			$attrValue = 'SHA512';
		}
		
        WebShopXmlUtils::addParameter($dom, $variables, CLIENTSIGNATURE, $signature, $attrName, $attrValue);

        $this->lastInputXml = WebShopXmlUtils::xmlToString($dom);
        $this->logger->info($this->operationLogNames["fizetesiTranzakcioKetszereplos"] . " keres: " . $posId . " / " . $azonosito);
        $this->logger->debug($this->operationLogNames["fizetesiTranzakcioKetszereplos"] . " keres:\n" . $this->removeSensitiveParams($this->lastInputXml));

        /* Tranzakci� adatainak napl�z�sa egy k�l�n f�jlba */
        $transLogger = new TransactionLogger(
                    $this->getTranLogDir($this->property, $posId), $this->logger);

        $transLogger->logKetszereplosFizetesInditas($posId, $azonosito, $osszeg,
                    $devizanem, $nyelvkod, $regisztraltUgyfelId, $kartyaszam,
                    $cvc2cvv2, $kartyaLejarat, $vevoNev, $vevoPostaCim, $vevoIPCim,
                    $ertesitoMail, $ertesitoTel, $ketlepcsosFizetes);

        /* Tranzakci� ind�t�sa */
        $workflowState = SoapUtils::startWorkflowSynch(WF_KETSZEREPLOSFIZETES, $this->lastInputXml, $this->soapClient, $this->logger);
        $response = new WResponse(WF_KETSZEREPLOSFIZETES, $workflowState);

        /* a folyamat v�lasz�nak napl�z�sa */
        if ($response->isFinished()) {
            $this->logger->info($this->operationLogNames["fizetesiTranzakcioKetszereplos"] . " folyamat azonosito: " 
                . $response->getInstanceId());
            $responseDom = $response->getResponseDOM();
            $this->lastOutputXml = WebShopXmlUtils::xmlToString($responseDom);
 	        $this->logger->debug($this->operationLogNames["fizetesiTranzakcioKetszereplos"] . " valasz:\n" 
                . trim($this->lastOutputXml));
            $transLogger->logKetszereplosFizetesBefejezes($azonosito, $posId, $response);
        }
        else {
            $this->logger->error($this->operationLogNames["fizetesiTranzakcioKetszereplos"] . " hiba, azonosito: " . $azonosito);  
            $this->logger->error($workflowState);
        }

        $this->logger->debug($this->operationLogNames["fizetesiTranzakcioKetszereplos"] . " befejezodott.");

        return $response;
    }
  
    /**
     * K�tl�pcs�s fizet�si tranzakci� lez�r�s�nak (WEBSHOPFIZETESLEZARAS) ind�t�sa.
     *
     * @param string $posId
     *            a shop azonos�t�ja 
     * @param string $azonosito
     *            a lez�rand� fizet�si tranzakci� egyedi azonos�t�ja 
     * @param mixed $jovahagyo
     * 			  megadja, hogy a lez�r�s j�v�hagy� vagy tilt� jelleg�, 
     *            azaz a k�tl�pcs�s fizet�s sor�n z�rolt �sszeg t�nylegesen
     *            be kell-e terhelni a vev� sz�ml�j�n, avagy storn�zni
     *            kell a t�telt.
	 * @param mixed $osszeg
     *            kisebb �sszeggel t�rt�n� komplett�roz�s eset�n megadhat� �sszeg
	 *            (num, max. 13+2), opcion�lis tizedesponttal
     * 
     * @return WResponse a tranzakci� v�lasz�t reprezent�l� value object.
     *         Sikeres v�grehajt�s eset�n a v�lasz adatokat WebShopFizetesValasz
     *         objektum reprezent�lja.
     *         Kommunik�ci�s hiba eset�n a finished flag false �rt�k� lesz!
     */
    function fizetesiTranzakcioLezaras(
            $posId,
            $azonosito, 
            $jovahagyo,
            $osszeg) {

        $this->logger->debug($this->operationLogNames["ketlepcsosFizetesLezaras"] . " indul...");

        $dom = WebShopXmlUtils::getRequestSkeleton(WF_KETLEPCSOSFIZETESLEZARAS, $variables);

        /* param�terek beilleszt�se */
        WebShopXmlUtils::addParameter($dom, $variables, CLIENTCODE, CLIENTCODE_VALUE);
        WebShopXmlUtils::addParameter($dom, $variables, POSID, $posId);
        WebShopXmlUtils::addParameter($dom, $variables, TRANSACTIONID, $azonosito);
        WebShopXmlUtils::addParameter($dom, $variables, APPROVED, RequestUtils::booleanToString($jovahagyo));
        WebShopXmlUtils::addParameter($dom, $variables, AMOUNT, $osszeg);
        
        /* al��r�s kisz�m�t�sa �s param�terk�nt besz�r�sa */
        $signatureFields = array(0 => $posId, $azonosito);
        $signatureText = SignatureUtils::getSignatureText($signatureFields);

        $pkcs8PrivateKey = SignatureUtils::loadPrivateKey($this->getPrivKeyFileName($this->property, $posId));
        $signature = SignatureUtils::generateSignature($signatureText, $pkcs8PrivateKey, $this->property, $this->logger);

        $attrName = null;
		$attrValue = null;
		
		if (version_compare(PHP_VERSION, '5.4.8', '>=')) {
			$attrName = 'algorithm';
			$attrValue = 'SHA512';
		}
		
        WebShopXmlUtils::addParameter($dom, $variables, CLIENTSIGNATURE, $signature, $attrName, $attrValue);

        $this->lastInputXml = WebShopXmlUtils::xmlToString($dom);
        $this->logger->debug($this->operationLogNames["ketlepcsosFizetesLezaras"] . " keres:\n" . $this->lastInputXml);

        /* Tranzakci� adatainak napl�z�sa egy k�l�n f�jlba */
        $transLogger = new TransactionLogger(
                    $this->getTranLogDir($this->property, $posId), $this->logger);

        $transLogger->logFizetesLezarasInditas($posId, $azonosito, $jovahagyo, $osszeg);

        /* Tranzakci� ind�t�sa */
        $workflowState = SoapUtils::startWorkflowSynch(WF_KETLEPCSOSFIZETESLEZARAS, $this->lastInputXml, $this->soapClient, $this->logger);
        $response = new WResponse(WF_KETLEPCSOSFIZETESLEZARAS, $workflowState);

        /* a folyamat v�lasz�nak napl�z�sa */
        if ($response->isFinished()) {
            $this->logger->info($this->operationLogNames["ketlepcsosFizetesLezaras"] . " folyamat azonosito: " 
                . $response->getInstanceId());
 	        $this->lastOutputXml = WebShopXmlUtils::xmlToString($response->getResponseDOM());
            $this->logger->debug($this->operationLogNames["fizetesiTranzakcioLezaras"] . " valasz:\n" 
                . trim($this->lastOutputXml));
            $transLogger->logKetszereplosFizetesBefejezes($azonosito, $posId, $response);
        }
        else {
            $this->logger->error($this->operationLogNames["ketlepcsosFizetesLezaras"] . " hiba, azonosito: " . $azonosito);  
            $this->logger->error($workflowState);
        }

        $this->logger->debug($this->operationLogNames["ketlepcsosFizetesLezaras"] . " befejezodott.");

        return $response;
    }

    /**
     * WEBSHOPTRANZAKCIOLEKERDEZES folyamat szinkron ind�t�sa polloz�s c�lj�b�l.
     * A bank nem javasolja, hogy polloz�sos technik�val t�rt�njen a fizet�si
     * tranzakci�k eredm�ny�nek lek�rdez�se - mindazon�ltal kommunik�ci�s vagy
     * egy�b hiba eset�n ez az egyetlen m�dja annak, hogy a tranzakci� v�lasz�t
     * ut�lag le lehessen k�rdezni.
     * 
     * @param string $posId webshop azonos�t�
     * @param string $azonosito lek�rdezend� tranzakci� azonos�t�
     * @param int $inditas a tranzakci� ind�t�sa az ind�t� kliens �r�ja szerint 
     *                     (a lek�rdez�s +-24 �r�ra fog korl�toz�dni)
     * 
     * @return mixed Sikeres lek�rdez�s �s l�tez� tranzakci� eset�n 
     *               a vonatkoz� WebShopFizetesAdatok. A tranzakci� �llapot�t
     *               ez az objektum fogja tartalmazni - ami utalhat p�ld�ul 
     *               vev� oldali input v�rakoz�sra vagy feldolgozott st�tuszra.
     *               FALSE hib�s lek�rdez�s eset�n. (Pl. nem l�tezik tranzakci�)
     */
    function tranzakcioPoll($posId, $azonosito,  $inditas) {

        $maxRekordSzam = "1";
        $idoszakEleje = $inditas - 60*60*24;
        $idoszakVege = $inditas + 60*60*24;
                
        $tranzAdatok = false;                        
        $response = $this->tranzakcioStatuszLekerdezes($posId, $azonosito, $maxRekordSzam, $idoszakEleje, $idoszakVege);
        if ($response) {
            $answer = $response->getAnswer();
            if ($response->isSuccessful()
                    && $response->getAnswer()
                    && count($answer->getWebShopFizetesAdatok()) > 0) {

                // Siker�lt lek�rdezni az adott tranzakci� adat�t
                $fizetesAdatok = $answer->getWebShopFizetesAdatok();
                $tranzAdatok = reset($fizetesAdatok);
            }
        }
        return $tranzAdatok;
    }
    
    /**
     * Fizet�s j�v��r�s tranzakci� (WEBSHOPFIZETESJOVAIRAS) ind�t�sa.
     *
     * @param string $posId
     *            a shop azonos�t�ja 
     * @param string $azonosito
     *            a j�v��rand� fizet�si tranzakci� egyedi azonos�t�ja 
     * @param mixed $osszeg
     *            v�s�rl�s �sszege (num, max. 13+2), opcion�lis tizedesponttal
     * 
     * @return WResponse a tranzakci� v�lasz�t reprezent�l� value object.
     *         Sikeres v�grehajt�s eset�n a v�lasz adatokat WebShopJovairasValasz
     *         objektum reprezent�lja.
     *         Kommunik�ci�s hiba eset�n a finished flag false �rt�k� lesz!
     */
    function fizetesJovairas(
            $posId,
            $azonosito, 
            $osszeg) {

        $this->logger->debug($this->operationLogNames["fizetesJovairas"] . " indul...");

        $dom = WebShopXmlUtils::getRequestSkeleton(WF_FIZETESJOVAIRAS, $variables);

        /* param�terek beilleszt�se */
        WebShopXmlUtils::addParameter($dom, $variables, CLIENTCODE, CLIENTCODE_VALUE);
        WebShopXmlUtils::addParameter($dom, $variables, POSID, $posId);
        WebShopXmlUtils::addParameter($dom, $variables, TRANSACTIONID, $azonosito);
        WebShopXmlUtils::addParameter($dom, $variables, AMOUNT, $osszeg);
        
        /* al��r�s kisz�m�t�sa �s param�terk�nt besz�r�sa */
        $signatureFields = array(0 => $posId, $azonosito, $osszeg);
        $signatureText = SignatureUtils::getSignatureText($signatureFields);

        $pkcs8PrivateKey = SignatureUtils::loadPrivateKey($this->getPrivKeyFileName($this->property, $posId));
        $signature = SignatureUtils::generateSignature($signatureText, $pkcs8PrivateKey, $this->property, $this->logger);

        $attrName = null;
		$attrValue = null;
		
		if (version_compare(PHP_VERSION, '5.4.8', '>=')) {
			$attrName = 'algorithm';
			$attrValue = 'SHA512';
		}
		
        WebShopXmlUtils::addParameter($dom, $variables, CLIENTSIGNATURE, $signature, $attrName, $attrValue);

        $this->lastInputXml = WebShopXmlUtils::xmlToString($dom);
        $this->logger->debug($this->operationLogNames["fizetesJovairas"] . " keres:\n" . $this->lastInputXml);

        /* Tranzakci� adatainak napl�z�sa egy k�l�n f�jlba */
        $transLogger = new TransactionLogger(
                    $this->getTranLogDir($this->property, $posId), $this->logger);

        $transLogger->logFizetesJovairasInditas($posId, $azonosito, $osszeg);

        /* Tranzakci� ind�t�sa */
        $workflowState = SoapUtils::startWorkflowSynch(WF_FIZETESJOVAIRAS, $this->lastInputXml, $this->soapClient, $this->logger);
        $response = new WResponse(WF_FIZETESJOVAIRAS, $workflowState);

        /* a folyamat v�lasz�nak napl�z�sa */
        if ($response->isFinished()) {
            $this->logger->info($this->operationLogNames["fizetesJovairas"] . " folyamat azonosito: " 
                . $response->getInstanceId());
 	        $this->lastOutputXml = WebShopXmlUtils::xmlToString($response->getResponseDOM());
            $this->logger->debug($this->operationLogNames["fizetesJovairas"] . " valasz:\n" 
                . trim($this->lastOutputXml));
            $transLogger->logKetszereplosFizetesBefejezes($azonosito, $posId, $response);
        }
        else {
            $this->logger->error($this->operationLogNames["fizetesJovairas"] . " hiba, azonosito: " . $azonosito);  
            $this->logger->error($workflowState);
        }

        $this->logger->debug($this->operationLogNames["fizetesJovairas"] . " befejezodott.");

        return $response;
    }
    
    
    /**
     * WEBSHOPTRANZAKCIOLEKERDEZES folyamat szinkron ind�t�sa.
     * 
     * @param string $posId webshop azonos�t�
     * @param string $muvelet m�velet
     * 
     * @return WResponse a tranzakci� v�lasz�t reprezent�l� value object.
     *         Sikeres v�grehajt�s eset�n a v�lasz adatokat WebShopAdatokLista
     *         objektum reprezent�lja.
     *         Kommunik�ci�s hiba eset�n a finished flag false �rt�k� lesz!
     */
    function kulcsLekerdezes($posId, $muvelet) {
        
        global $variables;
        
        $this->logger->debug($this->operationLogNames["kulcsLekerdezes"] . " indul...");

        $dom = WebShopXmlUtils::getRequestSkeleton(WF_KULCSLEKERDEZES, $variables);
        
        /* param�terek beilleszt�se */
        WebShopXmlUtils::addParameter($dom, $variables, CLIENTCODE, CLIENTCODE_VALUE);
        WebShopXmlUtils::addParameter($dom, $variables, POSID, $posId);
        WebShopXmlUtils::addParameter($dom, $variables, MUVELET, $muvelet);

        /* al��r�s kisz�m�t�sa �s param�terk�nt besz�r�sa */
        $signatureFields = array(0 => $posId, $muvelet);
        $signatureText = SignatureUtils::getSignatureText($signatureFields);

        $pkcs8PrivateKey = SignatureUtils::loadPrivateKey($this->getPrivKeyFileName($this->property, $posId));
        $signature = SignatureUtils::generateSignature($signatureText, $pkcs8PrivateKey, $this->property, $this->logger);

        $attrName = null;
		$attrValue = null;
		
		if (version_compare(PHP_VERSION, '5.4.8', '>=')) {
			$attrName = 'algorithm';
			$attrValue = 'SHA512';
		}
		
        WebShopXmlUtils::addParameter($dom, $variables, CLIENTSIGNATURE, $signature, $attrName, $attrValue);

        $this->lastInputXml = WebShopXmlUtils::xmlToString($dom);
        $this->logger->debug($this->operationLogNames['kulcsLekerdezes'] . " keres:\n" . $this->lastInputXml);
		
        /* a folyamat ind�t�sa */
        $workflowState = SoapUtils::startWorkflowSynch(WF_KULCSLEKERDEZES, $this->lastInputXml, $this->soapClient, $this->logger);
        $response = new WResponse(WF_KULCSLEKERDEZES, $workflowState);

        /* a folyamat v�lasz�nak napl�z�sa */
        if ($response->isFinished()) {
            $this->logger->info($this->operationLogNames['kulcsLekerdezes'] . ' folyamat azonosito: ' 
                . $response->getInstanceId());
            $responseDom = $response->getResponseDOM();
            $this->lastOutputXml = WebShopXmlUtils::xmlToString($responseDom);
            $this->logger->debug($this->operationLogNames['kulcsLekerdezes'] . " valasz:\n" 
                . trim($this->lastOutputXml));
        }
        else {
            $this->logger->error($this->operationLogNames['kulcsLekerdezes'] . ' hiba!');
            $this->logger->error($workflowState);            
        }

        $this->logger->debug($this->operationLogNames['kulcsLekerdezes'] . ' befejezodott.');

        return $response;
    }
    
    function removeSensitiveParams($inputXml) {

      $cvcPattern = '/(.*)(<' . CVCCVV . '>)(\d+)(<\/' . CVCCVV . '>)(.*)/i';
      $cvcReplacement = '$1$2***$4$5';
      $expDatePattern = '/(.*)(<' . EXPIRATIONDATE . '>)(\d+)(<\/' . EXPIRATIONDATE . '>)(.*)/i';
      $expDateReplacement = '$1$2****$4$5';

      $result = preg_replace(array($cvcPattern,$expDatePattern), array($cvcReplacement, $expDateReplacement), $inputXml);

      return $result;

     }

}

?>