<?php
    class Conectar{

        protected $dbh;

        protected function Conexion(){

            try{
		
$serverName = "SERVIDOR\ASPEL";
$connectionInfo = array( 
 "Database"=>"SAE0903",
 "UID"=>"sa",
 "PWD"=>"ASPEL123*",
 "TrustServerCertificate"=>true
);
$conectar = sqlsrv_connect( $serverName, $connectionInfo);


                return $conectar;
            }catch(Exception $e){

                print "Error Conexion BD: ". $e->getMessage() . "<br/>";
                die();
            }
        }

    }
?>
