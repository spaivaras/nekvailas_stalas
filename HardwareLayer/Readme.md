Main RPI hardware layer
=========

A simple combination of libraries for reading data from [rfid_reader](https://github.com/joinedforces/nekvailas_stalas/tree/master/rfid_reader) via I2C

------

Dependencies
-------
- This code is cardinally linked to [WiringPi library](http://wiringpi.com/)
- Binary also needs root access to work because of access to **/dev/mem**

Raspberry outputs
-------
- **P1** - 3.3VCC
- **P3** - SDA
- **P5** - SCL
- **P6** - GND

Raspberry inputs
-------
- **P7** - INT Reader 0
- **P11** - INT Reader 1
- **P13** - INT Reader 2
- **P15** - INT Reader 3

---
**Thanks to:**

- [@drogon](http://wiringpi.com/contact/) - WiringPi
- [Jeff Rowberg](https://github.com/jrowberg/i2cdevlib) - MPU6050/i2cdevlib
- [Richard Hirst](https://github.com/richardghirst/PiBits/tree/master/MPU6050-Pi-Demo) - For hacking i2cdevlib on PI :)