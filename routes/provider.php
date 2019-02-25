<?php declare(strict_types=1);

use Slim\Http\Request;
use Slim\Http\Response;

use OAuthClient\Models\OauthProvider;

$app->get("/provider", function (Request $request, Response $response, array $arguments): Response {
      $response = $this->view->render($response, 'provider/new.html', $arguments);
      return $response;
});

// Persist the Oauth Provider Details
$app->post("/provider", function (Request $request, Response $response, array $arguments): Response {
    //TODO: Add validation/Data sanitize, etc
    $parsedBody = $request->getParsedBody();

    $oauthprovider = new OauthProvider();

    //TODO: Parse the input
    $oauthprovider->oauthprovider_key = $parsedBody['oauthprovider_key'];
    $oauthprovider->authorization_uri = $parsedBody['authorization_uri'];
    $oauthprovider->callback_uri = $parsedBody['callback_uri'];
    $oauthprovider->token_uri = $parsedBody['token_uri'];
    $oauthprovider->client_id = $parsedBody['client_id'];
    $oauthprovider->client_secret = $parsedBody['client_secret'];

    //TODO Validate the request...
    // $validation = $this->validator->validate($application->toArray(), $application->getRules());
    //TODO: Return Form with errors
    // if($validation->fails()) {
    //   $this->logger->warning("Invalid POST data sent, not creating", $album->toArray());
    //   return $response->withStatus(400);
    // }


    $oauthprovider->save();

    $location = "provider/{$oauthprovider->oauthprovider_id}";

    //Redirect to location
    return $response->withStatus(302)->withHeader('Location', $location);
});

$app->get("/provider/{id:[0-9]+}", function (Request $request, Response $response, array $arguments): Response {

    $oauthprovider = OauthProvider::find($arguments['id']);

    $arguments['oauthprovider'] = $oauthprovider;

    if (!$oauthprovider) {
        return $response->withStatus(404);
    }

    $response = $this->view->render($response, 'provider/view.html', $arguments);

    return $response;
});
