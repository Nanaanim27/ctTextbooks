import csv

# Load list of known textbooks
fi = open('textbooks.csv','rb')
reader = csv.reader(fi)

textbooks = {}
for row in reader:
  textbooks[row[0]] = 1

# Append textbook boolean to response
fi = open('results_eflip.csv','rb')
reader = csv.reader(fi)

fo = open('results_textbooks.csv','wb')
writer = csv.writer(fo,delimiter='\t')
num_textbooks = 0
for row in reader:
  if row[2] in textbooks:
    row.append(1)
    num_textbooks += 1
  else:
    row.append(0)
  row[1] = row[1].replace('\\','\\\\')
  writer.writerow(row)

print(num_textbooks)
