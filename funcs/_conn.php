<?php
header('Access-Control-Allow-Origin: *');

$sql_host = "localhost";
$sql_user = "root";
$sql_pass = "";
$sql_base = "borebooru";

$maxPerPagina = 25;
$maxTagList = 25;

$DATA =  "C:/xampp/htdocs/DATA/";

Function conn(){
	global $sql_host,$sql_user,$sql_pass,$sql_base;
	return new mysqli($sql_host, $sql_user, $sql_pass, $sql_base);
}

Function agregar($tags,$format){
	$link = conn();
	$id = _getNumero();
	$tags = str_replace("  "," ",$tags);
	$link->query("INSERT INTO `imagenes` (`ID`, `TAG`, `FORMAT`) VALUES ('".$id."', '".$tags."', '".$format."');");
	
	//Insertar las tags en el contados
	$partes = explode(" ",$tags);
	foreach ($partes as $parte){
		contarTag($parte, 1);
	}
	
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
			foreach ($tags as $filtro){
				$mini = false;
				$parte = str_replace("  "," ",$row["TAG"]);
				$partes = explode(" ",$parte);
				foreach ($partes as $parte){
					if($parte == $filtro){
						$mini = true;
					}
				}
				if($mini == false){
					$PasaFiltros = false;
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


Function contarTag($tag, $dif){
	If ($tag == "" or $tag == " ")
		return;
	
	$link = conn();
	$result = $link->query("SELECT * FROM `tags` WHERE `TAG`='".$tag."';");
	$row = $result->fetch_assoc();
	if($row){
		$link->query("DELETE FROM `tags` WHERE `TAG`='".$tag."';");
		$link->query("INSERT INTO `tags` (`TAG`,`USOS`) VALUES ('".$tag."','"._formato($row["USOS"]+$dif)."');");
	}else{
		$link->query("INSERT INTO `tags` (`TAG`,`USOS`) VALUES ('".$tag."','1');");
	}
	
	$link->close();
}
Function getTags(){
	Global $maxTagList;
	$n = 0;
	
	$link = conn();
	$result = $link->query("SELECT * FROM `tags`");
	while($row = $result->fetch_assoc()){
		$tags_todos[$n] = array('tag' => $row["TAG"], 'usos' => $row["USOS"]);
		$n++;
	}
	
	if(!isset($tags_todos))
		return "NADA";
	
	usort($tags_todos, '_compararUsos');
	
	$i = 0;
	while($i < $n And $i < $maxTagList){
		$tags[$i] = array('tag' => $tags_todos[$i]["tag"], 'usos' => $tags_todos[$i]["usos"]);
		$i++;
	}
	
	$link->close();
	return $tags;
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
	$n = number_format($n);
	
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

Function _compararUsos($a, $b){
	return $a['usos'] < $b['usos'];
}

Function eliminar($id){
	global $DATA;
	$link = conn();
	
	$result = $link->query("SELECT * FROM imagenes WHERE id=$id");
	while($row = $result->fetch_assoc()){
		$partes = explode(" ",$row["TAG"]);
		foreach ($partes as $parte){
			contarTag($parte, -1);
		}
		unlink($DATA."original/"._formato($id).".".$row["FORMAT"]);
		unlink($DATA."thumbnails/"._formato($id).".jpg");
	}
	
	$link->query("DELETE FROM imagenes WHERE ID=$id;");
	
	$link->close();
	return $id;
}
?>