<?php
require_once 'google-api-php-client/src/Google_Client.php';
require_once 'google-api-php-client/src/contrib/Google_YouTubeService.php';

$DEVELOPER_KEY = 'AIzaSyDCbgwn1MQJCUL_ulbUW8lltH4sAsg4gDE';

$client = new Google_Client();
$client->setDeveloperKey($DEVELOPER_KEY);
$youtube = new Google_YoutubeService($client);

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

    foreach ($searchResponse['items'] as $searchResult) {
        switch ($searchResult['id']['kind']) {
            case 'youtube#video':
                $videos .= sprintf('<li><a href="?f=%s"><img src="%s" /></a></li>', $searchResult['id']['videoId'], "http://img.youtube.com/vi/" . $searchResult['id']['videoId'] . "/default.jpg");
                break;
        }
    }
}

if (isset($_GET['f'])) {
    $videos = sprintf('<iframe width="420" height="315" src="%s" frameborder="0" allowfullscreen></iframe>', "//www.youtube.com/embed/" . $searchResult['id']['videoId']);
}
?>

<!doctype html>
<html>
  <head>
    <title>YouTube Search</title>

    <link rel="stylesheet" href=".\style.css">
    <link href='http://fonts.googleapis.com/css?family=Maven+Pro:400,500' rel='stylesheet' type='text/css'>

  </head>

  <body>
    <form method="GET">
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
    </div>
    </body>
</html>
