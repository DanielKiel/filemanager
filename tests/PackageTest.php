<?php

/**
 * Created by PhpStorm.
 * User: dk
 * Date: 25.11.17
 * Time: 16:05
 */
use  Illuminate\Support\Facades\Storage;
class PackageTest extends \Tests\TestCase
{
    use \Illuminate\Foundation\Testing\DatabaseTransactions;


    public function testUploadAPI()
    {
        $user = \App\User::create([
            'name' => 'admin',
            'email' => 'admin@admin.de',
            'password' => bcrypt('password')
        ]);

        \Laravel\Passport\Passport::actingAs($user);

        Storage::fake('local');

        //we will have validation error here
        $response = $this->json('POST', '/services/filemanager/v1/upload/local', [
            'upload' => \Illuminate\Http\UploadedFile::fake()->image('avatar.jpg'),
        ]);

        $this->assertEquals(200, $response->getStatusCode());


        $uploaded = json_decode($response->getContent());

        // Assert the file was stored...
        Storage::disk()->assertExists($uploaded->path);

        // Assert a file does not exist...
        Storage::disk()->assertMissing('missing.jpg');

        $response = $this->get('/services/filemanager/v1/' . $uploaded->id);

        $this->assertEquals(200, $response->getStatusCode());

        $img = getimagesizefromstring(base64_decode($response->getContent()));

        $this->assertEquals(180, $img[0]);

        $response = $this->get('/services/filemanager/v1/' . $uploaded->id . '/0');

        $this->assertEquals(200, $response->getStatusCode());

        $img = getimagesizefromstring(base64_decode($response->getContent()));

        $this->assertEquals(10, $img[0]);

        $this->assertEquals(1, \Dionyseos\Filemanager\Models\File::published()->count());

        $this->assertEquals(0, \Dionyseos\Filemanager\Models\File::published(false)->count());

        $response = $this->json('PUT','/services/filemanager/v1/' . $uploaded->id, [
            'published' => false
        ]);

        $this->assertEquals(200, $response->getStatusCode());

        $this->assertEquals(0, \Dionyseos\Filemanager\Models\File::published()->count());

        $this->assertEquals(1, \Dionyseos\Filemanager\Models\File::published(false)->count());
    }
}
