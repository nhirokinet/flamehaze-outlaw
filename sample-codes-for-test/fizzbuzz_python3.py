import sys

N = int(input())

for i in range(0, N):
        t = int(input())

        if t % 15 == 0:
                print ("FizzBuzz")
        elif t % 5 == 0:
                print ("Buzz")
        elif t % 3 == 0:
                print ("Fizz")
        else:
                print (t)
