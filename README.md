twitter-api-php-cache
=====================

PHP class to get latest tweets and store in cache. Compatible with version 1.1 of the Twitter API. Sign up for a Developer account on Twitter to get you own access tokens (required.) https://dev.twitter.com/



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

		// twitter handle
		$text = preg_replace_callback(
			"~(?is)(^|[\n ])@([^ \"\t\n\r<]*)~",
			function($m) { return $m[1].'<a href="https://twitter.com/'.$m[2].'">@'.$m[2].'</a>'; },
			$text
		);

		// urls
		$text = preg_replace_callback(
			"~(?is)(^|[\n ])([\w]+?://[\w]+[^ \"\n\r\t<]*)~is",
			function($m) { return $m[1].'<a href="'.$m[2].'">'.$m[2].'</a>'; },
			$text
		);

		// hashtag
		$text = preg_replace_callback(
			"~([^a-zA-Z0-9-_&])#([0-9a-zA-Z_]+)~",
			function($m) { return $m[1].'<a href="https://twitter.com/hashtag/'.$m[2].'?src=hash">#'.$m[2].'</a>'; },
			$text
		);

		// cashtag
		$text = preg_replace_callback(
			"~([^a-zA-Z0-9-_&])\\$([a-zA-Z]{2,4})~",
			function($m) { return $m[1].'<a href="https://twitter.com/search?q=%24'.$m[2].'&src=ctag">$'.$m[2].'</a>'; },
			$text
		);
		
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


You can also get the tweet date. To use, you need: `$tweet['created']` but it is unformatted. 

Here's an example of how you could format it:

```
$diff = time_passed(strtotime($tweet['created']), strtotime('now'));
$units = 0;
$created_at = array();
foreach($diff as $unit => $value)
{
	if($value != 0 && $units < 1)
	{
		if($value === 1)
		{
			#let's remove the plural "s"
			$unit = substr($unit, 0, -1);
		}
		$created_at[]= $value . ' ' .$unit;
		++$units;		
	}
}
$created_at = implode(', ',$created_at);
$created_at .= ' ago';
```

Thanks!