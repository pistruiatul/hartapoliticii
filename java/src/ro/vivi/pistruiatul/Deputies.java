package ro.vivi.pistruiatul;

import java.io.File;
import java.io.RandomAccessFile;
import java.util.HashMap;
import java.util.logging.Logger;
import java.util.regex.Matcher;
import java.util.regex.Pattern;

/**
 * Holds, parses and does everything that is attached with the list of deputies.
 * It will usually just load the list from our own file / database.
 * @author vivi
 *
 */
public class Deputies {
  Logger log = Logger.getLogger("ro.vivi.pistruiatul.Deputies");

  Pattern percent =
    Pattern.compile("(.*)votes missed</a> \\((.*)%\\), <a href=(.*)");

  /** A repository of deputies */
  public HashMap<String, Deputy> deps = new HashMap<String, Deputy>();

  /**
   * The url of a random law containing an initial set of deputies from which
   * we will start making a list of each of them. Then, once we have.
   */
  public static String SEED_LAW_URL =
    "http://www.cdep.ro/pls/steno/eVot.Nominal?uniqueId=502";

  /**
   * Read the currently existing list of deputies from the database.
   */
  public Deputies(String year) {
    loadFromDb(year);
  }

  /** Loads the deputies from the database */
  private void loadFromDb(String year) {
    DbManager.loadDeputies(deps, year);
    log.info("Deputies loaded from db: " + deps.size());
  }

  /**
   * Reads more info about the deputies and updates the database with it.
   */
  public void enhanceInfo() {
    for (Deputy dep : deps.values()) {
      dep.getInfoFromSite();
      // here I should update the database.
    }
  }

  public Deputy getDeputyForIdm(int idm) {
    for (Deputy dep : deps.values()) {
      if (dep.idm == idm) {
        return dep;
      }
    }
    return null;
  }

  public int getIdForIdm(int idm) {
    for (Deputy deputy : deps.values()) {
      if (deputy.idm == idm) {
        return deputy.idm;
      }
    }
    return -1;
  }

  /**
   * Checks whether this deputy is already in the chamber of deputies.
   * @param name
   * @return
   */
  public boolean contains(String name) {
    return deps.containsKey(name);
  }

  /**
   * Returns a deputy with this exact name.
   * @param name The exact name of the deputy.
   * @return The deputy object, or null if this deputy does not exist.
   */
  public Deputy get(String name) {
    return deps.get(name);
  }

  /**
   * Add a deputy to the data model.
   */
  public void add(Deputy deputy) {
    deps.put(deputy.name, deputy);

    // Add this deputy to mysql.
    storeInMysql(deputy);
  }

  /**
   * Given a deputy object, it stores it in mysql. After that, it stores the
   * mysql id in the object too (so that when a vote or whatever else needs to
   * reference this, it just knows).
   * @param deputy
   */
  public void storeInMysql(Deputy deputy) {
    DbManager.insertDeputy(deputy);
  }

}
