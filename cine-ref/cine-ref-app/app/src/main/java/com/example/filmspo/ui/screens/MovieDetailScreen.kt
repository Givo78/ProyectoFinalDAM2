package com.example.filmspo.ui.screens

import android.net.Uri
import androidx.activity.compose.rememberLauncherForActivityResult
import androidx.activity.result.contract.ActivityResultContracts
import androidx.compose.foundation.background
import androidx.compose.foundation.border
import androidx.compose.foundation.layout.Arrangement
import androidx.compose.foundation.layout.Box
import androidx.compose.foundation.layout.Column
import androidx.compose.foundation.layout.ExperimentalLayoutApi
import androidx.compose.foundation.layout.FlowRow
import androidx.compose.foundation.layout.Row
import androidx.compose.foundation.layout.Spacer
import androidx.compose.foundation.layout.aspectRatio
import androidx.compose.foundation.layout.fillMaxSize
import androidx.compose.foundation.layout.fillMaxWidth
import androidx.compose.foundation.layout.height
import androidx.compose.foundation.layout.offset
import androidx.compose.foundation.layout.padding
import androidx.compose.foundation.layout.width
import androidx.compose.foundation.rememberScrollState
import androidx.compose.ui.graphics.RectangleShape
import androidx.compose.foundation.verticalScroll
import androidx.compose.material.icons.Icons
import androidx.compose.material.icons.filled.ArrowBack
import androidx.compose.material.icons.filled.Favorite
import androidx.compose.material.icons.filled.FavoriteBorder
import androidx.compose.material3.CircularProgressIndicator
import androidx.compose.material3.Icon
import androidx.compose.material3.IconButton
import androidx.compose.material3.OutlinedTextField
import androidx.compose.material3.OutlinedTextFieldDefaults
import androidx.compose.material3.Text
import androidx.compose.runtime.Composable
import androidx.compose.runtime.LaunchedEffect
import androidx.compose.runtime.collectAsState
import androidx.compose.runtime.getValue
import androidx.compose.runtime.mutableStateOf
import androidx.compose.runtime.remember
import androidx.compose.runtime.setValue
import androidx.compose.ui.Alignment
import androidx.compose.ui.Modifier
import androidx.compose.ui.graphics.Color
import androidx.compose.ui.layout.ContentScale
import androidx.compose.ui.text.font.FontWeight
import androidx.compose.ui.unit.dp
import androidx.compose.ui.unit.sp
import androidx.lifecycle.viewmodel.compose.viewModel
import coil.compose.AsyncImage
import com.example.filmspo.data.repository.AuthRepository
import com.example.filmspo.ui.components.BrutalistButton
import com.example.filmspo.ui.components.BrutalistError
import com.example.filmspo.ui.components.BrutalistSuccess
import com.example.filmspo.ui.components.BrutalistTag
import com.example.filmspo.ui.components.PostCard
import com.example.filmspo.ui.theme.ColorAcento
import com.example.filmspo.ui.theme.ColorAmarillo
import com.example.filmspo.ui.theme.ColorAzul
import com.example.filmspo.ui.theme.ColorFondo
import com.example.filmspo.ui.theme.ColorNegro
import com.example.filmspo.ui.theme.ColorRojo
import com.example.filmspo.ui.theme.ColorVerde
import com.example.filmspo.ui.theme.PermanentMarker
import com.example.filmspo.ui.theme.SpaceGrotesk
import com.example.filmspo.viewmodel.MovieDetailViewModel
import kotlinx.coroutines.delay

