#!/usr/bin/perl

$N = <>;

for ($i=0; $i<$N; ++$i) {
	$t = int(<>);

	if ($t % 15 == 0) {
		print "FizzBuzz\n";
	} elsif ($t % 5 == 0) {
		print "Buzz\n";
	} elsif ($t % 3 == 0) {
		print "Fizz\n";
	} else {
		print $t . "\n";
	}
}
