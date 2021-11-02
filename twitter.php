<?php

/**
 * twitter-api-php-cache
 * 
 * PHP class for Version 1.1 of the Twitter API
 * 
 * PHP version 5
 *
 * @author   Jeffrey Schuette <jeffschuette@me.com>
 * @license  http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link     http://github.com/schuette/twitter-api-php-cache
 * @version  1.0.2
 */

class Twitter {


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




	public function getTweets() {

		// Twitter resource URL
		$url = "https://api.twitter.com/1.1/statuses/user_timeline.json";

		$oauth = array(
			'screen_name' => $this->screen_name,
			'count' => $this->tweet_count,
			'oauth_consumer_key' => $this->consumer_key,
			'oauth_nonce' => time(),
			'oauth_signature_method' => 'HMAC-SHA1',
			'oauth_token' => $this->oauth_access_token,
			'oauth_timestamp' => time(),
			'oauth_version' => '1.0'
		);

		$base_info = $this->buildBaseString($url, 'GET', $oauth);
		$composite_key = rawurlencode($this->consumer_secret) . '&' . rawurlencode($this->oauth_access_token_secret);
		$oauth_signature = base64_encode(hash_hmac('sha1', $base_info, $composite_key, true));
		$oauth['oauth_signature'] = $oauth_signature;


		$header = array($this->buildAuthorizationHeader($oauth), 'Expect:');
		$options = array( CURLOPT_HTTPHEADER => $header,
			CURLOPT_HEADER => false,
			CURLOPT_URL => $url .'?screen_name='.$this->screen_name.'&count='.$this->tweet_count.'&exclude_replies=true',
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_SSL_VERIFYPEER => false);

		// Find the document root
		$doc_root = $_SERVER['DOCUMENT_ROOT'];
		if (substr($doc_root, -1) != '/')
			$doc_root = $doc_root.'/';

		// Define the full cache file path
		$cache_file = $doc_root.$this->cache_folder . '/' . $this->screen_name . '.json';

		// Check if cache file doesn't exist or needs to be updated
		if(!is_file($cache_file) || (date("U")-date("U", filemtime($cache_file))) > $this->cache_expiration) {
			$feed = curl_init();
			curl_setopt_array($feed, $options);
			$json = curl_exec($feed);
			curl_close($feed);
					
			if(is_string($value = $this->storeCache($json, $cache_file, $this->screen_name))) return $value;
		}

		// Return the cache file
		return $this->readCache($cache_file, $this->screen_name);
	}

	private function readCache($cache_file = FALSE, $screen_name = FALSE) {

		$twitter_data = json_decode(file_get_contents($cache_file));

		// handle Twitter rate limit error
		if (isset($twitter_data->errors)) return '';

		foreach ($twitter_data as $tweet) {
			if (!empty($tweet)) {
				$tweets_array[] = array(
					'created' => (string)$tweet->created_at,
					'text' => (string)$tweet->text,
					'id' => (string)$tweet->id,
					'link' => (string)'https://twitter.com/'.$screen_name.'/status/'.$tweet->id
				);
			}
		}

		// If tweets, return amount specified above, otherwise show a message
		return (is_array($tweets_array))? array_slice($tweets_array, 0, $this->tweet_count) : 'There are no tweets for this account.';			

	}

	private function storeCache($json = FALSE, $cache_file = FALSE, $screen_name = FALSE) {

		// Does cache folder exist?
		if (is_dir($this->cache_folder)) {

			// Is the directory writable?
			if (is_writable($this->cache_folder)) {
				$twitter_data = json_decode($json);
				file_put_contents($cache_file,json_encode($twitter_data));
				return TRUE;
			}
			else
				return 'Error, the folder you have specified is not writable.';
		}
		else
			return 'Error, the folder you have specified does not exist.';	

	}

	private function buildBaseString($baseURI, $method, $params) {

		$r = array();
		ksort($params);
		foreach($params as $key=>$value) {
			$r[] = "$key=" . rawurlencode($value);
		}

		return $method."&" . rawurlencode($baseURI) . '&' . rawurlencode(implode('&', $r));
	}

	private function buildAuthorizationHeader($oauth) {
		$r = 'Authorization: OAuth ';
		$values = array();
		foreach($oauth as $key=>$value)
			$values[] = "$key=\"" . rawurlencode($value) . "\"";

		$r .= implode(', ', $values);
		return $r;
	}

}

?>