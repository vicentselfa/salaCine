<?php session_start();
   require_once ('../classes/classArxiu.php');
   require_once('../classes/classConnexioPDO.php');

   // Per impedir accessos ‘directes’
   if (!$_SESSION['Validat'])    {
         header("Location: index.php");
   }
     
   if (isset($_POST['peli'])) { // Venim de triar peli
      $_SESSION['peli'] = $_POST['peli'];
      $dbo = new connexioPDO(); // Connexió a mySQL per defecte
      $dbo->connectar();
      $sql = "SELECT sala FROM `pelicules` WHERE titol='" .$_SESSION['peli'] ."'";
      // echo "sql: " .$sql;
      $dbo->consultar ($sql);
      $sala = $dbo->getCamp('sala');
      $_SESSION['sala'] = $sala;
      $_SESSION['Missatge'] = "Fes clic sobre les butaques que t'interessen.";
   }
   
//   echo "<pre>"; print_r($_SESSION);
//   echo "<pre>"; print_r($_GET);
?>
<!DOCTYPE html>
<html>
  <head>
    <title>Reserves cinema</title>
    <meta charset="utf-8">
    <link href="estils.css" rel="stylesheet">
  </head>
sala

<?php
   // print_r($_SESSION);
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
       // session_unset(); 
       $_SESSION ['reserves'] = null;
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
         // session_unset();   
         $_SESSION ['reserves'] = null;
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
   $butaques.= "<table border='1' align='center' >
      <tr><th colspan = '" .$cols ."'> <div class='missatge'> Benvingut:   <div class='text'>" .$_SESSION['Usuari'] ."</th></tr>" 
    ."<tr><th colspan = '" .$cols ."'> <div class='missatge'> Pel·lícula:  <div class='text'>" .$_SESSION['peli'] ."</th></tr>"
    ."<tr><th colspan = '" .$cols ."'> <div class='missatge'> Sala:        <div class='text'>" .$_SESSION['sala'] ."</th></tr>";           
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
   $butaques .= "<tr><td colspan = '" .$cols ."'><input type = 'submit' name='Confirmar' value='Pagar i confirmar reserves ' style='width:100%'> </td></tr>";
   $butaques .= "<tr><td colspan = '" .$cols ."'><input type = 'submit' name='Anular' value='Anul·lar reserves sense confirmar' style='width:100%'> </td></tr>";
   // $butaques .= "<tr><td colspan = '" .$cols ."'><input type = 'submit' name='novaSessio' value='Nova sessio de cine' style='width:100%'> </td></tr>";
   $butaques .= "<tr><td colspan = '" .$cols ."'><div class='missatge'>" .$_SESSION['Missatge'] ."</td></tr>";
   $butaques .= "<tr><td colspan = '" .$cols ."' class='entrades'>" .$entrades ."</td></tr>";  
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