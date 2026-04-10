
<div x-show="showAddModal"
     x-transition:enter="transition ease-out duration-200"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="transition ease-in duration-150"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0"
     @keydown.escape.window="showAddModal = false"
     class="fixed inset-0 z-50 flex items-center justify-center bg-black/30 backdrop-blur-sm"
     @click.self="showAddModal = false"
     x-cloak>

    <div x-show="showAddModal"
         x-transition:enter="transition ease-out duration-200 delay-75"
         x-transition:enter-start="opacity-0 scale-95 translate-y-4"
         x-transition:enter-end="opacity-100 scale-100 translate-y-0"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95"
         class="bg-white rounded-2xl shadow-xl p-8 max-w-md w-full mx-4"
         @click.stop>

        <h3 class="text-xl font-bold text-gray-900 mb-6">Adicionar Pessoa</h3>

        <form @submit.prevent="addPerson">
            
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">Nome</label>
                <input type="text" x-model="newPersonName" required autofocus
                       placeholder="Digite o nome completo..."
                       class="w-full px-4 py-2.5 border-2 border-gray-200 rounded-xl focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-colors text-sm">
            </div>

            
            <div class="mb-8">
                <label class="block text-sm font-medium text-gray-700 mb-3">Tipo</label>
                <div class="flex gap-3">
                    <button type="button" @click="newPersonType = 'professor'"
                            :class="{
                                'ring-2 ring-blue-500 ring-offset-2 border-blue-400': newPersonType === 'professor',
                                'border-blue-200': newPersonType !== 'professor'
                            }"
                            class="flex-1 py-3 px-4 rounded-xl border-2 bg-blue-50 text-blue-700 font-medium hover:bg-blue-100 transition-all text-sm">
                         Professor
                    </button>
                    <button type="button" @click="newPersonType = 'mentor'"
                            :class="{
                                'ring-2 ring-green-500 ring-offset-2 border-green-400': newPersonType === 'mentor',
                                'border-green-200': newPersonType !== 'mentor'
                            }"
                            class="flex-1 py-3 px-4 rounded-xl border-2 bg-green-50 text-green-700 font-medium hover:bg-green-100 transition-all text-sm">
                         Mentorado
                    </button>
                </div>
            </div>

            
            <div class="flex gap-3">
                <button type="submit"
                        class="flex-1 py-2.5 px-4 bg-blue-500 text-white font-medium rounded-xl hover:bg-blue-600 active:bg-blue-700 transition-colors shadow-sm text-sm">
                    Confirmar
                </button>
                <button type="button" @click="showAddModal = false; newPersonName = ''; newPersonType = 'professor';"
                        class="flex-1 py-2.5 px-4 border-2 border-gray-300 text-gray-700 font-medium rounded-xl hover:border-gray-400 hover:bg-gray-50 transition-colors text-sm">
                    Cancelar
                </button>
            </div>
        </form>
    </div>
</div>
<?php /**PATH C:\Users\Mateus Amaro\Documents\DevMenthors\Cronograma de Aulas\resources\views/schedule/modals/add-person.blade.php ENDPATH**/ ?>