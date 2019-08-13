<?php

/* This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/. */
 
namespace CloudObjects\Utilities\RDF;

use ML\IRI\IRI;

class ParserTest extends \PHPUnit_Framework_TestCase {

    private function indexAssertions($output) {
        $this->assertArrayHasKey('coid://example.com', $output);
        $this->assertArrayHasKey('http://www.w3.org/2000/01/rdf-schema#label',
            $output['coid://example.com']);
        $this->assertEquals("Example",
            $output['coid://example.com']['http://www.w3.org/2000/01/rdf-schema#label'][0]['value']);
        $this->assertArrayHasKey('http://www.w3.org/2000/01/rdf-schema#comment',
            $output['coid://example.com']);
        
        $comments = $output['coid://example.com']['http://www.w3.org/2000/01/rdf-schema#comment'];
        $this->assertCount(2, $comments);
        $this->assertEquals("Localized Example", $comments[0]['value']);
        $this->assertEquals("en", $comments[0]['lang']);
        $this->assertEquals("Lokalisiertes Beispiel", $comments[1]['value']);
        $this->assertEquals("de", $comments[1]['lang']);
    }

    public function testNTriplesToIndex() {
        $input = "<coid://example.com> <http://www.w3.org/2000/01/rdf-schema#label> \"Example\" .\n"
            ."<coid://example.com> <http://www.w3.org/2000/01/rdf-schema#comment> \"Localized Example\"@en ."
            ."<coid://example.com> <http://www.w3.org/2000/01/rdf-schema#comment> \"Lokalisiertes Beispiel\"@de .";
        $output = Parser::parseToIndex($input);
        $this->indexAssertions($output);  
    }

    public function testTurtleToIndex() {
        $input = "@prefix rdfs: <http://www.w3.org/2000/01/rdf-schema#> . "
            ."<coid://example.com> rdfs:label \"Example\" ;"
            ." rdfs:comment \"Localized Example\"@en ;"
            ." rdfs:comment \"Lokalisiertes Beispiel\"@de .";
        $output = Parser::parseToIndex($input);
        $this->indexAssertions($output);  
    }

    public function testRDFXMLToIndex() {
        $input = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>"
            ."<rdf:RDF xmlns:rdf=\"http://www.w3.org/1999/02/22-rdf-syntax-ns#\" "
            ."  xmlns:rdfs=\"http://www.w3.org/2000/01/rdf-schema#\"> "
            ." <rdf:Description rdf:about=\"coid://example.com\"> "
            ."  <rdfs:label>Example</rdfs:label> "
            ."  <rdfs:comment xml:lang=\"en\">Localized Example</rdfs:comment> "
            ."  <rdfs:comment xml:lang=\"de\">Lokalisiertes Beispiel</rdfs:comment> "
            ." </rdf:Description> "
            ."</rdf:RDF>";
        $output = Parser::parseToIndex($input);
        $this->indexAssertions($output);  
    }

    public function testJsonLDToIndex() {
        $input = "{ \"@id\" : \"coid://example.com\", "
            ." \"http://www.w3.org/2000/01/rdf-schema#label\" : \"Example\", "
            ." \"http://www.w3.org/2000/01/rdf-schema#comment\" : [ { \"@value\" : \"Localized Example\", \"@language\" : \"en\" }, "
            ." { \"@value\" : \"Lokalisiertes Beispiel\", \"@language\" : \"de\" } ] }";
        $output = Parser::parseToIndex($input);
        $this->indexAssertions($output);  
    }

}
