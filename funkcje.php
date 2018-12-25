<?php
require_once 'config.php';
/**
 * 
 * @param Object $object wskaźnik na połączenie do Zimbra
 * @param string $property nazwa pola poszukiwanego
 * @return string Wartosc pola poszukiwanego
 */
function getAccountProp($object,$property){
		foreach($object as &$value){
		if ($value->n == $property){
			return $value->_;
		}
		}
		return "";
	}

/**
 * getZimbraUserAttr - pobieranie atrybutu konta ZIMBRA
 * @param Object $api Uchwyt do połaczenia Zimbra
 * @param string $Email Użytkownik którego czytamy atrybuty (np. "user@com.pl")
 * @param string $Attr Poszukiwany atrybut (np. "zimbraAccountStatus")
 * @return string|null Wartość oczekiwana dla danego atrybutu zgodna z Zimbra - null gdy nie znaleziono użytkownika 
 * Example getZimbraUserAttr('server.com.pl', 'admin@com.pl', 'password', 'user@com.pl', 'zimbraAccountStatus')
*/
function getZimbraUserAttr($api, $Email, $Attr){
	
	$account = new \Zimbra\Struct\AccountSelector(\Zimbra\Enum\AccountBy::NAME(), $Email);
	try {
		$accountInfo = $api->getAccount($account);
	} catch (Exception $e) {
		return "";
	} 
	
	return getAccountProp($accountInfo->account->a,$Attr);
	
}

/**
 * getCNofDN - wydobywanie CN z ciągu DN
 * @param string $dn ciąg zgodny z LDAP DN
 * @return string Pierwsza wartość z ciągu DN
 * Example getCNofDN('cn=user,dc=com,dc=pl')
*/
function getCNofDN($dn) {
	$return=preg_match('/[^cn=]([^,]*)/i',$dn,$dn);
	return($dn[0]);
}

/**
* getUserMailAccountPicture - status do obrazka
* @param string $konto Adres email konta Zimbra
* @param Object $api wskaźnik na obiekt połaczenia do Zimbra
* @return string Obrazek w zapisie HTML z obrazkiem statusu
*/					
function getUserMailAccountPicture($konto, $api) {

    $account_name = $konto.'@'.$_SESSION['mailDomain'];
    //echo $account_name;
	$info = getZimbraUserAttr($api, $account_name, 'zimbraAccountStatus');
	//echo $info;
	switch ($info) {
		case 'active':
			$result = "<IMG SRC=\"images/zimbra-ok.png\" title=\"ZIMBRA_active\" width=15>";
			break;
		case 'lockout':
			$result = "<IMG SRC=\"images/zimbra-close.png\" title=\"ZIMBRA_lockout\" width=15>&nbsp;";
			break;
		case 'closed':
			$result = "<IMG SRC=\"images/zimbra-close.png\" title=\"ZIMBRA_closed\" width=15>&nbsp;&nbsp;";
			break;
		default:
			$result = "";
	}
	
	return $result;	
}

/**
//  * Wyswietlanie obrazka statusu
 * @param integer $inputCode wartosc pola useraccesscontrol
 * @return NULL|string kod HTML do wyswietlenia obrazka statusu
 */
