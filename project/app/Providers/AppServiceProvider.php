<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Normalizer\ArrayDenormalizer;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\PropertyInfo\Extractor\ReflectionExtractor;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(SerializerInterface::class, function ($app) {
            $encoders = [new JsonEncoder(), new XmlEncoder()];
            $normalizers = [
                new ObjectNormalizer(null, null, null, new ReflectionExtractor()), // Use reflection for property type extraction
                new ArrayDenormalizer(),
            ];

            return new Serializer($normalizers, $encoders);
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
