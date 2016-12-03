#!/usr/bin/env python
# -*- coding: utf-8 -*-

import codecs
import re
import json

def load_file(id):
    f = "/tmp/candidat%s.txt" % id

    input_file = codecs.open(f, 'r', 'utf-8')
    input = input_file.read()
    input_file.close()

    return input


def extract_name(content):
    """# Cristiana Irina ANGHEL

    * * *

      * PARTID"""

    m = re.findall('\s# (.*)', content)
    if len(m) <= 1:
        return None

    if m[1].startswith(" "):
        return None
    else:
        return m[1]


def extract_section(content, name, header, ends_with="", remove_new_lines=False):
    lines = content.split("\n")
    content = ""
    started = 0
    for line in lines:
        if line == "# %s" % name:
            started = 1
            continue

        if started == 1 and line.startswith(header) and line.endswith(ends_with):
            started = 2
            continue

        if started == 2 and line != "" and line != "* * *" and line != "  * PARTID":
            content += "%s\n" % line

        if started == 2 and (line == "* * *" or line == "  * PARTID"):
            break

    if remove_new_lines:
        content = content.replace("\n", "")

    return content


def main():
    # load all the files from /tmp
    print("[")
    for i in range(1, 1000):
        content = load_file(i)

        name = extract_name(content)
        if name is None:
            continue

        data = {}
        data['nume'] = name
        data['integritate'] = extract_section(content, name, "### INTEGRITATE")
        data['stat_de_drept'] = extract_section(content, name, "### ATITUDINE FA")
        data['istoric_politic'] = extract_section(content, name, "### ISTORIC POLITIC")
        data['studii'] = extract_section(content, name, "### STUDII")
        data['activitate_profesionala'] = extract_section(content, name, "### ACTIVITATE PROFESIONAL")

        data['activitate_parlamentara'] = extract_section(content, name, "### ACTIVITATE PARLAMENTAR", remove_new_lines=True)
        data['declaratie_avere'] = extract_section(content, name, "### DECLARA", "VERE", remove_new_lines=True)
        data['declaratie_interese'] = extract_section(content, name, "### DECLARA", "ERESE", remove_new_lines=True)

        data['controverse'] = extract_section(content, name, "### CONTROVERSE")

        data['sursa'] = "http://www.alegeriparlamentare2016.ro/candidati/candidat_detail/id:%s" % i

        print(json.dumps(data, sort_keys=True, indent=4, separators=(',', ': ')))
        print(",")
    print("]")



main()