RFID Reader
=========

A simple 125KHz rfid reader based on a ATMega8

Build around a single timer (Timer1) uses 2 interrupts (INT0 and TIMER1_COMPA) transmits data via UART
Manchested code decoding based on Timming approach

------

Input
-------
PD2 (INT0) - Input of Card data (after apmlifier)

Outputs
-------
PB1 - Carrier wave (125KHz square)
PD1 - UART TX

PCB
------
Single layer thru-hole in KiCad format


---
**Thanks to:**

Based on concept from: [Vassilis Serasidis](http://www.serasidis.gr/circuits/RFID_reader/125kHz_RFID_reader.htm)