<?php

namespace Tests\Feature\Type;

use Netro\Type\Type;
use PHPUnit\Framework\TestCase;
use Faker;
use Faker\Provider\Base as FakerBase;
use Faker\Provider\Lorem as FakerLorem;
use Exception;

/**
 * Class TypeTest
 * @package Tests\Feature\Type
 */
class TypeTest extends TestCase
{
    /** @var Faker\Generator */
    protected $fakerFactory;

    /** @var array */
    public static $data = [];

    public function setUp()
    {
        $this->fakerFactory = Faker\Factory::create();
    }

    /**
     * @throws Exception
     */
    public function testSave()
    {
        $tests = [
            [
                'class' => \Netro\Type\Post::class,
                'facade' => \Netro\Facade\Type\Post::class,
                'data' => [
                    'title' => $this->fakerFactory->name,
                    'content' => FakerLorem::text(),
                    'status' => FakerBase::randomElement(['publish', 'draft']),
                    'createdAt' => now(),
                    'modifiedAt' => now(),
                ]
            ],
            [
                'class' => \Netro\Type\Page::class,
                'facade' => \Netro\Facade\Type\Page::class,
                'data' => [
                    'title' => $this->fakerFactory->name,
                    'content' => FakerLorem::text(),
                    'status' => FakerBase::randomElement(['publish', 'draft']),
                    'createdAt' => now(),
                    'modifiedAt' => now(),
                ]
            ]
        ];

        foreach ($tests as $test) {
            /** @var Type $class */

            $data = $test['data'];
            $class = new $test['class'];
            $save = $class->setTitle($data['title'])
                ->setContent($data['content'])
                ->setStatus($data['status'])
                ->save();

            $this->assertInstanceOf($test['class'], $save);
            $this->assertEquals($data['title'], $save->getTitle());
            $this->assertEquals(apply_filters('the_content', $data['content']), $save->getContent());
            $this->assertEquals($data['status'], $save->getStatus());
            $this->assertEquals($class->getPostType(), $save->getPostType());

            self::$data[] = $save;
        }
    }

    public function testDelete()
    {
        /** @var Type $type */

        foreach (self::$data as $type) {
            $this->assertEquals(true, $type->delete());
        }
    }
}
