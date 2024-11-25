
//VALIDAR DATOS PROFESORES-ALUMNOS
function validateForm() {
    const nombre = document.getElementById("nombre").value.trim();
    const apellido = document.getElementById("apellido").value.trim();
    const correo = document.getElementById("correo").value.trim();
    const fec_nac = document.getElementById("fec_nac").value;
    const errorMessages = [];

    // Validación del nombre
    if (nombre.length < 2) {
        errorMessages.push("El nombre debe tener al menos 2 letras.");
    }

    if (!/^[a-zA-ZÁÉÍÓÚáéíóúÑñ\s]+$/.test(nombre)) {
        errorMessages.push("El nombre solo debe contener letras, espacios y puede incluir acentos.");
    }
    

    // Validación del apellido
    if (apellido.length < 2) {
        errorMessages.push("El apellido debe tener al menos 2 letras.");
    }
    if (!/^[a-zA-ZÁÉÍÓÚáéíóúÑñ\s]+$/.test(apellido)) {
        errorMessages.push("El apellido solo debe contener letras y espacios.");
    }

    // Validación del correo electrónico
    const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailPattern.test(correo)) {
        errorMessages.push("Por favor, ingresa un correo electrónico válido.");
    }

    // Validación de la fecha de nacimiento
    const birthDate = new Date(fec_nac);
    const today = new Date();
    if (birthDate >= today || isNaN(birthDate.getTime())) {
        errorMessages.push("Por favor, ingresa una fecha de nacimiento válida.");
    }

    const birthYear = birthDate.getFullYear();
    if (birthYear <= 1800) {
        errorMessages.push("Por favor, ingresa una fecha de nacimiento válida.");
    }

    // Mostrar errores o enviar el formulario
    const errorContainer = document.getElementById("error-messages");
    if (errorMessages.length > 0) {
        errorContainer.innerHTML = errorMessages.join("<br>");
        errorContainer.style.display = "block"; // Muestra el contenedor de errores
        return false; // Evita que el formulario se envíe
    } else {
        errorContainer.style.display = "none"; // Oculta el contenedor de errores si no hay problemas
    }

    return true;
}

function validateFormUpdate() {
    const nombre = document.getElementById("nombre").value.trim();
    const apellido = document.getElementById("apellido").value.trim();
    const fec_nac = document.getElementById("fec_nac").value;
    const password = document.getElementById("password").value.trim();
    const errorMessages = [];

    // Validación del nombre
    if (nombre.length < 2) {
        errorMessages.push("El nombre debe tener al menos 2 letras.");
    }
    if (!/^[a-zA-ZÁÉÍÓÚáéíóúÑñ\s]+$/.test(nombre)) {
        errorMessages.push("El nombre solo debe contener letras y espacios.");
    }

    // Validación del apellido
    if (apellido.length < 2) {
        errorMessages.push("El apellido debe tener al menos 2 letras.");
    }
    if (!/^[a-zA-ZÁÉÍÓÚáéíóúÑñ\s]+$/.test(apellido)) {
        errorMessages.push("El apellido solo debe contener letras y espacios.");
    }

    // Validación de la fecha de nacimiento
    const birthDate = new Date(fec_nac);
    const today = new Date();
    if (birthDate >= today || isNaN(birthDate.getTime())) {
        errorMessages.push("Por favor, ingresa una fecha de nacimiento válida.");
    }

    const birthYear = birthDate.getFullYear();
    if (birthYear <= 1800) {
        errorMessages.push("Por favor, ingresa una fecha de nacimiento válida.");
    }

    // Validación de la contraseña
    if (password.length < 4) {
        errorMessages.push("La contraseña debe tener al menos 4 caracteres.");
    }

    // Mostrar errores o enviar el formulario
    const errorContainer = document.getElementById("error-messages");
    if (errorMessages.length > 0) {
        errorContainer.innerHTML = errorMessages.join("<br>");
        errorContainer.style.display = "block"; // Muestra el contenedor de errores
        return false; // Evita que el formulario se envíe
    } else {
        errorContainer.style.display = "none"; // Oculta el contenedor de errores si no hay problemas
    }

    return true;
}


function validateFormUpdateAdmin() {
    const nombre = document.getElementById("nombre").value.trim();
    const apellido = document.getElementById("apellido").value.trim();
    const fec_nac = document.getElementById("fec_nac").value;
    const errorMessages = [];

    // Validación del nombre
    if (nombre.length < 2) {
        errorMessages.push("El nombre debe tener al menos 2 letras.");
    }
    if (!/^[a-zA-ZÁÉÍÓÚáéíóúÑñ\s]+$/.test(nombre)) {
        errorMessages.push("El nombre solo debe contener letras y espacios.");
    }

    // Validación del apellido
    if (apellido.length < 2) {
        errorMessages.push("El apellido debe tener al menos 2 letras.");
    }
    if (!/^[a-zA-ZÁÉÍÓÚáéíóúÑñ\s]+$/.test(apellido)) {
        errorMessages.push("El apellido solo debe contener letras y espacios.");
    }

    // Validación de la fecha de nacimiento
    const birthDate = new Date(fec_nac);
    const today = new Date();
    if (birthDate >= today || isNaN(birthDate.getTime())) {
        errorMessages.push("Por favor, ingresa una fecha de nacimiento válida.");
    }

    const birthYear = birthDate.getFullYear();
    if (birthYear <= 1800) {
        errorMessages.push("Por favor, ingresa una fecha de nacimiento válida.");
    }

    // Mostrar errores o enviar el formulario
    const errorContainer = document.getElementById("error-messages");
    if (errorMessages.length > 0) {
        errorContainer.innerHTML = errorMessages.join("<br>");
        errorContainer.style.display = "block"; // Muestra el contenedor de errores
        return false; // Evita que el formulario se envíe
    } else {
        errorContainer.style.display = "none"; // Oculta el contenedor de errores si no hay problemas
    }

    return true;
}

//VALIDAR DATOS MATERIAS
function validateMateriaForm() {
    const nombre = document.getElementById("nombre").value.trim();
    const descripcion = document.getElementById("descripcion").value.trim();
    const errorMessages = [];


    // Validación del nombre de la materia
    if (nombre.length < 5) {
        errorMessages.push("El nombre de la materia debe tener al menos 5 caracteres.");
    }

    // Validación de descripcion
    if (descripcion.length < 8) {
        errorMessages.push("La descripcion debe tener un minimo de 8 caracteres.");
    }
    
    const errorContainer = document.getElementById("error-messages");
    if (errorMessages.length > 0) {
        errorContainer.innerHTML = errorMessages.join("<br>");
        errorContainer.style.display = "block"; // Muestra el contenedor de errores
        return false; // Evita que el formulario se envíe
    } else {
        errorContainer.style.display = "none"; // Oculta el contenedor de errores si no hay problemas
    }

    return true;
}