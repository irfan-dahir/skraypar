<?php

namespace Skraypar;

class Iterator {

	public $line;
	public $iterator;
	public $response;

	private $breakpointPattern;
	private $breakpointCallback;
	private $iteratorCallable;
	private $file;
	private $offset;

	public function __construct(array &$file, int &$offset = 0, int &$iterator = 0, $response = []) {
		$this->iterator = &$iterator;
		$this->file = &$file;
		$this->offset = &$offset;
		$this->response = $response;
	}

	public function setBreakpointPattern(string $pattern) {
		$this->breakpointPattern = $pattern;
	}

	public function setBreakpointCallback(callable $callable) {
		$this->breakpointCallback = $callable;
	}

	public function setIteratorCallable(callable $callable) {
		$this->iteratorCallable = $callable;
	}

	public function getIterator() {
		return $this->iterator;
	}

	public function parse() {
		while (true) {
			$this->line = $this->getLine();

			if (preg_match($this->breakpointPattern, $this->line)) {
				break;
			}

			($this->iteratorCallable)();

			$this->iterator++;
		}
	}

	public function getLine() {
		return $this->file[$this->offset + $this->iterator];
	}

}