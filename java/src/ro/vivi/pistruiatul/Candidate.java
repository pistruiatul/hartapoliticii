package ro.vivi.pistruiatul;

/**
 * A data structure for holding a candidate in the parliamentary elections.
 * This holds the name, votes and other info about the candidate.
 * @author vivi
 *
 */
public class Candidate implements Comparable {
  public int id;
  public int idperson;

  /** Name of the candidate. */
  public String name;

  /** Number of votes. */
  public int votes;

  /** The seat that he was running for. */
  public Seat runsForSeat;

  /** The seat that he won. */
  public Seat wonSeat;

  /** The party he belongs to. */
  public int party;

  public String partyName;

  public int compareTo(Object o) {
    Candidate c = (Candidate)o;
    return c.votes - this.votes;
  }

  public String toString() {
    return name + "(" + votes + (wonSeat != null ? "/w" : "") + ")";
  }
}
