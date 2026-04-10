{{-- SUBSTITUTES GRID TAB --}}
<div x-show="activeTab === 'substitutes'" x-cloak class="flex-1 overflow-auto">
    <div class="p-8">
        {{-- GRID: 4 turmas × 4 sábados --}}
        <div class="grid" style="grid-template-columns: 140px repeat(4, 1fr); gap: 1rem;">

            {{-- COLUMN HEADERS --}}
            <div></div>
            @for($day = 0; $day < 4; $day++)
                <div class="text-center pb-2">
                    @php
                        $baseDate = \Carbon\Carbon::createFromDate($currentYear, $currentMonth, 1)->startOfMonth();
                        while ($baseDate->dayOfWeek != 6) { $baseDate->addDay(); }
                        $saturdayDate = $baseDate->copy()->addWeeks($day);
                    @endphp
                    <div class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-0.5">
                        {{ $saturdayDate->format('d') }}
                        {{ \Carbon\Carbon::createFromDate($currentYear, $currentMonth, 1)->translatedFormat('M') }}
                    </div>
                    <div class="text-sm font-medium text-gray-700">Sábado {{ $day + 1 }}</div>
                </div>
            @endfor

            {{-- ROWS (one per class) --}}
            @foreach($classes as $class)
                {{-- CLASS NAME --}}
                <div class="flex items-center min-h-[120px]">
                    <span class="font-semibold text-sm {{ $class->level === 'advanced' ? 'text-teacher-tag-text' : 'text-mentor-tag-text' }}">
                        {{ $class->name }}
                    </span>
                </div>

                {{-- SUBSTITUTE SLOTS --}}
                @for($day = 0; $day < 4; $day++)
                    @php
                        $cellSubs = isset($gridData[$class->id][$day]) ? $gridData[$class->id][$day]['substitutes'] : collect();
                    @endphp
                    <div x-data="{ isOver: false, error: '' }"
                         @dragover.prevent="isOver = true"
                         @dragleave="isOver = false"
                         @drop.prevent="
                             isOver = false;
                             error = '';
                             const personId = $event.dataTransfer.getData('personId') || draggedPersonId;
                             if (personId) {
                                 assignSubstitute(personId, {{ $day }}, '{{ $class->id }}');
                             }
                         "
                         :class="{
                             'ring-2 ring-blue-400/30 bg-blue-50/50 scale-[1.02]': isOver && !error,
                             'ring-2 ring-red-400/50 bg-red-50/50': error
                         }"
                         class="rounded-xl p-3 bg-white shadow-sm border border-gray-200/50 hover:shadow-md transition-all duration-200 min-h-[120px] flex flex-col"
                         id="sub-{{ $class->id }}-{{ $day }}">

                        {{-- SUBSTITUTES --}}
                        <div class="flex-1 flex flex-col gap-1.5">
                            @if($cellSubs->isNotEmpty())
                                @foreach($cellSubs as $substitute)
                                    <div class="group flex items-center justify-between gap-1 px-2.5 py-1.5 rounded-lg text-xs font-medium transition-all
                                        {{ $substitute->person->type === 'professor'
                                           ? 'bg-amber-50 text-amber-700 border border-amber-200'
                                           : 'bg-orange-50 text-orange-700 border border-orange-200' }}">
                                        <span class="truncate flex items-center gap-1">
                                            <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                                            </svg>
                                            {{ $substitute->person->name }}
                                        </span>
                                        <button @click="removeSubstitute({{ $substitute->id }})"
                                                class="flex-shrink-0 w-5 h-5 flex items-center justify-center rounded-full opacity-0 group-hover:opacity-100 hover:bg-white/60 transition-all text-xs font-bold">
                                            ×
                                        </button>
                                    </div>
                                @endforeach
                            @else
                                <div class="flex-1 flex items-center justify-center">
                                    <div class="text-xs text-gray-300 text-center py-2">
                                        <svg class="w-6 h-6 mx-auto mb-1 text-gray-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                                        </svg>
                                        Arraste suplentes
                                    </div>
                                </div>
                            @endif
                        </div>

                        {{-- ERROR MESSAGE --}}
                        <div x-show="error" x-transition class="text-xs text-red-500 font-medium mt-1 px-1" x-text="error"></div>
                    </div>
                @endfor
            @endforeach
        </div>
    </div>
</div>
