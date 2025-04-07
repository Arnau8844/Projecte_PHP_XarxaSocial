<!-- Modal de Restablecer Contraseña -->
<div class="modal fade" id="resetPasswordModal" tabindex="-1" aria-labelledby="resetPasswordModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" id="resetPasswordModalLabel">Restablecer Contraseña</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
        <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="POST">
            <div class="mb-3">
                <label for="usernameOrEmail" class="form-label">Nombre de usuario o correo electrónico</label>
                <input type="text" class="form-control" id="usernameOrEmail" name="usernameOrEmail" placeholder="Introduce tu nombre de usuario o correo electrónico" required>
            </div>
            <button type="submit" class="btn btn-primary">Enviar correo de restablecimiento</button>
        </form>
        </div>
        </div>
    </div>
</div>

    <div class="modal fade" id="configModal" tabindex="-1" aria-labelledby="configModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="configModalLabel">Configuración</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body">
                <ul class="list-group">
                    <li class="list-group-item">
                        <a href="cambiar_password.php" class="text-decoration-none">
                            <i class="fas fa-key"></i> Cambiar Contraseña
                        </a>
                    </li>
                    <li class="list-group-item">
                        <a href="notificaciones.php" class="text-decoration-none">
                            <i class="fas fa-bell"></i> Configurar Notificaciones
                        </a>
                    </li>
                    <li class="list-group-item">
                        <a href="privacidad.php" class="text-decoration-none">
                            <i class="fas fa-user-shield"></i> Configuración de Privacidad
                        </a>
                    </li>
                    <li class="list-group-item">
                        <a href="logout.php" class="text-decoration-none text-danger">
                            <i class="fas fa-sign-out-alt"></i> Cerrar Sesión
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
    </div>

<!-- Modal para editar perfil -->
<div class="modal fade" id="editProfileModal" tabindex="-1" aria-labelledby="editProfileModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editProfileModalLabel">Editar Perfil</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body align-items-center justify-content-center">
                <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="POST" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label for="avatar" class="form-label">Avatar</label>
                        <div class="d-flex justify-content-center position-relative">
                            <div class="editar-avatar position-relative d-flex align-items-center justify-content-center"
                                style="cursor: pointer; width: 130px; height: 130px; border-radius: 50%; overflow: visible; position: relative;"
                                onclick="document.getElementById('fileInput').click();">
                                <img id="avatarPreview" src="../uploads/avatars/<?php echo htmlspecialchars($user['avatar']); ?>"
                                     alt="avatar"
                                     style="width: 100%; height: 100%; object-fit: cover; border-radius: 50%;">
                                <div class="edit-icon position-absolute"
                                     style="bottom: -10px; right: -10px; background: rgba(0, 0, 0, 0.6); color: white; font-size: 20px; width: 35px; height: 35px; border-radius: 50%; display: flex; justify-content: center; align-items: center; z-index: 5;">
                                    <i class="fas fa-pencil-alt"></i>
                                </div>
                            </div>
                            <input type="file" id="fileInput" name="avatar" accept="image/*" style="display: none;" onchange="updateAvatarPreview(event)">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="mail" class="form-label">Correo Electrónico</label>
                        <input type="email" class="form-control" id="mail" name="mail" value="<?php echo htmlspecialchars($user['mail']); ?>" required readonly>
                    </div>

                    <div class="mb-3">
                        <label for="username" class="form-label">Username</label>
                        <input type="text" class="form-control" id="username" name="username" value="<?php echo htmlspecialchars($user['username']); ?>" required>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="firstName" class="form-label">Primer Nombre</label>
                            <input type="text" class="form-control" id="firstName" name="firstName" value="<?php echo htmlspecialchars($user['userFirstName']); ?>" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="lastName" class="form-label">Apellido</label>
                            <input type="text" class="form-control" id="lastName" name="lastName" value="<?php echo htmlspecialchars($user['userLastName']); ?>" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="data_naix" class="form-label">Fecha de Nacimiento</label>
                        <input type="date" class="form-control" id="data_naix" name="data_naix" value="<?php echo htmlspecialchars($user['data_naix']); ?>">
                    </div>

                    <div class="mb-3">
                        <label for="location" class="form-label">Ubicación</label>
                        <input type="text" class="form-control" id="location" name="location" value="<?php echo htmlspecialchars($user['location'] ?? ''); ?>">
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label">Descripción</label>
                        <textarea class="form-control" id="description" name="description" rows="3"><?php echo htmlspecialchars($user['description'] ?? ''); ?></textarea>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal Mejorado tipo "Twitter" -->
