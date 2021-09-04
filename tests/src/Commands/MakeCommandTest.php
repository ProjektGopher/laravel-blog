<?php

namespace Projektgopher\Blog\Tests;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class MakeCommandTest extends TestCase
{
    /** @test */
    public function it_has_a_make_command()
    {
        $this->assertTrue(Arr::has(Artisan::all(), 'blog:make'));
    }

    /** @test */
    public function it_sluggifies_a_title()
    {
        File::spy();

        $title = 'Long blog title that has to be sluggified';

        $this->artisan('blog:make')
            ->expectsQuestion('What is the name of your blog post?', $title)
            ->expectsOutput(Str::slug($title));
    }

    /** @test */
    public function it_accepts_a_title_argument()
    {
        File::spy();

        $title = 'Long blog title that has to be sluggified';

        $this->artisan("blog:make '{$title}'")
            ->expectsOutput(Str::slug($title));
    }

    /** @test */
    public function it_creates_the_unpublished_directory_if_none_exists()
    {
        File::spy();

        $this->artisan('blog:make test');

        File::shouldHaveReceived('ensureDirectoryExists')
            ->once()
            ->with('resources/blogs/unpublished');
    }

    /** @test */
    public function it_checks_for_existing_unpublished_markdown_file()
    {
        $name = "Long title that should be sluggified";
        $slug = Str::slug($name);

        File::shouldReceive('ensureDirectoryExists');
        File::shouldReceive('exists')
            ->once()
            ->with("resources/blogs/unpublished/{$slug}.md")
            ->andReturn(true);

        $this->artisan("blog:make '{$name}'")
            ->expectsOutput('This file already exists. Try a different name.')
            ->expectsQuestion('What is the name of your blog post?', $name);
    }

    // check for .md file with same name as slug, prompt if exists (published also, could be problem later)
    // create .md file with slug name, using stub
}
