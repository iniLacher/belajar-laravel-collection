<?php

namespace Tests\Feature;

use App\Data\Person;
use GrahamCampbell\ResultType\Result;
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
     'IT' => collect([
        ['name' => 'kingabdi', 'thr' => 'Dapat THR'],
        ['name' => 'budi',     'thr' => 'Dapat THR'],
    ]),
    'HR' => collect([
        ['name' => 'udin',     'thr' => 'Belum THR'],
        ['name' => 'siti',     'thr' => 'Belum THR'],
    ]),
    ], $result->all());
  }


  public function testZip() {
    $collection1 = collect(['kingabdi', 'rohman', 'abdillatur']);
    $result =$collection1->zip(['rich', 'happy', 'beriman']);

    $this->assertEquals([collect(['kingabdi', 'rich']), collect(['rohman', 'happy']), collect(['abdillatur', 'beriman'])], $result->all());
  }


  public function testConcat() {
    $collection1 = collect([1,2,3]);
    $result = $collection1->concat([4,5,6]);

    $this->assertEquals([1,2,3,4,5,6], $result->all());
  }

  public function testCombine(){
    $collection1 = collect(['name', 'age', 'city']);
    $result = $collection1->combine(['kingabdi', 78, 'khazakstan']);
    $this->assertEquals(['name' => 'kingabdi', 'age' => 78, 'city' => 'khazakstan'], $result->all());
  }

  public function testCollapse() {
    $collection1 = collect([[1,2,3], [4,5,6], [7,8,9]]);
    $result = $collection1->collapse();

    $this->assertEquals([1,2,3,4,5,6,7,8,9], $result->all());
  }

  public function testFlatMap() {
    $collection = collect([
      ['name' => 'kingabdi', 'hobbies' => ['coding', 'gaming']],
      ['name' => 'udin',     'hobbies' => ['cooking', 'traveling']],
      ['name' => 'rohman',     'hobbies' => ['praying', 'reading quran']],
      ['name' => 'abdul',     'hobbies' => ['karauke', 'mangku LC']]
    ]);

    $result = $collection->flatMap(function($item) {
      return $item['hobbies'];
    });

    $this->assertEquals(['coding', 'gaming', 'cooking', 'traveling', 'praying', 'reading quran', 'karauke', 'mangku LC'], $result->all());
  }


  public function testStringRepresentation() {
    $collection = collect([
      ['name' => 'kingabdi', 'dept' => 'IT'],
      ['name' => 'udin', 'dept' => 'ngarit'],
      ['name' => 'somad', 'dept' => 'bar'],
      ['name' => 'kingrohman', 'dept' => 'ustadz'],
    ]);

      $result = $collection->implode('name', ', ');
      $this->assertEquals('kingabdi, udin, somad, kingrohman', $result);

    $wadahJoinGlue = collect(['kingabdi', 'udin', 'somad', 'kingrohman']);

    $this->assertEquals('kingabdi, udin, somad and kingrohman', $wadahJoinGlue->join(', ', ' and '));
  }

  public function testFiltering() {
    $collection = collect([
      'kingabdi' => 100,
      'abdi' => 85,
      'bamabang' => 55
    ]);

    $result = $collection->filter(function($value, $key) {
      return $value >= 80;
    });
    $this->assertEquals(['kingabdi' => 100, 'abdi' => 85], $result->all());
  }

  public function testPartitioning() {
    $collection = collect([
      'kingabdi' => 100,
      'abdi' => 85,
      'bamabang' => 55
    ]);

    [$result1, $result2] = $collection->partition(function($value, $key) {
      return $value >= 80;
    });

    $this->assertEquals(['kingabdi' => 100, 'abdi' => 85], $result1->all());
    $this->assertEquals(['bamabang' => 55], $result2->all());
  }


  public function testTesting()  {
    $collection = collect(['budi', 'udin', 'siti']);

    $this->assertTrue($collection->contains('udin'));
    $this->assertTrue($collection->contains(function ($value, $key) {
      return $value === 'udin';
    }));
  }

  public function testGrouping() {
    $collection = collect([
      ['name' => 'kingabdi', 'dept' => 'IT'],
      ['name' => 'udin',     'dept' => 'HR'],
      ['name' => 'budi',     'dept' => 'IT'],
      ['name' => 'siti',     'dept' => 'HR'],
      ['name' => 'rohman',     'dept' => 'IT'],
      ['name' => 'abdillatur',     'dept' => 'HR'],
    ]);

    $result = $collection->groupBy('dept');

    $this->assertEquals([
      'IT' => collect([
        ['name' => 'kingabdi', 'dept' => 'IT'],
        ['name' => 'budi',     'dept' => 'IT'],
        ['name' => 'rohman',     'dept' => 'IT'],
      ]),
      'HR' => collect([
        ['name' => 'udin',     'dept' => 'HR'],
        ['name' => 'siti',     'dept' => 'HR'],
        ['name' => 'abdillatur',     'dept' => 'HR'],
      ]),
    ], $result->all());
  }

  public function testSlicing() {
    $collection = collect([1,2,3,4,5]);
    $result = $collection->slice(2);
    $this->assertEquals([3,4,5], $result->values()->all());


    $coll = collect([1,2,3,4,5]);
    $resultColl = $coll->slice(1, 3);
    $this->assertEquals([2,3,4], $resultColl->values()->all());

    $collStr = collect(['kingabdi', 'udin', 'siti', 'rohman', 'abdillatur']);
    $resultCollStr = $collStr->slice(1, 3);
    $this->assertEquals(['udin', 'siti', 'rohman'], $resultCollStr->values()->all());
    // klo nggak mau  pake values() maka bisa pake assertEqualsCanonicalizing untuk mengabaikan indexnya, karena slice itu kan memotong jadi hasilnya kan di mulai dari 1 indexnya yaitu udin,dan si assertEquals itu membandingkan indexnya dari 0,makannya kalo pake cara biasa dia akan error.
  }


  public function testTake() {
    $collection = collect([1,2,3,4,5]);
    $result = $collection->take(3);
    $this->assertEquals([1,2,3], $result->values()->all());

    $coll = collect([1,2,3,4,5]);
    $resultColl = $coll->take(-2);
    $this->assertEquals([4,5], $resultColl->values()->all());

    $result  = $collection->takeUntil(function ($value, $key) {
      return $value === 5;
    });
    $this->assertEquals([1,2,3,4], $result->values()->all());

    $result = collect([1,2,3,4,5])->takeWhile(function ($value, $key) {
      return $value < 4;
    });
    $this->assertEquals([1,2,3], $result->values()->all());
  }

  public function testSkip () {
    $collection = collect([1,2,3,4,5,6]);

    $result = $collection->skip(3);

    $this->assertEquals([4,5,6], $result->values()->all());

    $result = $collection->skipUntil(function($value, $key){
      return $value === 3;
    });

    $this->assertEqualsCanonicalizing([3,4,5,6], $result->all());

    $result = $collection->skipWhile(function($value, $key){
      return $value > 3;
    });

    $this->assertEquals([1,2,3], $result->values()->all());
  }
}
