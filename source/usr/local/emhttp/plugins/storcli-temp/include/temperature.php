<?php
header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');

function respond($arr, $code = 200) {
  http_response_code($code);
  echo json_encode($arr, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
  exit;
}

function matchValue($text, $pattern) {
  if (preg_match($pattern, $text, $m)) return $m[1];
  return null;
}

$c = isset($_GET['c']) ? $_GET['c'] : '0';
if (!preg_match('/^\d+$/', (string)$c)) $c = '0';

$storcli = '/usr/local/bin/storcli';
if (!is_executable($storcli)) {
  respond(['ok' => false, 'message' => 'storcli not found at /usr/local/bin/storcli'], 500);
}

$cmd = $storcli . ' /c' . $c . ' show all 2>&1';
$out = [];
$rc = 0;
exec($cmd, $out, $rc);
$text = implode("\n", $out);

if ($rc !== 0) {
  respond(['ok' => false, 'message' => 'storcli failed', 'rc' => $rc, 'output' => $text], 500);
}

$support = matchValue($text, '/^Support Temperature\s*=\s*(Yes|No)\s*$/mi');
$sensorRoc = matchValue($text, '/^Temperature Sensor for ROC\s*=\s*([A-Za-z]+)\s*$/mi');
$sensorCtl = matchValue($text, '/^Temperature Sensor for Controller\s*=\s*([A-Za-z]+)\s*$/mi');
$rocTemp = matchValue($text, '/^ROC temperature\(Degree Celsius\)\s*=\s*([0-9]+)\s*$/mi');
$rocTemp = ($rocTemp === null) ? null : (int)$rocTemp;

$now = time();
respond([
  'ok' => true,
  'controller' => (int)$c,
  'roc_c' => $rocTemp,
  'support_temp' => ($support === 'Yes'),
  'sensor_roc' => $sensorRoc,
  'sensor_controller' => $sensorCtl,
  'ts' => $now,
  'ts_local' => date('Y-m-d H:i:s', $now),
]);
