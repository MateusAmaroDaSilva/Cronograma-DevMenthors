{{-- REMOVE PERSON MODAL --}}
<div x-show="showRemoveModal"
     x-transition:enter="transition ease-out duration-200"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="transition ease-in duration-150"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0"
     @keydown.escape.window="showRemoveModal = false"
     class="fixed inset-0 z-50 flex items-center justify-center bg-black/30 backdrop-blur-sm"
     @click.self="showRemoveModal = false"
     x-cloak>

    <div x-show="showRemoveModal"
         x-transition:enter="transition ease-out duration-200 delay-75"
         x-transition:enter-start="opacity-0 scale-95 translate-y-4"
         x-transition:enter-end="opacity-100 scale-100 translate-y-0"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95"
         class="bg-white rounded-2xl shadow-xl p-8 max-w-md w-full mx-4"
         @click.stop>

        <div class="flex items-center justify-between mb-6">
            <h3 class="text-xl font-bold text-gray-900">Remover Pessoa</h3>
            <button type="button" @click="showRemoveModal = false"
                    class="text-gray-400 hover:text-gray-600 transition-colors p-1 hover:bg-gray-100 rounded-lg">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>

        {{-- SEARCH INPUT --}}
        <div class="mb-5">
            <div class="relative">
                <svg class="w-4 h-4 absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
                <input type="text" x-model="removeFilter" placeholder="Pesquisar nome..."
                       class="w-full pl-10 pr-4 py-2.5 border-2 border-gray-200 rounded-xl focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-colors text-sm">
            </div>
        </div>

        {{-- PEOPLE LIST --}}
        <div class="space-y-4 mb-6 max-h-72 overflow-y-auto pr-1">
            {{-- PROFESSORS --}}
            <div>
                <h4 class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2 px-1 flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-blue-400"></span>
                    Professores
                </h4>
                <div class="space-y-1.5">
                    @foreach($people->where('type', 'professor')->sortBy('name') as $person)
                        <button type="button" @click="removePerson('{{ $person->id }}', '{{ addslashes($person->name) }}')"
                                x-show="'{{ addslashes($person->name) }}'.toLowerCase().includes(removeFilter.toLowerCase())"
                                class="w-full text-left px-4 py-2.5 rounded-xl bg-blue-50 text-blue-700 hover:bg-blue-100 border border-blue-200 hover:border-blue-300 transition-all text-sm font-medium flex items-center justify-between group">
                            <span>{{ $person->name }}</span>
                            <svg class="w-4 h-4 opacity-0 group-hover:opacity-100 text-red-400 transition-opacity" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3H4v2h16V7h-3z"></path>
                            </svg>
                        </button>
                    @endforeach
                </div>
            </div>

            {{-- MENTORS --}}
            <div>
                <h4 class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2 px-1 flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-green-400"></span>
                    Mentorados
                </h4>
                <div class="space-y-1.5">
                    @foreach($people->where('type', 'mentor')->sortBy('name') as $person)
                        <button type="button" @click="removePerson('{{ $person->id }}', '{{ addslashes($person->name) }}')"
                                x-show="'{{ addslashes($person->name) }}'.toLowerCase().includes(removeFilter.toLowerCase())"
                                class="w-full text-left px-4 py-2.5 rounded-xl bg-green-50 text-green-700 hover:bg-green-100 border border-green-200 hover:border-green-300 transition-all text-sm font-medium flex items-center justify-between group">
                            <span>{{ $person->name }}</span>
                            <svg class="w-4 h-4 opacity-0 group-hover:opacity-100 text-red-400 transition-opacity" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3H4v2h16V7h-3z"></path>
                            </svg>
                        </button>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- CLOSE BUTTON --}}
        <button type="button" @click="showRemoveModal = false; removeFilter = '';"
                class="w-full py-2.5 px-4 border-2 border-gray-300 text-gray-700 font-medium rounded-xl hover:border-gray-400 hover:bg-gray-50 transition-colors text-sm">
            Fechar
        </button>
    </div>
</div>
