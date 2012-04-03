<?php
/**
 * @fileoverview Various utilities related to string operations, like removing
 * diacritics, measuring the distance in between two strings, etc.
 */

/**
 * Removes the diacritics from all the strings in an array. The method does
 * not alter the original array and returns a new array with the new strings.
 *
 * @param {Array} $array The array of strings that need to be stripped of
 *     diacritics.
 * @return {Array} The resulting array, no diacritics.
 */
function getArrayWithoutDiacritics($array) {
  $new = array();
  foreach ($array as $elem) {
    $new[] = getStringWithoutDiacritics($elem);
  }
  return $new;
}


/**
 * Returns a naive measure of the distance between two strings. Distance is
 * defined as the number of letters that I would need to change or add to get
 * from $from to $to.
 *
 * ATTENTION: If we find that the anagrams comparison returns too many false
 * positives, we can switch to a more... letter by letter comparison.
 *
 * What we desire in principle is to identify cases like 'Ioan vs. Ion' and
 * 'Nicolae vs. Niculae'. This method fails for 'Moraru vs Rotaru'.
 *
 * @param {string} from The string from which I want to start.
 * @param {string} to The string to which I need to get.
 * @return {number} The approximate number of operations that I need to do to
 *     in order to transform the strings.
 * @deprecated
 */
function distanceBetweenAnagrammedStrings($from, $to) {
  $fromIndex = getLettersIndex($from);
  $toIndex = getLettersIndex($to);

  $dist = 0;
  foreach ($fromIndex as $letter => $count) {
    $dist += abs($count - $toIndex[$letter]);
  }

  // We only care about 0, 1, and 2+
  if (strlen($from) <= strlen($to)) {
    return $dist + (strlen($to) - strlen($from));
  } else {
    return max($dist, strlen($from) - strlen($to));
  }
}


/**
 * Returns a naive measure of the distance between two strings. Distance is
 * defined as the number of letters that I would need to change or add to get
 * from $from to $to.
 *
 * ATTENTION: This walks through letters in the existing order, not anagrams.
 *
 * What we desire in principle is to identify cases like 'Ioan vs. Ion' and
 * 'Nicolae vs. Niculae'.
 *
 * @param {string} from The string from which I want to start.
 * @param {string} to The string to which I need to get.
 * @return {number} The approximate number of operations that I need to do to
 *     in order to transform the strings.
 */
function distanceBetweenStrings($from, $to) {
  $len1 = strlen($from);
  $len2 = strlen($to);
  if (abs($len1 - $len2) > 1) {
    return abs($len1 - $len2);
  }

  $i = 0;
  $j = 0;
  $diff = 0;

  while ($i < $len1 && $j < $len2) {
    $l1 = $from[$i];
    $l2 = $to[$j];

    if ($l1 == $l2) {
      $i++;
      $j++;

    } else if ($len1 > $len2) {
      $i++;
      $diff++;

    } else if ($len2 > $len1) {
      $j++;
      $diff++;

    } else {
      $i++;
      $j++;
      $diff++;
    }
    if ($diff > 1) return $diff;
  }
  return $diff + ($len1 - $i) + ($len2 - $j);
}


/**
 * Returns a hash map of letters and number of occurences in a string.
 *
 * @param {string} The string for which we want the index of letters.
 * @return {HashMap.<char, int>} The map of letters plus occurences.
 */
function getLettersIndex($str) {
  $index = array();
  for ($i = 0; $i < strlen($str); $i++) {
    $index[substr($str, $i, 1)]++;
  }
  return $index;
}

/**
 * Moves the first name in a space separated string to be last and returns
 * that flipped string. For example, if "Costache Octavian Mihai" is passed
 * in as a parameter, the method will return "Octavian Mihai Costache".
 *
 * TODO(vivi): Deprecate this function, remove names from all tables except
 * the people table and just use displayName from that table.
 *
 * @param {string} The original name.
 * @return {string} The name with the first name being last.
 */
function moveFirstNameLast($name) {
  $parts = split(" ", $name);
  $first = array_shift($parts);
  return implode(' ', $parts) . " $first";
}


/**
 * Removes the diacritics from a string and returns a new string with the
 * transformed value.
 *
 * @param {string} $str The string from which we need to remove diacritics.
 * @return {string} The new value, no diacritics.
 */
