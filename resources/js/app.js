import Alpine from 'alpinejs';
import Chart from 'chart.js/auto';
import { createApp } from 'vue';
import LoginExperience from './components/LoginExperience.vue';

window.Alpine = Alpine;

Alpine.data('numeroValidator', (config) => ({
    numeroIdentificacion: config.initialValue ?? '',
    numeroExiste: false,
    numeroMensaje: '',
    numeroChequeando: false,
    async validarNumero() {
        const numero = this.numeroIdentificacion.trim();

        if (!numero) {
            this.numeroExiste = false;
            this.numeroMensaje = '';
            return;
        }

        this.numeroChequeando = true;

        try {
            const url = new URL(config.checkUrl, window.location.origin);
            url.searchParams.set('numero', numero);
            if (config.ignoreId) {
                url.searchParams.set('ignore', config.ignoreId);
            }

            const response = await fetch(url.toString(), {
                headers: {
                    Accept: 'application/json',
                },
            });

            const data = await response.json();

            this.numeroExiste = Boolean(data.exists);
            this.numeroMensaje = data.message ?? '';
        } catch (error) {
            this.numeroExiste = false;
            this.numeroMensaje = 'No se pudo validar el número en este momento.';
        } finally {
            this.numeroChequeando = false;
        }
    },
    init() {
        if (this.numeroIdentificacion) {
            this.validarNumero();
        }
    },
}));

Alpine.data('votanteForm', (config) => ({
    numeroIdentificacion: config.initial?.numero_identificacion ?? '',
    departamento: config.initial?.departamento ?? '',
    municipio: config.initial?.municipio ?? '',
    puesto_votacion: config.initial?.puesto_votacion ?? '',
    comuna: config.initial?.comuna ?? '',
    direccion: config.initial?.direccion ?? '',
    submitting: false,
    submissionStage: 'preparando',
    numeroExiste: false,
    numeroMensaje: '',
    numeroChequeando: false,
    numeroValidacionTimer: null,
    suggestions: {
        departamento: [],
        municipio: [],
        puesto_votacion: [],
        comuna: [],
        direccion: [],
    },
    suggestionsVisible: {
        departamento: false,
        municipio: false,
        puesto_votacion: false,
        comuna: false,
        direccion: false,
    },
    suggestionsLoading: {
        departamento: false,
        municipio: false,
        puesto_votacion: false,
        comuna: false,
        direccion: false,
    },
    catalogCache: {
        departamento: [],
        municipio: [],
        puesto_votacion: [],
        comuna: [],
        direccion: [],
    },
    activeField: null,
    async validarNumero() {
        const numero = this.numeroIdentificacion.trim();

        if (!numero) {
            this.numeroExiste = false;
            this.numeroMensaje = '';
            return;
        }

        this.numeroChequeando = true;

        try {
            const url = new URL(config.checkUrl, window.location.origin);
            url.searchParams.set('numero', numero);
            if (config.ignoreId) {
                url.searchParams.set('ignore', config.ignoreId);
            }

            const response = await fetch(url.toString(), {
                headers: { Accept: 'application/json' },
            });
            const data = await response.json();

            this.numeroExiste = Boolean(data.exists);
            this.numeroMensaje = data.message ?? '';
        } catch (error) {
            this.numeroExiste = false;
            this.numeroMensaje = 'No se pudo validar el número en este momento.';
        } finally {
            this.numeroChequeando = false;
        }
    },
    programarValidacionNumero() {
        if (this.numeroValidacionTimer) {
            clearTimeout(this.numeroValidacionTimer);
        }

        this.numeroValidacionTimer = setTimeout(() => {
            void this.validarNumero();
        }, 350);
    },
    async buscarCatalogo(field, forceOpen = false) {
        this.activeField = field;
        const value = (this[field] ?? '').trim();
        this.suggestionsVisible[field] = true;
        this.suggestionsLoading[field] = true;

        const url = new URL(config.searchUrl, window.location.origin);
        url.searchParams.set('field', field);
        url.searchParams.set('term', value);

        if (field !== 'departamento' && this.departamento.trim()) {
            url.searchParams.set('departamento', this.departamento.trim());
        }

        if (field === 'puesto_votacion' || field === 'comuna' || field === 'direccion') {
            if (this.municipio.trim()) {
                url.searchParams.set('municipio', this.municipio.trim());
            }
        }

        try {
            const response = await fetch(url.toString(), {
                headers: { Accept: 'application/json' },
            });
            const data = await response.json();
            const items = Array.isArray(data.items) ? data.items : [];
            this.suggestions[field] = items;
            this.catalogCache[field] = items;
            this.suggestionsVisible[field] = forceOpen || this.activeField === field;
        } catch (error) {
            this.suggestions[field] = [];
            this.suggestionsVisible[field] = false;
        } finally {
            this.suggestionsLoading[field] = false;
        }
    },
    abrirCatalogo(field) {
        this.activeField = field;
        this.suggestionsVisible[field] = true;

        if (this.catalogCache[field]?.length) {
            this.suggestions[field] = this.catalogCache[field];
        }

        if (!this.suggestions[field].length) {
            this.suggestionsLoading[field] = true;
        }

        void this.buscarCatalogo(field, true);
    },
    selectValue(field, value) {
        this[field] = value;
        this.suggestions[field] = [];
        this.suggestionsVisible[field] = false;
        this.suggestionsLoading[field] = false;

        if (field === 'departamento') {
            this.municipio = '';
            this.puesto_votacion = '';
            this.comuna = '';
            this.direccion = '';
        }

        if (field === 'municipio') {
            this.puesto_votacion = '';
            this.comuna = '';
            this.direccion = '';
        }
    },
    closeSuggestions(field) {
        this.suggestions[field] = [];
        this.suggestionsVisible[field] = false;
        this.suggestionsLoading[field] = false;
    },
    startSubmit(event) {
        if (this.submitting) {
            return;
        }

        this.submitting = true;
        this.submissionStage = 'preparando';

        const form = event?.target;

        this._submissionTimers = [
            setTimeout(() => {
                this.submissionStage = 'validando';
            }, 280),
            setTimeout(() => {
                this.submissionStage = 'guardando';
                form?.submit();
            }, 750),
        ];
    },
    init() {
        this.$watch('numeroIdentificacion', () => {
            this.programarValidacionNumero();
        });

        if (this.numeroIdentificacion) {
            this.validarNumero();
        }

        const fields = ['departamento', 'municipio', 'puesto_votacion', 'comuna', 'direccion'];
        fields.forEach((field) => {
            void this.buscarCatalogo(field, false);
        });
    },
}));

