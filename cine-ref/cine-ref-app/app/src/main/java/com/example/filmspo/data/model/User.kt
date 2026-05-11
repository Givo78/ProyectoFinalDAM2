package com.example.filmspo.data.model

data class User(
    val uid: String = "",
    val email: String = "",
    val displayName: String = "",
    val role: String = "user"
) {
    fun isModerator() = role == "moderator"
    fun initial() = displayName.firstOrNull()?.uppercaseChar()?.toString()
        ?: email.firstOrNull()?.uppercaseChar()?.toString() ?: "?"
}