function getStringWithoutDiacritics($str) {
  $repl = array(
    'î' => 'i',
    'í' => 'i',
    'Î' => 'I',
    'ă' => 'a',
    'Ă' => 'A',
    'â' => 'a',
    'Â' => 'A',

    'ț' => 't',
    'Ț' => 'T',
    'ș' => 's',
    'Ș' => 'S',
    'ş' => 's',
    'Ş' => 'S',

    'á' => 'a',
    'Á' => 'A',
    'é' => 'e',
    'Ó' => 'O',
    'ó' => 'o',
    'ö' => 'o',
    'ő' => 'o',
    'ţ' => 't',
    'Ţ' => 'T',
    'É' => 'E',
    'Ő' => 'O',
    'Ö' => 'O'
  );
  return strtr($str, $repl);
}


/**
 * Transforms a string into lowercase, also taking diacritics into account.
 */
function strtolower_ro($str) {
  $repl = array(
    'Î' => 'î',
    'Ă' => 'ă',
    'Â' => 'â',
    'Á' => 'á',
    'Ț' => 'ț',
    'Ș' => 'ș',
    'Ş' => 'ș',
    'Ţ' => 'ț',
    'Ó' => 'o',
    'Ö' => 'o',
    'Ő' => 'o',
    'Ó' => 'o',
    'É' => 'e'
  );
  return strtr(strtolower($str), $repl);
}


function correctDiacritics($str) {
  $repl = array(
    'ţ' => 'ț',
    'ş' => 'ș',
    'Ş' => 'Ș',
    'Ţ' => 'Ț'
  );
  return strtr($str, $repl);
}


function highlightStr($haystack, $needle) {
  $haystack = correctDiacritics($haystack);

  // return $haystack if there is no highlight color or strings given,
  // nothing to do.
  if (strlen($haystack) < 1 || strlen($needle) < 1) {
    return $haystack;
  }

  preg_match_all("/$needle+/i", $haystack, $matches);
  if (is_array($matches[0]) && count($matches[0]) >= 1) {
    foreach ($matches[0] as $match) {
      $haystack = str_replace($match, '<b>'.$match.'</b>', $haystack);
    }
  }
  return $haystack;
}


/**
 * Returns the best snippet that contains the query from the $text passed in
 * as a parameter. For now this is a pretty dumb function, it could definitely
 * be improved in the future.
 *
 * NOTE: This method is VERY basic and it will not get amazing results, but
 * it's a start.
 * NOTE: This seems to not work correctly if $query contains diacritics.
 *
 * ATTENTION: This doesn't current work properly if tags are in here, it just
 * cuts them in half.
 *
 * @param {String} $text The original text from which we want to extract
 *     the snippet.
 * @param {String} $query The query text.
 * @return {String} The cut down string.
 */
function getSnippet($text, $query) {
  if ($query == '') {
    $suffix = 250 < strlen($text) ? '...' : '';
    return substr($text, 0, min(strlen($text), 250)) . $suffix;
  }

  $text = correctDiacritics($text);
  $query = correctDiacritics($query);

  $first_pos = stripos($text, $query);

  $start = max(0, $first_pos - 70);
  $end = min(strlen($text), $first_pos + 200);

  $prefix = $start > 0 ? '...' : '';
  $suffix = $end < strlen($text) ? '...' : '';

  return $prefix . substr($text, $start, $end - $start) . $suffix;
}


/**
 * Takes a string and marks it down each word with a span element that holds
 * the id of that word in the sequence. For example, from the input string
 *    "Foo bar."
 * it will return the string
 *    "<span tokenId=1>Foo</spam> <span tokenId=2>bar</span>."
 *
 * @param {String} $text The text that we need to annotate.
 * @return {String} The annotated text with the spans in it.
 */
function markDownTextBlock($text, $prefix) {
  $new_text = preg_replace('/([\wșȘîÎăĂâÂțȚţŢşŞ]+)/',
                           '<span id=xxx>\1</span>', $text);

  $pos = strpos($new_text, 'xxx');
  $count = 0;

  // Stupid workaround.
  $one = 1;

  while ($pos > 0) {
    $new_text = preg_replace("/xxx/", "word-{$count}", $new_text, $one);
    $pos = strpos($new_text, "xxx");
    $count++;
  }
  return $new_text;
}

?>
