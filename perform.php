<?PHP

require_once("stats_functions.php");
session_start();
error_reporting(~E_ALL);

$times = 1000000;

if (isset($_GET['reset']))
	$_SESSION['complete'] = array();
	
if (!isset($_GET['do']) || $_GET['do'] != 'stats') {

$test_start = microtime(true);

$mode = (isset($_GET['mode'])) ? $_GET['mode'] : NULL;

$string = "This is a string to hash a whole lot!!";

$modes = hash_algos();
if ($mode !== NULL && in_array($mode, $modes)) {
	$i = 0;
	while ((current($modes) != $mode) && $i <= count($modes)) {
		next($modes);
		$i++;
	}
	if (current($modes) === FALSE)
		reset($modes);
}
$mode = current($modes);

$t = 0;
$current = 0;

echo "Beginning tests on \"$mode\"...<br />\n";

$time_start = microtime(true);
while ($t < $times){
	hash($mode, $string);
	$t++;
}
$time_end = microtime(true);

$current = $time_end - $time_start;

if (!isset($_SESSION['complete'][$mode]))
	$_SESSION['complete'][$mode] = array();

array_push($_SESSION['complete'][$mode], $current);

echo "
<script type=\"text/JavaScript\">
	<!--
	function refresh() {
		window.location = \"".basename(__FILE__)."?mode=".next($modes)."&do=tests&".SID."\";
	}

	setTimeout(\"refresh()\", 500);
	//   -->
</script>";

$test_end = microtime(true);

$test_length = $test_end - $test_start;

echo "Completed tests on \"$mode\" in $test_length seconds...<br />\nPerforming Next Test...<br />\n<a href='perform.php?do=stats' target='_blank'>Show Statistics</a>";

} else {

$tests = $_SESSION['complete'];

uasort($tests, 'metric_sort');

echo "
<table width='900px' border='1'>
	<thead>Current Performance Metrics: <a href='".basename(__FILE__)."?reset=true&do=stats&".SID."'>(RESET)</a>&nbsp<a href='#' onclick='refresh();' >(REFRESH)</a></thead>
	<tbody>
		<tr>
			<th rowspan='2'>Hash Mode</th>
			<th>Mean Time</th>
			<th>Median Time</th>
			<th>Max Time</th>
			<th>Min Time</th>
			<th>Range Time</th>
			<th rowspan='2'>Count</th>
		</tr>
		<tr>
			<th>Mean CPS</th>
			<th>Median CPS</th>
			<th>Max CPS</th>
			<th>Min CPS</th>
			<th>Range</th>
		</tr>";

$n = 0;
foreach ($tests as $type => $metrics) {
	$highlight = ($n % 2 != 0) ? " style='background: EEE;'" : "";
	echo "
		<tr$highlight>
			<th rowspan='2'>$type</th>
			<td>".stat_mean($metrics)."</td>
			<td>".stat_median($metrics)."</td>
			<td>".max($metrics)."</td>
			<td>".min($metrics)."</td>
			<td>".stat_range($metrics)."</td>
			<td rowspan='2' align='center' >(".count($metrics).")</td>
		</tr>
		<tr$highlight>
			<td>".$times/stat_mean($metrics)."</td>
			<td>".$times/stat_median($metrics)."</td>
			<td>".$times/min($metrics)."</td>
			<td>".$times/max($metrics)."</td>
			<td>".(($times/min($metrics))-($times/max($metrics)))."</td>
		</tr>";
		$n++;
}

echo "
	</tbody>
</table>
<br />
<script type=\"text/JavaScript\">
	<!--
	function refresh() {
		window.location = \"".basename(__FILE__)."?&do=stats&".SID."\";
	}

	setTimeout(\"refresh()\", 60000);
	//   -->
</script>";

}

?>