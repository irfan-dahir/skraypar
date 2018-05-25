<?php

namespace Skraypar;

use \Skraypar\Helper\Funcs as Func;

abstract class Skraypar {

	public $status;
	public $file;
	public $filePath;
	public $data;
	public $model;
	public $rules = [];
	public $matches = [];
	public $line;
	public $lineNo;

	public $response;

	private $accepting = [
		200, // OK
		303, // Forwarded
	];
	private $rejecting = [
		'400' => 'Bad Request',
		'403' => 'Forbidden',
		'404' => 'File not found',
		'429' => 'Too Many Requests',
		'500' => 'Server Error'
	];
	private $client;

	abstract public function loadRules();

	public function setPath($filePath) {
		$this->filePath = $filePath;
	}

	public function loadFile() {
		if (is_null($this->filePath)){
			throw new \Exception("File path is null");
		}

		if (Func::isURL($this->filePath)) {
			$this->client = new \GuzzleHttp\Client(['http_errors' => false]);
			$response = $this->client->request('GET', $this->filePath);
			$this->status = $response->getStatusCode();

			if ($this->isAccepting()) {
				$this->response = $response->getBody();
				$this->file = preg_split("/\r\n|\n|\r/", $this->response);
				return;
			}

			if ($this->isRejecting()) {
				throw new \Exception("Request failed: " . $this->getRejectingStatus());
			}
		}

		if (!file_exists($this->filePath)) {
			throw new \Exception("File does not exist");
		}

		if (!(is_file($this->filePath) && is_readable($this->filePath))) {
			throw new \Exception("File is not readable");
		}

		$this->file = file($this->filePath);
	}

	public function parse() {
		foreach ($this->file as $lineNo => $line) {
			$this->lineNo = $lineNo;
			$this->line = $line;


			foreach ($this->rules as $pattern => &$rule) {
				if ($rule->found) { continue; }

				if (preg_match($pattern, $this->line, $this->matches)) {
					($rule->callback)();
					$rule->found = true;
				}
			}
		}
	}

	public function addRule($hash, $pattern, $callback) {
		$this->rules[$pattern] = new \Skraypar\Rule($hash, $pattern, $callback);
	}

	private function isAccepting() {
		return in_array($this->status, $this->accepting) ? true : false;
	}

	private function isRejecting() {
		return array_key_exists($this->status, $this->rejecting) ? true : false;
	}

	private function getRejectingStatus() {
		return $this->rejecting[$this->status];
	}

}