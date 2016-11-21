#include <stdio.h>

int main() {
        int N;
        scanf("%d", &N);

        for (int i = 0; i < N; ++i) {
                int t;
                scanf("%d", &t);

                if(t % 15 == 0) {
                        printf("FizzBuzz\n");
                } else if(t % 5 == 0) {
                        printf("Buzz\n");
                } else if(t % 3 == 0) {
                        printf("Fizz\n");
                } else {
                        printf("%d\n", t);
                }
        }
}
