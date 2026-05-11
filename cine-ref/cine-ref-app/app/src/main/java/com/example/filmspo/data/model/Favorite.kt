package com.example.filmspo.data.model

data class Favorite(
    val id: String = "",
    val title: String = "",
    val posterPath: String? = null,
    val releaseDate: String = ""
) {
    fun posterUrl() = posterPath?.let { "https://image.tmdb.org/t/p/w500$it" }
    fun year() = releaseDate.take(4)
}
