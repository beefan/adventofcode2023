<article class="day-desc">
  <h2>--- Day 3: Gear Ratios ---</h2>
  <p>You and the Elf eventually reach a <a href="https://en.wikipedia.org/wiki/Gondola_lift" target="_blank">gondola lift</a> station; he says the gondola lift will take you up to the <em>water source</em>, but this is as far as he can bring you. You go inside.</p>
  <p>It doesn't take long to find the gondolas, but there seems to be a problem: they're not moving.</p>
  <p>"Aaah!"</p>
  <p>You turn around to see a slightly-greasy Elf with a wrench and a look of surprise. "Sorry, I wasn't expecting anyone! The gondola lift isn't working right now; it'll still be a while before I can fix it." You offer to help.</p>
  <p>The engineer explains that an engine part seems to be missing from the engine, but nobody can figure out which one. If you can <em>add up all the part numbers</em> in the engine schematic, it should be easy to work out which part is missing.</p>
  <p>The engine schematic (your puzzle input) consists of a visual representation of the engine. There are lots of numbers and symbols you don't really understand, but apparently <em>any number adjacent to a symbol</em>, even diagonally, is a "part number" and should be included in your sum. (Periods (<code>.</code>) do not count as a symbol.)</p>
  <p>Here is an example engine schematic:</p>
  <pre><code>467..114..
...*......
..35..633.
......#...
617*......
.....+.58.
..592.....
......755.
...$.*....
.664.598..
</code></pre>
  <p>In this schematic, two numbers are <em>not</em> part numbers because they are not adjacent to a symbol: <code>114</code> (top right) and <code>58</code> (middle right). Every other number is adjacent to a symbol and so <em>is</em> a part number; their sum is <code><em>4361</em></code>.</p>
  <p>Of course, the actual engine schematic is much larger. <em>What is the sum of all of the part numbers in the engine schematic?</em></p>
</article>
<p>To play, please identify yourself via one of these services:</p>
<p><a href="/auth/github">[GitHub]</a> <a href="/auth/google">[Google]</a> <a href="/auth/twitter">[Twitter]</a> <a href="/auth/reddit">[Reddit]</a> <span class="quiet">- <a href="/about#faq_auth">[How Does Auth Work?]</a></span></p>

<?php

class Schematic
{
  public array $inputs = [];
  public function __construct(string $input)
  {
    $lines = explode(PHP_EOL, $input);

    foreach ($lines as $y => $line) {
      $currentInput = [];
      foreach (str_split($line) as $x => $char) {
        $type = $this->getCharType($char);

        if (($currentInput['type'] ?? $type) !== $type) {
          $this->inputs[] = $this->newInput($currentInput);
          $currentInput = [];
        }

        if (empty($currentInput)) {
          $currentInput['value'] = $char;
          $currentInput['type'] = $type;
        } elseif ($currentInput['type'] === $type) {
          $currentInput['value'] .= $char;
        }

        $currentInput['coordinates'][] = new Coordinate($x, $y);
      }

      $this->inputs[] = $this->newInput($currentInput);
    }
  }

  public function partNumbers(): array
  {
    $symbols = array_filter($this->inputs, fn (Input $item) => get_class($item) === Symbol::class);
    $numbers = array_filter($this->inputs, fn (Input $item) => get_class($item) === Number::class);

    $partNumbers = [];
    /** @var Number $number */
    foreach ($numbers as $number) {
      if ($number->isAdjacentToSymbol($symbols)) $partNumbers[] = (int) $number->value;
    }

    return $partNumbers;
  }

  public function gearRatios(): array
  {
    $symbols = array_filter($this->inputs, fn (Input $item) => get_class($item) === Symbol::class);
    $numbers = array_filter($this->inputs, fn (Input $item) => get_class($item) === Number::class);

    return array_filter(
      array_map(fn (Symbol $symbol) => $symbol->gearRatio($numbers), $symbols)
    );
  }

  private function getCharType(string $char): string
  {
    $type = 'symbol';
    if (preg_match('/\d/', $char)) {
      $type = 'number';
    } elseif ($char === '.') {
      $type = 'space';
    }

    return $type;
  }

