package ro.vivi.pistruiatul;

import java.util.HashMap;
import java.util.logging.Logger;
import java.util.regex.Matcher;
import java.util.regex.Pattern;

/**
 * Data model for the cdepLaws that were voted electronically.
 * @author vivi
 */
public class CdepLaws implements PageConsumer {
  Logger log = Logger.getLogger("ro.vivi.pistruiatul.CdepLaws");

  /** The url from which we will grab the daily votes. */
  public static String SEED_PATH =
    "/pls/steno/eVot.Data?dat={DATE}&cam=2&idl=1";

  /** Keeping track of the cdepLaws, indexed by the id of the law. */
  HashMap<String, CdepLaw> laws = new HashMap<String, CdepLaw>();

  /**
   * A reference to Main that will help me get references to other pieces of
   * this puzzle.
   */
  private Main main;

  /**
   * Initializes some of the much needed data structures.
   * @param year The year that needs to be loaded.
   */
  public CdepLaws(Main main, String year) {
    this.main = main;
    loadFromDb(year);
  }

  /** Loads the deputies from the database */
  private void loadFromDb(String year) {
    DbManager.loadCdepLaws(this, year);
    log.info("CdepLaws loaded from db: " + laws.size());
  }

  /**
   * Fetches the list of cdepLaws from the site.
   */
  public void loadLawsFromSite() {
    for (int page = 1; page <= 36; page++) {
      String path = SEED_PATH.replace("{PAGE}", "" + page);
      log.info("Fetching page: " + path);
      InternetsCrawler.enqueue(Main.HOST, path, this);
    }
  }

  /**
   * Enhances the information on cdepLaws by figuring out who proposed a certain law
   * and if it was rejected or not.
   */
  public void enhanceInfo() {
    for (CdepLaw cdepLaw : laws.values()) {
      cdepLaw.loadDetailsFromSite();
    }
  }

  public CdepLaw getLaw(String projectNumber, String idp) {
    if (!laws.containsKey(projectNumber)) {
      CdepLaw cdepLaw = new CdepLaw(projectNumber, this);
      cdepLaw.idp = idp;

      cdepLaw.link = "http://www.cdep.ro/pls/proiecte/upl_pck.proiect?idp=" + idp;
      // If the cdepLaw is not already in the hash, put it there
      laws.put(projectNumber, cdepLaw);
      cdepLaw.id = DbManager.insertCdepLaw(cdepLaw);

      log.info("CdepLaw " + projectNumber + " id=" + cdepLaw.id);

      cdepLaw.loadDetailsFromSite();

      return cdepLaw;
    } else {
      return laws.get(projectNumber);
    }
  }

  /**
   * @inheritDoc
   */
  public void consume(String page) {
    Pattern dateAndTime =
      Pattern.compile("<a href=\"eVot\\.Nominal\\?idv=([0-9]+)\">(.*)</a>");
    Pattern numarLink =
      Pattern.compile("<A HREF=\"/pls/proiecte/upl_pck\\.proiect\\?idp=" +
          "([0-9]*)\" TARGET=\"PROIECTE\">PL ([0-9/]*)</A>");

    // consume the page that came back.
    String[] lines = page.split("\n");
    int i = 0;
    while (i < lines.length) {
      String line = lines[i];
      Matcher m = dateAndTime.matcher(line);
      if (m.matches()) {
        // We have a cdepLaw, let's create an object
        String idv = m.group(1);
        String datetime = m.group(2);

        // Now I might have a line with the type which can be Adoptare,
        // Respingere If it doesn't start with <A HREF
        i += 4;
        Boolean isFinalVote = lines[i].startsWith("<A HREF");
        String type = !lines[i].startsWith("<A HREF") ? lines[i++] : "Ordinar";

        // it means it's a cdepLaw
        Matcher nl = numarLink.matcher(lines[i]);
        String idp = nl.matches() ? nl.group(1) : "-1";
        String projectNumber = nl.matches() ? nl.group(2) : "NonFinalVote" + idv;

        if (nl.matches()) {
          i++;
        }

        //CdepLaw cdepLaw = cdepLaws.containsKey(projectNumber) ?
        //          cdepLaws.get(projectNumber) : new CdepLaw(projectNumber, this);
        CdepLaw cdepLaw = getLaw(projectNumber, idp);

        cdepLaw.setDescription(lines[i]);
        cdepLaw.addVote(idv, type, datetime);

        if (!laws.containsKey(projectNumber)) {
          // If the cdepLaw is not already in the hash, put it there
          laws.put(projectNumber, cdepLaw);
          cdepLaw.id = DbManager.insertCdepLaw(cdepLaw);
        }
        cdepLaw.loadVotesFromSite(idv);
      }
      i++;
    }
  }

  /**
   * Returns a reference to the pool of deputies.
   */
  public Deputies getDeputies() {
    return main.deputies;
  }
}
