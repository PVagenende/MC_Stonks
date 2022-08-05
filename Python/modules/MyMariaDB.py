import logging
import sys

import mariadb


class DB:
    def __init__(self, host, port, db, user, passw):
        try:
            self.conn = mariadb.connect(
                user=user,
                password=passw,
                host=host,
                port=port,
                database=db)
        except mariadb.Error as e:
            logging.error("DB.init --> Failed to create a database connection, database responded with: %s", e)
            sys.exit()
        self.cur = self.conn.cursor()

    def checkplayer(self, player):
        self.cur.execute("SELECT * FROM users WHERE UUID=?", (player,))
        rows = self.cur.fetchall()
        if len(rows) != 0:
            logging.debug('DB.checkplayer --> UUID %s matched with username %s', player, rows[0][1])
            return rows[0]
        else:
            logging.debug('DB.checkplayer --> UUID %s not found', player)
            return False

    def updatetimestamp(self, UUID, modtime):
        sql = """UPDATE users SET lastchange = %s WHERE UUID = %s"""
        val = (modtime, UUID)
        try:
            self.cur.execute(sql, val)
            self.conn.commit()
            logging.debug("DB.updatetimestamp --> Updated timestamp for %s", UUID)
        except mariadb.Error as e:
            logging.warning('DB.updatetimestamp --> Could not update the timestamp the database responded %s', UUID, e)

    def createnewplayer(self, UUID, name, modtime):
        sql = """INSERT INTO users (UUID, name, lastchange) VALUES (%s, %s, %s)"""
        val = (UUID, name, modtime)
        try:
            self.cur.execute(sql, val)
            self.conn.commit()
            logging.debug("DB.updatetimestamp --> Created new player with data %s, %s, %s", UUID, name, modtime)
        except mariadb.Error as e:
            logging.warning('DB.updatetimestamp --> Could not update the timestamp the database responded %s', UUID, e)

    def updatestats(self, UUID, key, val):
        updatesql = """UPDATE stats SET value = %s WHERE UUID = %s AND descr = %s"""
        updateval = (val, UUID, key)
        insertsql = """INSERT INTO stats (UUID, descr, value) VALUES (%s, %s, %s)"""
        insertval = (UUID, key, val)
        try:
            self.cur.execute(updatesql, updateval)
            self.conn.commit()
            if self.cur.rowcount == 0:
                logging.debug("DB.updatestats --> %s not found for player %s to update, trying to insert instead", key, UUID)
                self.cur.execute(insertsql, insertval)
                self.conn.commit()
        except mariadb.Error as e:
            logging.warning('DB.updatestats --> Could not update or insert key %s for player %s, the database responded %s', key, UUID, e)

    def getallplayers(self):
        self.cur.execute("""SELECT * FROM users""")
        rows = self.cur.fetchall()
        if len(rows) != 0:
            return rows
        else:
            logging.error('DB.getallplayers --> No players found in the database')
            return False

    def updateplayername(self, UUID, newname):
        sql = """UPDATE users SET name = %s WHERE UUID = %s"""
        val = (newname, UUID)
        try:
            self.cur.execute(sql, val)
            self.conn.commit()
            logging.debug("DB.updateplayername --> Updated playername to %s for %s", newname, UUID)
        except mariadb.Error as e:
            logging.warning('DB.updateplayername --> Could not update the playername for %s the database responded %s', UUID, e)

    def getuniquestats(self):
        self.cur.execute("""SELECT DISTINCT descr from stats""")
        rows = self.cur.fetchall()
        result = []
        if len(rows) != 0:
            for tpl in rows:
                result.append(tpl[0])
            return result
        else:
            logging.error('DB.getuniquestats --> Failed to pull all stat items in the database')
            return False

    def getstattypes(self):
        self.cur.execute("""SELECT descr from stat_types""")
        rows = self.cur.fetchall()
        result = []
        if len(rows) != 0:
            for tpl in rows:
                result.append(tpl[0])
            return result
        else:
            logging.error('DB.getstattypes --> Failed to pull stat_types items in the database')
            return False

    def addstattype(self, stattype, friendly):
        sql = """INSERT INTO stat_types (descr, friendly) VALUES (%s, %s)"""
        val = (stattype, friendly)
        try:
            self.cur.execute(sql, val)
            self.conn.commit()
            logging.info("DB.addstattype --> %s added to stat_types", stattype)
        except mariadb.Error as e:
            logging.warning('DB.addstattype --> Could not add %s to stattypes, the database responded %s', stattype, e)

    def updatemetrics(self, stattype, total, top):
        sql = """UPDATE stat_types SET total = %s, top = %s WHERE descr = %s"""
        val = (total, top, stattype)
        try:
            self.cur.execute(sql, val)
            self.conn.commit()
            logging.debug("DB.updatemetrics --> Updated %s with a max of %s and a top for %s", stattype, total, top)
        except mariadb.Error as e:
            logging.warning('DB.updatemetrics --> Could not update the metrics for %s, the database responded with %s', stattype, e)

    def getmetricsum(self, stattype):
        special = ['mined', 'crafted']
        if stattype in special:
            if stattype == 'mined':
                self.cur.execute("select sum(total) from stat_types where descr like '%mined%'")
            elif stattype == 'crafted':
                self.cur.execute("select sum(total) from stat_types where descr like '%crafted%'")
        else:
            self.cur.execute("select sum(value) from stats where descr=?", (stattype,))
        rows = self.cur.fetchall()
        if len(rows) != 0:
            logging.debug('DB.getmetricsum --> %s came back with a total of %s', stattype, rows[0][0])
            return rows[0]
        else:
            logging.debug('DB.getmetricsum --> %s not found', stattype)
            return False

    def getmetricstop(self, stattype):
        self.cur.execute("SELECT UUID FROM stats WHERE descr = ? order by value desc limit 1", (stattype,))
        rows = self.cur.fetchall()
        if len(rows) != 0:
            logging.debug('DB.getmetricsum --> %s came back with a top for %s', stattype, rows[0][0])
            return rows[0]
        else:
            logging.debug('DB.getmetricsum --> %s not found', stattype)
            return False

    def close(self):
        self.conn.close()


