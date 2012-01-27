package ro.vivi.pistruiatul;

import java.sql.Date;
import java.util.logging.Logger;
import java.util.regex.Matcher;
import java.util.regex.Pattern;

public class EuroDeputies implements PageConsumer {
  Logger log = Logger.getLogger("ro.vivi.pistruiatul.EuroDeputies");

  /** The link to the Main app that will allow me to query for senators
   * and deputies to identify where are the eurodeputies from.
   */
  private Main main;

  public EuroDeputies(Main main) {
    this.main = main;
  }

  /**
   * Load the deputies from a file. I will still call the consume function.
   */
  public void loadEuroFromFile() {
    String fname = "europarlamentari.html";
    String data = InternetsCrawler.getUtfStringFromDisk(fname);
    consume(data);
  }

  Pattern deputyLink =
    Pattern.compile("<td class=\"headlinetext\"><b><A HREF=\"" +
        "/pls/parlam/structura\\.mp\\?idm=(\\d*)&cam=(\\d)&leg=(\\d*)\">" +
        "([^>]*)</A></b>");
  Pattern dateCellPattern =
    Pattern.compile("<td align=\"center\" class=\"headlinetext\" nowrap>" +
        "([^>]*)</td>");

  /**
   * Given the page with the dates, parse it.
   */
  public void consume(String data) {
    String[] lines = data.split("\n");
    int count = 0;
    int i = 0;
    while (i < lines.length) {
      String line = lines[i];
      Matcher m = deputyLink.matcher(line);
      if (m.matches()) {
        int idm = Integer.parseInt(m.group(1));
        int chamber = Integer.parseInt(m.group(2));
        String name = m.group(4);

        // This should be fixed, I should make a Person abstract class and also
        // a Chamber class that would contain a set of Persons with their ID's
        // and their table name and stuff. For now, we can continue a little bit
        // with the duplication and code.
        int id = -1;
        /*
            chamber == 1 ?
                 main.senators.getIdForIdm(idm) :
                 main.deputies.getIdForIdm(idm);
        */
        if (id != -1) {
          // figure out the timein and timeout values.
          Matcher dateIn = dateCellPattern.matcher(lines[i + 3]);
          Matcher dateOut = dateCellPattern.matcher(lines[i + 4]);
          if (dateIn.matches() && dateOut.matches()) {
            count++;

            long left = getTimeFromDateString(dateIn.group(1));
            long back = getTimeFromDateString(dateOut.group(1));
            log.info("Euro trash: " + id + " " + chamber + " " +
                left + " " + back);

            DbManager.insertAwayTime(id, chamber, left, back);
          }
        }
      }
      i++;
    }

    log.info("Euro parlamentari: " + count);
  }

  Pattern datePattern = Pattern.compile("(\\d*)\\.(\\d*)\\.(\\d*)");

  /**
   * Returns the date from a string that is "dd.mm.yyyy"
   * @param str
   * @return
   */
  long getTimeFromDateString(String str) {
    // 12 decembrie 2004
    Matcher m = datePattern.matcher(str.trim());
    if (!m.matches() || str == "") {
      // the default start date is.... ? 01.jan.2007?
      return (new Date(107, 01, 01)).getTime();
    }
    int day = Integer.parseInt(m.group(1));
    int month = Integer.parseInt(m.group(2));
    int year = Integer.parseInt(m.group(3));

    Date date = new Date(year - 1900, month, day);
    return date.getTime();
  }
}
