/* 
 * File:   Gyro.h
 * Author: Aivaras
 *
 * Created on Trečiadienis, 2014, Balandžio 30, 19.38
 */

#ifndef GYRO_H
#define	GYRO_H

#include "TableEventsQueue.h"
#include "MPU6050.h"


class Gyro {
    private:
        TableEventsQueue* Q;
        MPU6050* hardware;
        time_t lastMotion;
    public:            
        Gyro(TableEventsQueue *);
        
        int init();
        void motionInterrupt();
        
        virtual ~Gyro();        
};

#endif	/* GYRO_H */

