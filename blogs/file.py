from bs4 import BeautifulSoup
import requests

# Send request and get response
response = requests.get('https://example.com')
html_content = response.text

# Parse content
soup = BeautifulSoup(html_content, 'html.parser')



import requests

# Target URL
url = 'https://example.com'

# Send GET request
response = requests.get(url)

# Optional headers
headers = {
    'User-Agent': 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36'
}




data = []
for item in items:
    item_data = {
        'title': item.find('h2').text,
        'description': item.find('p').text,
        'link': item.find('a')['href']
    }
    data.append(item_data)

import json
json_data = json.dumps(data, indent=4)