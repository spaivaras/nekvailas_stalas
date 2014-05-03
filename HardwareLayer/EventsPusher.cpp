#include "config.h"

#include <stdio.h> // sprintf and friends
#include <stdint.h> // uint?_t types
#include <stdlib.h> // NULL
#include <string.h> // memcpy
#include <sys/msg.h> // msgget msgsnd ...
#include <pthread.h>
#include <curl/curl.h>
#include "logger.h"
#include "EventsPusher.h"


EventsPusher::EventsPusher(int msg_key) {
	MSG_KEY = msg_key;

	// setup msgq
	msgq_id = msgget(MSG_KEY, MSG_PERM|IPC_CREAT);
	if (msgq_id < 0) {
		ERR("failed to create message queue with msgqid = %d", msgq_id);
	}
	INFO("pusher constructed: %d", msgq_id);

	// fire up pusher thread
	int return_code = pthread_create( 
		&pusher_thread, 
		NULL, 
		EventsPusher::pusherThreadStaticEntryPoint, 
		this
	);
	if(return_code < 0)
	{
		ERR("Error - pthread_create() return code: %d", return_code);
	}
	INFO("pusher thread constructed: %lu", pusher_thread);
}

EventsPusher::~EventsPusher() {
	// sould join in all threads

	// freeing up curl_buffer mem
	if(curl_buffer.memory)
		free(curl_buffer.memory);

	INFO("destructing pusher");
}

void EventsPusher::pusherThreadEntryPoint() {
	DEBUG("Pusher Thread got created");
	char message[MSG_LEN+2];
	while (true) {
		readMessage();
		sprintf(message, "[%s]", msg.mtext);
		pushMessage(message);
	}
}

int EventsPusher::readMessage() {
	DEBUG("Pusher thread starts to read...");
	// read the message from queue
	int return_code = msgrcv(msgq_id, &msg, sizeof(msg.mtext), 0, 0); 
	if (return_code < 0) {
		ERR("msgrcv failed, return_code=%d", return_code);
		return -1;
	} 
	INFO("received msg: %s", msg.mtext);
	return 0;
}
int EventsPusher::pushMessage(char *message) {
	DEBUG("- - - Its curl time!");

	CURL *curl;
	CURLcode res;

	struct curl_slist *headers = NULL;
    headers = curl_slist_append(headers, "Content-Type: application/json");

	// make sure we starting fresh on empty buffer
	curlFreeBuffer(&curl_buffer);

	curl = curl_easy_init();
	if(curl) {
		curl_easy_setopt(curl, CURLOPT_URL, REMOTE_API_URL);
		curl_easy_setopt(curl, CURLOPT_POSTFIELDS, message);
		curl_easy_setopt(curl, CURLOPT_HEADER, 1);
		//curl_easy_setopt(curl, CURLOPT_NOBODY, 1);
		curl_easy_setopt(curl, CURLOPT_TIMEOUT, 2);

		curl_easy_setopt(curl, CURLOPT_NOPROGRESS, 1L);
		curl_easy_setopt(curl, CURLOPT_NOSIGNAL, 1L);
		curl_easy_setopt(curl, CURLOPT_USERAGENT, PROJECT_NAME);
		curl_easy_setopt(curl, CURLOPT_HTTPHEADER, headers);

		curl_easy_setopt(curl, CURLOPT_WRITEFUNCTION, curlCallback);
		curl_easy_setopt(curl, CURLOPT_WRITEDATA, (void *)&curl_buffer);

		res = curl_easy_perform(curl);
		curl_easy_cleanup(curl);

		if(res != CURLE_OK) {
			ERR("curl failed: %s", curl_easy_strerror(res));
			return -1;
		} else {
			INFO("Got data from server:\n%s", curl_buffer.memory);
		}
	}

	// check the header
	// X-TableEventStored: 1

	return 0;
}

void EventsPusher::curlFreeBuffer(struct CurlBufferStruct *b) {
	free(b->memory);
	b->memory = NULL;
	b->size = 0;
}

size_t EventsPusher::curlCallback(void *chunk, size_t size, size_t nmemb, void *buffer)
{
	size_t realsize = size * nmemb;
	struct CurlBufferStruct *mem = (struct CurlBufferStruct *)buffer;

	mem->memory = (char*)realloc(mem->memory, mem->size + realsize + 1);
	if(mem->memory == NULL) {
		ERR("not enough memory (realloc returned NULL)");
		return 0;
	}

	memcpy(&(mem->memory[mem->size]), chunk, realsize);
	mem->size += realsize;
	mem->memory[mem->size] = 0;

	return realsize;
}

