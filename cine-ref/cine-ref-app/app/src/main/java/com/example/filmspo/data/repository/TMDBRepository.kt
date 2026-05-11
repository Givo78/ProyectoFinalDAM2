package com.example.filmspo.data.repository

import com.example.filmspo.data.model.Movie
import com.example.filmspo.data.remote.TMDBApi
import com.example.filmspo.data.remote.TMDBConfig
import kotlinx.coroutines.async
import kotlinx.coroutines.awaitAll
import kotlinx.coroutines.coroutineScope
import okhttp3.OkHttpClient
import okhttp3.logging.HttpLoggingInterceptor
import retrofit2.Retrofit
import retrofit2.converter.gson.GsonConverterFactory

class TMDBRepository {

    private val api: TMDBApi by lazy {
        val logging = HttpLoggingInterceptor().apply { level = HttpLoggingInterceptor.Level.BASIC }
        val client = OkHttpClient.Builder().addInterceptor(logging).build()
        Retrofit.Builder()
            .baseUrl(TMDBConfig.BASE_URL)
            .client(client)
            .addConverterFactory(GsonConverterFactory.create())
            .build()
            .create(TMDBApi::class.java)
    }

    suspend fun getPopular(): List<Movie> = runCatching {
        api.getPopular().results
    }.getOrDefault(emptyList())

    suspend fun getCuratedMovies(): List<Movie> = runCatching {
        coroutineScope {
            TMDBConfig.CURATED_IDS.map { id ->
                async { runCatching { api.getDetail(id) }.getOrNull() }
            }.awaitAll().filterNotNull()
        }
    }.getOrDefault(emptyList())

    suspend fun search(query: String): List<Movie> = runCatching {
        api.search(query).results
    }.getOrDefault(emptyList())

    suspend fun getDetail(id: Int): Movie? = runCatching {
        api.getDetail(id)
    }.getOrNull()

    suspend fun getUpcoming(): List<Movie> = runCatching {
        api.getUpcoming().results.take(5)
    }.getOrDefault(emptyList())

    suspend fun getTopRated(): List<Movie> = runCatching {
        api.getTopRated().results.take(5)
    }.getOrDefault(emptyList())
}
