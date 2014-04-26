#ifndef _EVENTSPUSHER_H_
#define _EVENTSPUSHER_H_

class EventsPusher {
		int MSG_KEY;
		static const int MSG_TYPE = 1;
		static const int MSG_LEN = 256;
		static const int MSG_PERM = 0777;
		int msgq_id;
		struct msg_buf {
			long mtype;
			char mtext[MSG_LEN];
		} msg;
		pthread_t pusher_thread;
	public:
		EventsPusher(int msg_key);
		~EventsPusher();
        static void* pusherThreadStaticEntryPoint(void *arg) { ((EventsPusher*)arg)->pusherThreadEntryPoint(); return NULL; };
		void pusherThreadEntryPoint();
//		void flusherThreadEntryPoint();
//	private:

};

#endif


