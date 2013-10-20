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

  $channel = $youtube->channels->listChannels('contentDetails', array(
      'mine' => 'true',
  ));

  $playlists = $youtube->playlists->listPlaylists('snippet', array('id' => $channel['items'][0]['contentDetails']['relatedPlaylists']['watchLater']));

  $playlistItemsResponse = $youtube->playlistItems->listPlaylistItems('snippet', array(
        'playlistId' => $playlists['items']['0']['id'],
        'maxResults' => 50
      ));

    foreach ($playlistItemsResponse['items'] as $playlistItem) {
        $htmlBody .= sprintf('<li><a href="http://www.youtube.com/embed/%s"><img src="%s" /></a><a href="?v=%s">%s</a></li>', $playlistItem['snippet']['resourceId']['videoId'], "http://img.youtube.com/vi/" . $playlistItem['snippet']['resourceId']['videoId'] . "/default.jpg", $playlistItem['snippet']['resourceId']['videoId'], $playlistItem['snippet']['title']);
      }

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
    <style>
       body { background-image:url('fundo.jpg');
       background-repeat: no-repeat;
       }
       </style>
  </head>

  <body>

    <form method="GET">


        <div id="botoesuser">
            <ul>
                <div id="botao_conectar">
                    <?php
                    if(isset($authUrl)) {
                        print "<strong><a name='connect' style='background: none'  href='$authUrl'>Connect</a></strong>";
                    } else {
                    print "<strong><a name='logout' style='background: none' href='?logout'>Logout</a></strong>";
                    }
                    ?>
                </div>
            </ul>
        </div>

        <h1> GDG </h1>
        <div id="pesquisa">
            Search:<input type="text" name="q">
            <button>Pesquisar!</button>
        </div>

    </form>

    <div id="resultado">
        <!-- Resultado da pesquisa -->
        <h3> Resultado </h3>
        <ul><?php if(isset($videos)){
               echo $videos;
               }
        ?></ul>
        <h3>Watch later</h3>
        <?php
        echo $htmlBody;
        ?>
    </div>
    </body>
</html>
