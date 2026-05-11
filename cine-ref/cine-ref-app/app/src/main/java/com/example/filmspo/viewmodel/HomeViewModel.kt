package com.example.filmspo.viewmodel

import androidx.lifecycle.ViewModel
import androidx.lifecycle.viewModelScope
import com.example.filmspo.data.model.Movie
import com.example.filmspo.data.repository.TMDBRepository
import kotlinx.coroutines.FlowPreview
import kotlinx.coroutines.flow.MutableStateFlow
import kotlinx.coroutines.flow.StateFlow
import kotlinx.coroutines.flow.debounce
import kotlinx.coroutines.flow.distinctUntilChanged
import kotlinx.coroutines.launch

data class HomeState(
    val movies: List<Movie> = emptyList(),
    val isLoading: Boolean = false,
    val searchQuery: String = "",
    val error: String? = null
)

@OptIn(FlowPreview::class)
class HomeViewModel : ViewModel() {

    private val tmdbRepo = TMDBRepository()

    private val _state = MutableStateFlow(HomeState())
    val state: StateFlow<HomeState> = _state

    private val _searchQuery = MutableStateFlow("")

    init {
        loadCuratedMovies()
        viewModelScope.launch {
            _searchQuery
                .debounce(400)
                .distinctUntilChanged()
                .collect { query ->
                    if (query.isBlank()) loadCuratedMovies() else searchMovies(query)
                }
        }
    }

    private fun loadCuratedMovies() {
        viewModelScope.launch {
            _state.value = _state.value.copy(isLoading = true, error = null)
            val movies = tmdbRepo.getPopular()
            _state.value = _state.value.copy(movies = movies, isLoading = false)
        }
    }

    private fun searchMovies(query: String) {
        viewModelScope.launch {
            _state.value = _state.value.copy(isLoading = true, error = null)
            val movies = tmdbRepo.search(query)
            _state.value = _state.value.copy(movies = movies, isLoading = false)
        }
    }

    fun onSearchQueryChange(query: String) {
        _state.value = _state.value.copy(searchQuery = query)
        _searchQuery.value = query
    }

    fun clearSearch() {
        _state.value = _state.value.copy(searchQuery = "")
        _searchQuery.value = ""
    }
}
