#include <stdio.h>
#include <stdint.h>
#include <unistd.h>
#include <stdlib.h>
#include <wiringPi.h>
#include <ctime>

#include "Gyro.h"
#include "config.h"

Gyro::Gyro(TableEventsQueue* q_ref) {
    Q = q_ref;
    hardware = new MPU6050();
    lastMotion = time(NULL);
}

Gyro::~Gyro() {
    Q = NULL;
    delete hardware;
}

void  Gyro::motionInterrupt()
{
    time_t currentMotion = time(NULL);
    
    //Truly no need to flood the heck out of the server
    if (currentMotion - lastMotion > 1) {
        Q->addTableShakeEvent();
        lastMotion = currentMotion;
    }
}

int Gyro::init()
{
    bool status = false;
    int count = 0;
    
    while (count < RETRY_COUNT && !status) {
        hardware->initialize();
        status = hardware->testConnection();
    }
    
    if (!status) {
        return -1;
    }
    
    count = 0;
    status = false;
    
    while (count < RETRY_COUNT && !status) {
        hardware->setMotionDetectionThreshold(MOTION_FORCE_TRESHOLD);
        hardware->setMotionDetectionDuration(MOTION_DURATION_TRESHOLD);
        hardware->setIntMotionEnabled(true);
        status = hardware->getIntMotionEnabled();
    }
    
    if (!status) {
        return -2;
    }
   
    return 0;
}





