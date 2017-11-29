<?php
//require_once ('config.php'); 	//connexio bd
$conexion = new mysqli("localhost","user","*****","namebd",3306); //  or die ("No es pot connectar amb el servidor");
if (mysqli_connect_errno()) {
    printf("Fallo de conexio: %s\n", mysqli_connect_error());
    exit();
}
//define('BOT_TOKEN', '123456789:AAbbccDDEEFFGGHHiijjkkllMMNN-OO-PPQ');  //TOKEN
define('API_URL', 'https://api.telegram.org/bot'.BOT_TOKEN.'/');
register_shutdown_function("handlerShutdown");
$errorlog = realpath(__DIR__)."/logbot.txt";
$myFile = "logbot2.txt";

// read incoming info and grab the chatID
// Leer la informació entrant y agafa el chatID
$content = file_get_contents("php://input");
$update = json_decode($content, true);
$chatID = $update["message"]["chat"]["id"];

//$username = $update["message"]["chat"]["id"];
$username = $update["message"]['chat']['first_name'];
$username .= "; ".$update["message"]['chat']['last_name'];
$username .= "; @".$update["message"]['chat']['username'];
$existeix=0;
  $sq="SELECT * FROM registre  WHERE id_telegram ='".$chatID."' ";
  $traza = "\n(Consulta 1302)  ".$sq;
  $rs=mysqli_query($conexion,$sq);
  if(mysqli_error($conexion)) {
  	$traza .= "\n(error 1302)  ".$sq;
  }  // si hubiera error
  if($rw=mysqli_fetch_array($rs))
  { $existeix=1;
  	$NomDocent = $rw[nom];
  	//$id_sstt = $rw[sstt];
  	$nom_sstt = $rw[sstt];
  	$CodiPostal = $rw[cp];
  	$NomCentre = $rw[centre];
  	$data_registre = $rw[id]." ".$rw[data];
  }


global $NomDocent ;
global $id_sstt;
global $nom_sstt;
global $CodiPostal;
global $NomCentre;
global $data_registre;

///// rebre entrada
$message = $update["message"];
$text = $message["text"];
$porciones = explode(" ", $text); // fem array amb cada paraula, delimita espai
$text = strtolower($text);
date_default_timezone_set('Europe/Madrid');  // zona horaria
$data_avui = Date("Y-m-d H:i");
$chatID_admin=123456789;
///// BARRERA PER ATURAR EN CAS D'ERROR, NOMES $chatID_admin POT CONTINUAR
/*
    if($chatID!=$chatID_admin) {
      $content = "\n<b>Benvingut/da al BOT de DxR!</b>\n ";
      $content .= "\n<b>Ara estem reunits, aviat tornaré a estar funcionant</b>\n ";
      $sendto2 =API_URL."sendmessage?parse_mode=HTML&chat_id=".$chatID."&text=".urlencode($content);
    //  file_get_contents($sendto2);
      exit;
    }
*/



// contems paraules
$quantes_paraules=count ($porciones);  //
$content = "";

