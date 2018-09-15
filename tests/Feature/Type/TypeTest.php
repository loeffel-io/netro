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
     * @param array $data
     * @return Author
     */
    private function createAuthor(array $data): Author
    {
        $author = new Author();
        return $author->setId($data['author']->ID);
    }

    /**
     * @param Type $class
     * @param array $data
     * @return Type
     * @throws Exception
     */
    private function save(Type $class, array $data): Type
    {
        return $class->setTitle($data['title'])
            ->setContent($data['content'])
            ->setStatus($data['status'])
            ->setAuthor($this->createAuthor($data))
            ->save();
    }

    /**
     * @param Type $type
     * @param array $data
     */
    private function assertAuthor(Type $type, array $data)
    {
        $this->assertEquals($data['author']->ID, $type->getAuthor()->getId());
        $this->assertEquals($data['author']->display_name, $type->getAuthor()->getDisplayName());
        $this->assertEquals($data['author']->first_name, $type->getAuthor()->getFirstName());
        $this->assertEquals($data['author']->last_name, $type->getAuthor()->getLastName());
        $this->assertEquals($data['author']->user_email, $type->getAuthor()->getEmail());
        $this->assertEquals(
            get_user_meta($data['author']->ID, 'last_name', true),
            $type->getAuthor()->getMeta('last_name')
        );
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
            $type = $this->save($class, $data);

            $this->assertInstanceOf($test['class'], $type);
            $this->assertEquals($data['title'], $type->getTitle());
            $this->assertEquals(apply_filters('the_content', $data['content']), $type->getContent());
            $this->assertEquals($data['status'], $type->getStatus());
            $this->assertEquals($class->getPostType(), $type->getPostType());
            $this->assertAuthor($type, $data);

            self::$data[] = $type;
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
