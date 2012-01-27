package ro.vivi.pistruiatul;

import java.util.HashMap;
import java.util.logging.Logger;
import java.util.regex.Matcher;
import java.util.regex.Pattern;

/**
 * Loads the cdepLaws for the senate from the files present on disk. The files are
 * result files from senat.ro, because unfortunately we couldn't just load
 * them from there without significant headache.
 *
 * Each file contains the cdepLaws voted in one day.
 * @author vivi
 *
 */
public class SenateLaws {
  Logger log = Logger.getLogger("ro.vivi.pistruiatul.SenateLaws");

  Main main;

  Pattern lawPattern =
    Pattern.compile("(.*)AppID=([^\">]*)\"(.*)");

  /**
   * Keeping track of the cdepLaws, indexed by the id of the law.
   */
  HashMap<String, SenateLaw> laws = new HashMap<String, SenateLaw>();

  public SenateLaws(Main main, String year) {
    this.main = main;
    loadFromDb(year);
  }

  /** Loads the deputies from the database */
  private void loadFromDb(String year) {
    DbManager.loadSenateLaws(this, year);
    log.info("Senate cdepLaws loaded from db: " + laws.size());
  }

  /**
   * Read all the cdepLaws from files.
   */
  public void getLawsFromFiles() {
    int count = 0;
    for (int i = 1; i <= 81; i++) {
      String padding = (i < 10 ? "00" : (i < 100 ? "0" : ""));
      String fname = "senat/" + padding + i;
      String data = InternetsCrawler.getUtfStringFromDisk(fname);
      String[] lines = data.split("\n");

      for (String line : lines) {
        if (line.indexOf("DetaliuVOT") > -1) {
          // get that link
          int end = line.indexOf("\"", line.indexOf("AppID="));
          if (end == -1) {
            log.info(line.indexOf("AppID=") + " " + line);
          }
          if (line.indexOf("AppID=") == -1) {
            log.info(line.indexOf("AppID=") + " " + line);
          }
          String lawId = line.substring(line.indexOf("AppID=") + 6, end);

          SenateLaw law = laws.get(lawId);
          if (law == null) {
            law = new SenateLaw(this);
            law.appId = lawId;

            law.id = DbManager.insertSenateLaw(law);
            laws.put(lawId, law);
          } else {
            //log.info("Collision?" + lawId + " " + fname);
          }

          law.crawlLawFromSite(lawId);

          // Create a SenateLaw object and tell it to load itself from that path.
          count++;
        }
      }
    }
    log.info("Senate cdepLaws: " + count);
  }

  /**
   * Returns a reference to the pool of deputies.
   */
  public Senators getSenators() {
    return main.senators;
  }
}
