<?php
if ($_GET['q']) {
    // Call set_include_path() as needed to point to your client library.
    require_once 'google-api-php-client/src/Google_Client.php';
    require_once 'google-api-php-client/src/contrib/Google_YouTubeService.php';

    /* Set $DEVELOPER_KEY to the "API key" value from the "Access" tab of the
    Google APIs Console <http://code.google.com/apis/console#access>
    Please ensure that you have enabled the YouTube Data API for your project. */
    $DEVELOPER_KEY = 'AIzaSyDCbgwn1MQJCUL_ulbUW8lltH4sAsg4gDE';

    if (!isset($videos)) {
        $videos = "";
    }

    $client = new Google_Client();
    $client->setDeveloperKey($DEVELOPER_KEY);

    $youtube = new Google_YoutubeService($client);

    try {
        $searchResponse = $youtube->search->listSearch('id,snippet', array(
            'q' => $_GET['q'],
            'maxResults' => 25,
        ));

        $videos = '';
        $channels = '';

    foreach ($searchResponse['items'] as $searchResult) {
      switch ($searchResult['id']['kind']) {
        case 'youtube#video':
          $videos .= sprintf('<li>%s (%s)</li>', $searchResult['snippet']['title'],
            $searchResult['id']['videoId']."<a href=index.php?v=".$searchResult['id']['videoId']." target=_blank>   Videos Relacionados</a>");
          break;
        case 'youtube#channel':
          $channels .= sprintf('<li>%s (%s)</li>', $searchResult['snippet']['title'],
            $searchResult['id']['channelId']);
          break;
       }
     }
     } catch (Google_ServiceException $e) {
        $htmlBody .= sprintf('<p>A service error occurred: <code>%s</code></p>',
            htmlspecialchars($e->getMessage()));
    } catch (Google_Exception $e) {
        $htmlBody .= sprintf('<p>An client error occurred: <code>%s</code></p>',
            htmlspecialchars($e->getMessage()));
    }
}
if ($_GET['v']) {

 // Call set_include_path() as needed to point to your client library.
    require_once 'google-api-php-client/src/Google_Client.php';
    require_once 'google-api-php-client/src/contrib/Google_YouTubeService.php';

    /* Set $DEVELOPER_KEY to the "API key" value from the "Access" tab of the
    Google APIs Console <http://code.google.com/apis/console#access>
    Please ensure that you have enabled the YouTube Data API for your project. */
    $DEVELOPER_KEY = 'AIzaSyDCbgwn1MQJCUL_ulbUW8lltH4sAsg4gDE';

    if (!isset($videos)) {
        $videos = "";
    }

    $client = new Google_Client();
    $client->setDeveloperKey($DEVELOPER_KEY);

    $youtube = new Google_YoutubeService($client);
    
    try {
        $searchResponse = $youtube->search->listSearch('id,snippet', array(
            'relatedToVideoId' => $_GET['v'],
            'maxResults' => 25,
        ));

        $videos = '';
        $channels = '';

    foreach ($searchResponse['items'] as $searchResult) {
      switch ($searchResult['id']['kind']) {
        case 'youtube#video':
          $videos .= sprintf('<li>%s (%s)</li>', $searchResult['snippet']['title'],
            $searchResult['id']['videoId']."<a href=index.php?v=".$searchResult['id']['videoId']." target=_blank>   Videos Relacionados</a>");
          break;
        case 'youtube#channel':
          $channels .= sprintf('<li>%s (%s)</li>', $searchResult['snippet']['title'],
            $searchResult['id']['channelId']);
          break;
       }
     }
     } catch (Google_ServiceException $e) {
        $htmlBody .= sprintf('<p>A service error occurred: <code>%s</code></p>',
            htmlspecialchars($e->getMessage()));
    } catch (Google_Exception $e) {
        $htmlBody .= sprintf('<p>An client error occurred: <code>%s</code></p>',
            htmlspecialchars($e->getMessage()));
    }

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

        <div>
            Search:<input type="text" name="q">
        </div>

        <button>Pesquisar!</button>

    </form>

    <div id="resultado">
        <!-- Resultado da pesquisa -->
        <h3> Resultado </h3>
        <ul><?php echo $videos; ?></ul>
    </div>
    </body>
</html>
