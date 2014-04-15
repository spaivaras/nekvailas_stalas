EESchema Schematic File Version 2  date Mon 14 Apr 2014 11:05:24 PM EEST
LIBS:power
LIBS:device
LIBS:transistors
LIBS:conn
LIBS:linear
LIBS:regul
LIBS:74xx
LIBS:cmos4000
LIBS:adc-dac
LIBS:memory
LIBS:xilinx
LIBS:special
LIBS:microcontrollers
LIBS:dsp
LIBS:microchip
LIBS:analog_switches
LIBS:motorola
LIBS:texas
LIBS:intel
LIBS:audio
LIBS:interface
LIBS:digital-audio
LIBS:philips
LIBS:display
LIBS:cypress
LIBS:siliconi
LIBS:opto
LIBS:atmel
LIBS:contrib
LIBS:valves
EELAYER 43  0
EELAYER END
$Descr A4 11700 8267
encoding utf-8
Sheet 1 1
Title ""
Date "14 apr 2014"
Rev ""
Comp ""
Comment1 ""
Comment2 ""
Comment3 ""
Comment4 ""
$EndDescr
$Comp
L GND #PWR01
U 1 1 534C3EB9
P 6400 4050
F 0 "#PWR01" H 6400 4050 30  0001 C CNN
F 1 "GND" H 6400 3980 30  0001 C CNN
	1    6400 4050
	1    0    0    -1  
$EndComp
Text Label 6500 4150 0    60   ~ 0
B2
Text Label 6300 4550 0    60   ~ 0
B1
Text Label 5100 4150 0    60   ~ 0
A2
Text Label 4900 4550 0    60   ~ 0
A1
Wire Wire Line
	5100 4550 4900 4550
Wire Wire Line
	4900 4550 4900 4050
Wire Wire Line
	6300 4050 6300 4550
Wire Wire Line
	6300 4550 6500 4550
Wire Wire Line
	6500 4150 6500 4050
Wire Wire Line
	5100 4150 5100 4050
$Comp
L C C2
U 1 1 534C2DBC
P 6500 4350
F 0 "C2" H 6550 4450 50  0000 L CNN
F 1 "C" H 6550 4250 50  0000 L CNN
	1    6500 4350
	1    0    0    -1  
$EndComp
$Comp
L C C1
U 1 1 534C2DB2
P 5100 4350
F 0 "C1" H 5150 4450 50  0000 L CNN
F 1 "C" H 5150 4250 50  0000 L CNN
	1    5100 4350
	1    0    0    -1  
$EndComp
$Comp
L CONN_3 K1
U 1 1 534C2416
P 6400 3700
F 0 "K1" V 6350 3700 50  0000 C CNN
F 1 "CONN_3" V 6450 3700 40  0000 C CNN
	1    6400 3700
	0    -1   -1   0   
$EndComp
$Comp
L CONN_2 P1
U 1 1 534C1E6B
P 5000 3700
F 0 "P1" V 4950 3700 40  0000 C CNN
F 1 "CONN_2" V 5050 3700 40  0000 C CNN
	1    5000 3700
	0    -1   -1   0   
$EndComp
$EndSCHEMATC
