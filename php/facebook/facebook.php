<?php
require_once( 'model/Usuario.model.php');
require_once( 'config/conecta.php');

if( $_SERVER['REQUEST_METHOD'] == 'GET' && isset( $_GET['code'] ) ) {

  // Informe o seu App ID abaixo
  $appId = '828770590531960';

  // Digite o App Secret do seu aplicativo abaixo:
  $appSecret = '185ad7d553e70bb09b1a141c892e6d2f';

  // Url informada no campo "Site URL"
  $redirectUri = urlencode('http://locaisseguros.com.br/beta');

  // Obtém o código da query string
  $code = $_GET['code'];

  // Monta a url para obter o token de acesso e assim obter os dados do usuário
  $token_url  = "https://graph.facebook.com/oauth/access_token?";
  $token_url .= "client_id=" . $appId . "&redirect_uri=" . $redirectUri;
  $token_url .= "&client_secret=" . $appSecret . "&code=" . $code;

  //pega os dados
  $response = @file_get_contents($token_url);

  if( $response ) {

    $params = null;
    parse_str($response, $params);

    if( isset( $params['access_token'] ) && $params['access_token'] ) {

      $graph_url  = "https://graph.facebook.com/me?access_token=";
      $graph_url .= $params['access_token'];
      $user       = json_decode(file_get_contents($graph_url));

      // nesse IF verificamos se veio os dados corretamente
      if( isset( $user->email ) && $user->email ) {

        /*
         *Apartir daqui, você já tem acesso aos dados usuario, podendo armazená-los
         *em sessão, cookie ou já pode inserir em seu banco de dados para efetuar
         *autenticação.
         *No meu caso, solicitei todos os dados abaixo e guardei em sessões.
         */

        $_SESSION['email']         = $user->email;
        $_SESSION['nome']          = $user->name;
        $_SESSION['localizacao']   = $user->locale;
        $_SESSION['uid_facebook']  = $user->id;
        $_SESSION['link_facebook'] = $user->link;

        $sQueryValidaId = "SELECT usuario FROM usuariofacebook where id = {$user->id}";
        $rsValidaId     = $oConexao->query( $sQueryValidaId );

        if ( !$rsValidaId ) {

          $oConexao->close();
          throw new Exception("Erro ao verificar o ID do Facebook.");
        }

        if ( $rsValidaId->num_rows > 0 ) {

          $oDadosUsuario = $rsValidaId->fetch_object();

          $oUsuario      = new Usuario( $oDadosUsuario->usuario );

          $_SESSION['lLogado']     = true;
          $_SESSION['sNome']       = $oUsuario->getNome();
          $_SESSION['iSequencial'] = $oUsuario->getSequencial();
          $_SESSION['sEmail']      = $oUsuario->getEmail();
          $_SESSION['sUsuario']    = $oUsuario->getLogin();

          if( $oUsuario->ultimoLocalPesquisado() != null ) {
            $_SESSION['iUltimoEstabelecimento'] = $oUsuario->ultimoLocalPesquisado();
          }
        } else {

          $aStringEmail = explode( '@', $user->email );
          $oUsuario     = new Usuario();

          $oUsuario->setNome( $user->name );
          $oUsuario->setEmail( $user->email );
          $oUsuario->setLogin( $aStringEmail[0] );
          $oUsuario->setSenha( $aStringEmail[0] );
          $oUsuario->salvar();
          $oUsuario->salvarDadosFacebook( $user );
        }

        header( "location: index.php" );
      }
    } else {

      echo "Erro de conexão com Facebook";
      exit(0);
    }
  } else {

    echo "Erro de conexão com Facebook";
    exit(0);
  }
} else if( $_SERVER['REQUEST_METHOD'] == 'GET' && isset( $_GET['error'] ) ) {
  echo 'Permissão não concedida';
}