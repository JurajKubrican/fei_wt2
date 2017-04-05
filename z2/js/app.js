/**
 * Created by kubri on 14.3.2017.
 */


var app = (function($,app){
  ('use strict');

  function handleResponse(response){
    if(response.status === "ok"){
      location.assign('home.php');
    }else{
      $('input[name=login],input[name=pass]').css('background-color','red')
    }

  }

  $(document).ready(function(){
    $('#loginForm').submit(function(e){
      e.preventDefault();
      e.stopPropagation();
      var data ={
        login:$('input[name=login]').val(),
        pass:$('input[name=pass]').val(),
        type:$('input[name=type]:checked').val()
      }
      $.post('api/?request=login-' + data.type,data,function(data){
        handleResponse(data);
      })

    })



    $('#registerForm').submit(function(e){
      e.preventDefault();
      e.stopPropagation();

      var data = new FormData('loginForm');
      data.append('login',$('input[name=login]').val())
      data.append('pass',$('input[name=pass]').val())
      data.append('name1',$('input[name=name1]').val())
      data.append('name2',$('input[name=name2]').val())
      data.append('email',$('input[name=email]').val())
      fetch("api/?request=register",{
        method: "POST",
        body: data,
        credentials: "same-origin"
      })
        .then(function(response) {
          return response.json()
            .then(function(json){
              handleResponse(json);
            }.bind(this))
        }.bind(this));
    })

    $('.logout').click(function () {

      var auth2 = gapi.auth2.getAuthInstance();
      auth2.signOut().then(function () {
        console.log('User signed out.');
      });

      fetch("api/?request=logout",{
        credentials: "same-origin"
      })
        .then(function(response) {
          return response.json()
            .then(function(json){
              handleResponse(json);
            }.bind(this))
        }.bind(this));
    })




  })

  function onSignIn(googleUser) {
    var id_token = googleUser.getAuthResponse().id_token;

    var data = new FormData();
    data.append('token',id_token);
    fetch("api/?request=login-google",{
      method: "POST",
      body: data,
      credentials: "same-origin"
    })
      .then(function(response) {
        return response.json()
          .then(function(json){
            if(typeof(user)!=='undefined' && user!=='')
              return;
            handleResponse(json);
          }.bind(this))
      }.bind(this));

  }

  app = {
    onSignIn:onSignIn
  };

  return app;

}($,app))


function onSignIn(e){
  app.onSignIn(e);
}