Alpine.data('submissionFeedback', (config = {}) => ({
    submitting: false,
    submissionStage: 'preparando',
    selectedFileName: '',
    selectedFilePreview: '',
    errorMessage: '',
    targetName: config.name ?? 'el registro',
    maxFileSizeBytes: 12 * 1024 * 1024,
    _submissionTimers: [],
    clearSubmissionTimers() {
        (this._submissionTimers || []).forEach((timer) => clearTimeout(timer));
        this._submissionTimers = [];
    },
    get stageMessage() {
        if (this.submissionStage === 'error') {
            return 'Archivo no válido';
        }

        if (this.submissionStage === 'validando') {
            return 'Validando imagen...';
        }

        if (this.submissionStage === 'guardando') {
            return 'Guardando registro...';
        }

        return 'Preparando carga...';
    },
    isValidImageFile(file) {
        if (!file) {
            return false;
        }

        if (file.type && file.type.startsWith('image/')) {
            return true;
        }

        return /\.(png|jpe?g|gif|webp|bmp|heic|heif)$/i.test(file.name ?? '');
    },
    async prepareUpload(event) {
        const file = event?.target?.files?.[0];

        if (!file) {
            return;
        }

        this.clearSubmissionTimers();
        this.selectedFileName = file.name;
        this.selectedFilePreview = '';
        this.errorMessage = '';

        if (!this.isValidImageFile(file)) {
            this.submitting = true;
            this.submissionStage = 'error';
            this.errorMessage = 'El archivo debe ser una imagen. Selecciona un PNG, JPG, GIF, WEBP, BMP, HEIC o HEIF.';

            this._submissionTimers = [
                setTimeout(() => {
                    this.submitting = false;
                    this.submissionStage = 'preparando';
                    this.errorMessage = '';
                    this.selectedFileName = '';
                }, 4500),
            ];

            return;
        }

        if (file.size > this.maxFileSizeBytes) {
            this.submitting = true;
            this.submissionStage = 'error';
            this.errorMessage = 'La imagen supera el límite permitido de 12 MB. Comprime la foto o usa una versión más ligera.';

            this._submissionTimers = [
                setTimeout(() => {
                    this.submitting = false;
                    this.submissionStage = 'preparando';
                    this.errorMessage = '';
                    this.selectedFileName = '';
                }, 3500),
            ];

            return;
        }

        this.submitting = true;
        this.submissionStage = 'preparando';

        if (file.type && file.type.startsWith('image/')) {
            try {
            this.selectedFilePreview = await new Promise((resolve, reject) => {
                    const reader = new FileReader();
                    reader.onload = () => resolve(String(reader.result ?? ''));
                    reader.onerror = () => reject(new Error('No se pudo leer la imagen.'));
                    reader.readAsDataURL(file);
                });
            } catch (error) {
                this.selectedFilePreview = '';
            }
        }

        const form = event?.target?.closest('form');

        this._submissionTimers = [
            setTimeout(() => {
                this.submissionStage = 'validando';
            }, 280),
            setTimeout(() => {
                this.submissionStage = 'guardando';
                form?.submit();
            }, 750),
        ];
    },
    startSubmit(event) {
        if (this.submitting) {
            return;
        }

        this.clearSubmissionTimers();
        const form = event?.target;
        this.submitting = true;
        this.submissionStage = 'preparando';

        this._submissionTimers = [
            setTimeout(() => {
                this.submissionStage = 'validando';
            }, 280),
            setTimeout(() => {
                this.submissionStage = 'guardando';
                form?.submit();
            }, 750),
        ];
    },
}));


