N = (STDIN.gets.chomp).to_i

for i in 1..N do
	t = (STDIN.gets.chomp).to_i

	if t % 15 == 0 then
		puts "FizzBuzz"
	elsif t % 5 == 0 then
		puts "Buzz"
	elsif t % 3 == 0 then
		puts "Fizz"
	else
		puts t
	end
end
