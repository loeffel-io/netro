<?php

namespace Tests\Feature\Type;

use Netro\Support\Author;
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
                    'author' => get_user_by('id', 1),
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
                    'author' => get_user_by('id', 1),
                ]
            ]
        ];

        foreach ($tests as $test) {
            /** @var Type $class */

            $data = $test['data'];
            $class = new $test['class'];
            $author = new Author();
            $author->setId($data['author']->ID);

            $save = $class->setTitle($data['title'])
                ->setContent($data['content'])
                ->setStatus($data['status'])
                ->setAuthor($author)
                ->save();

            $this->assertInstanceOf($test['class'], $save);
            $this->assertEquals($data['title'], $save->getTitle());
            $this->assertEquals(apply_filters('the_content', $data['content']), $save->getContent());
            $this->assertEquals($data['status'], $save->getStatus());
            $this->assertEquals($class->getPostType(), $save->getPostType());
            $this->assertEquals($data['author']->ID, $save->getAuthor()->getId());
            $this->assertEquals($data['author']->display_name, $save->getAuthor()->getDisplayName());
            $this->assertEquals($data['author']->first_name, $save->getAuthor()->getFirstName());
            $this->assertEquals($data['author']->last_name, $save->getAuthor()->getLastName());
            $this->assertEquals($data['author']->user_email, $save->getAuthor()->getEmail());
            $this->assertEquals(
                get_user_meta($data['author']->ID, 'last_name', true),
                $save->getAuthor()->getMeta('last_name')
            );

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
