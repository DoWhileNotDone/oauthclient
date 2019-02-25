<?php declare(strict_types=1);

use Slim\Http\Request;
use Slim\Http\Response;
use OAuthClient\Models\OauthProvider;

$app->get("/authorize", function (Request $request, Response $response, array $arguments): Response {
      //TODO: We can check if the user already has a valid access token set
      unset($_SESSION['access_token']);
      //TODO: There may be other authorization type configured, but at the moment we assume/use Oauth
      $response = $this->view->render($response, 'authorize/oauth.html', $arguments);
      return $response;
});

//Redirect to Oauth Server with required details to receive Authorization code
$app->post("/authorize", function (Request $request, Response $response, array $arguments): Response {

    unset($_SESSION['access_token']);

    // Generate a random hash and store in the session
    // Used to `compare against the value of same returned from the oauth server`
    $_SESSION['state'] = bin2hex(random_bytes(16));

    //TODO: Allow the provider key to be selected or supplied
    $oauthprovider = OauthProvider::findOneByKey(OauthProvider::OAUTHSERVER);

    $params = [
       'response_type' => 'code',
       'client_id' => $oauthprovider->client_id,
       'callback_uri' => $oauthprovider->callback_uri,
       'scope' => 'any', //FIXME: At the moment, for this instance, we don't have scopes defined
       'state' => $_SESSION['state']
    ];

    //TODO: Save/Log the request

    $location = $oauthprovider->authorization_uri.'?'.http_build_query($params);

    //Redirect user to Oauth Server to authenticate with.
    return $response->withStatus(302)->withHeader('Location', $location);
});
