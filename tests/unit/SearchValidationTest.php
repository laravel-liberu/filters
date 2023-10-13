<?php

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;
use LaravelLiberu\Filters\Exceptions\ComparisonOperator;
use LaravelLiberu\Filters\Exceptions\SearchMode;
use LaravelLiberu\Filters\Services\Search;
use Tests\TestCase;

class SearchValidationTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->createTestTable();
    }

    /** @test */
    public function validates_search_mode()
    {
        $mode = 'unknown_mode';

        $this->expectException(SearchMode::class);
        $this->expectExceptionMessage(SearchMode::unknown()->getMessage());

        (new Search(ValidateSearchTestModel::query(), ['name'], 'something'))
            ->searchMode($mode);
    }

    /** @test */
    public function validates_comparison_operator()
    {
        $comparisonOperator = 'unknown';

        $this->expectException(ComparisonOperator::class);
        $this->expectExceptionMessage(ComparisonOperator::unknown()->getMessage());

        (new Search(ValidateSearchTestModel::query(), ['name'], 'something'))
            ->comparisonOperator($comparisonOperator);
    }

    private function createTestTable()
    {
        Schema::create('search_test_models', function ($table) {
            $table->increments('id');
            $table->string('name');
            $table->string('email');
            $table->timestamps();
        });
    }
}

class ValidateSearchTestModel extends Model
{
    protected $fillable = ['name', 'email'];

    public function relation()
    {
        return $this->hasOne(SearchTestRelation::class, 'parent_id');
    }
}
