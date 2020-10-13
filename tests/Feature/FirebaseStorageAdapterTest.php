<?php

namespace Tests\Feature;

use Mockery;
use App\Components\FirebaseStorageAdapter;
use Google\Cloud\Storage\Bucket;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Kreait\Firebase\Storage as FirebaseStorage;
use Tests\TestCase;
use Kreait\Firebase\Exception\RuntimeException;

class FirebaseStorageAdapterTest extends TestCase
{
    /** @test */
    public function shouldUploadAFile()
    {
        Storage::fake('public');

        $storageMock = Mockery::mock(FirebaseStorage::class);
        $bucketMock = Mockery::mock(Bucket::class);
        $bucketMock->shouldReceive('upload')->once();
        $storageMock->shouldReceive('getBucket')->once()->andReturn($bucketMock);

        $localFolder = public_path('uploads') . '/';
        $image = UploadedFile::fake()->image('photo.png');
        $file = $image->move($localFolder, $image->getFilename());

        $firebaseStorageAdapter = new FirebaseStorageAdapter($storageMock);
        $name = Str::random(80);
        $result = $firebaseStorageAdapter->uploadFile($file->getRealPath(), $name);

        $this->assertTrue($result);

        \Mockery::close();
    }

    /** @test */
    public function shouldReturnFalseIfGetBucketMethodThrowsAnError()
    {
        Storage::fake('public');

        $storageMock = Mockery::mock(FirebaseStorage::class);
        $storageMock->shouldReceive('getBucket')->once()->andThrow(RuntimeException::class);

        $localFolder = public_path('uploads') . '/';
        $image = UploadedFile::fake()->image('photo.png');
        $file = $image->move($localFolder, $image->getFilename());

        $firebaseStorageAdapter = new FirebaseStorageAdapter($storageMock);
        $name = Str::random(80);
        $result = $firebaseStorageAdapter->uploadFile($file->getRealPath(), $name);

        $this->assertFalse($result);

        \Mockery::close();
    }

    /** @test */
    public function shouldReturnFalseIfUploadMethodThrowsAnError()
    {
        Storage::fake('public');

        $storageMock = Mockery::mock(FirebaseStorage::class);
        $bucketMock = Mockery::mock(Bucket::class);
        $bucketMock->shouldReceive('upload')->once()->andThrow(\InvalidArgumentException::class);
        $storageMock->shouldReceive('getBucket')->once()->andReturn($bucketMock);

        $localFolder = public_path('uploads') . '/';
        $image = UploadedFile::fake()->image('photo.png');
        $file = $image->move($localFolder, $image->getFilename());

        $firebaseStorageAdapter = new FirebaseStorageAdapter($storageMock);
        $name = Str::random(80);
        $result = $firebaseStorageAdapter->uploadFile($file->getRealPath(), $name);

        $this->assertFalse($result);

        \Mockery::close();
    }
}
