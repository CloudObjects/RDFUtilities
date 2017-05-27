<?php

/* This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/. */

namespace CloudObjects\Utilities\RDF;

class Parser {

    /**
     * Parses the given input and returns an ARC2 index.
     * If the input is JsonLD the JsonLD parser is used, otherwise ARC2 is used.
     *
     * @param string $input
     */
    public static function parseToIndex($input) {
        if (($input[0] == '{' || $input[0] == '[')) {
            return Arc2JsonLdConverter::jsonLdToIndex($input);
        } else {
            $parser = \ARC2::getRDFParser();
		    $parser->parse('', $input);
            return $parser->getSimpleIndex(false);
        }
    }
}