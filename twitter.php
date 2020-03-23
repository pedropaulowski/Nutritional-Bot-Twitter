<?php 
require_once('config.php');
require_once('TwitterAPIExchange.php');
set_time_limit(60);


/*
ler um arquivo de texto e pegar uma palavra
*/
$arquivo = file('comidas.txt');
$qtdFoods = count($arquivo);
$random_number = rand(0, $qtdFoods);
$fd= $arquivo[$random_number];
$food = trim($fd);
/*
for($i=0; $i < strlen($fd); $i++){
    echo ".$fd[$i]";
    if($fd[$i] != '' && $fd[$i] != ' ') {
        $food .= $fd[$i];
    }
}
echo "<br>";
$trimmed = trim($food);
for($i=0; $i < strlen($trimmed); $i++){
    echo ".$trimmed[$i]";

}*/

$settings = array(
    'oauth_access_token' => '',
    'oauth_access_secret_token' => '',
    'consumer_key' => '',
    'consumer_secret' => '',

);
$status = array();
$settings['oauth_access_token']= TWITTER_ACCESS_TOKEN;
$settings['oauth_access_token_secret']= TWITTER_ACCESS_SECRET_TOKEN;
$settings['consumer_key']= TWITTER_API_KEY;
$settings['consumer_secret']= TWITTER_API_SECRET_KEY;

$url = 'https://api.twitter.com/1.1/statuses/update.json';

$requestMethod = 'POST';

$curl = curl_init();

curl_setopt_array($curl, array(
	CURLOPT_URL => "https://edamam-food-and-grocery-database.p.rapidapi.com/parser?ingr=$food",
	CURLOPT_RETURNTRANSFER => true,
	CURLOPT_FOLLOWLOCATION => true,
	CURLOPT_ENCODING => "",
	CURLOPT_MAXREDIRS => 10,
	CURLOPT_TIMEOUT => 30,
	CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
	CURLOPT_CUSTOMREQUEST => "GET",
	CURLOPT_HTTPHEADER => array(
		"x-rapidapi-host: edamam-food-and-grocery-database.p.rapidapi.com",
		"x-rapidapi-key: f2d7f9a704mshb305388dfef512bp17ecb5jsn03d5147e08ab"
	),
));

$response = curl_exec($curl);
$err = curl_error($curl);

curl_close($curl);

if ($err) {
	echo "cURL Error #:" . $err;
} else {
    $aux = 0;
    $foram = array();
    $arr = json_decode($response, true);
    $hints = $arr['hints'];
    $size = count($hints);
    for($i = 0; $i < $size; $i++) {
        $fruta = $arr['hints'][$i]['food']['label'];
        if(in_array($fruta, $foram) == true){
            continue;
        } else {
            $foram[] = $fruta;
        }
        $nutrientes = $arr['hints'][$i]['food']['nutrients'];
        if(isset($arr['hints'][$i]['food']['nutrients']['ENERC_KCAL']))
            $kcal = number_format($arr['hints'][$i]['food']['nutrients']['ENERC_KCAL'], 2);
        else 
            continue;

        if(isset($arr['hints'][$i]['food']['nutrients']['PROCNT']))
            $protein = number_format($arr['hints'][$i]['food']['nutrients']['PROCNT'], 2);
        else 
            continue;

        if(isset($arr['hints'][$i]['food']['nutrients']['FAT']))
            $fat = number_format($arr['hints'][$i]['food']['nutrients']['FAT'], 2);
        else 
            continue;
        if(isset($arr['hints'][$i]['food']['nutrients']['FIBTG']))
            $fiber = number_format($arr['hints'][$i]['food']['nutrients']['FIBTG'], 2);
        else 
            continue;

        if(isset($arr['hints'][$i]['food']['foodContentsLabel'])) {
            $foodContentsLabel = $arr['hints'][$i]['food']['foodContentsLabel'];
            $status[] = "About $fruta, it has ".$kcal." calories, ".$protein."g of proteins, "
            .$fat."g of fat (in general) and , ".$fiber."g of fiber.\r\n.It contains ".$foodContentsLabel."\r\nNotice: it's in 100 grams of $fruta";
            $aux++;
        } else {
            $status[] = "About $fruta, it has ".$kcal." calories, ".$protein."g of proteins, "
            .$fat."g of fat (in general) and , ".$fiber."g of fiber.\r\nNotice: it's in 100 grams of $fruta";
            $aux++;
            
        }
        if(strlen($status[$aux-1]) <=280){
            echo "<br>"."<br>".$status[$aux-1]."<br>"."<br>";
        }
    }   

}
/*
for($i = 0; $i < $aux; $i++) {
    if(isset($status[$i]) && strlen($status[$i]) <=280) {
        $apiData = array(
            'status' => "".$status[$i].""
        );

        $twitter = new TwitterAPIExchange($settings);
        $twitter->buildOauth($url, $requestMethod);

        $twitter->setPostfields($apiData);

        $response = $twitter->performRequest(true, array( CURLOPT_SSL_VERIFYHOST => 0, CURLOPT_SSL_VERIFYPEER => 0 ));

    }
}
*/

