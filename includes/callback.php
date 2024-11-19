<?php 
    session_start();
    include_once "db.php";
    include_once "fshub.php";
    
    $db         =   NEW db();
    $fshub      =   NEW fshub();
    
    $fshubID    =   2030;
    $userdata   =   $fshub->get("airline/{$fshubID}/pilot")->data; 
    $action     =   !empty($_GET["action"]) ?  $_GET["action"]: null;
    
    switch($action){  
        case "removeFlight":
        
        break;

        case "getFlight":
            $_POST      =   json_decode(file_get_contents("php://input"), true)["0"];
            $uuid   =   !empty($_POST["data"]) ? $_POST["data"] : null;
            if($uuid){
                $dataArray      = ["uuid" => $uuid];
                $query          = "SELECT * FROM `blackbox` WHERE `uuid` = :uuid"; 
                $flicht         = $db->get($query, $dataArray);
                $flichtDate     = json_decode($flicht->flightdata);

                $dataArray =    [
                    "departure"     =>  "{$flichtDate->departure->city}",
                    "depIcao"       =>  "{$flichtDate->departure->icao}",
                    
                    "arrival"       =>  "{$flichtDate->arrival->city}",
                    "arrivalIcao"   =>  "{$flichtDate->arrival->icao}",

                    "airline"       =>  "{$flichtDate->airline}",
                    "pilot"         =>  "{$flichtDate->pilotName}",
                    "aircraft"      =>  "{$flichtDate->name}",
                    "aircraftShort" =>  "{$flichtDate->shortname}",
                    "landing_rate"  =>  "{$flichtDate->landing_rate}ft",
                    "distance"      =>  "{$flichtDate->distance}",
                    "fuelUsed"      =>  "{$flichtDate->fuelUsed} KGS"
                ];
                //echo print_r( $dataArray );
                //echo print_r( $flichtDate );

                echo json_encode($dataArray);
            }            
        break;

        case "getBlackboxData":
                $query      = "SELECT * FROM `blackbox` ORDER BY `blackbox`.`postDate` DESC";
                $flichtDate   = $db->getAll($query);
                if($flichtDate){
                    foreach($flichtDate AS $flicht){
                        $data   =   json_decode($flicht->flightdata);
                        $array[] = [
                            "uuid"  => "{$flicht->uuid}",
                            "from"  => "{$data->departure->city} ({$data->departure->icao})",
                            "to"    => "{$data->arrival->city} ({$data->arrival->icao})",
                            "date"  => "{$flicht->postDate}",
                        ];
                    }
                    
                    echo json_encode($array);
                }
        break;

        case "reloadBlackboxData":
                foreach($userdata as $user){
                    if($user->is_online > 0){
                        
                        //set departure in session = locale
                        if(empty($_SESSION["departure"]) === true){
                            //set session for later check
                            $_SESSION["departure"]  = $user->locale;
                        }
                        
                        //set arrival in session
                        if($_SESSION["departure"] !== $user->locale){
                            //set session arrival and post in to database as json
                            $_SESSION["arrival"]  = $user->locale;
                            
                            $airline        =   $fshub->get("airline/{$fshubID}/arrival/{$_SESSION["arrival"]}")->data; 
                            
                            //echo "<pre>", print_r($airline[0]->links[0]->uri);
                            //die;
                            $flightID       =   explode("/", $airline[0]->links[0]->uri)[2];
                            $geo            =   $fshub->get("flight/{$flightID}/geo")->data;                                 
                            
                            $fuelUsed       = $airline[0]->departure->fuel - $airline[0]->arrival->fuel;

                            $arrayJson = [
                                "pilotName"     => "{$airline[0]->user->name}",
                                "departure"     =>  [
                                    "city"      =>  "{$airline[0]->departure->name}",
                                    "icao"      =>  "{$_SESSION["departure"]}",
                                    "geo"       =>  [
                                        "lat"   =>  "{$airline[0]->departure->geo->lat}",
                                        "lng"   =>  "{$airline[0]->departure->geo->lng}",
                                    ],

                                ],

                                "arrival"       =>  [
                                    "city"      =>  "{$airline[0]->arrival->name}",
                                    "icao"      =>  "{$_SESSION["arrival"]}",
                                    "geo"       =>  [
                                        "lat"   =>  "{$airline[0]->arrival->geo->lat}",
                                        "lng"   =>  "{$airline[0]->arrival->geo->lng}",
                                    ],

                                ],

                                "geo"           =>  $geo->track->features[2]->geometry->coordinates,
                                
                                "airline"       =>  "{$airline[0]->airline->name} ({$airline[0]->airline->abbr})",
                                "flightNr"      =>  "{$airline[0]->plan->callsign}",
                                "crlv"          =>  "FL{$airline[0]->plan->cruise_lvl}",
                                "route"         =>  "FL{$airline[0]->plan->route}",

                                "name"          =>  "{$airline[0]->aircraft->icao_name}",
                                "shortname"     =>  "{$airline[0]->aircraft->icao}",
                                "tail"          =>  !empty($airline[0]->aircraft->user_conf->tail) ? $airline[0]->aircraft->user_conf->tail : "N/A",

                                "departureFuel" =>  "{$airline[0]->departure->fuel}",
                                "arrivalFuel"   =>  "{$airline[0]->arrival->fuel}",
                                "fuelUsed"      =>  "{$fuelUsed}",

                                "landing_rate"  =>  "{$airline[0]->landing_rate}",
                                "distance"      =>  "{$airline[0]->distance->nm}NM / {{$airline[0]->distance->km}km} ",
                                "pitch"         =>  "{$airline[0]->arrival->pitch}",   
                                "bank"          =>  "{$airline[0]->arrival->bank}",   
                                "speed"         =>  "{$airline[0]->arrival->spd->tas}",                                
                                "wind"          =>  "{$airline[0]->arrival->wind->spd}",                                
                            ];

                            $jsonData = json_encode($arrayJson);
                            
                            function MakeUuid(){
                                return sprintf( '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
                                    mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ),
                                    mt_rand( 0, 0xffff ),
                                    mt_rand( 0, 0x0fff ) | 0x4000,
                                    mt_rand( 0, 0x3fff ) | 0x8000,
                                    mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff )
                                );
                            }


                            if($jsonData){
                                $dataArray  =   [
                                    "uuid"          =>  MakeUuid(),
                                    "flightData"    =>  $jsonData,
                                    "postDate"      =>  getdate()["year"]."-".getdate()["mon"]."-".getdate()["mday"]." ".getdate()["hours"].":".getdate()["minutes"].":".getdate()["seconds"],
                                ];
                                
                                //post into database
                                $query      = "INSERT INTO `blackbox` (`uuid`, `flightData`) VALUES (:uuid, :flightData)";
                                $database   = $db->action($query, $dataArray);
                                
                                //end reset the session after database return success posted                                    
                                if($database){
                                    session_unset();                                            
                                }
                            }
                        }                                                           
                    }
                }   
            break;   
        }
?>
