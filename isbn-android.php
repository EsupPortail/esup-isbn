<?php

header('Content-Type: text/html; charset=utf-8');

$current_url = 'http://localhost/cgi-bin/isbn-android.php';
$dest = "zxing://scan/?ret=" . urlencode($current_url . "?code={CODE}");

if ($_GET["code"]) {
    header("Location: " . search_flora($_GET["code"]));
} else {

?>
<html>
  <meta name="viewport" content="width=device-width">
    <a href="<?php echo $dest ?>">
       <img src="//zxing.appspot.com/img/app.png">
       <br>scan barcode
   </a>
   <p>
   <br>To scan code with your mobile camera you need to install free Barcode Scanner -app
   <br><a href="market://details?id=com.google.zxing.client.android"><img src="//zxing.appspot.com/img/badge.png"></a>
</html>
<?php
}

function search_flora($code) {

    $login_url = 'http://flora.univ-rouen.fr/flora/servlet/LoginServlet';
    $ch=curl_init($login_url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    // get headers too with this line
    curl_setopt($ch, CURLOPT_HEADER, 1);
    $login_postfields = 'success=jsp%2Fsystem%2Fwin_main.jsp&failure=jsp%2Ferror.jsp&profile=anonymous&connectionSiteId=1&connectionSiteLabel=Site+1';
    curl_setopt($ch, CURLOPT_POSTFIELDS, $login_postfields);
    $result = curl_exec($ch);
    preg_match('/^Set-Cookie: JSESSIONID=([^;]*)/mi', $result, $cookies);
    curl_close($ch);

        
    $search_url = 'http://flora.univ-rouen.fr/flora/servlet/ActionFlowManager?confirm=action_confirm&forward=action_forward&action=search';
    $search_postfields = 'SCD_VISIBLE=O&INDEX_LIV=default.UNIMARC.EAN&query=SIMPLE_ROUEN_PUBLIC&source=default&sysFormTagHidden=&ActionManagerInit=true&CRIT=' . $code;
    $ch=curl_init($search_url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $search_postfields);
    $cookies_all = 'JSESSIONID=' . $cookies[1] . ';  FLORA_SESSION=1';
    curl_setopt( $ch, CURLOPT_COOKIE, $cookies_all); 

    $html = curl_exec($ch);
    
    if (preg_match("/sysDoAction\('(.*)', '(.*)', '(.*)', '(.*)'\).*/", $html, $m)) {
        $result_url = 'http://flora.univ-rouen.fr/flora/jsp/index_view_direct.jsp?' . $m[4];
        return $result_url;
    } else {
?>

        <html>
         <p>
            <b>Aucun réssulat.</b>
            <hr/>
            Vous pouvez contacter un bibliothécaire à l'accueil ou <a href="http://documentation.univ-rouen.fr/reponse-a-distance-ubib-338833.kjsp" target="_blank">directement en ligne</a>
         </p>
          <meta name="viewport" content="width=device-width">
            <a href="<?php echo $dest ?>">
               <img src="//zxing.appspot.com/img/app.png">
               <br>scan barcode
           </a>
           <p>
           <br>To scan code with your mobile camera you need to install free Barcode Scanner -app
           <br><a href="market://details?id=com.google.zxing.client.android"><img src="//zxing.appspot.com/img/badge.png"></a>
        </html>
        
        
<?php
        exit(0);
    }
}

function curl($url) {
  //echo "getting $url\n";
  $ch=curl_init($url);
  curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
  $output=curl_exec($ch);
  curl_close($ch);
  return $output;
}
?>
