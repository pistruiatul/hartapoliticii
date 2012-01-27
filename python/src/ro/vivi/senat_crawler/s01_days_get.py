'''
Created on Aug 28, 2009

Parses the pages with the votes of each day from the senat.ro pages here
http://www.senat.ro/Voturiplen.aspx.

The code:
 - goes through each month from Dec 2008 until present
 - parses the days for each of the months
 - saves the page for each day into it's own file.

No META information is needed.

@author: vivi
'''

import datetime
from dateutil.relativedelta import relativedelta
import re
import os
import sys
from mechanize import Browser

def browse_to_month(br, month, year):
  """ Browses a browser on the senate page to a certain month in a certain year.
  We will then use this information to get the id's of the dates with votes on
  that month and get those pages.
  """
  br.select_form(name='Voturi')
  form = br.form

  form['drpMonthCal'] = [month]
  form['drpYearCal'] = [year]

  form.new_control('InputControl', '__EVENTTARGET', { 'id': '__EVENTTARGET' });
  form.new_control('InputControl', '__EVENTARGUMENT',
                   { 'id': '__EVENTARGUMENT' });

  form['__EVENTTARGET'] = 'drpMonthCal'
  form['__EVENTARGUMENT'] = year

  return br.submit().read()


def browse_to_day(br, day_id):
  br.select_form(name='Voturi')
  form = br.form

  # Fuck you, senat.ro and asp.net!
  form.new_control('InputControl', '__EVENTTARGET', { 'id': '__EVENTTARGET' });
  form.new_control('InputControl', '__EVENTARGUMENT',
                   { 'id': '__EVENTARGUMENT' });

  form['chkPaginare'] = []
  form['__EVENTTARGET'] = 'calVOT'
  form['__EVENTARGUMENT'] = day_id

  response = br.submit()
  return response.read()


def get_day_ids_from_page(page):
  return re.findall('<a href="javascript:'
                    '__doPostBack\\(\'calVOT\',\'(\d+)\'\\)', page);


def get_day_pages(br, days):
  for day_id in days:
    print ' +  day id', day_id
    fname = outdir + '/pages/day_' + day_id + '.html'

    if not os.path.exists(fname) or NO_CACHE:
      print ' ... writing page on disk to ', fname
      # Check if somehow maybe I have this day id parse already.
      page = browse_to_day(br, day_id)

      # Write it on disk so we don't parse this too often. Cache it basically.
      f = open(fname, 'w')
      f.write(page)
      f.close()
    else:
      print ' ... day already parsed'



# ===========================
# The main method for now, do not rely on it.
form_months = ['Ianuarie', 'Februarie', 'Martie', 'Aprilie', 'Mai', 'Iunie',
               'Iulie',
               'August', 'Septembrie', 'Octombrie', 'Noiembrie', 'Decembrie']

# first of december
date = datetime.date(2008, 12, 1)

NO_CACHE = False

if len(sys.argv) <= 1:
  print "The first argument should be the output directory."
  sys.exit(1)

outdir = sys.argv[1]
if not os.path.exists(outdir + '/pages'):
  os.mkdir(outdir + '/pages')

# Browse to each of the months, starting January 2009
while date < datetime.date.today():
  # Make up a new browser for each month.
  br = Browser()
  br.open('http://www.senat.ro/Voturiplen.aspx')

  month = form_months[date.month - 1]
  year = str(date.year)
  print '-- Month:', month, year

  if month is 'Februarie' and year is 2009:
    # Browsing to February is broken, unless we go to January first, go to 7th
    # of february from there, and only then switch to February. HOT.
    print ' + going to January first'
    browse_to_month(br, 'January', year)
    print ' + attempting to load Feb 7th'
    browse_to_day(br, '3324')
    print 'Done, now switching to February'

  page = browse_to_month(br, "%d" % date.month, year)
  days = get_day_ids_from_page(page)

  get_day_pages(br, days)

  date = date + relativedelta(date, months = 1)

print "Done!"