Alpine.data('dashboardStats', (config) => ({
    activeTab: config.initialTab ?? 'resumen',
    charts: [],
    init() {
        const render = () => this.renderCharts();

        this.$nextTick(() => {
            render();
            setTimeout(render, 0);
        });

        window.addEventListener('load', render, { once: true });
    },
    renderCharts() {
        this.destroyCharts();

        if (this.activeTab !== 'estadisticas') {
            return;
        }

        const departmentCanvas = this.$refs.departmentChart || document.getElementById('departmentChart');
        const municipalityCanvas = this.$refs.municipalityChart || document.getElementById('municipalityChart');
        const trendCanvas = this.$refs.trendChart || document.getElementById('trendChart');

        if (departmentCanvas && config.topDepartamentos?.length) {
            this.charts.push(new Chart(departmentCanvas, {
                type: 'doughnut',
                data: {
                    labels: config.topDepartamentos.map((item) => item.label),
                    datasets: [{
                        data: config.topDepartamentos.map((item) => item.total),
                        backgroundColor: ['#4f46e5', '#6366f1', '#818cf8', '#a5b4fc', '#c7d2fe', '#e0e7ff'],
                        borderWidth: 0,
                    }],
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                usePointStyle: true,
                                boxWidth: 8,
                                color: '#475569',
                            },
                        },
                    },
                    cutout: '68%',
                },
            }));
        }

        if (municipalityCanvas && config.topMunicipios?.length) {
            this.charts.push(new Chart(municipalityCanvas, {
                type: 'bar',
                data: {
                    labels: config.topMunicipios.map((item) => item.label),
                    datasets: [{
                        label: 'Votantes',
                        data: config.topMunicipios.map((item) => item.total),
                        backgroundColor: '#14b8a6',
                        borderRadius: 10,
                    }],
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                    },
                    scales: {
                        x: {
                            ticks: { color: '#64748b' },
                            grid: { display: false },
                        },
                        y: {
                            ticks: { color: '#64748b' },
                            grid: { color: 'rgba(148, 163, 184, 0.15)' },
                        },
                    },
                },
            }));
        }

        if (trendCanvas && config.dailyTrend?.length) {
            this.charts.push(new Chart(trendCanvas, {
                type: 'line',
                data: {
                    labels: config.dailyTrend.map((item) => item.label),
                    datasets: [{
                        label: 'Registros',
                        data: config.dailyTrend.map((item) => item.total),
                        borderColor: '#7c3aed',
                        backgroundColor: 'rgba(124, 58, 237, 0.12)',
                        fill: true,
                        tension: 0.35,
                        pointRadius: 3,
                        pointHoverRadius: 5,
                    }],
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                    },
                    scales: {
                        x: {
                            ticks: { color: '#64748b' },
                            grid: { display: false },
                        },
                        y: {
                            beginAtZero: true,
                            ticks: { color: '#64748b', precision: 0 },
                            grid: { color: 'rgba(148, 163, 184, 0.15)' },
                        },
                    },
                },
            }));
        }
    },
    destroyCharts() {
        this.charts.forEach((chart) => chart.destroy());
        this.charts = [];
    },
    activate(tab) {
        this.activeTab = tab;
        if (tab === 'estadisticas') {
            this.$nextTick(() => this.renderCharts());
        }
    },
}));

