<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="Sistema de cronograma mensal de aulas da DevMenthors. Gerenciamento de rodízio de professores e mentorados.">
    <title>{{ config('app.name') }} — Cronograma de Aulas</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;450;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100 text-gray-900/80 font-inter antialiased" style="font-feature-settings: 'tnum';">

    <div class="fixed inset-0 pointer-events-none h-screen z-0">
        <svg class="pointer-events-none absolute left-0 top-0 h-full w-[120px] opacity-60"
             viewBox="0 0 120 800" preserveAspectRatio="none" fill="none">
            <path d="M-60 0C-20 100 80 150 40 300C0 450 90 500 50 650C10 800 -30 750 -60 800V0Z"
                  fill="hsl(230 90% 50%)" fill-opacity="0.15"/>
            <path d="M-80 0C-40 120 60 180 20 340C-20 500 70 540 30 680C-10 820 -50 780 -80 800V0Z"
                  fill="hsl(230 90% 50%)" fill-opacity="0.08"/>
        </svg>

        <svg class="pointer-events-none absolute right-0 top-0 h-full w-[120px] opacity-60"
             viewBox="0 0 120 800" preserveAspectRatio="none" fill="none">
            <path d="M180 0C140 100 40 150 80 300C120 450 30 500 70 650C110 800 150 750 180 800V0Z"
                  fill="hsl(230 90% 50%)" fill-opacity="0.15"/>
            <path d="M200 0C160 120 60 180 100 340C140 500 50 540 90 680C130 820 170 780 200 800V0Z"
                  fill="hsl(230 90% 50%)" fill-opacity="0.08"/>
        </svg>
    </div>

    <div x-data="scheduleBoard({{ json_encode([
        'initialMonth' => $currentMonth,
        'initialYear' => $currentYear,
        'people' => $people->map(fn($p) => [
            'id' => $p->id,
            'name' => $p->name,
            'type' => $p->type,
            'allocations' => $p->current_allocations ?? 0,
        ]),
        'scheduleId' => $schedule->id,
    ]) }})" x-init="init()" class="relative h-screen flex flex-col overflow-hidden z-10">

        <header class="flex items-center justify-between px-8 py-4 bg-white/80 backdrop-blur-sm border-b border-gray-200/50">
            <div class="flex items-center gap-3">
                <img src="{{ asset('assets/devmenthors_LogoColor.png') }}" alt="DevMenthors" class="h-8 w-auto">
                <span class="text-lg font-semibold text-gray-900">DevMenthors · Cronograma</span>
            </div>

            <div class="flex items-center gap-6">
                {{-- MONTH NAVIGATOR --}}
                <div class="flex items-center gap-3">
                    <button @click="changeMonth(-1)" id="btn-prev-month" class="p-2 hover:bg-gray-100 rounded-lg transition-colors">
                        <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                        </svg>
                    </button>
                    <span class="text-sm font-medium min-w-[150px] text-center select-none" x-text="getCurrentMonthName()"></span>
                    <button @click="changeMonth(1)" id="btn-next-month" class="p-2 hover:bg-gray-100 rounded-lg transition-colors">
                        <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </button>
                </div>

                {{-- ACTION BUTTONS --}}
                <div class="flex items-center gap-3">
                    <button @click="generateSchedule()" id="btn-generate"
                            class="flex items-center gap-2 px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 active:bg-blue-700 transition-colors font-medium text-sm shadow-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                        </svg>
                        Gerar Rodízio
                    </button>
                    <button @click="clearSchedule()" id="btn-clear"
                            class="flex items-center gap-2 px-4 py-2 border-2 border-gray-300 text-gray-700 rounded-lg hover:border-gray-400 hover:bg-gray-50 active:bg-gray-100 transition-colors font-medium text-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3H4v2h16V7h-3z"></path>
                        </svg>
                        Limpar
                    </button>
                </div>
            </div>
        </header>

        <div class="flex flex-1 overflow-hidden">
            @include('schedule.sidebar')

            <div class="flex-1 flex flex-col overflow-hidden">
                <div class="flex border-b border-gray-200/50 bg-white/50 backdrop-blur-sm px-8">
                    <button @click="activeTab = 'schedule'" id="tab-schedule"
                            :class="{'border-b-2 border-blue-500 text-blue-600': activeTab === 'schedule', 'text-gray-500 hover:text-gray-700': activeTab !== 'schedule'}"
                            class="flex items-center gap-2 px-4 py-3 font-medium transition-colors text-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                        Cronograma
                    </button>
                    <button @click="activeTab = 'substitutes'" id="tab-substitutes"
                            :class="{'border-b-2 border-blue-500 text-blue-600': activeTab === 'substitutes', 'text-gray-500 hover:text-gray-700': activeTab !== 'substitutes'}"
                            class="flex items-center gap-2 px-4 py-3 font-medium transition-colors text-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                        Suplentes
                    </button>
                </div>

                @include('schedule.grid')

                @include('schedule.substitutes')
            </div>
        </div>

        @include('schedule.modals.add-person')
        @include('schedule.modals.remove-person')
        @include('schedule.modals.password-modal')
        @include('schedule.modals.toast')
    </div>

</body>
</html>
