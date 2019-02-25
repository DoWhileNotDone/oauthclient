<?php declare(strict_types=1);

use Slim\Http\Request;
use Slim\Http\Response;

use GuzzleHttp\Psr7\Request as GuzzRequest;
use GuzzleHttp\Psr7\Response as GuzzResponse;

use OAuthClient\Models\OauthProvider;

$app->get("/callback", function (Request $request, Response $response, array $arguments): Response {

    $params = $request->getQueryParams();

    $authorization_code = $params['authorization_code'] ?? null;
    $state = $params['state'] ?? null;

    // TODO: Log and redirect on error
    if ($authorization_code === null) {
        return $response->withStatus(403);
    }

    // Verify the state matches our stored state
    if ($state === null || $_SESSION['state'] !== $state) {
        return $response->withStatus(403);
    }

    //FIXME: Check Provider that was initially used for the request
    $oauthprovider = OauthProvider::findOneByKey(OauthProvider::OAUTHSERVER);

    $params = [
      'grant_type' => 'authorization_code',
      'client_id' => $oauthprovider->client_id,
      'client_secret' => $oauthprovider->client_secret,
      'callback_uri' => $oauthprovider->callback_uri,
      'authorization_code' => $authorization_code,
    ];

    $location = $oauthprovider->token_uri.'?'.http_build_query($params);

    $client = new \GuzzleHttp\Client();
    $guzzle_response = $client->request('GET', $location);

    $response_content = json_decode($guzzle_response->getBody()->getContents(), true);

    //Save Access Token for requests to provider
    $_SESSION['access_token'] = $response_content['access_token'];

    //Redirect to location
    $location = "/";
    return $response->withStatus(302)->withHeader('Location', $location);
});
