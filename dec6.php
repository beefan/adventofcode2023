<article class="day-desc">
  <h2>--- Day 6: Wait For It ---</h2>
  <p>The ferry quickly brings you across Island Island. After asking around, you discover that there is indeed normally a large pile of sand somewhere near here, but you don't see anything besides lots of water and the small island where the ferry has docked.</p>
  <p>As you try to figure out what to do next, you notice a poster on a wall near the ferry dock. "Boat races! Open to the public! Grand prize is an all-expenses-paid trip to <em>Desert Island</em>!" That must be where the sand comes from! Best of all, the boat races are starting in just a few minutes.</p>
  <p>You manage to sign up as a competitor in the boat races just in time. The organizer explains that it's not really a traditional race - instead, you will get a fixed amount of time during which your boat has to travel as far as it can, and you win if your boat goes the farthest.</p>
  <p>As part of signing up, you get a sheet of paper (your puzzle input) that lists the <em>time</em> allowed for each race and also the best <em>distance</em> ever recorded in that race. To guarantee you win the grand prize, you need to make sure you <em>go farther in each race</em> than the current record holder.</p>
  <p>The organizer brings you over to the area where the boat races are held. The boats are much smaller than you expected - they're actually <em>toy boats</em>, each with a big button on top. Holding down the button <em>charges the boat</em>, and releasing the button <em>allows the boat to move</em>. Boats move faster if their button was held longer, but time spent holding the button counts against the total race time. You can only hold the button at the start of the race, and boats don't move until the button is released.</p>
  <p>For example:</p>
  <pre><code>Time:      7  15   30
Distance:  9  40  200
</code></pre>
  <p>This document describes three races:</p>
  <ul>
    <li>The first race lasts 7 milliseconds. The record distance in this race is 9 millimeters.</li>
    <li>The second race lasts 15 milliseconds. The record distance in this race is 40 millimeters.</li>
    <li>The third race lasts 30 milliseconds. The record distance in this race is 200 millimeters.</li>
  </ul>
  <p>Your toy boat has a starting speed of <em>zero millimeters per millisecond</em>. For each whole millisecond you spend at the beginning of the race holding down the button, the boat's speed increases by <em>one millimeter per millisecond</em>.</p>
  <p>So, because the first race lasts 7 milliseconds, you only have a few options:</p>
  <ul>
    <li>Don't hold the button at all (that is, hold it for <em><code>0</code> milliseconds</em>) at the start of the race. The boat won't move; it will have traveled <em><code>0</code> millimeters</em> by the end of the race.</li>
    <li>Hold the button for <em><code>1</code> millisecond</em> at the start of the race. Then, the boat will travel at a speed of <code>1</code> millimeter per millisecond for 6 milliseconds, reaching a total distance traveled of <em><code>6</code> millimeters</em>.</li>
    <li>Hold the button for <em><code>2</code> milliseconds</em>, giving the boat a speed of <code>2</code> millimeters per millisecond. It will then get 5 milliseconds to move, reaching a total distance of <em><code>10</code> millimeters</em>.</li>
    <li>Hold the button for <em><code>3</code> milliseconds</em>. After its remaining 4 milliseconds of travel time, the boat will have gone <em><code>12</code> millimeters</em>.</li>
    <li>Hold the button for <em><code>4</code> milliseconds</em>. After its remaining 3 milliseconds of travel time, the boat will have gone <em><code>12</code> millimeters</em>.</li>
    <li>Hold the button for <em><code>5</code> milliseconds</em>, causing the boat to travel a total of <em><code>10</code> millimeters</em>.</li>
    <li>Hold the button for <em><code>6</code> milliseconds</em>, causing the boat to travel a total of <em><code>6</code> millimeters</em>.</li>
    <li>Hold the button for <em><code>7</code> milliseconds</em>. That's the entire duration of the race. You never let go of the button. The boat can't move until you let go of the button. Please make sure you let go of the button so the boat gets to move. <em><code>0</code> millimeters</em>.</li>
  </ul>
  <p>Since the current record for this race is <code>9</code> millimeters, there are actually <code><em>4</em></code> different ways you could win: you could hold the button for <code>2</code>, <code>3</code>, <code>4</code>, or <code>5</code> milliseconds at the start of the race.</p>
  <p>In the second race, you could hold the button for at least <code>4</code> milliseconds and at most <code>11</code> milliseconds and beat the record, a total of <code><em>8</em></code> different ways to win.</p>
  <p>In the third race, you could hold the button for at least <code>11</code> milliseconds and no more than <code>19</code> milliseconds and still beat the record, a total of <code><em>9</em></code> ways you could win.</p>
  <p>To see how much margin of error you have, determine the <em>number of ways you can beat the record</em> in each race; in this example, if you multiply these values together, you get <code><em>288</em></code> (<code>4</code> * <code>8</code> * <code>9</code>).</p>
  <p>Determine the number of ways you could beat the record in each race. <em>What do you get if you multiply these numbers together?</em></p>
</article>
<p>To play, please identify yourself via one of these services:</p>
<p><a href="/auth/github">[GitHub]</a> <a href="/auth/google">[Google]</a> <a href="/auth/twitter">[Twitter]</a> <a href="/auth/reddit">[Reddit]</a> <span class="quiet">- <a href="/about#faq_auth">[How Does Auth Work?]</a></span></p>

<?php
class RaceRecord
{
  public array $record;
  public function __construct($raw, $forPartTwo = false)
  {
    $once = explode(PHP_EOL, $raw);
    $cleanedDistance = trim(explode('Distance: ', $once[1])[1]);
    $cleanedTime = trim(explode('Time: ', $once[0])[1]);

    if ($forPartTwo) {
      $distances = [preg_replace('/\s/', '', $cleanedDistance)];
      $times = [preg_replace('/\s/', '', $cleanedTime)];
    } else {
      $distances = array_filter(preg_split('/\s/', $cleanedDistance));
      $times = array_filter(preg_split('/\s/', $cleanedTime));
    }

    $this->record = array_combine($distances, $times);
  }
}

class Boat
{
  public int $speed = 0;
  private float $acceleration = 1;

  private function charge(int $millisecond)
  {
    for ($i = 0; $i < $millisecond; $i++) {
      $this->speed += $this->acceleration;
    }
  }

  private function race(int $time)
  {
    $distance = $this->speed * $time;
    $this->speed = 0;
    return $distance;
  }

  public function waysToWinCount(int $distance, int $time): int
  {
    $count = 0;
    for ($chargeTime = 0; $chargeTime < $time; $chargeTime++) {
      $raceTime = $time - $chargeTime;
      $this->charge($chargeTime);
      if ($this->race($raceTime) > $distance) {
        $count++;
      }
    }

    return $count;
  }

  public function waysToWinCount2(int $distance, int $time): int
  {
    $root1 = (-$time - sqrt(pow($time, 2) - 4 * $distance)) / 2;
    $root2 = (-$time + sqrt(pow($time, 2) - 4 * $distance)) / 2;
    var_dump(abs($root2 - $root1));
    return floor(abs($root2 - $root1));
  }
}

function advent()
{
  $input = file_get_contents('dec6.input');
  // $input = <<<TEXT
  //   Time:      7  15   30
  //   Distance:  9  40  200
  //   TEXT;

  $raceRecord = new RaceRecord($input, true);
  $boat = new Boat();

  $wins = [];
  foreach ($raceRecord->record as $distance => $time) {
    $wins[] = $boat->waysToWinCount2($distance, $time);
  }

  $margin = array_reduce($wins, fn ($acc, $win) => $acc * $win, 1);
  var_dump($margin);
}

advent();
?>