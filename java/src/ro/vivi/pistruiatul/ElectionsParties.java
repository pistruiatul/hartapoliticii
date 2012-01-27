package ro.vivi.pistruiatul;

import java.util.HashMap;
import java.util.logging.Logger;

public class ElectionsParties {
  static Logger log = Logger.getLogger("ro.vivi.pistruiatul.ElectionsParties");

  /** Parties */
  public static HashMap<String, Integer> parties =
      new HashMap<String, Integer>();

  static {
    DbManager.loadElectionsParties(parties);
    log.info("Election parties loaded from db: " + parties.size());
  }

  /** Returns the id of the party by name */
  public static int getPartyId(String name) {
    return addParty(name);
  }

  /** Add a party */
  private static int addParty(String name) {
    name = name.trim();
    if (!parties.containsKey(name)) {
      Integer id = new Integer(DbManager.insertElectionsParty(name));
      parties.put(name, id);
    }

    return parties.get(name).intValue();
  }
}
