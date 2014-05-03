#ifndef _LOGGER_H_
#define _LOGGER_H_

#include <syslog.h>

// interface
#define EMERG(...)   LOGGER_LOG(LOG_EMERG, __VA_ARGS__)
#define ALERT(...)   LOGGER_LOG(LOG_ALERT, __VA_ARGS__)
#define CRIT(...)    LOGGER_LOG(LOG_CRIT, __VA_ARGS__)
#define ERR(...)     LOGGER_LOG(LOG_ERR,__VA_ARGS__)
#define WARNING(...) LOGGER_LOG(LOG_WARNING, __VA_ARGS__)
#define NOTICE(...)  LOGGER_LOG(LOG_NOTICE, __VA_ARGS__)
#define INFO(...)    LOGGER_LOG(LOG_INFO, __VA_ARGS__)
#define DEBUG(...)   LOGGER_LOG(LOG_DEBUG, __VA_ARGS__)


#ifdef LOGGER_SYSLOG
#define LOGGER_LOG(level, ...) logger_syslog(level, __FILE__, __LINE__, __func__, __VA_ARGS__)
#else
#define LOGGER_LOG(level, ...) logger_printf(level, __FILE__, __LINE__, __func__, __VA_ARGS__)

#endif

void logger_printf(int level, const char* file, int line, const char* func, const char* fmt, ...);
void logger_syslog(int level, const char* file, int line, const char* func, const char* fmt, ...);

#endif

