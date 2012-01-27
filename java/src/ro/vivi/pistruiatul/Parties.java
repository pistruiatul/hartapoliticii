package ro.vivi.pistruiatul;

import com.mysql.jdbc.StringUtils;

import java.util.HashMap;
import java.util.logging.Logger;

/**
 * Keep track of parties.
 * @author vivi
 *
 */
public class Parties {
  static Logger log = Logger.getLogger("ro.vivi.pistruiatul.Parties");

  /** Parties */
  public static HashMap<String, Integer> parties =
      new HashMap<String, Integer>();

  static {
    DbManager.loadParliamentParties(parties);
    log.info("Parties loaded from db: " + parties.size());
  }

  /** Returns the id of the party by name */
  public static int getPartyId(String name) {
    return addParty(name);
  }

  /**
   * Return the name of the party by id.
   * @param id The id of the party we are looking for.
   * @return The name of the party.
   */
  public static String getPartyName(int id) {
    for (String party : parties.keySet()) {
      Integer pid = parties.get(party);
      if (pid == id) {
        return party;
      }
    }
    return "";
  }

  /**
   * Add a party. If the party already exists, this is a no-op and it just
   * returns the id of the party with this name.
   * @param name The name of the party.
   * @return The id of the party.
   */
  private static int addParty(String name) {
    String newName = name;
    if (name.contains("Mare")) log.info("What party is this? [" + name + "]");

    if (name.startsWith("Indep") || name.startsWith("INDEP") ||
        name.startsWith("Prog") || name.startsWith("Mixt") ||
        name.startsWith("Neafiliati") || name.startsWith("GRP"))
      newName = "Independent";

    if (name.startsWith("Mino")) newName = "Minoritati";
    if (name.equals("PDL")) newName = "PD-L";

    if (!parties.containsKey(newName)) {
      log.info("Trying to add [" + newName + "]");
      Integer id = DbManager.insertParty(newName);
      parties.put(newName, id);
    }

    return parties.get(newName);
  }

}
