from bs4 import BeautifulSoup
import urllib2
import re
import sqlite3

conn = sqlite3.connect('test.db')
print "Opened database successfully"

#conn.execute('''DROP TABLE player;''')
##print "Table deleted successfully";
#
#conn.execute('''CREATE TABLE player
#    (pid int,
#    name varchar(100),
#    dob_m int,
#    dob_d int,
#    dob_y int,
#    height_ft int,
#    height_in int,
#    position_1 varchar(15),
#    position_2 varchar(15),
#    image varchar(300)
#    );''')
#print "Table created successfully";

alphabet = 'z'
pid = 7729

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
# l: 3747 - 4052    y: 7173 - 7228
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
            page = urllib2.urlopen(url)
            soup = BeautifulSoup(page.read(), "lxml")
            
            # GET NAME
            temp = soup.find('h2', {'class':'scf12r a-center'})
            temp = temp.find('b')
            temp = str(temp)
            temp = temp[3:len(temp)-4]
            #name = temp.text.strip()
            name = temp.strip()
            #print name
            name = name.decode("utf-8")
            if name == 'Ali Parwin':
                name = 'Ali Parvin'
            #print name

            tempo = soup.find_all('td', {'class':'scf6'})
            flag = 0
            dob = None
            height = None
            height_ft = None
            height_in = None
            position_1 = None
            position_2 = None
            for tmpo in tempo:
                tmo = tmpo.text.strip().splitlines()
                #print tm[0]
                strTmp = tmo[0].encode("utf-8")
                if flag == 1:
                    dob = strTmp
                    flag = 0
                elif flag == 2:
                    height = strTmp
                    flag = 0
                elif flag == 3:
                    if strTmp.find(',') > -1:
                        position = strTmp.split(",")
                        position_1 = position[0].strip()
                        position_2 = position[1].strip()
                        #print strTmp
                            #for m in re.finditer(',', strTmp):
                            #print(', found', m.start())
                    else:
                        position_1 = strTmp
                    flag = 0
                elif strTmp == 'Born Date:':
                    flag = 1
                elif strTmp == 'Height:':
                    flag = 2
                elif strTmp == 'Position:':
                    flag = 3

            #print position
            #print dob
            dob = dob.split(' ')
            if dob[0] == 'January':
                dob[0] = 1
            elif dob[0] == 'February':
                dob[0] = 2
            elif dob[0] == 'March':
                dob[0] = 3
            elif dob[0] == 'April':
                dob[0] = 4
            elif dob[0] == 'May':
                dob[0] = 5
            elif dob[0] == 'June':
                dob[0] = 6
            elif dob[0] == 'July':
                dob[0] = 7
            elif dob[0] == 'August':
                dob[0] = 8
            elif dob[0] == 'September':
                dob[0] = 9
            elif dob[0] == 'October':
                dob[0] = 10
            elif dob[0] == 'November':
                dob[0] = 11
            elif dob[0] == 'December':
                dob[0] = 12
            dob_m = dob[0]
            dob_d = int(dob[1][:2])
            dob_y = int(dob[2])
            
            if height:
                height = float(height[:4]) * 39.3701
                height_ft = int(height / 12)
                height_in = int(height % 12)
            #print height

            profile = None

            list = [pid, name, dob_m, dob_d, dob_y, height_ft, height_in, position_1, position_2, profile]
            conn.execute('insert into player values (?,?,?,?,?,?,?,?,?,?)', list)
            print list
            print
            
            pid = pid + 1
            #print


conn.commit()
conn.close()
