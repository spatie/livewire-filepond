@props([
    'multiple' => false,
    'required' => false,
    'disabled' => false,
    'placeholder' => __('Drag & Drop your files or <span class="filepond--label-action"> Browse </span>'),
])

@php
if (! $wireModelAttribute = $attributes->whereStartsWith('wire:model')->first()) {
    throw new Exception("You must wire:model to the filepond input.");
}

$pondProperties = $attributes->except([
    'class',
    'placeholder',
    'required',
    'disabled',
    'multiple',
    'wire:model',
]);

// convert keys from kebab-case to camelCase
$pondProperties = collect($pondProperties)
    ->mapWithKeys(fn ($value, $key) => [Illuminate\Support\Str::camel($key) => $value])
    ->toArray();

$pondLocalizations = __('livewire-filepond::filepond');
@endphp

<div
    class="{{ $attributes->get('class') }}"
    wire:ignore
    x-cloak
    x-data="{
        model: @entangle($wireModelAttribute),
        isMultiple: @js($multiple),
        current: undefined,
        files: [],
        async loadModel() {
            if (! this.model) {
              return;
            }

            if (this.isMultiple) {
              await Promise.all(Object.values(this.model).map(async (picture) => this.files.push(await URLtoFile(picture))))
              return;
            }

            this.files.push(await URLtoFile(this.model))
        }
    }"
    x-init="async () => {
      await loadModel();

      const pond = LivewireFilePond.create($refs.input);

      pond.setOptions({
          allowMultiple: isMultiple,
          server: {
              process: (fieldName, file, metadata, load, error, progress) => {
                  @this.upload('{{ $wireModelAttribute }}', file, load, error, progress);
              },
              revert: (filename, load) => {
                  @this.revert('{{ $wireModelAttribute }}', filename, load);
              },
              remove: (file, load) => {
              console.log(file);
                  @this.remove('{{ $wireModelAttribute }}', file.name);
                  load();
              },
          },
          labelIdle: @js($placeholder),
          required: @js($required),
          disabled: @js($disabled),
      });

      pond.setOptions(@js($pondLocalizations));

      pond.setOptions(@js($pondProperties));

      pond.addFiles(files)
      pond.on('addfile', (error, file) => {
          if (error) console.log(error);
      });
    }"
>
    <input type="file" x-ref="input">
</div>
