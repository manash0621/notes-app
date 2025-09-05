<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Note;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NoteTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_user_can_create_a_note()
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->post('/notes', [
                'title' => 'Test Note',
                'content' => 'This is a test note content.',
            ])
            ->assertJson(['success' => true]);

        $this->assertDatabaseHas('notes', [
            'title' => 'Test Note',
            'content' => 'This is a test note content.',
            'user_id' => $user->id,
        ]);
    }

    /** @test */
    public function a_user_can_see_their_notes_on_dashboard()
    {
        $user = User::factory()->create();
        $note = Note::factory()->create([
            'user_id' => $user->id,
            'title' => 'Dashboard Note',
        ]);

        $this->actingAs($user)
            ->get('/dashboard')
            ->assertStatus(200)
            ->assertSee('Dashboard Note');
    }
}
