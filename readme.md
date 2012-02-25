# Welcome to Quick Analyse

## Legal stuff
exec.php is in the public domain, do with it what you please
Other content is available under another license, for example
pChart is released under the terms of the GPL v3

## Introduction
Quick Analyse is a very quick and dirty tool for investigating
relationships between fields in data. It was written to make analysing
the Felix Sex Survey quick and easy.

The code moves data into categories depending on the first parameter
(the filter). For example, if the filter is Gender, then data will
be organized into Male and Female categories depending on the value
for each row. Then, we calculate the number of entries of a given
Field (the second parameter), and their percentage. This means, using
the previous example, if the Field is "Cat or Dog", we will calculate
the percentage of people who like cats and dogs within each gender,
as well as the number.

This information is put into a text file (report.txt), in the format
of the relationship being investigated, followed by each Field value
and the appropriate count and percentage.

Additionally, within the output directory (which you must create, and
ensure is writable), a graph is generated showing this in a visual
manner, this allows you to see if the data is worth using. Note that
this graph is not always readable: it is generated in a dumb manner.

You should also create a small (i.e. 50x55) image called cat.jpg, to be
shown on the top right of each graph.

## Usage
A database must exist, the connection parameters can be set within
exec.php. There must be a column for each field, and a row for each
element of data.

You can then produce output by running
  exec.php Filter Field "Optional SQL"

If you specify optional SQL, this will be appended to the query (and
noted within the report and graphs), this allows for more complex
investigations.

# Support
This code is not supported, has just been uploaded to dump stuff.
