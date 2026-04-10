document.addEventListener('alpine:init', () => {
    Alpine.data('scheduleBoard', (config = {}) => ({
        activeTab: 'schedule',
        currentMonth: config.initialMonth || new Date().getMonth() + 1,
        currentYear: config.initialYear || new Date().getFullYear(),
        showAddModal: false,
        showRemoveModal: false,
        removeFilter: '',
        draggedPersonId: null,
        draggedPersonType: null,
        toastMessage: '',
        toastType: 'success',
        showToast: false,
        newPersonName: '',
        newPersonType: 'professor',

        init() {
            this.showAddModal = false;
            this.showRemoveModal = false;
            this.activeTab = 'schedule';
            this.removeFilter = '';
            // Ensure month/year are integers
            this.currentMonth = parseInt(this.currentMonth, 10);
            this.currentYear = parseInt(this.currentYear, 10);
        },

        // ─────────────────────────────────────────────
        // MONTH NAVIGATION
        // ─────────────────────────────────────────────

        getCurrentMonthName() {
            const months = [
                'janeiro', 'fevereiro', 'março', 'abril', 'maio', 'junho',
                'julho', 'agosto', 'setembro', 'outubro', 'novembro', 'dezembro'
            ];
            const m = parseInt(this.currentMonth, 10);
            const y = parseInt(this.currentYear, 10);
            const monthName = months[m - 1] || 'janeiro';
            return monthName.charAt(0).toUpperCase() + monthName.slice(1) + ' ' + y;
        },

        changeMonth(direction) {
            let month = parseInt(this.currentMonth, 10) + direction;
            let year = parseInt(this.currentYear, 10);

            if (month > 12) {
                month = 1;
                year++;
            } else if (month < 1) {
                month = 12;
                year--;
            }

            // Navigate — the schedule data is persisted server-side per month
            location.href = `/?month=${month}&year=${year}`;
        },

        // ─────────────────────────────────────────────
        // PASSWORD PROTECTION / CONFIRMATION MODAL
        // ─────────────────────────────────────────────
        showPasswordModal: false,
        passwordInput: '',
        passwordAction: null, // 'generate' or 'clear'
        passwordModalTitle: 'Acesso Restrito',
        passwordModalDescription: '',

        requestPassword(action) {
            this.passwordAction = action;
            this.passwordInput = '';
            
            if (action === 'generate') {
                this.passwordModalTitle = 'Gerar Novo Rodízio';
                this.passwordModalDescription = 'Tem certeza que deseja <b>gerar um novo rodízio</b>?<br><br>Isto substituirá os professores atuais de todo o mês. Para prosseguir, insira a senha de admnistrador:';
            } else if (action === 'clear') {
                this.passwordModalTitle = 'Limpar Cronograma';
                this.passwordModalDescription = 'Tem certeza que deseja <b>limpar o cronograma</b>?<br><br>Todas as escalas, suplentes e aulas deste mês serão apagadas. Para prosseguir, insira a senha:';
            }

            this.showPasswordModal = true;
        },

        submitPassword() {
            if (this.passwordInput === 'DevMenthors2026') {
                this.showPasswordModal = false;
                this.showNotification('Senha correta. Ação autorizada!', 'success');
                
                // Aguarda um instante para o aviso sumir ou fluir melhor
                setTimeout(() => {
                    if (this.passwordAction === 'generate') {
                        this.executeGenerateSchedule();
                    } else if (this.passwordAction === 'clear') {
                        this.executeClearSchedule();
                    }
                }, 500);
            } else {
                this.showNotification('Senha incorreta', 'error');
            }
        },
        
        // ─────────────────────────────────────────────
        // SCHEDULE GENERATION & CLEARING
        // ─────────────────────────────────────────────

        generateSchedule() {
            this.requestPassword('generate');
        },

        async executeGenerateSchedule() {
            try {
                this.showNotification('Gerando rodízio...', 'info');
                const response = await this._fetch('/schedule/generate', {
                    month: parseInt(this.currentMonth, 10),
                    year: parseInt(this.currentYear, 10),
                });

                const data = await response.json();

                if (data.success) {
                    this.showNotification(data.message || 'Rodízio gerado com sucesso!', 'success');
                    setTimeout(() => location.reload(), 1000);
                } else {
                    this.showNotification(data.errors?.[0] || 'Erro ao gerar rodízio', 'error');
                }
            } catch (error) {
                console.error('Error:', error);
                this.showNotification('Erro ao gerar rodízio', 'error');
            }
        },

        clearSchedule() {
            this.requestPassword('clear');
        },

        async executeClearSchedule() {
            try {
                this.showNotification('Limpando...', 'info');
                const response = await this._fetch('/schedule/clear', {
                    month: parseInt(this.currentMonth, 10),
                    year: parseInt(this.currentYear, 10),
                });

                if (response.ok) {
                    this.showNotification('Cronograma limpo', 'info');
                    setTimeout(() => location.reload(), 800);
                }
            } catch (error) {
                console.error('Error:', error);
                this.showNotification('Erro ao limpar cronograma', 'error');
            }
        },

        // ─────────────────────────────────────────────
        // PEOPLE MANAGEMENT
        // ─────────────────────────────────────────────

        async addPerson() {
            if (!this.newPersonName.trim()) {
                this.showNotification('Digite um nome', 'error');
                return;
            }

            try {
                const response = await this._fetch('/people', {
                    name: this.newPersonName.trim(),
                    type: this.newPersonType,
                });

                const data = await response.json();

                if (data.success) {
                    this.showAddModal = false;
                    this.newPersonName = '';
                    this.newPersonType = 'professor';
                    this.showNotification(data.message || 'Pessoa adicionada com sucesso!', 'success');
                    setTimeout(() => location.reload(), 500);
                } else {
                    this.showNotification(data.message || 'Erro ao adicionar pessoa', 'error');
                }
            } catch (error) {
                console.error('Error:', error);
                this.showNotification('Erro ao adicionar pessoa', 'error');
            }
        },

        async removePerson(personId, personName) {
            if (!confirm(`Tem certeza que deseja remover ${personName}?`)) return;

            try {
                const response = await fetch(`/people/${personId}`, {
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': this._csrfToken(),
                    },
                });

                if (response.ok) {
                    this.showRemoveModal = false;
                    this.removeFilter = '';
                    this.showNotification('Pessoa removida com sucesso!', 'success');
                    setTimeout(() => location.reload(), 500);
                } else {
                    this.showNotification('Erro ao remover pessoa', 'error');
                }
            } catch (error) {
                console.error('Error:', error);
                this.showNotification('Erro ao remover pessoa', 'error');
            }
        },

        // ─────────────────────────────────────────────
        // SCHEDULE ASSIGNMENT (DRAG & DROP)
        // ─────────────────────────────────────────────

        async assignPerson(personId, day, classId) {
            if (!personId) return;

            try {
                const response = await this._fetch('/schedule/assign', {
                    person_id: personId,
                    day: day,
                    class_id: classId,
                    month: parseInt(this.currentMonth, 10),
                    year: parseInt(this.currentYear, 10),
                });

                const data = await response.json();

                if (!data.valid) {
                    this.showNotification(data.reason, 'error');
                } else {
                    this.showNotification('Pessoa alocada com sucesso!', 'success');
                    setTimeout(() => location.reload(), 500);
                }
            } catch (error) {
                console.error('Error:', error);
                this.showNotification('Erro ao alocar pessoa', 'error');
            }
        },

        async removeEntry(entryId) {
            if (!confirm('Deseja remover esta pessoa do cronograma?')) return;

            try {
                const response = await this._fetch('/schedule/remove', {
                    entry_id: entryId,
                });

                if (response.ok) {
                    this.showNotification('Removido com sucesso', 'success');
                    setTimeout(() => location.reload(), 500);
                }
            } catch (error) {
                console.error('Error:', error);
                this.showNotification('Erro ao remover', 'error');
            }
        },

        // ─────────────────────────────────────────────
        // LESSON NAME (per cell: schedule + class + day)
        // ─────────────────────────────────────────────

        _lessonDebounce: null,

        updateLessonName(scheduleId, classId, dayIndex, lessonName) {
            // Debounce to avoid excessive requests while typing
            if (this._lessonDebounce) clearTimeout(this._lessonDebounce);
            this._lessonDebounce = setTimeout(async () => {
                try {
                    await this._fetch('/schedule/lesson-name', {
                        schedule_id: scheduleId,
                        class_id: classId,
                        day_index: dayIndex,
                        lesson_name: lessonName,
                    });
                } catch (error) {
                    console.error('Error updating lesson name:', error);
                }
            }, 500);
        },

        // ─────────────────────────────────────────────
        // SUBSTITUTES
        // ─────────────────────────────────────────────

        async assignSubstitute(personId, day, classId) {
            if (!personId) return;

            try {
                const response = await this._fetch('/substitutes/assign', {
                    person_id: personId,
                    day: day,
                    class_id: classId,
                    month: parseInt(this.currentMonth, 10),
                    year: parseInt(this.currentYear, 10),
                });

                const data = await response.json();

                if (!data.valid) {
                    this.showNotification(data.reason, 'error');
                } else {
                    this.showNotification('Suplente alocado com sucesso!', 'success');
                    setTimeout(() => location.reload(), 500);
                }
            } catch (error) {
                console.error('Error:', error);
                this.showNotification('Erro ao alocar suplente', 'error');
            }
        },

        async removeSubstitute(substituteId) {
            if (!confirm('Deseja remover este suplente?')) return;

            try {
                const response = await this._fetch('/substitutes/remove', {
                    substitute_id: substituteId,
                });

                if (response.ok) {
                    this.showNotification('Suplente removido com sucesso', 'success');
                    setTimeout(() => location.reload(), 500);
                }
            } catch (error) {
                console.error('Error:', error);
                this.showNotification('Erro ao remover suplente', 'error');
            }
        },

        // ─────────────────────────────────────────────
        // NOTIFICATIONS
        // ─────────────────────────────────────────────

        showNotification(message, type = 'success') {
            this.toastMessage = message;
            this.toastType = type;
            this.showToast = true;
            setTimeout(() => { this.showToast = false; }, 4000);
        },

        // ─────────────────────────────────────────────
        // HELPERS
        // ─────────────────────────────────────────────

        _csrfToken() {
            return document.querySelector('meta[name="csrf-token"]')?.content || '';
        },

        async _fetch(url, body = {}) {
            return fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': this._csrfToken(),
                },
                body: JSON.stringify(body),
            });
        },
    }));
});
