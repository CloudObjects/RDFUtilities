<?php

namespace CloudObjects\Utilities\RDF;

use ML\IRI\IRI;
use ML\JsonLD\JsonLD, ML\JsonLD\Quad, ML\JsonLD\TypedValue, ML\JsonLD\RdfConstants;

class Arc2JsonLdConverter {

  /**
   * Converts an array of ARC2 triples into an array of RDF quads
   * in JsonLD library format.
   * @param array $triples ARC2 triples
   */
  public static function triplesToQuads(array $triples) {
    $quads = array();
    foreach ($triples as $t) {
      $quads[] = new Quad(new IRI($t['s']), new IRI($t['p']),
      ($t['o_type'] == 'uri') ? new IRI($t['o']) : new TypedValue($t['o'],
      (isset($t['o_datatype']) && $t['o_datatype']!='') ? $t['o_datatype'] : RdfConstants::XSD_STRING));
    }

    return $quads;
  }

  /**
   * Converts an ARC2 index into an array of RDF quads in JsonLD
   * library format.
   * @param array $index ARC2 index
   */
  public static function indexToQuads(array $index) {
    $quads = array();
    foreach ($index as $subject => $predicates) {
      foreach ($predicates as $predicate => $objects) {
        foreach ($objects as $object) {
          $quads[] = new Quad(new IRI($subject),
            new IRI($predicate),
            ($object['type']!='literal')
              ? new IRI($object['value']) : new TypedValue($object['value'],
            (isset($object['datatype']) && $object['datatype']!='')
              ? $object['datatype'] : RdfConstants::XSD_STRING));
        }
      }
    }
    return $quads;
  }

  /**
   * Converts an array of RDF quads in JsonLD library format into
   * an array of ARC2 triples.
   * @param array $quads JsonLD quads
   */
  public static function quadsToTriples(array $quads) {
    $arcTriples = array();
    foreach ($quads as $q) {
      $arcTriples[] = array(
        's' => (string)$q->getSubject(),
        'p' => (string)$q->getProperty(),
        'o' => (is_a($q->getObject(), 'ML\JsonLD\TypedValue')) ? $q->getObject()->getValue() : (string)$q->getObject(),
        'o_type' => (is_a($q->getObject(), 'ML\JsonLD\TypedValue')) ? 'literal' : 'uri'
      );
    }
    return $arcTriples;
  }

  /**
   * Converts an array of RDF quads in JsonLD library format into
   * an ARC2 index.
   * @param array $quads JsonLD quads
   */
  public static function quadsToIndex(array $quads) {
    return \ARC2::getSimpleIndex(self::quadsToTriples($quads), 0);
  }

  /**
   * Converts any JsonLD into an array of ARC2 triples.
   * @param string|array|object $jsonLd JsonLD content
   */
  public static function jsonLdToTriples($jsonLd) {
    if (is_array($jsonLd)) $jsonLd = (object)$jsonLd;
    return self::quadsToTriples(JsonLD::toRdf($jsonLd));
  }

  /**
   * Converts any JsonLD into an ARC2 index.
   * @param string|array|object $jsonLd JsonLD content
   */
  public static function jsonLdToIndex($jsonLd) {
    if (is_array($jsonLd)) $jsonLd = (object)$jsonLd;
    return self::quadsToIndex(JsonLD::toRdf($jsonLd));
  }

}
