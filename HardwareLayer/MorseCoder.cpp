
#ifndef _MORSECODER_H_
#define _MORSECODE_H_

class MorseCoder {
		static const int ON = 1;
		static const int OFF = 0;
		int dot_delay_ms;
		void* callback;
		char[256][8] morse_char_map;
	public:
		MorseCoder(int dot_delay_ms, void* callback);
        ~MorseCoder() {}
        int send(char* message);
    private:
		void sendLetter(char letter);
		void transmitDot();
		void transmitDash();
		void transmitSymbolSpace();
		void transmitLetterSpace();
		void transmitWordSpace();
		void transmitEndOfTransmission();
		void transmitSymbol(int state, int ticks);
#endif


int MorseCoder::send(char* message) {
	// split string on spaces
	// submit every word to
	transmitEndOfTransmission(); 
	return 0;
}
void MorseCoder::sendWord(char* word) {
	int i = 0;
	while (word[i] != '\0') {
		sendLetter(word[i]);
	}
	transmitWordSpace();
}
void MorseCoder::sendLetter(char letter) {
	int i;
	for	(i=0; i<8; i++) {
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
	transmitSymbol(OFF, 13-7); // I invented this timming myself
}
void MorseCoder::transmitSymbol(int state, int ticks) {
	callback(state);
	msleep(dot_delay_ms * ticks);
}



MorseCoder::MorseCoder(int dot_delay_ms, void* callback) {
	this.dot_delay_ms = dot_delay_ms;
	this.callback = callback;
	setupCharactersTable();
}
void MorseCoder::setupCharactersTable() {
	int i;
	// set error code to all characters
	morse_char_map[i] = {"........"}; 

	// define letters
	morse_char_map['A'] = morse_char_map['a'] = ".-";
	morse_char_map['B'] = morse_char_map['b'] = "-...";
	morse_char_map['C'] = morse_char_map['c'] = "-.-.";
	morse_char_map['D'] = morse_char_map['d'] = "-..";
	morse_char_map['E'] = morse_char_map['e'] = ".";
	morse_char_map['F'] = morse_char_map['f'] = "..-.";
	morse_char_map['G'] = morse_char_map['g'] = "--.";
	morse_char_map['H'] = morse_char_map['h'] = "....";
	morse_char_map['I'] = morse_char_map['i'] = "..";
	morse_char_map['J'] = morse_char_map['j'] = ".---";
	morse_char_map['K'] = morse_char_map['k'] = "-.-";
	morse_char_map['L'] = morse_char_map['l'] = ".-..";
	morse_char_map['M'] = morse_char_map['m'] = "--";
	morse_char_map['N'] = morse_char_map['n'] = "-.";
	morse_char_map['O'] = morse_char_map['o'] = "---";
	morse_char_map['P'] = morse_char_map['p'] = ".--.";
	morse_char_map['Q'] = morse_char_map['q'] = "--.-";
	morse_char_map['R'] = morse_char_map['r'] = ".-.";
	morse_char_map['S'] = morse_char_map['s'] = "...";
	morse_char_map['T'] = morse_char_map['t'] = "-";
	morse_char_map['U'] = morse_char_map['u'] = "..-";
	morse_char_map['V'] = morse_char_map['v'] = "...-";
	morse_char_map['W'] = morse_char_map['w'] = ".--";
	morse_char_map['X'] = morse_char_map['x'] = "-..-";
	morse_char_map['Y'] = morse_char_map['y'] = "-.--";
	morse_char_map['Z'] = morse_char_map['z'] = "--..";

	// define numbers
	morse_char_map['0'] = "-----";
	morse_char_map['1'] = ".----";
	morse_char_map['2'] = "..---";
	morse_char_map['3'] = "...--";
	morse_char_map['4'] = "....-";
	morse_char_map['5'] = ".....";
	morse_char_map['6'] = "-....";
	morse_char_map['7'] = "--...";
	morse_char_map['8'] = "---..";
	morse_char_map['9'] = "----.";

}




int main() {
	// setup pin
	MorseCoder* mc = new MorseCoder(60, morseCallback);
	printf("Sending something\n");
	mc.send("MORSE");
	printf("done sending\n");
}
void morseCallback(int state) {
	// set pin state
}


