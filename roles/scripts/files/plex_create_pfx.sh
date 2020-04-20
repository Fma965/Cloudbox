#!/usr/bin/env bash
#########################################################################
# Title:         Plex traefik-certs-dumper PFX generator                #
# Author(s):     Fma965                                                 #
# URL:           https://github.com/Fma965/CoolFlix                     #
# Description:   Create PFX cert via traefik-certs-dumper and acme.json #
# --                                                                    #
#########################################################################
#                   GNU General Public License v3.0                     #
#########################################################################
readonly PLEX_KEY="/opt/traefik/certs/coolflix.stream/privatekey.key"
readonly PLEX_CHAIN="/opt/traefik/certs/coolflix.stream/certificate.crt"
readonly PLEX_PFX="/opt/plex/certificate.pfx"

openssl pkcs12 -nodes -export -out "${PLEX_PFX}" -inkey "${PLEX_KEY}" -in "${PLEX_CHAIN}" -passout pass:test
chmod 775 "${PLEX_PFX}"