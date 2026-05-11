package com.example.filmspo.data.repository

import android.net.Uri
import com.example.filmspo.data.model.Favorite
import com.example.filmspo.data.model.Post
import com.example.filmspo.data.model.User
import com.google.firebase.firestore.FirebaseFirestore
import com.google.firebase.firestore.Query
import com.google.firebase.storage.FirebaseStorage
import kotlinx.coroutines.channels.awaitClose
import kotlinx.coroutines.flow.Flow
import kotlinx.coroutines.flow.callbackFlow
import kotlinx.coroutines.tasks.await

class FirestoreRepository {

    private val db = FirebaseFirestore.getInstance()
    private val storage = FirebaseStorage.getInstance()

    // ── Users ──────────────────────────────────────────────────────────────────

    suspend fun saveUser(user: User) {
        db.collection("users").document(user.uid).set(
            mapOf(
                "uid" to user.uid,
                "email" to user.email,
                "displayName" to user.displayName,
                "role" to user.role
            )
        ).await()
    }

    suspend fun getUser(uid: String): User? = runCatching {
        val doc = db.collection("users").document(uid).get().await()
        User(
            uid = doc.getString("uid") ?: uid,
            email = doc.getString("email") ?: "",
            displayName = doc.getString("displayName") ?: "",
            role = doc.getString("role") ?: "user"
        )
    }.getOrNull()

    suspend fun updateDisplayName(uid: String, name: String) {
        db.collection("users").document(uid).update("displayName", name).await()
    }

    // ── Favorites ──────────────────────────────────────────────────────────────

    suspend fun getFavorites(uid: String): List<Favorite> = runCatching {
        db.collection("users").document(uid).collection("favorites")
            .get().await()
            .documents.mapNotNull { doc ->
                Favorite(
                    id = doc.getString("id") ?: return@mapNotNull null,
                    title = doc.getString("title") ?: "",
                    posterPath = doc.getString("poster_path"),
                    releaseDate = doc.getString("release_date") ?: ""
                )
            }
    }.getOrDefault(emptyList())

    suspend fun addFavorite(uid: String, favorite: Favorite) {
        db.collection("users").document(uid)
            .collection("favorites").document(favorite.id)
            .set(
                mapOf(
                    "id" to favorite.id,
                    "title" to favorite.title,
                    "poster_path" to favorite.posterPath,
                    "release_date" to favorite.releaseDate
                )
            ).await()
    }

    suspend fun removeFavorite(uid: String, movieId: String) {
        db.collection("users").document(uid)
            .collection("favorites").document(movieId)
            .delete().await()
    }

    suspend fun isFavorite(uid: String, movieId: String): Boolean = runCatching {
        db.collection("users").document(uid)
            .collection("favorites").document(movieId)
            .get().await().exists()
    }.getOrDefault(false)

    // ── Posts ──────────────────────────────────────────────────────────────────

    fun getApprovedPostsForMovie(movieId: String): Flow<List<Post>> = callbackFlow {
        val listener = db.collection("posts")
            .whereEqualTo("movieId", movieId)
            .whereEqualTo("status", "approved")
            .orderBy("createdAt", Query.Direction.DESCENDING)
            .addSnapshotListener { snapshot, _ ->
                val posts = snapshot?.documents?.mapNotNull { doc ->
                    Post(
                        id = doc.id,
                        movieId = doc.getString("movieId") ?: "",
                        userId = doc.getString("userId") ?: "",
                        username = doc.getString("username") ?: "",
                        title = doc.getString("title") ?: "",
                        description = doc.getString("description") ?: "",
                        imageUrl = doc.getString("imageUrl") ?: "",
                        status = doc.getString("status") ?: "approved",
                        createdAt = doc.getLong("createdAt") ?: 0L
                    )
                } ?: emptyList()
                trySend(posts)
            }
        awaitClose { listener.remove() }
    }

    suspend fun createPost(post: Post, imageUri: Uri?) {
        var imageUrl = ""
        if (imageUri != null) {
            val ref = storage.reference.child("posts/${System.currentTimeMillis()}_${(Math.random() * 10000).toInt()}.jpg")
            ref.putFile(imageUri).await()
            imageUrl = ref.downloadUrl.await().toString()
        }
        db.collection("posts").add(
            mapOf(
                "movieId" to post.movieId,
                "userId" to post.userId,
                "username" to post.username,
                "title" to post.title,
                "description" to post.description,
                "imageUrl" to imageUrl,
                "status" to "pending",
                "createdAt" to System.currentTimeMillis()
            )
        ).await()
    }

    suspend fun deletePost(postId: String, imageUrl: String) {
        if (imageUrl.isNotEmpty()) {
            runCatching { storage.getReferenceFromUrl(imageUrl).delete().await() }
        }
        db.collection("posts").document(postId).delete().await()
    }

    // ── Moderation ─────────────────────────────────────────────────────────────

    suspend fun getPendingPosts(): List<Post> = runCatching {
        db.collection("posts")
            .whereEqualTo("status", "pending")
            .orderBy("createdAt", Query.Direction.DESCENDING)
            .get().await()
            .documents.mapNotNull { doc ->
                Post(
                    id = doc.id,
                    movieId = doc.getString("movieId") ?: "",
                    userId = doc.getString("userId") ?: "",
                    username = doc.getString("username") ?: "",
                    title = doc.getString("title") ?: "",
                    description = doc.getString("description") ?: "",
                    imageUrl = doc.getString("imageUrl") ?: "",
                    status = "pending",
                    createdAt = doc.getLong("createdAt") ?: 0L
                )
            }
    }.getOrDefault(emptyList())

    suspend fun approvePost(postId: String) {
        db.collection("posts").document(postId).update("status", "approved").await()
    }

    suspend fun rejectPost(postId: String, imageUrl: String) {
        deletePost(postId, imageUrl)
    }
}
