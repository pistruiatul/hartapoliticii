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
public class ListaCatavencu implements PageConsumer {
  Logger log = Logger.getLogger("ro.vivi.pistruiatul.ListaCatavencu");

  int count = 0;

  /**
   * Initializes the url's for the parties that have candidates in this race.
   */
  public ListaCatavencu() {
  }

  /**
   * Crawls the pages with the candidates.
   */
  public void run() {
    DbManager.deleteCatavencu();
    // Send the URL's to be consumed.
    for (int i = 1; i <= 40; i++) {
      this.consume(InternetsCrawler.getUtfStringFromDisk("catavencu/" +
          i + ".txt"));
    }
    log.info("Total: " + count + " people;");
  }

  Pattern p = Pattern.compile("(.*)\\((.*)\\)(\\s*)");

  /**
   * Consumes the page returned by the InternetsCrawler.
   */
  public void consume(String page) {
    String[] lines = page.split("\n");
    log.info(lines[0] + " " + lines.length);

    String url = lines[0];

    for (int i = 2; i < lines.length; i++) {
      if (!lines[i].equals("")) {
        // get the candidate's name
        Matcher m = p.matcher(lines[i].trim());
        if (m.matches()) {
          String name = m.group(1);
          String party = m.group(2);

          log.info("Name: " + name);
          DbManager.insertCatavencu(name, lines[i+1], url, party);
          count++;
        }
      }
    }
  }
}

