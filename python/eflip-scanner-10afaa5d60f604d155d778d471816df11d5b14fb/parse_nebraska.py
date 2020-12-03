#!/usr/bin/python

import argparse
import csv
import string

# Process command line arguments
parser = argparse.ArgumentParser(formatter_class=argparse.ArgumentDefaultsHelpFormatter)
parser.add_argument('--guide_file', default='EXPNEBR.GDE', help='Filename of GUIDE data')
parser.add_argument('--out_file', default='NebraskaData.csv', help='Filename for outputting results')
parser.add_argument('--nid_length', default=24, type=int, help='Nebraska ID')
parser.add_argument('--title_length', default=70, type=int, help='Title')
parser.add_argument('--isbn10_length', default=13, type=int, help='ISBN 10')
parser.add_argument('--junk_length', default=9, type=int, help='junk')
parser.add_argument('--publisher_length', default=20, type=int, help='Publisher')
parser.add_argument('--buyback_length', default=7, type=int, help='Buyback Price')
parser.add_argument('--sell_length', default=7, type=int, help='Sell Price')
parser.add_argument('--date_length', default=25, type=int, help='Date')
parser.add_argument('--isbn13_length', default=17, type=int, help='ISBN 13')
parser.add_argument('--min_price', default=6.0, type=float, help='Minimum price to keep')
args = parser.parse_args()

num_processed = 0

all_ = string.maketrans('','')
nodigs = all_.translate(all_, string.digits)

def strip_field(line, length):
  sub = line[0:length]
  line = line[length:]
  return(line, sub)

with open(args.guide_file, 'r') as guide_file:
  with open(args.out_file, 'wb') as outfile:
    outwriter = csv.writer(outfile, delimiter=',')
    outwriter.writerow(['Original Data', 'Clean ISBN', 'GetPrice Formula', 'x 1.25'])
    for line in guide_file:
      output = [line]
      (line, tmp) = strip_field(line, args.nid_length)
      (line, tmp) = strip_field(line, args.title_length)
      (line, tmp) = strip_field(line, args.isbn10_length)
      (line, tmp) = strip_field(line, args.junk_length)
      (line, tmp) = strip_field(line, args.publisher_length)
      (line, tmp) = strip_field(line, args.buyback_length)
      buyback_price = float(tmp.translate(all_, nodigs)) / 100
      (line, tmp) = strip_field(line, args.sell_length)
      (line, tmp) = strip_field(line, args.date_length)
      (line, tmp) = strip_field(line, args.isbn13_length)
      isbn13 = tmp.translate(all_, nodigs)
      output.append(isbn13)
      output.append('%0.2f' % buyback_price)
      output.append('%0.2f' % (buyback_price * 1.25))
      if buyback_price > args.min_price:
        outwriter.writerow(output)

