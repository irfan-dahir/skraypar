<?php
/*
 * LookAhead.php
 * Using of LookAheads with Iterators to simplify the example code from /examples/Iterator.php
 * A dynamic way of parsing safely without having to control the iterator manually
 */

require 'vendor/autoload.php';

class Jikan extends \Skraypar\Skraypar {

    public function loadRules() {
        $this->addRule('character', '~</div>Characters & Voice Actors</h2>~', function() {

            $characters = new \Skraypar\Iterator(
                $this->file, // Reference to file
                $this->lineNo // Reference to line number as offset
            ); // Reference to Iterator variable
/* Set a breakpoint callback
            $characters->setBreakpointCallback(function() {
                if (preg_match('~<a name="staff"></a>~', $this->file[$this->lineNo + $i])) {
                    break;
                }
            });
*/
            // Or alternatively, a breakpoint pattern
            $characters->setBreakpointPattern('~<a name="staff"></a>~');

            // Set an iterable callable
            // This function will be called while looping at every line
            $characters->setIteratorCallable(function() use (&$characters) {  // pass self-reference to access iterator & getLine()

                $character = [
                    'mal_id' => null,
                    'url' => null,
                    'image_url' => null,
                    'name' => null,
                    'role' => null,
                    'voice_actor' => []
                ];

                // Look for character table cell
                if (preg_match('~<td valign="top" width="27" class="ac borderClass (bgColor2|bgColor1)">~', $characters->getLine())) {

                    // prepare look ahead by pattern and set a callback once found
                    // the match automatically assigns anything to the iterator class's property `matches
                    // hence here, $characters->matches
                    $characters->lookAhead('~<img alt="(.*)" width="23" height="32" data-src="(.*)" data-srcset="(.*)" class="lazyload" />~', function() use (&$characters, &$character) {
                        $character['image_url'] = trim(substr(explode(",", $characters->matches[3])[1], 0, -3));
                    });

                    $characters->lookAhead('~<a href="(https://myanimelist.net/character/(.*)/(.*))">(.*)</a>~', function() use (&$characters, &$character) {
                        $character['mal_id'] = (int) $characters->matches[2];
                        $character['url'] = $characters->matches[1];
                        $character['name'] = $characters->matches[4];
                    });

                    $characters->lookAhead('~<small>(.*)</small>~', function() use (&$characters, &$character) {
                        $character['role'] = $characters->matches[1];
                    });

                    $voiceActors = new \Skraypar\Iterator($this->file, $this->lineNo);
                    $voiceActors->setBreakpointPattern('~</table>~');
                    $voiceActors->setIteratorCallable(function() use (&$voiceActors) { // Pass self reference
                        $voiceActor = [
                            'mal_id' => null,
                            'url' => null,
                            'image_url' => null,
                            'name' => null,
                            'language' => null
                        ];

                        if (preg_match('~<td valign="top" align="right" style="padding: 0 4px;" nowrap="">~', $voiceActors->getLine())) {

                            $voiceActors->lookAhead('~<a href="(https://myanimelist.net/people/(.*)/(.*))">(.*)</a>~', function() use (&$voiceActors, &$voiceActor) {
                                $voiceActor['mal_id'] = (int) $voiceActors->matches[2];
                                $voiceActor['url'] = $voiceActors->matches[1];
                                $voiceActor['name'] = $voiceActors->matches[4];
                            });

                            $voiceActors->lookAhead('~<small>(.*)</small>~', function() use (&$voiceActors, &$voiceActor) {
                                $voiceActor['language'] = $voiceActors->matches[1];
                            });

                            $voiceActors->lookAhead('~<img alt="(.*)" width="23" height="32" data-src="(.*)" data-srcset="(.*)" class="lazyload" />~', function() use (&$voiceActors, &$voiceActor) {
                                $voiceActor['image_url'] = trim(substr(explode(",", $voiceActors->matches[3])[1], 0, -3));
                            });

                            $voiceActors->response[] = $voiceActor;
                        }

                    });

                    $voiceActors->parse(); // Parse the iteration
                    $character['voice_actor'] = $voiceActors->response; // Set the response

                    $characters->response[] = $character;
                }
            });

            $characters->parse(); // Parse the iteration
            var_dump($characters->response); // Set response somewhere (possibly a model)
        });
    }

}

$jikan = new Jikan;
$jikan->setPath('https://myanimelist.net/anime/1/Cowboy_Bebop/characters');
$jikan->loadFile();
$jikan->loadRules();
$jikan->parse();