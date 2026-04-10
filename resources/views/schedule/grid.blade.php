{{-- SCHEDULE GRID TAB --}}
<div x-show="activeTab === 'schedule'" x-cloak class="flex-1 overflow-auto">
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
                        {{ $saturdayDate->translatedFormat('M') }}
                    </div>
                    <div class="text-sm font-medium text-gray-700">Sábado {{ $day + 1 }}</div>
                </div>
            @endfor

            {{-- ROWS (one per class) --}}
            @foreach($classes as $class)
                {{-- CLASS NAME --}}
                <div class="flex items-center min-h-[130px]">
                    <span class="font-semibold text-sm {{ $class->level === 'advanced' ? 'text-blue-600' : 'text-green-600' }}">
                        {{ $class->name }}
                    </span>
                </div>

                {{-- SCHEDULE SLOTS --}}
                @for($day = 0; $day < 4; $day++)
                    @php
                        $cellEntries = isset($gridData[$class->id][$day]) ? $gridData[$class->id][$day]['entries'] : collect();
                        $composition = $class->getRequiredComposition();
                        $totalSlots = $composition['professors'] + $composition['mentors'];
                        $allocated = $cellEntries->count();
                        $profCount = $cellEntries->filter(fn($e) => $e->person && $e->person->type === 'professor')->count();
                        $mentorCount = $cellEntries->filter(fn($e) => $e->person && $e->person->type === 'mentor')->count();
                        $existingLessonName = isset($gridData[$class->id][$day]['lesson_name']) ? $gridData[$class->id][$day]['lesson_name'] : '';
                    @endphp
                    <div x-data="{ isOver: false, error: '' }"
                         @dragover.prevent="isOver = true"
                         @dragleave="isOver = false"
                         @drop.prevent="
                             isOver = false;
                             error = '';
                             const personId = $event.dataTransfer.getData('personId') || draggedPersonId;
                             if (personId) {
                                 assignPerson(personId, {{ $day }}, '{{ $class->id }}');
                             }
                         "
                         :class="{
                             'ring-2 ring-blue-400/30 bg-blue-50/50 scale-[1.01]': isOver && !error,
                             'ring-2 ring-red-400/50 bg-red-50/50': error
                         }"
                         class="rounded-xl p-3 bg-white shadow-sm border border-gray-200/50 hover:shadow-md transition-all duration-200 min-h-[130px] flex flex-col"
                         id="slot-{{ $class->id }}-{{ $day }}">

                        {{-- LESSON NAME INPUT — always visible --}}
                        <input type="text"
                               placeholder="Nome da aula..."
                               value="{{ $existingLessonName }}"
                               @change="updateLessonName({{ $schedule->id }}, '{{ $class->id }}', {{ $day }}, $event.target.value)"
                               class="text-xs font-medium text-gray-600 placeholder-gray-300 bg-gray-50/80 px-2 py-1.5 rounded-lg mb-2 w-full
                                      focus:outline-none focus:ring-2 focus:ring-blue-400/50 focus:bg-white
                                      border border-transparent focus:border-blue-200 transition-all
                                      disabled:opacity-40 disabled:cursor-not-allowed">

                        {{-- ENTRIES --}}
                        <div class="flex-1 flex flex-col gap-1.5">
                            @if($cellEntries->isNotEmpty())
                                @foreach($cellEntries as $entry)
                                    <div class="group flex items-center justify-between gap-1 px-2.5 py-1.5 rounded-lg text-xs font-medium transition-all
                                        {{ $entry->person->type === 'professor'
                                           ? 'bg-blue-50/80 text-blue-700 border border-blue-200/60'
                                           : 'bg-green-50/80 text-green-700 border border-green-200/60' }}">
                                        <span class="truncate flex items-center gap-1">
                                            <svg class="w-3 h-3 opacity-40 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                            </svg>
                                            {{ $entry->person->name }}
                                        </span>
                                        <button @click="removeEntry({{ $entry->id }})"
                                                class="flex-shrink-0 w-5 h-5 flex items-center justify-center rounded-full opacity-0 group-hover:opacity-100 hover:bg-white/80 transition-all text-xs font-bold">
                                            ×
                                        </button>
                                    </div>
                                @endforeach
                            @else
                                <div class="flex-1 flex items-center justify-center">
                                    <div class="text-xs text-gray-300 text-center py-2">
                                        <svg class="w-6 h-6 mx-auto mb-1 text-gray-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0zM12.75 12a4.5 4.5 0 11-9 0 4.5 4.5 0 019 0z"></path>
                                        </svg>
                                        Arraste nomes aqui
                                    </div>
                                </div>
                            @endif
                        </div>

                        {{-- ERROR MESSAGE --}}
                        <div x-show="error" x-transition class="text-xs text-red-500 font-medium mt-1 px-1" x-text="error"></div>

                        {{-- CAPACITY INDICATOR --}}
                        <div class="text-xs mt-auto pt-2 border-t border-gray-100 flex items-center justify-between">
                            @if($class->level === 'advanced')
                                <span class="text-gray-400">{{ $profCount }}/{{ $composition['professors'] }} professores</span>
                            @else
                                <span class="text-gray-400">{{ $profCount }}/{{ $composition['professors'] }} prof · {{ $mentorCount }}/{{ $composition['mentors'] }} ment</span>
                            @endif
                            @if($allocated >= $totalSlots)
                                <span class="text-green-500 text-xs">✓</span>
                            @endif
                        </div>
                    </div>
                @endfor
            @endforeach
        </div>
    </div>
</div>
