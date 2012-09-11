<?php

$sourceFile = __DIR__.'/../xupdate.yaml';
$iniFile    = __DIR__.'/../xupdate.ini';
$localizedIniFile = __DIR__.'/../xupdate/language/%s/xupdate.ini';
$readmeTemplate = __DIR__.'/README.template.md';
$readmeFile     = __DIR__.'/../README.md';

$languageMap = [
	'ja' => 'ja_utf8',
];

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
	$preload['description'] = array_merge($default['description'], $preload['description']);
	$preload['tag'] = array_merge($default['tag'], $preload['tag']);
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
		if ( in_array($key, ['description', 'tag']) )
		{
			$value = $value['en'];
		}

		if ( is_bool($value) or is_null($value) )
		{
			$value = var_export($value, true);
		}
		else
		{
			$value = sprintf('"%s"', addslashes($value));
		}

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

// create localized ini string
foreach ( $languageMap as $langcode => $languageName )
{
	$localizedIniString = '';

	foreach ( $data as $name => $preload )
	{
		$localizedIniString .= sprintf("[%s]\n", $name);
		$localizedIniString .= sprintf("description = \"%s\"\n", addslashes($preload['description'][$langcode]));
		$localizedIniString .= sprintf("tag = \"%s\"\n", addslashes($preload['tag'][$langcode]));
		$localizedIniString .= "\n";
	}

	// lint ini string
	if ( parse_ini_string($localizedIniString, true) === false )
	{
		throw new RuntimeException('Failed to parse localized ini string: '.$langcode);
	}

	// create localized ini file
	file_put_contents(sprintf($localizedIniFile, $languageName), $localizedIniString);
}

// create index string
$index = [];

foreach ( $data as $name => $preload )
{
	$index[] = sprintf("### [%s](%s)\n%s", $preload['dirname'], $preload['detail_url'], $preload['description']['ja']);
}

$index = implode("\n", $index);

// create README
$readme = file_get_contents($readmeTemplate);
$readme = str_replace('{index}', $index, $readme);
file_put_contents($readmeFile, $readme);