<?php

use blog\entities\relation\RelationSql;
use Codeception\Test\Unit;

class RelationSqlTest extends Unit
{
    public function testReplaceSelectSection()
    {
        $relationAlias = new RelationSql('SELECT t.*, a.title, a.name FROM table a JOIN table2 t ON a.id=t.id');
        $relationAlias->classAlias('a', SomeClass::class);

        $expectedSql = "SELECT t.*, a.title as 'SomeClass__title', a.name as 'SomeClass__name' FROM table a JOIN table2 t ON a.id=t.id";
        expect($relationAlias->getSql())->equals($expectedSql);
    }

    public function testReplaceSelectSectionWithQuote()
    {
        $relationAlias = new RelationSql('SELECT a.`title`, a.`name` FROM table a');
        $relationAlias->classAlias('a', SomeClass::class);

        $expectedSql = "SELECT a.`title` as 'SomeClass__title', a.`name` as 'SomeClass__name' FROM table a";
        expect($relationAlias->getSql())->equals($expectedSql);
    }
}
