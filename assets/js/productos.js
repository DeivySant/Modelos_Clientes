let modoEdicion = false;
let claveProducto = '';
let claveEliminar = '';
let nombreEliminar = '';
const API_URL = '/clientes/api/productos_api.php';

function mostrarNotificacion(tipo, titulo, mensaje) {
  const overlay = document.getElementById('notificationOverlay');
  const icon = document.getElementById('notificationIcon');
  const svg = document.getElementById('notificationSvg');
  const titleEl = document.getElementById('notificationTitle');
  const messageEl = document.getElementById('notificationMessage');

  icon.className = 'notification-icon ' + tipo;
  if (tipo === 'success') {
    svg.innerHTML = '<polyline points="20 6 9 17 4 12"></polyline>';
  } else {
    svg.innerHTML = '<line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line>';
  }

  titleEl.textContent = titulo;
  messageEl.textContent = mensaje;
  overlay.classList.add('active');
}

function cerrarNotificacion() {
  document.getElementById('notificationOverlay').classList.remove('active');
  location.reload();
}

function abrirModal() {
  modoEdicion = false;
  claveProducto = '';
  document.getElementById('modalTitulo').textContent = 'Agregar Producto';
  document.getElementById('inp_nombre').value = '';
  document.getElementById('inp_descripcion').value = '';
  document.getElementById('inp_valor').value = '';
  document.getElementById('overlay').classList.add('active');
}

function abrirEditar(clave, nombre, descripcion, valor) {
  modoEdicion = true;
  claveProducto = clave;
  document.getElementById('modalTitulo').textContent = 'Editar Producto';
  document.getElementById('inp_nombre').value = nombre;
  document.getElementById('inp_descripcion').value = descripcion;
  document.getElementById('inp_valor').value = valor;
  document.getElementById('overlay').classList.add('active');
}

function cerrarModal() {
  document.getElementById('overlay').classList.remove('active');
}

function guardar() {
  const nombre = document.getElementById('inp_nombre').value.trim();
  const descripcion = document.getElementById('inp_descripcion').value.trim();
  const valor = document.getElementById('inp_valor').value.trim();

  if (!nombre || !descripcion || !valor) {
    mostrarNotificacion('error', 'Campos incompletos', 'Por favor completa todos los campos.');
    return;
  }

  const datos = new FormData();
  datos.append('nombre', nombre);
  datos.append('descripcion', descripcion);
  datos.append('valor', valor);

  if (modoEdicion) {
    datos.append('action', 'update');
    datos.append('old_nombre', claveProducto);
  } else {
    datos.append('action', 'insert');
  }

  cerrarModal();

  fetch(API_URL, { method: 'POST', body: datos })
    .then((r) => r.json())
    .then((res) => {
      if (res.success) {
        mostrarNotificacion('success', modoEdicion ? 'Actualizado' : 'Guardado', res.message);
      } else {
        mostrarNotificacion('error', 'Error', res.error || 'Ocurrio un error.');
      }
    })
    .catch(() => {
      mostrarNotificacion('error', 'Error de conexion', 'No se pudo conectar con el servidor.');
    });
}

function abrirEliminar(clave, nombre) {
  claveEliminar = clave;
  nombreEliminar = nombre;
  document.getElementById('deleteProductName').textContent = nombre;
  document.getElementById('deleteOverlay').classList.add('active');
}

function cerrarEliminar() {
  document.getElementById('deleteOverlay').classList.remove('active');
  claveEliminar = '';
  nombreEliminar = '';
}

function confirmarEliminar() {
  if (!claveEliminar) return;

  const datos = new FormData();
  datos.append('action', 'delete');
  datos.append('old_nombre', claveEliminar);

  cerrarEliminar();

  fetch(API_URL, { method: 'POST', body: datos })
    .then((r) => r.json())
    .then((res) => {
      if (res.success) {
        mostrarNotificacion('success', 'Eliminado', res.message);
      } else {
        mostrarNotificacion('error', 'Error', res.error || 'Ocurrio un error al eliminar ' + nombreEliminar + '.');
      }
    })
    .catch(() => {
      mostrarNotificacion('error', 'Error de conexion', 'No se pudo conectar con el servidor.');
    });
}

const overlayEl = document.getElementById('overlay');
if (overlayEl) {
  overlayEl.addEventListener('click', function (e) {
    if (e.target === this) cerrarModal();
  });
}

const deleteOverlayEl = document.getElementById('deleteOverlay');
if (deleteOverlayEl) {
  deleteOverlayEl.addEventListener('click', function (e) {
    if (e.target === this) cerrarEliminar();
  });
}


window.abrirModal = abrirModal;
window.abrirEditar = abrirEditar;
window.abrirEliminar = abrirEliminar;
window.cerrarModal = cerrarModal;
window.cerrarEliminar = cerrarEliminar;
window.confirmarEliminar = confirmarEliminar;
window.guardar = guardar;
window.cerrarNotificacion = cerrarNotificacion;

