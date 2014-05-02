
#include <stdio.h>
#include <stdint.h>
#include <unistd.h>
#include <stdlib.h>
#include <wiringPi.h>

#include "sleep.h"


void morseCallback(int state) {
	// set pin state
	printf("%d",state);
}


#ifndef _MORSECODER_H_
#define _MORSECODE_H_

class MorseCoder {
		static const int ON = 1;
		static const int OFF = 0;
		int dot_delay_ms;
		//void* callback;
		void(*callback)(int state);
		char *morse_char_map[256];
	public:
		MorseCoder(int dot_delay_ms, void(*callback)(int state));
        ~MorseCoder() {}
        int send(const char* message);
    private:
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


int MorseCoder::send(const char* message) {
	printf("%s(%s)\n", __FUNCTION__, message);
	// split string on spaces
	// submit every word to
	transmitEndOfTransmission(); 
	return 0;
}
void MorseCoder::sendWord(const char* word) {
	int i = 0;
	while (word[i] != '\0') {
		sendLetter(word[i]);
	}
	transmitWordSpace();
}
void MorseCoder::sendLetter(const char letter) {
	int i;
	for	(i=0; i<9; i++) {
		switch (morse_char_map[letter][i]) {
			case '.':
				transmitDot();
				break;
			case '-':
				transmitDash();
				break;
			case '\0':
				continue;
			default:
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
	printf("%s()\n", __FUNCTION__);
	transmitSymbol(OFF, 13-7); // I invented this timming myself
}
void MorseCoder::transmitSymbol(int state, int ticks) {
	printf("%s(%d, %d);\n", __FUNCTION__, state, ticks);
	(*this->callback)(state);
	printf("--- WE(%s) go past callback\n", __FUNCTION__);
	msleep(dot_delay_ms * ticks);
}



MorseCoder::MorseCoder(int dot_delay_ms, void(*callback)(int)) {
	dot_delay_ms = dot_delay_ms;
	callback = callback;
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

}




int main() {
	// setup pin
	MorseCoder* mc = new MorseCoder(60, morseCallback);
	printf("Sending something\n");
	mc->send("MORSE");
	printf("done sending\n");
	return 0;
}


