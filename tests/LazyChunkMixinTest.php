<?php


namespace Dababo\LazyChunk\Tests;


use Dababo\LazyChunk\Tests\Helpers\MockCallback;
use Dababo\LazyChunk\Tests\Models\Article;
use Illuminate\Support\Facades\DB;

class LazyChunkMixinTest extends TestCase
{
    protected $queryCount = 0;

    protected $createdArticles = 95;

    protected function setUp(): void
    {
        parent::setUp();

        $this->createArticleSchema();

        factory(Article::class, $this->createdArticles)->create();

        $this->queryCount = new MockCallback();

        DB::listen($this->queryCount->getCallable());
    }


    /** @test */
    public function it_only_queries_for_the_chunks()
    {
        Article::query()->lazyChunk(10)->flatten(1)->each(MockCallback::mustBeCalled($this->createdArticles));

        $this->queryCount->assertHasBeenCalled(10);
    }

    /** @test */
    public function it_works_on_the_raw_query_builder()
    {
        DB::table('articles')
            ->orderBy('id')
            ->lazyChunk(10)->flatten(1)
            ->each(MockCallback::mustBeCalled($this->createdArticles));

        $this->queryCount->assertHasBeenCalled(10);
    }

    /** @test */
    public function it_supports_flat_lazy_chunk()
    {
        Article::query()->flatLazyChunk(10)->each(MockCallback::mustBeCalled($this->createdArticles));

        $this->queryCount->assertHasBeenCalled(10);
    }

    /** @test */
    public function it_supports_chunk_callback()
    {
        Article::query()
            ->lazyChunk(10, MockCallback::mustBeCalled(10))
            ->flatten(1)
            ->each(MockCallback::mustBeCalled($this->createdArticles));
    }

    /** @test */
    public function it_can_be_stopped_with_the_chunk_callback()
    {
        Article::query()->lazyChunk(10, MockCallback::mustBeCalled(1, function () {
            return false;
        }))->flatten(1)->each(MockCallback::mustBeCalled(10));
    }

    /** @test */
    public function it_can_lazy_chunk_by_id()
    {
        Article::query()->lazyChunkById(10)->each(MockCallback::mustBeCalled(10));
    }

    // This was used to generate a mock database for memory usage testing
    /*public function it_generates_rows()
    {
        for ($i = 0; $i < 1000; $i++) {
            factory(Article::class, 1000)->create();
            dump("Next");
        }
    }*/
}