Train Abo
=========

A little command line tool to figure out what day to get the train abo.


Usage
-----

```./trainabo.php```

This lists the number of working days from today up to 14 days from today.

```./trainabo.php 2016-04-01```

This lists the number of working days from April 1st up to April 14th.

```./trainabo.php 2016-04-01 -e 4```

This lists the number of working days from April 1st up to April 4th.

```./trainabo.php 2016-04-01 -e 4 -d holidays_example.csv```

This lists the number of working days from April 1st up to April 4th.
And it excludes any dates found in the file ```holidays_example.csv```.
