#include <stdio.h>
#include <stdarg.h>
#include "logger.h"

static const char* logger_levels[] = {"EMERG", "ALERT", "CRIT", "ERR", "WARNING", "NOTICE", "INFO", "DEBUG"}; 

void logger_printf(int level, const char* file, int line, const char* func, const char* fmt, ...) {
	char msg[4097] = "";

	va_list args;
	va_start(args, fmt);
	vsprintf(msg, fmt, args);
	va_end(args);

	printf("%-7s %s:%d:%s() %s\n", logger_levels[level], file, line, func, msg);
}
void logger_syslog(int level, const char* file, int line, const char* func, const char* fmt, ...) {
	char msg[4097] = "";

	va_list args;
	va_start(args, fmt);
	vsprintf(msg, fmt, args);
	va_end(args);

	syslog(level, "%-7s %s:%d:%s() %s\n", logger_levels[level], file, line, func, msg);
}
