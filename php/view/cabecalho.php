<div class="navbar-wrapper">
  <div class="container">
    <div class="row">
      <div class="col-md-12">
        <nav class="navbar navbar-inverse" role="navigation">
        <!-- Brand and toggle get grouped for better mobile display -->
        <div class="navbar-header">
          <a class="navbar-brand" href="#">
            <img alt="Brand" src="imagens/logo.jpg" width="10%">
          </a>
        </div>
       
                    
                  <a href='https://www.facebook.com/dialog/oauth?client_id=815200142005202&redirect_uri=https://karinarovani.github.io/uber-meet-front/index.html&scope=public_profile,email'>
                    <input class='btn btn-primary btn-block' type='button' id='sign-in-facebook' value='Facebook'>
                  </a>
                 

                
        <!-- /.navbar-collapse -->
      </nav>
      </div>
    </div>
  </div>
</div>
<script>

var sUsuarioRPC = "../controller/usuario.RPC.php";

function validarDadosLogin() {

  if ( $('#inputUsuario').val() == '' ) {

    alert("Usuário não informado.");
    return false;
  }

  if ( $('#inputSenha').val() == '' ) {

    alert("Senha não informada");
    return false;
  }

  return true;
}

function login() {

  if ( !validarDadosLogin() ) {
    return;
  }

  var oParametros            = {};
      oParametros.sExecucao  = 'loginUsuario';
      oParametros.sUsuario   = $('#inputUsuario').val();
      oParametros.sSenha     = $('#inputSenha').val();
      oParametros.iTipoLogin = 1;

  $.ajax({
    url: sUsuarioRPC,
    data: oParametros,
    type: 'POST',
    success: retornoLogin
  });  
};

function retornoLogin( oResponse ) {

  var oRetorno = $.parseJSON( oResponse );

  if ( oRetorno.lErro ) {

  	alert(oRetorno.sMensagem);
  	return;
  }

  if ( oRetorno.lLogado ) {
  	window.location="index.php";
  }
}

</script>