import json
import logging
import os
import pathlib
import requests


class Parser:
    def __init__(self):
        logging.debug("Parser.__init__ --> Parser initiated")

    def listfiles(self, dir):
        logging.debug("Parser.listfiles --> listing files in %s", dir)
        included_extensions = ['json']
        file_names = [fn for fn in os.listdir(dir + '/stats')
                      if any(fn.endswith(ext) for ext in included_extensions)]
        return file_names

    def getlastchange(self, dir, file):
        lastmd = pathlib.Path(dir + '/stats/' + file).stat().st_mtime
        logging.debug("Parser.getlastchange --> %s was last changed on %s", file, lastmd)
        return lastmd

    def parseplayer(self, dir, file):
        fname = dir + '/stats/' + file
        logging.debug("Parser.parseplayer --> parsing %s", fname)
        stat = json.load(open(fname, 'r'))
        return stat['stats']

    def getname(self, uuid):
        url = 'https://api.mojang.com/user/profiles/'+uuid+'/names'
        r = requests.get(url)
        response = r.json()
        if r.status_code == 200:
            logging.debug('Pharser.getname --> Username found found for uuid %s, minecraft replied %s', uuid, response)
            name = response[-1]['name']
            return name
        elif r.status_code == 429:
            logging.error('Parser.getname --> Minecraft not replying with UUID -> name conversion, rate limiting kicked in')
            return None
        else:
            logging.error('Parser.getname --> Minecraft not replying with UUID -> name conversion, they say %s', response)
            return None
