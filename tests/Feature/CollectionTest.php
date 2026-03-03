<?php

namespace Tests\Feature;

use App\Data\Person;
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

  public function testMapInto () {
    $collection = collect(['kingabdi']);

    $result = $collection->mapInto(Person::class);

    $this->assertEquals([new Person('kingabdi')], $result->all());  
  } 

  public function testMapSpread() {
    $collection = collect([['udin', 'kingabdi'], ['Abdillatur', 'Rohman']]); 
    $result = $collection->mapSpread( function($firstName, $lastName) {
      $fullName = $firstName . ' ' . $lastName;
      return new Person($fullName);
    });

    $this->assertEquals([new Person('udin kingabdi'), new Person('Abdillatur Rohman')], $result->all());
  }


//   $collection = collect([
//     ['name' => 'kingabdi', 'dept' => 'IT'],
//     ['name' => 'udin',     'dept' => 'HR'],
//     ['name' => 'budi',     'dept' => 'IT'],
//     ['name' => 'siti',     'dept' => 'HR'],
// ]);

// $result = $collection->mapToGroups(function($item) {
//     return [$item['dept'] => $item['name']];
// //          ↑ KEY            ↑ VALUE
// });

// [
//     'IT' => ['kingabdi', 'budi'],
//     'HR' => ['udin', 'siti'],
// ]
  public function testMapToGroups() {
    $collection = collect([
    ['name' => 'kingabdi', 'dept' => 'IT', 'years' => 5],
    ['name' => 'udin',     'dept' => 'HR', 'years' => 1],
    ['name' => 'budi',     'dept' => 'IT', 'years' => 3],
    ['name' => 'siti',     'dept' => 'HR', 'years' => 2],
]);

   $result = $collection->mapToGroups(function($item) {
    $status = $item['years'] >= 3 ? 'Dapat THR' : 'Belum THR';
    return [$item['dept'] => ['name' => $item['name'], 'thr' => $status]];
});

    $this->assertEquals([
     'IT' => [
        ['name' => 'kingabdi', 'thr' => 'Dapat THR'],
        ['name' => 'budi',     'thr' => 'Dapat THR'],
        ['name' => 'andi',     'thr' => 'Belum THR'],
    ],
    'HR' => [
        ['name' => 'udin',     'thr' => 'Belum THR'],
        ['name' => 'siti',     'thr' => 'Belum THR'],
    ],
    ], $result->all());
  }
}
