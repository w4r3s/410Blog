<?php
// version.php

function getCurrentVersion() {
    $versionFile = __DIR__ . '/../version.txt'; 
    return file_exists($versionFile) ? trim(file_get_contents($versionFile)) : '未知版本';
}

function getGithubVersion($branch) {
    $url = "https://raw.githubusercontent.com/w4r3s/410Blog/$branch/version.txt";
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
    $githubVersion = curl_exec($curl);
    $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
    curl_close($curl);

   
    if ($httpCode == 200) {
        return trim($githubVersion);
    } else {
        return null; // Request failed or file not found
    }
}

function checkForUpdates(&$updateMessage, $currentVersion) {
    $stableVersion = getGithubVersion('stable');
    $betaVersion = getGithubVersion('beta');
    $updateMessage = '';

    if ($stableVersion && $stableVersion !== '404: Not Found' && version_compare($currentVersion, $stableVersion, '<')) {
        $updateMessage .= "<p style='color: green;'>New stable releases are available: $stableVersion</p>";
    }
    if ($betaVersion && $betaVersion !== '404: Not Found' && version_compare($currentVersion, $betaVersion, '<')) {
        $updateMessage .= "<p style='color: red;'>New beta versions are available: $betaVersion</p>";
    }
}
