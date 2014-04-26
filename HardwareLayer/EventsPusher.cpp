#include <stdio.h> // sprintf and friends
#include <stdint.h> // uint?_t types
#include <stdlib.h> // NULL
#include <sys/msg.h> // msgget msgsnd ...
#include <pthread.h>
#include "EventsPusher.h"


EventsPusher::EventsPusher(int msg_key) {
	MSG_KEY = msg_key;

	msgq_id = msgget(MSG_KEY, MSG_PERM|IPC_CREAT);
	if (msgq_id < 0) {
		printf("failed to create message queue with msgqid = %d\n", msgq_id);
	}
	printf("pusher constructed: %d\n", msgq_id);

	int return_code = pthread_create( 
		&pusher_thread, 
		NULL, 
		EventsPusher::pusherThreadStaticEntryPoint, 
		this
	);
	if(return_code < 0)
	{
		printf("Error - pthread_create() return code: %d\n", return_code);
	}
	printf("pusher thread constructed: %lu\n", pusher_thread);
}

EventsPusher::~EventsPusher() {
	printf("destructing pusher\n");
}

void EventsPusher::pusherThreadEntryPoint() {
	printf("Pusher Thread got created\n");

	while (true) {
		printf("Pusher thread starts to read...\n");
		// read the message from queue
		int return_code = msgrcv(msgq_id, &msg, sizeof(msg.mtext), 0, 0); 
		if (return_code < 0) {
			printf("msgrcv failed, return_code=%d\n", return_code);
		} 
		printf("received msg: %s\n", msg.mtext);

	}

}


