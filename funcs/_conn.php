<?php
header('Access-Control-Allow-Origin: *');

$sql_host = "localhost";
$sql_user = "root";
$sql_pass = "";
$sql_base = "borebooru";

$maxPerPagina = 25;

$DATA =  "C:/xampp/htdocs/DATA/";

Function conn(){
	global $sql_host,$sql_user,$sql_pass,$sql_base;
	return new mysqli($sql_host, $sql_user, $sql_pass, $sql_base);
}

Function agregar($tags,$format){
	$link = conn();
	$id = _getNumero();
	$link->query("INSERT INTO `imagenes` (`ID`, `TAG`, `FORMAT`) VALUES ('".$id."', '".$tags."', '".$format."');");
	$link->close();
	return $id;
}
Function getImage($tag,$page){
	Global $maxPerPagina;
	$tag = str_replace("  "," ",$tag);
	$tags = explode(" ",$tag);
	$link = conn();
	$result = $link->query("SELECT * FROM `imagenes`");
	$n = 0;
	$ninguna = true;
	while($row = $result->fetch_assoc()){
		$PasaFiltros = true;
		if($tag != null){
			foreach ($tags as $filtro) {
				$mini = false;
				$parte = str_replace("  "," ",$row["TAG"]);
				$partes = explode(" ",$parte);
				foreach ($partes as $parte) {
					if($parte == $filtro){
						$mini = true;
					}
				}
				if($mini == false){
					$PasaFiltros = false;
					$break;
				}
			}
		}
		if($PasaFiltros){
			if($n < ($page)*$maxPerPagina and $n >= ($page-1)*$maxPerPagina){
				$imagenes[$n] = array('id' => $row["ID"], 'format' => $row["FORMAT"]);
				
				$ninguna = false;
			}
			$n++;
		}
	}
	$link->close();
	$paginas = 0;
	while($n > 0){
		$paginas++;
		$n -= $maxPerPagina;
	}
	
	return array('imagenes' => $ninguna?null:$imagenes, 'paginas' => $paginas);
}
Function tumbnail($nombre){
	global $DATA;
	switch(pathinfo($nombre, PATHINFO_EXTENSION)){
		case "jpg":
			list($ancho, $alto, $tipo, $atributo) = getimagesize($DATA."original/".$nombre);
			if($ancho > $alto){
				$nuevo_ancho = 250;
				$nuevo_alto = $alto*(250/$ancho);
			}else{
				$nuevo_alto = 250;
				$nuevo_ancho = $ancho*(250/$alto);
			}
			$thumb = imagecreatetruecolor($nuevo_ancho, $nuevo_alto);
			$origen = imagecreatefromjpeg($DATA."original/".$nombre);
			imagecopyresized($thumb, $origen, 0, 0, 0, 0, $nuevo_ancho, $nuevo_alto, $ancho, $alto);
			imagejpeg($thumb,$DATA."thumbnails/".$nombre,75);
		break;
		case "png":
			list($ancho, $alto, $tipo, $atributo) = getimagesize($DATA."original/".$nombre);
			if($ancho > $alto){
				$nuevo_ancho = 250;
				$nuevo_alto = $alto*(250/$ancho);
			}else{
				$nuevo_alto = 250;
				$nuevo_ancho = $ancho*(250/$alto);
			}
			$thumb = imagecreatetruecolor($nuevo_ancho, $nuevo_alto);
			$origen = imagecreatefrompng($DATA."original/".$nombre);
			imagecopyresized($thumb, $origen, 0, 0, 0, 0, $nuevo_ancho, $nuevo_alto, $ancho, $alto);
			imagejpeg($thumb,$DATA."thumbnails/".pathinfo($nombre)['filename'].".jpg",75);
		break;
		case "gif":
			list($ancho, $alto, $tipo, $atributo) = getimagesize($DATA."original/".$nombre);
			if($ancho > $alto){
				$nuevo_ancho = 250;
				$nuevo_alto = $alto*(250/$ancho);
			}else{
				$nuevo_alto = 250;
				$nuevo_ancho = $ancho*(250/$alto);
			}
			$thumb = imagecreatetruecolor($nuevo_ancho, $nuevo_alto);
			$origen = imagecreatefromgif($DATA."original/".$nombre);
			imagecopyresized($thumb, $origen, 0, 0, 0, 0, $nuevo_ancho, $nuevo_alto, $ancho, $alto);
			imagejpeg($thumb,$DATA."thumbnails/".pathinfo($nombre)['filename'].".jpg",75);
		break;
		case "mp4":
			require 'vendor/autoload.php';
			$sec = 1;
			$movie = $DATA."original/".$nombre;
			$thumbnail = $DATA."thumbnails/".pathinfo($nombre)['filename'].".jpg";
			
			$ffmpeg = \FFMpeg\FFMpeg::create([
				'ffmpeg.binaries'  => 'c:\ffmpeg\bin\ffmpeg.exe',
				'ffprobe.binaries' => 'c:\ffmpeg\bin\ffprobe.exe' 
			]);
			$video = $ffmpeg->open($movie);
			$frame = $video->frame(FFMpeg\Coordinate\TimeCode::fromSeconds($sec));
			$frame->save($thumbnail);
			
			list($ancho, $alto, $tipo, $atributo) = getimagesize($thumbnail);
			if($ancho > $alto){
				$nuevo_ancho = 250;
				$nuevo_alto = $alto*(250/$ancho);
			}else{
				$nuevo_alto = 250;
				$nuevo_ancho = $ancho*(250/$alto);
			}
			$thumb = imagecreatetruecolor($nuevo_ancho, $nuevo_alto);
			$origen = imagecreatefromjpeg($thumbnail);
			imagecopyresized($thumb, $origen, 0, 0, 0, 0, $nuevo_ancho, $nuevo_alto, $ancho, $alto);
			imagejpeg($thumb,$thumbnail,75);
		break;
	}
	
}


