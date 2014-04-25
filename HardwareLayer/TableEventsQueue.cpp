#include <stdio.h> // sprintf and friends
#include <stdint.h> // uint?_t types
#include <stdlib.h> // NULL
#include <sys/time.h> // gettimeofday
#include <sys/msg.h> // msgget msgsnd ...
#include "TableEventsQueue.h"


TableEventsQueue::TableEventsQueue() {
	// setup msgq
	msgq_id = msgget(IPC_PRIVATE, MSG_PERM|IPC_CREAT|IPC_EXCL);
	if (msgq_id < 0) {
		printf("failed to create message queue with msgqid = %d\n", msgq_id);
	}
	printf("constructed: %d\n", msgq_id);
}

TableEventsQueue::~TableEventsQueue() {
	// destroy msgq
	int return_code = msgctl(msgq_id, IPC_RMID, NULL);
	if (return_code < 0) {
		printf("msgctl (return queue) failed, return_code=%d\n", return_code);
	}
	printf("destructed: %d\n", msgq_id);
}

void TableEventsQueue::addTableShakeEvent() {
	addEvent("TableShake", "{}");
}

void TableEventsQueue::addTableShakeEvent(uint32_t power) {
	char payload[32];
	sprintf(payload, "{\"power\":%u}", power);
	addEvent("TableShake", payload);
}

void TableEventsQueue::addCardSwipeEvent(uint8_t team, uint8_t player, uint32_t card_id) {
	char payload[128];
	sprintf(payload, "{\"team\":%u,\"player\":%u,\"card_id\":%u}"
		, team, player, card_id);
	addEvent("CardSwipe", payload);
}

void TableEventsQueue::addEvent(const char* type, const char* payload) {
	struct timeval tv;
	gettimeofday(&tv, NULL);
	char message[256];
	sprintf(message, "{\"time\":{\"sec\":%ld,\"usec\":%u},\"type\":\"%s\",\"data\":%s}"
		, tv.tv_sec, tv.tv_usec, type, payload);
	sendMessage(message);
}

void TableEventsQueue::sendMessage(const char* message) {
	printf("%s\n",message);

	msg.mtype = MSG_TYPE;
	sprintf(msg.mtext, message);
	int return_code = msgsnd(msgq_id, &msg, sizeof(msg.mtext), IPC_NOWAIT); 

	if (return_code < 0) {
		printf("msgsnd failed, return_code = %d\n", return_code);
	}
}

