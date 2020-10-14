<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Kreait\Firebase\Factory;
use App\Components\FirebaseStorageAdapter;

class FirebaseStorageProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(FirebaseStorageAdapter::class, function ($app) {
            $firebaseCredentials = [
                "type" => "service_account",
                "project_id" => "conab-cd8dd",
                "private_key_id" => "895f2fd4e7ad50dc6315305bcb532ce170532c0a",
                "private_key" => "-----BEGIN PRIVATE KEY-----\nMIIEvAIBADANBgkqhkiG9w0BAQEFAASCBKYwggSiAgEAAoIBAQCrXq8X+tLe43sp\nEAm3vggOr0y4wfPPR0x//mylkB7QK9Zj3JAQk8LC2VlR9MOlGYCNHIxb3ID02s5F\n7AlVgTf1XNw4uOT5u+Gy36B8wtByyLFvGqE2w+//STAvyrjkvEqCOex77L/CqQUZ\n+WQJxWWJYBwq49htwmhJHQUDBTxMLmZKvhnWJV8KvPtwqYLlZyJH9ogERDxdwNek\nyselTGL/Z9HoNZWCD8V1EFB6ntz4q+dfo2kkTo+0BtBG3sg2C6pBzCVg8yS9me7d\n+uWJ6YGzuAv2af4ndJS7Tf3cFI9HotriZAE+9IlBMttAZ/4EUNfXd33dy+7hjipq\nqm8dnZbPAgMBAAECggEAKPWIhAK/Q+a7GSVsLS6OXnQnskEpNPGlpzH4Zdn8g2T2\n6Qdep5ephUjNwHjwChX+U7vvEEWzzNI/Wxb/ZCBub4ayXBxVHht55nbstOUbTVQK\nlLOYtK5APpQPoFyjtIgMhvOxQi1j5/Ld9UdWUb+MdTkzgkzS/ejpZBA7km097DcG\nRAUFVaayWFoMQPPOIJAEMMLhzE8+NrgLfC4UzXp3uhJGDuGjqtBY0Jyh0AgfucA0\n1NMro4VZB8/dQDl97esLEkSJrfdCZephLm3lZMQxc6NNldUl7WJEx3xckWIZDvL4\nw1UkyQulMlKVtU4ua7zUL/Ykax1swdFed8VQL+5QgQKBgQDmu/1z/pK6rMPIYiyW\nfYPE91F05Uzh41LckGSbyFyAu1gCiOaMFt36TDfTR54bx9cCq0rdQKYCNx+hcAMj\n+nKUt2PiiTYY2S6yDRQljgs3tXlTe6LkcxaP+oA/vyTr72kL2eEvBFzdADoOMYxw\nfGy+ODx6QUo3S8SVIEm51gYl2wKBgQC+IpJ48+D1cefdoXH0s8A/OxZCM95W+z4X\ng2BnCFtFjvJCaNutboc98EZGmiEu/IjSlFAr0AfKuSmE3lS9/9TRmMuxrJO5kZQs\n+4oemacDQzN03ZOn6hm75i2kyQTo4lutMqU0lEbmmX2B4Y5aHc2c8W500QlNbpgo\nGWrGtr/3HQKBgEdf5dJo8JjAcrvc6rAO2aEnZFJF0FecC3jhTc4G196WlW4LTU4O\nkNIpm6dG4zjyw6c2t6zjn7bVuHom/UG26pToegSMr2hWXqSDeTT40K2F/Kb/eu8y\nTstxERUnGtPFNniNCKSTs+pFdLAJUclGJBlfqg2jfsLGNGRxHX1YIIS7AoGAQP4M\nV2/127B985J2I5E2C+ckqqiUPeNPzDXKRC3lFRfR08WIIfaAIDn7q7KP2UHVezXb\nWb6Yi0FT4eklynSiyKqUJj25mVsb7WxmQCOBpWnZMv9ys5kgBsydmZBlLz4A3GlN\nc3Wj7rtU3Yq+YCuD1zvn5+J0bZV6SWq2xwo9ADkCgYAaF97R4Oe1/xjuzqbzPHEg\nbJXFGf0fmiUWUQ/Gd1W8eRH7ASk+IH5/fnrJbaLbcZ2t0ZH0N25BWPPNyE0G4uGz\nrrrgJqnZcu6aiFzXHiWTfQyPHflIAbIQp8s4Uspim07CKKI1Jk7v9xXNP7WWn8zx\nA8UsGMjVWHnsMdhN4S2dpw==\n-----END PRIVATE KEY-----\n",
                "client_email" => "firebase-adminsdk-i0c6d@conab-cd8dd.iam.gserviceaccount.com",
                "client_id" => "110579680357193019098",
                "auth_uri" => "https://accounts.google.com/o/oauth2/auth",
                "token_uri" => "https://oauth2.googleapis.com/token",
                "auth_provider_x509_cert_url" => "https://www.googleapis.com/oauth2/v1/certs",
                "client_x509_cert_url" => "https://www.googleapis.com/robot/v1/metadata/x509/firebase-adminsdk-i0c6d%40conab-cd8dd.iam.gserviceaccount.com"
            ];
            $storage = (new Factory)->withServiceAccount($firebaseCredentials)->createStorage();
            return new FirebaseStorageAdapter($storage);
        });
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
