<?php
#Lo que tiene que hacer es modificar el array y que esto tenga los valores de las coordenadas 

#Lo que podemos hacer es que estos se reparta en funciones como la funcion que va a hacer el curl

function Getnodes($container_id){
    $cmd = "curl -X POST --data". '{"jsonrpc":"2.0","method":"perm_getNodesAllowlist","params":[], "id":1}'. "http://127.0.0.1:8545" . ">". "response.json";
    $salida = exec($cmd , $output_array , $cmd_status);
    GetIpOfNodes();
    $cmdos = "docker cp ./coordenadasgeo.csv". $container_id .":/home/datacoordenada";
    $salidados = exec($cmd , $output_array , $cmd_status);    
}

function GetIpOfNodes(){
    //Here we do the curl locally 
    //Take that response to a file , We do that with the curl > response.json
    $jsondata = file_get_contents("response.json");
    $json = json_decode($jsondata, true);
    $output = "<ul>";
    $array = array();
    $iterador = 0;
    foreach($json['result'] as $result){
            //print $result;
            $array[$iterador] = $result;
            $iterador = $iterador + 1;
           // print_r($array);
    }
    $iterados = 0;
    //print substr($array[1],137,13); // Asi podemos sacar al ip del string del array necesario//
    while ($iterados < count($array)-1):
        $cleanadd = substr($array[$iterados],137,13);
        //print($cleanadd);
        $respuesta = geolocation(strval($cleanadd));
        $iterados = $iterados + 1;
        $coordenadas = coordenadas($respuesta);
        
    endwhile;
}



function AnadirGeo($coordenadax, $coordenaday){

    $handle = fopen("./coordenadasgeo.csv", "a");
    $line = [
        "Coordenadas x" => $coordenadax,
        "Coordenadas y" => $coordenaday,
    ];

    fputcsv($handle , $line);

    fclose($handle);
}

function Getaddressalt(){
 
    // create curl resource
    $ch = curl_init();

    // set url
    curl_setopt($ch, CURLOPT_URL, "https://zqdatarecovery.com/ip.php");

    //return the transfer as a string
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

    // $output contains the output string
    $output = curl_exec($ch);
    print $ch;
    // close curl resource to free up system resources
    $variablealter = geolocation($output);
    curl_close($ch); 

    return $variablealter;


    
}
function getUrl($url){
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
    $response = curl_exec($ch);
    curl_close($ch);
    
    return $response;
    
}   
//AnadirGeo($xvalue,$yvalue);
//GetIpOfNodes();

//GetCoordinates();

//****************************************************************************************** */
///Esto sera usao la momento de invocar el contrato//
//$respuesta = getUrl("https://zqdatarecovery.com/ip.php");

function geolocation($ipaddress){
    $cmd = "node geolite.js" . " ".$ipaddress ;
    $salida = exec($cmd , $output_array , $cmd_status);
    //Concateno la otra funcion por lo que las ip de los docker no me retornan el json correctamente
    //print_r($output_array[0]);
    $string = "Estas aqui";

    if ($output_array[0] === "null"){
        $volatil = Getaddressalt();
        return $volatil;
    }else{
        return $output_array[7];
    }

    
}


function coordenadas($arrayseg){
    $result = strval($arrayseg);
    $resultado = substr($result, 8, 16); 
    $positionx = substr($resultado, 0, 6); 
    $positiony = substr($resultado, 7, 9); 
    print $positionx;
    $cleany = preg_replace('/[^a-zA-Z0-9_%\[().\]\\/-]/s', '', $positiony);
    print $positiony;
    //print $coordenaday;  
    AnadirGeo($positionx,$cleany);
     
}



///////Comenzamos a interactuar con las funciones descritas /////////


GetIpOfNodes();

?>