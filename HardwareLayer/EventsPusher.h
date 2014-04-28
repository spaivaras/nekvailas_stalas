#ifndef _EVENTSPUSHER_H_
#define _EVENTSPUSHER_H_

class EventsPusher {
		int MSG_KEY;
		static const int MSG_TYPE = 1;
		static const int MSG_LEN = 256;
		static const int MSG_PERM = 0777;
		int msgq_id;
		struct MessageBuffer {
			long mtype;
			char mtext[MSG_LEN];
		} msg;
		struct CurlBufferStruct {
			char *memory;
			size_t size;
		} curl_buffer;
		pthread_t pusher_thread;
	public:
		EventsPusher(int msg_key);
		~EventsPusher();
        static void* pusherThreadStaticEntryPoint(void *arg) { ((EventsPusher*)arg)->pusherThreadEntryPoint(); return NULL; };
		static size_t curlCallback(void *chunk, size_t size, size_t nmemb, void *buffer);
		void pusherThreadEntryPoint();
	private:
		int readMessage();
		int pushMessage(char *message);
		void curlFreeBuffer(struct CurlBufferStruct *b);
};

#endif


