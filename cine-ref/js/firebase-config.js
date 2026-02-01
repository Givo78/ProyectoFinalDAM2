// js/firebase-config.js

// Importa las funciones que necesites del SDK (versión 9+ modular)
import { initializeApp } from "https://www.gstatic.com/firebasejs/10.7.1/firebase-app.js";
import { getAuth } from "https://www.gstatic.com/firebasejs/10.7.1/firebase-auth.js";
import { getFirestore } from "https://www.gstatic.com/firebasejs/10.7.1/firebase-firestore.js";

// TODO: Reemplaza esto con tu configuración real de Firebase
// Consola Firebase -> Project Settings -> General -> Your apps -> SDK setup/configuration (NPM/CDN)
const firebaseConfig = {
    apiKey: "AIzaSyC0OnDZlZaCKxh9bHjyiW4vjetMAvQgmeo",
    authDomain: "ut5-firestore-alejandro.firebaseapp.com",
    projectId: "ut5-firestore-alejandro",
    storageBucket: "ut5-firestore-alejandro.firebasestorage.app",
    messagingSenderId: "886403096538",
    appId: "1:886403096538:web:83e5beff1b52ca22892ae3"
};

// Inicializar Firebase
const app = initializeApp(firebaseConfig);
const auth = getAuth(app);
const db = getFirestore(app);

export { auth, db };
