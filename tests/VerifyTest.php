<?php declare(strict_types=1);

include_once __DIR__.'/../src/Codeception/bootstrap.php';

use Codeception\Verify\Verify;
use PHPUnit\Framework\ExpectationFailedException;
use PHPUnit\Framework\TestCase;

final class VerifyTest extends TestCase
{
    /** @var DOMDocument */
    protected $xml;

    protected function setUp(): void
    {
        $this->xml = new DOMDocument;
        $this->xml->loadXML('<foo><bar>Baz</bar><bar>Baz</bar></foo>');
    }

    public function testEquals(): void
    {
        verify(5)->equals(5);
        verify('hello')->equals('hello');
        verify(5)->equals(5, 'user have 5 posts');
        verify(3.251)->equalsWithDelta(3.25, 0.01);
        verify(3.251)->equalsWithDelta(3.25, 0.01, 'respects delta');
        Verify::File(__FILE__)->equals(__FILE__);
    }

    public function testNotEquals(): void
    {
        verify(3)->notEquals(5);
        verify(3.252)->notEqualsWithDelta(3.25, 0.001);
        verify(3.252)->notEqualsWithDelta(3.25, 0.001, 'respects delta');
        Verify::File(__FILE__)->notEquals(__DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'composer.json');
    }

    public function testContains(): void
    {
        Verify::Array([3, 2])->contains(3);
        Verify::Array([3, 2])->notContains(5, 'user have 5 posts');
    }

    public function testGreaterLowerThan(): void
    {
        verify(7)->greaterThan(5);
        verify(7)->lessThan(10);
        verify(7)->lessThanOrEqual(7);
        verify(7)->lessThanOrEqual(8);
        verify(7)->greaterThanOrEqual(7);
        verify(7)->greaterThanOrEqual(5);
    }

    public function testTrueFalseNull(): void
    {
        verify(true)->true();
        verify(false)->false();
        verify(null)->null();
        verify(true)->notNull();
        verify(false)->false('something should be false');
        verify(true)->true('something should be true');
    }

    public function testEmptyNotEmpty(): void
    {
        verify(array('3', '5'))->notEmpty();
        verify(array())->empty();
    }

    public function testArrayHasKey(): void
    {
        $errors = ['title' => 'You should add title'];
        Verify::Array($errors)->hasKey('title');
        Verify::Array($errors)->hasNotKey('body');
    }

    public function testIsInstanceOf(): void
    {
        $testClass = new DateTime();
        verify($testClass)->instanceOf('DateTime');
        verify($testClass)->notInstanceOf('DateTimeZone');
    }

    public function testHasAttribute(): void
    {
        Verify::Class('Exception')->hasAttribute('message');
        Verify::Class('Exception')->notHasAttribute('fakeproperty');

        $testObject = (object) ['existingAttribute' => true];
        Verify::BaseObject($testObject)->hasAttribute('existingAttribute');
        Verify::BaseObject($testObject)->notHasAttribute('fakeproperty');
    }

    public function testHasStaticAttribute(): void
    {
        Verify::Class('FakeClassForTesting')->hasStaticAttribute('staticProperty');
        Verify::Class('FakeClassForTesting')->notHasStaticAttribute('fakeProperty');
    }

    public function testContainsOnly(): void
    {
        Verify::Array(['1', '2', '3'])->containsOnly('string');
        Verify::Array(['1', '2', 3])->notContainsOnly('string');
    }

    public function testContainsOnlyInstancesOf(): void
    {
        Verify::Array([new FakeClassForTesting(), new FakeClassForTesting(), new FakeClassForTesting()])
            ->containsOnlyInstancesOf('FakeClassForTesting');
    }

    public function testCount(): void
    {
        Verify::Array([1, 2, 3])->count(3);
        Verify::Array([1, 2, 3])->notCount(2);
    }

    public function testFileExists(): void
    {
        Verify::File(__FILE__)->exists();
        Verify::File('completelyrandomfilename.txt')->doesNotExists();
    }

