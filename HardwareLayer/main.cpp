#include "config.h"

#include <stdio.h>
#include <stdint.h>
#include <unistd.h>
#include <stdlib.h>
#include <wiringPi.h>
#include <signal.h>
#include <string.h> // strsignal()

#include "I2Cdev.h"
#include "TableEventsQueue.h"
#include "sleep.h"
#include "Gyro.h"


volatile sig_atomic_t is_it_good_time_to_die = false;

TableEventsQueue Q;
Gyro gyro (&Q);

uint32_t getUserData(uint8_t address)
{
    uint8_t buffer[4] = {0x00, 0x00, 0x00, 0x00};
    
    I2Cdev::readBytes(address, 0x10, 4, buffer);

    return (buffer[0] << 24) |
           (buffer[1] << 16) |
           (buffer[2] << 8) |
           buffer[3];    
}

void userInterrupt(uint8_t id)
{       
    uint32_t user = getUserData(100 + id);
    if (user == 0) {
        return;
    }

    uint8_t team = (id & 1<<1 ) >> 1;
    uint8_t player = (id & 1);
    Q.addCardSwipeEvent(team, player, user);
}

void userInterrupt0()
{
    return userInterrupt(0);
}

void userInterrupt1()
{
    return userInterrupt(1);
}

void userInterrupt2()
{
    return userInterrupt(2);
}

void userInterrupt3()
{
    return userInterrupt(3);
}

void motionInterrupt()
{
    return gyro.motionInterrupt();
}

int setupInterrupts()
{
    int status = wiringPiSetup();
    if (status < 0) {
        printf ("Unable to setup wiringPi: %d\n", status);
        return -1;
    }
    
    wiringPiISR (READER_0_INTERRUPT_PIN, INT_EDGE_RISING, &userInterrupt0);
    wiringPiISR (READER_1_INTERRUPT_PIN, INT_EDGE_RISING, &userInterrupt1);
    wiringPiISR (READER_2_INTERRUPT_PIN, INT_EDGE_RISING, &userInterrupt2);
    wiringPiISR (READER_3_INTERRUPT_PIN, INT_EDGE_RISING, &userInterrupt3);
    
    wiringPiISR (MOTION_INTERRUPT_PIN, INT_EDGE_RISING, &motionInterrupt);

    return 0;
}

void sigTerminateEntryPoint(int sig) {
	printf("Terminating on signal(%d) - %s\n", sig, strsignal(sig));
	is_it_good_time_to_die = true;
}
void setupSignals() {
	signal(SIGHUP, sigTerminateEntryPoint);
	signal(SIGINT, sigTerminateEntryPoint);
	signal(SIGABRT, sigTerminateEntryPoint);
	signal(SIGTERM, sigTerminateEntryPoint);
}

int main() {
    int status = 0;
    printf("I'm working hard, give me a break!ok?\n");
    setupSignals();
    
    printf("Init gyro...\n");
    status = gyro.init();
    if (status < 0) {
        printf("Gyro init failed\n");
        return -1;
    }
    
    printf("Interrupts setup...\n");
    if (setupInterrupts() < 0) {
        return -1;
    }
    
    while(!is_it_good_time_to_die){
        msleep(10);
    }

    printf("I made peace with myself. Now I`m ready to return from main loop...\n");
 
    return 0;
}

