#!/bin/bash

# cd to current script directory
cd "$(dirname "$0")"

# Create temp file
tmpfile=$(mktemp /tmp/merge.XXXXXX)
exec 3>"$tmpfile"
exec 4<"$tmpfile"
rm "$tmpfile"

# Merge logs
for i in {14..2}; do
	zcat "/var/log/apache2/api_access.log.${i}.gz" >&3;
done;
cat "/var/log/apache2/api_access.log.1" >&3;
cat "/var/log/apache2/api_access.log" >&3;

# Gzip logs
date=$(date '+%Y-%m-%d')
cat <&4 | ./stat-spam.php --fields=fullversion,php,locale,ip - >"spam-statistics_${date}.txt"

exec 3>&-
