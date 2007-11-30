all: rootls rootopen rootisdir

rootls: rootls.o
	gcc -o rootls rootls.o
	chmod +s rootls

rootls.o: rootls.c
	gcc -c rootls.c

rootopen: rootopen.o
	gcc -o rootopen rootopen.o
	chmod +s rootopen

rootopen.o: rootopen.c
	gcc -c rootopen.c

rootisdir.o: rootisdir.c
	gcc -c rootisdir.c

rootisdir: rootisdir.o
	gcc -o rootisdir rootisdir.o
	chmod +s rootisdir

clean:
	rm -f rootls.o rootopen.o rootisdir.o

distclean: clean
	rm -f rootls rootopen rootisdir