Function _getNumero(){
	$link = conn();
	$result = $link->query("SELECT * FROM `numero`");
	$row = $result->fetch_assoc();
	$link->query("DELETE FROM `numero` WHERE `ultimo`='".$row["ultimo"]."'");
	$link->query("INSERT INTO `numero` (`ultimo`) VALUES ('"._formato($row["ultimo"]+1)."');");
	$link->close();
	return _formato($row["ultimo"]);
}
Function _formato($n){
	if($n < 10){
		return "00000".$n;
	}else if($n < 100){
		return "0000".$n;
	}else if($n < 1000){
		return "000".$n;
	}else if($n < 10000){
		return "00".$n;
	}else if($n < 100000){
		return "0".$n;
	}
	return $n;
}


/*
Function getTCode(){
	return date("YmdHis");
}
Function getUserData($id,$data){
	$link = conn();
	$result = $link->query("SELECT * FROM `usuarios` WHERE `ID`='".$id."'");
	$row = mysqli_fetch_assoc($result);
	$link->close();
	switch($data){
		case 1: return $row["nombre"];
		case 2: return $row["apellido"];
		case 3: return $row["email"];
		case 4: return $row["hash"];
		case 5: return $row["empresa"];
		case 6: return $row["direccion"];
		case 7: return $row["cesta"];
		case 8: return $row["telefono"];
		case 9: return $row["estado"];
		case 10: return $row["dni"];
		default: return "NULL";
	}
}
Function getPrecio($id){
	$result = $link->query("SELECT * FROM `productos` WHERE `ID`='".$id."'");
	$row = mysqli_fetch_assoc($result);
	return $row["precio"];
}
Function getProductoNombre($id){
	$link = conn();
	$result = $link->query("SELECT * FROM `productos` WHERE `ID`='".$id."'");
	$row = mysqli_fetch_assoc($result);
	$link->close();
	return $row["nombre"];
}
function getProductos(){
	$link = conn();
	$result = $link->query("SELECT * FROM `productos`");
	while ($row = $result->fetch_assoc()) {
		$productos[] = array('ID' => $row['ID'], 'nombre' => $row['nombre'], 'categoria' => $row['categoria']);
	}
	$link->close();
	return _val_sort($productos, 'categoria');
}
function getProductosPlus(){
	$link = conn();
	$result = $link->query("SELECT * FROM `productos`");
	while ($row = $result->fetch_assoc()) {
		$productos[] = array('ID' => $row['ID'], 'nombre' => $row['nombre'], 'categoria' => $row['categoria'], 'descripcion' => $row['descripcion'], 'precio' => $row['precio'], 'existencias' => $row['existencias']);
	}
	$link->close();
	return _val_sort($productos, 'categoria');
}
function getProductosData($id){
	$link = conn();
	$result = $link->query("SELECT * FROM `productos` WHERE `ID`='".$id."'");
	$row = $result->fetch_assoc();
	$producto = array('ID' => $row['ID'], 'nombre' => $row['nombre'], 'categoria' => $row['categoria'], 'descripcion' => $row['descripcion'], 'precio' => $row['precio'], 'existencias' => $row['existencias']);
	$link->close();
	return $producto;
}
function getProductosOrganized(){
	$link = conn();
	$result = $link->query("SELECT * FROM `productos`");
	$productos = Array();
	while ($row = $result->fetch_assoc()) {
		$productos[$row['ID']] = array('nombre' => $row['nombre'], 'categoria' => $row['categoria'], 'descripcion' => $row['descripcion'],'precio' => $row['precio'], 'existencias' => $row['existencias']);
	}
	$link->close();
	return $productos;
}
function getPedidos(){
	$link = conn();
	$result = $link->query("SELECT * FROM `pedidos`");
	while ($row = $result->fetch_assoc()) {
		$productos[] = array('ID' => $row['ID'], 'usuario' => $row['usuario'], 'estado' => $row['estado']);
	}
	foreach( $productos as $key => $value ) {
		switch($productos[$key]["estado"]){
			case 1: $productos[$key]["estado"] = "Sin confirmar"; break;
			case 2: $productos[$key]["estado"] = "Pendiente de pago"; break;
			case 3: $productos[$key]["estado"] = "Enviado"; break;
			case 4: $productos[$key]["estado"] = "Finalizado"; break;
			case 5: $productos[$key]["estado"] = "Cancelado"; break;
			default: $productos[$key]["estado"] = "ERROR"; break;
		}
		$result = $link->query("SELECT * FROM `usuarios` WHERE `ID`='".$productos[$key]["usuario"]."'");
		if($result == null){
			continue;
		}
		$row = $result->fetch_assoc();
		$productos[$key]["usuario"] = $row["apellido"].", ".$row["nombre"];
	}
	$link->close();
	return $productos;
}
function getPedidoData($id){
	$link = conn();
	$result = $link->query("SELECT * FROM `pedidos` WHERE `ID`='".$id."'");
	$row = $result->fetch_assoc();
	if($row["seguimiento"] != ""){
		$porte = getEnvioCoste($id);
	}else{
		$porte = "";
	}
	$producto = array('ID' => $row['ID'], 'usuario' => $row['usuario'], 'producto' => $row['producto'], 'direccion' => $row['direccion'], 'anotaciones' => $row['anotaciones'], 'seguimiento' => $row['seguimiento'], 'envio' => $porte, 'estado' => $row['estado']);
	$link->close();
	return $producto;
}
function getUsuarios(){
	$link = conn();
	$result = $link->query("SELECT * FROM `usuarios`");
	while ($row = $result->fetch_assoc()) {
		$productos[] = array('ID' => $row['ID'], 'nombre' => $row['apellido'].", ".$row['nombre'], 'email' => $row['email'], 'telefono' => $row['telefono'], 'empresa' => $row['empresa'], 'dni' => $row['dni'], 'estado' => $row['estado']);
	}
	foreach( $productos as $key => $value ) {
		switch($productos[$key]["estado"]){
			case 1: $productos[$key]["estado"] = "Sin activar"; break;
			case 2: $productos[$key]["estado"] = "Activo"; break;
			case 3: $productos[$key]["estado"] = "Cancelado"; break;
			default: $productos[$key]["estado"] = "ERROR"; break;
		}
		$productos[$key]["empresa"] = $productos[$key]["empresa"] == "NAN"?"No procede":$productos[$key]["empresa"];
	}
	$link->close();
	return $productos;
}


function _val_sort($array,$key) {
	foreach($array as $k=>$v) {
		$b[] = strtolower($v[$key]);
	}
	asort($b);
	foreach($b as $k=>$v) {
		$c[] = $array[$k];
	}
	return $c;
}
function intercambiar($sesion){
	$link = conn();
	$result = $link->query("SELECT * FROM `sesiones` WHERE `sesion`='".$sesion."'");
	$row = $result->fetch_assoc();
	$link->close();
	return $row["usuario"];
}
function getVariables(){
	$link = conn();
	$result = $link->query("SELECT * FROM `vars`;");
	$valores = Array();
	while($row = $result->fetch_assoc()){
		$valores[$row["clave"]] = $row["valor"];
	}
	$link->close();
	return $valores;
}
function getFacturaData($id){
	$link = conn();
	$result = $link->query("SELECT * FROM `pedidos` WHERE `ID` = '".$id."'");
	$row = $result->fetch_assoc();
	$pedido = array('ID' => $row['ID'], 'usuario' => $row['usuario'], 'producto' => $row['producto'], 'direccion' => $row['direccion'], 'anotaciones' => $row['anotaciones'], 'estado' => $row['estado'], 'seguimiento' => $row['seguimiento']);
	$link->close();
	return $pedido;
}
function getAllUserData($sesion){
	$id = intercambiar($sesion);
	$link = conn();
	$result = $link->query("SELECT * FROM `usuarios` WHERE `ID` = '".$id."'");
	$row = $result->fetch_assoc();
	$pedido = array('ID' => $row['ID'], 'nombre' => $row['nombre'], 'apellido' => $row['apellido'], 'email' => $row['email'], 'hash' => $row['hash'], 'empresa' => $row['empresa'], 'direccion' => $row['direccion'], 'cesta' => $row['cesta'], 'telefono' => $row['telefono'], 'dni' => $row['dni'], 'estado' => $row['estado']);
	$link->close();
	return $pedido;
}
function divideDireccion($direccion){
	$partes = explode('|',$direccion);
	return array('pais' => $partes[0], 'provincia' => $partes[1], 'localidad' => $partes[2], 'cp' => $partes[3], 'direccion1' => $partes[4], 'direccion2' => $partes[5]);
}
function unirDireccion($direccion){
	$unida = $direccion["pais"]."|".$direccion["provincia"]."|".$direccion["localidad"]."|".$direccion["cp"]."|".$direccion["direccion1"]."|".$direccion["direccion2"]."|";
	return $unida;
}
function dividePedido($pedido){
	$productos = explode('|',$pedido);
	for($i = 0; $i < sizeof($productos); $i++){
		$partes = explode('x',$productos[$i]);
		if(!empty($partes[3])){
			$dividido[] = array('id' => $partes[0], 'cantidad' => $partes[1], 'cocido' => $partes[2], 'precio' => $partes[3], 'descuento' => $partes[4]);
		}else{
			$dividido[] = array('id' => $partes[0], 'cantidad' => $partes[1], 'cocido' => $partes[2]);
		}
	}
	return $dividido;
}
function unirPedido($pedido){
	$unido = "";
	$primero = true;
	foreach( $pedido as $key => $value ) {
		if(!empty($pedido[$key]["precio"])){
			$unido .= ($primero?"":"|").$pedido[$key]["id"]."x".$pedido[$key]["cantidad"]."x".$pedido[$key]["cocido"]."x".$pedido[$key]["precio"];
		}else{
			$unido .= ($primero?"":"|").$pedido[$key]["id"]."x".$pedido[$key]["cantidad"]."x".$pedido[$key]["cocido"];
		}
		$primero = false;
	}
	return $unido;
}
function getEnvioCoste($id){
	$link = conn();
	$result = $link->query("SELECT * FROM `vars` WHERE `clave` = '".$id."'");
	$row = $result->fetch_assoc();
	$link->close();
	return $row['valor'];
}
function getAllCategorias(){
	$link = conn();
	$result = $link->query("SELECT * FROM `productos`");
	$categorias["nombre"] = null;
	$categorias["id"] = null;
	$n = 0;
	while($row = $result->fetch_assoc()){
		if(_noRepetition($row["categoria"], $categorias, $n)){
			$categorias["nombre"][] = $row["categoria"];
			$categorias["id"][] = $row["ID"];
			$n++;
		}
	}
	$link->close();
	return $categorias;
}
function _noRepetition($categoria, $lista, $n){
	for($i = 0; $i < $n; $i++){
		if($categoria == $lista["nombre"][$i]){
			return false;
		}
	}
	return true;
}
function getFecha($id){
	global $nombre_meses;
	$fecha = substr($id,7,2)." de ".$nombre_meses[substr($id,5,2)]." de ".substr($id,1,4);
	return $fecha;
}
function getSomePedidos($page, $sesion){
	$id = intercambiar($sesion);
	$link = conn();
	$result = $link->query("SELECT * FROM `pedidos`");
	$max = 5;
	$ya = true;
	while ($row = $result->fetch_assoc()) {
		if($max != 0 and $row['usuario'] == $id){
			$productos[] = array('ID' => $row['ID'], 'usuario' => $row['usuario'], 'producto' => $row['producto'], 'estado' => $row['estado']);
			$max--;
			$ya = false;
		}
	}
	$link->close();
	if($ya){
		return "ninguno";
	}else{
		return array_reverse($productos);
	}
}

function getDescuentos(){
	$link = conn();
	$result = $link->query("SELECT * FROM `ofertas`");
	$nada = true;
	while($row = $result->fetch_assoc()){
		$ofertas[] = array('producto' => $row['Producto'], 'descuento' => $row['Descuento'], 'cantidad' => $row['Cantidad']);
		$nada = false;
	}
	$link->close();
	if($nada){
		return null;
	}
	return $ofertas;
}
function getDescuento($producto){
	$link = conn();
	$result = $link->query("SELECT * FROM `ofertas` WHERE `Producto` = '".$producto."'");
	$nada = true;
	while($row = $result->fetch_assoc()){
		$oferta = array('producto' => $row['Producto'], 'descuento' => $row['Descuento'], 'cantidad' => $row['Cantidad']);
		$nada = false;
	}
	$link->close();
	if($nada){
		return null;
	}
	return $oferta;
}
function userDescuento($producto,$cantidad){
	$link = conn();
	$result = $link->query("SELECT * FROM `ofertas` WHERE `Producto` = '".$producto."'");
	$nada = true;
	$row = $result->fetch_assoc();
	$oferta = array('producto' => $row['Producto'], 'descuento' => $row['Descuento'], 'cantidad' => $row['Cantidad']);
	$link->query("DELETE FROM `ofertas` WHERE `Producto` = '".$producto."'");
	if($cantidad < $oferta["cantidad"]){
		$link->query("INSERT INTO `ofertas` (`Producto`, `Cantidad`, `Descuento`) VALUES ('".$oferta["producto"]."', '".($oferta["cantidad"]-$cantidad)."', '".$oferta["descuento"]."');");
	}
	$link->close();
}

function getFacturaTrazabilidad($id){
	$link = conn();
	$result = $link->query("SELECT * FROM `trazabilidad` WHERE `Venta` = '".$id."'");
	$nada = true;
	while ($row = $result->fetch_assoc()){
		$nada = false;
		break;
	}
	$link->close();
	if($nada){
		return "";
	}
	return $row['Factura'];
}


/* Seccion de emails */
//$rem = array('TEST' => '<h1>COSA HERMOSA DE CODIGO</h1>');
//sendMail("","U20180816111200","","activacion",$rem,"Mira mira","");

