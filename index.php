<?php //Remember to stay alert

namespace ISAudit;

require __DIR__ . '/vendor/autoload.php';

$infusionsoft = new \Infusionsoft\Infusionsoft(array(
    'clientId' => 'xxxx',
    'clientSecret' => 'xxxx',
    'redirectUri' => 'http://localhost:8888',
));


// If the serialized token is available in the session storage, we tell the SDK
// to use that token for subsequent requests.
if (isset($_SESSION['token'])) {
    $infusionsoft->setToken(unserialize($_SESSION['token']));
}

// If we are returning from Infusionsoft we need to exchange the code for an
// access token.
if (isset($_GET['code']) and !$infusionsoft->getToken()) {
    $accessToken = $infusionsoft->requestAccessToken($_GET['code']);

    $token = new Token();

    $token->update_tokens_in_database($accessToken);
}


if ($infusionsoft->getToken()) {
    print_r($infusionsoft->getToken());
} else {
    echo '<a href="' . $infusionsoft->getAuthorizationUrl() . '">Click here to authorize</a>';
}
