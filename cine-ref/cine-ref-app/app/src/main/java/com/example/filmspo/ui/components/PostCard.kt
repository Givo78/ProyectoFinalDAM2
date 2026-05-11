package com.example.filmspo.ui.components

import androidx.compose.foundation.background
import androidx.compose.foundation.border
import androidx.compose.foundation.layout.Box
import androidx.compose.foundation.layout.Column
import androidx.compose.foundation.layout.Row
import androidx.compose.foundation.layout.Spacer
import androidx.compose.foundation.layout.fillMaxWidth
import androidx.compose.foundation.layout.height
import androidx.compose.foundation.layout.offset
import androidx.compose.foundation.layout.padding
import androidx.compose.foundation.layout.width
import androidx.compose.material.icons.Icons
import androidx.compose.material.icons.filled.Delete
import androidx.compose.material3.Icon
import androidx.compose.material3.IconButton
import androidx.compose.material3.Text
import androidx.compose.runtime.Composable
import androidx.compose.ui.Alignment
import androidx.compose.ui.Modifier
import androidx.compose.ui.graphics.Color
import androidx.compose.ui.layout.ContentScale
import androidx.compose.ui.text.font.FontWeight
import androidx.compose.ui.text.style.TextOverflow
import androidx.compose.ui.unit.dp
import androidx.compose.ui.unit.sp
import coil.compose.AsyncImage
import com.example.filmspo.data.model.Post
import com.example.filmspo.ui.theme.ColorAmarillo
import com.example.filmspo.ui.theme.ColorBlanco
import com.example.filmspo.ui.theme.ColorNegro
import com.example.filmspo.ui.theme.ColorRojo
import com.example.filmspo.ui.theme.PermanentMarker
import com.example.filmspo.ui.theme.SpaceGrotesk
import java.text.SimpleDateFormat
import java.util.Date
import java.util.Locale

@Composable
fun PostCard(
    post: Post,
    canDelete: Boolean = false,
    onDelete: () -> Unit = {},
    modifier: Modifier = Modifier
) {
    Box(modifier = modifier) {
        // Sombra
        Box(
            modifier = Modifier
                .matchParentSize()
                .offset(x = 5.dp, y = 5.dp)
                .background(ColorNegro)
        )
        Column(
            modifier = Modifier
                .fillMaxWidth()
                .background(ColorBlanco)
                .border(3.dp, ColorNegro)
                .padding(16.dp)
        ) {
            Row(
                modifier = Modifier.fillMaxWidth(),
                verticalAlignment = Alignment.CenterVertically
            ) {
                BrutalistAvatar(
                    initial = post.username.firstOrNull()?.uppercaseChar()?.toString() ?: "?",
                    size = 36.dp,
                    background = ColorAmarillo
                )
                Spacer(Modifier.width(8.dp))
                Column(modifier = Modifier.weight(1f)) {
                    Text(
                        text = post.username,
                        fontFamily = SpaceGrotesk,
                        fontWeight = FontWeight.ExtraBold,
                        fontSize = 13.sp,
                        color = ColorNegro
                    )
                    Text(
                        text = formatDate(post.createdAt),
                        fontFamily = SpaceGrotesk,
                        fontWeight = FontWeight.Normal,
                        fontSize = 11.sp,
                        color = Color(0xFF555555)
                    )
                }
                if (canDelete) {
                    IconButton(onClick = onDelete) {
                        Icon(
                            Icons.Default.Delete,
                            contentDescription = "Eliminar post",
                            tint = ColorRojo
                        )
                    }
                }
            }

            Spacer(Modifier.height(12.dp))

            // Título post con borde izquierdo rojo
            Box(
                modifier = Modifier
                    .fillMaxWidth()
                    .border(
                        width = 0.dp,
                        color = Color.Transparent
                    )
            ) {
                Row {
                    Box(
                        modifier = Modifier
                            .width(6.dp)
                            .height(24.dp)
                            .background(ColorRojo)
                    )
                    Spacer(Modifier.width(8.dp))
                    Text(
                        text = post.title,
                        fontFamily = PermanentMarker,
                        fontWeight = FontWeight.Normal,
                        fontSize = 18.sp,
                        color = ColorNegro
                    )
                }
            }

            Spacer(Modifier.height(8.dp))

            Text(
                text = post.description,
                fontFamily = SpaceGrotesk,
                fontWeight = FontWeight.Normal,
                fontSize = 14.sp,
                color = Color(0xFF333333),
                maxLines = 5,
                overflow = TextOverflow.Ellipsis
            )

            if (post.imageUrl.isNotEmpty()) {
                Spacer(Modifier.height(12.dp))
                Box(
                    modifier = Modifier
                        .fillMaxWidth()
                        .height(180.dp)
                        .background(Color(0xFF00E5FF))
                        .border(3.dp, ColorNegro)
                ) {
                    AsyncImage(
                        model = post.imageUrl,
                        contentDescription = "Imagen del post",
                        contentScale = ContentScale.Crop,
                        modifier = Modifier.fillMaxWidth()
                    )
                }
            }
        }
    }
}

private fun formatDate(timestamp: Long): String {
    if (timestamp == 0L) return ""
    return try {
        val sdf = SimpleDateFormat("dd MMM yyyy", Locale("es"))
        sdf.format(Date(timestamp))
    } catch (e: Exception) {
        ""
    }
}