    public function testEqualsJsonFile(): void
    {
        Verify::JsonFile(__DIR__ . DIRECTORY_SEPARATOR . 'assets' . DIRECTORY_SEPARATOR . 'json-test-file.json')
            ->equalsJsonFile(__DIR__ . DIRECTORY_SEPARATOR . 'assets' . DIRECTORY_SEPARATOR . 'equal-json-test-file.json');
        Verify::JsonString('{"some" : "data"}')->equalsJsonFile(__DIR__ . DIRECTORY_SEPARATOR . 'assets' . DIRECTORY_SEPARATOR . 'equal-json-test-file.json');
    }

    public function testEqualsJsonString(): void
    {
        Verify::JsonString('{"some" : "data"}')->equalsJsonString('{"some" : "data"}');
    }

    public function testRegExp(): void
    {
        Verify::String('somestring')->matchesRegExp('/string/');
    }

    public function testMatchesFormat(): void
    {
        Verify::String('somestring')->matchesFormat('%s');
        Verify::String('somestring')->notMatchesFormat('%i');
    }

    public function testMatchesFormatFile(): void
    {
        Verify::String('23')->matchesFormatFile(__DIR__ . DIRECTORY_SEPARATOR . 'assets' . DIRECTORY_SEPARATOR . 'format-file.txt');
        Verify::String('asdfas')->notMatchesFormatFile(__DIR__ . DIRECTORY_SEPARATOR . 'assets' . DIRECTORY_SEPARATOR . 'format-file.txt');
    }

    public function testSame(): void
    {
        verify(1)->same(0+1);
        verify(1)->notSame(true);
    }

    public function testEndsWith(): void
    {
        Verify::String('A completely not funny string')->endsWith('ny string');
        Verify::String('A completely not funny string')->notEndsWith('A completely');
    }

    public function testEqualsFile(): void
    {
        Verify::String('%i')->equalsFile(__DIR__ . DIRECTORY_SEPARATOR . 'assets' . DIRECTORY_SEPARATOR . 'format-file.txt');
        Verify::String('Another string')->notEqualsFile(__DIR__ . DIRECTORY_SEPARATOR . 'assets' . DIRECTORY_SEPARATOR . 'format-file.txt');
    }

    public function testStartsWith(): void
    {
        Verify::String('A completely not funny string')->startsWith('A completely');
        Verify::String('A completely not funny string')->startsNotWith('string');
    }

    public function testEqualsXmlFile(): void
    {
        Verify::XmlFile(__DIR__ . DIRECTORY_SEPARATOR . 'assets' . DIRECTORY_SEPARATOR . 'xml-test-file.xml')
            ->equalsXmlFile(__DIR__ . DIRECTORY_SEPARATOR . 'assets' . DIRECTORY_SEPARATOR . 'xml-test-file.xml');
        Verify::XmlString('<foo><bar>Baz</bar><bar>Baz</bar></foo>')
            ->equalsXmlFile(__DIR__ . DIRECTORY_SEPARATOR . 'assets' . DIRECTORY_SEPARATOR . 'xml-test-file.xml');
    }

    public function testEqualsXmlString(): void
    {
        Verify::XmlString('<foo><bar>Baz</bar><bar>Baz</bar></foo>')
            ->equalsXmlString('<foo><bar>Baz</bar><bar>Baz</bar></foo>');
    }

    public function testStringContainsString(): void
    {
        Verify::String('foo bar')->containsString('o b');
        Verify::String('foo bar')->notContainsString('BAR');
    }

    public function testStringContainsStringIgnoringCase(): void
    {
        Verify::String('foo bar')->containsStringIgnoringCase('O b');
        Verify::String('foo bar')->notContainsStringIgnoringCase('baz');
    }

    public function testIsString(): void
    {
        verify('foo bar')->isString();
        verify(false)->isNotString();
    }

    public function testIsArray(): void
    {
        verify([1,2,3])->isArray();
        verify(false)->isNotArray();
    }

    public function testIsBool(): void
    {
        verify(false)->isBool();
        verify([1,2,3])->isNotBool();
    }