<div class="modal fade" id="modalCrearPost" tabindex="-1" aria-labelledby="modalCrearPostLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <!-- Encabezado del modal -->
      <div class="modal-header">
        <h5 class="modal-title" id="modalCrearPostLabel">Crear un Post</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>
      
      <!-- Cuerpo del modal -->
      <div class="modal-body">
        <div id="alertaPost"></div>
        
        <!-- Formulario para postear -->
        <form id="formPost" enctype="multipart/form-data">
          <!-- Texto principal -->
          <div class="mb-3">
            <textarea name="content"
                      class="form-control border-0"
                      rows="3"
                      placeholder="¿Qué estás pensando?"
                      required
                      style="resize: none;"></textarea>
          </div>
          
          <!-- Footer del modal con íconos y botón de “Post” -->
          <div class="d-flex justify-content-between align-items-center">
            
            <!-- Íconos (Imagen, GIF, Encuesta, Emoji, Calendario, Ubicación...) -->
            <div class="d-flex align-items-center">
              <!-- Imagen -->
              <label for="imageInput" class="me-3 mb-0" style="cursor: pointer;">
                <i class="far fa-image" style="font-size: 1.3rem;"></i>
              </label>
              <input type="file" id="imageInput" name="image" accept="image/*" style="display: none;">
              
              <!-- GIF (ejemplo) -->
              <label for="gifInput" class="me-3 mb-0" style="cursor: pointer;">
                <i class="far fa-file-video" style="font-size: 1.3rem;"></i>
              </label>
              <input type="file" id="gifInput" name="gif" accept=".gif" style="display: none;">
              
              <!-- Encuesta (poll) -->
              <label for="pollInput" class="me-3 mb-0" style="cursor: pointer;">
                <i class="fas fa-poll-h" style="font-size: 1.3rem;"></i>
              </label>
              <input type="text" id="pollInput" name="poll" placeholder="Crear encuesta" style="display: none;">
              
              <!-- Emoji -->
              <label for="emojiInput" class="me-3 mb-0" style="cursor: pointer;">
                <i class="far fa-smile" style="font-size: 1.3rem;"></i>
              </label>
              <input type="text" id="emojiInput" name="emoji" placeholder="Seleccionar emoji" style="display: none;">
              
              <!-- Programar publicación (schedule) -->
              <label for="scheduleInput" class="me-3 mb-0" style="cursor: pointer;">
                <i class="far fa-calendar-alt" style="font-size: 1.3rem;"></i>
              </label>
              <input type="text" id="scheduleInput" name="schedule" placeholder="Programar" style="display: none;">
              
              <!-- Ubicación -->
              <label for="locationInput" class="mb-0" style="cursor: pointer;">
                <i class="fas fa-map-marker-alt" style="font-size: 1.3rem;"></i>
              </label>
              <input type="text" id="locationInput" name="location" placeholder="Ubicación" style="display: none;">
            </div>
            
            <!-- Botón para publicar -->
            <button type="submit" class="btn btn-primary rounded-pill px-4">Post</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>


<script>
    function updateAvatarPreview(event) {
        const file = event.target.files[0];
        const reader = new FileReader();
        
        reader.onload = function(e) {
            const avatarPreview = document.getElementById('avatarPreview');
            avatarPreview.src = e.target.result;
        }

        if (file) {
            reader.readAsDataURL(file);
        }
    }
</script>


<script>
document.getElementById('formPost').addEventListener('submit', async function(e) {
  e.preventDefault();

  const form = e.target;
  const formData = new FormData(form);
  const alerta = document.getElementById("alertaPost");
  alerta.innerHTML = "";

  try {
    const response = await fetch('../post-handler.php', { // Cambié la ruta a '../post-handler.php'
      method: 'POST',
      body: formData
    });

    const data = await response.json();

    if (data.success) {
      alerta.innerHTML = `<div class="alert alert-success">${data.message}</div>`;
      form.reset();
      setTimeout(() => {
        const modal = bootstrap.Modal.getInstance(document.getElementById('modalCrearPost'));
        modal.hide();
        location.reload(); // opcional si quieres recargar los posts
      }, 1000);
    } else {
      alerta.innerHTML = `<div class="alert alert-danger">${data.message}</div>`;
    }
  } catch (error) {
    alerta.innerHTML = `<div class="alert alert-danger">Error al procesar el post.</div>`;
  }
});
</script>
