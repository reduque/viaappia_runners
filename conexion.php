<?php
@session_start();
//ini_set('default_charset', 'utf-8');
date_default_timezone_set('America/Caracas');

$ruta='https://viaappia.test/img/';

/*
Variables de entorno
composer require vlucas/phpdotenv
*/

require __DIR__ . '/vendor/autoload.php';
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();


$servername='localhost';

$username=$_ENV["DB_USERNAME"];
$password=$_ENV["DB_PASSWORD"];
$database=$_ENV["DB_DATABASE"];


$mysqli = new mysqli($servername, $username, $password, $database);
if ($mysqli->connect_error) {
  die("Connection failed: " . $mysqli->connect_error);
}
$mysqli->set_charset("utf8");
//$mysqli = mysqli_connect($servername, $username, $password, $database);

function lee1($q){
	$c = $GLOBALS['mysqli']->query($q);
	$r=mysqli_fetch_array($c, MYSQLI_ASSOC);
	$c->close();
	return $r;
}

function lee1o($q){
	$c = $GLOBALS['mysqli']->query($q);
	$r=mysqli_fetch_object($c);
	$c->close();
	return $r;
}

function leen($q){
	$r = $GLOBALS['mysqli']->query($q);
	$r->fetch_all(MYSQLI_ASSOC);
	//$r->close();
	return $r;
}


function rqq($objeto){
	$temp='';
	if(isset($_REQUEST[$objeto])) $temp = trim($_REQUEST[$objeto]);
	
	$virus = array("'","--","__","=","<",">");
	$cambios  = array("","","","","","");
	$temp = str_replace($virus, $cambios, $temp);
	return $temp;
}
function rq2($objeto){
	$temp='';
	if(isset($_REQUEST[$objeto])) $temp = trim($_REQUEST[$objeto]);
	$virus = array("'","--","__","script");
	$cambios  = array("´","","","");
	$temp = str_replace($virus, $cambios, $temp);
	return $temp;
}
function volteafecha($cualfecha){
	$mfecha=explode("-", $cualfecha);
	return $mfecha[2] . "/" . $mfecha[1] . "/" . $mfecha[0];
}

$meses = array("","Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Setiembre", "Octubre", "Noviembre", "Diciembre");

function codifica($valor){
	$valor+=1000;
	$mrcd=array( 0 =>'A', 1 =>'B', 2 =>'C', 3 =>'D', 4 =>'E', 5 =>'F', 6 =>'G', 7 =>'H', 8 =>'I', 9 =>'J',10 =>'K',11 =>'L',12 =>'M',13 =>'N',14 =>'O',15 =>'P',16 =>'Q',17 =>'R',18 =>'S',19 =>'T',20 =>'U',21 =>'V',22 =>'W',23 =>'X',24 =>'Y',25 =>'Z',26 =>'1',27 =>'2',28 =>'3',29 =>'4',30 =>'5',31 =>'6',32 =>'7',33 =>'8',34 =>'9',35 =>'0',36 =>'a',37 =>'b',38 =>'c',39 =>'d',40 =>'e',41 =>'f',42 =>'g',43 =>'h',44 =>'i',45 =>'j',46 =>'k',47 =>'l',48 =>'m',49 =>'n',50 =>'o',51 =>'p',52 =>'q',53 =>'r',54 =>'s',55 =>'t',56 =>'u',57 =>'v',58 =>'w',59 =>'x',60 =>'y',61 =>'z');
	$separador=rand(0,6);
	//$separador=0;
	$salida="";
    while($valor>55){
		@$division    = $valor/55;
		@$resultINT   = floor($valor/55);
		@$remnant     = $valor%55;
		$salida  = $mrcd[$remnant+$separador].
		$mrcd[61-($remnant+$separador)].$salida;
		$valor=$resultINT;
	}
	$salida  = $mrcd[$separador*5] . $mrcd[$valor+$separador]. $mrcd[61-($valor+$separador)].$salida;
	return $salida;
}
function decodifica($valor){
	$mrcd=array( 0 =>'A', 1 =>'B', 2 =>'C', 3 =>'D', 4 =>'E', 5 =>'F', 6 =>'G', 7 =>'H', 8 =>'I', 9 =>'J',10 =>'K',11 =>'L',12 =>'M',13 =>'N',14 =>'O',15 =>'P',16 =>'Q',17 =>'R',18 =>'S',19 =>'T',20 =>'U',21 =>'V',22 =>'W',23 =>'X',24 =>'Y',25 =>'Z',26 =>'1',27 =>'2',28 =>'3',29 =>'4',30 =>'5',31 =>'6',32 =>'7',33 =>'8',34 =>'9',35 =>'0',36 =>'a',37 =>'b',38 =>'c',39 =>'d',40 =>'e',41 =>'f',42 =>'g',43 =>'h',44 =>'i',45 =>'j',46 =>'k',47 =>'l',48 =>'m',49 =>'n',50 =>'o',51 =>'p',52 =>'q',53 =>'r',54 =>'s',55 =>'t',56 =>'u',57 =>'v',58 =>'w',59 =>'x',60 =>'y',61 =>'z');
	$separador=0;
	$mvalor=str_split($valor);
	$resultado1=0;
	$resultado2=0;
	for($i=0;$i<=61;$i++)
		if($mvalor[0]==$mrcd[$i])
			$separador=$i/5;
			
	$contexp=((count($mvalor)-1)/2)-1;
	for($l=1;$l<count($mvalor);$l+=2){
		for($i=0;$i<=61;$i++){
			if($mvalor[$l]==$mrcd[$i]){
				$resultado1+=($i-$separador)*pow(55,$contexp);
			}
			if($mvalor[$l+1]==$mrcd[$i]){
				$resultado2+=(61-($i+$separador))*pow(55,$contexp);
			}
		}
		$contexp--;
	}
	if($resultado1==$resultado2 and is_int($separador)){
		return $resultado1 - 1000;
	}else{
		return "";
	}
}


