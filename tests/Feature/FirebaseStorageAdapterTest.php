<?php

namespace Tests\Feature;

use Mockery;
use App\Components\FirebaseStorageAdapter;
use Google\Cloud\Storage\Bucket;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Kreait\Firebase\Storage as FirebaseStorage;
use Tests\TestCase;
use Kreait\Firebase\Exception\RuntimeException;

class FirebaseStorageAdapterTest extends TestCase
{
    private $storageMock;
    private $bucketMock;

    public function setUp(): void
    {
        parent::setUp();
        $this->storageMock = $storageMock = Mockery::mock(FirebaseStorage::class);
        $this->bucketMock = $bucketMock = Mockery::mock(Bucket::class);
    }

    public function tearDown(): void
    {
        parent::tearDown();
        Mockery::close();
    }

    /** @test */
    public function shouldUploadAFile()
    {
        Storage::fake('public');

        $this->bucketMock->shouldReceive('upload')->once();
        $this->storageMock->shouldReceive('getBucket')
            ->once()->andReturn($this->bucketMock);
        $file = $this->makeFakeFile();

        $firebaseStorageAdapter = new FirebaseStorageAdapter($this->storageMock);
        $result = $firebaseStorageAdapter->uploadFile($file->getRealPath(), Str::random(80));

        $this->assertTrue($result);
    }

    /** @test */
    public function shouldReturnFalseIfGetBucketMethodThrowsAnError()
    {
        Storage::fake('public');

        $this->storageMock->shouldReceive('getBucket')
            ->once()->andThrow(RuntimeException::class);
        $file = $this->makeFakeFile();

        $firebaseStorageAdapter = new FirebaseStorageAdapter($this->storageMock);
        $result = $firebaseStorageAdapter->uploadFile($file->getRealPath(), Str::random(80));

        $this->assertFalse($result);
    }

    /** @test */
    public function shouldReturnFalseIfUploadMethodThrowsAnError()
    {
        Storage::fake('public');

        $this->bucketMock->shouldReceive('upload')
            ->once()->andThrow(\InvalidArgumentException::class);
        $this->storageMock->shouldReceive('getBucket')
            ->once()->andReturn($this->bucketMock);
        $file = $this->makeFakeFile();

        $firebaseStorageAdapter = new FirebaseStorageAdapter($this->storageMock);
        $result = $firebaseStorageAdapter->uploadFile($file->getRealPath(), Str::random(80));

        $this->assertFalse($result);
    }

    private function makeFakeFile()
    {
        $localFolder = public_path('uploads') . '/';
        $image = UploadedFile::fake()->image('photo.png');
        return $image->move($localFolder, $image->getFilename());
    }
}
