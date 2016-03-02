<?php

namespace Propel\Tests\Issues;

use Propel\Tests\TestCase;
use Propel\Runtime\Collection\ObjectCollection;

class DummyObject 
{
    private $id;
    
    private static $counter = 0;
    
    public function __construct()
    {
        $this->id = self::$counter++;
    }
    
    public function getId()
    {
        return $this->id;
    }
    
    public function hashCode()
    {
        return (string)$this->id;
    }
}

/**
 * This test proves the bug described in https://github.com/propelorm/Propel2/issues/1133.
 * 
 * @group database
 */
class Issue1133Test extends TestCase
{
    
    public function testIssue1133()
    {
        $testCollection = new ObjectCollection;
        $testCollection->setModel(DummyObject::class);

        for ($i = 0; $i < 3; $i++)
        {
            $testCollection->append(new DummyObject);
        }

        $firstToRemove = $testCollection[0];
        $objectThatShouldNotBeRemoved = $testCollection[2];

        // breaks index numbering
        $testCollection->removeObject($firstToRemove);
        $objectThatWillBeRemoved = new DummyObject;
        $testCollection->append($objectThatWillBeRemoved);
        $testCollection->removeObject($objectThatWillBeRemoved);

        $this->assertContains($objectThatShouldNotBeRemoved, $testCollection, 'ObjectCollection does not contain item that should be in collection.');
        $this->assertNotContains($objectThatWillBeRemoved, $testCollection, 'ObjectCollection contains item that should be removed.');
    }
}