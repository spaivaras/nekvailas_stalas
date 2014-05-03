#include <stdio.h>
#include <pthread.h>
#include <string.h>
#include "logger.h"
#include "sleep.h"
#include "MorseCoder.h"


MorseCoder::MorseCoder(int dot_delay_ms, void(*callback)(int)) {
	is_async_send_in_progress = false;
	unit_delay_ms = dot_delay_ms;
	user_callback = callback;
	setupCharactersTable();
}
void MorseCoder::setupCharactersTable() {
	int i;
	// set error code to all characters
	for (i=0; i<256; i++) {
		morse_char_map[i] = (char*)"........"; 
	}

	// define letters
	morse_char_map['A'] = morse_char_map['a'] = (char*)".-";
	morse_char_map['B'] = morse_char_map['b'] = (char*)"-...";
	morse_char_map['C'] = morse_char_map['c'] = (char*)"-.-.";
	morse_char_map['D'] = morse_char_map['d'] = (char*)"-..";
	morse_char_map['E'] = morse_char_map['e'] = (char*)".";
	morse_char_map['F'] = morse_char_map['f'] = (char*)"..-.";
	morse_char_map['G'] = morse_char_map['g'] = (char*)"--.";
	morse_char_map['H'] = morse_char_map['h'] = (char*)"....";
	morse_char_map['I'] = morse_char_map['i'] = (char*)"..";
	morse_char_map['J'] = morse_char_map['j'] = (char*)".---";
	morse_char_map['K'] = morse_char_map['k'] = (char*)"-.-";
	morse_char_map['L'] = morse_char_map['l'] = (char*)".-..";
	morse_char_map['M'] = morse_char_map['m'] = (char*)"--";
	morse_char_map['N'] = morse_char_map['n'] = (char*)"-.";
	morse_char_map['O'] = morse_char_map['o'] = (char*)"---";
	morse_char_map['P'] = morse_char_map['p'] = (char*)".--.";
	morse_char_map['Q'] = morse_char_map['q'] = (char*)"--.-";
	morse_char_map['R'] = morse_char_map['r'] = (char*)".-.";
	morse_char_map['S'] = morse_char_map['s'] = (char*)"...";
	morse_char_map['T'] = morse_char_map['t'] = (char*)"-";
	morse_char_map['U'] = morse_char_map['u'] = (char*)"..-";
	morse_char_map['V'] = morse_char_map['v'] = (char*)"...-";
	morse_char_map['W'] = morse_char_map['w'] = (char*)".--";
	morse_char_map['X'] = morse_char_map['x'] = (char*)"-..-";
	morse_char_map['Y'] = morse_char_map['y'] = (char*)"-.--";
	morse_char_map['Z'] = morse_char_map['z'] = (char*)"--..";

	// define numbers
	morse_char_map['0'] = (char*)"-----";
	morse_char_map['1'] = (char*)".----";
	morse_char_map['2'] = (char*)"..---";
	morse_char_map['3'] = (char*)"...--";
	morse_char_map['4'] = (char*)"....-";
	morse_char_map['5'] = (char*)".....";
	morse_char_map['6'] = (char*)"-....";
	morse_char_map['7'] = (char*)"--...";
	morse_char_map['8'] = (char*)"---..";
	morse_char_map['9'] = (char*)"----.";

	// define punctuation
	morse_char_map['.'] = (char*)".-.-.-";
	morse_char_map[','] = (char*)"--..--";
	morse_char_map['?'] = (char*)"..--..";
	morse_char_map['\''] = (char*)".----.";
	morse_char_map['!'] = (char*)"-.-.--";
	morse_char_map['/'] = (char*)"-..-.";
	morse_char_map['('] = (char*)"-.--.";
	morse_char_map[')'] = (char*)"-.--.-";
	morse_char_map['&'] = (char*)".-...";
	morse_char_map[':'] = (char*)"---...";
	morse_char_map[';'] = (char*)"-.-.-.";
	morse_char_map['='] = (char*)"-...-";
	morse_char_map['+'] = (char*)".-.-.";
	morse_char_map['-'] = (char*)"-....-";
	morse_char_map['_'] = (char*)"..--.-";
	morse_char_map['"'] = (char*)".-..-.";
	morse_char_map['$'] = (char*)"...-..-";
	morse_char_map['@'] = (char*)".--.-.";

}

int MorseCoder::sendAsync(const char* message) {
	if (is_async_send_in_progress) {
		return -1;
	}
	is_async_send_in_progress = true;

	strcpy(msg, message);

	int rc = pthread_create(&sender_thread, NULL, senderThreadEntryPoint, this);
	if (rc < 0) {
		ERR("Error - pthread_create() return code: %d", rc);
	}
	INFO("sender thread constructed: %lu", sender_thread);
	pthread_detach(sender_thread);

	return 0;
}
int MorseCoder::send() {
	int rc = send(msg);
	is_async_send_in_progress = false;
	return rc;
}
int MorseCoder::send(const char* message) {
	int mi,len;
	char word[256] = "";
	for (mi=0; message[mi] != '\0'; mi++) {
		if (message[mi] != ' ') {
			len = strlen(word);
			word[len] = message[mi];
			word[len+1] = '\0';
		} else {
			if (strlen(word) > 0) {
				sendWord(word);
			}
			word[0] = '\0';
		}
	}
	if (strlen(word) > 0) {
		sendWord(word);
	}

	transmitEndOfTransmission(); 
	return 0;
}


void MorseCoder::sendWord(const char* word) {
	int i;
	for (i=0; word[i] != '\0'; i++) {
		sendLetter(word[i]);
	}
	transmitWordSpace();
}
void MorseCoder::sendLetter(const char letter) {
	unsigned int i;
	char* code = morse_char_map[(unsigned)letter];
	for	(i=0; i<strlen(code); i++) {
		switch (code[i]) {
			case '.':
				transmitDot();
				break;
			case '-':
				transmitDash();
				break;
			default:
				WARNING("Got unknown char(%c)", code[i]);
				transmitSymbol(ON,10);
				transmitSymbol(OFF,10);
				transmitSymbol(ON,10);
				transmitSymbol(OFF,10);
		}
	}
	transmitLetterSpace();
}
void MorseCoder::transmitDot() {
	transmitSymbol(ON, 1);
	transmitSymbolSpace();
}
void MorseCoder::transmitDash() {
	transmitSymbol(ON, 3);
	transmitSymbolSpace();
}
void MorseCoder::transmitSymbolSpace() {
	transmitSymbol(OFF, 1);
}
void MorseCoder::transmitLetterSpace() {
	transmitSymbol(OFF, 3-1); // one symbol-space is included after each dot/dash
}
void MorseCoder::transmitWordSpace() {
	transmitSymbol(OFF, 7-3); // one letter-space is included after each letter
}
void MorseCoder::transmitEndOfTransmission() {
	transmitSymbol(OFF, 13-7); // I invented this timming myself
}
void MorseCoder::transmitSymbol(int state, int ticks) {
	user_callback(state);
	msleep(unit_delay_ms * ticks);
}




