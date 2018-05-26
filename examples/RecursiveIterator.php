<?php

require 'vendor/autoload.php';

class Jikan extends \Skraypar\Skraypar {

	public function loadRules() {
        $this->addRule('character', '~</div>Characters & Voice Actors</h2>~', function() {

        	$characterIterator = 0;
        	$characters = new \Skraypar\Iterator($this->file, $this->lineNo, $characterIterator);
/*
			$characters->setBreakpointCallback(function() {
				if (preg_match('~<a name="staff"></a>~', $this->file[$this->lineNo + $i])) {
					break;
				}
        	});
*/
        	$characters->setBreakpointPattern('~<a name="staff"></a>~');
        	$characters->setIteratorCallable(function() use (&$characters) {
                $character = [
                	'mal_id' => null,
                	'url' => null,
                	'image_url' => null,
                	'name' => null,
                	'role' => null,
                	'voice_actor' => []
                ];

                if (preg_match('~<td valign="top" width="27" class="ac borderClass (bgColor2|bgColor1)">~', $characters->getLine())) {
                    $characters->iterator += 3;
                    preg_match('~<img alt="(.*)" width="23" height="32" data-src="(.*)" data-srcset="(.*)" class="lazyload" />~', $characters->getLine(), $this->matches);
                    $character['image_url'] = trim(substr(explode(",", $this->matches[3])[1], 0, -3));

                    $characters->iterator += 5;
                    preg_match('~<a href="(https://myanimelist.net/character/(.*)/(.*))">(.*)</a>~', $characters->getLine(), $this->matches);
                    $character['mal_id'] = (int) $this->matches[2];
                    $character['url'] = $this->matches[1];
                    $character['name'] = $this->matches[4];

                    $characters->iterator += 2;
                    preg_match('~<small>(.*)</small>~', $characters->getLine(), $this->matches);
                    $character['role'] = $this->matches[1];

                    $voiceActorIterator = 0;
                    $voiceActors = new \Skraypar\Iterator($this->file, $this->lineNo, $voiceActorIterator);
        			$voiceActors->setBreakpointPattern('~</table>~');
        			$voiceActors->setIteratorCallable(function() use (&$voiceActors, &$characters) {
        				$voiceActor = [
        					'mal_id' => null,
        					'url' => null,
        					'image_url' => null,
        					'name' => null,
        					'language' => null
        				];

                        if (preg_match('~<td valign="top" align="right" style="padding: 0 4px;" nowrap="">~', $voiceActors->getLine())) {

                            $voiceActors->iterator++;
                            preg_match('~<a href="(https://myanimelist.net/people/(.*)/(.*))">(.*)</a>~', $voiceActors->getLine(), $this->matches);
                            $voiceActor['mal_id'] = (int) $this->matches[2];
                            $voiceActor['url'] = $this->matches[1];
                            $voiceActor['name'] = $this->matches[4];
                            
                            $voiceActors->iterator++;
                            preg_match('~<small>(.*)</small>~', $voiceActors->getLine(), $this->matches);
                            $voiceActor['language'] = $this->matches[1];

                            $voiceActors->iterator += 5;
                            preg_match('~<img alt="(.*)" width="23" height="32" data-src="(.*)" data-srcset="(.*)" class="lazyload" />~', $voiceActors->getLine(), $this->matches);
                            $voiceActor['image_url'] = trim(substr(explode(",", $this->matches[3])[1], 0, -3));

                            $voiceActors->response[] = $voiceActor;
                    	}
        			});
        			$voiceActors->parse();
        			$character['voice_actor'] = $voiceActors->response;

                    $characters->response[] = $character;
                }
        	});

        	$characters->parse();
        });
	}

}

$jikan = new Jikan;
$jikan->setPath('https://myanimelist.net/anime/1/Cowboy_Bebop/characters');
$jikan->loadFile();
$jikan->loadRules();
$jikan->parse();