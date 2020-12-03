"""
Amascan module which contains a collection of classes useful for interfacing
with amazon APIs to fetch book/product information.

There are a few different types of classes:
* Product: A simple data model for holding product information
* ProductFetcher: takes a list of IDNs (identifiers) and communicates with an
    API to return the corresponding products
* InputReader: Takes a filename and returns IDNs
* ProductWriter: Outputs product in whatever format desired, takes a product
    filter lambda to not write certain products.
* AmazonPool / AmazonWorker: Multi-threading
"""

import csv
import threading
import Queue
import unicodedata

from amazon.api import AmazonAPI

TITLE_LENGTH = 50


class ProductFetcher(object):
  def fetch_products(self, idns, amazon):
    raise NotImplementedError


class AWSProductFetcher(ProductFetcher):
  def __init__(self, args):
    self.is_asin = args.is_asin
    # Textbook knowledge should logically be moved to a different object.
    self.textbooks = {}
    fi = open(args.textbook_file, 'rb')
    reader = csv.reader(fi)
    for row in reader:
      self.textbooks[row[0]] = 1

  @staticmethod
  def _to_num(val, divisor):
    if val:
      val = int(val) / divisor
    else:
      val = 0 / divisor
    return val

  def _extract_product(self, raw_product):
    product = Product()
    product.asin = raw_product.asin
    product.title = raw_product.title
    product.ean = raw_product._safe_get_element_text('ItemAttributes.EAN')
    product.isbn = raw_product.isbn
    product.sales_rank = self._to_num(raw_product.sales_rank or 999999999, 1)
    product.weight = raw_product._safe_get_element_text('ItemAttributes.PackageDimensions.Weight')
    product.publication_date = raw_product._safe_get_element_text('ItemAttributes.PublicationDate')
    product.num_used = self._to_num(raw_product._safe_get_element_text('OfferSummary.TotalUsed'), 1)
    product.num_new = self._to_num(raw_product._safe_get_element_text('OfferSummary.TotalNew'), 1)
    product.used_price = self._to_num(raw_product._safe_get_element_text('OfferSummary.LowestUsedPrice.Amount'), 100.0)
    product.new_price = self._to_num(raw_product._safe_get_element_text('OfferSummary.LowestNewPrice.Amount'), 100.0)
    product.trade_in_price = self._to_num(raw_product._safe_get_element_text('ItemAttributes.TradeInValue.Amount'), 100.0)
    product.amazon_price = self._to_num(raw_product._safe_get_element_text('Offers.Offer.OfferListing.Price.Amount'), 100.0)
    product.publisher = raw_product._safe_get_element_text('ItemAttributes.Publisher')
    product.is_textbook = product.ean in self.textbooks
    return product

  def fetch_products(self, idns, amazon):
    products = []
    raw_products = []
    try:
      if self.is_asin:
        raw_products = amazon.lookup(ItemId=','.join(idns),IdType='ASIN',ResponseGroup='SalesRank,ItemAttributes,OfferFull',MerchantId='Amazon')
      else:
        raw_products = amazon.lookup(ItemId=','.join(idns),IdType='ISBN',SearchIndex='Books',ResponseGroup='SalesRank,ItemAttributes,OfferFull',MerchantId='Amazon')
      if not isinstance(raw_products, list):
        raw_products = [raw_products]
      for raw_product in raw_products:
        if raw_product._safe_get_element_text('ItemAttributes.ProductGroup') == 'Book':
          product = self._extract_product(raw_product)
          products.append(product)
    except Exception as e:
      print(e)
      print(amazon.api.AWSAccessKeyId)
    return products


# Generic Product wrapper
class Product(object):
  def __init__(self):
    self.title = ""
    self.asin = ""
    self.ean = ""
    self.isbn = ""
    self.sales_rank = 0
    self.weight = 0.0
    self.publication_date = ""
    self.num_used = 0
    self.num_new = 0
    self.used_price = 0.0
    self.new_price = 0.0
    self.trade_in_price = 0.0
    self.amazon_price = 0.0
    self.publisher = ""
    self.is_textbook = False

  @property
  def publication_date(self):
    return self._publication_date

  @publication_date.setter
  def publication_date(self, value):
    if value and len(value) > 4:
      value = value[0:4]
    self._publication_date = value

  @property
  def title(self):
    return self._title

  @title.setter
  def title(self, value):
    if isinstance(value, unicode):
      value = unicodedata.normalize('NFKD', value).encode('ascii','ignore')
    if value and len(value) > TITLE_LENGTH:
      value = value[0:TITLE_LENGTH]
    self._title = value

class ProductWriter(object):
  def __init__(self, args, product_filter):
    self.product_filter = product_filter

  def write_product(self, product):
    raise NotImplementedError


