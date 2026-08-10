document.addEventListener('DOMContentLoaded', () => {
    const api = window.CampusCareApi;
    
    const ui = {
        formulario: document.getElementById('formFiltros'),
        categoria: document.getElementById('filtroCategoria'),
        buscar: document.getElementById('campoBuscar'),
        limpiar: document.getElementById('botonLimpiar'),
        contenedor: document.getElementById('recursosContenedor'),
    };

    const estado = {
        recursos: [],
        categoria: '',
        termino: '',
    };

    const iconos = new Map([
        ['estres', '😫'],
        ['ansiedad', '😰'],
        ['sueno', '😴'],
        ['estado de animo', '🙂'],
        ['relaciones', '💬'],
        ['motivacion', '🔥'],
    ]);

    ui.formulario.addEventListener('submit', (event) => {
        event.preventDefault();
        actualizarFiltros();
    });
    ui.categoria.addEventListener('change', actualizarFiltros);
    ui.buscar.addEventListener('search', actualizarFiltros);
    ui.limpiar.addEventListener('click', limpiarFiltros);

    async function cargarRecursos() {
        mostrarEstado('Cargando recursos...', 'info');

        try {
            const respuesta = await api.request('/api/recursos/listado-recursos');
            estado.recursos = (Array.isArray(respuesta) ? respuesta : [])
                .filter((recurso) => recurso.activo !== false);
            cargarCategorias();
            renderizar();
        } catch (error) {
            mostrarEstado(
                error.message || 'No se pudieron cargar los recursos.',
                'danger',
            );
        }
    }

    function actualizarFiltros() {
        estado.categoria = ui.categoria.value;
        estado.termino = normalizar(ui.buscar.value.trim());
        renderizar();
    }

    function limpiarFiltros() {
        ui.categoria.value = '';
        ui.buscar.value = '';
        estado.categoria = '';
        estado.termino = '';
        renderizar();
        ui.buscar.focus();
    }

    function normalizar(valor) {
        return String(valor ?? '')
            .normalize('NFD')
            .replace(/[\u0300-\u036f]/g, '')
            .replace(/_/g, ' ')
            .toLocaleLowerCase('es')
            .trim();
    }

    function categoriaDe(recurso) {
        return String(recurso.categoriaNombre ?? recurso.categoria ?? 'Sin categoría');
    }

    function tipoDe(recurso) {
        return String(recurso.tipoRecursoNombre ?? recurso.tipo ?? '');
    }

    function cargarCategorias() {
        const categorias = [...new Set(estado.recursos.map(categoriaDe))]
            .sort((a, b) => a.localeCompare(b, 'es'));

        ui.categoria.replaceChildren(new Option('Todas las categorías', ''));
        categorias.forEach((categoria) => {
            ui.categoria.add(new Option(categoria, categoria));
        });
    }

    function filtrarRecursos() {
        return estado.recursos.filter((recurso) => {
            const coincideCategoria = !estado.categoria
                || categoriaDe(recurso) === estado.categoria;
            const texto = normalizar([
                recurso.titulo,
                tipoDe(recurso),
                recurso.contenido,
            ].join(' '));

            return coincideCategoria && (!estado.termino || texto.includes(estado.termino));
        });
    }

    function agruparPorCategoria(recursos) {
        return recursos.reduce((grupos, recurso) => {
            const categoria = categoriaDe(recurso);
            if (!grupos.has(categoria)) grupos.set(categoria, []);
            grupos.get(categoria).push(recurso);
            return grupos;
        }, new Map());
    }

    function urlSegura(valor) {
        if (!valor) return null;
        try {
            const url = new URL(valor, window.location.origin);
            return ['http:', 'https:'].includes(url.protocol) ? url.href : null;
        } catch {
            return null;
        }
    }

    function crearTarjeta(recurso) {
        const columna = document.createElement('div');
        columna.className = 'col-md-6 col-lg-4';

        const tarjeta = document.createElement('article');
        tarjeta.className = 'card h-100 shadow-sm border-0 rounded-3 card-hover';

        const cuerpo = document.createElement('div');
        cuerpo.className = 'card-body p-4';

        const titulo = document.createElement('h3');
        titulo.className = 'h5 card-title fw-bold d-flex justify-content-between align-items-center';
        titulo.append(document.createTextNode(recurso.titulo ?? 'Recurso'));

        const icono = document.createElement('span');
        icono.className = 'icono-categoria';
        icono.setAttribute('aria-hidden', 'true');
        icono.textContent = iconos.get(normalizar(categoriaDe(recurso))) ?? '💤';
        titulo.append(icono);

        const tipo = document.createElement('p');
        tipo.className = 'card-subtitle mb-2 text-muted';
        tipo.textContent = `Tipo: ${tipoDe(recurso) || 'No especificado'}`;

        const descripcion = document.createElement('p');
        descripcion.className = 'card-text mt-2';
        const contenido = String(recurso.contenido ?? '');
        descripcion.textContent = contenido.length > 120
            ? `${contenido.slice(0, 120)}…`
            : contenido;

        cuerpo.append(titulo, tipo, descripcion);
        tarjeta.append(cuerpo);

        const url = urlSegura(recurso.urlEnlace ?? recurso.url_enlace);
        if (url) {
            const pie = document.createElement('div');
            pie.className = 'card-footer bg-transparent border-0 px-4 pb-4';
            const enlace = document.createElement('a');
            enlace.className = 'btn btn-primary w-100 rounded-pill';
            enlace.href = url;
            enlace.target = '_blank';
            enlace.rel = 'noopener noreferrer';
            enlace.textContent = 'Ver más';
            pie.append(enlace);
            tarjeta.append(pie);
        }

        columna.append(tarjeta);
        return columna;
    }

    function renderizar() {
        const recursos = filtrarRecursos();
        const filtrosActivos = Boolean(estado.categoria || estado.termino);
        ui.limpiar.classList.toggle('d-none', !filtrosActivos);

        if (!recursos.length) {
            mostrarEstado('No hay recursos disponibles para los filtros seleccionados.', 'warning');
            return;
        }

        const fragmento = document.createDocumentFragment();
        agruparPorCategoria(recursos).forEach((items, categoria) => {
            const titulo = document.createElement('h2');
            titulo.className = 'mt-5 mb-3 border-bottom pb-2';
            titulo.textContent = categoria;

            const fila = document.createElement('div');
            fila.className = 'row g-4';
            fila.append(...items.map(crearTarjeta));
            fragmento.append(titulo, fila);
        });
        ui.contenedor.replaceChildren(fragmento);
    }

    function mostrarEstado(texto, tipo) {
        const alerta = document.createElement('div');
        alerta.className = `alert alert-${tipo} text-center`;
        alerta.textContent = texto;
        ui.contenedor.replaceChildren(alerta);
    }

    cargarRecursos();
});
