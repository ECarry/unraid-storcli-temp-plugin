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

function execText($cmd, &$rc) {
  $out = [];
  $rc = 0;
  exec($cmd, $out, $rc);
  return implode("\n", $out);
}

$storcli = '/usr/local/bin/storcli';
if (!is_executable($storcli)) {
  respond(['ok' => false, 'message' => 'storcli not found at /usr/local/bin/storcli'], 500);
}

function getControllers($storcli) {
  $rc = 0;
  $text = execText($storcli . ' show 2>&1', $rc);
  if ($rc !== 0) return [0];

  $matches = [];
  if (preg_match_all('/\/(?:c|C)(\d+)/', $text, $matches)) {
    $ids = array_map('intval', $matches[1]);
    $ids = array_values(array_unique($ids));
    sort($ids);
    if (count($ids) > 0) return $ids;
  }

  return [0];
}

function readController($storcli, $c) {
  $rc = 0;
  $text = execText($storcli . ' /c' . $c . ' show all 2>&1', $rc);
  if ($rc !== 0) {
    return [
      'id' => (int)$c,
      'ok' => false,
      'message' => 'storcli failed',
      'rc' => $rc,
    ];
  }

  $support = matchValue($text, '/^Support Temperature\s*=\s*(Yes|No)\s*$/mi');
  $sensorRoc = matchValue($text, '/^Temperature Sensor for ROC\s*=\s*([A-Za-z]+)\s*$/mi');
  $sensorCtl = matchValue($text, '/^Temperature Sensor for Controller\s*=\s*([A-Za-z]+)\s*$/mi');
  $rocTemp = matchValue($text, '/^ROC temperature\(Degree Celsius\)\s*=\s*([0-9]+)\s*$/mi');
  $rocTemp = ($rocTemp === null) ? null : (int)$rocTemp;

  return [
    'id' => (int)$c,
    'ok' => true,
    'roc_c' => $rocTemp,
    'support_temp' => ($support === 'Yes'),
    'sensor_roc' => $sensorRoc,
    'sensor_controller' => $sensorCtl,
  ];
}

$cParam = isset($_GET['c']) ? $_GET['c'] : null;

$controllers = [];
if ($cParam !== null && preg_match('/^\d+$/', (string)$cParam)) {
  $controllers = [(int)$cParam];
} else {
  $controllers = getControllers($storcli);
}

$results = [];
foreach ($controllers as $c) {
  $results[] = readController($storcli, $c);
}

$now = time();
respond([
  'ok' => true,
  'controllers' => $results,
  'ts' => $now,
  'ts_local' => date('Y-m-d H:i:s', $now),
]);
