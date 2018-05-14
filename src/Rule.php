<?php

namespace Skraypar;

class Rule {

	public $pattern;
	public $callback;
	public $found;

	public function __construct(string $pattern, callable $callback) {
		$this->pattern = $pattern;
		$this->callback = $callback;
		$this->found = false;
	}
}