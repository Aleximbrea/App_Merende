<?php
    include_once("../other/php/utils.php");
    session_start();
    $dbh = connect();
	//prendo i dati dal form
	 $nome=$_POST["nome"];
	 $dominio = $_POST["dominio"];
	
	 
	 // Controllo che i campi siano stati completati
    $errors = [];
    if(empty($_POST['nome'])){
        $errors[] = 1;
		
    }
	if(empty($_POST['dominio'])){
        $errors[] = 2;
		
    }
	
	if(count($errors) == 0)
	{
	$sql = "INSERT INTO `istituti`(`ID`, `NomeIstituto`, `Dominio`)";
    $sql .= "VALUES ('','?',?)";
	$res=sqlInsert($dbn,$sql,"ss",$nome,$dominio);	
	}
	 $dbh->close();
    echo json_encode($errors);
	
?>