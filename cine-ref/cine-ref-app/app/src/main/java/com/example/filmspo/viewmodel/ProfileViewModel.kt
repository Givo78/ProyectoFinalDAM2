package com.example.filmspo.viewmodel

import androidx.lifecycle.ViewModel
import androidx.lifecycle.viewModelScope
import com.example.filmspo.data.model.Favorite
import com.example.filmspo.data.model.User
import com.example.filmspo.data.repository.AuthRepository
import com.example.filmspo.data.repository.FirestoreRepository
import com.google.firebase.auth.UserProfileChangeRequest
import kotlinx.coroutines.flow.MutableStateFlow
import kotlinx.coroutines.flow.StateFlow
import kotlinx.coroutines.launch
import kotlinx.coroutines.tasks.await

data class ProfileState(
    val user: User? = null,
    val favorites: List<Favorite> = emptyList(),
    val isLoading: Boolean = false,
    val isSaving: Boolean = false,
    val error: String? = null,
    val successMessage: String? = null
)

class ProfileViewModel : ViewModel() {

    private val authRepo = AuthRepository()
    private val firestoreRepo = FirestoreRepository()

    private val _state = MutableStateFlow(ProfileState())
    val state: StateFlow<ProfileState> = _state

    init {
        loadProfile()
    }

    fun loadProfile() {
        val uid = authRepo.currentUser?.uid ?: return
        viewModelScope.launch {
            _state.value = _state.value.copy(isLoading = true)
            val user = firestoreRepo.getUser(uid)
            val favorites = firestoreRepo.getFavorites(uid)
            _state.value = _state.value.copy(
                user = user,
                favorites = favorites,
                isLoading = false
            )
        }
    }

    fun updateName(newName: String) {
        if (newName.isBlank()) {
            _state.value = _state.value.copy(error = "El nombre no puede estar vacío")
            return
        }
        val uid = authRepo.currentUser?.uid ?: return
        viewModelScope.launch {
            _state.value = _state.value.copy(isSaving = true, error = null)
            runCatching {
                firestoreRepo.updateDisplayName(uid, newName.trim())
                val profileUpdates = UserProfileChangeRequest.Builder()
                    .setDisplayName(newName.trim())
                    .build()
                authRepo.currentUser?.updateProfile(profileUpdates)?.await()
                _state.value = _state.value.copy(
                    user = _state.value.user?.copy(displayName = newName.trim()),
                    isSaving = false,
                    successMessage = "Nombre actualizado"
                )
            }.onFailure {
                _state.value = _state.value.copy(isSaving = false, error = "Error al actualizar")
            }
        }
    }

    fun removeFavorite(movieId: String) {
        val uid = authRepo.currentUser?.uid ?: return
        viewModelScope.launch {
            runCatching {
                firestoreRepo.removeFavorite(uid, movieId)
                _state.value = _state.value.copy(
                    favorites = _state.value.favorites.filter { it.id != movieId }
                )
            }
        }
    }

    fun clearMessages() {
        _state.value = _state.value.copy(error = null, successMessage = null)
    }
}
