package com.example.filmspo.viewmodel

import androidx.lifecycle.ViewModel
import androidx.lifecycle.viewModelScope
import com.example.filmspo.data.model.Post
import com.example.filmspo.data.repository.FirestoreRepository
import kotlinx.coroutines.flow.MutableStateFlow
import kotlinx.coroutines.flow.StateFlow
import kotlinx.coroutines.launch

data class ModerationState(
    val pendingPosts: List<Post> = emptyList(),
    val isLoading: Boolean = false,
    val error: String? = null,
    val message: String? = null
)

class ModerationViewModel : ViewModel() {

    private val firestoreRepo = FirestoreRepository()

    private val _state = MutableStateFlow(ModerationState())
    val state: StateFlow<ModerationState> = _state

    init {
        loadPendingPosts()
    }

    fun loadPendingPosts() {
        viewModelScope.launch {
            _state.value = _state.value.copy(isLoading = true)
            val posts = firestoreRepo.getPendingPosts()
            _state.value = _state.value.copy(pendingPosts = posts, isLoading = false)
        }
    }

    fun approvePost(post: Post) {
        viewModelScope.launch {
            runCatching {
                firestoreRepo.approvePost(post.id)
                _state.value = _state.value.copy(
                    pendingPosts = _state.value.pendingPosts.filter { it.id != post.id },
                    message = "Post aprobado"
                )
            }.onFailure {
                _state.value = _state.value.copy(error = "Error al aprobar")
            }
        }
    }

    fun rejectPost(post: Post) {
        viewModelScope.launch {
            runCatching {
                firestoreRepo.rejectPost(post.id, post.imageUrl)
                _state.value = _state.value.copy(
                    pendingPosts = _state.value.pendingPosts.filter { it.id != post.id },
                    message = "Post rechazado"
                )
            }.onFailure {
                _state.value = _state.value.copy(error = "Error al rechazar")
            }
        }
    }

    fun clearMessage() {
        _state.value = _state.value.copy(error = null, message = null)
    }
}
