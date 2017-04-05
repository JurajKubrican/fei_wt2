<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="description" content="A fully featured admin theme which can be used to build CRM, CMS, etc.">
  <meta name="author" content="Coderthemes">
  <meta name="google-signin-client_id" content="127627165896-atdl7sljj38036dg8ba02aa33r13dt7j.apps.googleusercontent.com">


  <title>Feedbacker Admin</title>

  <link href="css/bootstrap.min.css" rel="stylesheet" type="text/css" />


</head>
<body>

<div class="wrapper-page">
  <div class="card-box">
    <div class="panel-heading">
      <div class="red"></div>
      <h3 class="text-center">Prihlásenie</h3>
    </div>


    <div class="panel-body">
      <form class="form-horizontal m-t-20" role="form" method="post" action="" id="loginForm">

        <div class="form-group ">
          <div class="col-xs-12">
            <input class="form-control" type="text" required="" placeholder="E-mail" name="login" value="xkubricanj">
          </div>
        </div>

        <div class="form-group">
          <div class="col-xs-12">
            <input class="form-control" type="password" required="" placeholder="Heslo" name="pass" value="">
          </div>
        </div>

        <div class="form-group">
          <div class="col-xs-12">
            <label>local<input type="radio" name="type" value="local"/></label>
            <label>LDAP<input type="radio" name="type" value="ldap" checked/></label>
          </div>
        </div>

        <!--        <div class="form-group ">-->
<!--          <div class="col-xs-12">-->
<!--            <div class="checkbox checkbox-primary">-->
<!--              <input id="form-control" type="checkbox" name="remember" value="true">-->
<!--              <label for="checkbox-signup">-->
<!--                Zapamätať si ma                            </label>-->
<!--            </div>-->
<!---->
<!--          </div>-->
<!--        </div>-->

        <div class="form-group text-center m-t-40">
          <input name="page" type="hidden" value="" />
          <div class="col-xs-12">
            <button class="btn btn-pink btn-block text-uppercase waves-effect waves-light" type="submit">Prihlásiť</button>
          </div>
        </div>


<!--        b-->
      </form>

    </div>
  </div>

</div>

<!-- jQuery  -->
<script src="js/jquery.js"></script>
<script src="js/bootstrap.min.js"></script>

<script src="js/app.js"></script>

<script src="https://apis.google.com/js/platform.js" async defer></script>
<div class="g-signin2" data-onsuccess="onSignIn"></div>


</body>
</html>