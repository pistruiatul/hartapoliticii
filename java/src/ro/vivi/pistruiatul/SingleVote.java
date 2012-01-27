package ro.vivi.pistruiatul;

/**
 * A single vote is just a data structure with a few bits of information about
 * the content of the vote, the person that casted it, and some references to
 * the voting session this vote is a part of.
 */
class SingleVote {
  /**
   * The time at which the vote happened. The same as the VotingSession, so
   * we could probably eliminate this.
   * TODO(vivi): Eliminate this, replace with a reference to a voting session.
   */
  public long time;

  /**
   * The unique id of the voting session that this single vote belongs to.
   * TODO(vivi): Eliminate this, replace with a reference to a voting session.
   */
  public String uniqueId;

  /**
   * What is the content of this vote.
   */
  public Type type;

  /**
   * A reference to the participant that casted this vote.
   */
  public Deputy deputy;



  /**
   * The type of a vote.
   *   YES
   *   NO
   *   Abstain
   *   Missing - the person was present but did not want to express a vote.
   *   Error - used to return error codes.
   */
  public static enum Type {
    DA, NU, AB, MI
  }

  // We allow this for votes that we construct in stages.
  public SingleVote(){
  }

  public SingleVote(long time, String uniqueId, String voteStr, Deputy deputy) {
    this.time = time;
    this.uniqueId = uniqueId;
    this.type = getTypeFromString(voteStr);
    this.deputy = deputy;
  }

  /**
   * Given a string, returns the type of the vote.
   * @param voteStr The string holding the vote.
   * @return The type.
   */
  public static Type getTypeFromString(String voteStr) {
    if (voteStr.equals("DA")) {
      return Type.DA;
    } else if (voteStr.equals("NU")) {
      return Type.NU;
    } else if (voteStr.startsWith("Ab")) {
      return Type.AB;
    }
    return Type.MI;
  }

  /**
   * Returns this vote's type as a string.
   * @return A string representation of this vote's type.
   */
  public String getTypeAsString() {
    if (type == Type.DA) {
      return "DA";
    } else if (type == Type.NU) {
      return "NU";
    } else if (type == Type.AB) {
      return "Ab";
    }
    return "-";
  }

  /**
   * Used for debugging purposes.
   * @return Some sort of string representation of this object.
   */
  public String toString() {
    return deputy.name + " " + type + " " + time;
  }
}