  private function newInput(array $currentInput): Input
  {
    switch ($currentInput['type']) {
      case 'space':
        $input = new Spacer($currentInput['coordinates']);
        break;
      case 'number':
        $input = new Number($currentInput['coordinates']);
        break;
      default:
        $input =  new Symbol($currentInput['coordinates']);
        break;
    }

    $input->value = $currentInput['value'];
    return $input;
  }
}

class Coordinate
{
  public int $x = 0;
  public int $y = 0;
  public function __construct(int $x, int $y)
  {
    $this->x = $x;
    $this->y = $y;
  }

  public function toArray(): array
  {
    return [
      'x' => $this->x,
      'y' => $this->y,
    ];
  }
}

class Input
{
  /** @var Coordinate[] $coordinates */
  public array $coordinates = [];
  public ?string $value = null;
  public function __construct(array $coordinates)
  {
    $this->coordinates = $coordinates;
  }

  public function adjacentCoordinates(): array
  {
    $result = [];
    foreach ($this->coordinates as $coordinate) {
      $x = $coordinate->x;
      $y = $coordinate->y;
      $result = [
        ...$result,
        ['x' => $x + 1, 'y' => $y + 1],
        ['x' => $x + 1, 'y' => $y],
        ['x' => $x + 1, 'y' => $y - 1],
        ['x' => $x, 'y' => $y + 1],
        ['x' => $x, 'y' => $y - 1],
        ['x' => $x - 1, 'y' => $y + 1],
        ['x' => $x - 1, 'y' => $y],
        ['x' => $x - 1, 'y' => $y - 1],
      ];
    }
    return $result;
  }

  public function resolveCoordinates(array $inputs): array
  {
    $inputCoordinates = array_map(
      fn (Input $input) => $input->coordinates,
      $inputs
    );
    return array_map(
      fn (array $coordinate) => $coordinate[0]->toArray(),
      $inputCoordinates
    );
  }
}

class Spacer extends Input
{
}

class Number extends Input
{
  public function isAdjacentToSymbol(array $symbols): bool
  {
    $symbolCoordinates = $this->resolveCoordinates($symbols);

    foreach ($this->adjacentCoordinates() as $adjacentCoordinate) {
      if (in_array($adjacentCoordinate, $symbolCoordinates)) {
        return true;
      }
    }
    return false;
  }
}

class Symbol extends Input
{
  public function gearRatio(array $numbers): int
  {
    if ($this->value !== '*') return 0;

    $matches = [];
    foreach ($numbers as $number) {
      $numberCoordinates = array_map(fn (Coordinate $point) => $point->toArray(), $number->coordinates);

      foreach ($this->adjacentCoordinates() as $adjacentCoordinate) {
        if (in_array($adjacentCoordinate, $numberCoordinates)) {
          $matches[] = (int) $number->value;
          break;
        }
      }
    }

    if (count($matches) < 2) return 0;

    return array_reduce($matches, fn ($acc, $item) => $acc * $item, 1);
  }
}

function advent()
{
  $input = file_get_contents('dec3.input');
  //   $input = "467..114..
  // ...*......
  // ..35..633.
  // ......#...
  // 617*......
  // .....+.58.
  // ..592.....
  // ......755.
  // ...$.*....
  // .664.598..";


  $schematic = new Schematic($input);
  $partNumbers = $schematic->partNumbers();

  var_dump(array_reduce($partNumbers, fn ($acc, $n) => $acc + $n));
}

function advent2()
{
  $input = file_get_contents('dec3.input');
  //   $input =
  //     "467..114..
  // ...*......
  // ..35..633.
  // ......#...
  // 617*......
  // .....+.58.
  // ..592.....
  // ......755.
  // ...$.*....
  // .664.598..";


  $schematic = new Schematic($input);
  $gearRatios = $schematic->gearRatios();

  var_dump(array_reduce($gearRatios, fn ($acc, $n) => $acc + $n));
}

advent2();
?>