object Main extends App {
	val scanner = new java.util.Scanner(System.in)
	val N = scanner.nextInt

	(1 to N).foreach { _ =>
		val t = scanner.nextInt
		
		if (t % 15 == 0) {
			println("FizzBuzz")
		} else if (t % 5 == 0) {
			println("Buzz")
		} else if (t % 3 == 0) {
			println("Fizz")
		} else {
			println(t)
		}

	}
}
