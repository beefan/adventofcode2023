<article class="day-desc">
  <h2>--- Day 8: Haunted Wasteland ---</h2>
  <p>You're still riding a camel across Desert Island when you spot a sandstorm quickly approaching. When you turn to warn the Elf, she disappears before your eyes! To be fair, she had just finished warning you about <em>ghosts</em> a few minutes ago.</p>
  <p>One of the camel's pouches is labeled "maps" - sure enough, it's full of documents (your puzzle input) about how to navigate the desert. At least, you're pretty sure that's what they are; one of the documents contains a list of left/right instructions, and the rest of the documents seem to describe some kind of <em>network</em> of labeled nodes.</p>
  <p>It seems like you're meant to use the <em>left/right</em> instructions to <em>navigate the network</em>. Perhaps if you have the camel follow the same instructions, you can escape the haunted wasteland!</p>
  <p>After examining the maps for a bit, two nodes stick out: <code>AAA</code> and <code>ZZZ</code>. You feel like <code>AAA</code> is where you are now, and you have to follow the left/right instructions until you reach <code>ZZZ</code>.</p>
  <p>This format defines each <em>node</em> of the network individually. For example:</p>
  <pre><code>RL

AAA = (BBB, CCC)
BBB = (DDD, EEE)
CCC = (ZZZ, GGG)
DDD = (DDD, DDD)
EEE = (EEE, EEE)
GGG = (GGG, GGG)
ZZZ = (ZZZ, ZZZ)
</code></pre>
  <p>Starting with <code>AAA</code>, you need to <em>look up the next element</em> based on the next left/right instruction in your input. In this example, start with <code>AAA</code> and go <em>right</em> (<code>R</code>) by choosing the right element of <code>AAA</code>, <code><em>CCC</em></code>. Then, <code>L</code> means to choose the <em>left</em> element of <code>CCC</code>, <code><em>ZZZ</em></code>. By following the left/right instructions, you reach <code>ZZZ</code> in <code><em>2</em></code> steps.</p>
  <p>Of course, you might not find <code>ZZZ</code> right away. If you run out of left/right instructions, repeat the whole sequence of instructions as necessary: <code>RL</code> really means <code>RLRLRLRLRLRLRLRL...</code> and so on. For example, here is a situation that takes <code><em>6</em></code> steps to reach <code>ZZZ</code>:</p>
  <pre><code>LLR

AAA = (BBB, BBB)
BBB = (AAA, ZZZ)
ZZZ = (ZZZ, ZZZ)
</code></pre>
  <p>Starting at <code>AAA</code>, follow the left/right instructions. <em>How many steps are required to reach <code>ZZZ</code>?</em></p>
</article>
<p>To play, please identify yourself via one of these services:</p>
<p><a href="/auth/github">[GitHub]</a> <a href="/auth/google">[Google]</a> <a href="/auth/twitter">[Twitter]</a> <a href="/auth/reddit">[Reddit]</a> <span class="quiet">- <a href="/about#faq_auth">[How Does Auth Work?]</a></span></p>

<?php
class Instructions
{
  public array $instructions;
  private int $index = 0;
  public function __construct($raw)
  {
    $this->instructions = str_split($raw);
  }

  public function next()
  {
    $dir = $this->instructions[$this->index];

    $this->index++;
    if ($this->index >= count($this->instructions)) $this->index = 0;

    return $dir;
  }

  public function zero()
  {
    $this->index = 0;
  }
}

class Element
{
  public string $node;
  public string $left;
  public string $right;

  public function __construct($raw)
  {
    [$this->node, $dirs] = explode(' = ', $raw);
    $dirs = str_replace('(', '', $dirs);
    $dirs = str_replace(')', '', $dirs);

    [$this->left, $this->right] = explode(', ', $dirs);
  }
}

function advent()
{
  $input = file_get_contents('dec8.input');
  // $input = <<<TEXT
  //   LLR

  //   AAA = (BBB, BBB)
  //   BBB = (AAA, ZZZ)
  //   ZZZ = (ZZZ, ZZZ)
  //   TEXT;

  [$instructions, $map] = explode(PHP_EOL . PHP_EOL, $input);

  $instructions = new Instructions($instructions);
  $elements = array_map(fn ($element) => new Element($element), explode(PHP_EOL, $map));

  $steps = 0;
  $currentElement = findElement($elements, 'AAA');
  while ($currentElement->node !== 'ZZZ') {
    $steps += 1;
    $dir = $instructions->next();
    $node = $dir === 'R' ? $currentElement->right : $currentElement->left;

    $currentElement = findElement($elements, $node);
  }

  var_dump($steps);
}

function advent2()
{
  $input = file_get_contents('dec8.input');
  // $input = <<<TEXT
  //   LR

  //   11A = (11B, XXX)
  //   11B = (XXX, 11Z)
  //   11Z = (11B, XXX)
  //   22A = (22B, XXX)
  //   22B = (22C, 22C)
  //   22C = (22Z, 22Z)
  //   22Z = (22B, 22B)
  //   XXX = (XXX, XXX)
  //   TEXT;

  [$instructions, $map] = explode(PHP_EOL . PHP_EOL, $input);

  $instructions = new Instructions($instructions);
  $elements = array_map(fn ($element) => new Element($element), explode(PHP_EOL, $map));

  $steps = [];
  $currentElements = findElements($elements, 'A');
  foreach ($currentElements as $element) {
    $step = 0;
    while (!str_ends_with($element->node, 'Z')) {
      $step++;
      $dir = $instructions->next();
      $node = $dir === 'R' ? $element->right : $element->left;

      $element = findElement($elements, $node);
    }
    $steps[] = $step;
    $instructions->zero();
  }

  return lcm($steps);
}

function lcm($elements)
{
  $lcm = array_reduce(array_slice($elements, 1), function ($acc, int $element) {
    return ($acc * $element) / (int) gmp_gcd($acc, $element);
  }, $elements[0]);

  return $lcm;
}

function findElement($haystack, $needle)
{
  foreach ($haystack as $element) {
    if ($element->node === $needle) {
      return $element;
    }
  }
}

function findElements($haystack, $needle)
{
  $elements = [];
  foreach ($haystack as $element) {
    if (str_ends_with($element->node, $needle)) {
      $elements[] = $element;
    }
  }

  return $elements;
}

var_dump(advent2());
?>