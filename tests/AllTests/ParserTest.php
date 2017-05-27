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
  }

  public function testNTriplesToIndex() {
    $input = "<coid://example.com> <http://www.w3.org/2000/01/rdf-schema#label> \"Example\" .";
    $output = Parser::parseToIndex($input);
    $this->indexAssertions($output);  
  }

  public function testTurtleToIndex() {
    $input = "@prefix rdfs: <http://www.w3.org/2000/01/rdf-schema#> . "
      ."<coid://example.com> rdfs:label \"Example\" .";
    $output = Parser::parseToIndex($input);
    $this->indexAssertions($output);  
  }

  public function testRDFXMLToIndex() {
    $input = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>"
      ."<rdf:RDF xmlns:rdf=\"http://www.w3.org/1999/02/22-rdf-syntax-ns#\" "
      ."  xmlns:rdfs=\"http://www.w3.org/2000/01/rdf-schema#\"> "
      ." <rdf:Description rdf:about=\"coid://example.com\"> "
      ."  <rdfs:label>Example</rdfs:label> "
      ." </rdf:Description> "
      ."</rdf:RDF>";
    $output = Parser::parseToIndex($input);
    $this->indexAssertions($output);  
  }

  public function testJsonLDToIndex() {
    $input = "{ \"@id\" : \"coid://example.com\", "
      ." \"http://www.w3.org/2000/01/rdf-schema#label\" : \"Example\" }";
    $output = Parser::parseToIndex($input);
    $this->indexAssertions($output);  
  }

}