function getUserAccountControlAttributesPicture($inputCode)
{

$userAccountControlFlags = array(16777216 => "<IMG SRC=\"images/pytanie.png\" title=\"TRUSTED_TO_AUTH_FOR_DELEGATION\" width=15>",
8388608 => "<IMG SRC=\"images/passexpired.png\" title=\"PASSWORD_EXPIRED\" width=15>",
4194304 => "<IMG SRC=\"images/pytanie.png\" title=\"DONT_REQ_PREAUTH\" width=15>",
2097152 => "<IMG SRC=\"images/pytanie.png\" title=\"USE_DES_KEY_ONLY\" width=15>",
1048576 => "<IMG SRC=\"images/pytanie.png\" title=\"NOT_DELEGATED\" width=15>",
524288 => "<IMG SRC=\"images/pytanie.png\" title=\"TRUSTED_FOR_DELEGATION\" width=15>",
262144 => "<IMG SRC=\"images/pytanie.png\" title=\"SMARTCARD_REQUIRED\" width=15>",
131072 => "<IMG SRC=\"images/pytanie.png\" title=\"MNS_LOGON_ACCOUNT\" width=15>",
65536 => "<IMG SRC=\"images/nieskonczony.png\" title=\"DONT_EXPIRE_PASSWORD\" width=15>",
8192 => "<IMG SRC=\"images/pytanie.png\" title=\"SERVER_TRUST_ACCOUNT\" width=15>",
4096 => "<IMG SRC=\"images/pytanie.png\" title=\"WORKSTATION_TRUST_ACCOUNT\" width=15>",
2048 => "<IMG SRC=\"images/pytanie.png\" title=\"INTERDOMAIN_TRUST_ACCOUNT\" width=15>",
512 => "<IMG SRC=\"images/persona.jpg\" title=\"NORMAL_ACCOUNT\" width=15>",
256 => "<IMG SRC=\"images/pytanie.png\" title=\"TEMP_DUPLICATE_ACCOUNT\" width=15>",
128 => "<IMG SRC=\"images/pytanie.png\" title=\"ENCRYPTED_TEXT_PWD_ALLOWED\" width=15>",
64 => "<IMG SRC=\"images/passnotchange.png\" title=\"PASSWD_CANT_CHANGE\" width=15>",
32 => "<IMG SRC=\"images/passnotreq.png\" title=\"PASSWD_NOTREQD\" width=15>",
16 => "<IMG SRC=\"images/lockout.png\" title=\"LOCKOUT\" width=15>",
8 => "<IMG SRC=\"images/homedirreq.png\" title=\"HOMEDIR_REQUIRED\" width=15>",
2 => "<IMG SRC=\"images/disabled.png\" title=\"ACCOUNTDISABLE\" width=15>",
1 => "<IMG SRC=\"images/script.jpg\" title=\"SCRIPT\" width=15>");

$attributes = NULL;
while($inputCode > 0) {
    foreach($userAccountControlFlags as $flag => $flagName) {
        $temp = $inputCode-$flag;
        if($temp>0) {
            $attributes=$attributes.$userAccountControlFlags[$flag];
            $inputCode = $temp;
        }
        if($temp==0) {
            if(isset($userAccountControlFlags[$inputCode])) {
                $attributes=$attributes.$userAccountControlFlags[$inputCode];
            }
            $inputCode = $temp;
        }
    }
}
return $attributes;
}


/**
 * Zmiana czasu windows na linux
 * @param DateTime $ldapTime
 * @return number
 */
function ldapTimeToUnixTime($ldapTime) {
  $secsAfterADEpoch = $ldapTime / 10000000;
  $ADToUnixConverter = ((1970 - 1601) * 365 - 3 + round((1970 - 1601) / 4)) * 86400;
  return intval($secsAfterADEpoch - $ADToUnixConverter);
}


/**
 * Zamiana czasu
 * @param DateTime $timestamp
 * @param boolean $smal format daty true-krotka, false-dluga data z czasem
 * @return string
 */
function convertLdapTimeStamp($timestamp, $smal){
        //PHP script to convert a timestamp returned from an LDAP query into a Unix timestamp 
        // The date as returned by LDAP in format yyyymmddhhmmsst
        $date = $timestamp;

        // Get the individual date segments by splitting up the LDAP date
        $year = substr($date,0,4);
        $month = substr($date,4,2);
        $day = substr($date,6,2);
        $hour = substr($date,8,2);
        $minute = substr($date,10,2);
        $second = substr($date,12,2);

        // Make the Unix timestamp from the individual parts
        $timestamp = mktime($hour, $minute, $second, $month, $day, $year);

        // Output the finished timestamp
		if ($day != "") {
			if ($smal) {
				return $year."-".$month."-".$day;
			} else {
			return $year."-".$month."-".$day." ".$hour.":".$minute.":".$second;
			}
		} else {
			return "";
		}
    }

	
?>