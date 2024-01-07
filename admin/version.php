<?php
// version.php

function getCurrentVersion() {
    $versionFile = __DIR__ . '/../version.txt'; 
    return file_exists($versionFile) ? trim(file_get_contents($versionFile)) : 'Unknown Version';
}

function getGithubFileContent($branch, $filename) {
    $url = "https://raw.githubusercontent.com/w4r3s/410Blog/$branch/$filename";
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
    $content = curl_exec($curl);
    $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
    curl_close($curl);

    if ($httpCode == 200) {
        return trim($content);
    } else {
        return null; // Request failed or file not found
    }
}

function checkForUpdates(&$updateMessage, $currentVersion) {
    $stableVersion = getGithubFileContent('stable', 'version.txt');
    $betaVersion = getGithubFileContent('beta', 'version.txt');
    $updateDetails = '';
    $updateMessage = '';

    if ($stableVersion && version_compare($currentVersion, $stableVersion, '<')) {
        $stableNews = getGithubFileContent('stable', 'news.txt');
        $updateDetails .= "<p style='color: green;'>New stable release available: $stableVersion</p>";
        $updateDetails .= $stableNews ? "<div>$stableNews</div>" : "";
    }

    if ($betaVersion && version_compare($currentVersion, $betaVersion, '<')) {
        $betaNews = getGithubFileContent('beta', 'news.txt');
        $updateDetails .= "<p style='color: red;'>New beta version available: $betaVersion</p>";
        $updateDetails .= $betaNews ? "<div>$betaNews</div>" : "";
    }

    $updateMessage = $updateDetails;
}