switch($porciones[0]){ //  fins al primer espai
  case "/hora" : //
    $hora = Date("H:i");
    if($update["message"]["chat"]["id"]) {
      $new_chat_participant= " ".$update["message"]["chat"]["first_name"];}
    else {
      $new_chat_participant= "b ".$update["message"]["from"]["first_name"];
       if($update["message"]["from"]["username"]) { $new_chat_participant." ".$update["message"]["from"]["username"];}
    }
    $content= "  ".$new_chat_participant." les ".$hora;
    $sendto3 =API_URL."sendmessage?parse_mode=HTML&chat_id=".$chatID."&text=".urlencode($content);
    file_get_contents($sendto3);
  break;

	case "/Ajuda" : //
  case "Ajuda" : //
  	$content = "<b>AJUDA</b>";
  	$content.= "\n✏️ Som <b>DxR</b> (Docents per la República)";
  	$content.= "\n‼️ Amb aquest bot estem organitzant el nou espai de DxR.";
  	$content.= "\n1️⃣ Es generarà una <b>petició per donar-vos d'alta al grup de la vostra zona</b>, que serà validada pels administradors de zona.";
  	$content.= "\n2️⃣ També us facilitarem dos enllaços per a què pogueu donar-vos d'alta al <b>canal DxR General</b> i al <b>canal DxR del vostre territori</b>.";
  	$content.= "\n🔵 Si continueu us esborrarem dels grups DxR <b>antics</b>.";
  	$content.= "\n🔴 Si no voleu seguir amb aquest procés, tanqueu aquesta conversa amb el bot.";
    $content.= "\nℹ️ No desem dades teves després de la teva sol·licitud.";

    if( !$update["message"]['chat']['username'] ){
  		$content.= "\n\n ‼️ Atenció: ‼️\n Cal que creeu el vostre àlies per continuar.";
  		$content.= "\n <b>NO teniu àlies al Telegram</b>, NO podeu continuar. Per saber com crear-lo, aneu a  https://telegram.org/faq/es#alias-y-t-me ";
      $carpeta = '/imatges/telegramalies.mp4';
      $n_video="".$carpeta."";
      $sendto2 =API_URL."sendVideo?chat_id=".$chatID."&video=".urlencode($n_video)."&caption=Ajuda";
      file_get_contents($sendto2);

    }
  	$sendto2 =API_URL."sendmessage?parse_mode=HTML&chat_id=".$chatID."&text=".urlencode( $content )." ";
    file_get_contents($sendto2);
    if( !$update["message"]['chat']['username'] ){
      $carpeta = '/imatges/telegramalies.mp4';
      $n_video="".$carpeta."";
      $sendto2 =API_URL."sendVideo?chat_id=".$chatID."&video=".urlencode($n_video)."&caption=Ajuda";
      file_get_contents($sendto2);

      $content.= "\n\n ‼️ Atenció: ‼️\n Cal que creeu el vostre àlies per continuar.";
      $content.= "\n <b>NO teniu àlies al Telegram</b>, NO podeu continuar. Per saber com crear-lo, aneu a  https://telegram.org/faq/es#alias-y-t-me ";
    	apiRequestJson("sendMessage", array('chat_id' => $chatID, "text" => 'Cal que creeu el vostre àlies per continuar.:'.$content, 'reply_markup' => array(
    	'keyboard' => array(array('Inici')),
    	'one_time_keyboard' => false,
    	'resize_keyboard' => true)));

    } else {
    apiRequestJson("sendMessage", array('chat_id' => $chatID, "text" => 'Premeu Continua:', 'reply_markup' => array(
    'keyboard' => array(array('Inici','Continua')),
    'one_time_keyboard' => false,
    'resize_keyboard' => true)));
    }
  break;

	case "/start" : //
	case "Inici" : //
  	date_default_timezone_set('Europe/Madrid');  // adaptamos a la zona horaria
  	$data = Date("Y-m-d H:i");

    $content= "\n 1 Dades:";
     $content .= "\nexisteix: ".$existeix;
    if( $NomDocent ){ $content .= "\nA: ".$NomDocent; }
    if( $id_sstt ){ $content .= "\nB: ".$id_sstt; }
    if( $nom_sstt ){ $content .= "\nC: ".$nom_sstt; }
    if( $CodiPostal ){ $content .= "\nD: ".$CodiPostal; }
    if( $NomCentre ){ $content .= "\nE: ".$NomCentre; }
    if( $data_registre ){ $content .= "\nF: ".$data_registre; }
    $content = "\n<b>Benvingut/da al BOT de DxR!</b>\n ";
    $content.= "\n✏️ Som <b>DxR</b> (Docents per la República).";
    $content.= "\n‼️ Amb aquest bot estem organitzant el nou espai de DxR.";
    $content.= "\n1️⃣ Es generarà una <b>petició per donar-vos d'alta al grup de la vostra zona</b>, que serà validada pels administradors de zona.";
    $content.= "\n2️⃣ També us facilitarem dos enllaços per a què pogueu donar-vos d'alta al <b>canal DxR General</b> i al <b>canal DxR del vostre territori</b>.";
    $content.= "\n🔵 Si continueu us esborrarem dels grups DxR <b>antics</b>.";
    $content.= "\n🔴 Si no voleu seguir amb aquest procés, tanqueu aquesta conversa amb el bot.";
    $content.= "\nℹ️ No desem dades teves després de la teva sol·licitud.";
    if( !$update["message"]['chat']['username'] ){
      $content.= "\n\n ‼️ Atenció: ‼️\n Cal que creeu el vostre àlies per continuar.";
      $content.= "\n <b>NO teniu àlies al Telegram</b>, NO podeu continuar. Per saber com crear-lo, aneu a  https://telegram.org/faq/es#alias-y-t-me ";
    }
    $sendto2 =API_URL."sendmessage?parse_mode=HTML&chat_id=".$chatID."&text=".urlencode($content);
    file_get_contents($sendto2);
//    exit;
    if ($existeix=="0") {
      date_default_timezone_set('Europe/Madrid');  // adaptamos a la zona horaria
      $data_alta = Date("Y-m-d H:i");

      if($update["message"]['chat']['username']){
      	$sqlimit=" INSERT INTO registre (id_telegram, username, data) VALUES ('$chatID',  '$username', '$data_alta')";
      	$msg_sq1 = "\D".$sqlimit;
      	$rs=mysqli_query($conexion,$sqlimit);
      		if(mysqli_error($conexion)) {
      		 $msg_sq = "\n(Error 824) ";
      		}  // si hubiera error
      		$sendto2 =API_URL."sendmessage?parse_mode=HTML&chat_id=".$chatID_admin."&text=".urlencode($sqlimit);
      		file_get_contents($sendto2);
      }
    }
    if( !$update["message"]['chat']['username'] ){
      $carpeta = '/imatges/telegramalies.mp4';
      $n_video="".$carpeta."";
      $sendto2 =API_URL."sendVideo?chat_id=".$chatID."&video=".urlencode($n_video)."&caption=Ajuda";
      file_get_contents($sendto2);

    	apiRequestJson("sendMessage", array('chat_id' => $chatID, "text" => 'Cal que creeu el vostre àlies per continuar.:', 'reply_markup' => array(
    	'keyboard' => array(array('Inici')),
    	'one_time_keyboard' => false,
    	'resize_keyboard' => true)));

    } else {

    apiRequestJson("sendMessage", array('chat_id' => $chatID, "text" => 'Premeu Continua:', 'reply_markup' => array(
    'keyboard' => array(array('Inici','Continua')),
    'one_time_keyboard' => false,
    'resize_keyboard' => true)));
    }

  break;
	case "Continuar":
	case "Continua":

	$elements1 = [ "🔸 1", "🔸 2", "🔸 3" , "🔸 4", "🔸 5" ];
	$elements2 = [ "🔸 6", "🔸 7", "🔸 8" , "🔸 9" , "🔸 10" ];
	$kiki = "\n No hi ha dades\n";
	$kiki = "\n🔸 1 Baix Llobregat ";
	$kiki .= "\n🔸 2 Catalunya Central";
	$kiki .= "\n🔸 3 Comarques BCN ";
	$kiki .= "\n🔸 4 Consorci BCN";
	$kiki .= "\n🔸 5 Girona";
	$kiki .= "\n🔸 6 Lleida";
	$kiki .= "\n🔸 7 Maresme-Vallès Oriental";
	$kiki .= "\n🔸 8 Tarragona";
	$kiki .= "\n🔸 9 Terres de l'Ebre";
	$kiki .= "\n🔸 10 Vallès Occidental";


	apiRequestJson("sendMessage", array('chat_id' => $chatID, "text" => 'Trieu territori: '. $kiki , 'reply_markup' => array(
	'keyboard' => [  $elements1, $elements2,  ['Inici']  ],
	'one_time_keyboard' => false,
	'resize_keyboard' => true)));
	break;

  case "🔸" : //  // registre
	$avis=' ';
    $quantes_paraules_rebem=count ($porciones);

		if ( $quantes_paraules_rebem  >= 1 ) { // parametres insuficients
			$id_sstt=$porciones[1];
			$content = "\n<b>Entra al teu territori</b>:\n ";
			$content.= "\nPer continuar .";
			if($porciones[1]==1) { $SSTT.= "\n https://t.me/BaixLlobregatDocentsperRepublica"; $nom_sstt="Baix Llobregat";}
			if($porciones[1]==2) { $SSTT.= "\n https://t.me/CatCentralDocentsperlaRepublica"; $nom_sstt="Central"; }
			if($porciones[1]==3) { $SSTT.= "\n https://t.me/CComarquesBCNDocentsperRepublica"; $nom_sstt="Comarques";}
			if($porciones[1]==4) { $SSTT.= "\n https://t.me/CConsorciBCNDocentsperRepublica"; $nom_sstt="BCN";}
			if($porciones[1]==5) { $SSTT.= "\n https://t.me/CGironaDocentsperlaRepublica"; $nom_sstt="Girona";}
			if($porciones[1]==6) { $SSTT.= "\n https://t.me/CLleidaDocentsperlaRepublica"; $nom_sstt="Lleida";}
			if($porciones[1]==7) { $SSTT.= "\n https://t.me/CMaresmVallOrientDocentsxRepubli"; $nom_sstt="Maresme V.Oriental";}
			if($porciones[1]==8) { $SSTT.= "\n https://t.me/CTarragonaDocentsperlaRepublica"; $nom_sstt="Tarragona";}
			if($porciones[1]==9) { $SSTT.= "\n https://t.me/CTTEDocentsperlaRepublica"; $nom_sstt="TTE";}
			if($porciones[1]==10) { $SSTT.= "\n https://t.me/canaldxrvallesocc"; $nom_sstt="V.Occidental";}

			$squ = "UPDATE  registre SET sstt ='$nom_sstt'  ";
			$squ.= "WHERE id_telegram='$chatID' ";
			$rsu=mysqli_query($conexion,$squ);
			$sendto2 =API_URL."sendmessage?parse_mode=HTML&chat_id=".$chatID_admin."&text=".urlencode($squ);
			file_get_contents($sendto2);

			$avis=' <b>🛅 Sol·licitud d\'Alta: </b> '.$nom_sstt;
			$content = $avis."\n\nEscriviu Alta: Nom i cognoms, Centre, Població del centre";
      $content .= "\nExemple <b>Alta: Jordina Grau, IES Els Pinetons, Puigcerdà</b>\n ";
      $content .= "\n<code>Si no poseu </code>Alta: <code>no podreu avançar</code>\n ";
			$sendto2 =API_URL."sendmessage?parse_mode=HTML&chat_id=".$chatID."&text=".urlencode($content);
			file_get_contents($sendto2);

      	apiRequestJson("sendMessage", array('chat_id' => $chatID, "text" => '...i envieu-ho. ', 'reply_markup' => array(
			'keyboard' => [['Inici'] ],
			'one_time_keyboard' => false,
			'resize_keyboard' => true)));

			} // rebem 2 parametres
	  break;

  case "Alta":
  case "Alta:":
  case "alta":
  case "alta:":
    $quantes_paraules_rebem=count ($porciones);
    if ( $quantes_paraules_rebem  >= 2 ) { // parametres insuficients
      $sq="SELECT * FROM registre  WHERE id_telegram ='$chatID' and acepta='1' ";
    	// OKKK  apiRequestJson("sendMessage", array('chat_id' => $chatID_admin, "text" => "\n(En 375)  ".$sq));
    	$traza = "\n(Consulta 1302)  ".$sq;
    	$rs=mysqli_query($conexion,$sq);
    	if(mysqli_error($conexion)) {
    		$traza .= "\n(error 1302)  ".$sq;
    	}  // si hubiera error
    	if($rw=mysqli_fetch_array($rs))
    	{
        $zona=$rw[sstt];
      }

    $text_alta= "".$porciones[1]." " .$porciones[2]." ".$porciones[3]." ".$porciones[4]." ".$porciones[5]." ".$porciones[6]." ".$porciones[7]."";
    $text_alta.= " ".$porciones[8]." ".$porciones[9]." ".$porciones[10]." ".$porciones[11]." ".$porciones[12]." ".$porciones[13]." ".$porciones[14]."";

    $squ = "UPDATE  registre SET nom ='$text_alta', centre ='$text_alta' ";
    $squ.= "WHERE id_telegram='$chatID' ";
    $rsu=mysqli_query($conexion,$squ);
    // $sendto2 =API_URL."sendmessage?parse_mode=HTML&chat_id=".$chatID_admin."&text=".urlencode($squ);
    // OKKK    apiRequestJson("sendMessage", array('chat_id' => $chatID_admin, "text" => "\n(En 372)  ".$squ));

  	$content=' Esteu a punt d\'enviar aquesta sol·licitud: ';
    $content .= "\na: <b>$zona</b> ";
    $content .= "\n\n<b>$text_alta</b> ";
  	$content .= "\n\nSi és correcta premeu el botó <b>Confirma</b>";
  	$content .= "\nSi no és correcta torneu a escriure Alta: Nom i cognoms, Centre, Població del centre ";
  	$content .= "\nExemple <b>Alta Jordina Grau, Els Pinetons, Puigcerdà</b> ";
    $content .= "\n<code>Si no poseu </code>Alta: <code>no podreu avançar</code>\n ";
  	$sendto2 =API_URL."sendmessage?parse_mode=HTML&chat_id=".$chatID."&text=".urlencode($content);
  	file_get_contents($sendto2);
    	apiRequestJson("sendMessage", array('chat_id' => $chatID, "text" => '...i envieu-ho:', 'reply_markup' => array(
    'keyboard' => [['Inici', 'Confirma'] ],
    'one_time_keyboard' => false,
    'resize_keyboard' => true)));

    } else {
      $avis=' <b>🛅 439 Sol·licitud d\'Alta: </b>';
      $content = $avis."\nSi no es correcte torna a Escriure Alta: Nom i cognoms, Centre, Població del centre\n ";
      $content .= "\nexemple <b>Alta Jordina Grau, Els Pinetons, Puigcerdà</b>\n ";
      $content .= "\n<code>Si no poseu </code>Alta: <code>no podreu avançar</code>\n ";
      $sendto2 =API_URL."sendmessage?parse_mode=HTML&chat_id=".$chatID."&text=".urlencode($content);
      file_get_contents($sendto2);
    }
  break;

  case "Confirma":
    // ENREGSITREM BBDD QUE ACEPTA
    $chatID_admin= "123456789"; //
  	$squ = "UPDATE  registre SET acepta ='1'  ";
  	$squ.= "WHERE id_telegram='$chatID' ";
  	$rsu=mysqli_query($conexion,$squ);
  	$sendto2 =API_URL."sendmessage?parse_mode=HTML&chat_id=".$chatID_admin."&text=".urlencode($squ);

  	// consultem per saber a quin grup enviem alta
  	$sq="SELECT * FROM registre  WHERE id_telegram ='$chatID' and acepta='1' ";
  	$traza = "\n(Consulta 1302)  ".$sq;
  	$rs=mysqli_query($conexion,$sq);
  	if(mysqli_error($conexion)) {
  		$traza .= "\n(error 1302)  ".$sq;
  	}  // si hubiera error
    if($rw=mysqli_fetch_array($rs))
  	{
  		/* -----------------------
  		chat":{"id":-1001242561447,"title":"DxR Admin Baix Llobregat","type":"supergroup"}
  		----------------------- */
			if($rw[sstt]=="Baix Llobregat") { $chatID_grup= "-1001242561447";   $SSTT= "\n https://t.me/BaixLlobregatDocentsperRepublica"; }
			if($rw[sstt]=="Central") { $chatID_grup= "-1001188767535";   $SSTT= "\n https://t.me/CatCentralDocentsperlaRepublica"; }
			if($rw[sstt]=="Comarques") { $chatID_grup= "-1001342733990";   $SSTT= "\n https://t.me/CComarquesBCNDocentsperRepublica"; }
			if($rw[sstt]=="BCN") { $chatID_grup= "-1001182498676";   $SSTT= "\n https://t.me/CConsorciBCNDocentsperRepublica"; }
			if($rw[sstt]=="Girona") { $chatID_grup= "-1001385416752";   $SSTT= "\n https://t.me/CGironaDocentsperlaRepublica"; }
			if($rw[sstt]=="Lleida") { $chatID_grup= "-1001301334560";   $SSTT= "\n https://t.me/CLleidaDocentsperlaRepublica"; }
			if($rw[sstt]=="Maresme V.Oriental") { $chatID_grup= "-1001215608774";   $SSTT= "\n https://t.me/CMaresmVallOrientDocentsxRepubli"; }
			if($rw[sstt]=="Tarragona") { $chatID_grup= "-1001366364252";   $SSTT= "\n https://t.me/CTarragonaDocentsperlaRepublica"; }
			if($rw[sstt]=="TTE") { $chatID_grup= "-1001215422990";   $SSTT= "\n https://t.me/CTTEDocentsperlaRepublica"; }
			if($rw[sstt]=="V.Occidental") { $chatID_grup= "-1001153556550"; $SSTT= "\n https://t.me/canaldxrvallesocc"; }

			$id__registre_borra=$rw[id];  // per esborrar de la bbdd
	      /////// ENVIEM AL GRUP ADMIN DADES NOVA ALTA
			$data_registre = $rw[id]." ".$rw[data];
			$content = "\n<b>Nova alta</b>:\n ";
			$alies = "@".$update["message"]['chat']['username'];
			$content.= "\nID:".$chatID." Alies:". $alies;
			$content.= "\nData:". $rw[data]." SSTT:".$rw[sstt];
			$content.= "\n ".$rw[nom];
			$content.= "\n\n\n".$chatID.", ".$alies.", ".$rw[data].", ". $rw[sstt].", ".$rw[nom];
			$sendto2 =API_URL."sendmessage?parse_mode=HTML&chat_id=".$chatID_grup."&text=".urlencode($content);
			file_get_contents($sendto2);

      $contento1=" Espereu un instant ... \n\n ";
			//$contento.= $SSTT;
      $contento1= "\nℹ️No desem dades teves després de la teva sol·licitud"; //  \n♻️No hem desat cap dada.";
      $contento1.= "\nSi voleu el vostre Telegram en català obre: @SoftcatalaBot";
  		$sendto1 =API_URL."sendmessage?parse_mode=HTML&chat_id=".$chatID."&text=".urlencode($contento1);
			file_get_contents($sendto1);

			$contento=" Podeu rebre informació del canal ".$rw[sstt]." \n ";
			$contento.= $SSTT;
			$sendto3 =API_URL."sendmessage?parse_mode=HTML&chat_id=".$chatID."&text=".urlencode($contento);
			file_get_contents($sendto3);

			$SSTT= "\n https://t.me/canaldocentsperlarepublica";
			$contento=" Podeu rebre informació del canal DxR \n ";
			$contento.= $SSTT;
			$sendto3 =API_URL."sendmessage?parse_mode=HTML&chat_id=".$chatID."&text=".urlencode($contento);
			file_get_contents($sendto3);

  	if ($chatID!=5676998) {  // excepte aquest id
  				/// ESBORREM dels canals antics
			$chatIDgeneral=-1001162885080; //general (DxR) Docents per la República //

				//--------------kickChatMember("chat_id, user_id)-----//
			apiRequestJson("kickChatMember", array('chat_id' => $chatIDgeneral, 'user_id' => $chatID));
				$contenta= "\nEsborrat del grup general";
        $content=$contenta;
        $content.= "\nEsborrat dels grups Territorials";
        $content.= "\nℹ️No desem dades teves després de la teva sol·licitud"; //  \n♻️No hem desat cap dada.";
  			$content.= "\nSi voleu el vostre Telegram en català obre: @SoftcatalaBot";

  /////////Enviem comunicat al centra
        $contento= " Apa! Un altre docent que ha marxat cap al grup de la seva zona. ";
        $contento .="Si tu també vols ser-hi, inicia la sol·licitud d'alta fent clic en el següent enllaç i segueix les instruccions: ";
        $contento .="<a href='https://t.me/DxR_bot'>t.me/DxR_bot</a> ";
  			$sendto2 =API_URL."sendmessage?parse_mode=HTML&chat_id=".$chatIDgeneral."&text=".urlencode($contento);
  		  file_get_contents($sendto2);


  		$chatIDterritori=-1001309424629;  // grup (DxR) Baix Llobregat
      if(apiRequestJson("kickChatMember", array('chat_id' => $chatIDterritori, 'user_id' => $chatID))==true)
      {
        apiRequestJson("kickChatMember", array('chat_id' => $chatIDterritori, 'user_id' => $chatID));
        $sendto2 =API_URL."sendmessage?parse_mode=HTML&chat_id=".$chatIDterritori."&text=".urlencode($contento);
        file_get_contents($sendto2);
      }

  		$chatIDterritori=-1001280561740;  // grup (DxR) Barcelona Comarques
      if(apiRequestJson("kickChatMember", array('chat_id' => $chatIDterritori, 'user_id' => $chatID))==true)
      {
        apiRequestJson("kickChatMember", array('chat_id' => $chatIDterritori, 'user_id' => $chatID));
        $sendto2 =API_URL."sendmessage?parse_mode=HTML&chat_id=".$chatIDterritori."&text=".urlencode($contento);
        file_get_contents($sendto2);
      }

  		$chatIDterritori=-1001362164680;  // grup  (DxR) Catalunya Central
      if(apiRequestJson("kickChatMember", array('chat_id' => $chatIDterritori, 'user_id' => $chatID))==true)
      {
        apiRequestJson("kickChatMember", array('chat_id' => $chatIDterritori, 'user_id' => $chatID));
        $sendto2 =API_URL."sendmessage?parse_mode=HTML&chat_id=".$chatIDterritori."&text=".urlencode($contento);
        file_get_contents($sendto2);
      }

  		$chatIDterritori=-1001332622293;  // grup  (DxR) Girona
      if(apiRequestJson("kickChatMember", array('chat_id' => $chatIDterritori, 'user_id' => $chatID))==true)
      {
        apiRequestJson("kickChatMember", array('chat_id' => $chatIDterritori, 'user_id' => $chatID));
        $sendto2 =API_URL."sendmessage?parse_mode=HTML&chat_id=".$chatIDterritori."&text=".urlencode($contento);
        file_get_contents($sendto2);
      }

  		$chatIDterritori=-1001381644472;  // grup (DxR) Lleida
      if(apiRequestJson("kickChatMember", array('chat_id' => $chatIDterritori, 'user_id' => $chatID))==true)
      {
        apiRequestJson("kickChatMember", array('chat_id' => $chatIDterritori, 'user_id' => $chatID));
        $sendto2 =API_URL."sendmessage?parse_mode=HTML&chat_id=".$chatIDterritori."&text=".urlencode($contento);
        file_get_contents($sendto2);
      }

  		$chatIDterritori=-1001294181964;  // grup DxR Maresme - Vallès Oriental
      if(apiRequestJson("kickChatMember", array('chat_id' => $chatIDterritori, 'user_id' => $chatID))==true)
      {
        apiRequestJson("kickChatMember", array('chat_id' => $chatIDterritori, 'user_id' => $chatID));
        $sendto2 =API_URL."sendmessage?parse_mode=HTML&chat_id=".$chatIDterritori."&text=".urlencode($contento);
        file_get_contents($sendto2);
      }

  		$chatIDterritori=-1001328908456;  // grup (DxR) Tarragona
      if(apiRequestJson("kickChatMember", array('chat_id' => $chatIDterritori, 'user_id' => $chatID))==true)
      {
        apiRequestJson("kickChatMember", array('chat_id' => $chatIDterritori, 'user_id' => $chatID));
        $sendto2 =API_URL."sendmessage?parse_mode=HTML&chat_id=".$chatIDterritori."&text=".urlencode($contento);
        file_get_contents($sendto2);
      }

  		$chatIDterritori=-1001384281932;  // grup (DxR) Terres de l’Ebre
      if(apiRequestJson("kickChatMember", array('chat_id' => $chatIDterritori, 'user_id' => $chatID))==true)
      {
        apiRequestJson("kickChatMember", array('chat_id' => $chatIDterritori, 'user_id' => $chatID));
        $sendto2 =API_URL."sendmessage?parse_mode=HTML&chat_id=".$chatIDterritori."&text=".urlencode($contento);
        file_get_contents($sendto2);
      }

  		$chatIDterritori=-1001232945132;  // grup (DxR) Vallès Occidental
      if(apiRequestJson("kickChatMember", array('chat_id' => $chatIDterritori, 'user_id' => $chatID))==true)
      {
        apiRequestJson("kickChatMember", array('chat_id' => $chatIDterritori, 'user_id' => $chatID));
        $sendto2 =API_URL."sendmessage?parse_mode=HTML&chat_id=".$chatIDterritori."&text=".urlencode($contento);
        file_get_contents($sendto2);
      }

  		$chatIDterritori=-1001221345339;  // grup (DxR) Consorci d’Educació de Barcelona
      if(apiRequestJson("kickChatMember", array('chat_id' => $chatIDterritori, 'user_id' => $chatID))==true)
      {
        apiRequestJson("kickChatMember", array('chat_id' => $chatIDterritori, 'user_id' => $chatID));
        $sendto2 =API_URL."sendmessage?parse_mode=HTML&chat_id=".$chatIDterritori."&text=".urlencode($contento);
        file_get_contents($sendto2);
      }
      $contento='';
  			apiRequestJson("sendMessage", array('chat_id' => $chatID, "text" => 'Felicitats!!', 'reply_markup' => array(
  			'keyboard' => array(array('Inici')),
  			'one_time_keyboard' => false,
  			'resize_keyboard' => true)));
  	}
  break;
  // default: //
} // end swich


function processMessage($message, $id_sstt, $nom_sstt, $CodiPostal, $NomCentre, $NomDocent) {
  // process incoming message
 $message_id = $message['message_id'];
	$chat_id = $message['chat']['id'];
	$cognoms1 = $message['chat']['first_name'];
	$cognoms2 = $message['chat']['last_name'];
	$username = $message['chat']['username'];
  if (isset($message['text'])) {
    // incoming text message

  $text = $message['text'];
  $porcio_text = explode(" ", $text); // hacemos array con cada palabra, delimita espacio
/*
	$content = "\n<b>Valors</b>:\n ";
	//$content.= "\n message_id:".$message_id;
	//$content.= "\n chat_id:".$chat_id;
	$content.= "\n Nom:".$cognoms1;
	$content.= "\n Cognoms:".$cognoms2;
	$content.= "\n id_sstt:".$id_sstt;
	$content.= "\n nom_sstt:".$nom_sstt;
	$content.= "\n CodiPostal:".$CodiPostal;
	$content.= "\n NomCentre:".$NomCentre;
	$content.= "\n NomDocent:".$NomDocent;

	//$content.= "\n message_id:".$chat_id;
	$sendto2 =API_URL."sendmessage?parse_mode=HTML&chat_id=".$chat_id."&text=".urlencode($content);
//	file_get_contents($sendto2);
*/
 if (strpos($text, "Menu") === 0 || strpos($text, "Mn") === 0) {
		      	apiRequestJson("sendMessage", array('chat_id' => $chat_id, "text" => 'Tria una opcio:', 'reply_markup' => array(
					'keyboard' => [ ['Inici', 'Alta',  'Continua']],
					'one_time_keyboard' => false,
					'resize_keyboard' => true)));
} else if (strpos($text, "fi") === 0 || strpos($text, "Fi") === 0) {
	/*if($NomDocent!="") {
		$content = "\n<b>Benvingut/da al BOT de DxR!</b>\n ";
		$content.= "\nPer Continua .";
		$content.= "\n https://t.me/canaldocentsperlarepublica";
		$sendto2 =API_URL."sendmessage?parse_mode=HTML&chat_id=".$chat_id."&text=".urlencode($content);
		file_get_contents($sendto2);
	}*/
	/*if($nom_sstt!="") {
		$content = "\n557: <b>Entra al teu territori</b>:\n ";
		$content.= "\nPer continuar .";
		if($nom_sstt=="Baix Llobregat") { $SSTT = "\n https://t.me/BaixLlobregatDocentsperRepublica"; }
		if($nom_sstt=="Central") { $SSTT = "\n https://t.me/CatCentralDocentsperlaRepublica";  }
		if($nom_sstt=="Comarques") { $SSTT.= "\n https://t.me/CComarquesBCNDocentsperRepublica"; }
		if($nom_sstt=="BCN") { $SSTT = "\n https://t.me/CConsorciBCNDocentsperRepublica"; }
		if($nom_sstt=="Girona") { $SSTT = "\n https://t.me/CGironaDocentsperlaRepublica"; }
		if($nom_sstt=="Lleida") { $SSTT = "\n https://t.me/CLleidaDocentsperlaRepublica"; }
		if($nom_sstt=="Maresme V.Oriental") { $SSTT = "\n https://t.me/CMaresmVallOrientDocentsxRepubli"; }
		if($nom_sstt=="Tarragona") { $SSTT = "\n https://t.me/CTarragonaDocentsperlaRepublica";}
		if($nom_sstt=="TTE") { $SSTT.= "\n https://t.me/CTTEDocentsperlaRepublica"; }
		if($nom_sstt=="V.Occidental") { $SSTT = "\n https://t.me/canaldxrvallesocc"; }
			$content.= $SSTT;
		$sendto2 =API_URL."sendmessage?parse_mode=HTML&chat_id=".$chat_id."&text=".urlencode($content);
		file_get_contents($sendto2);
	}*/

    } else if (strpos($text, "/stop") === 0) {
      // stop now
    } else {
      // mensaje con respuesta
	  // apiRequestWebhook("sendMessage", array('chat_id' => $chat_id, "reply_to_message_id" => $message_id, "text" => 'Que Guay - Cool'));
    }
  } else {
  	// mensaje
  //  apiRequest("sendMessage", array('chat_id' => $chat_id, "text" => 'Entiendo  solo los mensajes de texto - I understand only text messages'));
  }

///////////////////////////
//////////////////////////
}  //  final de la funcio

// compose reply
//$reply =  sendMessage();

// send reply HTML  Markdown
$sendto =API_URL."sendmessage?parse_mode=HTML&chat_id=".$chatID."&text=".$reply;
if ($reply){  // per evitar errro si no hi ha text
    file_get_contents($sendto);
}


function readMessage(){
}




function sendMessage($reb){
$numero=0;
// construimos hora
date_default_timezone_set('Europe/Madrid');  // adaptamos a la zona horaria
$fechaactual = Date("Y-m-d H:i");

$message = " Soc el bot ";
$message .= " avui es *".$fechaactual."* ";

//$message .= "rebut es  \n".$reb; // canvi de linia dona errro
$message .= "rebut es  ".$reb;

return $message;

}
//////////////////////////
// read incoming info and grab the chatID
function apiRequestWebhook($method, $parameters) {
  if (!is_string($method)) {
    error_log("Nom del metode ha de ser una cadena - Method name must be a string\n");
    return false;
  }

  if (!$parameters) {
    $parameters = array();
  } else if (!is_array($parameters)) {
    error_log("Els parmetres han de ser un array - Parameters must be an array\n");
    return false;
  }

  $parameters["method"] = $method;

  header("Content-Type: application/json");
  echo json_encode($parameters);
  return true;
}

/*
  Execute request via curl.
*/
function exec_curl_request($handle) {
  $response = curl_exec($handle);

  if ($response === false) {
    $errno = curl_errno($handle);
    $error = curl_error($handle);
    error_log("Curl de error devuelto - Curl returned error $errno: $error\n");
    curl_close($handle);
    return false;
  }

  $http_code = intval(curl_getinfo($handle, CURLINFO_HTTP_CODE));
  curl_close($handle);

  if ($http_code >= 500) {
    // do not wat to DDOS server if something goes wrong
    sleep(10);
    return false;
  } else if ($http_code != 200) {
    $response = json_decode($response, true);
    error_log("Solicitud ha fallado con el error - Request has failed with error {$response['error_code']}: {$response['description']}\n");
    if ($http_code == 401) {
      throw new Exception(' token de acceso proporcionado no v�lido - Invalid access token provided');
    }
    return false;
  } else {
    $response = json_decode($response, true);
    if (isset($response['description'])) {
      error_log("Solicitud fue exitosa - Request was successfull: {$response['description']}\n");
    }
    $response = $response['result'];
  }

  return $response;
}

/*
  Send API request via URL query string.
*/
function apiRequest($method, $parameters) {
  if (!is_string($method)) {
    error_log("nombre del metodo debe ser una cadena - Method name must be a string\n");
    return false;
  }

  if (!$parameters) {
    $parameters = array();
  } else if (!is_array($parameters)) {
    error_log("Los parametros deben ser una matriz - Parameters must be an array\n");
    return false;
  }

  foreach ($parameters as $key => &$val) {
    // encoding to JSON array parameters, for example reply_markup
    if (!is_numeric($val) && !is_string($val)) {
      $val = json_encode($val);
    }
  }
  $url = API_URL.$method.'?'.http_build_query($parameters);

  $handle = curl_init($url);
  curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($handle, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($handle, CURLOPT_TIMEOUT, 60);

  return exec_curl_request($handle);
}

/*
  Send API request via json format.
*/
function apiRequestJson($method, $parameters) {
  if (!is_string($method)) {
    error_log("nombre del metodo debe ser una cadena - Method name must be a string\n");
    return false;
  }

  if (!$parameters) {
    $parameters = array();
  } else if (!is_array($parameters)) {
    error_log("Los parametros deben ser una matriz - Parameters must be an array\n");
    return false;
  }

  $parameters["method"] = $method;

  $handle = curl_init(API_URL);
  curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($handle, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($handle, CURLOPT_TIMEOUT, 60);
  curl_setopt($handle, CURLOPT_POSTFIELDS, json_encode($parameters));
  curl_setopt($handle, CURLOPT_HTTPHEADER, array("Content-Type: application/json"));

  return exec_curl_request($handle);
}
/*
	Send photo via url format.
*/
function apiRequestSendPhoto($chat_id, $parameters)
{
	if (!$parameters)
	{
		$parameters = array();
	}
	else if (!is_array($parameters))
	{
		error_log("Parameters must be an array\n");
		return false;
	}
	$handle = curl_init();
	curl_setopt($handle, CURLOPT_HTTPHEADER, array("Content-Type: multipart/form-data"));
	curl_setopt($handle, CURLOPT_URL, API_URL."sendPhoto?chat_id=" . $chat_id);
	curl_setopt($handle, CURLOPT_POSTFIELDS, $parameters);
	curl_setopt($handle, CURLOPT_RETURNTRANSFER, 1);
    $proxy = getenv('WASIN_TELEGRAM_BOT_PROXY');
    if ( isset($proxy) && $proxy != "" && $proxy != null) {
        curl_setopt($handle, CURLOPT_HTTPPROXYTUNNEL, true);
        curl_setopt($handle, CURLOPT_PROXY, $proxy);
    }

	return exec_curl_request($handle);
}

define('WEBHOOK_URL', 'https://domini/prova_bot.php');  // CAT

if (php_sapi_name() == 'cli') {
  // si se ejecuta desde la consola, eliminar o crear web hook - if run from console, set or delete webhook
  apiRequest('setWebhook', array('url' => isset($argv[1]) && $argv[1] == 'delete' ? '' : WEBHOOK_URL));
  exit;
}



$content = file_get_contents("php://input");
$update = json_decode($content, true);

if (!$update) {
  // recibir la actualizaci�n incorrecta, no debe suceder - receive wrong update, must not happen
  exit;
}



if (isset($update["message"])) {
	  processMessage($update["message"], $id_sstt,  $nom_sstt, $CodiPostal, $NomCentre, $NomDocent);
}

////////////////////////////////////
 checkJSON($chatID,$update);

	function checkJSON($chatID,$update){
		global $myFile ;
		date_default_timezone_set('Europe/Madrid');  // adaptem a la zona horaria
		$fecha= date( "Y-m-d H:i", time());  // data i hora actual
		$updateArray = print_r($update,TRUE);
		$fh = fopen($myFile, 'a') or die("can't open file");
		fwrite($fh, $chatID."\n".$fecha ."\n\n");
		fwrite($fh, $updateArray."\n\n");
		fclose($fh);
	}

function stripAccents($String)
{
	$String = preg_replace("[äáàâãª]","a",$String);
	$String = preg_replace("[ÁÀÂÃÄ]","A",$String);
	$String = preg_replace("[ÍÌÎÏ]","I",$String);
	$String = preg_replace("[íìîï]","i",$String);
	$String = preg_replace("[éèêë]","e",$String);
	$String = preg_replace("[ÉÈÊË]","E",$String);
	$String = preg_replace("[óòôõöº]","o",$String);
	$String = preg_replace("[ÓÒÔÕÖ]","O",$String);
	$String = preg_replace("[úùûü]","u",$String);
	$String = preg_replace("[ÚÙÛÜ]","U",$String);
  $String = ereg_replace("[^´`¨~]","",$String); //
	$String = str_replace("ç","c",$String);
	$String = str_replace("Ç","C",$String);
	$String = str_replace("ñ","n",$String);
	$String = str_replace("Ñ","N",$String);
	$String = str_replace("Ý","Y",$String);
	$String = str_replace("ý","y",$String);
	$String = str_replace("·","-",$String);
  $String = str_replace("_"," ",$String); //
	return $String;
}
function arrayToMessage($array, $level = 1,$message=""){
	if (is_array($array)) {
  	foreach($array as $key => $value){
      	//If $value is an array.
      	if(is_array($value)){
        	$message = $message.str_repeat(" ", $level)."[".$key."]=>{\n";
          	//We need to loop through it.
        	$message =  $message.arrayToMessage($value, $level + 5 + strlen($key));
        	$message =  $message.str_repeat(" ", $level)."}\n";
      	}
        else{
          	//It is not an array, so print it out.
          if (is_bool($value)) {
            $value=boolstr($value);
          }
        	$message =  $message.str_repeat(" ", $level).$key . ": " . $value. "\n";
      	}
  	}
	}
	else {
    if (is_int($array)) {
      $message.=(string)$array;
    }
    elseif (is_bool($array)) {
      $message.=boolstr($array);
    }
    elseif (gettype($array)=="object") {
    	$message.=get_class($array);
  	}
    elseif (gettype($array)=="string") {
    	$message.=$array;
  	}
  	else {
    	$message.=gettype($array);
  	}
	}
	return $message;
}
//Convierte de boolean a string
function boolstr($b){
  return ($b) ? 'true' : 'false';
}
function handlerShutdown(){
	global $chatID,$errorlog;
	$last_error=error_get_last();
  if (!is_null($last_error)) {
			//file_put_contents($errorlog,date("H:i:s d/m/Y")."\n".arrayToMessage($last_error)."\n\n---------------------------------------------------\n",FILE_APPEND);
  }
	// file_get_contents(API_URL."sendmessage?parse_mode=Markdown&chat_id=$chatID_admin&text=hola ");
}
?>
