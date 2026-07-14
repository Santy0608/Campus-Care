 /**
 * lineas_apoyo.js
 * Carga las líneas de apoyo desde el backend y las renderiza dinámicamente.
 *
 */

document.addEventListener('DOMContentLoaded', async () => {
    await cargarLineasApoyo();

    const usuario = getUsuarioSesion();
    if (usuario?.role === 'ADMIN') {
        mostrarPanelAdmin();
    }
});

//GET /api/lineas-apoyo/listado-lineas-apoyo
async function cargarLineasApoyo() {
    const contenedor = document.getElementById('contenedor-lineas');
    const spinner    = document.getElementById('spinner-lineas');

    try {
        const resp = await fetch('/api/lineas-apoyo/listado-lineas-apoyo');

        if (!resp.ok) throw new Error('Error al obtener líneas de apoyo.');

        const lineas = await resp.json();

        if (spinner) spinner.style.display = 'none';

        if (!lineas.length) {
            contenedor.innerHTML = `
                <div class="col-12 text-center text-muted">
                    No hay líneas de apoyo disponibles en este momento.
                </div>`;
            return;
        }

        contenedor.innerHTML = lineas.map(linea => crearTarjeta(linea)).join('');

    } catch (err) {
        console.error('Error al cargar líneas de apoyo:', err);
        if (spinner) spinner.style.display = 'none';
        contenedor.innerHTML = `
            <div class="col-12">
                <div class="alert alert-danger">
                    No se pudo cargar la información. Intenta de nuevo más tarde.
                </div>
            </div>`;
    }
}

// GET /api/lineas-apoyo/{id} 

async function obtenerLineaApoyo(id) {
    const resp = await fetch(`/api/lineas-apoyo/${id}`, {
        headers: { 'Authorization': `Bearer ${sessionStorage.getItem('token')}` }
    });

    if (!resp.ok) throw new Error(`No se pudo obtener la línea de apoyo con id ${id}.`);

    return await resp.json();
}

//  POST /api/líneas-apoyo/agregar-linea-apoyo

async function agregarLineaApoyo(datos) {

    const resp = await fetch('/api/líneas-apoyo/agregar-linea-apoyo', {
        method:  'POST',
        headers: {
            'Content-Type':  'application/json',
            'Authorization': `Bearer ${sessionStorage.getItem('token')}`
        },
        body: JSON.stringify(datos)
    });

    if (!resp.ok) throw new Error('Error al agregar la línea de apoyo.');

    return await resp.json();
}

// PUT /api/líneas-apoyo/actualizar-linea-apoyo/{id} 

async function actualizarLineaApoyo(id, datos) {

    const resp = await fetch(`/api/líneas-apoyo/actualizar-linea-apoyo/${id}`, {
        method:  'PUT',
        headers: {
            'Content-Type':  'application/json',
            'Authorization': `Bearer ${sessionStorage.getItem('token')}`
        },
        body: JSON.stringify(datos)
    });

    if (!resp.ok) throw new Error(`Error al actualizar la línea de apoyo con id ${id}.`);

    return await resp.json();
}

// DELETE /api/líneas-apoyo/eliminar-linea-apoyo/{id} 

async function eliminarLineaApoyo(id) {
    // ⚠ URL con tilde en "líneas" — así está en el backend según el PDF
    const resp = await fetch(`/api/líneas-apoyo/eliminar-linea-apoyo/${id}`, {
        method:  'DELETE',
        headers: { 'Authorization': `Bearer ${sessionStorage.getItem('token')}` }
    });

    if (!resp.ok) throw new Error(`Error al eliminar la línea de apoyo con id ${id}.`);

    // Recargar la lista tras eliminar
    await cargarLineasApoyo();
}

// Panel Admin (solo visible si role === 'ADMIN') ───────────────────────────

