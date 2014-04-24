BIN=hl
SRC=main.cpp I2Cdev.cpp
OBJDIR=obj
OBJ=$(patsubst %,$(OBJDIR)/%,$(SRC:.cpp=.o))
CC=gcc
CFLAGS=-lwiringPi
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
	rm -f hl $(OBJDIR)/*.o
	rmdir $(OBJDIR)

