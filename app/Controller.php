<?php
abstract class Controller{
    /**
     * Afficher une vue
     *
     * @param string $fichier
     * @param array $data
     * @return void
     */
    public function render(string $fichier, array $data = [], $layout = 'default', array $addCSS = [], array $addJS = []){
        
        //This function uses array keys as variable names and values as variable values. 
        extract($data);
        //extract($addCSS);
        //extract($addJS);

        $device = 'mobile';
        if (!$this->isMobileDev()) {
           $device = 'desktop';
        }
        //define('Image_Dir','views/' .$device. '/'.strtolower(get_class($this)).'/images/');
        //echo ImgURL ;
        // On démarre le buffer de sortie
        ob_start();

        // On génère la vue
        require_once(ROOT.'views/' .$device. '/'.strtolower(get_class($this)).'/'.$fichier.'.php');

        // On stocke le contenu dans $content
        $content = ob_get_clean();

        // On fabrique le "template"
        
        require_once(ROOT.'views/' .$device. '/layout/' . $layout . '.php');
    }

    /**
     * Permet de charger un modèle
     *
     * @param string $model
     * @return void
     */
    public function loadModel(string $model){
       
        // On va chercher le fichier correspondant au modèle souhaité
        require_once(ROOT.'models/'.$model.'.php');
        
        // On crée une instance de ce modèle. Ainsi "Article" sera accessible par $this->Article
        $this->$model = new $model();
    }

    /**
     * Permet de charger un modèle
     *
     * @param string $model
     * @return void
     */
    public function loadObject(string $object){
       
        // On va chercher le fichier correspondant au modèle souhaité
        require_once(ROOT.'objects/'.$object.'.php');
        
        // On crée une instance de ce modèle. Ainsi "Article" sera accessible par $this->Article
        $this->$object = new $object();
    }

    public function sendMail($shop, $customer, $messageMail) {
        /* Start sending mail */
        $destinataire = $customer['email'] ; //$_POST['email']; // 'rsopheaktra@gmail.com';
        $expediteur = $shop['email']; //$_POST['emailExpediteur']; //'rsopheaktra@gmail.com';
        $nomExpediteur = $shop['title']; //$_POST['nom'];
        $copie = $shop['email']; //$_POST['emailcc']; //'rsopheaktra@gmail.com';
        $copie_cachee = $shop['email']; //$_POST['emailcc']; // 'rsopheaktra@gmail.com';
        $objet = 'Votre commande chez ' . $nomExpediteur; //$_POST['sujet']; //'Test'; // Objet du message

        $headers  = 'MIME-Version: 1.0' . "\n"; // Version MIME
        $headers .= 'Content-type: text/html; charset=ISO-8859-1'."\n"; // l'en-tete Content-type pour le format HTML
        $headers .= 'Reply-To: '.$expediteur."\n"; // Mail de reponse
        $headers .= 'From: ' .$nomExpediteur. '<' .$expediteur. '>'."\n"; // Expediteur
        $headers .= 'Delivered-to: '.$destinataire."\n"; // Destinataire
        $headers .= 'Cc: '.$copie."\n"; // Copie Cc
        $headers .= 'Bcc: '.$copie_cachee."\n\n"; // Copie cachée Bcc        
        
        $sendmail = mail($destinataire, $objet, $messageMail, $headers);
        //$sendmail = true;
        if ($sendmail) // Envoi du message
        {
           return true;
        }
        return false;
    }

    public function Language() {
         $lang = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2); 
         $acceptLang = ['fr', 'kh', 'en']; 
         $lang = in_array($lang, $acceptLang) ? $lang : 'en';
         return $lang;
     }

     public function isMobileDev(){ 
          if( !empty($_SERVER['HTTP_USER_AGENT']) ){ 
              $user_ag = $_SERVER['HTTP_USER_AGENT']; 
              if (preg_match('/(Mobile|Android|Tablet|GoBrowser|[0-9]x[0-9]*|uZardWeb\/|Mini|Doris\/|Skyfire\/|iPhone|Fennec\/|Maemo|Iris\/|CLDC\-|Mobi\/)/uis',$user_ag)){ 
                 return true; 
              }
          } 
          return false; 
     }

     public function isWindowDev(){ 
          if( !empty($_SERVER['HTTP_USER_AGENT']) ){ 
              $user_ag = $_SERVER['HTTP_USER_AGENT']; 
              if (preg_match('/windows|win32/i',$user_ag)){ 
                 return true; 
              }
          } 
          return false; 
     }

     public function NumberToEuro($number,$digits) {
          $Euro = str_replace('.', ',', number_format($number, $digits) );
          $Euro = str_replace('.', ' ', $Euro);
          return $Euro . ' €';
     }
/* 
     $user_agent = $_SERVER['HTTP_USER_AGENT']; 
     public function getOS() { 
        global $user_agent; 
        $os_platform = "Unknown OS Platform"; 
        $os_array = array( '/windows nt 10/i' => 'Windows 10', '/windows nt 6.3/i' => 'Windows 8.1', '/windows nt 6.2/i' => 'Windows 8', '/windows nt 6.1/i' => 'Windows 7', '/windows nt 6.0/i' => 'Windows Vista', '/windows nt 5.2/i' => 'Windows Server 2003/XP x64', '/windows nt 5.1/i' => 'Windows XP', '/windows xp/i' => 'Windows XP', '/windows nt 5.0/i' => 'Windows 2000', '/windows me/i' => 'Windows ME', '/win98/i' => 'Windows 98', '/win95/i' => 'Windows 95', '/win16/i' => 'Windows 3.11', '/macintosh|mac os x/i' => 'Mac OS X', '/mac_powerpc/i' => 'Mac OS 9', '/linux/i' => 'Linux', '/ubuntu/i' => 'Ubuntu', '/iphone/i' => 'iPhone', '/ipod/i' => 'iPod', '/ipad/i' => 'iPad', '/android/i' => 'Android', '/blackberry/i' => 'BlackBerry', '/webos/i' => 'Mobile' );
        foreach ($os_array as $regex => $value) if (preg_match($regex, $user_agent)) $os_platform = $value; 
        return $os_platform; 
     } 
     public function getBrowser() { 
        global $user_agent; 
        $browser = "Unknown Browser"; 
        $browser_array = array( '/msie/i' => 'Internet Explorer', '/firefox/i' => 'Firefox', '/safari/i' => 'Safari', '/chrome/i' => 'Chrome', '/edge/i' => 'Edge', '/opera/i' => 'Opera', '/netscape/i' => 'Netscape', '/maxthon/i' => 'Maxthon', '/konqueror/i' => 'Konqueror', '/mobile/i' => 'Handheld Browser' ); 
        foreach ($browser_array as $regex => $value) if (preg_match($regex, $user_agent)) $browser = $value; 
        return $browser; 
     } 
     $user_os = getOS(); 
     $user_browser = getBrowser(); 
     $device_details = "<strong>Browser: </strong>".$user_browser."<br /><strong>Operating System: </strong>".$user_os.""; 
     print_r($device_details); 
     echo("<br /><br /><br />".$_SERVER['HTTP_USER_AGENT']."");
*/
}