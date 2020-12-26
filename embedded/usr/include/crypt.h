#ifndef _CRYPT_H
#define _CRYPT_H

#ifdef __cplusplus
extern "C" {
#endif

void encrypt(char *, int);
void setkey(const char *);
char *crypt(const char *, const char *);

#ifdef _GNU_SOURCE

struct crypt_data {
	int initialized;
	char __buf[256];
};

char *crypt_r(const char *, const char *, struct crypt_data *);

#endif /* _GNU_SOURCE */

#ifdef __cplusplus
}
#endif

#endif
