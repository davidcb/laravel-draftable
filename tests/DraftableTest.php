<?php

namespace Davidcb\LaravelDraftable\Test;

class DraftableTest extends TestCase
{
    private $defaultTitle = 'new title';

    /** @test */
    public function it_saves_a_draft_for_the_model()
    {
        $dummy = $this->createDummy();
        $dummy->saveAsDraft();

        $this->assertEquals($this->defaultTitle, $dummy->draft->draftable_data['title']);
        $this->assertCount(10, Dummy::all());
        $this->assertCount(1, $dummy->getAllDrafts());
        $this->assertCount(1, $dummy->getNotPublishedDrafts());
        $this->assertCount(0, $dummy->getPublishedDrafts());
    }

    /** @test */
    public function it_saves_a_draft_and_the_model()
    {
        $dummy = $this->createDummy();
        $dummy->saveWithDraft();

        $this->assertEquals($this->defaultTitle, $dummy->draft->draftable_data['title']);
        $this->assertEquals($this->defaultTitle, $dummy->title);
        $this->assertCount(1, $dummy->getAllDrafts());
        $this->assertCount(0, $dummy->getNotPublishedDrafts());
        $this->assertCount(1, $dummy->getPublishedDrafts());
    }

    /** @test */
    public function it_publishes_a_previously_saved_draft()
    {
        $dummy = $this->createDummy();
        $dummy->saveAsDraft();
        $dummy->publish();

        $this->assertEquals($this->defaultTitle, $dummy->title);
        $this->assertCount(0, $dummy->getNotPublishedDrafts());
        $this->assertCount(1, $dummy->getPublishedDrafts());
    }

    private function createDummy()
    {
        $dummy = new Dummy();
        $dummy->title = $this->defaultTitle;

        return $dummy;
    }
}
