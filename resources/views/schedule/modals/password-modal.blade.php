{{-- PASSWORD MODAL --}}
<div x-show="showPasswordModal" 
     x-transition.opacity 
     class="fixed inset-0 z-50 flex items-center justify-center bg-gray-900/40 backdrop-blur-sm"
     x-cloak>
    <div x-show="showPasswordModal"
         x-transition.scale.origin.bottom
         @click.away="showPasswordModal = false"
         class="bg-white rounded-2xl shadow-xl w-full max-w-sm overflow-hidden transform transition-all">
        
        <div class="px-6 py-5 border-b border-gray-100 flex justify-between items-center bg-gray-50/50">
            <h3 class="text-sm font-semibold text-gray-900 flex items-center gap-2">
                <svg class="w-4 h-4 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                </svg>
                <span x-text="passwordModalTitle">Acesso Restrito</span>
            </h3>
            <button @click="showPasswordModal = false" class="text-gray-400 hover:text-gray-600 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>

        <div class="p-6">
            <p class="text-sm text-gray-600 mb-5" x-html="passwordModalDescription">
                Por favor, insira a senha de administrador para continuar com a ação.
            </p>
            
            <form @submit.prevent="submitPassword">
                <div class="mb-5">
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Senha</label>
                    <input type="password" 
                           x-model="passwordInput"
                           placeholder="Digite a senha..."
                           x-ref="pwdInput"
                           x-init="$watch('showPasswordModal', value => { if (value) setTimeout(() => $refs.pwdInput.focus(), 100) })"
                           class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/50 focus:border-blue-500 transition-all">
                </div>

                <div class="flex gap-2">
                    <button type="button" @click="showPasswordModal = false"
                            class="flex-1 px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-xl transition-all">
                        Cancelar
                    </button>
                    <button type="submit"
                            class="flex-1 px-4 py-2 text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 rounded-xl transition-all shadow-sm">
                        Confirmar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
