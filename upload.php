<?php
	include "funcs/_conn.php";
	
	if(!empty($_GET["mod"])){
		$id = agregar($_POST["tags"],pathinfo($_FILES["imagen"]["name"], PATHINFO_EXTENSION));
		
		$name = $id.".".pathinfo($_FILES["imagen"]["name"], PATHINFO_EXTENSION);
		move_uploaded_file($_FILES["imagen"]["tmp_name"], $DATA."original/".$name);
		tumbnail($name);
		
		//header("Location: upload.php");
		//die();
	}
?>
<html>
	<head>
		<title>BoreBooru</title>
		<meta http-equiv="content-type" content="text/html; charset=utf-8" />
		<link rel="stylesheet" href="css/style.css" type="text/css" />
		<link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
	</head>
	<body>
	
	<div class="w3-sidebar w3-light-grey w3-bar-block" style="width:20%">
		<a href="index.php" target="_self"><h3 class="w3-bar-item"><img src="img/logo.png" width="100%" height="auto" /></h3></a>
	</div>

	<div style="margin-left:20%">
		<div class="w3-container w3-teal" style="text-align:center;">
		  <h1>Subir al almacen de B0vE</h1>
		</div>
		<div class="uploadContainer">
			<form action="upload.php?mod=true" method="post" enctype="multipart/form-data">
				<input type="file" name="imagen"><br>
				<input type="text" size="90" name="tags" value="" placeholder="Remplaza espacios por _ y usa espacios para separar los tags"><br>
				<input type="submit" value="Subir">
			</form>
		</div>
	</div>
	
	</body>
</head>