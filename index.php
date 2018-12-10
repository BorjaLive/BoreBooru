<?php
	include "funcs/_conn.php";
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
		<a href="upload.php" target="_self"><h3 class="w3-bar-item"><img src="img/logo.png" width="100%" height="auto" /></h3></a>
		<div class="searchBox">
			<form action="" method="get">
				<input type="text" name="search" value=""/>
				<input type="submit" value="Buscar" />
			</form>
		</div>
		<div class="tagBox">
			<h3 class="w3-bar-item">Tags</h3>
			<a href="#" class="w3-bar-item w3-button">Tag 1</a>
			<a href="#" class="w3-bar-item w3-button">Tag 2</a>
			<a href="#" class="w3-bar-item w3-button">Tag 3</a>
		</div>
	</div>

	<div style="margin-left:20%">
		<div class="w3-container w3-teal" style="text-align:center;">
		  <h1>El almacén bien clasificado de B0vE</h1>
		</div>
		<div class="thumbContainer">
			<?php
				$data = getImage(empty($_GET["search"])?null:$_GET["search"],empty($_GET["page"])?1:$_GET["page"]);
				$imagenes = $data["imagenes"];
				$paginas = $data["paginas"];
				if($imagenes == null){
					echo "<h1>Nada de nada</h1>";
				}else{
					foreach ($imagenes as $imagen) {
						echo '<a href="DATA/original/'.$imagen["id"].'.'.$imagen["format"].'" target="_blank"><img src="DATA/thumbnails/'.$imagen["id"].'.jpg"/></a>';
					}
				}
				
			?>
		</div>
		<hr>
		<div class="paginamiento" style="display:<?php echo $imagenes==null?"none":"block";?>;">
			<?php
				if(empty($_GET["page"])){
					if($paginas > 1){
						$siguiente = 2;
					}else{
						$siguiente = 1;
					}
				}else{
					if($paginas > $_GET["page"]){
						$siguiente = $_GET["page"]+1;
					}else{
						$siguiente = $_GET["page"];
					}
				}
				if(!empty($_GET["page"]) and $_GET["page"] != 1){
					$anterior = $_GET["page"]-1;
				}else{
					$anterior = 1;
				}
			?>
			<a href="?page=1<?php echo empty($_GET["search"])?"":"&search=".$_GET["search"] ?>" target="_self"><---</a> <a href="?page=<?php echo $anterior; ?><?php echo empty($_GET["search"])?"":"&search=".$_GET["search"] ?>" target="_self"><-</a> 
			<?php
				for($i = 1; $i <= 5; $i++){
					if($paginas >= $i){
						echo ' <a href="?page='.$i.(empty($_GET["search"])?"":"&search=".$_GET["search"]).'" target="_self">'.$i.'</a> ';
					}
				}
			?>
			<a href="?page=<?php echo $siguiente; ?><?php echo empty($_GET["search"])?"":"&search=".$_GET["search"] ?>" target="_self">-></a> <a href="?page=<?php echo $paginas; ?><?php echo empty($_GET["search"])?"":"&search=".$_GET["search"] ?>" target="_self">---></a>
		</div>
	</div>
	
	</body>
</head>