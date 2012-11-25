<?php session_start();
   require_once ('classArxiu.php');
?>
<!DOCTYPE html>
<html>
  <head>
    <title>Reserves cinema</title>
    <meta charset="utf-8">
    <style TYPE="text/css"> 
      .sala {font: bold 14pt verdana, sans-serif; color: red; text-align:center} 
      .entrades {font: bold 10pt verdana, sans-serif; color: navy; text-align:center}
    </style>
  </head>


<?php
   if (!isset($_SESSION['reserves'])) { // Primera vegada
      $_SESSION['reserves'] = array(); 
   }
   
   $sala=new salaCine('sala1sessio1.txt');   
      
   if (isset($_GET['fila'])){ //Reservar una butaca
     $fila =  $_GET['fila'];      $col  =  $_GET['col'];
     // Creem acc�s exclusiu a l'arxiu mentres reservem!!!
     $reserves = $sala->veureSala();  // Ac� obrim l'arxiu !!!
     $reserves [$fila] = substr_replace ( $reserves [$fila] , '1' , $col-1 ,1 );
     $sala->escriureSala($reserves);  // Ac� tanquem l'arxiu !!!
     // Guardar les reserves d'esta sessi�
     $numReserves = count($_SESSION ['reserves']);
     $_SESSION ['reserves'][$numReserves] = $fila.$col; 
   }
      
   if (isset($_GET['Confirmar'])){ //Confirmar reserves => 2
       $reserves = $sala->veureSala();
       foreach ($_SESSION ['reserves'] as $reserva) {
          $fila =  substr($reserva,0,1);
          $col  =  substr($reserva,1,1);
          $reserves [$fila] = substr_replace ( $reserves [$fila] , '2' , $col-1 ,1 );
       }
       $sala->escriureSala($reserves);
       $entrades = "Entrades comprades: <hr>";
       foreach ($_SESSION ['reserves'] as $reserva) {
          $fila =  substr($reserva,0,1);
          $col  =  substr($reserva,1,1);
          $entrades .= "Fila: " .$fila ." -- Columna: " .$col ."<br>";
       }
       // Esborrar reserves de la sessio
       session_unset();       
   }
     
   if (isset($_GET['Anular'])){ //Anul�lar reserves => 0
         $reserves = $sala->veureSala();
         foreach ($_SESSION ['reserves'] as $reserva) {
            $fila =  substr($reserva,0,1);
            $col  =  substr($reserva,1,1);
            $reserves [$fila] = substr_replace ( $reserves [$fila] , '0' , $col-1 ,1 );
         }
         $sala->escriureSala($reserves);
         // Esborrar reserves de la sessio
         session_unset();       
   }
   
   if (isset($_GET['novaSessio'])){ //Totes les butaques a 0
         $sala->novaSessio(3,4);
         // Esborrar reserves de la sessio
         session_unset();       
   }

      
   // Creem el formulari amb les dades de l'arxiu
   $reserves = $sala->veureSala();
   $files =$sala->getFiles(); $cols = $sala->getCols();
   $butaques = "<form name='cine' method='GET' action='reserves.php'>";
   $butaques.= "<table border='1' align='center' ><tr><th colspan = '" .$cols ."' class='sala'>La pantalla</th></tr>";
   for ($i= 1; $i<=$files; $i++) {
      $butaques .= "<tr>";      
      for ($j= 1; $j<=$cols; $j++) {
         // Llegir estat butaca
         $reserva = substr ($reserves [$i], $j-1, 1);
         // Dibuixar-la
         if ($reserva!='0') {// Reservat o venut
            $enllas = "#";
            if ($reserva!=='1') { // reservat 
               $imatge = "<img src='imatges/confirmat.png'>";
            }
            else $imatge = "<img src='imatges/reservat.png'>";
         }
         else { // NO reservat
            $enllas = "reserves.php?fila=" .$i ."&col=" .$j;
            $imatge = "<img src='imatges/lliure.png'>";
         }
         $butaques .= "<td align='center'><a href='" .$enllas ."'>" .$imatge ."</a></td>";
      }
      $butaques .= "/<tr>";
   }
   $butaques .= "<tr><td colspan = '" .$cols ."'><input type = 'submit' name='Confirmar' value='Confirmar reserves ' style='width:100%'> </th></tr>";
   $butaques .= "<tr><td colspan = '" .$cols ."'><input type = 'submit' name='Anular' value='Anul·lar reserves sense confirmar' style='width:100%'> </th></tr>";
   $butaques .= "<tr><td colspan = '" .$cols ."'><input type = 'submit' name='novaSessio' value='Nova sessio de cine' style='width:100%'> </th></tr>";
   $butaques .= "<tr><td colspan = '" .$cols ."' class='entrades'>" .$entrades ."</th></tr>";   
   $butaques .= "</table>";
   $butaques .= "</form>";
   echo $butaques; 

   /*
      print_r ($reserves); 
      echo "Sessio: <hr>";
      print_r($_SESSION);
      echo "<hr>GET: <hr>";
      print_r($_GET);
   */ 
   
?>