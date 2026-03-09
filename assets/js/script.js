  let modoEdicion = false;
  let identificacionOriginal = "";
  const API_URL = "/clientes/api/api.php";

  // ═══════════════════════════════════════════════════════════
  // MOSTRAR NOTIFICACIÓN
  // ═══════════════════════════════════════════════════════════
  function mostrarNotificacion(tipo, titulo, mensaje) {
    const overlay = document.getElementById("notificationOverlay");
    const icon = document.getElementById("notificationIcon");
    const svg = document.getElementById("notificationSvg");
    const titleEl = document.getElementById("notificationTitle");
    const messageEl = document.getElementById("notificationMessage");

    icon.className = "notification-icon " + tipo;

    if (tipo === "success") {
      svg.innerHTML = '<polyline points="20 6 9 17 4 12"></polyline>';
    } else {
      svg.innerHTML = '<line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line>';
    }

    titleEl.textContent = titulo;
    messageEl.textContent = mensaje;
    overlay.classList.add("active");
  }

  function cerrarNotificacion() {
    document.getElementById("notificationOverlay").classList.remove("active");
    location.reload();
  }

  // ═══════════════════════════════════════════════════════════
  // ABRIR MODAL PARA AGREGAR
  // ═══════════════════════════════════════════════════════════
  function abrirModal() {
    modoEdicion = false;
    identificacionOriginal = "";
    document.getElementById("modalTitulo").textContent = "Agregar Cliente";
    document.getElementById("inp_id").value = "";
    document.getElementById("inp_nombre").value = "";
    document.getElementById("inp_correo").value = "";
    document.getElementById("overlay").classList.add("active");
  }

  // ═══════════════════════════════════════════════════════════
  // ABRIR MODAL PARA EDITAR
  // ═══════════════════════════════════════════════════════════
  function abrirEditar(id, nombre, correo) {
    modoEdicion = true;
    identificacionOriginal = id;
    document.getElementById("modalTitulo").textContent = "Editar Cliente";
    document.getElementById("inp_id").value = id;
    document.getElementById("inp_nombre").value = nombre;
    document.getElementById("inp_correo").value = correo;
    document.getElementById("overlay").classList.add("active");
  }

  // ═══════════════════════════════════════════════════════════
  // CERRAR MODAL
  // ═══════════════════════════════════════════════════════════
  function cerrarModal() {
    document.getElementById("overlay").classList.remove("active");
  }

  // ═══════════════════════════════════════════════════════════
  // GUARDAR (INSERT O UPDATE) - USANDO AJAX
  // ═══════════════════════════════════════════════════════════
  function guardar() {
    const id     = document.getElementById("inp_id").value.trim();
    const nombre = document.getElementById("inp_nombre").value.trim();
    const correo = document.getElementById("inp_correo").value.trim();

    if (!id || !nombre || !correo) {
      mostrarNotificacion("error", "Campos incompletos", "Por favor completa todos los campos.");
      return;
    }

    const datos = new FormData();
    datos.append("identificacion", id);
    datos.append("nombre", nombre);
    datos.append("correo", correo);

    if (modoEdicion) {
      datos.append("action", "update");
      datos.append("identificacion_vieja", identificacionOriginal);
    } else {
      datos.append("action", "insert");
    }

    cerrarModal();

    // AJAX con fetch
    fetch(API_URL, { method: "POST", body: datos })
      .then(r => r.json())
      .then(res => {
        if (res.success) {
          if (modoEdicion) {
            mostrarNotificacion("success", "¡Actualizado!", "El cliente ha sido actualizado correctamente.");
          } else {
            mostrarNotificacion("success", "¡Guardado!", "El cliente ha sido agregado correctamente.");
          }
        } else {
          mostrarNotificacion("error", "Error", res.error || "Ocurrió un error al guardar.");
        }
      })
      .catch(() => {
        mostrarNotificacion("error", "Error de conexión", "No se pudo conectar con el servidor.");
      });
  }

  // ═══════════════════════════════════════════════════════════
  // ELIMINAR - USANDO AJAX
  // ═══════════════════════════════════════════════════════════
  function eliminar(id, nombre) {
    if (!confirm("¿Eliminar el cliente " + nombre + " (ID: " + id + ")?")) return;

    const datos = new FormData();
    datos.append("action", "delete");
    datos.append("identificacion", id);

    // AJAX con fetch
    fetch(API_URL, { method: "POST", body: datos })
      .then(r => r.json())
      .then(res => {
        if (res.success) {
          mostrarNotificacion("success", "¡Eliminado!", "El cliente ha sido eliminado correctamente.");
        } else {
          mostrarNotificacion("error", "Error", res.error || "Ocurrió un error al eliminar.");
        }
      })
      .catch(() => {
        mostrarNotificacion("error", "Error de conexión", "No se pudo conectar con el servidor.");
      });
  }

  // ═══════════════════════════════════════════════════════════
  // BUSCAR CLIENTE (por identificación o nombre)
  // ═══════════════════════════════════════════════════════════
  function buscarCliente() {
    const input = document.getElementById("inputBuscar");
    const filtro = input.value.toLowerCase().trim();
    const filas = document.querySelectorAll(".fila-cliente");
    let encontrados = 0;

    filas.forEach(fila => {
      const identificacion = fila.getAttribute("data-identificacion");
      const nombre = fila.getAttribute("data-nombre");

      // Busca en identificación O en nombre
      if (identificacion.includes(filtro) || nombre.includes(filtro)) {
        fila.style.display = "";
        encontrados++;
      } else {
        fila.style.display = "none";
      }
    });

    // Mostrar mensaje si no hay resultados
    actualizarMensajeBusqueda(encontrados);
  }

  // ═══════════════════════════════════════════════════════════
  // LIMPIAR BÚSQUEDA
  // ═══════════════════════════════════════════════════════════
  function limpiarBusqueda() {
    document.getElementById("inputBuscar").value = "";
    const filas = document.querySelectorAll(".fila-cliente");
    filas.forEach(fila => {
      fila.style.display = "";
    });
    actualizarMensajeBusqueda(filas.length);
  }

  // ═══════════════════════════════════════════════════════════
  // ACTUALIZAR MENSAJE DE BÚSQUEDA
  // ═══════════════════════════════════════════════════════════
  function actualizarMensajeBusqueda(cantidad) {
    const tbody = document.getElementById("tabla");
    let mensajeExistente = tbody.querySelector(".mensaje-busqueda");

    // Eliminar mensaje anterior si existe
    if (mensajeExistente) {
      mensajeExistente.remove();
    }

    // Si no hay resultados, mostrar mensaje
    if (cantidad === 0) {
      const mensaje = document.createElement("tr");
      mensaje.className = "mensaje-busqueda";
      mensaje.innerHTML = '<td colspan="4" class="empty">No se encontraron resultados para tu búsqueda.</td>';
      tbody.appendChild(mensaje);
    }
  }

  // ═══════════════════════════════════════════════════════════
  // CERRAR MODAL AL HACER CLIC FUERA
  // ═══════════════════════════════════════════════════════════
  const overlayEl = document.getElementById("overlay");
  if (overlayEl) {
    overlayEl.addEventListener("click", function(e) {
      if (e.target === this) cerrarModal();
    });
  }

  // Inicializa eventos para botones del dashboard sin depender de onclick inline
  document.addEventListener("DOMContentLoaded", function() {
    const btnAgregar = document.getElementById("btnAgregarCliente");
    if (btnAgregar) {
      btnAgregar.addEventListener("click", abrirModal);
    }

    document.querySelectorAll(".js-editar").forEach(function(btn) {
      btn.addEventListener("click", function() {
        abrirEditar(
          this.dataset.id || "",
          this.dataset.nombre || "",
          this.dataset.correo || ""
        );
      });
    });

    document.querySelectorAll(".js-eliminar").forEach(function(btn) {
      btn.addEventListener("click", function() {
        eliminar(this.dataset.id || "", this.dataset.nombre || "");
      });
    });
  });


