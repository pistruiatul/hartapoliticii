package ro.vivi.pistruiatul;

import java.util.ArrayList;
import java.util.HashMap;
import java.util.logging.Logger;
import java.util.regex.Matcher;
import java.util.regex.Pattern;

public class TraficHack implements PageConsumer {
  Logger log = Logger.getLogger("ro.vivi.pistruiatul.TraficHack");
  int count = 0;
  int state = 1;
  String currentDate;

  ArrayList<String> urls = new ArrayList<String>();

  HashMap<String, Integer> yay = new HashMap<String, Integer>();
  HashMap<String, Integer> nay = new HashMap<String, Integer>();


  public TraficHack() {
    // Am I instantiating anything here?
    InternetsCrawler.useCache = false;
  }


  public void run() {
    for (int i = 0; i < 41; i++) {
      InternetsCrawler.enqueue("www.trafic.ro",
          "/siturinoi/pagina" + i + ".html", this);
    }
    state = 2;

    for (String url : urls) {
      String[] parts = url.split(",");
      currentDate = parts[0];
      InternetsCrawler.enqueue("www.trafic.ro", parts[1], this);

    }

    for (String date : nay.keySet()) {
      Integer y = yay.get(date);
      Integer n = nay.get(date);
      if (date != "" && y != null && n != null) {
        log.info(date + ": " + y + " paid out of " + (y + n));
      }
    }
  }

  Pattern newSite = Pattern.compile(
      "<tr align=right valign=top([^>]*)id=([^>]*)>");
  Pattern newSiteDate = Pattern.compile(
      "<small class=grena>([^ ]*) - ([^ ]*) - ([\\d]*).([\\d]*).([\\d]*)</small></td>");
  Pattern newSiteUrl = Pattern.compile(
      "<td class=i><a href=http://stat4.trafic.ro([^>]*)>&nbsp;</a></td>");

  /**
   * Consumes a page with the list of new sites.
   */
  public void consume(String page) {
    switch(state) {
    case 1:
      consumeList(page);
      break;
    case 2:
      consumeStatPage(page);
      break;
    }
  }

  public void consumeList(String page) {
    String lines[] = page.split("\n");
    String date = "";
    String url;

    for (int i = 0; i < lines.length; i++) {
      Matcher nsm = newSite.matcher(lines[i]);
      if (nsm.matches()) {
        log.info(lines[i]);

        Matcher dateM = newSiteDate.matcher(lines[i+3].trim());
        if (dateM.matches()) {
          date = dateM.group(3) + "." + dateM.group(4) + "." + dateM.group(5);
          log.info(date);
        }

        Matcher urlM = newSiteUrl.matcher(lines[i+4].trim());
        if (urlM.matches()) {
          url = urlM.group(1);
          log.info(url);
          urls.add(date + "," + url);
        }

        count++;
      }
    }

    log.info("Total new websites: " + count);
  }

  public void consumeStatPage(String page) {
    String paid = "Err";

    if (currentDate == "") {
      log.info("what the fuck?");
      System.exit(1);
    }

    if (page.indexOf("confirmarea efectuarii platii") > 0) {
      paid = "NOT";

      Integer count = nay.get(currentDate);
      count = count == null ? new Integer(1) : new Integer(count.intValue() + 1);
      nay.put(currentDate, count);
    } else {
      paid = "YAY";

      Integer count = yay.get(currentDate);
      count = count == null ? new Integer(1) : new Integer(count.intValue() + 1);
      yay.put(currentDate, count);
    }

    log.info(currentDate + " " + paid);
  }
}
