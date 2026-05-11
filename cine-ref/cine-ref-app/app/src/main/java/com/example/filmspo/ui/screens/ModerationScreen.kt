package com.example.filmspo.ui.screens

import androidx.compose.foundation.background
import androidx.compose.foundation.border
import androidx.compose.foundation.layout.Arrangement
import androidx.compose.foundation.layout.Box
import androidx.compose.foundation.layout.Column
import androidx.compose.foundation.layout.Row
import androidx.compose.foundation.layout.Spacer
import androidx.compose.foundation.layout.fillMaxSize
import androidx.compose.foundation.layout.fillMaxWidth
import androidx.compose.foundation.layout.height
import androidx.compose.foundation.layout.offset
import androidx.compose.foundation.layout.padding
import androidx.compose.foundation.lazy.LazyColumn
import androidx.compose.foundation.lazy.items
import androidx.compose.material.icons.Icons
import androidx.compose.material.icons.filled.ArrowBack
import androidx.compose.material3.CircularProgressIndicator
import androidx.compose.material3.Icon
import androidx.compose.material3.IconButton
import androidx.compose.material3.Text
import androidx.compose.runtime.Composable
import androidx.compose.runtime.LaunchedEffect
import androidx.compose.runtime.collectAsState
import androidx.compose.runtime.getValue
import androidx.compose.ui.Alignment
import androidx.compose.ui.Modifier
import androidx.compose.ui.draw.drawBehind
import androidx.compose.ui.geometry.Offset
import androidx.compose.ui.graphics.Color
import androidx.compose.ui.text.font.FontWeight
import androidx.compose.ui.unit.dp
import androidx.compose.ui.unit.sp
import androidx.lifecycle.viewmodel.compose.viewModel
import com.example.filmspo.ui.components.BrutalistButton
import com.example.filmspo.ui.components.BrutalistError
import com.example.filmspo.ui.components.BrutalistSuccess
import com.example.filmspo.ui.components.PostCard
import com.example.filmspo.ui.theme.ColorAcento
import com.example.filmspo.ui.theme.ColorAmarillo
import com.example.filmspo.ui.theme.ColorFondo
import com.example.filmspo.ui.theme.ColorNegro
import com.example.filmspo.ui.theme.ColorRojo
import com.example.filmspo.ui.theme.ColorVerde
import com.example.filmspo.ui.theme.PermanentMarker
import com.example.filmspo.ui.theme.SpaceGrotesk
import com.example.filmspo.viewmodel.ModerationViewModel
import kotlinx.coroutines.delay

@Composable
fun ModerationScreen(
    onBack: () -> Unit,
    viewModel: ModerationViewModel = viewModel()
) {
    val state by viewModel.state.collectAsState()

    LaunchedEffect(state.message) {
        if (state.message != null) {
            delay(1500)
            viewModel.clearMessage()
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
    ) {
        // Navbar
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
                    text = "MODERACIÓN",
                    fontFamily = PermanentMarker,
                    fontSize = 22.sp,
                    color = ColorNegro
                )
            }
        }

        // Mensajes
        if (state.error != null) {
            BrutalistError(state.error!!, modifier = Modifier.padding(horizontal = 16.dp, vertical = 8.dp))
        }
        if (state.message != null) {
            BrutalistSuccess(state.message!!, modifier = Modifier.padding(horizontal = 16.dp, vertical = 8.dp))
        }

        Box(
            modifier = Modifier
                .fillMaxWidth()
                .padding(horizontal = 16.dp, vertical = 12.dp)
        ) {
            Box(
                modifier = Modifier
                    .matchParentSize()
                    .offset(x = 4.dp, y = 4.dp)
                    .background(ColorNegro)
            )
            Text(
                text = "Posts pendientes (${state.pendingPosts.size})",
                fontFamily = PermanentMarker,
                fontSize = 20.sp,
                color = ColorNegro,
                modifier = Modifier
                    .fillMaxWidth()
                    .background(ColorAmarillo)
                    .border(3.dp, ColorNegro)
                    .padding(horizontal = 16.dp, vertical = 8.dp)
            )
        }

        when {
            state.isLoading -> {
                Box(Modifier.fillMaxSize(), contentAlignment = Alignment.Center) {
                    CircularProgressIndicator(color = ColorAcento)
                }
            }
            state.pendingPosts.isEmpty() -> {
                Box(
                    modifier = Modifier
                        .fillMaxWidth()
                        .padding(horizontal = 16.dp)
                        .background(ColorVerde)
                        .border(3.dp, ColorNegro)
                        .padding(32.dp),
                    contentAlignment = Alignment.Center
                ) {
                    Text(
                        "No hay posts pendientes",
                        fontFamily = PermanentMarker,
                        fontSize = 20.sp,
                        color = ColorNegro
                    )
                }
            }
            else -> {
                LazyColumn(
                    modifier = Modifier.fillMaxSize(),
                    verticalArrangement = Arrangement.spacedBy(16.dp),
                ) {
                    items(state.pendingPosts, key = { it.id }) { post ->
                        Column(modifier = Modifier.padding(horizontal = 16.dp)) {
                            PostCard(post = post)

                            Spacer(Modifier.height(8.dp))

                            Row(
                                horizontalArrangement = Arrangement.spacedBy(12.dp),
                                modifier = Modifier.fillMaxWidth()
                            ) {
                                BrutalistButton(
                                    text = "✓ Aprobar",
                                    onClick = { viewModel.approvePost(post) },
                                    modifier = Modifier.weight(1f),
                                    background = ColorVerde,
                                    textColor = ColorNegro
                                )
                                BrutalistButton(
                                    text = "✗ Rechazar",
                                    onClick = { viewModel.rejectPost(post) },
                                    modifier = Modifier.weight(1f),
                                    background = ColorRojo,
                                    textColor = Color.White
                                )
                            }
                        }
                    }
                    item { Spacer(Modifier.height(24.dp)) }
                }
            }
        }
    }
}
