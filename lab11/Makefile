CFLAGS = -Wall -O2
LIBS = -lsqlite3

all: dump load

dump: dump.o
	gcc $(CFLAGS) -o $@ $< $(LIBS)

load: load.o
	gcc $(CFLAGS) -o $@ $< $(LIBS)


%.o: %.c
	gcc $(CFLAGS) -c $< 


.PHONY: clean
clean: 
	rm -f dump load *.o *~
