package ro.vivi.pistruiatul;

public class Utils {

  public static String replaceDiacritics(String msg) {
    String s = msg.replace("\u021B", "t");
    s = s.replace("\u021A", "T");
    s = s.replace("\u0163", "t");
    s = s.replace("\u0162", "T");

    s = s.replace("\u00EE", "i");
    s = s.replace("\u00CE", "I");

    s = s.replace("\u0219", "s");
    s = s.replace("\u0218", "S");
    s = s.replace("\u015F", "s");
    s = s.replace("\u015E", "S");

    s = s.replace("\u00E9", "e");
    s = s.replace("\u00F3", "o");
    s = s.replace("\u00F6", "o");

    s = s.replace("\u0103", "a");
    s = s.replace("\u0102", "A");
    s = s.replace("\u00E1", "a");
    s = s.replace("\u00E2", "a");
    s = s.replace("\u00C2", "A");
    return s;
  }

}
