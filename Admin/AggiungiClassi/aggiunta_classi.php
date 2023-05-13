<?php
    include_once("../other/php/utils.php");
    session_start();
    $dbh = connect();
	//prendo i dati dal form
	
	 $classe=$_POST["classe"];
	 $istituto = $_POST["istituto"];
	
	 
	 // Controllo che i campi siano stati completati
    $errors = [];
    if(empty($_POST['classe'])){
        $errors[] = 1;
		
    }
	if(empty($_POST['istituto'])){
        $errors[] = 2;
		
    }
	
	if(count($errors) == 0)
	{
		$hash=password_hash($POST['password'],PASSWORD_BCRYPT);
	$sql = "INSERT INTO `classi`(`ID`, `Classe`, `IDIstituto`)";
    $sql .= "VALUES ('',?,?)";
	$res=sqlInsert($dbn,$sql,"si",$classe,$istituto);
		
	}
	 $dbh->close();
    echo json_encode($errors);
	
?>