<span wire:loading wire:target="{{ $target }}" class="loader-btn"></span>

<style>
.loader-btn {
    width: 14px;
    height: 14px;
    border: 2px solid #fff;
    border-top-color: transparent;
    border-radius: 50%;
    display: inline-block;
    vertical-align: middle;
    animation: rotate 0.6s linear infinite;
}

@keyframes rotate {
    100% {
        transform: rotate(360deg);
    }
}
</style>
