<?php
require 'vendor/autoload.php';
require_once 'dbConfig.php';
use Telegram\Bot\Api;
use Illuminate\Database\Capsule\Manager as Capsule;

$client = new Api('6163316944:AAG-jq_W8sNERqWWCXpWigpMfldEVaR1Wwg');
$last_update_id=0;
$partenza = false;
$IDVolo = false;

while(true){
	$response = $client->getUpdates(['offset'=>$last_update_id, 'timeout'=>5]);
    if (count($response)<=0) continue;

	foreach ($response as $r){
        $last_update_id=$r->getUpdateId()+1;
		$message=$r->getMessage();
		$chatId=$message->getChat()->getId();
		$text=$message->getText();

        $textAnswer="";
        switch(strtoupper($text)){
            case"/START": $textAnswer="Ciao sono il bot di Giorgio Airlines 👩🏼‍✈️🛬\nCome posso aiutarti?\nse fai /help, potrai capire cosa posso fare";
                break;
            case"/HELP": $textAnswer="Puoi cercare voli usando /voli\nPuoi controllare i tuoi voli prenotati con /myvoli";
            break;

            case "/VOLI":$textAnswer="inserisci una partenza";
                $partenza = true;
            break;

            case "/MYVOLI":$textAnswer="Ecco i tuoi voli!!!🛫🛫\n\n".myVoli($chatId);
            break;

            case "/PRENOTA":$textAnswer="inserisci ID volo";
                $IDVolo = true;
            break;

            default: 
                if($IDVolo == true){

                    if(prenota($chatId,$text,1)){
                        $textAnswer = "volo prenotato";
                    }
                    else{
                        $textAnswer = "volo non trovato";
                    }
                    $controlloID = false;
                }
                else if($partenza == true){
                    $textAnswer = "Ecco a te la lista dei voli 🛫🛫\n\n".Voli($text,1);
                    $partenza = false;
                }
                else if(str_contains($text,"/voli")){
                    $textAnswer = "Ecco a te la lista dei voli 🛫🛫\n\n".Voli($text,0);
                }
                else if(str_contains($text,"/prenota")){
                
                    if(prenota($chatId,$text,0)){
                        $textAnswer = "volo prenotato";
                    }
                    else{
                        $textAnswer = "volo non trovato";
                    }
                
                }
                else{
                    $textAnswer = "non capisco...";
                };
        }

        $voli = jsonAPI();

        if($text==""){
            $text = "Ci deve essere stato un malinteso";
        }
        try{
            $response = $client->sendMessage([
                'chat_id' => $chatId,
              'text' => $textAnswer
          ]);
        }
        catch(Exception $e){
            $response = $client->sendMessage([
                'chat_id' => $chatId,
                'text' => "c' è stato un malinteso😥\nRiprova"
          ]);
        }
        
	}
}


function  jsonAPI(){
    $getFile = file_get_contents('voli.json');
    $json=json_decode($getFile,TRUE);
    return $json;
}

function myVoli($chatId){
    $results = Capsule::table('voloprenotato')->where('IDPersona','=',strval($chatId)) -> get();
    $text="";

    foreach($results as $risultati){
        $text.="ID volo: ".$risultati->IDVolo."\nData: ".$risultati->Data."\nPartenza: ".$risultati->partenza."\nTerminal: ".$risultati->Terminal."\nArrivo: ".$risultati->arrivo."\n\n";
    }
    
    return $text;
}


