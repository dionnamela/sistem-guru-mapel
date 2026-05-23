<?php

use Livewire\Component;

new class extends Component {
    public string $name = 'John Doe';
    public int $age = 20;
    public string $grade = 'A';
};
?>

<div class="p-6 bg-white rounded-lg shadow">
    <h1 class="text-2xl font-bold text-gray-900">{{ $name }}</h1>
    <p class="mt-2 text-gray-600">Age: {{ $age }}</p>
    <p class="mt-1 text-gray-600">Grade: {{ $grade }}</p>
</div>
