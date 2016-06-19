#!/usr/bin/env python
'''
    Copyright (C) 2016 xtr4nge [_AT_] gmail.com

    This program is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program.  If not, see <http://www.gnu.org/licenses/>.
'''

#REF: http://stackoverflow.com/questions/21613091/how-to-use-scapy-to-determine-wireless-encryption-type

import datetime
a = datetime.datetime.now()

import logging
logging.getLogger("scapy.runtime").setLevel(logging.ERROR)
from scapy.all import *

import time
import sys, getopt
import json
from multiprocessing import Process
import signal
import threading

# HELP MENU
def usage():
    print "\nscan-rogue 1.0 by xtr4nge"
    
    print "Usage: scan-rogue.py <options>\n"
    print "Options:"
    print "-i <i>, --interface=<i>                  set interface (default: mon0)"
    print "-t <time>, --time=<time>                 scan time"
    print "-l <log>, --log=<log>                    log file (output)"
    print "-m <list> --monitor=<list>               ap list to monitor [format: ssid|bssid,bssid;ssid|bssid] (from param)"
    print "-f <file> --file=<file>                  ap list to monitor [format: ssid|bssid,bssid] (from file)"
    print "-d <seconds> --delay=<seconds>           seconds between alerts"
    print "-a --alert                               enables email alerts"
    print "-k --karma                               detects karma|mana attacks"
    print "-h                                       Print this help message."
    print ""
    print "Author: xtr4nge"
    print ""

# MENU OPTIONS
def parseOptions(argv):
    INTERFACE = "mon0"
    TIME =  int(0)
    LOG = ""
    MONITOR = ""
    CHANNEL = "1,2,3,4,5,6,7,8,9,10,11,12,13"
    FILE = ""
    DELAY = 5
    ALERT = False
    KARMA = False

    try:
        opts, args = getopt.getopt(argv, "hi:t:l:c:m:f:d:ak",
                                   ["help", "interface=", "time=", "log=", "channel=", "monitor=", "file=", "delay=", "alert", "karma"])

        for opt, arg in opts:
            if opt in ("-h", "--help"):
                usage()
                sys.exit()
            elif opt in ("-i", "--interface"):
                INTERFACE = arg
            elif opt in ("-t", "--time"):
                TIME = int(arg)
            elif opt in ("-l", "--log"):
                LOG = arg
                with open(LOG, 'w') as f:
                    f.write("")
            elif opt in ("-c", "--channel"):
                CHANNEL = arg
            elif opt in ("-m", "--monitor"):
                MONITOR = arg
            elif opt in ("-f", "--file"):
                FILE = arg
            elif opt in ("-d", "--delay"):
                DELAY = arg
            elif opt in ("-a", "--alert"):
                ALERT = True
            elif opt in ("-k", "--karma"):
                KARMA = True
        
        # CHECK OPTIONS
        if MONITOR == "" and FILE == "" and KARMA == False:
            usage()
            sys.exit()
        
        # CHANNEL INTO INT ARRAY
        TEMP = CHANNEL.split(",")
        CHANNEL = []
        for i in TEMP:
            CHANNEL.append(int(i))
        
        return (INTERFACE, TIME, LOG, CHANNEL, MONITOR, FILE, DELAY, ALERT, KARMA)
                    
    except getopt.GetoptError:           
        usage()
        sys.exit(2) 

# CHECKS TIME PASSED BETWEEN ALERTS
def checkDelay(FLAG, DELAY):
    NOW = int(time.time())
    FLAG = int(FLAG)

    if (FLAG + DELAY) < NOW:
        return True
    else:
        return False

def logEvent(LOG, MSG):
    with open(LOG, 'a') as f:    
        timestamp = time.strftime('%Y-%m-%d %H:%M:%S')
        f.write(str(timestamp) +"," + MSG + "\n")

# SEND EMAIL ALERTS
def sendMail(MSG):
    global LOG
    
    import smtplib
    from configobj import ConfigObj
    config = ConfigObj("email.conf")
    
    FROM    = config["email"]["from"]
    TO      = config["email"]["to"]
    SUBJECT = "FruityWiFi ALERT: DetectRogue"
    TEXT    = MSG
    SERVER  = config["email"]["server"]
    PORT    = config["email"]["port"]
    USER    = config["email"]["user"]
    PASS    = config["email"]["pass"]
    AUTH    = config["email"]["auth"]
    STLS    = config["email"]["starttls"]
    
    message = """\From: %s\nTo: %s\nSubject: %s\n\n%s
    """ % (FROM, TO, SUBJECT, TEXT)
        
    try:
        server = smtplib.SMTP(SERVER, PORT)
        if STLS == "1": server.starttls()
        if AUTH == "1": server.login(USER, PASS)
        server.sendmail(FROM, TO, message)
        server.quit()
    except:
        print "SMTP ERROR. (Fix the setup and restart the module.)"
        logEvent(LOG, "SMTP ERROR. (Fix the setup and restart the module.)")
        sys.exit(1)

# -------------------------
# GLOBAL VARIABLES
# -------------------------

(INTERFACE, TIME, LOG, CHANNEL, MONITOR, FILE, DELAY, ALERT, KARMA) = parseOptions(sys.argv[1:])

INVENTORY = {}
ROGUE = {}
APLIST = {}
ROGUEKARMA = {}

# LOAD SSID|BSSID FROM MONITOR
if MONITOR != "":
    print MONITOR
    MONITOR = MONITOR.split(";")
    for i in MONITOR:
        db = i.split("|")
        items = db[1].split(",")
        
        if db[0] != "" and db[1] != "":
            INVENTORY[db[0]] = map(str.lower, items)

