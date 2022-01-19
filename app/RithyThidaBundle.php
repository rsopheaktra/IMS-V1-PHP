<?php
    // tell system what function to use as the error handler
    set_error_handler('errorHandler');
    if (version_compare(phpversion(), '5.0.0', '<')) {
       // these do not exist in PHP 4, so define them manually
       define('E_STRICT', 2048);
       define('E_RECOVERABLE_ERROR', 4096);
       define('E_DEPRECATED', 8192);
       define('E_USER_DEPRECATED', 16384);
    } else {
       // new constants were added in new versions of PHP 5
       if (version_compare(phpversion(), '5.2.0', '<')) {
          // this does not exist before 5.2, so define manually
          define('E_RECOVERABLE_ERROR', 4096);
       } // if
       if (version_compare(phpversion(), '5.3.0', '<')) {
          // these do not exist before 5.3, so define manually
          define('E_DEPRECATED', 8192);
          define('E_USER_DEPRECATED', 16384);
       } // if
    } // if

    //function errorHandler ($errno, $errstr, $errfile, $errline, $errcontext)
    function errorHandler ($errno, $errstr, $errfile, $errline) 
    {
       // If the error condition is E_USER_ERROR or above then abort.
  
       echo "Start Script : <br/>";
       echo "<b>Error:</b> [$errno] $errstr<br>";
       echo "Ending Script";
       die();
    }

    function CommaToDot($money) {
        return str_replace(',', '.', $money);
    }

    function NumberToEuro($number,$digits) {
        $Euro = str_replace('.', ',', number_format($number, $digits) );
        $Euro = str_replace('.', ' ', $Euro);
        return $Euro . ' ' . currencySymbole('Euros');
    }

    function StringToEuro($string) {
        return $string . ' ' . currencySymbole('Euros');
    }

    function NumberToDollar($number,$digits) {
        $Euro = str_replace('.', ',', number_format($number, $digits) );
        $Euro = str_replace('.', ' ', $Euro);
        return $Euro . ' ' . currencySymbole('Dollars');
    } 

    function phpAlert($msg) {
        echo '<script type="text/javascript">alert("' . $msg . '")</script>';
    }

    function currencySymbole($sym) {
        $currency = array(
            'Dollars' => '$',
            'Euros' => '€',
            'Riels' => '៛'
        );

        return $currency[$sym];
    }

    function weightSymbole($sym) {
        $weight = array(
            'Ton' => 'T',
            'Kilogrammes' => 'Kg',
            'Grammes' => 'g'
        );

        return $weight[$sym];
    }

    function liquidSymbole($sym) {
        $liquid = array(
            'Litres' => 'L',
            'Decilitre' => 'dl',
            'Centilitre' => 'cl'
        );

        return $liquid[$sym];
    }

    /* Encryption and Descryption */
    function sign($message, $key) {
        return hash_hmac('sha256', $message, $key) . $message;
    }

    function verify($bundle, $key) {
        return hash_equals(
            hash_hmac('sha256', mb_substr($bundle, 64, null, '8bit'), $key),
            mb_substr($bundle, 0, 64, '8bit')
        );
    }

    function getKey($password, $keysize = 16) {
        return hash_pbkdf2('sha256',$password,'some_token',100000,$keysize,true);
    }

    function encrypt($message, $password) {
        $iv = random_bytes(16);
        $key = getKey($password);
        $result = sign(openssl_encrypt($message,'aes-256-ctr',$key,OPENSSL_RAW_DATA,$iv), $key);
        return bin2hex($iv).bin2hex($result);
    }

    function decrypt($hash, $password) {
        $iv = hex2bin(substr($hash, 0, 32));
        $data = hex2bin(substr($hash, 32));
        $key = getKey($password);
        if (!verify($data, $key)) {
           return null;
        }
        return openssl_decrypt(mb_substr($data, 64, null, '8bit'),'aes-256-ctr',$key,OPENSSL_RAW_DATA,$iv);
    }
/* //call function 
$string_to_encrypt = 'ROS Sopheaktra';
$password = 'password';
$encrypted_string = encrypt($string_to_encrypt, $password);
$decrypted_string = decrypt($encrypted_string, $password);
*/

/**
 * create file with content, and create folder structure if doesn't exist 
 * @param String $filepath
 * @param String $message
 */
function writeFile ($filepath, $message){
    try {
        $isInFolder = preg_match("/^(.*)\/([^\/]+)$/", $filepath, $filepathMatches);
        if($isInFolder) {
            $folderName = $filepathMatches[1];
            $fileName = $filepathMatches[2];
            if (!is_dir($folderName)) {
                mkdir($folderName, 0777, true);
            }
        }
        file_put_contents($filepath, $message);
    } catch (Exception $e) {
        echo "ERR: error writing '$message' to '$filepath', ". $e->getMessage();
    }
}

function readFiles($filepath){
    return file_get_contents($filepath);
}

?>