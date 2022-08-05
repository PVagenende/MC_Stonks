import configparser
import logging
from modules.Parser import Parser
from modules.MyMariaDB import DB
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

# Initiate and setup of objects
parser = Parser()
db = DB(config['MARIADB']['host'], int(config['MARIADB']['port']), config['MARIADB']['database'], config['MARIADB']['user'], config['MARIADB']['password'])


# Actual script logic
worlds = config.items("WORLDS")
for world in worlds:
    logging.debug("main-># starting %s in %s", world[0], world[-1])
    # Grab all stats files for parsing
    files = parser.listfiles(world[-1])
    for file in files:
        # strip UUID from file
        player = file[:-5]
        # get the last modification date of the file to see if the script needs to parse the file for upload to the DB
        timestamp = int(parser.getlastchange(world[-1], file))
        # check if the user already has an entry in the DB
        user = db.checkplayer(player)
        if not user:
            PlayerName = parser.getname(player)
            # Escape if minecraft is not responding with the username
            if PlayerName:
                # Create new record for player and update the timestamp
                db.createnewplayer(player, PlayerName, timestamp)
                # Collect all stats and upload them
                stats = parser.parseplayer(world[-1], file)
                for stat in stats.items():
                    for key, val in stat[1].items():
                        item = key.split(":")
                        db.updatestats(player, stat[0] + ":" + item[-1], val)
        else:
            if timestamp != int(user[2]):
                logging.debug("%s has new file, processing", user[1])
                print('processing ' + user[1])
                # Update the timestamp of the players last read
                db.updatetimestamp(player, timestamp)
                # Collect all stats and upload them
                stats = parser.parseplayer(world[-1], file)
                for stat in stats.items():
                    for key, val in stat[1].items():
                        item = key.split(":")
                        db.updatestats(player, stat[0] + ":" + item[-1], val)
            else:
                logging.debug("%s has no updates, doing nothing for this player", user[1])





# Close the db connection nicely
db.close()
logging.info('Stats Parsing took %s seconds', time.time()-start)
# TODO add script execution time and send it to info log

