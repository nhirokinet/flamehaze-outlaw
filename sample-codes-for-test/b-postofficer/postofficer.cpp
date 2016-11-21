#include <iostream>
#include <vector>

using namespace std;

int inside_loop (long long arrive, long long speed, long long x, long long loop) {
	long long arrive_clock = arrive % (2 * loop * speed);
	if (arrive_clock < x * speed) {
		arrive_clock += loop * speed * 2;
	}

	return arrive_clock < (x + loop) * speed;
}

int main (){
	long long t, d, s, N;
	vector <long long> l, x, y;

	cin >> t >> d >> s >> N;

	for(long long i = 0; i < N; ++i){
		long long tl, tx, ty;
		cin >> tl >> tx >> ty;

		if (tx < 0) {
			tx += 2 * ty;
		}

		l.push_back(tl);
		x.push_back(tx);
		y.push_back(ty);
	}

	l.push_back(d);
	x.push_back(0);
	y.push_back(t + 1);

	long long lo = 1;
	long long hi = s + 1;

	while (lo < hi) {
		long long mid = (lo + hi) / 2;

		long long curpos = 0;
		long long arrive = 0;

		for (long long i = 0; i < l.size(); ++i) {
			arrive += l[i] - curpos;

			if (! inside_loop(arrive, mid, x[i], y[i])) {
				arrive = ((arrive - x[i] * mid + (2 * y[i] * mid) - 1) / (2 * y[i] * mid)) * (2 * y[i] * mid) + x[i] * mid;
			}
			curpos = l[i];
		}

		if (arrive > t * mid) {
			lo = mid + 1;
		} else {
			hi = mid; 
		}
	}

	if (lo > s) {
		cout << -1 << endl;
	} else {
		cout << lo << endl;
	}
}
