package com.example.filmspo.ui.screens

import androidx.compose.foundation.background
import androidx.compose.foundation.border
import androidx.compose.foundation.layout.Arrangement
import androidx.compose.foundation.layout.Box
import androidx.compose.foundation.layout.Column
import androidx.compose.foundation.layout.PaddingValues
import androidx.compose.foundation.layout.Row
import androidx.compose.foundation.layout.Spacer
import androidx.compose.foundation.layout.fillMaxSize
import androidx.compose.foundation.layout.fillMaxWidth
import androidx.compose.foundation.layout.height
import androidx.compose.foundation.layout.offset
import androidx.compose.foundation.layout.padding
import androidx.compose.foundation.layout.size
import androidx.compose.foundation.layout.width
import androidx.compose.foundation.lazy.grid.GridCells
import androidx.compose.foundation.lazy.grid.LazyVerticalGrid
import androidx.compose.foundation.lazy.grid.itemsIndexed
import androidx.compose.ui.draw.rotate
import androidx.compose.ui.graphics.RectangleShape
import androidx.compose.foundation.text.KeyboardActions
import androidx.compose.foundation.text.KeyboardOptions
import androidx.compose.material.icons.Icons
import androidx.compose.material.icons.filled.Clear
import androidx.compose.material.icons.filled.Search
import androidx.compose.material3.CircularProgressIndicator
import androidx.compose.material3.Icon
import androidx.compose.material3.IconButton
import androidx.compose.material3.OutlinedTextField
import androidx.compose.material3.OutlinedTextFieldDefaults
import androidx.compose.material3.Text
import androidx.compose.runtime.Composable
import androidx.compose.runtime.collectAsState
import androidx.compose.runtime.getValue
import androidx.compose.ui.Alignment
import androidx.compose.ui.Modifier
import androidx.compose.ui.draw.drawBehind
import androidx.compose.ui.geometry.Offset
import androidx.compose.ui.graphics.Color
import androidx.compose.ui.platform.LocalFocusManager
import androidx.compose.ui.text.font.FontWeight
import androidx.compose.ui.text.input.ImeAction
import androidx.compose.ui.unit.dp
import androidx.compose.ui.unit.sp
import androidx.lifecycle.viewmodel.compose.viewModel
import com.example.filmspo.ui.components.MovieCard
import com.example.filmspo.ui.theme.ColorAcento
import com.example.filmspo.ui.theme.ColorAmarillo
import com.example.filmspo.ui.theme.ColorFondo
import com.example.filmspo.ui.theme.ColorNegro
import com.example.filmspo.ui.theme.ColorVerde
import com.example.filmspo.ui.theme.PermanentMarker
import com.example.filmspo.ui.theme.SpaceGrotesk
import com.example.filmspo.viewmodel.HomeViewModel

