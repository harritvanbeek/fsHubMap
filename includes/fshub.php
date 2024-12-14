<?php
    class fshub{
        protected   $env    = NULL,  
                    $apiUri = "https://fshub.io/api/v3/";


        public function __construct()
        {
            //$this->env        =   NEW \classes\core\env;                      

        }

        public function get($methode = null)
        {
            if($methode){
                return self::request($methode);
            }
        }

        protected function request($methode = null)
        {
            $headers = [
                'X-Pilot-Token:CBg4TijKG08PtleUxqIQskRasroYQkylMmKgo36nhkwSVvwzpiw45Yxhuoyx',
                'User-Agent: FsHub-Sdk-Php/1.0',
            ];
            
            $curl = curl_init(); 
            curl_setopt($curl,  CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($curl,  CURLOPT_URL, $this->apiUri.$methode);
            curl_setopt($curl,  CURLOPT_HTTPHEADER, $headers);
            curl_setopt($curl,  CURLOPT_RETURNTRANSFER, true);
            
            $head       = curl_exec($curl);
            $httpCode   = curl_getinfo($curl, CURLINFO_HTTP_CODE);    
            $data       = json_decode($head, false);
            
            curl_close($curl);
                        
            return $data;
        }
    }
