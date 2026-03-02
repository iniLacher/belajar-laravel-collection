<?php

namespace Tests\Feature;

use Tests\TestCase;

class CollectionTest extends TestCase
{
  public function testCollection() {
    $collection = collect([1,2,'3', 'inistring']);

    $this->assertEqualsCanonicalizing([1,2,3, 'inistring'], $collection->all());
  }

  public function testCollectionForEach() {
    $collection = collect([1,2,3,4,5]);

    foreach ($collection as $key => $value) {
      $this->assertEquals($key + 1, $value);
    }
  }


  public function testCrud() {
    $collection = collect([]);

    $collection->push(1,2,3,4,5);
    $this->assertEquals([1,2,3,4,5], $collection->all());


    $result = $collection->pop();
    $this->assertEquals(5, $result);
    $this->assertEquals([1,2,3,4], $collection->all());
  }

  public function testMap() {
    $collection = collect([1,2,3]);

    $result = $collection->map( function($item) {
      return $item * 2;
    });

    $this->assertEquals([2,4,6], $result->all());
  }
}
