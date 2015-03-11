<?php

	namespace Corelib\Utils
	{
		use Exception;

		class SpotifyUriConverter
		{
			const SPOTIFY_SCHEMA = 'https';
			const SPOTIFY_HOST = 'open.spotify.com';
			const SPOTIFY_URI_TARGET = 'spotify:';

			private $uri;

			public function __construct($uri)
			{
				$this->uri = trim($uri);
			}

			public function convert()
			{
				if (substr($this->uri, 0, strlen(self::SPOTIFY_URI_TARGET)) == self::SPOTIFY_URI_TARGET)
				{
					$parts = array_slice(explode(':', $this->uri), 1);
					return (sprintf('%s://%s/%s', self::SPOTIFY_SCHEMA, self::SPOTIFY_HOST, implode('/', $parts)));
				}
				else
				{
					$url = new UrlParser(urldecode($this->uri));
					if ($url->getHost() != 'open.spotify.com')
					{
						throw new Exception("Spotify URL (open.spotify.com) or URI (spotify:) expected.");
					}
					return (sprintf('spotify%s', str_replace('/', ':', $url->getPath())));
				}
			}

			public static function convertUri($uri)
			{
				return ((new SpotifyUriConverter($uri))->convert());
			}
		}
	}