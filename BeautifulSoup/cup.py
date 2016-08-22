from bs4 import BeautifulSoup
import urllib2
import re
import sqlite3

conn = sqlite3.connect('test.db')
print "Opened database successfully"

conn.execute('''DROP TABLE cup;''')

conn.execute('''CREATE TABLE CUP
    (year int PRIMARY KEY NOT NULL,
    country varchar(30) not null);
    ''')

print "Table created successfully";

for i in range(1930, 2018, 4):
    url= 'http://www.thesoccerworldcups.com/world_cups/'+str(i)+'_world_cup.php'
    page = urllib2.urlopen(url)
    soup = BeautifulSoup(page.read(), "lxml")

    line = soup.find('span','scf12o')
    if line:
        line = line.text.strip()
        matchObj = re.match(r'(.*)\n(.*).*\n(.*)', line, re.M)
        year = matchObj.group(2).strip()
        country = matchObj.group(3).strip()
        
        list = [int(year),country]
        
        conn.execute('insert into cup (year, country) values (?,?)',list)

        print year
        print country

conn.commit()

conn.close()

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

#teaminfo = soup.find("div", {"class": "team-info"})
#name = teaminfo.h4.contents
#rating = teaminfo.ul.p.span.contents

