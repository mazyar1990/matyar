LOAD DATA LOCAL INFILE '/path/to/your/corpus_for_import.csv'
INTO TABLE translations
FIELDS TERMINATED BY ','
ENCLOSED BY '"'
LINES TERMINATED BY '\n'
IGNORE 1 ROWS
(arabic_text, persian_text);