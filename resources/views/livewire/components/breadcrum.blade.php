<div>
    <div class=" w-[90%] mx-auto md:w-full  items-center justify-between page-block md:flex ">
        <div class="mb-4 page-header-title">
            <h4 class="mb-0 text-2xl md:text-3xl uppercase font-semibold bg-gradient-to-r from-purple-500 via-pink-500 to-red-500 bg-clip-text text-transparent">{{ strtoupper($title ?? 'Dashboard') }}</h4>
        </div>
        <ul class="absolute mt-4 right-10 breadcrumb">
           
            <li class="text-lg breadcrumb-item"><a href="javascript: void(0)">Dashboard</a></li>
            <li class="text-lg breadcrumb-item" aria-current="page">{{ $title ?? 'Dashboard' }}</li>
        </ul>
    </div>
 {{-- <li class="text-lg breadcrumb-item"><a href="../dashboard/index.html">Home</a></li> --}}
</div>