@Composable
fun HomeScreen(
    onMovieClick: (Int) -> Unit,
    viewModel: HomeViewModel = viewModel()
) {
    val state by viewModel.state.collectAsState()
    val focusManager = LocalFocusManager.current

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
                .padding(horizontal = 16.dp, vertical = 12.dp)
        ) {
            Row(
                verticalAlignment = Alignment.CenterVertically,
                modifier = Modifier.fillMaxWidth()
            ) {
                Text(
                    text = "FILMSPO",
                    fontFamily = PermanentMarker,
                    fontSize = 26.sp,
                    color = ColorNegro
                )
                Spacer(Modifier.weight(1f))
            }
        }

        // Barra de búsqueda
        Box(
            modifier = Modifier
                .fillMaxWidth()
                .padding(16.dp)
        ) {
            Box(
                modifier = Modifier
                    .matchParentSize()
                    .offset(x = 3.dp, y = 3.dp)
                    .background(ColorNegro)
            )
            OutlinedTextField(
                value = state.searchQuery,
                onValueChange = viewModel::onSearchQueryChange,
                placeholder = {
                    Text(
                        "Buscar película...",
                        fontFamily = SpaceGrotesk,
                        fontWeight = FontWeight.Bold,
                        color = Color(0xFF555555)
                    )
                },
                leadingIcon = {
                    Icon(Icons.Default.Search, contentDescription = null, tint = ColorNegro)
                },
                trailingIcon = {
                    if (state.searchQuery.isNotEmpty()) {
                        IconButton(onClick = {
                            viewModel.clearSearch()
                            focusManager.clearFocus()
                        }) {
                            Icon(Icons.Default.Clear, contentDescription = "Limpiar", tint = ColorNegro)
                        }
                    }
                },
                singleLine = true,
                shape = RectangleShape,
                colors = OutlinedTextFieldDefaults.colors(
                    focusedBorderColor = ColorNegro,
                    unfocusedBorderColor = ColorNegro,
                    focusedContainerColor = ColorAmarillo,
                    unfocusedContainerColor = Color.White
                ),
                keyboardOptions = KeyboardOptions(imeAction = ImeAction.Search),
                keyboardActions = KeyboardActions(onSearch = { focusManager.clearFocus() }),
                modifier = Modifier
                    .fillMaxWidth()
                    .border(3.dp, ColorNegro, RectangleShape)
            )
        }

        if (state.searchQuery.isNotEmpty()) {
            Box(
                modifier = Modifier
                    .padding(horizontal = 16.dp, vertical = 4.dp)
                    .rotate(-1f)
            ) {
                Box(
                    modifier = Modifier
                        .matchParentSize()
                        .offset(x = 4.dp, y = 4.dp)
                        .background(ColorNegro)
                )
                Text(
                    text = "Resultados: \"${state.searchQuery}\"".uppercase(),
                    fontFamily = PermanentMarker,
                    fontSize = 18.sp,
                    color = ColorNegro,
                    modifier = Modifier
                        .background(ColorAmarillo)
                        .border(3.dp, ColorNegro)
                        .padding(horizontal = 10.dp, vertical = 4.dp)
                )
            }
        } else {
            Box(
                modifier = Modifier
                    .padding(horizontal = 16.dp, vertical = 4.dp)
                    .rotate(1f)
            ) {
                Box(
                    modifier = Modifier
                        .matchParentSize()
                        .offset(x = 4.dp, y = 4.dp)
                        .background(ColorNegro)
                )
                Text(
                    text = "Selección de Inspiración Visual",
                    fontFamily = PermanentMarker,
                    fontSize = 22.sp,
                    color = ColorNegro,
                    modifier = Modifier
                        .background(ColorVerde)
                        .border(3.dp, ColorNegro)
                        .padding(horizontal = 10.dp, vertical = 4.dp)
                )
            }
        }

        Spacer(Modifier.height(8.dp))

        when {
            state.isLoading -> {
                Box(Modifier.fillMaxSize(), contentAlignment = Alignment.Center) {
                    Column(horizontalAlignment = Alignment.CenterHorizontally) {
                        CircularProgressIndicator(color = ColorAcento, strokeWidth = 4.dp)
                        Spacer(Modifier.height(16.dp))
                        Text("Cargando...", fontFamily = SpaceGrotesk, fontWeight = FontWeight.Bold)
                    }
                }
            }
            state.movies.isEmpty() -> {
                Box(
                    modifier = Modifier
                        .fillMaxWidth()
                        .padding(24.dp)
                        .background(ColorAmarillo)
                        .border(3.dp, ColorNegro)
                        .padding(32.dp),
                    contentAlignment = Alignment.Center
                ) {
                    Text(
                        text = "No se encontraron películas",
                        fontFamily = PermanentMarker,
                        fontSize = 20.sp,
                        color = ColorNegro
                    )
                }
            }
            else -> {
                LazyVerticalGrid(
                    columns = GridCells.Fixed(2),
                    contentPadding = PaddingValues(16.dp),
                    horizontalArrangement = Arrangement.spacedBy(16.dp),
                    verticalArrangement = Arrangement.spacedBy(20.dp),
                    modifier = Modifier.fillMaxSize()
                ) {
                    val backgrounds = listOf(
                        Color.White,
                        Color(0xFFFFFBEA),
                        Color(0xFFF0FBFF),
                        Color(0xFFFCF0FF)
                    )
                    itemsIndexed(state.movies, key = { _, movie -> movie.id }) { index, movie ->
                        MovieCard(
                            movie = movie,
                            onClick = { onMovieClick(movie.id) },
                            cardBackground = backgrounds[index % backgrounds.size],
                            modifier = Modifier.rotate(if (index % 2 == 0) -1.5f else 1.5f)
                        )
                    }
                }
            }
        }
    }
}
