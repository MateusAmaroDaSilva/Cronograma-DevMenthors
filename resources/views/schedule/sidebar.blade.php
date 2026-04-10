<aside class="w-72 bg-white border-r border-gray-200/50 flex flex-col overflow-hidden">
    {{-- BUTTONS --}}
    <div class="px-5 py-4 border-b border-gray-200/50">
        <div class="flex gap-2">
            <button @click="showAddModal = true" id="btn-add-person"
                    class="flex items-center justify-center gap-1.5 text-sm font-medium px-3 py-2 border border-gray-200 text-gray-700 hover:bg-gray-50 rounded-lg transition-colors flex-1">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>
                </svg>
                Adicionar
            </button>
            <button @click="showRemoveModal = true" id="btn-remove-person"
                    class="flex items-center justify-center gap-1.5 text-sm font-medium px-3 py-2 border border-gray-200 text-gray-700 hover:bg-gray-50 rounded-lg transition-colors flex-1">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7a4 4 0 11-8 0 4 4 0 018 0zM9 14a6 6 0 00-6 6v1h12v-1a6 6 0 00-6-6zM21 12h-6"></path>
                </svg>
                Remover
            </button>
        </div>
    </div>

    {{-- PEOPLE LIST --}}
    <div class="flex-1 overflow-y-auto px-5 py-4 space-y-5">

        {{-- ── PROFESSORES ── --}}
        <div>
            <h3 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-3">
                Professores ({{ $people->where('type', 'professor')->count() }})
            </h3>
            <div class="flex flex-wrap gap-1.5">
                @foreach($people->where('type', 'professor')->sortBy('name') as $person)
                    <div draggable="true"
                         x-data="{ dragging: false }"
                         @dragstart="dragging = true; draggedPersonId = '{{ $person->id }}'; draggedPersonType = 'professor'; $event.dataTransfer.setData('personId', '{{ $person->id }}'); $event.dataTransfer.effectAllowed = 'move';"
                         @dragend="dragging = false; draggedPersonId = null; draggedPersonType = null;"
                         :class="{ 'opacity-40 scale-95': dragging }"
                         class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium cursor-grab active:cursor-grabbing
                                border border-blue-200/70 bg-blue-50/50 text-blue-700
                                hover:bg-blue-100/70 hover:border-blue-300/80 transition-all duration-150"
                         id="person-{{ $person->id }}">
                        <svg class="w-3 h-3 opacity-40 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                        <span class="truncate">{{ $person->name }}</span>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- ── MENTORADOS ── --}}
        <div>
            <h3 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-3">
                Mentorados ({{ $people->where('type', 'mentor')->count() }})
            </h3>
            <div class="flex flex-wrap gap-1.5">
                @foreach($people->where('type', 'mentor')->sortBy('name') as $person)
                    <div draggable="true"
                         x-data="{ dragging: false }"
                         @dragstart="dragging = true; draggedPersonId = '{{ $person->id }}'; draggedPersonType = 'mentor'; $event.dataTransfer.setData('personId', '{{ $person->id }}'); $event.dataTransfer.effectAllowed = 'move';"
                         @dragend="dragging = false; draggedPersonId = null; draggedPersonType = null;"
                         :class="{ 'opacity-40 scale-95': dragging }"
                         class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium cursor-grab active:cursor-grabbing
                                border border-green-200/70 bg-green-50/50 text-green-700
                                hover:bg-green-100/70 hover:border-green-300/80 transition-all duration-150"
                         id="person-{{ $person->id }}">
                        <svg class="w-3 h-3 opacity-40 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                        <span class="truncate">{{ $person->name }}</span>
                    </div>
                @endforeach
            </div>
        </div>

    </div>

    {{-- RULES SECTION --}}
    <div class="border-t border-gray-200/50 px-5 py-4 bg-gray-50/30">
        <h3 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Regras</h3>
        <ul class="space-y-1 text-xs text-gray-500 leading-relaxed">
            <li class="flex gap-1.5 items-start">
                <span class="text-gray-300 mt-px">•</span>
                <span>Mentorados só em turmas Junior</span>
            </li>
            <li class="flex gap-1.5 items-start">
                <span class="text-gray-300 mt-px">•</span>
                <span>Mentorados não dão aula 2 sábados seguidos</span>
            </li>
            <li class="flex gap-1.5 items-start">
                <span class="text-gray-300 mt-px">•</span>
                <span>Todos devem ter ao menos 1 folga no mês</span>
            </li>
            <li class="flex gap-1.5 items-start">
                <span class="text-gray-300 mt-px">•</span>
                <span>1 pessoa por turma por sábado</span>
            </li>
        </ul>
    </div>
</aside>
