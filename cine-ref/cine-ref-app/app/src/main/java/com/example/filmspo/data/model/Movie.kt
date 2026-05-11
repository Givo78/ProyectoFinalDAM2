package com.example.filmspo.data.model

import com.google.gson.annotations.SerializedName

data class Movie(
    val id: Int = 0,
    val title: String = "",
    val overview: String = "",
    @SerializedName("poster_path") val posterPath: String? = null,
    @SerializedName("release_date") val releaseDate: String = "",
    @SerializedName("vote_average") val voteAverage: Double = 0.0,
    val genres: List<Genre> = emptyList()
) {
    fun posterUrl() = posterPath?.let { "https://image.tmdb.org/t/p/w500$it" }
    fun year() = releaseDate.take(4)
}

data class Genre(val id: Int = 0, val name: String = "")

data class TMDBResponse(val results: List<Movie> = emptyList())
