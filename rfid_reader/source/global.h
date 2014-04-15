/*
 * global.h
 *
 * Created: 2014-04-15 20:53:16
 *  Author: Aivaras
 */ 


#ifndef GLOBAL_H_
#define GLOBAL_H_

#ifndef F_CPU
	#define F_CPU 8000000UL
#endif

#define BAUD 9600UL

#include <avr/io.h>
#include <stdlib.h>
#include <avr/interrupt.h>
#include <util/delay.h>
#include <util/parity.h>

#include "UART/UART.h"
#include "Reader/Reader.h"

#endif /* GLOBAL_H_ */