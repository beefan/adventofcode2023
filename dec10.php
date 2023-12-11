<article class="day-desc">
  <h2>--- Day 10: Pipe Maze ---</h2>
  <p>You use the hang glider to ride the hot air from Desert Island all the way up to the floating metal island. This island is surprisingly cold and there definitely aren't any thermals to glide on, so you leave your hang glider behind.</p>
  <p>You wander around for a while, but you don't find any people or animals. However, you do occasionally find signposts labeled "<a href="https://en.wikipedia.org/wiki/Hot_spring" target="_blank">Hot Springs</a>" pointing in a seemingly consistent direction; maybe you can find someone at the hot springs and ask them where the desert-machine parts are made.</p>
  <p>The landscape here is alien; even the flowers and trees are made of metal. As you stop to admire some metal grass, you notice something metallic scurry away in your peripheral vision and jump into a big pipe! It didn't look like any animal you've ever seen; if you want a better look, you'll need to get ahead of it.</p>
  <p>Scanning the area, you discover that the entire field you're standing on is <span title="Manufactured by Hamilton and Hilbert Pipe Company">densely packed with pipes</span>; it was hard to tell at first because they're the same metallic silver color as the "ground". You make a quick sketch of all of the surface pipes you can see (your puzzle input).</p>
  <p>The pipes are arranged in a two-dimensional grid of <em>tiles</em>:</p>
  <ul>
    <li><code>|</code> is a <em>vertical pipe</em> connecting north and south.</li>
    <li><code>-</code> is a <em>horizontal pipe</em> connecting east and west.</li>
    <li><code>L</code> is a <em>90-degree bend</em> connecting north and east.</li>
    <li><code>J</code> is a <em>90-degree bend</em> connecting north and west.</li>
    <li><code>7</code> is a <em>90-degree bend</em> connecting south and west.</li>
    <li><code>F</code> is a <em>90-degree bend</em> connecting south and east.</li>
    <li><code>.</code> is <em>ground</em>; there is no pipe in this tile.</li>
    <li><code>S</code> is the <em>starting position</em> of the animal; there is a pipe on this tile, but your sketch doesn't show what shape the pipe has.</li>
  </ul>
  <p>Based on the acoustics of the animal's scurrying, you're confident the pipe that contains the animal is <em>one large, continuous loop</em>.</p>
  <p>For example, here is a square loop of pipe:</p>
  <pre><code>.....
.F-7.
.|.|.
.L-J.
.....
</code></pre>
  <p>If the animal had entered this loop in the northwest corner, the sketch would instead look like this:</p>
  <pre><code>.....
.<em>S</em>-7.
.|.|.
.L-J.
.....
</code></pre>
  <p>In the above diagram, the <code>S</code> tile is still a 90-degree <code>F</code> bend: you can tell because of how the adjacent pipes connect to it.</p>
  <p>Unfortunately, there are also many pipes that <em>aren't connected to the loop</em>! This sketch shows the same loop as above:</p>
  <pre><code>-L|F7
7S-7|
L|7||
-L-J|
L|-JF
</code></pre>
  <p>In the above diagram, you can still figure out which pipes form the main loop: they're the ones connected to <code>S</code>, pipes those pipes connect to, pipes <em>those</em> pipes connect to, and so on. Every pipe in the main loop connects to its two neighbors (including <code>S</code>, which will have exactly two pipes connecting to it, and which is assumed to connect back to those two pipes).</p>
  <p>Here is a sketch that contains a slightly more complex main loop:</p>
  <pre><code>..F7.
.FJ|.
SJ.L7
|F--J
LJ...
</code></pre>
  <p>Here's the same example sketch with the extra, non-main-loop pipe tiles also shown:</p>
  <pre><code>7-F7-
.FJ|7
SJLL7
|F--J
LJ.LJ
</code></pre>
  <p>If you want to <em>get out ahead of the animal</em>, you should find the tile in the loop that is <em>farthest</em> from the starting position. Because the animal is in the pipe, it doesn't make sense to measure this by direct distance. Instead, you need to find the tile that would take the longest number of steps <em>along the loop</em> to reach from the starting point - regardless of which way around the loop the animal went.</p>
  <p>In the first example with the square loop:</p>
  <pre><code>.....
.S-7.
.|.|.
.L-J.
.....
</code></pre>
  <p>You can count the distance each tile in the loop is from the starting point like this:</p>
  <pre><code>.....
.012.
.1.3.
.23<em>4</em>.
.....
</code></pre>
  <p>In this example, the farthest point from the start is <code><em>4</em></code> steps away.</p>
  <p>Here's the more complex loop again:</p>
  <pre><code>..F7.
.FJ|.
SJ.L7
|F--J
LJ...
</code></pre>
  <p>Here are the distances for each tile on that loop:</p>
  <pre><code>..45.
.236.
01.7<em>8</em>
14567
23...
</code></pre>
  <p>Find the single giant loop starting at <code>S</code>. <em>How many steps along the loop does it take to get from the starting position to the point farthest from the starting position?</em></p>
