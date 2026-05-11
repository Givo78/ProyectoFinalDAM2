package com.example.filmspo.data.remote

import com.example.filmspo.data.model.Movie
import com.example.filmspo.data.model.TMDBResponse
import retrofit2.http.GET
import retrofit2.http.Path
import retrofit2.http.Query

interface TMDBApi {

    @GET("movie/popular")
    suspend fun getPopular(
        @Query("api_key") apiKey: String = TMDBConfig.API_KEY,
        @Query("language") lang: String = "es-ES"
    ): TMDBResponse

    @GET("movie/upcoming")
    suspend fun getUpcoming(
        @Query("api_key") apiKey: String = TMDBConfig.API_KEY,
        @Query("language") lang: String = "es-ES"
    ): TMDBResponse

    @GET("movie/top_rated")
    suspend fun getTopRated(
        @Query("api_key") apiKey: String = TMDBConfig.API_KEY,
        @Query("language") lang: String = "es-ES"
    ): TMDBResponse

    @GET("search/movie")
    suspend fun search(
        @Query("query") query: String,
        @Query("api_key") apiKey: String = TMDBConfig.API_KEY,
        @Query("language") lang: String = "es-ES"
    ): TMDBResponse

    @GET("movie/{id}")
    suspend fun getDetail(
        @Path("id") id: Int,
        @Query("api_key") apiKey: String = TMDBConfig.API_KEY,
        @Query("language") lang: String = "es-ES"
    ): Movie
}

object TMDBConfig {
    const val API_KEY = "105bbd0346ffdd95539724c374e3b6fd"
    const val BASE_URL = "https://api.themoviedb.org/3/"

    // IDs curados igual que la web
    val CURATED_IDS = listOf(
        438631, 10315, 545611, 376867, 120467, 540473, 792307, 531428,
        335984, 324857, 64690, 313369, 844, 496243, 152601, 530385,
        76341, 34647, 301365, 194, 129, 11906, 62, 329865, 157336,
        603, 747188, 762504, 414906, 872585, 569094, 290098, 503919,
        473033, 399174, 399055, 530915, 49047, 113, 11658, 37165,
        11104, 146, 14784, 149, 9323, 1417, 268896, 8967, 361292
    )
}
