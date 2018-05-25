<?php

namespace Skraypar;

class Rule {

	public $hash;
	public $pattern;
	public $callback;
	public $found;

	public function __construct(string $hash, string $pattern, callable $callback) {
		$this->hash = $hash;
		$this->pattern = $pattern;
		$this->callback = $callback;
		$this->found = false;
	}
}