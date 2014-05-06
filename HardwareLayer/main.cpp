#include "config.h"

#include <stdio.h>
#include <stdint.h>
#include <unistd.h>
#include <stdlib.h>
#include <wiringPi.h>
#include <signal.h>
#include <string.h> // strsignal()
#include <ctime>

#include "logger.h"
#include "I2Cdev.h"
#include "TableEventsQueue.h"
#include "MorseCoder.h"
#include "Gyro.h"
#include "sleep.h"


volatile sig_atomic_t is_it_good_time_to_die = false;

TableEventsQueue Q;
Gyro gyro (&Q);
MorseCoder *MC;

time_t goalKeeper0 = time(NULL);
time_t goalKeeper1 = time(NULL);


void beeperCallback(int state) {
    digitalWrite(BEEPER_PIN, state);
}

void motionInterrupt() {
    return gyro.motionInterrupt();
}

uint32_t getUserData(uint8_t address) {
    uint8_t buffer[4] = {0x00, 0x00, 0x00, 0x00};    
    I2Cdev::readBytes(address, 0x10, 4, buffer);

    return (buffer[0] << 24) |
           (buffer[1] << 16) |
           (buffer[2] << 8) |
           buffer[3];    
}

void userInterrupt(uint8_t id, uint8_t player, uint8_t team) {       
    uint32_t user = getUserData(100 + id);
    if (user == 0) {
        return;
    }

    Q.addCardSwipeEvent(team, player, user);
    MC->sendAsync("T");
}

void userInterrupt0() {
    return userInterrupt(0, 0, 0);
}

void userInterrupt1() {
    return userInterrupt(1, 1, 1);
}

void userInterrupt2() {
    return userInterrupt(2, 1, 0);
}

void userInterrupt3() {
    return userInterrupt(3, 0, 1);
}

void goalInterrupt(uint8_t team, time_t *goalKeeper) {
    time_t currentTime = time(NULL);
    if (goalKeeper && ((currentTime - *goalKeeper) > GOAL_INTERRUPT_DELAY_S)) {
        *goalKeeper = currentTime;
        
        Q.addAutoGoalEvent(team);
	MC->sendAsync("G");
    }                	
}

void goalInterrupt0() {
    return goalInterrupt(0, &goalKeeper0);
}

void goalInterrupt1() {
    return goalInterrupt(1, &goalKeeper1);
}

void goalInterrupt2() {
    return goalInterrupt(0, NULL);
}

void goalInterrupt3() {
    return goalInterrupt(1, NULL);
}

int setupInterruptsAndPins() {
    int status = wiringPiSetup();
    if (status < 0) {
        ERR("Unable to setup wiringPi: %d", status);
        return -1;
    }

    pinMode(BEEPER_PIN, OUTPUT);
   
   //TODO: Seems like interrupts are on both edges despite the setting name
    wiringPiISR (READER_0_INTERRUPT_PIN, INT_EDGE_RISING, &userInterrupt0);
    wiringPiISR (READER_1_INTERRUPT_PIN, INT_EDGE_RISING, &userInterrupt1);
    wiringPiISR (READER_2_INTERRUPT_PIN, INT_EDGE_RISING, &userInterrupt2);
    wiringPiISR (READER_3_INTERRUPT_PIN, INT_EDGE_RISING, &userInterrupt3);
    
    wiringPiISR (MOTION_INTERRUPT_PIN, INT_EDGE_RISING, &motionInterrupt);

    wiringPiISR (GOAL_0_INTERRUPT_PIN, INT_EDGE_RISING, &goalInterrupt0);
    wiringPiISR (GOAL_1_INTERRUPT_PIN, INT_EDGE_RISING, &goalInterrupt1);
    wiringPiISR (GOAL_2_INTERRUPT_PIN, INT_EDGE_RISING, &goalInterrupt2);
    wiringPiISR (GOAL_3_INTERRUPT_PIN, INT_EDGE_RISING, &goalInterrupt3);

    return 0;
}

void sigTerminateEntryPoint(int sig) {
    NOTICE("Terminating on signal(%d) - %s", sig, strsignal(sig));
    is_it_good_time_to_die = true;
}

void setupSignals() {
    signal(SIGHUP, sigTerminateEntryPoint);
    signal(SIGINT, sigTerminateEntryPoint);
    signal(SIGABRT, sigTerminateEntryPoint);
    signal(SIGTERM, sigTerminateEntryPoint);
}

int main() {
    setlogmask (LOG_UPTO (LOG_DEBUG));
    openlog(PROJECT_NAME, LOG_PID|LOG_PERROR, LOG_USER);
    NOTICE("Starting... got into main loop!");

    setupSignals();
 
    INFO("Init gyro...");
    int status = gyro.init();
    if (status < 0) {
        ERR("Gyro init failed");
        return -1;
    }

    INFO("IO setup...");
    if (setupInterruptsAndPins() < 0) {
        return -1;
    }
    
    MC = new MorseCoder(BEEPER_DELAY_MS, beeperCallback);
    MC->send("Stalas");

    while(!is_it_good_time_to_die){
        msleep(10);
    }

    MC->send("out");

    NOTICE("I made peace with myself. Now I`m ready to return from main loop...");
    closelog();

    return 0;
}

