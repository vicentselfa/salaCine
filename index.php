<?php 
   session_start(); 
   if ($_SESSION['Missatge']) {
      $_SESSION['Missatge'] ="Escriu usuari i contrasenya o registra't";
   }
   require_once('classConnexioPDO.php');
?>
<!DOCTYPE html>
<html>
  <head>
    <title>Reserves cinema</title>   <meta charset="utf-8">
    <style TYPE="text/css"> 
       .titol { font: bold 14pt verdana, sans-serif; color: navy; text-align:center;}
       .text {font: 12pt verdana, sans-serif; color: navy; text-align:center;}
       .missatge {font: 10pt verdana, sans-serif; color: green; text-align:center;}
    </style>
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
                   $_SESSION['Usuari'] = $Usuari;
                   $_SESSION['Validat'] = true;
                   $_SESSION['Missatge'] = "";
                   header('Location: reserves.php');
               }
               else {
                  echo "NO validat!";
                  $_SESSION['Missatge'] = "Usuari o contrasenya NO vàlids";
               }
           }
      }
      else {
         if (isset($_POST['Registrar'])) {
           // echo "Registrar";
           if ($_POST['user'] !='') {
               $dbo = new connexioPDO(); // Connexió a mySQL per defecte
               $dbo->connectar();
               if ($dbo->registrar($_POST['user'], $_POST['password']) ) {
                  $_SESSION['Usuari'] = $Usuari;
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
      } 
      ?>
      <form method="POST">
         <table align="center" width="225" cellspacing="2" cellpadding="2" border="1">
            <tr>
               <td colspan="2" align="center"> <div class='titol'> Sala de cine </div></td>
            </tr>
            <tr>
               <td ><div class='text'>Usuari:</div></td>
               <td><input type="Text" name="user" size="8" maxlength="50"></td>
            </tr>
            <tr>
               <td ><div class='text'>Contrasenya:</div></td>
               <td><input type="password" name="password" size="8" maxlength="50"></td>
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
   </body> 
    