from bs4 import BeautifulSoup
import urllib2
import re
import sqlite3

conn = sqlite3.connect('test.db')
print "Opened database successfully"

#conn.execute('''DROP TABLE country;''')
#print "Table deleted successfully";
#
#conn.execute('''CREATE TABLE game_stats
#    (cup_year int,
#    stage varchar(20),
#    cid1 int,
#    cid2 int,
#    pid int,
#    goal int,
#    penalty int,
#    time varchar(10)
#    );''')
#print "Table created successfully";

url = 'http://www.thesoccerworldcups.com/games/2014_holland_costa_rica.php'
page = urllib2.urlopen(url)
soup = BeautifulSoup(page.read(), "lxml")

penalty_shootout = soup.find("strong", string="Penalty Shootout:")

if penalty_shootout:
    temp = soup.find_all('div', {'class': 'left a-left clearfix'})
    table = temp[len(temp)-1]

    left = table.find_all('div', {'class': 'left'})
    
    for l in left:
        player = l.text.strip()
        if len(player) > 0:
            parentheses = player.find('(')
            if parentheses > 0:
                player = player[0:parentheses-1]
            green = l.find('div', style=re.compile("green"))
            if green:
                goal = 1
            else:
                goal = 0
            print [player, goal]


conn.commit()
conn.close()
