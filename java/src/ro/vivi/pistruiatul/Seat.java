package ro.vivi.pistruiatul;

/**
 * More like a structure for holding a seat in the House Of Deputies.
 * @author vivi
 *
 */
public class Seat {
  /** Names are like "2 Alba" or "4 Bihor" */
  public String name;

  /** Whether this is taken or not. */
  public Candidate winner;

  /** The total number of votes for this seat. */
  public int totalVotes;

  /** The closest runner up */
  public Candidate runnerUp;

  /** How many more votes should the runner up have had to change the outcome */
  public int runnerUpVotes = Integer.MAX_VALUE;

  /** Why would the runner up have won */
  public String runnerUpReason;

  /**
   * Debugging purposes method.
   */
  @Override
  public String toString() {
    return name + "(" + totalVotes + ")";
  }

  /**
   * Print a detailed string, still for debugging purposes.
   */
  public String toDetailedString() {
    return name + " decided by " + runnerUpVotes + " votes.\n" +
      "  Winner: " + winner + "\n" +
      "  RunnerUp: " + runnerUp + "\n" +
      "  How: " + runnerUpReason;
  }
}