# LOAD SSID|BSSID FROM FILE
if FILE != "":
    with open(FILE, "r") as lines:
        for line in lines:
            line = line.strip()
            db = line.split("|")
            items = db[1].split(",")

            if db[0] != "" and db[1] != "":
                INVENTORY[db[0]] = map(str.lower, items)


#print INVENTORY

# -------------------------
# SNIFFER
# -------------------------
def sniffer(pkt):
    global TIME
    global LOG
    global CHECK
    global INVENTORY
    global ALERT
    global KARMA
    
    ## Done in the lfilter param
    # if Dot11Beacon not in pkt and Dot11ProbeResp not in pkt:
    #     return
    bssid = pkt[Dot11].addr3
        
    signal = -(256-ord(pkt.notdecoded[-4:-3]))
    p = pkt[Dot11Elt]
    cap = pkt.sprintf("{Dot11Beacon:%Dot11Beacon.cap%}"
                      "{Dot11ProbeResp:%Dot11ProbeResp.cap%}").split('+')
    ssid, channel = None, None
    crypto = []
    
    while isinstance(p, Dot11Elt):
        if p.ID == 0:
            ssid = p.info
        elif p.ID == 3:
            try: channel = ord(p.info)
            except: channel = None

        p = p.payload
    
    b = datetime.datetime.now()

    
    # ---- MAGIC HERE [VIGILANT (MONITOR)] ----
    
    if len(INVENTORY) > 0:
        #SHOW OK
        if ssid in INVENTORY and bssid in INVENTORY[ssid]:
            pass
            #print "OK: " + str(bssid) + " > " + str(ssid)
        
        # DETECT ROGUE
        if ssid in INVENTORY and bssid not in INVENTORY[ssid]:
            pass
            
            if bssid not in ROGUE: # FIRST ALERT
                ROGUE[bssid] = int(time.time())
                # PRINT ALERT
                print "ROGUE: " + str(bssid) + " > " + str(ssid) + " ["+str(channel)+"]"
                if LOG != "":
                    MSG = str(bssid) + "," + str(ssid) + "," + str(channel) + " [NEW]"
                    logEvent(LOG, MSG)
                # SEND ALERT
                if ALERT:
                    sendMail("SSID: " + str(ssid) + "\nBSSID: " + str(bssid) + "\nCHANNEL: " + str(channel))
                    if LOG != "": logEvent(LOG, "EMAIL SENT.")
                
            elif checkDelay(ROGUE[bssid], DELAY): # FOLLOWING ALERT
                ROGUE[bssid] = int(time.time())
                # PRINT ALERT
                print "ROGUE: " + str(bssid) + " > " + str(ssid) + " ["+str(channel)+"]"
                if LOG != "":
                    MSG = str(bssid) + "," + str(ssid) + "," + str(channel)
                    logEvent(LOG, MSG)
    
    
    # ---- MAGIC HERE [KARMA|MANA] ----
    
    # DETECT KARMA|MANA ATTACKS
    if KARMA:
        if bssid not in APLIST and ssid != None and ssid != "":
            APLIST[bssid] = [ssid]
        elif bssid in APLIST and ssid != None and ssid != "" and ssid not in APLIST[bssid]:
            APLIST[bssid].append(ssid)
            if len(APLIST[bssid]) > 1:
                if bssid not in ROGUEKARMA: # FIRST ALERT
                    ROGUEKARMA[bssid] = int(time.time())
                    print "ROGUE [KARMA]: " + str(bssid) + " | "+str(channel) + " | " + str(APLIST[bssid])
                    if LOG != "":
                        MSG = str(bssid) + "," + str(APLIST[bssid]) + "," + str(channel) + " [KARMA|MANA] [NEW]"
                        logEvent(LOG, MSG)
                    if ALERT:
                        sendMail("[KARMA|MANA]\nSSID: " + str(APLIST[bssid]) + "\nBSSID: " + str(bssid) + "\nCHANNEL: " + str(channel))
                        if LOG != "": logEvent(LOG, "EMAIL SENT [KARMA|MANA].")
                elif checkDelay(ROGUEKARMA[bssid], DELAY): # FOLLOWING ALERT
                    ROGUEKARMA[bssid] = int(time.time())
                    print "ROGUE [KARMA]: " + str(bssid) + " | "+str(channel) + " | " + str(APLIST[bssid])
                    if LOG != "":
                        MSG = str(bssid) + "," + str(APLIST[bssid]) + "," + str(channel) + " [KARMA|MANA]"
                        logEvent(LOG, MSG)
    
    # --------------------
    
    if (b - a) > datetime.timedelta(seconds=TIME) and TIME > 0:
        sys.exit()
        
    return

# Channel hopper - This code is very similar to that found in airoscapy.py (http://www.thesprawl.org/projects/airoscapy/)
def channel_hopper(interface):
    global CHANNEL
    while True:
        try:
            #channel = random.randrange(1,13)
            channel = random.choice(CHANNEL)
            os.system("iwconfig %s channel %d" % (interface, channel))
            time.sleep(1)
        except KeyboardInterrupt:
            break

def stop_channel_hop(signal, frame):
    # set the stop_sniff variable to True to stop the sniffer
    global stop_sniff
    stop_sniff = True
    channel_hop.terminate()
    channel_hop.join()


try:
    channel_hop = Process(target = channel_hopper, args=(INTERFACE,))
    channel_hop.start()
    signal.signal(signal.SIGINT, stop_channel_hop)
    
    sniff(iface=INTERFACE, prn=sniffer, store=False,
          lfilter=lambda p: (Dot11Beacon in p or Dot11ProbeResp in p))
except Exception as e:
    print str(e)
    print "Bye ;)"

