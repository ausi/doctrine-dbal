<?php

declare(strict_types=1);

namespace Doctrine\DBAL\Tests\Functional\Schema;

use Doctrine\DBAL\Schema\AbstractSchemaManager;
use Doctrine\DBAL\Schema\Comparator;
use Doctrine\DBAL\Schema\Table;
use Doctrine\DBAL\Tests\FunctionalTestCase;

class ComparatorTest extends FunctionalTestCase
{
    /** @var AbstractSchemaManager */
    private $schemaManager;

    /** @var Comparator */
    private $comparator;

    protected function setUp(): void
    {
        parent::setUp();

        $this->schemaManager = $this->connection->getSchemaManager();
        $this->comparator    = new Comparator($this->schemaManager->getDatabasePlatform());
    }

    /**
     * @param mixed $value
     *
     * @dataProvider defaultValueProvider
     */
    public function testDefaultValueComparison(string $type, $value): void
    {
        $table = new Table('default_value');
        $table->addColumn('test', $type, ['default' => $value]);

        $this->schemaManager->dropAndCreateTable($table);

        $onlineTable = $this->schemaManager->listTableDetails('default_value');

        self::assertFalse($this->comparator->diffTable($table, $onlineTable));
    }

    /**
     * @param mixed $value
     *
     * @dataProvider defaultValueProvider
     */
    public function testDefaultValueComparisonDeprecatedComparator(string $type, $value): void
    {
        // Comparator without platform is deprecated
        $this->comparator = new Comparator();

        $this->testDefaultValueComparison($type, $value);
    }

    /**
     * @return mixed[][]
     */
    public static function defaultValueProvider(): iterable
    {
        return [
            ['integer', 1],
            ['boolean', false],
        ];
    }
}