Alpine.data('novedadesPanel', (config) => ({
    open: false,
    loading: false,
    error: '',
    responsables: [],
    selected: null,
    detalle: [],
    meta: {
        total: 0,
        per_page: 10,
        current_page: 1,
        last_page: 1,
    },
    async openDetalle(responsable) {
        this.open = true;
        this.selected = responsable;
        await this.loadDetalle(1);
    },
    close() {
        this.open = false;
        this.loading = false;
        this.error = '';
        this.detalle = [];
        this.selected = null;
        this.meta = {
            total: 0,
            per_page: 10,
            current_page: 1,
            last_page: 1,
        };
    },
    async loadDetalle(page = 1) {
        if (!this.selected) {
            return;
        }

        this.loading = true;
        this.error = '';

        try {
            const endpoint = this.selected.id === 0 ? config.globalUrl : `${config.baseUrl}/${this.selected.id}/pendientes`;
            const url = new URL(endpoint, window.location.origin);
            url.searchParams.set('page', String(page));
            url.searchParams.set('per_page', '10');

            const response = await fetch(url.toString(), {
                headers: { Accept: 'application/json' },
            });

            if (!response.ok) {
                throw new Error('No se pudo cargar el detalle.');
            }

            const data = await response.json();
            this.selected = data.responsable ?? this.selected;
            this.detalle = Array.isArray(data.items) ? data.items : [];
            this.meta = data.meta ?? this.meta;
        } catch (error) {
            this.error = 'No se pudo cargar el detalle de pendientes.';
        } finally {
            this.loading = false;
        }
    },
    nextPage() {
        if (this.meta.current_page < this.meta.last_page) {
            void this.loadDetalle(this.meta.current_page + 1);
        }
    },
    prevPage() {
        if (this.meta.current_page > 1) {
            void this.loadDetalle(this.meta.current_page - 1);
        }
    },
}));

Alpine.data('toastStack', (config = {}) => ({
    toasts: Array.isArray(config.initialToasts)
        ? config.initialToasts.map((toast) => ({ ...toast }))
        : [],
    init() {
        this.toasts.forEach((toast, index) => {
            setTimeout(() => {
                this.dismiss(toast.id);
            }, 12000 + (index * 1800));
        });
    },
    dismiss(id) {
        this.toasts = this.toasts.filter((toast) => toast.id !== id);
    },
    toneClasses(tone) {
        if (tone === 'success') {
            return {
                panel: 'border-emerald-200 bg-emerald-50 text-emerald-950 shadow-emerald-950/10 dark:border-emerald-500/30 dark:bg-emerald-500/10 dark:text-emerald-50',
                badge: 'bg-emerald-100 text-emerald-700 dark:bg-emerald-500/15 dark:text-emerald-200',
                icon: 'text-emerald-600',
            };
        }

        if (tone === 'danger') {
            return {
                panel: 'border-red-200 bg-red-50 text-red-950 shadow-red-950/10 dark:border-red-500/30 dark:bg-red-500/10 dark:text-red-50',
                badge: 'bg-red-100 text-red-700 dark:bg-red-500/15 dark:text-red-200',
                icon: 'text-red-600',
            };
        }

        return {
            panel: 'border-amber-200 bg-amber-50 text-amber-950 shadow-amber-950/10 dark:border-amber-500/30 dark:bg-amber-500/10 dark:text-amber-50',
            badge: 'bg-amber-100 text-amber-700 dark:bg-amber-500/15 dark:text-amber-200',
            icon: 'text-amber-600',
        };
    },
}));
const loginApp = document.getElementById('login-app');

if (loginApp) {
    const props = JSON.parse(loginApp.dataset.props ?? '{}');
    const fallback = document.getElementById('login-fallback');

    if (fallback) {
        fallback.classList.add('hidden');
    }

    loginApp.classList.remove('hidden');
    createApp(LoginExperience, props).mount(loginApp);
}

Alpine.start();