    public function testIsFloat(): void
    {
        verify(1.5)->isFloat();
        verify(1)->isNotFloat();
    }

    public function testIsInt(): void
    {
        verify(5)->isInt();
        verify(1.5)->isNotInt();
    }

    public function testIsNumeric(): void
    {
        verify('1.5')->isNumeric();
        verify('foo bar')->isNotNumeric();
    }

    public function testIsObject(): void
    {
        verify(new stdClass)->isObject();
        verify(false)->isNotObject();
    }

    public function testIsResource(): void
    {
        verify(fopen(__FILE__, 'r'))->isResource();
        verify(false)->isNotResource();
    }

    public function testIsScalar(): void
    {
        verify('foo bar')->isScalar();
        verify([1,2,3])->isNotScalar();
    }

    public function testIsCallable(): void
    {
        verify(function(): void {})->isCallable();
        verify(false)->isNotCallable();
    }

    public function testEqualsCanonicalizing(): void
    {
        verify([3, 2, 1])->equalsCanonicalizing([1, 2, 3]);
    }

    public function testNotEqualsCanonicalizing(): void
    {
        verify([3, 2, 1])->notEqualsCanonicalizing([2, 3, 0, 1]);
    }

    public function testEqualsIgnoringCase(): void
    {
        verify('foo')->equalsIgnoringCase('FOO');
    }

    public function testNotEqualsIgnoringCase(): void
    {
        verify('foo')->notEqualsIgnoringCase('BAR');
    }

    public function testEqualsWithDelta(): void
    {
        verify(1.01)->equalsWithDelta(1.0, 0.1);
    }

    public function testNotEqualsWithDelta(): void
    {
        verify(1.2)->notEqualsWithDelta(1.0, 0.1);
    }

    public function testThrows(): void
    {
        $func = function (): void {
            throw new Exception('foo');
        };

        Verify::Callable($func)->throws();
        Verify::Callable($func)->throws(Exception::class);
        Verify::Callable($func)->throws(Exception::class, 'foo');
        Verify::Callable($func)->throws(new Exception());
        Verify::Callable($func)->throws(new Exception('foo'));

        Verify::Callable(function () use ($func): void {
            Verify::Callable($func)->throws(RuntimeException::class);
        })->throws(ExpectationFailedException::class);

        Verify::Callable(function (): void {
            Verify::Callable(function (): void {})->throws(Exception::class);
        })->throws(new ExpectationFailedException("exception 'Exception' was not thrown as expected"));
    }

    public function testDoesNotThrow(): void
    {
        $func = function (): void {
            throw new Exception('foo');
        };

        Verify::Callable(function (): void {})->doesNotThrow();
        Verify::Callable($func)->doesNotThrow(RuntimeException::class);
        Verify::Callable($func)->doesNotThrow(RuntimeException::class, 'bar');
        Verify::Callable($func)->doesNotThrow(RuntimeException::class, 'foo');
        Verify::Callable($func)->doesNotThrow(new RuntimeException());
        Verify::Callable($func)->doesNotThrow(new RuntimeException('bar'));
        Verify::Callable($func)->doesNotThrow(new RuntimeException('foo'));
        Verify::Callable($func)->doesNotThrow(Exception::class, 'bar');
        Verify::Callable($func)->doesNotThrow(new Exception('bar'));

        Verify::Callable(function () use ($func): void {
            Verify::Callable($func)->doesNotThrow();
        })->throws(new ExpectationFailedException('exception was not expected to be thrown'));

        Verify::Callable(function () use ($func): void {
            Verify::Callable($func)->doesNotThrow(Exception::class);
        })->throws(new ExpectationFailedException("exception 'Exception' was not expected to be thrown"));

        Verify::Callable(function () use ($func): void {
            Verify::Callable($func)->doesNotThrow(Exception::class, 'foo');
        })->throws(new ExpectationFailedException("exception 'Exception' with message 'foo' was not expected to be thrown"));
    }
}


class FakeClassForTesting
{
    static $staticProperty;
}
