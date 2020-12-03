import MySQLdb

config = {
  'host': 'localhost',
  'user': 'efliproot',
  'passwd': 'Qz@fV%qXUw69_7eB',
  'db': 'eflip',
}

db = MySQLdb.connect(**config)

cursor = db.cursor()

# Try to delete temp table that way if we stopped earlier it will still work

try:
  cursor.execute("DROP TABLE `books_new`")
except MySQLdb.Error as err:
  print(err)

try:
  cursor.execute("DROP TABLE `books_old`")
except MySQLdb.Error as err:
  print(err)

try:
  cursor.execute("CREATE TABLE books_new LIKE books;")

  cursor.execute("LOAD DATA INFILE '/home/vilhelm4/eflip-scanner/results_textbooks.csv' INTO TABLE `books_new` FIELDS TERMINATED BY '\t' OPTIONALLY ENCLOSED BY '\"' LINES TERMINATED BY '\r\n';")
  cursor.execute("RENAME TABLE `books` TO `books_old`, `books_new` TO `books`;")
  cursor.execute("DROP TABLE `books_old`")
except MySQLdb.Error as err:
  print "meow"
  print(err)

db.close()
