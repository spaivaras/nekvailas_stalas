BIN=stalasd
SRC=main.cpp I2Cdev.cpp TableEventsQueue.cpp EventsPusher.cpp sleep.cpp MPU6050.cpp Gyro.cpp MorseCoder.cpp logger.cpp
OBJDIR=obj
OBJ=$(patsubst %,$(OBJDIR)/%,$(SRC:.cpp=.o))
CC=g++
CFLAGS=-pedantic -Wall -std=c++0x -g
LFLAGS=-lwiringPi -lpthread -lcurl


all: $(BIN)

$(BIN): $(OBJDIR) $(OBJ)
	$(CC) $(CFLAGS) -o $(BIN) $(OBJ) $(LFLAGS)

$(OBJDIR)/%.o: %.cpp
	$(CC) $(CFLAGS) -c -o $@ $<


$(OBJDIR):
	mkdir -p $(OBJDIR)

.PHONY: clean
clean:
	rm -f $(BIN) $(OBJDIR)/*.o test
	rmdir $(OBJDIR) || true

.PHONY: install
install:
	test -s $(BIN) || { echo "\n$(BIN) not found, run 'make all' first\n"; exit 1; }
	cp $(BIN) /usr/local/sbin/stalasd
	cp init.d.sh /etc/init.d/stalasd
	chmod a+x /etc/init.d/stalasd
	insserv stalasd
	echo "\n installed successfully \n you may want to 'service stalasd start' now \n"

.PHONY: uninstall
uninstall:
	service stalasd stop || true
	insserv -r stalasd
	rm -f /etc/init.d/stalasd
	rm -f /usr/local/sbin/stalasd
	echo "\n uninstalled successfully \n"


