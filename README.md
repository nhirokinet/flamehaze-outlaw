# flamehaze-outlaw

Web system as the central of [flamehaze](https://github.com/nhirokinet/flamehaze)  .  
API specification is going to change later. Currently you should keep all the components latest.

## Usage

- The directory `htdocs` should be placed on document root. Remember you must edit config.php.
- The file `schema.sql` indicated schema for MySQL database. Note that this delete all existing data.
- The file `sample-codes-for-test/sqldata/sample.sqldump` is MySQL dump data, with the problems in にろきプロコン#1. Note that in this contest input data for problem B had a problem, and some data are deleted, leading to the lack of random input data.  
- Beware that POST request from flamehaze may JSON encodes and sends as large as 1MiB (default) text per the number of test cases in each problem. Multi byte character is expressed in \uxxxx format. Therefore, you need to configure php.ini and web server to accept 3MiB per the maximum number of test cases in one problem, plus extra data (less than 1MiB).
