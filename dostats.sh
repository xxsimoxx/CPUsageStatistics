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
cat <&4 | ./stat.php --fields=version,fullversion,shortphp,multisite,locale - >"statistics_${date}.txt"

# Add row to global file.
echo -ne `date +'%y/%m/%d'`"\t" >>"statistics_global.txt"
cat "statistics_${date}.txt" | grep -E 'total|ja' | tr -dc '0-9\n' | tr "\n" "\t" >>"statistics_global.txt"
echo >>"statistics_global.txt"

exec 3>&-
