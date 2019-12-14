<?php
session_start();
require_once("../fonctions.php");

$mysqli = db_connexion();

include ('../nb_online.php');

// recuperation config jeu
$sql = "SELECT disponible FROM config_jeu";
$res = $mysqli->query($sql);
$t_dispo = $res->fetch_assoc();

$dispo = $t_dispo["disponible"];

if($dispo){

	if (@$_SESSION["id_perso"]) {
		
		//recuperation des varaibles de sessions
		$id = $_SESSION["id_perso"];
		
		$sql = "SELECT pv_perso FROM perso WHERE id_perso='$id'";
		$res = $mysqli->query($sql);
		$tpv = $res->fetch_assoc();
		
		$testpv = $tpv['pv_perso'];
		
		if ($testpv <= 0) {
			echo "<font color=red>Vous êtes mort...</font>";
		}
		else {
			
			$erreur = "";
			
			if(isset($_GET["id_compagnie"])){
			
				$id_compagnie = $_GET["id_compagnie"];
				
				$verif1 = preg_match("#^[0-9]+$#i",$id_compagnie);
			
				if($verif1){
				
					// verification que le perso appartient bien a la compagnie
					$sql = "SELECT id_compagnie FROM perso_in_compagnie WHERE id_perso='$id' AND attenteValidation_compagnie='0'";
					$res = $mysqli->query($sql);
					
					$verif = $res->num_rows;
					
					if($verif){
					
						$sql = "SELECT resume_compagnie FROM compagnies WHERE id_compagnie=$id_compagnie";
						$res = $mysqli->query($sql);
						$t = $res->fetch_assoc();
						
						$resume = $t["resume_compagnie"];
						
						if(isset($_POST["changer"])) {
							
							$resume = addslashes($_POST["resume"]);
							$sql = "UPDATE compagnies SET resume_compagnie='$resume' WHERE id_compagnie='$id_compagnie'";
							$mysqli->query($sql);
							
							header("Location:compagnie.php");
						}
		
	?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
	<head>
		<title>Nord VS Sud</title>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	</head>
	<body>
	
	<div align="center">Sur cette page vous avez la possibilité de changer le resume de votre compagnie :<br>
	
		<form method="post" action="">
			<TEXTAREA cols="100" rows="1" name="resume">
<?php 
		if($resume == "") echo "Aucun resume"; 
		else echo "$resume";
?>
			</TEXTAREA><br><input type="submit" name="changer" value="changer">
		</form>
	</div>
	
	</body>
</html>
	<?php
					}
					else {
						echo "<center>Vous n'appartenez pas à cette compagnie</center>";
					}
				}
				else {
					echo "<center>La compagnie demandé n'existe pas</center>";
				}
			}
			else {
				echo "<center>Aucune compagnie spécifié</center>";
			}
		}
	}
	else{
		echo "<font color=red>Vous ne pouvez pas accéder à cette page, veuillez vous loguer.</font>";
	}?>
<?php
}
else {
	// logout
	$_SESSION = array(); // On ecrase le tableau de session
	session_destroy(); // On detruit la session
	
	header("Location: index2.php");
}
?>