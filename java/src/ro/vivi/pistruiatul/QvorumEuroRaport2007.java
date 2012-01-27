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
public class QvorumEuroRaport2007 implements PageConsumer {
  Logger log = Logger.getLogger("ro.vivi.pistruiatul.QvorumEuroRaport2007");

  /**
   * Initializes the url's for the parties that have candidates in this race.
   */
  public QvorumEuroRaport2007() {
  }

  /**
   * Crawls the pages with the candidates.
   */
  public void run() {
    DbManager.deleteQvorum2007();
    this.consume(
        InternetsCrawler.getUtfStringFromDisk("euro_raport_qvorum.txt"));
  }

  Pattern p = Pattern.compile("(.*) \\((.*)\\)(\\s*)");

  /**
   * Consumes the page returned by the InternetsCrawler.
   */
  public void consume(String page) {
    String[] euro = page.split("====\n");

    for (int i = 1; i < euro.length; i++) {
      String[] lines = euro[i].split("\n");
      Matcher m = p.matcher(lines[0]);
      String name = m.matches() ? m.group(1) : "none";

      name = name.trim();

      log.info("["+ name + "]");

      // The rest of the lines are text we should put together.
      StringBuilder sb = new StringBuilder();
      int j = 2;

      do {
        if (lines[j].startsWith("/+")) {
          while (!lines[j].startsWith("+/")) {
            j++;
          }
          j++;
        }

        sb.append(lines[j]).append("<br>\n");
        j++;
      } while (j < lines.length);

      DbManager.insertQvorumEntry(name, sb.toString());
    }

    log.info("total candidati: " + euro.length);
  }

}

