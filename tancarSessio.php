<?php
session_start();   
echo time();
if (isset($_SESSION['ultimaActivitat']) && (time() - $_SESSION['ultimaActivitat'] > 300)) {
    // La �ltima petici� s'ha fet fa m�s de 5 minuts 
    echo "S'ha acabat!!!";
    session_destroy();   // Eliminem les dades de la sessi�
    session_unset();     // Eliminem la sessi�
}
$_SESSION['ultimaActivitat'] = time(); // Actualitzem el temps 


?>
