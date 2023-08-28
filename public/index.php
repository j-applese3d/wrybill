<?php

require_once 'vendor/autoload.php';

$app = new \Wrybill\Wrybill("./public/audio", "./public/.cache", '');
$files = json_encode($app->getAudioFiles(), JSON_THROW_ON_ERROR);

?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Wrybill</title>
    <script src="js/App.js"></script>
</head>
<body>

<div id="content"></div>

<script>
    const app = new App();
    app.initialize(<?= $files ?>, document.getElementById('content'));
</script>

</body>
</html>