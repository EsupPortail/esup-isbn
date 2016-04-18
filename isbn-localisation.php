<?php

if ($_GET["ISBN"]) {
   $cod_isbn=$_GET["ISBN"];

   $fp = fopen ("localisation.js", "r"); 
   $contenu_du_fichier = fread ($fp, filesize('localisation.js')); 
   fclose ($fp); 
   $json = json_decode ($contenu_du_fichier,true);

   $nb_select=0;
   for($i=0;$i<(sizeof($json['localisation']));$i++) 
   {
     if($json['localisation'][$i]['select']) {
        $nb_select++;
     } 
   }

   if ($nb_select==1) {
        for($i=0;$i<(sizeof($json['localisation']));$i++) 
        {
        	if($json['localisation'][$i]['select']) {
             	$url = search_url($json['localisation'][$i]['URL'],$json['localisation'][$i]['type']);
              header("Location: " . $url);
         }
        }
   } else {
    	?>
    	<html>
          	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
       	<form action="isbn-android.php" method="get">
           	<meta name="viewport" content="width=device-width">
           	<h2><center>Disponibilité d'un ouvrage en BU</center></h2>
           	<br><br>
           	<br><center>
    	<?php
   	  switch ($nb_select) {
       		case 0:
           		echo "Vous devez sélectionner au moins une localisation dans le fichier localisation.js !";
           		break;
       		default :
           		echo "Différentes localisations possibles :<br>";
           		?>
                    	<?php
                     	for($i=0;$i<(sizeof($json['localisation']));$i++) 
                     	{
                       		if($json['localisation'][$i]['select']) {
                       		//preparation de l'URL :
                       		$url = search_url($json['localisation'][$i]['URL'],$json['localisation'][$i]['type']);
                     	?>
                       		<a href="<?php echo $url;?>"><? echo $json['localisation'][$i]['name'] ?></a>
                       		<br>
                     	<?php
                       		}
                     	}
                    	?>
                     	</center>
                  	</form>
               		</html>
           		<?php
	   		break;
   	}
   }
} else {
    echo "il manque l'argument ISBN dans l'URL !";
}


function search_url($url,$type) {

   switch ($type) {
       case 'PPN':
           $search_code= search_ppn($GLOBALS['cod_isbn']);
           if (!$search_code) {
               return "";
           } else {
               $search_url=str_replace("<code>",$search_code,$url);
           }
           break;
       case 'ISBN':
           $search_code= $GLOBALS['cod_isbn'];
           if (!$search_code) {
               return "";
           } else {
               $search_url=str_replace("<code>",$search_code,$url);
           }
           break;
       case 'FLORA':
           $search_url= search_flora($url,$GLOBALS['cod_isbn']); 
	   break;
       default :
           $search_code= $GLOBALS['cod_isbn'];
           if (!$search_code) {
               return "";
           } else {
               $search_url=str_replace("<code>",$search_code,$url);
           }
           break;
    }
    return $search_url;
}



function search_ppn($code_isbn) {

    $search_ppn=str_replace("<code_isbn>",$code_isbn,$GLOBALS['ws_isbntoppn']);
    $timeout=5;
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $search_ppn);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, $timeout);
    // ... set others params and options ...

    $decod_result = json_decode(curl_exec($curl),true);
    curl_close($curl);
    $ppn=$decod_result['sudoc']['query']['result']['ppn'];
    if (!$ppn){
        $ppn=$decod_result['sudoc']['query']['result'][0]['ppn'];
    }
    return $ppn;
}


function search_flora($base_url,$code_isbn) {
    $login_url = $base_url.'/servlet/LoginServlet';
   
    $ch=curl_init($login_url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    // get headers too with this line
    curl_setopt($ch, CURLOPT_HEADER, 1);
    $login_postfields = 'success=jsp%2Fsystem%2Fwin_main.jsp&failure=jsp%2Ferror.jsp&profile=anonymous&connectionSiteId=1&connectionSiteLabel=Site+1';
    curl_setopt($ch, CURLOPT_POSTFIELDS, $login_postfields);
    $result = curl_exec($ch);
    preg_match('/^Set-Cookie: JSESSIONID=([^;]*)/mi', $result, $cookies);
    curl_close($ch);
        
    $search_url = $base_url.'/servlet/ActionFlowManager?confirm=action_confirm&forward=action_forward&action=search';
    $search_postfields = 'SCD_VISIBLE=O&INDEX_LIV=default.UNIMARC.EAN&query=SIMPLE_ROUEN_PUBLIC&source=default&sysFormTagHidden=&ActionManagerInit=true&CRIT=' . $code_isbn;
    $ch=curl_init($search_url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $search_postfields);
    $cookies_all = 'JSESSIONID=' . $cookies[1] . ';  FLORA_SESSION=1';
    curl_setopt( $ch, CURLOPT_COOKIE, $cookies_all); 
    $html = curl_exec($ch);
    
    if (preg_match("/sysDoAction\('(.*)', '(.*)', '(.*)', '(.*)'\).*/", $html, $m)) {
        $result_url = $base_url.'/jsp/index_view_direct.jsp?' . $m[4];
        return $result_url;
    }
}

?>