function crea_insert($table,$datos){
	$campos=[];
	$valores=[];
	foreach($datos as $key => $dato){
		$campos[]=$key;
		$valores[]="'" . $dato . "'";
	}
	return "INSERT INTO " . $table . " (" . implode(',',$campos) . ") values (" . implode(',',$valores) . ")";
}

function crea_update($table,$datos,$where){
	$campos=[];
	foreach($datos as $key => $dato){
		$campos[]=$key . " = '" . $dato . "'";
	}
	return "UPDATE "  . $table . " SET " . implode(',',$campos) . " " . $where;
}

function estatus_array(){
	return [
		0 => 'Sin Atender',
		1 => 'En Proceso',
		2 => 'En Revisión', // no hay todos los productos
		3 => 'Atendido',
		4 => 'Editando',
		5 => 'Esperando aprobación',
		6 => 'Pagado',
		7 => 'En Despacho',
		8 => 'Entregado',
		10 => 'Eliminado'
	];
}


function enviar_push($url, $to, $titulo, $cuerpo, $mensaje){
	$token = $_ENV["FIREBASE_API_KEY"];
	//$to = $runners;
	$msg = array
		(
			'body'  => $cuerpo,
			'title' => $titulo,
			'icon'  => ("./android-chrome-192x192.png"),
			"sound" => "default"
			//'click_action' => "https://viaappia_runners.test/enero"
		);
	$fields = array
			(
				'registration_ids' => $to,
				'notification'  => $msg,
				'data' => [
					'url' => $url,
					'mensaje' => $mensaje
				]
			);
	$headers = array
			(
				'Authorization: key=' . $token,
				'Content-Type: application/json',
				'project_id: 639035428084'
			);

	$ch = curl_init();
	curl_setopt( $ch,CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send' );
	curl_setopt( $ch,CURLOPT_POST, true );
	curl_setopt( $ch,CURLOPT_HTTPHEADER, $headers );
	curl_setopt( $ch,CURLOPT_RETURNTRANSFER, true );
	curl_setopt( $ch,CURLOPT_SSL_VERIFYPEER, false );
	curl_setopt( $ch,CURLOPT_POSTFIELDS, json_encode( $fields ) );
	$result = curl_exec($ch );
	//echo($result);
	curl_close( $ch );
}