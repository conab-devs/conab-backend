<?php


namespace App\Components;
use Kreait\Firebase\Storage;

class FirebaseStorageAdapter
{
    private $storage;

    public function __construct(Storage $storage)
    {
        $this->storage = $storage;
    }

    public function uploadFile(string $filePath, string $name) : bool
    {
        try {
            $uploadedFile = fopen($filePath, 'r');
            $this->storage->getBucket()->upload($uploadedFile, [ 'name' => $name ]);
            return true;
        } catch (\Exception $error) {
            return false;
        }
    }

    public function getUrl(string $firebaseObject) : ?string
    {
        $expiresAt = new \DateTime('tomorrow');
        $imageReference = $this->storage->getBucket()->object($firebaseObject);
        if($imageReference->exists())
            return $imageReference->signedUrl($expiresAt);

        return null;
    }
}
