RFID Reader
=========

A simple 125KHz rfid reader based on a ATMega8

Build around a single timer (Timer1) uses 2 interrupts (INT0 and TIMER1_COMPA) transmits data via UART
Manchester code decoding based on Timing approach. Base clock 8MHz

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
Single layer through-hole in KiCad format
 - Drill: 1 mm
 - Pads: 2mm
 - Tracks 0.4 mm


---
**Thanks to:**

Based on concept from: [Vassilis Serasidis](http://www.serasidis.gr/circuits/RFID_reader/125kHz_RFID_reader.htm)