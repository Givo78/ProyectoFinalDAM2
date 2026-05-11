package com.example.filmspo.viewmodel

import androidx.lifecycle.ViewModel
import androidx.lifecycle.viewModelScope
import com.example.filmspo.data.model.User
import com.example.filmspo.data.repository.AuthRepository
import com.example.filmspo.data.repository.FirestoreRepository
import kotlinx.coroutines.flow.MutableStateFlow
import kotlinx.coroutines.flow.StateFlow
import kotlinx.coroutines.launch

data class AuthState(
    val isLoading: Boolean = false,
    val error: String? = null,
    val isAuthenticated: Boolean = false
)

class AuthViewModel : ViewModel() {

    private val authRepo = AuthRepository()
    private val firestoreRepo = FirestoreRepository()

    private val _state = MutableStateFlow(AuthState(isAuthenticated = authRepo.isLoggedIn()))
    val state: StateFlow<AuthState> = _state

    fun login(email: String, password: String) {
        if (email.isBlank() || password.isBlank()) {
            _state.value = _state.value.copy(error = "Por favor rellena todos los campos")
            return
        }
        viewModelScope.launch {
            _state.value = _state.value.copy(isLoading = true, error = null)
            authRepo.login(email.trim(), password).fold(
                onSuccess = { _state.value = _state.value.copy(isLoading = false, isAuthenticated = true) },
                onFailure = { _state.value = _state.value.copy(isLoading = false, error = "Credenciales incorrectas") }
            )
        }
    }

    fun register(name: String, email: String, password: String, confirmPassword: String) {
        if (name.isBlank() || email.isBlank() || password.isBlank()) {
            _state.value = _state.value.copy(error = "Por favor rellena todos los campos")
            return
        }
        if (password != confirmPassword) {
            _state.value = _state.value.copy(error = "Las contraseñas no coinciden")
            return
        }
        if (password.length < 6) {
            _state.value = _state.value.copy(error = "La contraseña debe tener al menos 6 caracteres")
            return
        }
        viewModelScope.launch {
            _state.value = _state.value.copy(isLoading = true, error = null)
            authRepo.register(email.trim(), password, name.trim()).fold(
                onSuccess = { firebaseUser ->
                    val user = User(
                        uid = firebaseUser.uid,
                        email = firebaseUser.email ?: "",
                        displayName = name.trim(),
                        role = "user"
                    )
                    firestoreRepo.saveUser(user)
                    _state.value = _state.value.copy(isLoading = false, isAuthenticated = true)
                },
                onFailure = { e ->
                    val msg = when {
                        e.message?.contains("email") == true -> "Este email ya está en uso"
                        else -> "Error al registrar: ${e.message}"
                    }
                    _state.value = _state.value.copy(isLoading = false, error = msg)
                }
            )
        }
    }

    fun logout() {
        authRepo.logout()
        _state.value = AuthState(isAuthenticated = false)
    }

    fun clearError() {
        _state.value = _state.value.copy(error = null)
    }
}
