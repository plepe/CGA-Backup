#include <stdio.h>
#include <stdlib.h>
#include <unistd.h>
#include <sys/types.h>
#include <dirent.h>
#include <sys/stat.h>

int main(int argc, char **argv) {
  DIR *dir;
  struct dirent *dirp;
  struct stat statp;
  char filename[1024];

  if(argc!=2) {
    fprintf(stderr, "rootisdir <path>\n");
    exit(0);
  }

//  dir=opendir(argv[1]);
//  sprintf(filename, "%s", argv[1]);
  if(lstat(argv[1], &statp)==-1)
    printf("0\n");
  else
    printf("1\n");
}
