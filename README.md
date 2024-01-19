# Get ClassicPress usage statistics from ClassicPress API

## Usage
- Login to api-v1.classicpress.net using ssh
- Go to `/var/log/apache2`
- Merge log files to get more data:
- -  `zcat api_access.log.*.gz >data.log`
  -  `cat api_access.log >>data.log`
- Download the merged data
- Launch the script: `./stat.php data.log`
