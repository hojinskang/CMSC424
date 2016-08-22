from bs4 import BeautifulSoup
import urllib2
import re
import sqlite3

conn = sqlite3.connect('test.db')
print "Opened database successfully"

#conn.execute('''DROP TABLE game_stats;''')
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

#url = 'http://www.thesoccerworldcups.com/world_cups.php'
#page = urllib2.urlopen(url)
#soup = BeautifulSoup(page.read(), "lxml")
#
#line = soup.find_all("a",string="Results")

#for l in line:
#print l['href']
cup_year = 1930

url = 'http://www.thesoccerworldcups.com/world_cups/1930_results.php'
page = urllib2.urlopen(url)
soup = BeautifulSoup(page.read(), "lxml")

scores = soup.find_all("a",string=re.compile("-"))
scores.pop(0)
stages = soup.find_all("a",string=re.compile("Round|Finals|Semifinals|3rd|Final"))
stages.pop(0)

for i in range(0, len(scores)):
    stage = stages[i].text.strip()
    
    url = 'http://www.thesoccerworldcups.com/world_cups/' + scores[i]['href']
    page = urllib2.urlopen(url)
    soup = BeautifulSoup(page.read(), "lxml")

    countries = soup.find_all("a",href=re.compile("national_team.php"))
    country1 = countries[0].text.strip()
    country2 = countries[1].text.strip()
    
    if country1 == 'West Germany':
        country1 = 'Germany'
    elif country1 == 'Yugoslavia' or country1 == 'Serbia and Montenegro' or country1 == 'FR of Yugoslavia':
        country1 = 'Serbia'
    elif country1 == 'Czechoslovakia':
        country1 = 'Czech Republic'
    elif country1 == 'Soviet Union':
        country1 = 'Russia'
    elif country1 == 'Zaire':
        country1 = 'RD Congo'
    elif country1 == 'Dutch East Indies':
        country1 = 'Indonesia'

    if country2 == 'West Germany':
        country2 = 'Germany'
    elif country2 == 'Yugoslavia' or country2 == 'Serbia and Montenegro' or country2 == 'FR of Yugoslavia':
        country2 = 'Serbia'
    elif country2 == 'Czechoslovakia':
        country2 = 'Czech Republic'
    elif country2 == 'Soviet Union':
        country2 = 'Russia'
    elif country2 == 'Zaire':
        country2 = 'RD Congo'
    elif country2 == 'Dutch East Indies':
        country2 = 'Indonesia'

    cur = conn.cursor()
    cur.execute("select id from country where name = '"+country1+"';")
    cid1 = cur.fetchone()[0]
    cur.execute("select id from country where name = '"+country2+"';")
    cid2 = cur.fetchone()[0]
    cur.close()
    
    goals = soup.find("strong", string="Goals:")
    penalty_shootout = soup.find("strong", string="Penalty Shootout:")
    
    if goals:
        temp = soup.find_all('div', {'class': 'left a-left clearfix'})[0]
        times = temp.find_all(string=re.compile('[0-9]'))
        players = temp.find_all(string=re.compile('[a-z]'))
        
        # for times
        i = 0
        # for players
        j = 0
        while j < len(players):
            if players[j].strip() != '(own goal)' and players[j].strip() != '(penalty)':
                time = times[i].strip()
                player = players[j].strip()
                
                #print time
                #print player
                
                cur = conn.cursor()
                cur.execute("select player.id from player, player_cup where id = pid and name = ? and cup_year = "+str(cup_year)+" and (cid = "+str(cid1)+" or cid = "+str(cid2)+");", [player])
                pids = cur.fetchone()
                if pids:
                    pid = pids[0]
                    #print pid
                    
                    if j + 1 < len(players) and players[j+1].strip() == '(own goal)':
                        goal = -1
                    else:
                        goal = 1
                    
                    print player
                    
                    list = [int(cup_year), stage, int(cid1), int(cid2), int(pid), int(goal), None, time]
                    print list
                    conn.execute('insert into game_stats values (?,?,?,?,?,?,?,?)', list)
                    print
                    i = i + 1
            
            j = j + 1

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
                    penalty = 1
                else:
                    penalty = 0
                
                cur = conn.cursor()
                cur.execute("select player.id from player, player_cup where id = pid and name = ? and cup_year = "+str(cup_year)+" and (cid = "+str(cid1)+" or cid = "+str(cid2)+");", [player])
                pids = cur.fetchone()
                if pids:
                    pid = pids[0]
                    print player
                    list = [int(cup_year), stage, int(cid1), int(cid2), int(pid), None, int(penalty), None]
                    print list
                    conn.execute('insert into game_stats values (?,?,?,?,?,?,?,?)', list)
                    print

conn.commit()
conn.close()
