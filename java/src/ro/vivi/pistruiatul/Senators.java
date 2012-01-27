package ro.vivi.pistruiatul;

import java.util.HashMap;
import java.util.logging.Logger;
import java.util.regex.Matcher;
import java.util.regex.Pattern;

/**
 * Keeps track of Senators.
 * @author vivi
 *
 */
public class Senators implements PageConsumer {
  Logger log = Logger.getLogger("ro.vivi.pistruiatul.Senators");

  public HashMap<String, Senator> senators = new HashMap<String, Senator>();

  Pattern senatorLinePattern =
    Pattern.compile("(\\d*)\\. ([\\D]*)(\\d*) (\\S*) (.*)");

  public Senators(String year) {
    loadFromDb(year);
  }

  /** Loads the deputies from the database */
  private void loadFromDb(String year) {
    DbManager.loadSenators(year, senators);
    log.info("Senators loaded from db: " + senators.size());
  }

  /**
   * Crawl it from the site
   */
  public void crawlFromSite() {
    String path = "/pls/parlam/structura.de?leg=2004&cam=1";
    InternetsCrawler.enqueue(Main.HOST, path, this);
  }

  /**
   * Returns a senator based on the name. Returns NULL if the senator with this
   * name does not exist.
   * @param name The exact name of the senator.
   * @return A senator object, or null if not found.
   */
  public Senator get(String name) {
    // Replace the diacritics in the name. Senat.ro is a retarded website.
    name = name.replace("&#238;", "i");
    name = name.replace("&#226;", "a");
    name = name.replace("&#225;", "a");
    name = name.replace("&#243;", "o");
    name = name.replace("&#252;", "u");
    name = name.replace("&#193;", "A");
    name = name.replace("&#233;", "e");
    name = name.replace("&#246;", "o");

    name = name.replace("  ", " ");
    name = Utils.replaceDiacritics(name);

    return senators.get(name);
  }

  public int getIdForIdm(int idm) {
    for (Senator senator : senators.values()) {
      if (senator.idm == idm) {
        return senator.idm;
      }
    }
    return -1;
  }

  Pattern senatorLink =
    Pattern.compile("<td class=\"headlinetext\"><b><A HREF=\"" +
        "/pls/parlam/structura.mp\\?idm=(\\d*)&cam=1&leg=2004\">([^>]*)" +
        "</A></b></td>");

  /**
   * Consumes the page with the list of senators from the cdep site.
   */
  public void consume(String page) {
    String[] lines = page.split("\n");
    for (String line : lines) {
      Matcher m = senatorLink.matcher(line);
      if (m.matches()) {
        String name = m.group(2);
        String idm = m.group(1);

        String asciiName = Utils.replaceDiacritics(name);
        asciiName = asciiName.replace("-", " ");

        Senator s = new Senator(asciiName, idm);

        if (!senators.containsKey(asciiName)) {
          senators.put(asciiName, s);
          DbManager.insertSenator(s);
        }

        s.getInfoFromSite();
      }
    }
  }

}
