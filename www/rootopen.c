#include <stdio.h>
#include <stdlib.h>
#include <unistd.h>
#include <sys/types.h>
#include <sys/stat.h>

int main(int argc, char **argv) {
  FILE *f;
  struct dirent *dirp;
  struct stat statp;
  char buf[1024];
  int count;

  if(argc!=2) {
    fprintf(stderr, "rootopen <file>\n");
    exit(0);
  }

  if((f=fopen(argv[1], "r"))==NULL) {
    fprintf(stderr, "cannot open file '%s'\n", argv[1]);
    exit(0);
  }

  while(count=fread(buf, 1, 1024, f)) {
    fwrite(buf, 1, count, stdout);
  }

  fclose(f);
}
