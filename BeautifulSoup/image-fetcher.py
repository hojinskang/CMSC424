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

if line:
    line = line.text.strip()
    matchObj = re.match(r'(.*)\n(.*).*\n(.*)', line, re.M)
    year = matchObj.group(2).strip()
    country = matchObj.group(3).strip()

    list = [int(year),country]

    conn.execute('insert into country (year, country) values (?,?)',list)

    print year
    print country

conn.commit()














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


#for ln in line:
#print ln
#    match = re.search(pattern, ln)


#champions = []
#numCupsWon = []
#cupsWon = []

#for country in soup.find_all('tr','scf12t'):
#    line = country.text.strip()
#    print line
#    matchObj = re.match(r'(.*)\n(.).*\n(.*)', line, re.M)
#    champions.append(matchObj.group(1).strip())
#    numCupsWon.append(matchObj.group(2).strip())
#    cupsWon.append(matchObj.group(3).strip())

#print "Champions"
#print champions
#print
#print "Number of Cups Won"
#print numCupsWon
#print
#print "Cups Won"
#print cupsWon

conn.close()

#teaminfo = soup.find("div", {"class": "team-info"})
#name = teaminfo.h4.contents
#rating = teaminfo.ul.p.span.contents

