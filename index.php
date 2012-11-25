<?php 
   session_start(); 
   if ($_SESSION['Missatge'] == '') {
      $_SESSION['Missatge'] ="Escriu usuari i contrasenya o registra't";
   }
   require_once('../classes/classConnexioPDO.php');
?>
<!DOCTYPE html>
<html>
  <head>
    <title>Reserves cinema</title>   <meta charset="utf-8">
    <link href="estils.css" rel="stylesheet">
  </head>
  <body>
   
     <?php
     // print_r($_SESSION);
     
     if (isset($_POST['Validar'])) {
           // echo "Validar";
           $dbo = new connexioPDO(); // Connexió a mySQL per defecte
           $dbo->connectar();
           if ($_POST['user'] !='') {
               if ($dbo->login($_POST['user'], $_POST['password'])) {
                   $_SESSION['Usuari'] = $_POST['user'];
                   $_SESSION['Validat'] = true;
                   $_SESSION['Missatge'] = "Usuari validat";
                   echo "Tria una  pel·lícula: <hr>";
                   $dbo->consultar ("select titol from pelicules;")  ;
                   // El formulari per triar la pel·lícula
                   echo "<form method='POST' action='reserves.php'>";
                     echo $dbo->mostrarConsultaDesplegable('peli');
                     echo "<input type ='submit' name='seleccionar' value='Seleccionar'>";
                   echo "</form>";
               }
               else {
                  echo "NO validat!";
                  $_SESSION['Missatge'] = "Usuari o contrasenya NO vàlids";
               }
           }
      }
      if (isset($_POST['Registrar'])) {
        // echo "Registrar";
        if ($_POST['user'] !='') {
            $dbo = new connexioPDO(); // Connexió a mySQL per defecte
            $dbo->connectar();
            if ($dbo->registrar($_POST['user'], $_POST['password']) ) {
               $_SESSION['Usuari'] = $_POST['user'];
               $_SESSION['Validat'] = true;
               $_SESSION['Missatge'] = "";
               header('Location: reserves.php');
            }
            else {
               // echo "NO registrat!";
               $_SESSION['Missatge'] = "Problemes amb el registre!";
            }
        }
      }
      if ($_SESSION ['altraPeli']) {
           $dbo = new connexioPDO(); // Connexió a mySQL per defecte
           $dbo->connectar();
            echo "Tria una  pel·lícula: <hr>";
            $dbo->consultar ("select titol from pelicules;")  ;
            // El formulari per triar la pel·lícula
            echo "<form method='POST' action='reserves.php'>";
              echo $dbo->mostrarConsultaDesplegable('peli');
              echo "<input type ='submit' name='seleccionar' value='Seleccionar'>";
            echo "</form>";
      }
      
      if (($_POST['user'] == '') && (!$_SESSION ['altraPeli'])){ 
      ?>
     <div id="login">
      <form method="POST">
         <table align="center" width="225" cellspacing="2" cellpadding="2" border="1">
            <tr>
               <td colspan="2" align="center"> <div class='titol'> Sala de cine </div></td>
            </tr>
            <tr>//
               <td ><div class='text'>Usuari:</div></td>
               <td><input type="Text" name="user" size="8" maxlength="50" value='vicent'></td>
            </tr>
            <tr>
               <td ><div class='text'>Contrasenya:</div></td>
               <td><input type="password" name="password" size="8" maxlength="50" value='vicent'></td>
            </tr>
            <tr>
               <td align="center"><input type="Submit" name = "Validar" value="Validar"></td>
               <td align="center"><input type="Submit" name = "Registrar" value="Registrar"></td>
            </tr>
            <tr>
               <td colspan="2"><div class='missatge'><?php echo $_SESSION['Missatge'] ?></div></td>
            </tr>
         </table> 
      </form>
      </div>
   </body> 
   <?php }     ?>