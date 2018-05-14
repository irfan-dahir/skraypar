# Skraypar - Pattern Parser
A core parsing feature of the [Jikan API](https://github.com/jikan-me) as a separate abstraction class.

**Work in Progress**


## Installation
1. This library uses [Composer](https://getcomposer.org), install that first.
2. `composer require irfan-dahir/skraypar`


## Usage
```
require 'vendor/autoload.php';

class Parser extends \Skraypar\Skraypar {

	public function loadRules() {
		$this->addRule(
			'~<meta property="og:url" content="(.*?)">~',
			function() {
				var_dump($this->matches);
			}
		);
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