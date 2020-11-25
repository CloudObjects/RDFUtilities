<?php

/* This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/. */

namespace CloudObjects\Utilities\RDF;

use ML\IRI\IRI;
use ML\JsonLD\JsonLD, ML\JsonLD\Quad, ML\JsonLD\LanguageTaggedString,
    ML\JsonLD\TypedValue, ML\JsonLD\RdfConstants;

class Arc2JsonLdConverter {

    /**
     * Indicates whether the object is an instance of a literal type;
     * either TypedValue or LanguageTaggedString.
     * @param $object
     * @return boolean
     */
    public static function isLiteral($object) {
        return (is_a($object, TypedValue::class) || is_a($object, LanguageTaggedString::class));
    }

    /**
     * Converts an array of ARC2 triples into an array of RDF quads
     * in JsonLD library format.
     * @param array $triples ARC2 triples
     */
    public static function triplesToQuads(array $triples) {
        $quads = array();
        foreach ($triples as $t) {
            if ($t['o_type'] == 'literal' && isset($t['o_lang']) && $t['o_lang'] != '')
                $value = new LanguageTaggedString($t['o'], $t['o_lang']);
            elseif ($t['o_type'] == 'literal')
                $value = new TypedValue($t['o'],
                    (isset($t['o_datatype']) && $t['o_datatype'] != '')
                        ? $t['o_datatype'] : RdfConstants::XSD_STRING);
            else
                $value = new IRI($t[0]);

            $quads[] = new Quad(new IRI($t['s']),
                new IRI($t['p']),
                $value);
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
                    if ($object['type'] == 'literal' && isset($object['lang']))
                        $value = new LanguageTaggedString($object['value'], $object['lang']);
                    elseif ($object['type'] == 'literal')
                        $value = new TypedValue($object['value'],
                        (isset($object['datatype']) && $object['datatype']!='')
                          ? $object['datatype'] : RdfConstants::XSD_STRING);
                    else
                        $value = new IRI($object['value']);
                    
                    $quads[] = new Quad(new IRI($subject), new IRI($predicate), $value);
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
        $arcTriples = [];
        foreach ($quads as $q) {
            $arcTriples[] = [
                's' => (string)$q->getSubject(),
                'p' => (string)$q->getProperty(),
                'o' => self::isLiteral($q->getObject())
                    ? $q->getObject()->getValue()
                    : (string)$q->getObject(),
                'o_type' => self::isLiteral($q->getObject())
                    ? 'literal'
                    : (($q->getObject()->getScheme() == '_')
                        ? 'bnode'
                        : 'uri'),
                'o_lang' => is_a($q->getObject(), LanguageTaggedString::class) 
                    ? $q->getObject()->getLanguage()
                    : ''
            ];
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
        return self::quadsToTriples(JsonLD::toRdf($jsonLd));
    }

    /**
     * Converts any JsonLD into an ARC2 index.
     * @param string|array|object $jsonLd JsonLD content
    */
    public static function jsonLdToIndex($jsonLd) {
        return self::quadsToIndex(JsonLD::toRdf($jsonLd));
    }

}
