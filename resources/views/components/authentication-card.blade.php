<div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-brand-50/50">
    <div class="transform hover:scale-105 transition duration-300">
        {{ $logo }}
    </div>

    <div class="w-full sm:max-w-md mt-6 px-8 py-8 bg-white border border-brand-100 shadow-xl overflow-hidden sm:rounded-2xl">
        {{ $slot }}
    </div>
</div>