</article>
<p>To play, please identify yourself via one of these services:</p>
<p><a href="/auth/github">[GitHub]</a> <a href="/auth/google">[Google]</a> <a href="/auth/twitter">[Twitter]</a> <a href="/auth/reddit">[Reddit]</a> <span class="quiet">- <a href="/about#faq_auth">[How Does Auth Work?]</a></span></p>

<?php
class Board
{
  public array $pipes = ['|', 'L', 'J', '-', 'F', '7'];
  public array $validLeft = ['-', 'L', 'F', 'S'];
  public array $validRight = ['-', 'J', '7', 'S'];
  public array $validUp = ['|', '7', 'F', 'S'];
  public array $validDown = ['|', 'L', 'J', 'S'];
  public array $rows = [];
  public function __construct($raw)
  {
    $once = explode(PHP_EOL, $raw);
    $y = 0;
    $this->rows = array_map(function ($row) use (&$y) {
      $x = 0;
      $res = array_map(
        function ($tile) use (&$y, &$x) {
          $tile = new Tile($tile, $x, $y);
          $x++;
          return $tile;
        },
        str_split($row)
      );

      $y++;
      return $res;
    }, $once);
  }

  public function getStartingTile()
  {
    foreach ($this->rows as $tiles) {
      foreach ($tiles as $tile) {
        if ($tile->mark === 'S') {
          return $tile;
        }
      }
    }
  }

  public function getTile($x, $y, $dir)
  {
    $tile = $this->rows[$y][$x] ?? null;
    if ($dir == 'R' && !$this->validRight($tile)) return null;
    if ($dir == 'L' && !$this->validLeft($tile)) return null;
    if ($dir == 'D' && !$this->validDown($tile)) return null;
    if ($dir == 'U' && !$this->validUp($tile)) return null;

    return $tile;
  }

  public function validPipe(?Tile $tile)
  {
    return in_array($tile?->mark, $this->pipes);
  }

  public function validUp(?Tile $tile)
  {
    return in_array($tile?->mark, $this->validUp);
  }

  public function validDown(?Tile $tile)
  {
    return in_array($tile?->mark, $this->validDown);
  }

  public function validRight(?Tile $tile)
  {
    return in_array($tile?->mark, $this->validRight);
  }

  public function validLeft(?Tile $tile)
  {
    return in_array($tile?->mark, $this->validLeft);
  }
}

class Tile
{
  public function __construct(public string $mark, public int $x, public int $y)
  {
  }
  public function paths(Board $board, ?Tile $previous = null)
  {
    $left = $board->getTile(...[...$this->left(), 'L']);
    $right = $board->getTile(...[...$this->right(), 'R']);
    $up = $board->getTile(...[...$this->up(), 'U']);
    $down = $board->getTile(...[...$this->down(), 'D']);

    $paths = [];
    if ($this->mark == '|') {
      $paths[] = $up;
      $paths[] = $down;
    }
    if ($this->mark == '-') {
      $paths[] = $left;
      $paths[] = $right;
    }
    if ($this->mark == '7') {
      $paths[] = $left;
      $paths[] = $down;
    }
    if ($this->mark == 'F') {
      $paths[] = $right;
      $paths[] = $down;
    }
    if ($this->mark == 'J') {
      $paths[] = $left;
      $paths[] = $up;
    }
    if ($this->mark == 'L') {
      $paths[] = $right;
      $paths[] = $up;
    }
    if ($this->mark == 'S') {
      $paths = array_filter([$right, $up, $left, $down], fn ($tile) => $board->validPipe($tile));
    }

    return array_values(
      $previous ? array_filter($paths, fn ($tile) => $tile !== $previous) : $paths
    );
  }
  public function left()
  {
    return [$this->x - 1, $this->y];
  }
  public function right()
  {
    return [$this->x + 1, $this->y];
  }
  public function up()
  {
    return [$this->x, $this->y - 1];
  }
  public function down()
  {
    return [$this->x, $this->y + 1];
  }
}

function advent()
{
  $input = file_get_contents('dec10.input');
  // $input = <<<TEXT
  //   .....
  //   .S-7.
  //   .|.|.
  //   .L-J.
  //   .....
  //   TEXT;
  // $input = <<<TEXT
  //   ..F7.
  //   .FJ|.
  //   SJ.L7
  //   |F--J
  //   LJ...
  //   TEXT;

  $board = new Board($input);
  $start = $board->getStartingTile();
  /**
   * @var Tile $way1
   * @var Tile $way2
   */
  [$way1, $way2] = $start->paths($board);
  $iterations = 1;
  $last1 = $start;
  $last2 = $start;

  while (!($way1->x == $way2->x && $way1->y == $way2->y)) {
    [$way1new] = $way1->paths($board, $last1);
    [$way2new] = $way2->paths($board, $last2);

    $last1 = $way1;
    $last2 = $way2;
    $way1 = $way1new;
    $way2 = $way2new;
    $iterations++;
  }
  var_dump($iterations);
}

advent();
?>