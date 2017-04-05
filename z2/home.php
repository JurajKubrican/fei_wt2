<?php

session_start();
session_name('session');

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if(empty($_SESSION['user'])){
  header("HTTP/1.1 301 Moved Permanently");
  header("Location: http:/wt.knet.sk/z2");
  die();
  die ('auth fail');
}
?>
<!DOCTYPE html>

<html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="description" content="A fully featured admin theme which can be used to build CRM, CMS, etc.">
  <meta name="author" content="Coderthemes">
  <meta name="google-signin-client_id" content="127627165896-atdl7sljj38036dg8ba02aa33r13dt7j.apps.googleusercontent.com">

  <title>Admin</title>

  <link href="css/bootstrap.min.css" rel="stylesheet" type="text/css" />


</head>
<body>
<script>
  var user = '<?=$_SESSION['user']?>';
</script>

<div class="wrapper-page">
  user id:<?=$_SESSION['user']?>
</div>
<button class="btn logout btn-danger" >LOGOUT</button>
<div>
  <ul>
    <?php
      require_once 'api/controller/JDB.php';

      global $objDb;
     $data = $objDb->getRows('users_history',['id'=>$_SESSION['user']]);
     foreach ($data as $row){
       ?>
       <li><?=$row['time']?></li>

       <?php
     }
    ?>

  </ul>
</div>



<!-- jQuery  -->
<script src="js/jquery.js"></script>
<script src="js/bootstrap.min.js"></script>

<script src="js/app.js"></script>
<script src="https://apis.google.com/js/platform.js" async defer></script>
<div style="display:none" class="g-signin2" data-onsuccess="onSignIn"></div>

</body>
</html>