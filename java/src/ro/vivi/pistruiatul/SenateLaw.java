package ro.vivi.pistruiatul;

import java.util.Date;
import java.util.HashMap;
import java.util.logging.Logger;
import java.util.regex.Matcher;
import java.util.regex.Pattern;

public class SenateLaw implements PageConsumer {

  static Logger log = Logger.getLogger("ro.vivi.pistruiatul.SenateLaw");

  private static final String LAW_PATH =
    "/votpublic/DetaliuVOT.aspx?AppID={ID}";

  SenateLaws laws;

  HashMap<Senator, Integer> votes = new HashMap<Senator, Integer>();

  public String appId;

  public int id;

  public SenateLaw(SenateLaws laws) {
    this.laws = laws;
  }

  /**
   * Goes to the senate site and fetches a law.
   * @param appId
   */
  public void crawlLawFromSite(String appId) {
    this.appId = appId;
    String path = LAW_PATH.replace("{ID}", appId);
    InternetsCrawler.enqueue("www.senat.ro", path, this);
  }

  /** Extracts the deputy ID number and his name from the line with his name */
  Pattern senatorVotePattern =
    Pattern.compile("<td><font(?:[^>]*)>([^<>]*)</font></td>" +
      "<td><font(?:[^>]*)>([^<>]*)</font></td>" +
      "<td><font(?:[^>]*)>(?:\\(\\d\\))?([^<>]*)</font></td>" +
      "<td><font(?:[^>]*)>([^<>]*)</font></td>" +
      "<td><font(?:[^>]*)>([^<>]*)</font></td>" +
      "<td><font(?:[^>]*)>([^<>]*)</font></td>" +
      "<td><font(?:[^>]*)>([^<>]*)</font></td>(?:.*)?");
  /*
  <td><font color="#333333">Taracila</font></td>
  <td><font color="#333333">Doru Ioan</font></td>
  <td><font color="#333333">&nbsp;</font></td>
  <td><font color="#333333">&nbsp;</font></td>
  <td><font color="#333333">X</font></td>
  <td><font color="#333333">&nbsp;</font></td>
  <td><font color="#333333">&nbsp;</font></td>
  */

  Pattern datePattern =
    Pattern.compile("(?:.*)<br />(\\d*)-(\\d*)-(\\d*) (\\d*):(\\d*)</span>");

  //<span id="DescriereLunga_ctl00_DESCRIERE_LUNGALabel">L516/2007|Vot final<br /><br />09-10-2007 12:24</span>

  /**
   * Given the text of a law with all the votes, go ahead and parse them votes
   * from there and put them in the database.
   */
  public void consume(String data) {
    String[] lines = data.split("\n");
    Senators senators = laws.getSenators();
    int votesCount = 0;

    int i = 0;
    long time = 0;
    while (i < lines.length) {
      String line = lines[i].trim();

      Matcher m = senatorVotePattern.matcher(line);
      Matcher d = datePattern.matcher(line);
      if (m.matches()) {
        String lastName = m.group(1);
        String firstName = m.group(2);
        String party = m.group(3);

        String vote = "x";
        if (m.group(4).equals("X")) vote = "DA";
        if (m.group(5).equals("X")) vote = "NU";
        if (m.group(6).equals("X")) vote = "Ab\u0163inere";
        if (m.group(7).equals("X")) vote = "-";

        party = party.trim();
        party = party.indexOf("INDEP") > -1 ? "-" : party;

        String fullName = lastName + " " + firstName;
        fullName = fullName.trim();
        fullName = fullName.replace("-", " ");
        if (fullName.equals("Cretu Ovidiu Tudor")) {
          fullName = "Cretu Ovidiu Teodor";
        }

        //log.info(party + " " + lastName + " " + firstName);
        Senator sen = senators.get(fullName);
        if (sen == null) {
          log.info("n-am gasit pe " + fullName + " " + appId);
        } else {
          // TODO(vivi) determine the time of this vote
          if (time == 0) {
            log.info(data);
            log.warning("time is zero in " + appId);
            System.exit(1);
          }
          sen.setParty("foo", party, time);
          DbManager.insertSenatorVote(appId, vote, sen, this, time);
        }
        votesCount++;

      } else if (d.matches()) {
        int day = Integer.parseInt(d.group(1));
        int month = Integer.parseInt(d.group(2)) - 1;
        int year = Integer.parseInt(d.group(3)) - 1900;

        int hour = Integer.parseInt(d.group(4));
        int minute = Integer.parseInt(d.group(5));

        Date date = new Date(year, month, day);
        date.setHours(hour);
        date.setMinutes(minute);

        time = date.getTime();

      }
      i++;
    }
    if (votesCount == 0) {
      //log.info(data);
      log.info("CdepLaw " + appId + " has " + votesCount + " " + lines.length);
      //System.exit(1);
    }
  }

}
