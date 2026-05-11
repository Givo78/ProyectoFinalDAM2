package com.example.filmspo.data.model

data class Post(
    val id: String = "",
    val movieId: String = "",
    val userId: String = "",
    val username: String = "",
    val title: String = "",
    val description: String = "",
    val imageUrl: String = "",
    val status: String = "pending",
    val createdAt: Long = 0L
)
