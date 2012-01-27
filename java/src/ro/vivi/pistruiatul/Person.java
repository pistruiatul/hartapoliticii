package ro.vivi.pistruiatul;

import java.util.HashMap;

/**
 * Represents a person from the people database... almost.
 * @author vivi
 */
public class Person {
  /**
   * The id in the database.
   */
  public int id;

  /**
   * The name of this person.
   */
  public String name;

  /**
   * The database of facts for a certain person. Stuff like bday, image url,
   * profession, occupation.
   */
  public HashMap<String, String> facts = new HashMap<String, String>();

  /**
   * For debugging purposes, write out all we know about this person.
   */
  public String toString() {
    StringBuilder sb = new StringBuilder(id + ". " + name);
    for (String key : facts.keySet()) {
      String value = facts.get(key);
      sb.append(", " + key + ": " + value);
    }
    return sb.toString();
  }

  /**
   * Get a certain fact about this person.
   * @param fact
   * @return
   */
  public String getFact(String fact) {
    return facts.get(fact);
  }
}