@OptIn(ExperimentalLayoutApi::class)
@Composable
fun MovieDetailScreen(
    movieId: Int,
    onBack: () -> Unit,
    viewModel: MovieDetailViewModel = viewModel()
) {
    val state by viewModel.state.collectAsState()
    val authRepo = remember { AuthRepository() }
    val currentUser = authRepo.currentUser

    var postTitle by remember { mutableStateOf("") }
    var postDescription by remember { mutableStateOf("") }
    var selectedImageUri by remember { mutableStateOf<Uri?>(null) }
    var showForm by remember { mutableStateOf(false) }

    val imagePicker = rememberLauncherForActivityResult(ActivityResultContracts.GetContent()) { uri ->
        selectedImageUri = uri
    }

    LaunchedEffect(movieId) {
        viewModel.loadMovie(movieId)
    }

    LaunchedEffect(state.successMessage) {
        val msg = state.successMessage ?: return@LaunchedEffect
        delay(2000)
        viewModel.clearMessages()
        // Si el post fue enviado a moderación cerramos el formulario
        if (msg.contains("moderación")) {
            postTitle = ""
            postDescription = ""
            selectedImageUri = null
            showForm = false
        }
    }

    Column(
        modifier = Modifier
            .fillMaxSize()
            .background(ColorFondo)
            .verticalScroll(rememberScrollState())
    ) {
        // Navbar con back
        Box(
            modifier = Modifier
                .fillMaxWidth()
                .background(ColorAcento)
                .border(width = 3.dp, color = ColorNegro)
                .padding(horizontal = 8.dp, vertical = 8.dp)
        ) {
            Row(verticalAlignment = Alignment.CenterVertically) {
                IconButton(onClick = onBack) {
                    Icon(Icons.Default.ArrowBack, contentDescription = "Volver", tint = ColorNegro)
                }
                Text(
                    text = "FILMSPO",
                    fontFamily = PermanentMarker,
                    fontSize = 22.sp,
                    color = ColorNegro
                )
            }
        }

        if (state.isLoading) {
            Box(
                modifier = Modifier.fillMaxWidth().height(300.dp),
                contentAlignment = Alignment.Center
            ) {
                CircularProgressIndicator(color = ColorAcento)
            }
            return@Column
        }

        val movie = state.movie ?: return@Column

        // Cabecera: poster + info
        Box(
            modifier = Modifier
                .fillMaxWidth()
                .background(Color.White)
                .border(width = 3.dp, color = ColorNegro)
                .padding(16.dp)
        ) {
            Column {
                Row(modifier = Modifier.fillMaxWidth()) {
                    // Poster
                    Box(
                        modifier = Modifier
                            .width(140.dp)
                            .aspectRatio(2f / 3f)
                            .background(ColorAzul)
                            .border(3.dp, ColorNegro)
                    ) {
                        AsyncImage(
                            model = movie.posterUrl(),
                            contentDescription = movie.title,
                            contentScale = ContentScale.Crop,
                            modifier = Modifier.fillMaxSize()
                        )
                    }

                    Spacer(Modifier.width(16.dp))

                    Column(modifier = Modifier.weight(1f)) {
                        // Título
                        Text(
                            text = movie.title,
                            fontFamily = PermanentMarker,
                            fontSize = 22.sp,
                            color = ColorNegro,
                            lineHeight = 26.sp
                        )

                        Spacer(Modifier.height(8.dp))

                        if (movie.releaseDate.isNotEmpty()) {
                            Box(
                                modifier = Modifier
                                    .background(ColorAmarillo)
                                    .border(2.dp, ColorNegro)
                                    .padding(horizontal = 8.dp, vertical = 2.dp)
                            ) {
                                Text(
                                    text = movie.year(),
                                    fontFamily = SpaceGrotesk,
                                    fontWeight = FontWeight.Bold,
                                    fontSize = 13.sp
                                )
                            }
                        }

                        Spacer(Modifier.height(12.dp))

                        // Rating
                        if (movie.voteAverage > 0) {
                            Text(
                                text = "★ ${"%.1f".format(movie.voteAverage)}",
                                fontFamily = SpaceGrotesk,
                                fontWeight = FontWeight.ExtraBold,
                                fontSize = 16.sp,
                                color = ColorNegro
                            )
                        }

                        Spacer(Modifier.height(12.dp))

                        // Botón favorito
                        if (currentUser != null) {
                            Box {
                                Box(
                                    modifier = Modifier
                                        .matchParentSize()
                                        .offset(x = 3.dp, y = 3.dp)
                                        .background(ColorNegro)
                                )
                                androidx.compose.material3.Button(
                                    onClick = { viewModel.toggleFavorite() },
                                    shape = RectangleShape,
                                    colors = androidx.compose.material3.ButtonDefaults.buttonColors(
                                        containerColor = if (state.isFavorited) ColorRojo else ColorAmarillo,
                                        contentColor = if (state.isFavorited) Color.White else ColorNegro
                                    ),
                                    border = androidx.compose.foundation.BorderStroke(3.dp, ColorNegro),
                                    elevation = androidx.compose.material3.ButtonDefaults.buttonElevation(0.dp)
                                ) {
                                    Icon(
                                        if (state.isFavorited) Icons.Default.Favorite else Icons.Default.FavoriteBorder,
                                        contentDescription = "Favorito",
                                        modifier = Modifier.padding(end = 4.dp)
                                    )
                                    Text(
                                        if (state.isFavorited) "Favorito" else "Añadir",
                                        fontFamily = SpaceGrotesk,
                                        fontWeight = FontWeight.Bold,
                                        fontSize = 12.sp
                                    )
                                }
                            }
                        }
                    }
                }

                // Géneros
                if (movie.genres.isNotEmpty()) {
                    Spacer(Modifier.height(12.dp))
                    FlowRow(horizontalArrangement = Arrangement.spacedBy(8.dp), modifier = Modifier.fillMaxWidth()) {
                        movie.genres.forEachIndexed { i, genre ->
                            BrutalistTag(
                                text = genre.name,
                                background = if (i % 2 == 0) ColorAzul else ColorVerde,
                                rotation = if (i % 2 == 0) -2f else 2f,
                                modifier = Modifier.padding(bottom = 8.dp)
                            )
                        }
                    }
                }

                // Sinopsis
                if (movie.overview.isNotEmpty()) {
                    Spacer(Modifier.height(12.dp))
                    Row(modifier = Modifier.fillMaxWidth()) {
                        Box(
                            modifier = Modifier
                                .width(6.dp)
                                .height(80.dp)
                                .background(ColorRojo)
                        )
                        Spacer(Modifier.width(8.dp))
                        Text(
                            text = movie.overview,
                            fontFamily = SpaceGrotesk,
                            fontWeight = FontWeight.Bold,
                            fontSize = 13.sp,
                            color = ColorNegro,
                            modifier = Modifier.background(Color(0xFFFFF4F7)).padding(8.dp)
                        )
                    }
                }
            }
        }

        // Mensajes
        if (state.error != null) {
            BrutalistError(message = state.error!!, modifier = Modifier.padding(horizontal = 16.dp, vertical = 8.dp))
        }
        if (state.successMessage != null) {
            BrutalistSuccess(message = state.successMessage!!, modifier = Modifier.padding(horizontal = 16.dp, vertical = 8.dp))
        }

        Spacer(Modifier.height(16.dp))

        // Sección Foro
        Box(
            modifier = Modifier
                .fillMaxWidth()
                .padding(horizontal = 16.dp)
        ) {
            Box(
                modifier = Modifier
                    .matchParentSize()
                    .offset(x = 4.dp, y = 4.dp)
                    .background(ColorNegro)
            )
            Text(
                text = "💬 Foro",
                fontFamily = PermanentMarker,
                fontSize = 24.sp,
                color = ColorNegro,
                modifier = Modifier
                    .fillMaxWidth()
                    .background(ColorVerde)
                    .border(3.dp, ColorNegro)
                    .padding(horizontal = 16.dp, vertical = 8.dp)
            )
        }

        Spacer(Modifier.height(12.dp))

        // Botón crear post
        if (currentUser != null && !showForm) {
            BrutalistButton(
                text = "+ Crear post",
                onClick = { showForm = true },
                modifier = Modifier.padding(horizontal = 16.dp),
                background = ColorAcento,
                textColor = ColorNegro
            )
            Spacer(Modifier.height(16.dp))
        }

        // Formulario de post
        if (showForm && currentUser != null) {
            Box(
                modifier = Modifier
                    .fillMaxWidth()
                    .padding(horizontal = 16.dp)
            ) {
                Box(
                    modifier = Modifier
                        .matchParentSize()
                        .offset(x = 5.dp, y = 5.dp)
                        .background(ColorNegro)
                )
                Column(
                    modifier = Modifier
                        .fillMaxWidth()
                        .background(Color(0xFFFFFBEA))
                        .border(3.dp, ColorNegro)
                        .padding(16.dp)
                ) {
                    Text(
                        "NUEVO POST",
                        fontFamily = PermanentMarker,
                        fontSize = 20.sp,
                        color = ColorNegro
                    )
                    Spacer(Modifier.height(12.dp))

                    OutlinedTextField(
                        value = postTitle,
                        onValueChange = { postTitle = it },
                        label = {
                            Text("Título", fontFamily = SpaceGrotesk, fontWeight = FontWeight.Bold)
                        },
                        singleLine = true,
                        shape = RectangleShape,
                        colors = OutlinedTextFieldDefaults.colors(
                            focusedBorderColor = ColorNegro,
                            unfocusedBorderColor = ColorNegro,
                            focusedContainerColor = Color.White,
                            unfocusedContainerColor = Color.White
                        ),
                        modifier = Modifier.fillMaxWidth().border(3.dp, ColorNegro, RectangleShape)
                    )
                    Spacer(Modifier.height(12.dp))

                    OutlinedTextField(
                        value = postDescription,
                        onValueChange = { postDescription = it },
                        label = {
                            Text("Descripción", fontFamily = SpaceGrotesk, fontWeight = FontWeight.Bold)
                        },
                        minLines = 3,
                        maxLines = 6,
                        shape = RectangleShape,
                        colors = OutlinedTextFieldDefaults.colors(
                            focusedBorderColor = ColorNegro,
                            unfocusedBorderColor = ColorNegro,
                            focusedContainerColor = Color.White,
                            unfocusedContainerColor = Color.White
                        ),
                        modifier = Modifier.fillMaxWidth().border(3.dp, ColorNegro, RectangleShape)
                    )

                    Spacer(Modifier.height(12.dp))

                    // Selector imagen
                    Row(verticalAlignment = Alignment.CenterVertically) {
                        BrutalistButton(
                            text = if (selectedImageUri != null) "Imagen ✓" else "Añadir imagen",
                            onClick = { imagePicker.launch("image/*") },
                            modifier = Modifier.weight(1f),
                            background = if (selectedImageUri != null) ColorVerde else Color.White,
                            textColor = ColorNegro
                        )
                    }

                    Spacer(Modifier.height(16.dp))

                    Row(horizontalArrangement = Arrangement.spacedBy(12.dp)) {
                        BrutalistButton(
                            text = if (state.isPostLoading) "Enviando..." else "Publicar",
                            onClick = {
                                viewModel.createPost(postTitle, postDescription, selectedImageUri)
                            },
                            enabled = !state.isPostLoading,
                            modifier = Modifier.weight(1f)
                        )
                        BrutalistButton(
                            text = "Cancelar",
                            onClick = { showForm = false; viewModel.clearMessages() },
                            modifier = Modifier.weight(1f),
                            background = Color.White,
                            textColor = ColorNegro
                        )
                    }
                }
            }
            Spacer(Modifier.height(16.dp))
        }

        // Lista de posts aprobados
        if (state.posts.isEmpty()) {
            Box(
                modifier = Modifier
                    .fillMaxWidth()
                    .padding(horizontal = 16.dp)
                    .background(ColorAmarillo)
                    .border(3.dp, ColorNegro)
                    .padding(24.dp),
                contentAlignment = Alignment.Center
            ) {
                Text(
                    "Sé el primero en comentar esta película",
                    fontFamily = SpaceGrotesk,
                    fontWeight = FontWeight.Bold,
                    fontSize = 14.sp,
                    color = ColorNegro
                )
            }
        } else {
            state.posts.forEach { post ->
                PostCard(
                    post = post,
                    canDelete = state.isModerator || currentUser?.uid == post.userId,
                    onDelete = { viewModel.deletePost(post) },
                    modifier = Modifier.padding(horizontal = 16.dp, vertical = 8.dp)
                )
            }
        }

        Spacer(Modifier.height(32.dp))
    }
}
