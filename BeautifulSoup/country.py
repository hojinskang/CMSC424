from bs4 import BeautifulSoup
import urllib2
import re
import sqlite3

conn = sqlite3.connect('test.db')
print "Opened database successfully"

conn.execute('''DROP TABLE country;''')
print "Table deleted successfully";

conn.execute('''CREATE TABLE country
    (id int primary key,
    name varchar(30),
    num_cups int,
    win int,
    draw int,
    loss int,
    goal_scored int,
    goal_against int,
    image varchar(100)
    );''')
print "Table created successfully";

url = 'http://www.thesoccerworldcups.com/national_teams.php'
page = urllib2.urlopen(url)
soup = BeautifulSoup(page.read(), "lxml")

line = soup.find_all("a",string="Stats")
line.pop(0)

for l in line:
    url = 'http://www.thesoccerworldcups.com/'+l['href']
    page = urllib2.urlopen(url)
    soup = BeautifulSoup(page.read(), "lxml")

    # get image
    imgURLs = soup.findAll('img')
    imgSrc = imgURLs[2]['src']
    imgHTML = 'http://www.thesoccerworldcups.com'+imgSrc[2:]

    # get name, num_cups, and id
    temp = soup.find_all('tr', {'class': 'scf12o'})

    matchObj = re.match(r'(.*)\n(.*)', temp[0].text.strip(), re.M)
    name = matchObj.group(1).strip()
    num_cups = matchObj.group(2).strip()

    lines = temp[1].text.strip().splitlines()
    last = lines[len(lines)-2]
    id = last.strip()

    # get win, draw, loss, goals scored, goals against
    temp = soup.find_all('tr', {'class': 'scf12t'})
    temp2 = temp[0].text.strip()
    matchObj = re.match(r'(.*)\n\n(.*)\n(.*)\n(.*)', temp2, re.M)
    win = matchObj.group(2).strip()
    draw = matchObj.group(3).strip()
    loss = matchObj.group(4).strip()

    temp2 = temp[1].text.strip()
    matchObj = re.match(r'(.*)\n(.*)', temp2, re.M)
    scored = matchObj.group(1).strip()
    against = matchObj.group(2).strip()

    print "ID: " + id
    print "Name: " + name
    print "Num_cups: " + num_cups
    print "Win: " + win
    print "Draw: " + draw
    print "Loss: " + loss
    print "Goals scored: " + scored
    print "Goals against: " + against
    print "Image: " + imgHTML
    print

    list = [int(id), name, int(num_cups), int(win), int(draw), int(loss), int(scored), int(against), imgHTML]
    conn.execute('insert into country values (?,?,?,?,?,?,?,?,?)', list)


conn.commit()
conn.close()
