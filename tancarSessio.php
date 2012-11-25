<?php
session_start();   
echo time();
if (isset($_SESSION['ultimaActivitat']) && (time() - $_SESSION['ultimaActivitat'] > 300)) {
    // La última petició s'ha fet fa més de 5 minuts 
    echo "S'ha acabat!!!";
    session_destroy();   // Eliminem les dades de la sessió
    session_unset();     // Eliminem la sessió
}
$_SESSION['ultimaActivitat'] = time(); // Actualitzem el temps 


?>