function Voli($textChat,$controllo){
    $voli = jsonAPI();
    $index = 0;
    $voli = $voli["data"];
    $text="";
    if($controllo == 0){
        $places = getPlace($textChat);
        try{
            foreach($voli as $volo){
                if(count($places)>1){
                    
                    //c' è destinazione
        
                    if(str_contains($volo["departure"]["airport"],$places[0]) || str_contains($volo["departure"]["timezone"],$places[0]) && str_contains($volo["arrival"]["airport"],$places[1]) || str_contains($volo["arrival"]["timezone"],$places[1]) && $index<11){ 
                        $text.=$index."."." ID volo: ".$volo["flight"]["number"]."\nData: ".$volo["flight_date"]."\nPartenza: ".$volo["departure"]["airport"]." - Città: ". $volo["departure"]["timezone"]."\nTerminal: ".$volo["departure"]["terminal"]."\nArrivo: ".$volo["arrival"]["airport"]." - Città: ".$volo["arrival"]["timezone"]."\n\n";
                        $index++;
                    }
                }
                else{
                    if(str_contains($volo["departure"]["airport"],$places[0]) || str_contains($volo["departure"]["timezone"],$places[0])&& $index<11){
                        $text.=$index."."." ID volo: ".$volo["flight"]["number"]."\nData: ".$volo["flight_date"]."\nPartenza: ".$volo["departure"]["airport"]." - Città: ". $volo["departure"]["timezone"]."\nTerminal: ".$volo["departure"]["terminal"]."\nArrivo: ".$volo["arrival"]["airport"]." - Città: ".$volo["arrival"]["timezone"]."\n\n";
                        $index++;
                    }
                }
                
                
            }
        }
        catch(Exception $e){
    
        }
    }
    else{
        try{
            foreach($voli as $volo){
                
                if(str_contains(strtoupper($volo["departure"]["airport"]),strtoupper($textChat)) || str_contains(strtoupper($volo["departure"]["timezone"]),strtoupper($textChat))&& $index<11){
                    $text.=$index."."." ID volo: ".$volo["flight"]["number"]."\nData: ".$volo["flight_date"]."\nPartenza: ".$volo["departure"]["airport"]." - Città: ". $volo["departure"]["timezone"]."\nTerminal: ".$volo["departure"]["terminal"]."\nArrivo: ".$volo["arrival"]["airport"]." - Città: ".$volo["arrival"]["timezone"]."\n\n";
                    $index++;
                }
            }
        }
        catch(Exception $e){
    
        }
    }
    
    if($text == ""){
        $text = "nessun volo trovato";
    }
    return $text;
}

function getPlace($textChat){
    $delimiter = ' ';
    $words = explode($delimiter, $textChat);
    $controlloPlace = 0;
    $places = array();
    $text="";
    try{
        foreach ($words as $word) {
        
            if(strtoupper($word)=="A" || str_contains(strtoupper($word),"ARRIV")){
                $controlloPlace =1000;
            }
    
    
            if($controlloPlace == 1000){
                array_push($places,trim($text));
                $text = "";
                $controlloPlace = 1;
            }
            else if($controlloPlace !=0){
                $text.=$word." ";
                $controlloPlace++;
            }
    
            if(str_contains(strtoupper($word),"DA") || str_contains(strtoupper($word),"PART")){
                $controlloPlace = 1;
            }
        }
        array_push($places,trim($text));
    }
    catch(Exception $e){

    }
    
    
    return $places;
}

function prenota($chatId,$text,$controlloID){
    $controlloTrovato = false;
    if($controlloID == 0){
        $delimiter = ' ';
        $words = explode($delimiter, $text);
        
        foreach($words as $word){
            
            if(is_int(intval($word))){
                $voli = jsonAPI();
                $voli = $voli["data"];
                
                foreach($voli as $volo){
                    if($volo["flight"]["number"] == $word){
                        $controlloTrovato = true;
                        prenotaVolo($volo["flight"]["number"],$chatId,$volo["departure"]["airport"],$volo["arrival"]["airport"],$volo["departure"]["terminal"],$volo["flight_date"]);
                        break;
                    }
                }

            }
        }
    }
    else{
        
        $voli = jsonAPI();
        $voli = $voli["data"];
            
        foreach($voli as $volo){
            if($volo["flight"]["number"] == $text){
                $controlloTrovato = true;
                prenotaVolo($volo["flight"]["number"],$chatId,$volo["departure"]["airport"],$volo["arrival"]["airport"],$volo["departure"]["terminal"],$volo["flight_date"]);
                break;
            }
        }

            
        
    }
    
    return $controlloTrovato;
}

function prenotaVolo($IDVolo,$idPersona,$partenza,$arrivo,$terminal,$data){
    if($terminal==""){
        $terminal="null";
    }
    try{
        Capsule::table('voloprenotato')->insert(
            array(
                    'IDVolo' =>intval($IDVolo),
                   'IDPersona'     =>   intval($idPersona), 
                   'partenza'   =>   $partenza,
                   'arrivo'     =>   $arrivo, 
                   'Terminal'   =>   $terminal,
                   'Data' => $data
            )
       );
    }
    catch(Exception $e){
        
    }
    
}

?>