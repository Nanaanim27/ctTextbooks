import mysql.connector
import csv
from decimal import *

config = {
  'user': 'efliproot',
  'password': '$Cr5c#55Ete8fruJ',
  'host': 'eflipprod.cljbdifkztiw.us-west-2.rds.amazonaws.com',
  'database': 'eflip',
}

rank_percentages = [
    [1,       10000,   0.64],
    [10000,   100000,  0.45],
    [100000,  250000,  0.30],
    [250000,  500000,  0.24],
    [500000,  1000000, 0.19],
    [1000000, 2000000, 0.10],
    [2000000, 3000000, 0.06]]

cnx = mysql.connector.connect(**config)
cursor = cnx.cursor()

fo = open('Integrity.csv','wb')
writer = csv.writer(fo)

for rank_percentage in rank_percentages:
  low_rank = rank_percentage[0]
  high_rank = rank_percentage[1]
  coeff = rank_percentage[2]
  needed_price = 1.00 / coeff
  print "Fetching: "
  print rank_percentage
  cursor.execute("SELECT `isbn`, `used_price`, `title`, `sales_rank` FROM `books` WHERE `sales_rank` >= %d AND `sales_rank` < %d AND`used_price` > %0.2f" % (low_rank, high_rank, needed_price))
  
  for row in cursor:
    modisbn = row[0]
    if len(modisbn) == 10:
      modisbn = modisbn[0:9]
    elif len(modisbn) == 13:
      modisbn = modisbn[3:12]
    else:
      continue
    expected_price = "%0.2f" % (float(row[1])*coeff)
    writer.writerow([modisbn, row[2], row[3], row[1], expected_price])

cnx.close()
