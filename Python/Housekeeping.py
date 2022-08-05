import configparser
import logging
from modules.Parser import Parser
from modules.MyMariaDB import DB
from time import sleep
import sys
import time
start = time.time()

# Get config params from config.ini
config = configparser.ConfigParser()
config.read('./config.ini')

# Setup logging as defined per config
def main():
    try:
        logging.basicConfig(filename=config['LOG']['log_file'], encoding='utf-8', format='%(levelname)s: %(message)s [%(asctime)s]', datefmt='%d/%m/%Y %H:%M:%S', level=int(config['LOG']['log_level']))
    except (configparser.Error, IOError, OSError) as e:
        logging.basicConfig(filename='emergency_log.log', encoding='utf-8', level=logging.DEBUG)
        logging.debug("CONFIG FILE NOT LOCATED, defaulted to debug logging")


if __name__ == '__main__':
    main()

db = DB(config['MARIADB']['host'], int(config['MARIADB']['port']), config['MARIADB']['database'], config['MARIADB']['user'], config['MARIADB']['password'])
parser = Parser()

def UpdateNamesOfPlayers():
    players = db.getallplayers()
    print(players)
    for player in players:
        temp = parser.getname(player[0])
        if player[1] != temp:
            logging.info('%s has changed their name to %s, updating the database', player[1], temp)
            db.updateplayername(player[0], temp)
        sleep(5)

def CollectStats():
    stat_types = db.getstattypes()
    unique_stats = db.getuniquestats()
    for stat in unique_stats:
        if stat not in stat_types:
            tmp = stat.split(':')
            action = tmp[1].replace('_', ' ')
            item = tmp[2].replace('_', ' ')
            friendly = action.title() + ' ' + item.title()
            friendly = friendly.replace('Custom ', '')
            db.addstattype(stat, friendly)


def UpdateMetrics():
    stat_types = db.getstattypes()
    for stat in stat_types:
        sum = db.getmetricsum(stat)
        if stat == 'mined' or stat == 'crafted':
            top = ['']
        else:
            top = db.getmetricstop(stat)
        db.updatemetrics(stat, str(sum[0]), top[0])


if '-NoNames' not in sys.argv:
    UpdateNamesOfPlayers()
if '-NoNewStats' not in sys.argv:
    CollectStats()
if '-NoCalculation' not in sys.argv:
    UpdateMetrics()

db.close()
logging.info('Housekeeping took %s seconds', time.time()-start)

