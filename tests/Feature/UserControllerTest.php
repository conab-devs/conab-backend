<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;
use App\User;

/** @group users */
class UserControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function should_return_validation_error_if_no_phones_is_passed()
    {
        $user = factory(User::class)->create();

        $response = $this->actingAs($user)->postJson('api/users', [
            'name' => 'valid_name',
            'email' => 'valid_mail@mail.com',
            'password' => 'valid_password',
            'cpf' => '111.111.111-11',
        ]);

        $response->assertStatus(422);
    }

    /** @test */
    public function should_return_validation_error_if_array_is_not_passed_to_phones()
    {
        $user = factory(User::class)->create();

        $response = $this->actingAs($user)->postJson('api/users', [
            'name' => 'valid_name',
            'email' => 'valid_mail@mail.com',
            'password' => 'valid_password',
            'cpf' => '111.111.111-11',
            'phones' => 123,
        ]);
        $response->assertStatus(422);
    }

    /** @test */
    public function should_return_validation_error_if_wrong_format_phone_number_are_passed()
    {
        $user = factory(User::class)->create();

        $response = $this->actingAs($user)->postJson('api/users', [
            'name' => 'valid_name',
            'email' => 'valid_mail@mail.com',
            'password' => 'valid_password',
            'cpf' => '111.111.111-11',
            'phones' => [['number' => '85 85858-8585']],
        ]);
        $response->assertStatus(422);
    }

    /** @test */
    public function should_return_validation_error_if_duplicated_phone_numbers_are_passed()
    {
        $user = factory(User::class)->create();
        $user->phones()->create([
            'number' => '(11) 11111-1111',
        ]);

        $response = $this->actingAs($user)->postJson('api/users', [
            'name' => 'valid_name',
            'email' => 'valid_mail@mail.com',
            'password' => 'valid_password',
            'cpf' => '111.111.111-11',
            'phones' => [['number' => '(11) 11111-1111']],
        ]);
        $response->assertStatus(422);
    }

    /** @test */
    public function should_return_validation_error_if_no_name_is_passed()
    {
        $response = $this->postJson('api/users', [
            'email' => 'valid_mail@mail.com',
            'password' => 'valid_password',
            'cpf' => 'valid_cpf',
        ]);
        $response->assertStatus(422);
    }

    /** @test */
    public function should_return_validation_error_if_integer_is_passed_on_name()
    {
        $response = $this->postJson('api/users', [
            'name' => 123456,
            'email' => 'valid_mail@mail.com',
            'password' => 'valid_password',
            'cpf' => 'valid_cpf',
        ]);
        $response->assertStatus(422);
    }

    /** @test */
    public function should_return_validation_error_if_no_email_is_passed()
    {
        $response = $this->postJson('api/users', [
            'name' => 'valid_name',
            'password' => 'valid_password',
            'cpf' => 'valid_cpf',
        ]);
        $response->assertStatus(422);
    }

    /** @test */
    public function should_return_validation_error_if_invalid_email_is_passed()
    {
        $response = $this->postJson('api/users', [
            'name' => 'valid_name',
            'email' => 'invalid_email',
            'password' => 'valid_password',
            'cpf' => 'valid_cpf',
        ]);
        $response->assertStatus(422);
    }

    /** @test */
    public function should_return_validation_error_if_email_already_exists()
    {
        factory(User::class)->create(['email' => 'valid_mail@mail.com']);

        $response = $this->postJson('api/users', [
            'name' => 'valid_name',
            'email' => 'valid_mail@mail.com',
            'password' => 'valid_password',
            'cpf' => 'valid_cpf',
        ]);
        $response->assertStatus(422);
    }

    /** @test */
    public function should_return_validation_error_if_no_password_is_passed()
    {
        $response = $this->postJson('api/users', [
            'name' => 'valid_name',
            'email' => 'valid_mail@mail.com',
            'cpf' => 'valid_cpf',
        ]);
        $response->assertStatus(422);
    }

    /** @test */
    public function should_return_validation_error_if_integer_password_is_passed()
    {
        $response = $this->postJson('api/users', [
            'name' => 'valid_name',
            'email' => 'valid_mail@mail.com',
            'password' => 123456,
            'cpf' => 'valid_cpf',
        ]);
        $response->assertStatus(422);
    }

    /** @test */
    public function should_return_validation_error_if_password_length_is_lesser_than_6()
    {
        $response = $this->postJson('api/users', [
            'name' => 'valid_name',
            'email' => 'valid_mail@mail.com',
            'password' => '1234',
            'cpf' => 'valid_cpf',
        ]);
        $response->assertStatus(422);
    }

    /** @test */
    public function should_return_validation_error_if_no_cpf_is_passed()
    {
        $response = $this->postJson('api/users', [
            'name' => 'valid_name',
            'email' => 'valid_mail@mail.com',
            'password' => '123456',
        ]);
        $response->assertStatus(422);
    }

    /** @test */
    public function should_return_validation_error_if_integer_cpf_is_passed()
    {
        $response = $this->postJson('api/users', [
            'name' => 'valid_name',
            'email' => 'valid_mail@mail.com',
            'password' => '123456',
            'cpf' => 1234,
        ]);
        $response->assertStatus(422);
    }

    /** @test */
    public function should_return_validation_error_if_cpf_with_wrong_format_is_passed()
    {
        $response = $this->postJson('api/users', [
            'name' => 'valid_name',
            'email' => 'valid_mail@mail.com',
            'password' => '123456',
            'cpf' => '1234',
        ]);
        $response->assertStatus(422);
    }

    /** @test */
    public function should_return_validation_error_if_existing_cpf_is_passed()
    {
        factory(User::class)->create(['cpf' => '123.123.123-12']);

        $response = $this->postJson('api/users', [
            'name' => 'valid_name',
            'email' => 'valid_mail@mail.com',
            'password' => '123456',
            'cpf' => '123.123.123-12',
        ]);
        $response->assertStatus(422);
    }

    /** @test */
    public function should_store_an_user()
    {
        Storage::fake('public');

        $file = UploadedFile::fake()->image('photo.png');

        $response = $this->postJson('api/users', [
            'name' => 'valid_name',
            'email' => 'valid_mail@mail.com',
            'password' => 'valid_password',
            'cpf' => '123.123.123-12',
            'phones' => [['number' => '(11) 11111-1111']],
            'avatar' => $file,
        ]);
        $response->assertCreated();
        $response->assertJsonStructure([
            'name', 'email', 'cpf', 'id', 'updated_at', 'created_at', 'phones',
        ]);

        Storage::disk('public')->assertExists('uploads/'. $file->hashName());

        $this->assertDatabaseHas('users', ['email' => 'valid_mail@mail.com']);

        $this->assertDatabaseHas('phones', ['number' => '(11) 11111-1111']);
    }

    /** @test */
    public function should_return_validation_error_if_integer_is_passed_on_name_on_update()
    {
        $user = factory(User::class)->create();

        $response = $this->actingAs($user)->putJson("api/users", ['name' => 123456]);
        $response->assertStatus(422);
    }

    /** @test */
    public function should_return_validation_error_if_string_is_not_passed_to_phone_on_update()
    {
        $user = factory(User::class)->create();

        $response = $this->actingAs($user)->putJson("api/users", [
            'phones' => 123,
        ]);
        $response->assertStatus(422);
    }

    /** @test */
    public function should_return_validation_error_if_wrong_format_phone_is_passed_on_update()
    {
        $user = factory(User::class)->create();

        $response = $this->actingAs($user)->putJson("api/users", [
            'phones' => '85 85858-8585',
        ]);
        $response->assertStatus(422);
    }

    /** @test */
    public function should_return_validation_error_if_duplicated_phone_is_passed_on_update()
    {
        $user = factory(User::class)->create();
        $user->phones()->create([
            'number' => '(11) 11111-1111',
        ]);

        $response = $this->actingAs($user)->putJson("api/users", [
            'phones' => '(11) 11111-1111',
        ]);
        $response->assertStatus(422);
    }

    /** @test */
    public function should_get_user_phone()
    {
        $user = factory(User::class)->create();
        $phone = $user->phones()->create(factory(\App\Phone::class)->make()->toArray());

        $response = $this->actingAs($user)->getJson('api/users');
        $response->assertJsonFragment($phone->toArray());
    }

    /** @test */
    public function should_return_validation_error_if_invalid_email_is_passed_on_update()
    {
        $user = factory(User::class)->create();

        $response = $this->actingAs($user)->putJson("api/users", [
            'email' => 'invalid_email',
        ]);
        $response->assertStatus(422);
    }

    /** @test */
    public function should_return_validation_error_if_email_already_exists_on_update()
    {
        $user = factory(User::class)->create([
            'email' => 'valid_mail@mail.com',
        ]);

        $response = $this->actingAs($user)->putJson("api/users", [
            'email' => 'valid_mail@mail.com',
        ]);
        $response->assertStatus(422);
    }

    /** @test */
    public function should_return_validation_error_if_integer_password_is_passed_on_update()
    {
        $user = factory(User::class)->create();

        $response = $this->actingAs($user)->putJson("api/users", [
            'password' => 123456,
        ]);
        $response->assertStatus(422);
    }

    /** @test */
    public function should_return_validation_error_if_password_length_is_lesser_than_6_on_update()
    {
        $user = factory(User::class)->create();

        $response = $this->actingAs($user)->putJson("api/users", [
            'password' => '1234',
        ]);
        $response->assertStatus(422);
    }

    /** @test */
    public function should_return_validation_error_if_integer_cpf_is_passed_on_update()
    {
        $user = factory(User::class)->create();

        $response = $this->actingAs($user)->putJson("api/users", [
            'cpf' => 1234,
        ]);
        $response->assertStatus(422);
    }

    /** @test */
    public function should_return_validation_error_if_cpf_with_wrong_format_is_passed_on_update()
    {
        $user = factory(User::class)->create();

        $response = $this->actingAs($user)->putJson("api/users", [
            'cpf' => '1234',
        ]);
        $response->assertStatus(422);
    }

    /** @test */
    public function should_return_validation_error_if_existing_cpf_is_passed_on_update()
    {
        $firstUser = factory(User::class)->create();
        $secondUser = factory(User::class)->create(['cpf' => '123.123.123-12']);

        $response = $this->actingAs($firstUser)->putJson("api/users", ['cpf' => $secondUser->cpf]);
        $response->assertStatus(422);
    }

    /** @test */
    public function should_update_user_without_password()
    {
        $user = factory(\App\User::class)->create();
        $cooperative = factory(\App\Cooperative::class)->create();

        $user->cooperative()->associate($cooperative);

        $new_information = [
            'name' => 'valid_name',
            'email' => 'valid_mail@mail.com',
            'cpf' => '123.123.123-12',
            'phones' => [['number' => '(55) 55555-5555']],
        ];

        $response = $this->actingAs($user)
            ->putJson("api/users", $new_information);
        $response->assertStatus(200);
        $new_informations['phones'] = [['number' => '(55) 55555-5555']];
        $new_informations['isProvider'] = true;
        $response->assertJson($new_informations);
    }

    /** @test */
    public function should_update_user()
    {
        $user = factory(User::class)->create([
            'password' => '123456',
        ]);

        Storage::fake('public');

        $file = UploadedFile::fake()->image('photo.png');

        $user->phones()->create(['number' => '(11) 11111-1111']);

        $new_informations = [
            'name' => 'valid_name',
            'email' => 'valid_mail@mail.com',
            'password' => '123456',
            'new_password' => 'an_password',
            'cpf' => '123.123.123-12',
            'phones' => [['number' => '(55) 55555-5555']],
            'avatar' => $file,
        ];

        $response = $this->actingAs($user)->putJson("api/users", $new_informations);
        $response->assertStatus(200);

        Storage::disk('public')->assertExists('uploads/'. $file->hashName());

        $expected_response = array_diff_assoc($new_informations, [
            'password' => '123456',
            'new_password' => 'an_password',
            'avatar' => $file
        ]);

        $response->assertJson($expected_response);

        $user->refresh();
        $this->assertTrue(Hash::check($new_informations['new_password'], $user->password));
    }

    /** @test */
    public function should_return_validation_error_if_new_password_is_not_sent()
    {
        $user = factory(User::class)->create(['password' => 'valid_pass']);

        $older_password = 'valid_pass';

        $new_informations = [
            'name' => 'valid_name',
            'email' => 'valid_mail@mail.com',
            'password' => $older_password,
            'cpf' => '123.123.123-12',
        ];

        $response = $this->actingAs($user)
            ->putJson("api/users", $new_informations);
        $response->assertStatus(422);
    }

    /** @test */
    public function should_get_the_authenticated_user_data()
    {
        $user = factory(User::class)->create();

        $response = $this->actingAs($user)->getJson('api/users');
        $response->assertStatus(200);

        $response->assertJson($user->toArray());
    }

    /** @test */
    public function should_allow_customers_to_delete_their_accounts_own_accounts()
    {
        $user = factory(User::class)->create([
            'user_type' => 'CUSTOMER',
            'cooperative_id' => null
        ]);

        $response = $this->actingAs($user)->deleteJson("api/users/$user->id");
        $response->assertStatus(204);

        $this->assertDeleted('users', $user->toArray());
    }
}
