package ro.vivi.pistruiatul;

import java.io.BufferedInputStream;
import java.io.File;
import java.io.FileInputStream;
import java.io.FileNotFoundException;
import java.io.FileOutputStream;
import java.io.IOException;
import java.math.BigInteger;
import java.nio.ByteBuffer;
import java.nio.CharBuffer;
import java.nio.charset.CharacterCodingException;
import java.nio.charset.Charset;
import java.nio.charset.CharsetDecoder;
import java.security.MessageDigest;
import java.security.NoSuchAlgorithmException;
import java.util.logging.Logger;

import HTTPClient.HTTPConnection;
import HTTPClient.HTTPResponse;
import HTTPClient.ModuleException;

/**
 * Manages downloading of pages from the internet and maybe caching them right
 * here, on disk.
 */
public class InternetsCrawler {
  static Logger log = Logger.getLogger("ro.vivi.pistruiatul.InternetsCrawler");

  public static boolean useCache = true;
  private static final String CACHE_DIR = "data.cached/";

  /**
   * A private constructor to make sure this remains a static class.
   */
  private InternetsCrawler() {

  }

  /**
   * Adds an url to the queue, without guaranteeing synchronicity. The consumer
   * handler will be called when the page comes.
   * @param host The host.
   * @param path The path.
   * @param consumer Who eventually consumes this page.
   */
  public static void enqueue(String host, String path, PageConsumer consumer) {
    consumer.consume(getPage(host, path, "ISO-8859-2"));
  }

  /**
   * Adds an url to the queue, without guaranteeing synchronicity. The consumer
   * handler will be called when the page comes.
   * @param host The host.
   * @param path The path.
   * @param consumer Who eventually consumes this page.
   * @param charset The charset under which to get the page.
   */
  public static void enqueue(String host, String path, PageConsumer consumer,
      String charset) {
    consumer.consume(getPage(host, path, charset));
  }

  /**
   * Does all the magic that the dumb HTTPClient needs to just get me the page
   * that I want, without me caring about all the exceptions.
   * @param host
   * @param path
   * @return
   */
  private static String getPage(String host, String path, String charset) {
    String cache = getFromCache(host, path);
    if (cache != null) {
      //log.info("Cache hit for " + host + path);
      return cache;
    }

    try {
      Thread.sleep(1000);
    } catch (Exception e) {};

    String data = "";

    try {
      HTTPConnection con = new HTTPConnection(host);
      HTTPResponse rsp = con.Get(path);
      // Call getText to block the connection until it gets the whole page.
      rsp.getText();
      byte[] bytes = rsp.getData();

      // Create decoder for ISO-8859-2
      Charset cset = Charset.forName(charset);
      CharsetDecoder decoder = cset.newDecoder();

      try {
        ByteBuffer bbuf = ByteBuffer.wrap(bytes);

        // Convert ISO-LATIN-2 bytes in a ByteBuffer to a character ByteBuffer
        // and then to a string. The new ByteBuffer is ready to be read.
        CharBuffer cbuf = decoder.decode(bbuf);
        data = cbuf.toString();

      } catch (CharacterCodingException e) {
        e.printStackTrace();
      }

    } catch (IOException ioe) {
      System.err.println(ioe.toString());
    } catch (ModuleException me) {
      System.err.println("Error handling request: " + me.getMessage());
    } catch (HTTPClient.ParseException e) {
      e.printStackTrace();
    }

    writeToCache(host, path, data);
    return data;
  }

  private static String getFromCache(String host, String path) {
    if (!useCache) return null;
    return getUtfStringFromDisk(CACHE_DIR + getHashKey(host, path));
  }

  public static String getUtfStringFromDisk(String fname) {
    try {
      File file = new File(fname);
      if (file.exists()) {
        BufferedInputStream in =
          new BufferedInputStream(new FileInputStream(file));

        byte[] data = new byte[in.available()];
        in.read(data);
        return new String(data, "UTF8");
      }
    } catch (FileNotFoundException fnfe) {
      fnfe.printStackTrace();
    } catch (IOException ioe) {
      ioe.printStackTrace();
    }
    return null;
  }


  /**
   * Given a URL and the data that we got from it, write it to a file somewhere
   * in a local cache. We will retrieve that data from there when we'll look
   * again at this url, if it did not expire.
   * @param host
   * @param path
   * @param data
   */
  private static void writeToCache(String host, String path, String data) {
    try {
      File file = new File(CACHE_DIR + getHashKey(host, path));
      FileOutputStream fos = new FileOutputStream(file);

      fos.write(data.getBytes("UTF8"));
    } catch (FileNotFoundException fnfe) {
      fnfe.printStackTrace();
    } catch (IOException ioe) {
      ioe.printStackTrace();
    }

  }

  /**
   * Get a fancy MD5 hash key for the url so that we make sure it doesn't
   * duplicate since the regular hash string is based on the first 32 characters
   * of one string.
   *
   * @param host
   * @param path
   * @return
   */
  private static String getHashKey(String host, String path) {
    String s = host + path;
    String hash = null;
    try {
      MessageDigest m = MessageDigest.getInstance("MD5");
      m.update(s.getBytes(), 0, s.length());
      hash = new BigInteger(1, m.digest()).toString();
    } catch (NoSuchAlgorithmException nsae) {
      nsae.printStackTrace();
    }
    return hash;
  }

}
