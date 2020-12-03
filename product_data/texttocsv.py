import re
import csv
import numpy as np
import pandas as pd



#lines = re.split('bbb','bbbaaaaaabbbccc')


#for line in lines:
#    if(line != ''):
#        print('bbb' + line)

out = open('amazon_textbook_20200926.csv','w',newline='')
csv_writer = csv.writer(out,dialect='excel')  


file = open("test2.txt","r")
f = file.read()

firstline = 'page position global_position asin discounted current_price currency before_price saving_amount saving_percent total_reviews rating url score sponsored amazonChoice bestSeller amazonPrime title thumbnail'
output = firstline.split()
csv_writer.writerow(output)


lines = re.split('{"position":',f)
for line in lines:
    if(line != ''):
        line = line.replace("{","")
        line = line.replace("}","")
        line = line.replace('"price":',"")
        line = line.replace('"reviews":',"")
        line = line.replace(" ","_")
        line = line.replace(","," ")
        line = line.replace('"page":',"")
        line = line.replace('"position":',"")
        line = line.replace('"global_position":',"")
        line = line.replace('"asin":',"")
        line = line.replace('"discounted":',"")
        line = line.replace('"current_price":',"")
        line = line.replace('"currency":',"")
        line = line.replace('"before_price":',"")
        line = line.replace('"savings_amount":',"")
        line = line.replace('"savings_percent":',"")
        line = line.replace('"total_reviews":',"")
        line = line.replace('"rating":',"")
        line = line.replace('"url":',"")
        line = line.replace('"score":',"")
        line = line.replace('"sponsored":',"")
        line = line.replace('"amazonChoice":',"")
        line = line.replace('"bestSeller":',"")
        line = line.replace('"amazonPrime":',"")
        line = line.replace('"title":',"")
        line = line.replace('"thumbnail":',"")
        output = line.split()
        csv_writer.writerow(output)
        print(output)
        #print(line)
        
file.close()
out.close()
