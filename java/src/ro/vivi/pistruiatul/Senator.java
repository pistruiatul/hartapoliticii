package ro.vivi.pistruiatul;

/**
 * Holds data for a senator.
 * @author vivi
 *
 */
public class Senator extends Deputy {
  // TODO(vivi): We no longer seem to use this. Write some principles and clean
  // this stuff up.
  protected static String SEN_URL =
    "pls/parlam/structura.mp?idm={IDM}&cam=1&leg=2004";

  /**
   * Sets the name for this senator.
   * @param name
   */
  public Senator(String name, String cdepId) {
    super(name, cdepId);
  }

  public int getIdPersonFromDb() {
    return DbManager.getIdPersonForSenator("2008", this);
  }

  protected String getPath() {
    return SEN_URL.replace("{IDM}", cdepId);
  }

  @Override
  public void updateDateTime(long start, long end, String motif, int id) {
    DbManager.updateSenator(start, end, motif, id);
  }

  @Override
  public void updateImage(String url) {
    DbManager.updateSenatorImage(this, url);
  }

  @Override
  public String getRoom() {
    return "senat";
  }
}
