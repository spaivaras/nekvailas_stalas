#ifndef _TABLEEVENTSQUEUE_H_
#define _TABLEEVENTSQUEUE_H_

class TableEventsQueue {
		static const int MSG_KEY = 1111;
		static const int MSG_TYPE = 1;
		static const int MSG_LEN = 256;
		static const int MSG_PERM = 0777;
		int msgq_id;
		struct msg_buf {
			long mtype;
			char mtext[MSG_LEN];
		} msg;
	public:
		TableEventsQueue();
		~TableEventsQueue();
		void addTableShakeEvent();
		void addTableShakeEvent(uint32_t power);
		void addCardSwipeEvent(uint8_t team, uint8_t player, uint32_t card_id);
	private:
		void addEvent(const char* type, const char* payload);
		void sendMessage(const char* message);
};

#endif
