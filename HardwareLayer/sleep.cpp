#include <time.h>
#include "sleep.h"

int nsleep(long ns) {
	struct timespec req={0};
	time_t sec=(int)(ns/1000000000L);
	ns=ns-(sec*1000000000L);
	req.tv_sec=sec;
	req.tv_nsec=ns;
	return nanosleep(&req, NULL);
}

int usleep(long us) {
	return nsleep(us*1000);
}

int msleep(long ms) {
	return nsleep(ms*1000*1000);
}

int sleep(long s) {
	return nsleep(s*1000*1000*1000);
}

