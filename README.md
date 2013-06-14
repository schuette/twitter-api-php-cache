twitter-api-php-cache
=====================

PHP class to get latest tweets and store in cache



## Configure twitter.php

```
// Set your Twitter username
private $screen_name = 'xxxxxx';

// Set the number of tweets to show
private $tweet_count = 2;

// Set the location of the cache folder
private $cache_folder = 'cache';

// Set the cache expiration value (time in seconds)
private $cache_expiration = 3600;

// Set access tokens (you must have a Developer account on Twitter)
private $oauth_access_token = "YOUR_OAUTH_ACCESS_TOKEN";
private $oauth_access_token_secret = "YOUR_OAUTH_ACCESS_TOKEN_SECRET";
private $consumer_key = "YOUR_CONSUMER_KEY";
private $consumer_secret = "YOUR_CONSUMER_SECRET";

```

Don't forgot to create a cache folder and set proper permissions.



## Usage

```

// Include the class file
require_once('twitter.php');

//Get the tweets
$twitter = new Twitter();
$tweets = $twitter->getTweets();


if(is_array($tweets))
{
	echo '<ul class="tweets">';
	foreach($tweets as $tweet) {
		$text = $tweet['text'];
		$text = preg_replace("#(^|[\n ])@([^ \"\t\n\r<]*)#ise", "'\\1<a href=\"http://www.twitter.com/\\2\">@\\2</a>'", $text);
		$text = preg_replace("#(^|[\n ])([\w]+?://[\w]+[^ \"\n\r\t<]*)#ise", "'\\1<a href=\"\\2\">\\2</a>'", $text);
		$text = preg_replace("#(^|[\n ])((www|ftp)\.[^ \"\t\n\r<]*)#ise", "'\\1<a href=\"http://\\2\">\\2</a>'", $text);
		$text = preg_replace('/([^a-zA-Z0-9-_&])#([0-9a-zA-Z_]+)/',"$1<a href=\"http://twitter.com/search?q=%23$2\">#$2</a>",$text);
		echo '<li>'.$text.'</li>';
	}
	echo '</ul>';
}
else
{
	echo '<p>' . $tweets . '</p>';	
}

```

This is just a quick example of how you can format the data in PHP. 
