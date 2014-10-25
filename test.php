<?php
// Unit testing without any library

spl_autoload_register(function ($class) {
	require __DIR__.'/class/'.$class.'.php';
});

$pass = $fail = 0;
foreach (glob(__DIR__."/test/*.php") as $testFile) {
	preg_match('/test\/(.*)\.php/', $testFile, $match);
	$className = $match[1];
	echo "\nEvaluating $className\n\n";

	require $testFile;
	$test = new $className();
	$reflection = new ReflectionObject($test);

	foreach ($reflection->getMethods() as $method) {
		if ($method->isConstructor()) continue;
		if ($method->isDestructor()) continue;

		echo "Case ".$method->getName().": ";
		$result = $method->invoke($test);
		if ($result === null) {
			$pass++;
			echo "SUCCEED\n";
		} else {
			$fail++;
			echo "FAILED\n";
			if (is_string($result)) echo "Reason: $result\n";
		}
	}
}


// Execute test suite

echo "\nSuccess: $pass, Failure: $fail, Total: ".($pass+$fail)."\n\n";