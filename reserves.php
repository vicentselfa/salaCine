<?php session_start();
   // Per impedir accessos ‘directes’
   if (!$_SESSION['Validat'])    {
         header("Location: index.php");
   }
   require_once ('../classes/classArxiu.php');
   require_once('../classes/classConnexioPDO.php');
?>
<!DOCTYPE html>
<html> 
  <head>
    <title>Reserves cinema</title>
    <meta charset="utf-8">
    <link href="estils.css" rel="stylesheet">
  </head>
  
<?php
   if (!isset($_SESSION['reserves'])) { // Primera vegada
      $_SESSION['reserves'] = array(); 
   }
   
  $tMaxSessio = 30; // Nombre de segons per sessió
  if (!isset($_SESSION['ultimaActivitat'])) {
      // Comencem a contar el temps de la sessió
      $_SESSION['ultimaActivitat'] = time();
  }   

   // Connexió que utilitzem 
   $dbo = new connexioPDO(); // Connexió a mySQL per defecte
   $dbo->connectar();

   if (isset($_POST['peli'])) { // echo "Venim de triar peli.";
      
      if (isset($_SESSION['sala'])) {
         // echo "Anul·lem les reserves no confirmades";
         $nomSala = "sala" .$_SESSION['sala'] .'.txt'; 
         $sala=new salaCine($nomSala);  
         $reserves = $sala->veureSala();
         foreach ($_SESSION ['reserves'] as $reserva) {
           $fila =  substr($reserva,0,1);
           $col  =  substr($reserva,1,1);
           $reserves [$fila] = substr_replace ( $reserves [$fila] , '0' , $col-1 ,1 );
         }

         $sala->escriureSala($reserves);
         // Esborrar reserves de la sessio
          $_SESSION ['reserves'] = null;
      }
      
      // 1.- Averiguem la sala on es projecta la peli
      $_SESSION['peli'] = $_POST['peli'];
      $sql = "SELECT sala FROM `pelicules` WHERE titol='" .$_SESSION['peli'] ."'";
      // echo "sql: " .$sql;
      $dbo->consultar ($sql);
      $sala = $dbo->getCamp('sala');
      $_SESSION['sala'] = $sala;
      $_SESSION['Missatge'] = "Fes clic sobre les butaques que t'interessen.";
      // 2.- Calculem els punts que té:
      $sql = "SELECT puntsPelis, puntsRoses FROM `usuaris` WHERE username='" .$_SESSION['Usuari'] ."'";
      //echo "sql: " .$sql;
      $dbo->consultar ($sql);
      $puntsPelis = $dbo->getCamp('puntsPelis');
      $dbo->consultar ($sql);
      $puntsRoses = $dbo->getCamp('puntsRoses');
      // echo "<hr>" .$puntsPelis ." -- "  .$puntsRoses;
      $_SESSION ['premi'] = "Tens " .$puntsPelis ." punts!!";
      // 3.- Mirem si passen de 50 i 100 respectivament
      if ($puntsRoses > 50) {
         $puntsRoses = $puntsRoses - 50;
         $_SESSION ['premi'] .= '<br>Et regalem UNA bossa de palometes!';
      }
      if ($puntsPelis > 100) {
         $puntsPelis = $puntsPelis - 100;
         $_SESSION ['premi'] .= '<br>Et regalem UNA entrada!';
      }
      // Actualitzem els punts en la taula
      $sql = "UPDATE usuaris SET puntsPelis = " .$puntsPelis ." WHERE username = '" .$_SESSION['Usuari'] ."';";
      $sql .="UPDATE usuaris SET puntsRoses = " .$puntsRoses ." WHERE username = '" .$_SESSION['Usuari'] ."';";
//      echo "sql: " .$sql;
      $dbo->consultar ($sql);
   }
   
   // echo "<pre>Get: "; print_r($_GET) ."</pre>";
   $nomSala = "sala" .$_SESSION['sala'] .'.txt'; 
   $sala=new salaCine($nomSala);  

  // echo "Temps remanent: " .(time() - $_SESSION['ultimaActivitat']);
  // Controlem el temps que dura la sessio
  // echo "Temps màxim: " .(time() - $_SESSION['ultimaActivitat']);
  if ( (isset($_SESSION['ultimaActivitat'])) && ((time() - $_SESSION['ultimaActivitat'])  > $tMaxSessio ) ) {

     // Anul·lem les reserves no confirmades
     $reserves = $sala->veureSala();
     foreach ($_SESSION ['reserves'] as $reserva) {
        $fila =  substr($reserva,0,1);
        $col  =  substr($reserva,1,1);
        $reserves [$fila] = substr_replace ( $reserves [$fila] , '0' , $col-1 ,1 );
     }
     $sala->escriureSala($reserves);
     
     // echo " Anul·lem les variables de sessió";
     session_unset();
     // Recuperem les variables de sessió que necessitem
     $_SESSION['Missatge'] = "La sessió ha caducat!";
     $_SESSION['Validat'] = false;
     $_POST['Validar'] = true;
     
     unset ($_GET['fila']); // Per anul·lar la última selecció feta fora de temps
     unset ($_GET['col']); // Per anul·lar la última selecció feta fora de temps
     // Enviem a la pàgina de login
     header("Location: index.php");
  }
   
   
//   echo "<hr>"; 
//   echo "<pre>"; print_r($_SESSION);
//   echo "<pre>"; print_r($_GET);

   // print_r($_SESSION);

   

      
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
       $punts = 0; 
       foreach ($_SESSION ['reserves'] as $reserva) {
          $fila =  substr($reserva,0,1);
          $col  =  substr($reserva,1,1);
          $entrades .= "Fila: " .$fila ." -- Columna: " .$col ."<br>";
          $punts += 10;
       }
       // Esborrar reserves de la sessio
       $_SESSION ['reserves'] = null;
       // Sumem els punts 
       $sql = "UPDATE usuaris SET puntsPelis = puntsPelis + " .$punts ." WHERE username = '" .$_SESSION['Usuari'] ."';";
       $sql .="UPDATE usuaris SET puntsRoses = puntsRoses + " .$punts ." WHERE username = '" .$_SESSION['Usuari'] ."';";
//       echo "sql: " .$sql;
       $dbo->consultar ($sql);
       
       
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
   
   if (isset($_GET['altraPeli'])){ 
         $_SESSION ['altraPeli'] = true;
         header('Location: index.php');
 
   }

     
   // Creem el formulari amb les dades de l'arxiu
   $reserves = $sala->veureSala();
   $files =$sala->getFiles(); $cols = $sala->getCols();
   $butaques = "<form name='cine' method='GET' action='reserves.php'>";
   $butaques.= "<table border='1' align='center' >
      <tr><th colspan = '" .$cols ."'> <div class='missatge'> Benvingut:   <div class='text'>" .$_SESSION['Usuari'] ."</th></tr>" 
    ."<tr><th colspan = '" .$cols ."'> <div class='missatge'> Pel·lícula:  <div class='text'>" .$_SESSION['peli'] ."</th></tr>"   
    ."<tr><th colspan = '" .$cols ."'> <div class='missatge'> Sala:        <div class='text'>" .$_SESSION['sala'] ."</th></tr>"           
    ."<tr><th colspan = '" .$cols ."'> <div class='missatge'> Punts:       <div class='text'>" .$_SESSION['premi'] ."</th></tr>";           
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
   $butaques .= "<tr><td colspan = '" .$cols ."'><input type = 'submit' name='altraPeli' value='Seleccionar una altra pel·lícula' style='width:100%'> </td></tr>";
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