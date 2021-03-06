<?php

declare(strict_types=1);

namespace Tests\Sorani\SimpleFramework\Validator;

use Sorani\SimpleFramework\TestCase\ExtendedTestCase;
use Sorani\SimpleFramework\Validator\Validator;

class ValidatorTest extends ExtendedTestCase
{
    private function makeValidator(?array $params = []): Validator
    {
        return new Validator($params);
    }
    public function testRequired()
    {
        $errors = $this->makeValidator([
            'name' => 'John',
        ])
            ->required('name', 'content')
            ->getErrors();

        $this->assertCount(1, $errors);
        $this->assertEquals('required', $errors['content']->getRule());
        $this->assertEquals('content', $errors['content']->getKey());
    }

    public function testRequiredIfSuccess()
    {
        $errors = $this->makeValidator([
            'name' => 'John',
            'content' => 'John Smith is an alias',
            'empty_content' => '',
        ])
            ->required('name', 'content', 'empty_content')
            ->getErrors();

        $this->assertCount(0, $errors);
    }


    public function testSlugSuccess()
    {
        $errors = $this->makeValidator([
            'theSlug' => 'my-article-azsazszezfr141',
            'theSlug2' => 'articleazsazszezfr141',
        ])
            // ->slug('theSlug')
            ->slug('theSlug2')
            ->getErrors();

        $this->assertCount(0, $errors);
    }

    public function testSlugError()
    {
        $errors = $this->makeValidator([
            'slug1' => 'ay-Article-azs1234', // uppercase
            'slug2' => 'ay-article_zs1234', // underscore
            'slug3' =>
            'ay--articlezs1234', // two dashes
            'slug4' => 'ay-articlezs1234-', // dashe at the end
            'slug5' => '-ay-articlezs1234', // dashe at the begining
        ])
            ->slug('slug1')
            ->slug('slug2')
            ->slug('slug3')
            ->slug('slug4')
            ->slug('slug5')
            ->getErrors();

        $this->assertEquals(
            ['slug1', 'slug2', 'slug3', 'slug4', 'slug5'],
            array_keys($errors)
        );
    }

    public function testNotEmpty()
    {
        $errors = $this->makeValidator([
            'e' => '',
            "e2" => '      '
        ])
            ->notEmpty('e', 'e2')
            ->getErrors();

        $this->assertCount(2, $errors);
    }


    public function testLength()
    {
        $params = [
            'slug' => '123456789',
        ];

        $this->assertCount(0, $this->makeValidator($params)->length('slug', 3)->getErrors());
        $errors =
            $this->makeValidator($params)->length('slug', 12)->getErrors();
        $this->assertCount(1, $errors);
        $this->assertEquals('The slug field must contain more than 12 characters', (string)$errors['slug']);
        $this->assertCount(0, $this->makeValidator($params)->length(
            'slug',
            null,
            20
        )->getErrors());
        $this->assertCount(1, $this->makeValidator($params)->length('slug', null, 8)->getErrors());
    }

    public function testDateTimeValid()
    {
        $params = ['date' => '2012-12-12 11:12:13'];
        // valid
        $this->assertCount(0, $this->makeValidator($params)->dateTime('date')->getErrors());
        $this->assertCount(0, $this->makeValidator(['date' => '2012-12-12 00:00:00'])->dateTime('date')->getErrors());
        $this->assertCount(
            0,
            $this->makeValidator(['date' => '2012-21-12 00:00:00'])->dateTime('date', 'Y-d-m H:i:s')->getErrors()
        );

        // validate different formats
        $this->assertCount(0, $this->makeValidator(['date' => '2012-12-12'])->dateTime('date', 'Y-m-d')->getErrors());
        $this->assertCount(0, $this->makeValidator(['date' => '12:10:12'])->dateTime('date', 'H:i:s')->getErrors());
        $this->assertCount(0, $this->makeValidator(['date' => null])->dateTime('date')->getErrors());
    }

    public function testDateTimeInvalid()
    {
        // invalid
        $this->assertCount(1, $this->makeValidator(['date' => '2012-21-12'])->dateTime('date')->getErrors());
        $this->assertCount(1, $this->makeValidator(['date' => '2013-02-29 11:12:13'])->dateTime('date')->getErrors());
        $this->assertCount(1, $this->makeValidator(['date' => '2013-01-01 25:12:13'])->dateTime('date')->getErrors());

        // other formats

        $this->assertCount(1, $this->makeValidator(['date' => '25:10:11'])->dateTime('date', 'H:i:s')->getErrors());
        $this->assertCount(
            1,
            $this->makeValidator(['date' => '25:10:11'])->dateTime('date', \DateTimeImmutable::RFC3339)->getErrors()
        );
    }

    public function testExistsRecord()
    {
        $pdo = $this->getTestDatabase();
        $this->makeInsertTestDatabase($pdo, "a1", "a2");

        $this->assertTrue(
            $this->makeValidator(['category' => 1])->existsRecord('category', 'comments', $pdo)->isValid()
        );
        $this->assertFalse(
            $this->makeValidator(['category' => 150])->existsRecord('category', 'comments', $pdo)->isValid()
        );
    }

    public function testUniqueRecord()
    {
        $pdo = $this->getTestDatabase();
        $this->makeInsertTestDatabase($pdo, "a1", "a2");

        $this->assertTrue(
            $this->makeValidator(['name' => "a3"])->uniqueRecord('name', 'comments', $pdo)->isValid()
        );
        $this->assertFalse(
            $this->makeValidator(['name' => "a1"])->uniqueRecord('name', 'comments', $pdo)->isValid()
        );
        $this->assertTrue(
            $this->makeValidator(['name' => "a1"])->uniqueRecord('name', 'comments', $pdo, 1)->isValid()
        );
        $this->assertFalse(
            $this->makeValidator(['name' => "a2"])->uniqueRecord('name', 'comments', $pdo, 1)->isValid()
        );
    }


    /**
     * @dataProvider booleanProvider
     */
    public function testBoolean($input, bool $isStrict, bool $expected)
    {
        $this->assertEquals(
            $expected,
            $this->makeValidator(['published' => $input])
                ->boolean('published', $isStrict)
                ->isValid()
        );
    }

    /**
     * @return array
     */
    public function booleanProvider(): array
    {
        return [
            // truthy
            ["1", false, true],
            [1, false, true],
            [true, false, true],

            // falsy
            ["0", false, true],
            [0, false, true],
            [false, false, true],

            // true or false
            [true, true, true],
            [false, true, true],
        ];
    }
}
