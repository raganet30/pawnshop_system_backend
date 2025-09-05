<?php
require_once "../config/db.php";

echo "<h3>⏰ Debugging Timestamp Alignment</h3>";

// PHP time
echo "<p><b>PHP time:</b> " . date("Y-m-d H:i:s") . " (" . date_default_timezone_get() . ")</p>";

// MySQL time
$stmt = $pdo->query("SELECT NOW() AS mysql_now, @@session.time_zone AS session_tz, @@global.time_zone AS global_tz");
$row = $stmt->fetch();

echo "<p><b>MySQL NOW():</b> " . $row['mysql_now'] . "</p>";
echo "<p><b>MySQL session timezone:</b> " . $row['session_tz'] . "</p>";
echo "<p><b>MySQL global timezone:</b> " . $row['global_tz'] . "</p>";
?>
