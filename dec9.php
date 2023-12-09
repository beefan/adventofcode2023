<article class="day-desc">
  <h2>--- Day 9: Mirage Maintenance ---</h2>
  <p>You ride the camel through the sandstorm and stop where the ghost's maps told you to stop. <span title="The sound of a sandstorm slowly settling.">The sandstorm subsequently subsides, somehow seeing you standing at an <em>oasis</em>!</span></p>
  <p>The camel goes to get some water and you stretch your neck. As you look up, you discover what must be yet another giant floating island, this one made of metal! That must be where the <em>parts to fix the sand machines</em> come from.</p>
  <p>There's even a <a href="https://en.wikipedia.org/wiki/Hang_gliding" target="_blank">hang glider</a> partially buried in the sand here; once the sun rises and heats up the sand, you might be able to use the glider and the hot air to get all the way up to the metal island!</p>
  <p>While you wait for the sun to rise, you admire the oasis hidden here in the middle of Desert Island. It must have a delicate ecosystem; you might as well take some ecological readings while you wait. Maybe you can report any environmental instabilities you find to someone so the oasis can be around for the next sandstorm-worn traveler.</p>
  <p>You pull out your handy <em>Oasis And Sand Instability Sensor</em> and analyze your surroundings. The OASIS produces a report of many values and how they are changing over time (your puzzle input). Each line in the report contains the <em>history</em> of a single value. For example:</p>
  <pre><code>0 3 6 9 12 15
1 3 6 10 15 21
10 13 16 21 30 45
</code></pre>
  <p>To best protect the oasis, your environmental report should include a <em>prediction of the next value</em> in each history. To do this, start by making a new sequence from the <em>difference at each step</em> of your history. If that sequence is <em>not</em> all zeroes, repeat this process, using the sequence you just generated as the input sequence. Once all of the values in your latest sequence are zeroes, you can extrapolate what the next value of the original history should be.</p>
  <p>In the above dataset, the first history is <code>0 3 6 9 12 15</code>. Because the values increase by <code>3</code> each step, the first sequence of differences that you generate will be <code>3 3 3 3 3</code>. Note that this sequence has one fewer value than the input sequence because at each step it considers two numbers from the input. Since these values aren't <em>all zero</em>, repeat the process: the values differ by <code>0</code> at each step, so the next sequence is <code>0 0 0 0</code>. This means you have enough information to extrapolate the history! Visually, these sequences can be arranged like this:</p>
  <pre><code>0   3   6   9  12  15
  3   3   3   3   3
    0   0   0   0
</code></pre>
  <p>To extrapolate, start by adding a new zero to the end of your list of zeroes; because the zeroes represent differences between the two values above them, this also means there is now a placeholder in every sequence above it:
  <p>
  <pre><code>0   3   6   9  12  15   <em>B</em>
  3   3   3   3   3   <em>A</em>
    0   0   0   0   <em>0</em>
</code></pre>
  <p>You can then start filling in placeholders from the bottom up. <code>A</code> needs to be the result of increasing <code>3</code> (the value to its left) by <code>0</code> (the value below it); this means <code>A</code> must be <code><em>3</em></code>:</p>
  <pre><code>0   3   6   9  12  15   B
  3   3   3   3   <em>3</em>   <em>3</em>
    0   0   0   0   <em>0</em>
</code></pre>
  <p>Finally, you can fill in <code>B</code>, which needs to be the result of increasing <code>15</code> (the value to its left) by <code>3</code> (the value below it), or <code><em>18</em></code>:</p>
  <pre><code>0   3   6   9  12  <em>15</em>  <em>18</em>
  3   3   3   3   3   <em>3</em>
    0   0   0   0   0
</code></pre>
  <p>So, the next value of the first history is <code><em>18</em></code>.</p>
  <p>Finding all-zero differences for the second history requires an additional sequence:</p>
  <pre><code>1   3   6  10  15  21
  2   3   4   5   6
    1   1   1   1
      0   0   0
</code></pre>
  <p>Then, following the same process as before, work out the next value in each sequence from the bottom up:</p>
  <pre><code>1   3   6  10  15  21  <em>28</em>
  2   3   4   5   6   <em>7</em>
    1   1   1   1   <em>1</em>
      0   0   0   <em>0</em>
</code></pre>
  <p>So, the next value of the second history is <code><em>28</em></code>.</p>
  <p>The third history requires even more sequences, but its next value can be found the same way:</p>
  <pre><code>10  13  16  21  30  45  <em>68</em>
   3   3   5   9  15  <em>23</em>
     0   2   4   6   <em>8</em>
       2   2   2   <em>2</em>
         0   0   <em>0</em>
</code></pre>
  <p>So, the next value of the third history is <code><em>68</em></code>.</p>
  <p>If you find the next value for each history in this example and add them together, you get <code><em>114</em></code>.</p>
  <p>Analyze your OASIS report and extrapolate the next value for each history. <em>What is the sum of these extrapolated values?</em></p>
</article>
<p>To play, please identify yourself via one of these services:</p>
<p><a href="/auth/github">[GitHub]</a> <a href="/auth/google">[Google]</a> <a href="/auth/twitter">[Twitter]</a> <a href="/auth/reddit">[Reddit]</a> <span class="quiet">- <a href="/about#faq_auth">[How Does Auth Work?]</a></span></p>

<?php
class Sensor
{
  public array $histories = [];
  public function __construct($raw)
  {
    $this->histories = array_map(fn ($line) => new History($line), explode(PHP_EOL, $raw));
  }
}

class History
{
  public array $map;
  public function __construct($raw)
  {
    $iteration = explode(' ', $raw);
    $index = 0;
    while (true) {
      $this->map[$index] = $iteration;

      $reachedAllZeros = count(array_unique($iteration)) == 1 && $iteration[0] == 0;
      if ($reachedAllZeros) break;

      $iteration = $this->getNextIteration($iteration);

      $index++;
    }
  }

  public function value()
  {
    $reversed = array_reverse($this->map);

    return array_reduce($reversed, function ($acc, array $history) {
      $lastIndex = count($history) - 1;
      return $acc + $history[$lastIndex];
    });
  }

  private function getNextIteration($iteration): array
  {
    $new = [];

    for ($i = 0; $i < count($iteration) - 1; $i++) {
      $new[] = $iteration[$i + 1] - $iteration[$i];
    }

    return $new;
  }
}

function advent()
{
  $input = file_get_contents('dec9.input');
  // $input = <<<TEXT
  //     0 3 6 9 12 15
  //     1 3 6 10 15 21
  //     10 13 16 21 30 45
  //     TEXT;

  $sensor = new Sensor($input);

  $sum = array_reduce($sensor->histories, fn ($acc, History $history) => $acc + $history->value());

  var_dump($sum);
}

advent();
?>