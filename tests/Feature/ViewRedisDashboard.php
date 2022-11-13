<?php

namespace JohnDoe\BlogPackage\Tests\Feature;

use JohnDoe\BlogPackage\Tests\TestCase;

class ViewRedisDashboard extends TestCase
{
    // /** @test */
    // function authenticated_users_can_create_a_post()
    // {
    //     // To make sure we don't start with a Post
    //     $this->assertCount(0, Post::all());

    //     $author = User::factory()->create();

    //     $response = $this->actingAs($author)->post(route('posts.store'), [
    //         'title' => 'My first fake title',
    //         'body'  => 'My first fake body',
    //     ]);

    //     $this->assertCount(1, Post::all());

    //     tap(Post::first(), function ($post) use ($response, $author) {
    //         $this->assertEquals('My first fake title', $post->title);
    //         $this->assertEquals('My first fake body', $post->body);
    //         $this->assertTrue($post->author->is($author));
    //         $response->assertRedirect(route('posts.show', $post));
    //     });
    // }
}
