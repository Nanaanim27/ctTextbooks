import requests

url = "https://amazon-product-reviews-keywords.p.rapidapi.com/product/search"

querystring = {"category":"aps","country":"US","keyword":"textbook"}

headers = {
    'x-rapidapi-host': "amazon-product-reviews-keywords.p.rapidapi.com",
    'x-rapidapi-key': "77104eef12mshe03180f644b11a0p1e749djsn25d3d12f0c99"
    }

response = requests.request("GET", url, headers=headers, params=querystring)

print(response.text)
