package com.example.filmspo.ui.components

import androidx.compose.foundation.background
import androidx.compose.foundation.border
import androidx.compose.foundation.clickable
import androidx.compose.foundation.layout.Box
import androidx.compose.foundation.layout.Column
import androidx.compose.foundation.layout.aspectRatio
import androidx.compose.foundation.layout.fillMaxWidth
import androidx.compose.foundation.layout.offset
import androidx.compose.foundation.layout.padding
import androidx.compose.ui.graphics.RectangleShape
import androidx.compose.material3.Text
import androidx.compose.runtime.Composable
import androidx.compose.ui.Modifier
import androidx.compose.ui.draw.clip
import androidx.compose.ui.graphics.Color
import androidx.compose.ui.layout.ContentScale
import androidx.compose.ui.text.font.FontWeight
import androidx.compose.ui.text.style.TextOverflow
import androidx.compose.ui.unit.dp
import androidx.compose.ui.unit.sp
import coil.compose.AsyncImage
import com.example.filmspo.data.model.Movie
import com.example.filmspo.ui.theme.ColorAzul
import com.example.filmspo.ui.theme.ColorBlanco
import com.example.filmspo.ui.theme.ColorNegro
import com.example.filmspo.ui.theme.PermanentMarker
import com.example.filmspo.ui.theme.SpaceGrotesk

@Composable
fun MovieCard(
    movie: Movie,
    onClick: () -> Unit,
    modifier: Modifier = Modifier,
    cardBackground: Color = ColorBlanco
) {
    Box(modifier = modifier.clickable(onClick = onClick)) {
        // Sombra desplazada estilo brutalist
        Box(
            modifier = Modifier
                .matchParentSize()
                .offset(x = 5.dp, y = 5.dp)
                .background(ColorNegro)
        )
        Column(
            modifier = Modifier
                .fillMaxWidth()
                .background(cardBackground)
                .border(3.dp, ColorNegro, RectangleShape)
                .padding(12.dp)
        ) {
            // Poster
            Box(
                modifier = Modifier
                    .fillMaxWidth()
                    .aspectRatio(2f / 3f)
                    .background(ColorAzul)
                    .border(3.dp, ColorNegro)
            ) {
                AsyncImage(
                    model = movie.posterUrl(),
                    contentDescription = movie.title,
                    contentScale = ContentScale.Crop,
                    modifier = Modifier
                        .fillMaxWidth()
                        .clip(RectangleShape)
                )
            }

            // Título
            Text(
                text = movie.title.uppercase(),
                fontFamily = SpaceGrotesk,
                fontWeight = FontWeight.ExtraBold,
                fontSize = 13.sp,
                color = ColorNegro,
                maxLines = 2,
                overflow = TextOverflow.Ellipsis,
                modifier = Modifier.padding(top = 8.dp)
            )

            // Año
            if (movie.year().isNotEmpty()) {
                Text(
                    text = movie.year(),
                    fontFamily = SpaceGrotesk,
                    fontWeight = FontWeight.Normal,
                    fontSize = 11.sp,
                    color = Color(0xFF555555),
                    modifier = Modifier.padding(top = 2.dp)
                )
            }
        }
    }
}
