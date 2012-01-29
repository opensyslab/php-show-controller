#!/usr/bin/python
# -*- coding: utf-8 -*-

#  This program is free software; you can redistribute it and/or modify
#  it under the terms of the GNU General Public License as published by
#  the Free Software Foundation; either version 2 of the License, or
#  (at your option) any later version.
#
#  This program is distributed in the hope that it will be useful,
#  but WITHOUT ANY WARRANTY; without even the implied warranty of
#  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
#  GNU Library General Public License for more details.
#
#  You should have received a copy of the GNU General Public License
#  along with this program; if not, write to the Free Software
#  Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA 02111-1307, USA.
#
# Copyright (C) 2011 Laurent Pierru <renzo@imaginux.com>
# Thanks to Stéphane Bonhomme <stephane@exselt.com>

"""Php-Show-Controller. TCP Server to control scenarios."""

import SocketServer
from delta import DmxSender
from config import HOST, PORT
#from threading import Lock
import time

# tcp server commands :
# start.n   : starts a thread playing sequence n one time
# stop.n    : stops a sequence
# status.n  : gives the status of a sequence
# list      : list of sequences currently playing
# stopall   : stops all sequences currently playing

# start only one thread
DS = DmxSender()
DS.start()

class MyTCPServer(SocketServer.TCPServer):
    allow_reuse_address=True

class MyTCPHandler(SocketServer.BaseRequestHandler):
    """
    The RequestHandler class for our server.

    It is instantiated once per connection to the server, and must
    override the handle() method to implement communication to the
    client.
    """

    def handle(self):
        # self.request is the TCP socket connected to the client

        self.data = self.request.recv(1024).strip()
        status=0
        data=None
        try:
            command, scenarid = self.data.split('.')
        except:
            command=self.data

        if command=="halt":
            DS.StopDmxSender()

        if command=="start":
            if not DS.scen_ids.has_key(scenarid):
                # add id into list
                DS.scen_ids[scenarid]=0
                status=1
        
        if command=="stop":
            if DS.scen_ids.has_key(scenarid):
                # remove id from list
                DS.scen_ids.pop(scenarid)
                status=1
            
        if command=="status":
            if DS.scen_ids.has_key(scenarid):
                # tell if running
                data="%s running" % scenarid

        if command=="list":
            try:
                print DS.scen_ids.keys()
                data=reduce(lambda x,y : y+'.'+x, DS.scen_ids.keys())
            except:
                pass

        if command=="stopall":
            for scenarid in DS.scen_ids:
                DS.scen_ids.pop(scenarid)
                status=1

        if command=="bo":
            # stopall
            for scenarid in DS.scen_ids:
                DS.scen_ids.pop(scenarid)
                status=1
            # bo
            DS.BlackOut()

        response=str(status)
        if data is not None:
            response=response+":"+data

        self.request.send(response)

###

if __name__=="__main__":

    # Create the server, binding to localhost on port 9999
    server = MyTCPServer((HOST, PORT), MyTCPHandler)

    # Activate the server; this will keep running until you
    # interrupt the program with Ctrl-C
    server.serve_forever()


