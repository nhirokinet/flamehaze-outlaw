<?php

$N = (int)(trim(fgets(STDIN)));

for ($i = 0; $i < $N; ++$i) {
	$t = (int)(trim(fgets(STDIN)));

	if ($t % 15 == 0) {
		print "FizzBuzz\n";
	} else if ($t % 5 == 0) {
		print "Buzz\n";
	} else if ($t % 3 == 0) {
		print "Fizz\n";
	} else {
		print $t . "\n";
	}
}
