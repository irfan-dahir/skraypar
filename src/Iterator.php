<?php

namespace Skraypar;

class Iterator {

	public $line;
	public $iterator;
	public $response;
	public $matches = [];

	private $breakpointPatterns = [];
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

	public function setBreakpointPattern($pattern) {
		if (is_string($pattern)) {
			$this->breakpointPatterns[] = $pattern;
		} elseif (is_array($pattern)) {
			$this->breakpointPatterns = array_merge($this->breakpointPatterns, $pattern);
		}

		return $this;
	}

	public function setBreakpointCallback(callable $callable) {
		$this->breakpointCallback = $callable;

		return $this;
	}

	public function setIteratorCallable(callable $callable) {
		$this->iteratorCallable = $callable;

		return $this;
	}

	public function getIterator() : int {
		return $this->iterator;
	}

	public function parse(bool $callableOnExit = false) {
		while (true) {
			$this->line = $this->getLine();

			foreach ($this->breakpointPatterns as $key => $value) {
				if (preg_match($value, $this->line)) {
					!$callableOnExit ?: ($this->iteratorCallable)();
					return;
				}
			}

			($this->iteratorCallable)();

			$this->iterator++;
		}
	}

	public function getLine() {
		if (!isset($this->file[$this->offset + $this->iterator])) {
			throw new \Exception("Parsing Error: End Of File (offset: #" . ($this->offset + $this->iterator) . ")");
		}

		return $this->file[$this->offset + $this->iterator];
	}

	public function lookAhead(string $pattern, callable $callback) {
		while (!preg_match($pattern, $this->line, $this->matches)) {
			$this->line = $this->getLine();

			$this->iterator++;
		}

		($callback)();
	}

}