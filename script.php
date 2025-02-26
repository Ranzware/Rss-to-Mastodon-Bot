<?php

// Mastodon API details
$mastodonInstance = 'https://mastodon.social'; // Replace with your Mastodon instance URL
$accessToken = 'YOUR_MASTODON_ACCESS_TOKEN'; // Replace with your Mastodon access token

// RSS Feed URL
$rssFeedUrl = 'https://example.com/rss-feed'; // Replace with your RSS feed URL

// File to store posted GUIDs
$postedGuidsFile = 'posted_guids.txt';

// Function to fetch RSS feed
function fetchRssFeed($url) {
    $rss = simplexml_load_file($url);
    if ($rss === false) {
        die('Error fetching RSS feed.');
    }
    return $rss;
}

// Function to post to Mastodon
function postToMastodon($status, $mastodonInstance, $accessToken) {
    $url = "{$mastodonInstance}/api/v1/statuses";
    $headers = [
        "Authorization: Bearer {$accessToken}",
        "Content-Type: application/json"
    ];
    $data = json_encode(['status' => $status]);

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    if ($httpCode !== 200) {
        die("Error posting to Mastodon. HTTP Code: {$httpCode}, Response: {$response}");
    }

    curl_close($ch);
    return $response;
}

// Function to read posted GUIDs from file
function readPostedGuids($file) {
    if (!file_exists($file)) {
        return [];
    }
    $guids = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    return $guids;
}

// Function to write posted GUIDs to file
function writePostedGuid($file, $guid) {
    file_put_contents($file, $guid . PHP_EOL, FILE_APPEND);
}

// Function to extract hashtags from a string
function extractHashtags($text) {
    preg_match_all('/#\w+/', $text, $matches);
    return implode(' ', $matches[0]); // Return hashtags as a space-separated string
}

// Fetch the RSS feed
$rss = fetchRssFeed($rssFeedUrl);

// Read already posted GUIDs
$postedGuids = readPostedGuids($postedGuidsFile);

// Loop through the RSS items and post to Mastodon
foreach ($rss->channel->item as $item) {
    $guid = (string)$item->guid;
    $title = (string)$item->title;
    $link = (string)$item->link;
    $description = (string)$item->description;

    // Check if the item has already been posted
    if (in_array($guid, $postedGuids)) {
        echo "Skipping already posted item: {$title}\n";
        continue;
    }

    // Extract hashtags from the title
    $hashtags = extractHashtags($title);

    // Create the status message
    $status = "{$title}\n\n{$description}\n\nRead More: {$link}";
    if (!empty($hashtags)) {
        $status .= "\n\n{$hashtags}"; // Append hashtags if any are found
    }

    // Post to Mastodon
    $response = postToMastodon($status, $mastodonInstance, $accessToken);
    echo "Posted: {$title}\n";

    // Record the GUID of the posted item
    writePostedGuid($postedGuidsFile, $guid);

    // Stop after posting one item
    break;
}

echo "Script completed. One item posted (if available).\n";

?>
