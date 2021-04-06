<?php

namespace Tests\Unit;

use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use App\Models\UserDetails;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    public function test_validation_requires_first_name_last_name_email_password()
    {
        $user = User::factory()->make();
        $this->actingAs($user);
        $this->json('POST', route('users.store'), [], [])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['first_name','last_name','email','password' ]);
    }

    public function test_user_is_created_correctly()
    {
        $user = User::factory()->make();
        $this->actingAs($user);
        $payload = [
            'first_name' => 'Some name',
            'last_name' => 'Some last name',
            'email' => 'some@email.lt',
            'password' => 'somePassword123',
        ];

        $this->json('POST', route('users.store'), $payload, [])
            ->assertStatus(201)
            ->assertJson([
                'data' => [
                        'first_name' => 'Some name',
                        'last_name' => 'Some last name',
                        'email' => 'some@email.lt',
                    ]
            ]);
    }

    public function test_user_with_address_is_also_added_correctly()
    {
        $user = User::factory()->make();
        $this->actingAs($user);
        $payload = [
            'first_name' => 'Another first name',
            'last_name' => 'Another last name',
            'email' => 'another@email.lt',
            'password' => 'somePassword123',
            'address' => 'Some address, Vilnius, Lithuania',
        ];

        $this->json('POST', route('users.store'), $payload, [])
            ->assertStatus(201)
            ->assertJson([
                'message'=> 'User successfully created',
                'data' => [
                    'first_name' => 'Another first name',
                    'last_name' => 'Another last name',
                    'email' => 'another@email.lt',
                    'address' => 'Some address, Vilnius, Lithuania',
                ]
            ]);
    }

    public function test_user_is_updated_correctly()
    {
        $user = User::factory()->create([
            'first_name' => 'Original some name',
            'last_name' => 'Original last name',
            'email' => 'original@email.lt',
            'password' => 'somePassword123',
        ]);
        $this->actingAs($user);
        $payload = [
            'first_name' => 'Updated name',
            'last_name' => 'Updated last name',
            'email' => 'updated@email.lt',
            'password' => 'updatedPassword123',
        ];

        $this->json('PUT', route('users.update', $user->id), $payload, [])
            ->assertJson([
                'data' => [
                    'first_name' => 'Updated name',
                    'last_name' => 'Updated last name',
                    'email' => 'updated@email.lt',
                ]
            ]);
    }

    public function test_user_address_is_also_updated_correctly()
    {
        $user = User::factory()->create();
        $this->actingAs($user);
        $rr = $user->getAttributes();
        //dd($rr);
        UserDetails::factory()->for($user)->create();

        $payload = [
            'first_name' => 'Updated another name',
            'last_name' => 'Updated another last name',
            'email' => 'updated.another@email.lt',
            'password' => 'updated.anotherPassword123',
            'address' => 'Updated some address, Vilnius, Lithuania',
        ];

        $this->json('PUT', route('users.update', $user->id), $payload, [])
            ->assertStatus(201)
            ->assertJson([
                'data' => [
                    'address' => 'Updated some address, Vilnius, Lithuania',
                ]
            ]);
    }

    public function test_user_is_deleted_correctly()
    {
        $user = User::factory()->create();
        $this->actingAs($user);
        $this->json('DELETE', route('users.destroy', $user->id), [], [])
            ->assertStatus(204);
    }

    public function test_user_address_is_also_deleted_correctly()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        // User's address field also added
        UserDetails::factory()->for($user)->create();

        $this->json('DELETE', route('users.destroy', $user->id), [], []);

        $this->assertDeleted($user);

    }

    public function test_users_are_listed_correctly()
    {
        User::factory()->count(3)->create();
        $user = User::factory()->create();
        $this->actingAs($user);
        // User's address field also added
        UserDetails::factory()->for($user)->create();

        $this->json('GET', route('users.index'), [], [])
            ->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'first_name',
                        'last_name',
                        'email',
                        'created_at',
                        'updated_at'
                    ]
                ],
                 'meta' => [
                     'current_page',
                     'from',
                     'last_page',
                     'links'=> [
                         '*' => [
                             'url',
                             'label',
                             'active',
                         ]
                     ],
                     'path',
                     'per_page',
                     'to',
                     'total'
            ]]);

    }
}
