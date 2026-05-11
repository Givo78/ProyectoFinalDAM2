package com.example.filmspo.ui.screens

import androidx.compose.foundation.background
import androidx.compose.foundation.border
import androidx.compose.foundation.clickable
import androidx.compose.foundation.layout.Arrangement
import androidx.compose.foundation.layout.Box
import androidx.compose.foundation.layout.Column
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
import androidx.compose.foundation.verticalScroll
import androidx.compose.material.icons.Icons
import androidx.compose.material.icons.filled.Delete
import androidx.compose.material.icons.filled.Edit
import androidx.compose.material.icons.filled.Shield
import androidx.compose.material3.CircularProgressIndicator
import androidx.compose.material3.Icon
import androidx.compose.material3.IconButton
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
import androidx.compose.ui.draw.drawBehind
import androidx.compose.ui.geometry.Offset
import androidx.compose.ui.graphics.Color
import androidx.compose.ui.layout.ContentScale
import androidx.compose.ui.text.font.FontWeight
import androidx.compose.ui.unit.dp
import androidx.compose.ui.unit.sp
import androidx.lifecycle.viewmodel.compose.viewModel
import coil.compose.AsyncImage
import com.example.filmspo.ui.components.BrutalistAvatar
import com.example.filmspo.ui.components.BrutalistButton
import com.example.filmspo.ui.components.BrutalistError
import com.example.filmspo.ui.components.BrutalistSuccess
import com.example.filmspo.ui.components.BrutalistTextField
import com.example.filmspo.ui.theme.ColorAcento
import com.example.filmspo.ui.theme.ColorAmarillo
import com.example.filmspo.ui.theme.ColorAzul
import com.example.filmspo.ui.theme.ColorFondo
import com.example.filmspo.ui.theme.ColorNegro
import com.example.filmspo.ui.theme.ColorRojo
import com.example.filmspo.ui.theme.ColorVerde
import com.example.filmspo.ui.theme.PermanentMarker
import com.example.filmspo.ui.theme.SpaceGrotesk
import com.example.filmspo.viewmodel.AuthViewModel
import com.example.filmspo.viewmodel.ProfileViewModel
import kotlinx.coroutines.delay

