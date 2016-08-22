from bs4 import BeautifulSoup
import urllib2
import re
import sqlite3

conn = sqlite3.connect('test.db')
print "Opened database successfully"

#conn.execute('''DROP TABLE country;''')
#print "Table deleted successfully";
#
#conn.execute('''CREATE TABLE country_cup
#    (cid int,
#    cup_year int,
#    standing int,
#    stage varchar(20),
#    points int,
#    win int,
#    draw int,
#    loss int,
#    goals_scored int,
#    goals_against int
#    );''')
#print "Table created successfully";

url = 'http://www.thesoccerworldcups.com/national_teams.php'
page = urllib2.urlopen(url)
soup = BeautifulSoup(page.read(), "lxml")

line = soup.find_all("a",string="World Cup by World Cup")

for l in line:
    url = 'http://www.thesoccerworldcups.com/'+l['href']
    #url = 'http://www.thesoccerworldcups.com/national_teams/algeria_world_cups.php'
    page = urllib2.urlopen(url)
    soup = BeautifulSoup(page.read(), "lxml")

    country_name = soup.find('div', {'class':'rd-100-33 size-11 negri a-center clearfix'}).text.strip()

    cur = conn.cursor()
    cur.execute("select id from country where name = '"+country_name+"';")
    cid = cur.fetchone()[0]
    cur.close()

    table = soup.find('table',{'class':'c0s5 a-right'})
    for i in range(3,len(table.contents),2):
        td = table.contents[i]
        if len(td.contents) > 3 and td.contents[5].text != '-':
            cup_year = td.contents[1].text
            standing = td.contents[5].text
            stage = td.contents[7].text
            pts = td.contents[9].text
            w = td.contents[13].text
            d = td.contents[15].text
            l = td.contents[17].text
            gs = td.contents[19].text
            ga = td.contents[21].text
            list = [int(cid), int(cup_year), int(standing), stage, int(pts), int(w), int(d), int(l), int(gs), int(ga)]
            conn.execute('insert into country_cup values (?,?,?,?,?,?,?,?,?,?)', list)
            print list
    print

conn.commit()
conn.close()
