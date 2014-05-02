#ifndef _MORSECODER_H_
#define _MORSECODE_H_

class MorseCoder {
		static const int ON = 1;
		static const int OFF = 0;
		int unit_delay_ms;
		void(*user_callback)(int state);
		char *morse_char_map[256];
		pthread_t sender_thread;
		char msg[4097];
		bool is_async_send_in_progress;
	public:
		MorseCoder(int unit_delay_ms, void(*callback)(int state));
        ~MorseCoder() {}
        int send(const char* message);
        int sendAsync(const char* message);
		static void* senderThreadEntryPoint(void *arg) { ((MorseCoder*)arg)->send(); return NULL; };
    private:
		int send();
		void setupCharactersTable();
		void sendWord(const char* word);
		void sendLetter(const char letter);
		void transmitDot();
		void transmitDash();
		void transmitSymbolSpace();
		void transmitLetterSpace();
		void transmitWordSpace();
		void transmitEndOfTransmission();
		void transmitSymbol(int state, int ticks);
};
#endif


