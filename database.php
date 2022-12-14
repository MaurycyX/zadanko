<?php
    $conn = null;
    try{
        $conn = new PDO("sqlite:ksiazki.db");
    }catch(PDOException $e){
        echo "<h1>Błąd w łączeniu z db</h1>";
    }
?>