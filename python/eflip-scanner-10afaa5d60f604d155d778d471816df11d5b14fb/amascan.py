#!/usr/bin/python

import amascan_lib
import argparse
import MySQLdb
import threading


# Process command line arguments
parser = argparse.ArgumentParser(formatter_class=argparse.ArgumentDefaultsHelpFormatter)
parser.add_argument('--input_file', default='asins.csv', help='CSV file of idns')
parser.add_argument('--output_file', default='results', help='Filename for outputting results')
parser.add_argument('--aws_file', default='aws_credentials.csv', help='Filename containing aws keys.')
parser.add_argument('--textbook_file', default='textbooks.csv', help='Filename containing known textbooks.')
parser.add_argument('--max_sales_rank', default=3000000, type=int, help='Maximum sales rank to keep.')
parser.add_argument('--logging_count', default=1000, type=int, help='Log every nth product')
parser.add_argument('--asin_position', default=0, type=int, help='Column in CSV of asin')
parser.add_argument('--isbn13_position', default=1, type=int, help='Column in CSV of isbn13')
parser.add_argument('--isbn10_position', default=2, type=int, help='Column in CSV of isbn10')
parser.add_argument('--skip_lines', default=0, type=int, help='Skip to a position')
parser.add_argument('--not_asin', default=False, action='store_true', help='Is the input file not ASINS')
parser.add_argument('--not_out_eflip', default=False, action='store_true', help='Do not output eflip results.')
parser.add_argument('--not_out_abbr', default=False, action='store_true', help='Do not output abbreviated results.')
parser.add_argument('--not_out_db', default=False, action='store_true', help='Do not output to database.')
args = parser.parse_args()
args.is_asin = not args.not_asin
args.is_out_eflip = not args.not_out_eflip
args.is_out_abbr = not args.not_out_abbr
args.is_out_db = not args.not_out_db

NUM_PROCESSED = args.skip_lines
OUT_LOCK = threading.Lock()
amazon_pool = amascan_lib.AmazonPool(args.aws_file)

def process_idns(idns, product_fetcher, product_writers, amazon=None):
  global NUM_FOUND
  global NUM_PROCESSED
  if amazon:
    with OUT_LOCK:
      NUM_PROCESSED = NUM_PROCESSED + len(idns)
      if NUM_PROCESSED % args.logging_count == 0:
        print('Processed %d books' % (NUM_PROCESSED))
    products = product_fetcher.fetch_products(idns, amazon)
    for product in products:
      for product_writer in product_writers:
        with OUT_LOCK:
          product_writer.write_product(product)

def main():
  if args.is_asin:
    input_reader = amascan_lib.ASINInputReader(args)
  else:
    input_reader = amascan_lib.ISBNInputReader(args)
  product_fetcher = amascan_lib.AWSProductFetcher(args)
  product_writers = []
  if args.is_out_abbr:
    product_writers.append(amascan_lib.AbbrProductWriter(args, lambda product: True))
  if args.is_out_eflip:
    product_writers.append(amascan_lib.EflipProductWriter(args, lambda product: product.sales_rank <= args.max_sales_rank))
  if args.is_out_db:
    config = {
      'host': 'localhost',
      'user': 'efliproot',
      'passwd': 'Qz@fV%qXUw69_7eB',
      'db': 'eflip',
    }
    db = MySQLdb.connect(**config)
    cursor = db.cursor()
    
    product_writers.append(amascan_lib.DBProductWriter(args, lambda product: True, cursor))
    
  while input_reader.has_idns:
    idns = input_reader.get_idns()
    amazon_pool.add_task(process_idns, idns, product_fetcher, product_writers)
  amazon_pool.wait_completion()

if __name__ == '__main__':
  main()
