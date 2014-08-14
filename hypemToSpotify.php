<?php
 
if ($argc !== 2)
{
	die("Usage: php {$argv[0]} hypem_user_name\n\n");
}
 
$user = $argv[1];
$page = 1;
$all_songs = array();
 
do 
{
	$json = @file_get_contents("http://hypem.com/playlist/loved/$user/json/$page/data.js");
	$object = json_decode($json, true);
 
	$page_songs = array();
 
	$index = 0;
	while (isset($object[$index]))
	{
		$page_songs[] = $object[$index]['artist'] . " " . $object[$index]['title'];
		$index++;
	}
 
	$page++;
	$all_songs = array_merge($all_songs, $page_songs);
} while(count($page_songs));
 
$spotify_hrefs = array();
 
foreach ($all_songs as $song)
{
	$json = @file_get_contents("http://ws.spotify.com/search/1/track.json?q=".urlencode($song));
	$object = json_decode($json, true);
 
	if (empty($object["info"]["num_results"]))
	{
		echo "$song not found.\n";
		continue;
	}
 
	$spotify_hrefs[] = $object["tracks"][0]["href"];
}
 
echo "\nFound " . count($spotify_hrefs) . " songs out of " . count($all_songs) . ".\n\n";
echo "1. Select a playlist in Spotify\n2. Copy and paste the following:\n\n";
echo implode("\n", $spotify_hrefs);
echo "\n\n";
