BIN=hl
SRC=main.cpp I2Cdev.cpp TableEventsQueue.cpp EventsPusher.cpp
OBJDIR=obj
OBJ=$(patsubst %,$(OBJDIR)/%,$(SRC:.cpp=.o))
CC=g++
CFLAGS=-pedantic -Wall -g
LFLAGS=-lwiringPi -lpthread -lcurl


all: $(OBJDIR) $(BIN)

$(BIN): $(OBJ)
	$(CC) $(CFLAGS) -o $(BIN) $(OBJ) $(LFLAGS)

$(OBJDIR)/%.o: %.cpp
	$(CC) $(CFLAGS) -c -o $@ $<


$(OBJDIR):
	mkdir -p $(OBJDIR)

.PHONY: clean
clean:
	rm -f hl $(OBJDIR)/*.o test
	rmdir $(OBJDIR) || true

