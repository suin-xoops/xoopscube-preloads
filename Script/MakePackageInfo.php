<?php

$sourceFile = __DIR__.'/../xupdate.yaml';
$iniFile    = __DIR__.'/../xupdate.ini';
$readmeTemplate = __DIR__.'/README.template.md';
$readmeFile     = __DIR__.'/../README.md';

$preloads = yaml_parse_file($sourceFile);

// get default
foreach ( $preloads as $index => $preload )
{
	if ( $preload['dirname'] === 'default' )
	{
		$default = $preload;
		unset($preloads[$index]);
		break;
	}
}

// set up data
$data = [];

foreach ( $preloads as $index => $preload )
{
	$preload = array_merge($default, $preload);
	$preload['target_key'] = $preload['dirname'];
	$preload['addon_url']  = $default['addon_url'].'/'.$preload['dirname'].'/'.$preload['dirname'].'.class.php';
	$preload['detail_url'] = $default['detail_url'].'/'.$preload['dirname'].'/';
	$name = $preload['dirname'];
	$data[$name] = $preload;
}

// sort
ksort($data);

// create ini string
$iniString = '';

foreach ( $data as $name => $preload )
{
	$iniString .= sprintf("[%s]\n", $name);

	foreach ( $preload as $key => $value )
	{
		$value = var_export($value, true);
		$iniString .= sprintf("%s = %s\n", $key, $value);
	}

	$iniString .= "\n";
}

$iniString = trim($iniString);

// lint ini string
if ( parse_ini_string($iniString, true) === false )
{
	throw new RuntimeException('Failed to parse ini string');
}

// create ini file
file_put_contents($iniFile, $iniString);

// create index string
$index = [];

foreach ( $data as $name => $preload )
{
	$index[] = sprintf("### [%s](%s)\n%s", $preload['dirname'], $preload['detail_url'], $preload['description']);
}

$index = implode("\n", $index);

// create README
$readme = file_get_contents($readmeTemplate);
$readme = str_replace('{index}', $index, $readme);
file_put_contents($readmeFile, $readme);