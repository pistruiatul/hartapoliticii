package ro.vivi.pistruiatul;

import java.util.logging.Logger;
import java.util.regex.Matcher;
import java.util.regex.Pattern;

/**
 * This class is responsible for keeping info about the video recordings of
 * a particular deputy. For now it only knows a link, total talk time, some
 * already agreggated info.
 *
 * @author vivi
 */
public class Video implements PageConsumer {
  static Logger log = Logger.getLogger("ro.vivi.pistruiatul.Video");

  static private String VIDEO_URL =
    "pls/steno/steno.lista?uniqueId={IDV}&leg=2004&idl=1";

  /** A reference to the deputy that this videos are for. */
  private Deputy deputy;

  /** The video id used by cdep.ro to show video stuff. */
  private String idv;

  Pattern videoInfoPattern =
    Pattern.compile("(.*)<b>(\\d*)</b> la <b>(\\d*)</b> puncte din sumarele " +
        "a <b>(\\d*)</b>(.*)video:</td><td class=\"textn\"><b>([^>]*)</td>" +
        "</tr></table></td>");

  Pattern timeLength = Pattern.compile("(\\d*)h(\\d*)m(\\d*)s");

  public Video(Deputy deputy) {
    this.deputy = deputy;
  }

  /**
   * Sets the video id for this.
   * @param idv
   */
  public void setIdv(String idv) {
    this.idv = idv;
  }

  /**
   * Fetches the info from the site.
   */
  public void crawlInfoFromSite() {
    if (idv == null) {
      return;
    }
    String path = VIDEO_URL.replace("{IDV}", idv);
    InternetsCrawler.enqueue(Main.HOST, path, this);
  }

  /**
   * Parses the page containing the video information.
   */
  public void consume(String page) {
    String[] lines = page.split("\n");

    int i = 0;
    while (i < lines.length) {
      Matcher m = videoInfoPattern.matcher(lines[i]);
      if (m.matches()) {
        int sessions = Integer.parseInt(m.group(3));
        String length = m.group(6);

        int seconds = getSecondsFromString(length);

        log.info("Video for " + deputy.name + " (idm:" + deputy.idm + "): " +
            sessions + " " + length + " " + seconds);
        DbManager.insertVideo(deputy, idv, sessions, seconds);
      }
      i++;
    }
  }

  /**
   * Gets the number of seconds from a poorly formatted length string :-)
   */
  private int getSecondsFromString(String length) {
    Matcher match = timeLength.matcher(length);
    if (match.matches()) {
      int h = Integer.parseInt(match.group(1));
      int m = Integer.parseInt(match.group(2));
      int s = Integer.parseInt(match.group(3));
      return h * 3600 + m * 60 + s;
    }
    return 0;
  }

}
