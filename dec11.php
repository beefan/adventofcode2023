<article class="day-desc">
  <h2>--- Day 11: Cosmic Expansion ---</h2>
  <p>You continue following signs for "Hot Springs" and eventually come across an <a href="https://en.wikipedia.org/wiki/Observatory" target="_blank">observatory</a>. The Elf within turns out to be a researcher studying cosmic expansion using the giant telescope here.</p>
  <p>He doesn't know anything about the missing machine parts; he's only visiting for this research project. However, he confirms that the hot springs are the next-closest area likely to have people; he'll even take you straight there once he's done with today's observation analysis.</p>
  <p>Maybe you can help him with the analysis to speed things up?</p>
  <p>The researcher has collected a bunch of data and compiled the data into a single giant <em>image</em> (your puzzle input). The image includes <em>empty space</em> (<code>.</code>) and <em>galaxies</em> (<code>#</code>). For example:</p>
  <pre><code>...#......
.......#..
#.........
..........
......#...
.#........
.........#
..........
.......#..
#...#.....
</code></pre>
  <p>The researcher is trying to figure out the sum of the lengths of the <em>shortest path between every pair of galaxies</em>. However, there's a catch: the universe expanded in the time it took the light from those galaxies to reach the observatory.</p>
  <p>Due to something involving gravitational effects, <em>only some space expands</em>. In fact, the result is that <em>any rows or columns that contain no galaxies</em> should all actually be twice as big.</p>
  <p>In the above example, three columns and two rows contain no galaxies:</p>
  <pre><code>   v  v  v
 ...#......
 .......#..
 #.........
&gt;..........&lt;
 ......#...
 .#........
 .........#
&gt;..........&lt;
 .......#..
 #...#.....
   ^  ^  ^
</code></pre>
  <p>These rows and columns need to be <em>twice as big</em>; the result of cosmic expansion therefore looks like this:</p>
  <pre><code>....#........
.........#...
#............
.............
.............
........#....
.#...........
............#
.............
.............
.........#...
#....#.......
</code></pre>
  <p>Equipped with this expanded universe, the shortest path between every pair of galaxies can be found. It can help to assign every galaxy a unique number:</p>
  <pre><code>....1........
.........2...
3............
.............
.............
........4....
.5...........
............6
.............
.............
.........7...
8....9.......
</code></pre>
  <p>In these 9 galaxies, there are <em>36 pairs</em>. Only count each pair once; order within the pair doesn't matter. For each pair, find any shortest path between the two galaxies using only steps that move up, down, left, or right exactly one <code>.</code> or <code>#</code> at a time. (The shortest path between two galaxies is allowed to pass through another galaxy.)</p>
  <p>For example, here is one of the shortest paths between galaxies <code>5</code> and <code>9</code>:</p>
  <pre><code>....1........
.........2...
3............
.............
.............
........4....
.5...........
.##.........6
..##.........
...##........
....##...7...
8....9.......
</code></pre>
  <p>This path has length <code><em>9</em></code> because it takes a minimum of <em>nine steps</em> to get from galaxy <code>5</code> to galaxy <code>9</code> (the eight locations marked <code>#</code> plus the step onto galaxy <code>9</code> itself). Here are some other example shortest path lengths:</p>
  <ul>
    <li>Between galaxy <code>1</code> and galaxy <code>7</code>: 15</li>
    <li>Between galaxy <code>3</code> and galaxy <code>6</code>: 17</li>
    <li>Between galaxy <code>8</code> and galaxy <code>9</code>: 5</li>
  </ul>
  <p>In this example, after expanding the universe, the sum of the shortest path between all 36 pairs of galaxies is <code><em>374</em></code>.</p>
  <p>Expand the universe, then find the length of the shortest path between every pair of galaxies. <em>What is the sum of these lengths?</em></p>
</article>
<p>To play, please identify yourself via one of these services:</p>
<p><a href="/auth/github">[GitHub]</a> <a href="/auth/google">[Google]</a> <a href="/auth/twitter">[Twitter]</a> <a href="/auth/reddit">[Reddit]</a> <span class="quiet">- <a href="/about#faq_auth">[How Does Auth Work?]</a></span></p>

<?php
class Galaxy
{
  public function __construct(public int $x, public int $y, public int $id)
  {
  }

  public function distanceToGalaxy(Galaxy $galaxy)
  {
    return abs($this->x - $galaxy->x) + abs($this->y - $galaxy->y);
  }
}

class Universe
{
  public array $rows;
  public array $galaxyPairs;
  public array $galaxies;

  public function __construct($input)
  {
    // clean input
    $once = explode(PHP_EOL, $input);
    $rows = array_map(function ($line) {
      return str_split($line);
    }, $once);

    $this->rows = $this->expand($rows);
    $this->galaxyPairs = $this->findGalaxyPairs() ?? [];
  }

  public function getGalaxy($id): Galaxy
  {
    return array_values(array_filter($this->galaxies, fn ($galaxy) => $galaxy->id == $id))[0];
  }

  private function findGalaxyPairs()
  {
    $galaxies = [];
    $id = 1;
    for ($y = 0; $y < count($this->rows); $y++) {
      for ($x = 0; $x < count($this->rows[$y]); $x++) {
        if ($this->rows[$y][$x] == '#') {
          $galaxies[] = new Galaxy($x, $y, $id);
          $id++;
        }
      }
    }
    $this->galaxies = $galaxies;

    $pairs = [];
    for ($i = 1; $i <= count($galaxies); $i++) {
      foreach ($galaxies as $galaxy) {
        if ($galaxy->id == $i) continue;
        if (in_array([$galaxy->id, $i], $pairs) || in_array([$i, $galaxy->id], $pairs)) continue;

        $pairs[] = [$galaxy->id, $i];
      }
    }

    return $pairs;
  }

  private function expand(array $rows)
  {
    // find empty rows
    $dupRows = [];
    for ($i = 0; $i < count($rows); $i++) {
      $row = $rows[$i];
      if (array_unique($row) == ['.']) {
        $dupRows[] = $i;
      }
    }

    // insert duplicate rows
    $offset = 0;
    foreach ($dupRows as $row) {
      $row = $row + $offset;
      $offset++;

      $nextId = $row + 1;
      $final = array_slice($rows, 0, $nextId);
      $final[$nextId] = $rows[$row];

      foreach (array_slice($rows, $nextId) as $item) {
        $nextId++;
        $final[$nextId] = $item;
      }

      $rows = $final;
    }

    // find empty cols
    $dupCols = [];
    for ($i = 0; $i < count($rows[0]); $i++) {
      $colVal = $rows[0][$i];
      $dup = true;
      foreach ($rows as $row) {
        if ($colVal !== $row[$i]) {
          $dup = false;
          break;
        }
      }

      if ($dup) $dupCols[] = $i;
    }

    // insert duplicate columns
    $offset = 0;
    foreach ($dupCols as $col) {
      $col = $col + $offset;
      $offset++;

      $rows = array_map(function ($row) use ($col) {
        $nextCol = $col + 1;

        $final = array_slice($row, 0, $nextCol);
        $final[$nextCol] = $row[$col];
        foreach (array_slice($row, $nextCol) as $item) {
          $nextCol++;
          $final[$nextCol] = $item;
        }

        return $final;
      }, $rows);
    }

    return $rows;
  }

  public function visualize()
  {
    foreach ($this->rows as $row) {
      foreach ($row as $col) {
        echo $col;
      }
      echo PHP_EOL;
    }
  }
}

function advent()
{
  $input = file_get_contents('dec11.input');
  // $input = <<<TEXT
  //     ...#......
  //     .......#..
  //     #.........
  //     ..........
  //     ......#...
  //     .#........
  //     .........#
  //     ..........
  //     .......#..
  //     #...#.....
  //     TEXT;

  $universe = new Universe($input);
  $universe->visualize();

  $shortestPaths = array_map(function ($pair) use ($universe) {
    [$galaxyId1, $galaxyId2] = $pair;
    $g1 = $universe->getGalaxy($galaxyId1);
    $g2 = $universe->getGalaxy($galaxyId2);

    return $g1->distanceToGalaxy($g2);
  }, $universe->galaxyPairs);

  $shortestPath = array_reduce($shortestPaths, fn ($acc, $distance) => $acc + $distance);

  var_dump($shortestPath);
}

advent();
?>