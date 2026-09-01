<?php

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Livewire\Livewire;
use Spatie\LivewireFilepond\Tests\TestSupport\Components\TestComponent;

beforeEach(function () {
    $this->basePath = sys_get_temp_dir().'/livewire-filepond-tests';
    $this->publicPath = "{$this->basePath}/public";
    $this->outsidePath = "{$this->basePath}/outside";

    File::deleteDirectory($this->basePath);
    File::ensureDirectoryExists("{$this->publicPath}/uploads");
    File::ensureDirectoryExists($this->outsidePath);

    app()->usePublicPath($this->publicPath);

    config()->set('app.url', 'http://localhost');
});

afterEach(function () {
    File::deleteDirectory($this->basePath);
});

it('can mount a Livewire component that has the filepond trait applied to it', function () {
    Livewire::test(TestComponent::class)->assertOk();
});

it('removes a file that is attached to an array property', function () {
    File::put("{$this->publicPath}/uploads/photo.jpg", 'contents');

    Livewire::test(TestComponent::class, ['photos' => ['/uploads/photo.jpg']])
        ->call('remove', 'photos', '/uploads/photo.jpg')
        ->assertSet('photos', []);

    expect(File::exists("{$this->publicPath}/uploads/photo.jpg"))->toBeFalse();
});

it('removes a file that is attached to a single property', function () {
    File::put("{$this->publicPath}/uploads/photo.jpg", 'contents');

    Livewire::test(TestComponent::class, ['photo' => '/uploads/photo.jpg'])
        ->call('remove', 'photo', '/uploads/photo.jpg')
        ->assertSet('photo', null);

    expect(File::exists("{$this->publicPath}/uploads/photo.jpg"))->toBeFalse();
});

it('removes a file when the client sends the filename prefixed with the app url', function () {
    File::put("{$this->publicPath}/uploads/photo.jpg", 'contents');

    Livewire::test(TestComponent::class, ['photos' => ['/uploads/photo.jpg']])
        ->call('remove', 'photos', 'http://localhost/uploads/photo.jpg')
        ->assertSet('photos', []);

    expect(File::exists("{$this->publicPath}/uploads/photo.jpg"))->toBeFalse();
});

it('removes a file when the property holds the full url', function () {
    File::put("{$this->publicPath}/uploads/photo.jpg", 'contents');

    Livewire::test(TestComponent::class, ['photos' => ['http://localhost/uploads/photo.jpg']])
        ->call('remove', 'photos', 'http://localhost/uploads/photo.jpg')
        ->assertSet('photos', []);

    expect(File::exists("{$this->publicPath}/uploads/photo.jpg"))->toBeFalse();
});

it('removes a file that lives behind the public storage symlink', function () {
    $storagePath = "{$this->basePath}/storage/app/public";
    File::ensureDirectoryExists($storagePath);
    File::put("{$storagePath}/photo.jpg", 'contents');
    symlink($storagePath, "{$this->publicPath}/storage");

    Livewire::test(TestComponent::class, ['photos' => ['/storage/photo.jpg']])
        ->call('remove', 'photos', '/storage/photo.jpg')
        ->assertSet('photos', []);

    expect(File::exists("{$storagePath}/photo.jpg"))->toBeFalse();
});

it('removes a file when the app url is not configured', function () {
    config()->set('app.url', null);

    File::put("{$this->publicPath}/uploads/photo.jpg", 'contents');

    Livewire::test(TestComponent::class, ['photos' => ['/uploads/photo.jpg']])
        ->call('remove', 'photos', '/uploads/photo.jpg')
        ->assertSet('photos', []);

    expect(File::exists("{$this->publicPath}/uploads/photo.jpg"))->toBeFalse();
});

it('leaves the other attached files alone', function () {
    File::put("{$this->publicPath}/uploads/first.jpg", 'contents');
    File::put("{$this->publicPath}/uploads/second.jpg", 'contents');

    Livewire::test(TestComponent::class, ['photos' => ['/uploads/first.jpg', '/uploads/second.jpg']])
        ->call('remove', 'photos', '/uploads/first.jpg')
        ->assertSet('photos', ['/uploads/second.jpg']);

    expect(File::exists("{$this->publicPath}/uploads/second.jpg"))->toBeTrue();
});

it('does not delete a file outside the public path when given a traversing filename', function () {
    $secret = "{$this->outsidePath}/secret.txt";
    File::put($secret, 'secret');

    Livewire::test(TestComponent::class)
        ->call('remove', 'photos', str_repeat('../', 10).ltrim($secret, '/'));

    expect(File::exists($secret))->toBeTrue();
});

it('does not delete a file outside the public path when the traversing filename is attached to the property', function () {
    $secret = "{$this->outsidePath}/secret.txt";
    File::put($secret, 'secret');

    $traversal = str_repeat('../', 10).ltrim($secret, '/');

    Livewire::test(TestComponent::class, ['photos' => [$traversal]])
        ->call('remove', 'photos', $traversal);

    expect(File::exists($secret))->toBeTrue();
});

it('does not delete a file outside the public path when the traversal starts mid path', function () {
    $secret = "{$this->outsidePath}/secret.txt";
    File::put($secret, 'secret');

    $traversal = '/uploads/'.str_repeat('../', 12).ltrim($secret, '/');

    Livewire::test(TestComponent::class, ['photos' => [$traversal]])
        ->call('remove', 'photos', $traversal);

    expect(File::exists($secret))->toBeTrue();
});

it('does not delete a file outside the public path when given an absolute filename', function () {
    $secret = "{$this->outsidePath}/secret.txt";
    File::put($secret, 'secret');

    Livewire::test(TestComponent::class, ['photos' => [$secret]])
        ->call('remove', 'photos', $secret);

    expect(File::exists($secret))->toBeTrue();
});

it('does not delete a file inside the public path that is not attached to the property', function () {
    File::put("{$this->publicPath}/index.php", 'contents');

    Livewire::test(TestComponent::class)
        ->call('remove', 'photos', '/index.php');

    expect(File::exists("{$this->publicPath}/index.php"))->toBeTrue();
});

it('does not delete a file when the filename contains a null byte', function () {
    File::put("{$this->publicPath}/uploads/photo.jpg", 'contents');

    $filename = "/uploads/photo.jpg\0.png";

    Livewire::test(TestComponent::class, ['photos' => [$filename]])
        ->call('remove', 'photos', $filename);

    expect(File::exists("{$this->publicPath}/uploads/photo.jpg"))->toBeTrue();
});

it('does nothing when the property does not exist', function () {
    File::put("{$this->publicPath}/uploads/photo.jpg", 'contents');

    Livewire::test(TestComponent::class)
        ->call('remove', 'nonExistingProperty', '/uploads/photo.jpg')
        ->assertOk();

    expect(File::exists("{$this->publicPath}/uploads/photo.jpg"))->toBeTrue();
});

it('reverts a temporary upload', function () {
    $component = Livewire::test(TestComponent::class)
        ->set('photo', UploadedFile::fake()->image('photo.jpg'));

    $upload = $component->get('photo');

    expect($upload->exists())->toBeTrue();

    $component->call('revert', 'photo', $upload->getFilename())
        ->assertSet('photo', null);

    expect($upload->exists())->toBeFalse();
});
