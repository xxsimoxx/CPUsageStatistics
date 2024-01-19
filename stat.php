#!/usr/bin/env php
<?php

require_once 'vendor/autoload.php';

use BenMorel\ApacheLogParser\Parser;
use dekor\ArrayToTextTable;

if (!isset($argv[1])) {
	help('Please specify a file.');
	exit;
}

$log_file = $argv[1];
if (!is_readable($log_file)) {
	help('Can\'t access '.$log_file.'.');
	exit;
}

$log_handle = fopen($log_file, 'r');
if (!$log_handle) {
	help('Can\'t read '.$log_file.'.');
}

$spammers_ip = [
	'183.90.183.160',
	'183.90.182.153',
	'183.90.183.156',
];

$data = [];
$end_time = strtotime('01/Jan/2000:00:00:00 +0000');
$start_time   = strtotime('31/Dec/3000:23:59:59 +0000');
$hosts = [];

$log_format = '%h %l %u %t "%r" %>s %O "%{Referer}i" "%{User-Agent}i"'; //combined
$log_parser = new Parser($log_format);

while (($line = fgets($log_handle)) !== false) {

	$parsed_line = $log_parser->parse($line, true);

	if (preg_match('~^GET (?:/v1)?/upgrade/(.*)\.json~', $parsed_line['firstRequestLine'], $matches) !== 1) {
		continue; // Skip requests that are not for updates.
	}

	if (in_array($parsed_line['remoteHostname'], $spammers_ip)) {
		continue; // Skip spammers
	}

	parse_str($parsed_line['firstRequestLine'], $request);
	parse_str($parsed_line['requestHeader:User-Agent'], $agent);

	if (!isset($agent['site']) || !isset($agent['ver']) || !isset($request['php']) || !isset($request['multisite_enabled']) || !isset($request['locale'])) {
		continue;
	}

	$data[$agent['site']] = [
		'version'      => $agent['ver'],
		'fullversion'  => $matches[1] ?? 'strange',
		'shortversion' => majorminor($agent['ver']),
		'php'          => $request['php'],
		'shortphp'     => majorminor($request['php']),
		'multisite'    => $request['multisite_enabled'],
		'locale'       => $request['locale'],
		'ip'           => $parsed_line['remoteHostname'],
	];

	$when = strtotime($parsed_line['time']);
	if ($when < $start_time) {
		$start_time = $when;
	}

	if ($when > $end_time) { //phpcs:ignore SlevomatCodingStandard.ControlStructures.EarlyExit.EarlyExitNotUsed
		$end_time = $when;
	}

}

fclose($log_handle);

$fields = [
	'version',      // CP version from User Agent.
	'fullversion',  // CP version from API endpoint.
//	'shortversion', // CP version from User Agent, shortened to major.minor.
//	'php',          // PHP version from the request.
	'shortphp',     // PHP version from the request, shortened to major.minor.
	'multisite',    // 0 for single, 1 for multisite.
	'locale',       // Locale.
//	'ip',           // IP address.
];

$stats = [];

foreach ($data as $values) {
	foreach ($fields as $field) {
		if (!isset($stats[$field][$values[$field]])) {
			$stats[$field][$values[$field]] = 0;
		}
		$stats[$field][$values[$field]]++;
	}
}

echo 'From '.date('d/m/Y H:i', $start_time).' to '.date('d/m/Y H:i', $end_time).'.'."\n";
echo '------------------------------------------'."\n\n";

foreach ($fields as $field) {
	render_data($stats, $field, $field);
}

function render_data($stats, $label, $readable) {
	$set = $stats[$label];
	arsort($set);
	$data = [];
	foreach ($set as $key => $value) {
		$data[] = [
			$readable => $key,
			'count' => $value,
		];
	}
	echo (new ArrayToTextTable($data))->render();
	echo "\n\n";
}

function majorminor($version) {
	if (preg_match('~^\d+\.\d+~', $version, $match) === 1) {
		return $match[0];
	}
	return $version;
}

function help($message = '') {
	echo $message."\n";
	echo basename(__FILE__).' <log_file>'."\n";
}
