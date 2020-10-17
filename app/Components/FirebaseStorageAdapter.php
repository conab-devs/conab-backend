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

    public function uploadFile(string $filePath, string $filename) : bool
    {
        try {
            $uploadedFile = fopen($filePath, 'r');
            $this->storage->getBucket()->upload($uploadedFile, [ 'name' => "uploads/$filename" ]);
            return true;
        } catch (\Exception $error) {
            throw $error;
        }
    }

    public function getUrl(string $filename) : ?string
    {
        $expiresAt = new \DateTime();
        // The url requires to define a expires time, so I defined it to 2 years.
        $expiresAt->add(new \DateInterval('P2Y'));
        $imageReference = $this->storage->getBucket()->object("uploads/$filename");
        if($imageReference->exists())
            return $imageReference->signedUrl($expiresAt);

        return null;
    }
}
