# Skraypar - Pattern Parser
Developing as a core parsing feature of (but not limited to) the [Jikan API](https://github.com/jikan-me) as a separate abstraction class.

**Work in Progress**


## Installation
1. This library uses [Composer](https://getcomposer.org), install that first.
2. `composer require irfan-dahir/skraypar`

## Usage
```php
<?php
require 'vendor/autoload.php';

class Parser extends \Skraypar\Skraypar {

	public function loadRules() { // Abstract function
		$this->addRule(
			'anime_url', // used as a hash key
			'~<meta property="og:url" content="(.*?)">~', // Pattern to match
			function() { // Function to execute when matched

				/*
				* $this->matches // output of pattern match
				* $this->line // current line
				* $this->lineNo // current line no.
				* $this->file // All lines in an array
				*/

				var_dump($this->matches);
			}
		);

		// Add more Rules here
	}
}

$parser = new Parser;
$parser->setPath('http://myanimelist.net/anime/1');
$parser->loadFile();
$parser->loadRules();
$parser->parse();
```

**Outputs**
```
array (size=2)
  0 => string '<meta property="og:url" content="https://myanimelist.net/anime/1/Cowboy_Bebop">' (length=79)
  1 => string 'https://myanimelist.net/anime/1/Cowboy_Bebop' (length=44)
```

## Dependencies
- [Guzzle](https://github.com/guzzle/guzzle)

## [Donate](https://liberapay.com/Nekomata/donate)
If you found this useful, please feel free to donate!

[![Donate](https://liberapay.com/assets/widgets/donate.svg)](https://liberapay.com/Nekomata/donate "Donate using Liberapay")