function mostrarPanelAdmin() {
    const panel = document.getElementById('panel-admin');
    if (panel) panel.classList.remove('d-none');

    const formAgregar = document.getElementById('form-agregar-linea');
    if (formAgregar) {
        formAgregar.addEventListener('submit', async (e) => {
            e.preventDefault();
            const msgArea = document.getElementById('mensaje-admin');

            try {
                await agregarLineaApoyo({
                    nombre:      formAgregar.nombre.value.trim(),
                    telefono:    formAgregar.telefono.value.trim(),
                    descripcion: formAgregar.descripcion.value.trim(),
                    disponible:  formAgregar.disponible.value.trim()
                });

                mostrarMensaje(msgArea, 'success', 'Línea de apoyo agregada correctamente.');
                formAgregar.reset();
                await cargarLineasApoyo();

            } catch (err) {
                console.error(err);
                mostrarMensaje(msgArea, 'danger', 'Error al agregar la línea de apoyo.');
            }
        });
    }
}

//  Helpers de UI 

function crearTarjeta(linea) {
    const nombre      = escapeHtml(linea.nombre      ?? 'Sin nombre');
    const telefono    = escapeHtml(linea.telefono     ?? '');
    const descripcion = escapeHtml(linea.descripcion  ?? '');
    const disponible  = escapeHtml(linea.disponible   ?? '');
    const icono       = linea.icono ?? 'fas fa-headset';
    const color       = linea.color ?? 'primary';

    const usuario      = getUsuarioSesion();
    const botonesAdmin = usuario?.role === 'ADMIN'
        ? `<div class="mt-3 d-flex gap-2 justify-content-center">
               <button class="btn btn-outline-warning btn-sm"
                       onclick="prepararEdicion(${linea.id})">
                   <i class="fas fa-edit me-1"></i>Editar
               </button>
               <button class="btn btn-outline-danger btn-sm"
                       onclick="confirmarEliminacion(${linea.id}, '${nombre}')">
                   <i class="fas fa-trash me-1"></i>Eliminar
               </button>
           </div>`
        : '';

    return `
        <div class="col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body p-4 text-center">
                    <i class="${icono} fa-2x text-${color} mb-3"></i>
                    <h5 class="card-title">${nombre}</h5>
                    ${telefono    ? `<h4 class="text-primary mb-2">${telefono}</h4>` : ''}
                    ${descripcion ? `<p class="text-muted mb-1">${descripcion}</p>` : ''}
                    ${disponible  ? `<small class="text-muted"><i class="fas fa-clock me-1"></i>${disponible}</small>` : ''}
                    ${telefono    ? `<div class="mt-3">
                                        <a href="tel:${telefono}" class="btn btn-outline-${color} btn-sm">
                                            <i class="fas fa-phone me-1"></i>Llamar ahora
                                        </a>
                                    </div>` : ''}
                    ${botonesAdmin}
                </div>
            </div>
        </div>`;
}

async function prepararEdicion(id) {
    try {
        const linea   = await obtenerLineaApoyo(id);
        const nombre  = prompt('Nombre:', linea.nombre ?? '');
        if (nombre === null) return;
        const telefono    = prompt('Teléfono:',    linea.telefono    ?? '');
        const descripcion = prompt('Descripción:', linea.descripcion ?? '');
        const disponible  = prompt('Disponibilidad:', linea.disponible ?? '');

        await actualizarLineaApoyo(id, { nombre, telefono, descripcion, disponible });
        await cargarLineasApoyo();

    } catch (err) {
        console.error(err);
        alert('Error al editar la línea de apoyo.');
    }
}

function confirmarEliminacion(id, nombre) {
    if (!confirm(`¿Eliminar la línea de apoyo "${nombre}"?`)) return;
    eliminarLineaApoyo(id).catch(() => alert('Error al eliminar.'));
}

function mostrarMensaje(container, tipo, texto) {
    if (!container) return;
    container.innerHTML = `<div class="alert alert-${tipo}">${escapeHtml(texto)}</div>`;
}

function getUsuarioSesion() {
    const raw = sessionStorage.getItem('usuario');
    if (!raw) return null;
    try { return JSON.parse(raw); } catch { return null; }
}

function escapeHtml(str) {
    const div = document.createElement('div');
    div.appendChild(document.createTextNode(String(str)));
    return div.innerHTML;
}