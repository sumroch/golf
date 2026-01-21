<dialog class="modal" id="{{ $modal_id ?? 'default' }}_modal">
    <div class="modal-box p-0">
        <form method="dialog">
            <button class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2">✕</button>
        </form>
        <div class="w-full bg-slate-50 p-8 text-center">
            <img class="w-25 h-25 mx-auto" src="{{ $modal_icon ?? asset('img/icon/default.svg') }}" alt="">
            <h3 class="text-2xl font-bold mb-0 mt-4">{{ $modal_title ?? 'Title Modal' }}</h3>
            <p class="mt-4 text-gray-500">{{ $modal_description ?? 'Description Here' }}</p>
            <a href="{{ $modal_url ?? '#' }}" class="bg-green-700 rounded-lg shadow text-white px-4 py-2 mt-8 inline-block text-xs">
                {{ $modal_button_text ?? 'Continue' }}
            </a>
        </div>
    </div>
    <form class="modal-backdrop" method="dialog">
        <button>close</button>
    </form>
</dialog>
