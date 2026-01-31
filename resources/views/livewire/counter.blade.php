<div>
    <h1>{{ $count }}</h1>

    <button wire:click="increment">+</button>

    <button wire:click="decrement">-</button>
       <input wire:model.live="text" type="text" placeholder="Enter the words here ">
       <p>The text will appear below</h2>
    <h2> {{ $text}}</p>

</div>
