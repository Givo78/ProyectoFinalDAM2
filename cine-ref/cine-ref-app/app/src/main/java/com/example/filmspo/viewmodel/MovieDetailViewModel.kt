package com.example.filmspo.viewmodel

import android.net.Uri
import androidx.lifecycle.ViewModel
import androidx.lifecycle.viewModelScope
import com.example.filmspo.data.model.Favorite
import com.example.filmspo.data.model.Movie
import com.example.filmspo.data.model.Post
import com.example.filmspo.data.repository.AuthRepository
import com.example.filmspo.data.repository.FirestoreRepository
import com.example.filmspo.data.repository.TMDBRepository
import kotlinx.coroutines.flow.MutableStateFlow
import kotlinx.coroutines.flow.StateFlow
import kotlinx.coroutines.launch

data class MovieDetailState(
    val movie: Movie? = null,
    val posts: List<Post> = emptyList(),
    val isFavorited: Boolean = false,
    val favoritesCount: Int = 0,
    val isLoading: Boolean = false,
    val isPostLoading: Boolean = false,
    val error: String? = null,
    val successMessage: String? = null
)

class MovieDetailViewModel : ViewModel() {

    private val tmdbRepo = TMDBRepository()
    private val firestoreRepo = FirestoreRepository()
    private val authRepo = AuthRepository()

    private val _state = MutableStateFlow(MovieDetailState())
    val state: StateFlow<MovieDetailState> = _state

    fun loadMovie(movieId: Int) {
        viewModelScope.launch {
            _state.value = _state.value.copy(isLoading = true)
            val movie = tmdbRepo.getDetail(movieId)
            _state.value = _state.value.copy(movie = movie, isLoading = false)

            val uid = authRepo.currentUser?.uid ?: return@launch
            val isFav = firestoreRepo.isFavorite(uid, movieId.toString())
            val favCount = firestoreRepo.getFavorites(uid).size
            _state.value = _state.value.copy(isFavorited = isFav, favoritesCount = favCount)
        }
        viewModelScope.launch {
            firestoreRepo.getApprovedPostsForMovie(movieId.toString()).collect { posts ->
                _state.value = _state.value.copy(posts = posts)
            }
        }
    }

    fun toggleFavorite() {
        val movie = _state.value.movie ?: return
        val uid = authRepo.currentUser?.uid ?: return

        viewModelScope.launch {
            if (_state.value.isFavorited) {
                firestoreRepo.removeFavorite(uid, movie.id.toString())
                _state.value = _state.value.copy(
                    isFavorited = false,
                    favoritesCount = (_state.value.favoritesCount - 1).coerceAtLeast(0),
                    successMessage = "Eliminado de favoritos"
                )
            } else {
                if (_state.value.favoritesCount >= 3) {
                    _state.value = _state.value.copy(error = "Solo puedes tener 3 películas favoritas")
                    return@launch
                }
                val fav = Favorite(
                    id = movie.id.toString(),
                    title = movie.title,
                    posterPath = movie.posterPath,
                    releaseDate = movie.releaseDate
                )
                firestoreRepo.addFavorite(uid, fav)
                _state.value = _state.value.copy(
                    isFavorited = true,
                    favoritesCount = _state.value.favoritesCount + 1,
                    successMessage = "Añadido a favoritos"
                )
            }
        }
    }

    fun createPost(title: String, description: String, imageUri: Uri?) {
        val movie = _state.value.movie ?: return
        val user = authRepo.currentUser ?: return

        if (title.isBlank() || description.isBlank()) {
            _state.value = _state.value.copy(error = "El título y la descripción son obligatorios")
            return
        }

        viewModelScope.launch {
            _state.value = _state.value.copy(isPostLoading = true, error = null)
            runCatching {
                val post = Post(
                    movieId = movie.id.toString(),
                    userId = user.uid,
                    username = user.displayName ?: user.email ?: "Usuario",
                    title = title.trim(),
                    description = description.trim()
                )
                firestoreRepo.createPost(post, imageUri)
            }.fold(
                onSuccess = {
                    _state.value = _state.value.copy(
                        isPostLoading = false,
                        successMessage = "Post enviado — pendiente de moderación"
                    )
                },
                onFailure = {
                    _state.value = _state.value.copy(
                        isPostLoading = false,
                        error = "Error al publicar el post"
                    )
                }
            )
        }
    }

    fun deletePost(post: Post) {
        viewModelScope.launch {
            runCatching { firestoreRepo.deletePost(post.id, post.imageUrl) }
        }
    }

    fun clearMessages() {
        _state.value = _state.value.copy(error = null, successMessage = null)
    }
}
