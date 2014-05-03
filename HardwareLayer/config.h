#ifndef _CONFIG_H_
#define _CONFIG_H_

	
#define PROJECT_NAME "nekvailas_stalas"
#define REMOTE_API_URL "http://wonderwall/kickertable/api/v1/event"
#define LOGGER_SYSLOG


#define RETRY_COUNT 10

//Gyro stuff
#define MOTION_DURATION_TRESHOLD 1 // 1 = 1ms
#define MOTION_FORCE_TRESHOLD 3 // 1 = 2mg
#define MOTION_INTERRUPT_PIN 15 //WiringPi notation

//CardReader stuff
#define READER_0_INTERRUPT_PIN 7
#define READER_1_INTERRUPT_PIN 0
#define READER_2_INTERRUPT_PIN 2
#define READER_3_INTERRUPT_PIN 3

//Goal tracking stuff
#define GOAL_0_INTERRUPT_PIN 16
#define GOAL_1_INTERRUPT_PIN 1
#define GOAL_2_INTERRUPT_PIN 4
#define GOAL_3_INTERRUPT_PIN 5

//Beeping
#define BEEPER_PIN 6
#define BEEPER_DELAY_MS 60 

#endif

