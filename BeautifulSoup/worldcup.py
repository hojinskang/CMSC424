



from bs4 import BeautifulSoup
import urllib2
import re
import sqlite3

conn = sqlite3.connect('test.db')
#print "Opened database successfully"

#conn.execute('''DROP TABLE country;''')

#conn.execute('''CREATE TABLE country
#    (id int primary key,
#    name varchar(30),
#    num_cups int,
#    win int,
#    draw int,
#    loss int,
#    goal_scored int,
#    goal_against int,
#    image varchar(100)
#    );''')

#print "Table created successfully";

#for i in range(1, 78):
url = 'http://www.thesoccerworldcups.com/national_teams.php'
page = urllib2.urlopen(url)
soup = BeautifulSoup(page.read(), "lxml")

line = soup.find_all("a",string="Stats")
line.pop(0)

l = line[0]
url = 'http://www.thesoccerworldcups.com/'+l['href']
page = urllib2.urlopen(url)
soup = BeautifulSoup(page.read(), "lxml")

# get image
imgURLs = soup.findAll('img')
imgSrc = imgURLs[2]['src']
imgHTML = 'http://www.thesoccerworldcups.com'+imgSrc[2:]

# get name
name = soup.find('div',{'class': 'rd-100-33 size-11 negri a-center clearfix'}).text.strip()


print name
print imgHTML


conn.close()
