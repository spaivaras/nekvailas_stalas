/* 
 * File:   Gyro.cpp
 * Author: Aivaras
 *
 * Created on Penktadienis, 2014, Balandžio 18, 20.43
 */

#include <stdio.h>
#include <stdint.h>
#include <unistd.h>
#include <wiringPi.h>
#include "I2Cdev.h"
#include "MPU6050.h"


MPU6050 gyro;


void motionInterrupt()
{
    printf("\nGOT MOTION\n");
}

int setupInterrupts()
{
    int status = wiringPiSetup();
    if (status < 0) {
        printf ("Unable to setup wiringPi: %d\n", status);
        return 0;
    }
    
    status = wiringPiISR (0, INT_EDGE_RISING, &motionInterrupt);
    if (status  < 0 ) {
        printf ("Unable to setup ISR: %d\n", status);
        return 0;
    }
    
    return 1;
}


int main() {
   
    printf("Initializing\n");
    gyro.initialize();
    
    printf("Testing device connections...\n");
    
    int status = gyro.testConnection();
    printf(status ? "MPU6050 connection successful\n" : "MPU6050 connection failed\n");
    if (!status) {
        return 0;
    }
    
    printf("Setting Gyro Motion interrupt and sensitivity\n");
    gyro.setMotionDetectionThreshold(1);
    gyro.setMotionDetectionDuration(1);
    gyro.setIntMotionEnabled(true);
    
    printf("Enabling interrupt listener\n");
    status = setupInterrupts();
    if (!status) {
        return 0;
    }
    
    for(;;)
    {
        printf("Temp: %f\r", float(gyro.getTemperature())/340+36.53);
    }
    
    return 0;
}

