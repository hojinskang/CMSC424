from bs4 import BeautifulSoup
import urllib2
import re
import sqlite3

conn = sqlite3.connect('test.db')
print "Opened database successfully"

#conn.execute('''DROP TABLE country;''')
#print "Table deleted successfully";

#conn.execute('''CREATE TABLE player_cup
#    (pid int,
#    cup_year int,
#    cid int,
#    jersey int,
#    played int,
#    started int,
#    captain int,
#    goals int,
#    goal_avg real,
#    yellow int,
#    red int
#    );''')
#print "Table created successfully";

alphabet = 'z'
pid = 7229

# a: 0 - 497        n: 4711 - 4923
# b: 498 - 1096     o: 4924 - 5089
# c: 1097 - 1615    p: 5090 - 5499   skip Juan Carlos Paredes
# d: 1616 - 1969    q: 5500 - 5526
# e: 1970 - 2140    r: 5527 - 5873
# f: 2141 - 2365    s: 5874 - 6507
# g: 2366 - 2722    t: 6508 - 6718
# h: 2723 - 3022    u: 6719 - 6750
# i: 3023 - 3123    v: 6751 - 7030
# j: 3124 - 3331    w: 7031 - 7167
# k: 3332 - 3746    x: 7168 - 7172
# l: 3747 - 4052*   y: 7173 - 7228
# m: 4053 - 4710    z: 7229 - 7338

url = 'http://www.thesoccerworldcups.com/players_index/letter_'+alphabet+'.php'
page = urllib2.urlopen(url)
soup = BeautifulSoup(page.read(), "lxml")

temp = soup.find_all('div', {'class':'rd-100-33'})

for tmp in temp:
    tm = str(tmp).splitlines()
    for t in tm:
        matchObj = re.search(r'^\<a href\=\"\.\.([a-z\/\_\.\-0-9]*)', t, re.M)
        if matchObj and (matchObj.group(1) != '/players/juan_carlos_paredes.php'):
            url =  'http://www.thesoccerworldcups.com' + matchObj.group(1)
            #print url
            page = urllib2.urlopen(url)
            soup = BeautifulSoup(page.read(), "lxml")

            temp = soup.find('h2', {'class':'scf12r a-center'})
            name = temp.text.strip()
            #print name

            temp = soup.find_all('td', {'class':'scf6'})
            flag = 0
            dob = None
            height = None
            for tmp in temp:
                tm = tmp.text.strip().splitlines()
                #print tm[0]
                strTmp = tm[0].encode("utf-8")
                if flag == 1:
                    dob = strTmp
                    flag = 0
                elif flag == 2:
                    height = strTmp
                    flag = 0
                elif strTmp == 'Born Date:':
                    flag = 1
                elif strTmp == 'Height:':
                    flag = 2

#            print dob
            #if height:
            #print height

            country_names = []
            temp = soup.find_all('td', {'class':'scf12t'})
            for tmp in temp:
                tmp = tmp.find_all('a')
                for t in tmp:
                    if t.find('img'):
                        country_name = t.find('img')['alt'].strip()
                        country_names.append(country_name)
                        #print country_name
                        if country_name == 'West Germany':
                            country_name = 'Germany'
                        elif country_name == 'Yugoslavia' or country_name == 'Serbia and Montenegro' or country_name == 'FR of Yugoslavia':
                            country_name = 'Serbia'
                        elif country_name == 'Czechoslovakia':
                            country_name = 'Czech Republic'
                        elif country_name == 'Soviet Union':
                            country_name = 'Russia'
                        elif country_name == 'Zaire':
                            country_name = 'RD Congo'
                        elif country_name == 'Dutch East Indies':
                            country_name = 'Indonesia'
                        cur = conn.cursor()
                        cur.execute("select id from country where name = '"+country_name+"';")
                        cid = cur.fetchone()[0]
                        cur.close()

            temp = soup.find('div', {'class':'rd-tbl-2'})
            temp = temp.contents[1].contents[1].text.strip().split('\n')
            #temp.remove('\n')
            stat_by_cup = []
            total = []
            flag = 0
            for t in temp:
                strTmp = str(t.strip())
                if len(strTmp) > 0:
                    if flag == 1:
                        stat_by_cup.append(strTmp)
                    if flag == 2:
                        total.append(strTmp)
                    if strTmp == 'Standing':
                        flag = 1
                    if strTmp == 'Totals:':
                        flag = 2

            #print stat_by_cup
            j = 0
            jersey = None
            print name
            for i in range(0, len(stat_by_cup)-1, 15):
                cup_year = int(stat_by_cup[i])
                if not stat_by_cup[i+1] == '-':
                    jersey = int(stat_by_cup[i+1])
                position = stat_by_cup[i+2]
                played = int(stat_by_cup[i+3])
                started = int(stat_by_cup[i+4])
                captain = int(stat_by_cup[i+5])
                goals = int(stat_by_cup[i+7])
                goal_avg = float(stat_by_cup[i+8])
                yellow = int(stat_by_cup[i+9])
                red = int(stat_by_cup[i+10])

                list = [pid, cup_year, cid, jersey, played, started, captain, goals, goal_avg, yellow, red]
                conn.execute('insert into player_cup values (?,?,?,?,?,?,?,?,?,?,?)', list)
                print list
#                print cup_year
#                print jersey
#                print position
#                print played
#                print started
#                print captain
#                print goals
#                print goal_avg
#                print yellow
#                print red

#            print total

            pid = pid+1
            print

#print total

conn.commit()
conn.close()
