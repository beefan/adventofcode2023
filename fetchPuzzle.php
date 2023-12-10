<?php

function fetchPuzzle()
{
  date_default_timezone_set('EST');
  $day = date('j');

  $puzzleFilePath = 'dec' . $day . '.php';
  $puzzleInputFilePath = 'dec' . $day . '.input';

  $puzzleUrl = 'https://adventofcode.com/2023/day/' . $day;
  $puzzleInputUrl = $puzzleUrl . '/input';

  $puzzle = explode('</main>', explode('<main>', file_get_contents($puzzleUrl))[1])[0] .
    '
<?php
  function advent() {
    $input = file_get_contents(\'' . $puzzleInputFilePath . '\');
    $input = <<<TEXT
      TEXT;
    foreach (explode(PHP_EOL, $input) as $line) {
      echo $line;
    }
  }

  advent();
?>
';

  $session = file_get_contents('.env');
  $inputOpts = array(
    'http' => array(
      'method' => "GET",
      'header' => "Accept-language: en\r\n" .
        "Cookie: $session\r\n"
    )
  );
  $inputContext = stream_context_create($inputOpts);
  $puzzleInput = trim(file_get_contents($puzzleInputUrl, false, $inputContext));

  if (file_exists($puzzleFilePath)) $puzzleFilePath .= '_dup';
  if (file_exists($puzzleInputFilePath)) $puzzleInputFilePath .= '_dup';

  echo "writing puzzle...";
  file_put_contents($puzzleFilePath, $puzzle);

  echo PHP_EOL;
  echo "writing puzzle input...";
  file_put_contents($puzzleInputFilePath, $puzzleInput);
}

fetchPuzzle();
