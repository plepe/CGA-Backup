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
    fprintf(stderr, "rootls <path>\n");
    exit(0);
  }

  dir=opendir(argv[1]);
  while((dirp=readdir(dir))!=NULL) {
    sprintf(filename, "%s%s", argv[1], dirp->d_name);
    lstat(filename, &statp);
    printf("%s\t%d\t%d\t%d\t%d\n", dirp->d_name, statp.st_mode, statp.st_size, statp.st_mtime, statp.st_ino);
  }
}
