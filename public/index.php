<?php

require_once 'vendor/autoload.php';

$app = new \Wrybill\Wrybill("./public/audio", "./public/.cache", '');
$files = $app->getAudioFiles();

$playList = [];
foreach ($files as $file) {
    $playList[] = [
        'icon' => null,
        'title' => $file['species_en'],
        'file' => '.' . $file['path'], // audio files are stored "up" one directory...
        'prePlay' => '.' . $file['species_name_audio_file'],
    ];
}

$htmlFileLocation = './html5-audio-player/index.html';
$htmlFile = file_get_contents($htmlFileLocation);
$htmlFile = preg_replace('/playList: \[(.*?)]/s', 'playList: ' . json_encode($playList, JSON_THROW_ON_ERROR), $htmlFile);
file_put_contents($htmlFileLocation, $htmlFile);

echo "File updated!\n";
exit;
