
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="description" content="A fully featured admin theme which can be used to build CRM, CMS, etc.">
  <meta name="author" content="Coderthemes">


  <title>Feedbacker Admin</title>

  <link href="css/bootstrap.min.css" rel="stylesheet" type="text/css" />


</head>
<body>

<div class="wrapper-page">
  <div class="card-box">
    <div class="panel-heading">
      <div class="red"></div>
      <h3 class="text-center">Regsitrácia</h3>
    </div>


    <div class="panel-body">
      <form class="form-horizontal m-t-20" role="form" method="post" action="" id="registerForm">

        <div class="form-group ">
          <div class="col-xs-12">
            <input class="form-control" type="text" required="" placeholder="Meno" name="name1">
          </div>
        </div>

        <div class="form-group ">
          <div class="col-xs-12">
            <input class="form-control" type="text" required="" placeholder="Priezvisko" name="name2">
          </div>
        </div>

        <div class="form-group ">
          <div class="col-xs-12">
            <input class="form-control" type="text" required="" placeholder="E-mail" name="email">
          </div>
        </div>

        <div class="form-group ">
          <div class="col-xs-12">
            <input class="form-control" type="text" required="" placeholder="login" name="login">
          </div>
        </div>

        <div class="form-group">
          <div class="col-xs-12">
            <input class="form-control" type="password" required="" placeholder="Heslo" name="pass">
          </div>
        </div>

        <div class="form-group text-center m-t-40">
          <input name="page" type="hidden" value="" />
          <div class="col-xs-12">
            <button class="btn btn-pink btn-block text-uppercase waves-effect waves-light" type="submit" name="submit">Prihlásiť</button>
          </div>
        </div>

      </form>

    </div>
  </div>

</div>

<!-- jQuery  -->
<script src="js/jquery.js"></script>
<script src="js/bootstrap.min.js"></script>

<script src="js/app.js"></script>

</body>
</html>