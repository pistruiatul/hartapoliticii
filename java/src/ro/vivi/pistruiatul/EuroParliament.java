package ro.vivi.pistruiatul;

import java.util.Date;
import java.util.HashMap;
import java.util.logging.Logger;
import java.util.regex.Matcher;
import java.util.regex.Pattern;

/**
 * Manages and parses the candidates for the Euro2009 parliamentary elections.
 * @author vivi
 */
public class EuroParliament implements PageConsumer {
  Logger log = Logger.getLogger("ro.vivi.pistruiatul.EuroParliament");

  String HOST = "www.europarl.europa.eu";

  int count = 0;
  int max = 0;
  int sum = 0;
  Date d;

  /**
   * Initializes the url's for the parties that have candidates in this race.
   */
  public EuroParliament() {
  }

  /**
   * Crawls the pages with the candidates.
   */
  public void run() {
    DbManager.deleteEuroParliament();
    // Send the URL's to be consumed.
    d = new Date(107, 11, 1);
    int year, month, day;

    InternetsCrawler.useCache = false;
    do {
      d.setDate(d.getDate() + 1);
      year = d.getYear() + 1900;
      month = d.getMonth() + 1;
      day = d.getDate();

      String date = "" + year + (month < 10 ? "0" : "") + month +
                   (day < 10 ? "0" : "") + day;
      String path = "/sides/getDoc.do?pubRef=-//EP//TEXT+PV+" + date +
                   "+ATT-REG+DOC+XML+V0//RO&language=RO";

      InternetsCrawler.enqueue(HOST, path, this, "UTF-8");
    } while (year < 2009 ||
             (year == 2009 && month < 5) ||
             (year == 2009 && month == 5 && day <=9));

    log.info("Total euro sessions " + count + " sessions;");
    log.info("  + max " + max);
    log.info("  + avg " + (sum / count));
  }

  /**
   * Consumes the page returned by the InternetsCrawler.
   */
  public void consume(String page) {
    if (page.indexOf("Document not found") > 0) {
      return;
    }
    String[] lines = page.split("Au semnat:</p>");
    String peopleLine =
      lines[1].substring(lines[1].indexOf(">") + 1, lines[1].indexOf("<", 5));
    String[] people = peopleLine.split(", ");

    for (String name : people) {
      name = name.replace("'", "&rsquo;");
      DbManager.insertEuroParliamentPresence(name, (long)(d.getTime() / 1000));
    }
    //log.info(d + " " + peopleLine + " " + people.length);

    max = Math.max(people.length, max);
    sum += people.length;
    count++;
  }
}