//$array_remplazo = array('[ID]' => "nope", '[NOMBRE]' => "Borja");
//sendMail("borjainlive@gmail.com","","","activacion",$array_remplazo,"Activacion [NOMBRE DE EMPRESA]","");

function sendMail($correo = "", $id= "", $sesion = "", $base = "plantilla", $datos = null, $asunto = "", $adjunto = ""){
	if($correo != ""){
		$email = $correo;
	}else{
		if($id != ""){
			$email = _getMailFromID($id);
		}else{
			if($sesion != ""){
				$email = _getMailFromSesion($sesion);
			}else{
				return false;
			}
		}
	}
	$nombreEmail = _getNombreEmail($email);
	
	$cuerpo = file_get_contents("http://localhost/parts/mails/".$base.".html");
	foreach( $datos as $key => $value ) {
		$cuerpo = str_replace("[".$key."]",$datos[$key],$cuerpo);
	}
	
	$cuerpoalt = "Error";
	enviarCorreo($email,$nombreEmail,$asunto,$cuerpo,$cuerpoalt,$adjunto);
}
function _getMailFromID($id){
	$link = conn();
	$result = $link->query("SELECT * FROM `usuarios` WHERE `ID` = '".$id."'");
	$row = $result->fetch_assoc();
	$link->close();
	return $row["email"];
}
function _getMailFromSesion($sesion){
	$link = conn();
	$result = $link->query("SELECT * FROM `sesiones` WHERE `sesion`='".$sesion."'");
	$row = $result->fetch_assoc();
	$results = $link->query("SELECT * FROM `usuarios` WHERE `ID` = '".$row["usuario"]."'");
	$rows = $results->fetch_assoc();
	$link->close();
	return $rows["email"];
}
function _getNombreEmail($email){
	$link = conn();
	$result = $link->query("SELECT * FROM `usuarios` WHERE `email` = '".$email."'");
	$row = $result->fetch_assoc();
	$link->close();
	return $row["apellido"].", ".$row["nombre"];
}










?>