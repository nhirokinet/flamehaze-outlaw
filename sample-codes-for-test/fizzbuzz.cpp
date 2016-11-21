#include <iostream>
#include <stdlib.h>

using namespace std;

int main() {
        int N;
        cin>>N;

        for(int i=0;i<N;++i){
                int t;
                cin>>t;

                if(t%15==0){
                        cout<<"FizzBuzz"<<endl;
                }else if(t%5==0){
                        cout<<"Buzz"<<endl;
                }else if(t%3==0){
                        cout<<"Fizz"<<endl;
                }else{
                        cout<<t<<endl;
                }
        }
}