class DBProductWriter(ProductWriter):
  def __init__(self, args, product_filter, cursor):
    super(DBProductWriter, self).__init__(args, product_filter)
    self.cursor = cursor

  def write_product(self, product):
    sql = """INSERT INTO `books` (asin, title, ean, isbn, sales_rank, weight,
        publication_date, total_new, total_used, trade_in_price, used_price,
        new_price, amazon_price, publisher, is_textbook) values(%s, %s, %s, %s, %s, %s,
        %s, %s, %s, %s, %s, %s, %s, %s, %s) ON DUPLICATE KEY UPDATE asin=%s,
        title=%s, ean=%s, isbn=%s, sales_rank=%s, weight=%s, publication_date=%s,
        total_new=%s, total_used=%s, trade_in_price=%s, used_price=%s, new_price=%s,
        amazon_price=%s, publisher=%s, is_textbook=%s"""
    self.cursor.execute(sql, (product.asin, product.title, product.ean, product.isbn,
          product.sales_rank, product.weight, product.publication_date, product.num_new,
          product.num_used, product.trade_in_price, product.used_price, product.new_price,
          product.amazon_price, product.publisher, product.is_textbook, product.asin, product.title, product.ean,
          product.isbn, product.sales_rank, product.weight, product.publication_date,
          product.num_new, product.num_used, product.trade_in_price, product.used_price,
          product.new_price, product.amazon_price, product.publisher, product.is_textbook))


class CSVProductWriter(ProductWriter):
  def __init__(self, args, product_filter, filename):
    super(CSVProductWriter, self).__init__(args, product_filter)
    self.filename = filename
    self.fo = open(self.filename, 'wb')
    self.writer = csv.writer(self.fo)


class AbbrProductWriter(CSVProductWriter):
  def __init__(self, args, product_filter):
    super(AbbrProductWriter, self).__init__(args, product_filter, '%s_abbr.csv' % args.output_file)

  def write_product(self, product):
    if self.product_filter(product):
        self.writer.writerow([product.asin, product.ean, product.isbn, product.sales_rank])


class EflipProductWriter(CSVProductWriter):
  def __init__(self, args, product_filter):
    super(EflipProductWriter, self).__init__(args, product_filter, '%s_eflip.csv' % args.output_file)

  def write_product(self, product):
    if self.product_filter(product):
      self.writer.writerow([
          product.asin,
          product.title,
          product.ean,
          product.isbn,
          product.sales_rank,
          product.weight,
          product.publication_date,
          product.num_new,
          product.num_used,
          product.trade_in_price,
          product.used_price,
          product.new_price,
          product.amazon_price,
          product.publisher,
      ])


class AmazonWorker(threading.Thread):
  """Worker thread to execute tasks from the given Queue.
    always adds the amazonAPI object to the kargs."""
  def __init__(self, tasks, amazon):
    threading.Thread.__init__(self)
    self.tasks = tasks
    self.daemon = True
    self.amazon = amazon
    self.start()

  def run(self):
    while True:
      func, args, kargs = self.tasks.get()
      kargs['amazon'] = self.amazon
      try:
        func(*args, **kargs)
      except Exception as e:
        print e
      finally:
        self.tasks.task_done()


class AmazonPool(object):
  affiliate_id = 'dank-20'

  def __init__(self, credentials_filename):
    creds = []
    with open(credentials_filename, 'rb') as fi:
      reader = csv.reader(fi)
      for row in reader:
        creds.append(row)
    self.tasks = Queue.Queue(len(creds))
    for cred in creds:
      amazon = AmazonAPI(cred[0], cred[1], self.affiliate_id, MaxQPS=0.95)
      AmazonWorker(self.tasks, amazon)

  def add_task(self, func, *args, **kargs):
    self.tasks.put((func, args, kargs))

  def wait_completion(self):
    self.tasks.join()
      

class InputReader(object):
  num_in_group = 10

  def __init__(self, args):
    self.has_idns = True
    
  # Get a single Identification Number. Implemented by subclasses.
  def get_idn(self):
    raise NotImplementedError

  # Get a list of Identification Numbers.
  def get_idns(self):
    idns = []
    while self.has_idns and len(idns) < self.num_in_group:
      idn = self.get_idn()
      if idn:
        idns.append(idn)
    return idns


class CSVInputReader(InputReader):
  def __init__(self, args):
    super(CSVInputReader, self).__init__(args)
    self.filename = args.input_file
    self.fi = open(self.filename, 'rb')
    self.reader = csv.reader(self.fi)
    for i in range(args.skip_lines):
      next(self.reader)

  def get_idn(self):
    try:
      row = next(self.reader)
      return self.parse_row(row)
    except StopIteration:
      print "End of file"
      self.has_idns = False
    return None

  def parse_row(self, row):
    raise NotImplementedError


class ISBNInputReader(CSVInputReader):
  def __init__(self, args):
    super(ISBNInputReader, self).__init__(args)
    self.isbn10_position = args.isbn10_position
    self.isbn13_position = args.isbn13_position

  def parse_row(self, row):
    if row[self.isbn13_position]:
      return row[self.isbn13_position]
    if row[self.isbn10_position]:
      return row[self.isbn10_position]
    return None


class ASINInputReader(CSVInputReader):
  def __init__(self, args):
    super(ASINInputReader, self).__init__(args)
    self.asin_position = args.asin_position

  def parse_row(self, row):
    return row[self.asin_position]
