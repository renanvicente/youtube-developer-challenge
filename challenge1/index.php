<?php
require_once 'google-api-php-client/src/Google_Client.php';
require_once 'google-api-php-client/src/contrib/Google_YouTubeService.php';
require_once 'google-api-php-client/src/contrib/Google_Oauth2Service.php';
session_start();

$DEVELOPER_KEY = 'AIzaSyDCbgwn1MQJCUL_ulbUW8lltH4sAsg4gDE';

$client = new Google_Client();
$client->setDeveloperKey($DEVELOPER_KEY);
$client->setClientId('212758449592.apps.googleusercontent.com');
$client->setClientSecret('1oH3JuPRBcRH8fzO7Crid6C6');
$client->setRedirectUri('http://linuxextreme.org/youtube/youtube-developer-demos/challenge1/');
$client->setApplicationName("YouTube Developer Examples");
$youtube = new Google_YoutubeService($client);
$oauth2 = new Google_Oauth2Service($client);

if (isset($_GET['code'])) {
  $client->authenticate($_GET['code']);
  $_SESSION['token'] = $client->getAccessToken();
  $redirect = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'];
  header('Location: ' . filter_var($redirect, FILTER_SANITIZE_URL));
  return;
}

if (isset($_SESSION['token'])) {
 $client->setAccessToken($_SESSION['token']);
}

if (isset($_REQUEST['logout'])) {
  unset($_SESSION['token']);
  $client->revokeToken();
}

if ($client->getAccessToken()) {

  $youtubeService = new Google_PlaylistItemsServiceResource($youtube);
  $playlistItems = $youtubeService->listPlaylistItems();

  // The access token may have been updated lazily.
  $_SESSION['token'] = $client->getAccessToken();
} else {
  $authUrl = $client->createAuthUrl();
}

if (isset($_GET['q'])) {
    $searchResponse = $youtube->search->listSearch('id,snippet', array(
        'q'                 => $_GET['q'],
        'maxResults'        => 25,
    ));

    foreach ($searchResponse['items'] as $searchResult) {
        switch ($searchResult['id']['kind']) {
            case 'youtube#video':
                $videos .= sprintf('<li><a href="?v=%s">%s</a></li>', $searchResult['id']['videoId'], $searchResult['snippet']['title']);
                break;
        }
    }
}

if (isset($_GET['v'])) {
    $searchResponse = $youtube->search->listSearch('id,snippet', array(
        'relatedToVideoId'  => $_GET['v'],
        'maxResults'        => 25,
        'type'              => 'video'
    ));

    $videos = sprintf('<iframe width="420" height="315" src="%s" frameborder="0" allowfullscreen></iframe>', "http://www.youtube.com/embed/" . $_GET['v']);

    foreach ($searchResponse['items'] as $searchResult) {
        switch ($searchResult['id']['kind']) {
            case 'youtube#video':
                $videos .= sprintf('<li><a href="?v=%s"><img src="%s" /></a></li>', $searchResult['id']['videoId'], "http://img.youtube.com/vi/" . $searchResult['id']['videoId'] . "/default.jpg");
                break;
        }
    }
}
?>

<!doctype html>
<html>
  <head>
    <title>YouTube Search</title>
    <link rel="stylesheet" href=".\bootstrap.css">
    <link rel="stylesheet" href=".\style.css">
    <link href='http://fonts.googleapis.com/css?family=Maven+Pro:400,500' rel='stylesheet' type='text/css'>

  </head>

  <body>
    <form method="GET">
        <div id="botoesusr">
        <?php
        if(isset($authUrl)) {
            print "<a name='login' href='$authUrl'>Connect Me!</a>";
        } else {
        print "<a name='addwatch' href='?logout'>Logout</a>";
        }
        ?>
        </div>
        <h1> GDG </h1>
        <div id="pesquisa">
            Search:<input type="text" name="q">
            <button>Pesquisar!</button>
        </div>

        <div id="botoesuser">
            <ul>
                <li> <button name='login'> Connect </button> </li>
                <li> <button name="addwatch"> Watch later </button> </li>
            </ul>
        </div>

    </form>

    <div id="resultado">
        <!-- Resultado da pesquisa -->
        <h3> Resultado </h3>
        <ul><?php if(isset($videos)){
               echo $videos;
               }
               echo $playlistItems;
        ?></ul>
    </div>
    </body>
</html>