@Composable
fun ProfileScreen(
    onNavigateToModeration: () -> Unit,
    onLogout: () -> Unit,
    onMovieClick: (Int) -> Unit,
    profileViewModel: ProfileViewModel = viewModel(),
    authViewModel: AuthViewModel = viewModel()
) {
    val state by profileViewModel.state.collectAsState()
    var editingName by remember { mutableStateOf(false) }
    var newName by remember { mutableStateOf("") }

    LaunchedEffect(state.successMessage) {
        if (state.successMessage != null) {
            delay(2000)
            profileViewModel.clearMessages()
            editingName = false
        }
    }

    Column(
        modifier = Modifier
            .fillMaxSize()
            .background(ColorFondo)
            .drawBehind {
                val dotColor = Color(0x15000000)
                val spacing = 20.dp.toPx()
                var x = 0f
                while (x < size.width) {
                    var y = 0f
                    while (y < size.height) {
                        drawCircle(dotColor, radius = 1.5f, center = Offset(x, y))
                        y += spacing
                    }
                    x += spacing
                }
            }
            .verticalScroll(rememberScrollState())
    ) {
        // Navbar
        Box(
            modifier = Modifier
                .fillMaxWidth()
                .background(ColorAcento)
                .border(width = 3.dp, color = ColorNegro)
                .padding(horizontal = 16.dp, vertical = 12.dp)
        ) {
            Text(
                text = "MI PERFIL",
                fontFamily = PermanentMarker,
                fontSize = 24.sp,
                color = ColorNegro
            )
        }

        Spacer(Modifier.height(24.dp))

        if (state.isLoading) {
            Box(Modifier.fillMaxWidth(), contentAlignment = Alignment.Center) {
                CircularProgressIndicator(color = ColorAcento)
            }
        } else {
            val user = state.user

            // Avatar + info
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
                        .background(Color.White)
                        .border(3.dp, ColorNegro)
                        .padding(20.dp),
                    horizontalAlignment = Alignment.CenterHorizontally
                ) {
                    BrutalistAvatar(
                        initial = user?.initial() ?: "?",
                        size = 72.dp,
                        background = ColorAmarillo
                    )

                    Spacer(Modifier.height(16.dp))

                    Text(
                        text = user?.displayName ?: "Usuario",
                        fontFamily = PermanentMarker,
                        fontSize = 26.sp,
                        color = ColorNegro
                    )

                    Text(
                        text = user?.email ?: "",
                        fontFamily = SpaceGrotesk,
                        fontWeight = FontWeight.Normal,
                        fontSize = 13.sp,
                        color = Color(0xFF555555)
                    )

                    if (user?.isModerator() == true) {
                        Spacer(Modifier.height(8.dp))
                        Box(
                            modifier = Modifier
                                .background(ColorAzul)
                                .border(2.dp, ColorNegro)
                                .padding(horizontal = 10.dp, vertical = 4.dp)
                        ) {
                            Row(verticalAlignment = Alignment.CenterVertically) {
                                Icon(Icons.Default.Shield, contentDescription = null, tint = ColorNegro)
                                Spacer(Modifier.width(4.dp))
                                Text(
                                    "MODERADOR",
                                    fontFamily = SpaceGrotesk,
                                    fontWeight = FontWeight.ExtraBold,
                                    fontSize = 12.sp
                                )
                            }
                        }
                    }

                    Spacer(Modifier.height(16.dp))

                    // Editar nombre
                    if (!editingName) {
                        BrutalistButton(
                            text = "Editar nombre",
                            onClick = {
                                editingName = true
                                newName = user?.displayName ?: ""
                            },
                            background = ColorAmarillo,
                            textColor = ColorNegro
                        )
                    } else {
                        BrutalistTextField(
                            value = newName,
                            onValueChange = { newName = it },
                            label = "Nuevo nombre"
                        )
                        Spacer(Modifier.height(12.dp))
                        Row(horizontalArrangement = Arrangement.spacedBy(8.dp)) {
                            BrutalistButton(
                                text = if (state.isSaving) "Guardando..." else "Guardar",
                                onClick = { profileViewModel.updateName(newName) },
                                enabled = !state.isSaving,
                                modifier = Modifier.weight(1f)
                            )
                            BrutalistButton(
                                text = "Cancelar",
                                onClick = { editingName = false },
                                modifier = Modifier.weight(1f),
                                background = Color.White,
                                textColor = ColorNegro
                            )
                        }
                    }
                }
            }

            // Mensajes
            if (state.error != null) {
                BrutalistError(state.error!!, modifier = Modifier.padding(horizontal = 16.dp, vertical = 8.dp))
            }
            if (state.successMessage != null) {
                BrutalistSuccess(state.successMessage!!, modifier = Modifier.padding(horizontal = 16.dp, vertical = 8.dp))
            }

            Spacer(Modifier.height(24.dp))

            // Favoritos
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
                    text = "❤ MIS FAVORITOS (${state.favorites.size}/3)",
                    fontFamily = PermanentMarker,
                    fontSize = 20.sp,
                    color = ColorNegro,
                    modifier = Modifier
                        .fillMaxWidth()
                        .background(ColorAcento)
                        .border(3.dp, ColorNegro)
                        .padding(horizontal = 16.dp, vertical = 8.dp)
                )
            }

            Spacer(Modifier.height(12.dp))

            if (state.favorites.isEmpty()) {
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
                        "Aún no tienes favoritos",
                        fontFamily = SpaceGrotesk,
                        fontWeight = FontWeight.Bold,
                        color = ColorNegro
                    )
                }
            } else {
                Row(
                    modifier = Modifier
                        .fillMaxWidth()
                        .padding(horizontal = 16.dp),
                    horizontalArrangement = Arrangement.spacedBy(12.dp)
                ) {
                    state.favorites.forEach { fav ->
                        Box(modifier = Modifier.weight(1f)) {
                            Box(
                                modifier = Modifier
                                    .matchParentSize()
                                    .offset(x = 4.dp, y = 4.dp)
                                    .background(ColorNegro)
                            )
                            Column(
                                modifier = Modifier
                                    .fillMaxWidth()
                                    .background(Color.White)
                                    .border(3.dp, ColorNegro)
                            ) {
                                Box(
                                    modifier = Modifier
                                        .fillMaxWidth()
                                        .aspectRatio(2f / 3f)
                                        .background(ColorAzul)
                                        .border(width = 0.dp, color = Color.Transparent)
                                ) {
                                    AsyncImage(
                                        model = fav.posterUrl(),
                                        contentDescription = fav.title,
                                        contentScale = ContentScale.Crop,
                                        modifier = Modifier.fillMaxSize()
                                    )
                                    IconButton(
                                        onClick = { profileViewModel.removeFavorite(fav.id) },
                                        modifier = Modifier
                                            .align(Alignment.TopEnd)
                                            .background(ColorRojo)
                                    ) {
                                        Icon(
                                            Icons.Default.Delete,
                                            contentDescription = "Quitar favorito",
                                            tint = Color.White
                                        )
                                    }
                                }
                                Text(
                                    text = fav.title,
                                    fontFamily = SpaceGrotesk,
                                    fontWeight = FontWeight.ExtraBold,
                                    fontSize = 11.sp,
                                    color = ColorNegro,
                                    maxLines = 2,
                                    modifier = Modifier.padding(8.dp)
                                )
                            }
                        }
                    }
                    // Espacios vacíos para rellenar hasta 3
                    repeat(3 - state.favorites.size) {
                        Spacer(Modifier.weight(1f))
                    }
                }
            }

            Spacer(Modifier.height(24.dp))

            // Botón moderación
            if (state.user?.isModerator() == true) {
                BrutalistButton(
                    text = "Panel de Moderación",
                    onClick = onNavigateToModeration,
                    modifier = Modifier.padding(horizontal = 16.dp),
                    background = ColorAzul,
                    textColor = ColorNegro
                )
                Spacer(Modifier.height(12.dp))
            }

            // Cerrar sesión
            BrutalistButton(
                text = "Cerrar Sesión",
                onClick = {
                    authViewModel.logout()
                    onLogout()
                },
                modifier = Modifier.padding(horizontal = 16.dp),
                background = ColorRojo,
                textColor = Color.White
            )

            Spacer(Modifier.height(32.dp))
        }
    }
}
