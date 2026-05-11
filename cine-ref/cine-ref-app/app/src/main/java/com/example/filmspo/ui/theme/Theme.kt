package com.example.filmspo.ui.theme

import androidx.compose.material3.MaterialTheme
import androidx.compose.material3.lightColorScheme
import androidx.compose.runtime.Composable

private val FilmspoColorScheme = lightColorScheme(
    primary = ColorAcento,
    onPrimary = ColorNegro,
    primaryContainer = ColorAmarillo,
    onPrimaryContainer = ColorNegro,
    secondary = ColorAmarillo,
    onSecondary = ColorNegro,
    secondaryContainer = ColorAzul,
    onSecondaryContainer = ColorNegro,
    tertiary = ColorVerde,
    onTertiary = ColorNegro,
    background = ColorFondo,
    onBackground = ColorNegro,
    surface = ColorBlanco,
    onSurface = ColorNegro,
    error = ColorRojo,
    onError = ColorBlanco,
)

@Composable
fun FilmspoTheme(content: @Composable () -> Unit) {
    MaterialTheme(
        colorScheme = FilmspoColorScheme,
        typography = Typography,
        content = content
    )
}
