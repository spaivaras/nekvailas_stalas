/*
 * RFID.c
 *
 * Created: 2014-04-11 22:03:34
 *  Author: Aivaras
 */ 


#include "global.h"

/************************************************************************/
/* Send vendor and serial data via uart                                 */
/************************************************************************/
void announceData()
{
	char string[12];
		
	uart_puts("User detected: \n\r");
	uart_puts("Vendor ID: ");
	
	ultoa(getVendorData(), string, 10);
	uart_puts(string);
	
	uart_puts("\n\rUser ID: ");
	
	ultoa(getSerialData(), string, 10);
	uart_puts(string);
	
	uart_puts("\n\r");	
}

int main(void)
{	
	initCarier();
	initUart();
	
	//Enable global interrupts
	sei();
	
    while(1)
    {
		if (readPoll()) {
			announceData();
			
			//After successful RFID read readPoll disables global interrupts
			sei();
		}
    }
}