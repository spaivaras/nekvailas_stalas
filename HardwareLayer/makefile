BIN=hl
SRC=main.cpp I2Cdev.cpp TableEventsQueue.cpp
OBJDIR=obj
OBJ=$(patsubst %,$(OBJDIR)/%,$(SRC:.cpp=.o))
CC=g++
CFLAGS=-lwiringPi -lpthread
LFLAGS=


all: $(OBJDIR) $(BIN)

$(BIN): $(OBJ)
	$(CC) $(CFLAGS) $(LFLAGS) -o $(BIN) $(OBJ)

$(OBJDIR)/%.o: %.cpp
	$(CC) $(CFLAGS) -c -o $@ $<


$(OBJDIR):
	mkdir -p $(OBJDIR)

.PHONY: clean
clean:
	rm -f hl $(OBJDIR)/*.o test
	rmdir $(OBJDIR) || true

