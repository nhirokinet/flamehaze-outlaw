import java.util.*;

public class Main {
        public static void main(String[] args){
                Scanner sc = new Scanner(System.in);

                int T = sc.nextInt();

		for (int i = 0; i < T; ++i) {
                	int input = sc.nextInt();

			if (input % 15 == 0) {
				System.out.println("FizzBuzz");
			} else if (input % 3 == 0) {
				System.out.println("Fizz");
			} else if (input % 5 == 0) {
				System.out.println("Buzz");
			} else {
				System.out.println(input);
			}
		}
        }
}

