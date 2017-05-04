<?php
session_start();
require 'twitteroauth-master/autoload.php';
use Abraham\TwitterOAuth\TwitterOAuth;

define('CONSUMER_KEY', '0er9dV2nNfqAWPLfRwxgMYgMa');
define('CONSUMER_SECRET', 'VwCYznPi1DWGEdQdQBQ9h4sTAbzHRndsTNW79L1ZStItlEwYfM');
define('OAUTH_CALLBACK', 'http://127.0.0.1/twitter/index.php');

if (!empty($_POST['importVal'])) {
    $_SESSION['importVal'] = $_POST['importVal'];
}

if (!isset($_SESSION['access_token'])) {
    $connection = new TwitterOAuth(CONSUMER_KEY, CONSUMER_SECRET);
    $request_token = $connection->oauth('oauth/request_token', array('oauth_callback' => OAUTH_CALLBACK));
    $url = $connection->url('oauth/authorize', array('oauth_token' => $request_token['oauth_token']));
    if (!empty($_POST)) {
        header("Location: $url");
    }
    if (isset($_REQUEST['oauth_verifier'], $_REQUEST['oauth_token'])) {
        $connection = new TwitterOAuth(CONSUMER_KEY, CONSUMER_SECRET, $_REQUEST['oauth_token'], $request_token['oauth_token_secret']);
        $access_token = $connection->oauth("oauth/access_token", array("oauth_verifier" => $_REQUEST['oauth_verifier']));
        $_SESSION['access_token'] = $access_token;
        header('Location: ./');
    }
} else {
    $access_token = $_SESSION['access_token'];

    $connection = new TwitterOAuth(CONSUMER_KEY, CONSUMER_SECRET, $access_token['oauth_token'], $access_token['oauth_token_secret']);
    $user = $connection->get("account/verify_credentials");
    $new_status = $connection->post("statuses/update", ['status' => $_SESSION['importVal']]);
    unset($_SESSION['importVal']);
    echo 'status sucsesfuly updated';
    die;
}

?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>
<body>
<form method="post">
    <input name="importVal">
    <button type="submit"> go!</button>
</form>
</body>
</html>
