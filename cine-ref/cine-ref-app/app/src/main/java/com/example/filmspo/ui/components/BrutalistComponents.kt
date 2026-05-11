package com.example.filmspo.ui.components

import androidx.compose.foundation.background
import androidx.compose.foundation.border
import androidx.compose.foundation.layout.Box
import androidx.compose.foundation.layout.PaddingValues
import androidx.compose.foundation.layout.fillMaxWidth
import androidx.compose.foundation.layout.height
import androidx.compose.foundation.layout.offset
import androidx.compose.foundation.layout.padding
import androidx.compose.ui.graphics.RectangleShape
import androidx.compose.foundation.text.KeyboardActions
import androidx.compose.foundation.text.KeyboardOptions
import androidx.compose.material3.Button
import androidx.compose.material3.ButtonDefaults
import androidx.compose.material3.OutlinedTextField
import androidx.compose.material3.OutlinedTextFieldDefaults
import androidx.compose.material3.Text
import androidx.compose.runtime.Composable
import androidx.compose.ui.Alignment
import androidx.compose.ui.Modifier
import androidx.compose.ui.graphics.Color
import androidx.compose.ui.text.font.FontWeight
import androidx.compose.ui.text.input.VisualTransformation
import androidx.compose.ui.unit.Dp
import androidx.compose.ui.unit.dp
import androidx.compose.ui.unit.sp
import com.example.filmspo.ui.theme.ColorAmarillo
import com.example.filmspo.ui.theme.ColorAcento
import com.example.filmspo.ui.theme.ColorAzul
import com.example.filmspo.ui.theme.ColorNegro
import com.example.filmspo.ui.theme.ColorBlanco
import com.example.filmspo.ui.theme.ColorRojo
import com.example.filmspo.ui.theme.SpaceGrotesk

// Botón brutalist primario (negro)
@Composable
fun BrutalistButton(
    text: String,
    onClick: () -> Unit,
    modifier: Modifier = Modifier,
    background: Color = ColorNegro,
    textColor: Color = ColorBlanco,
    enabled: Boolean = true
) {
    Box(modifier = modifier) {
        // Sombra desplazada
        Box(
            modifier = Modifier
                .matchParentSize()
                .offset(x = 4.dp, y = 4.dp)
                .background(ColorNegro)
        )
        Button(
            onClick = onClick,
            enabled = enabled,
            shape = RectangleShape,
            colors = ButtonDefaults.buttonColors(
                containerColor = background,
                contentColor = textColor,
                disabledContainerColor = Color(0xFF888888)
            ),
            border = androidx.compose.foundation.BorderStroke(3.dp, ColorNegro),
            contentPadding = PaddingValues(horizontal = 24.dp, vertical = 12.dp),
            elevation = ButtonDefaults.buttonElevation(0.dp, 0.dp, 0.dp),
            modifier = Modifier.fillMaxWidth()
        ) {
            Text(
                text = text.uppercase(),
                fontFamily = SpaceGrotesk,
                fontWeight = FontWeight.ExtraBold,
                fontSize = 16.sp,
                color = textColor
            )
        }
    }
}

// Botón brutalist secundario (blanco/amarillo)
@Composable
fun BrutalistOutlinedButton(
    text: String,
    onClick: () -> Unit,
    modifier: Modifier = Modifier,
    background: Color = ColorBlanco,
    textColor: Color = ColorNegro
) {
    BrutalistButton(
        text = text,
        onClick = onClick,
        modifier = modifier,
        background = background,
        textColor = textColor
    )
}

// Campo de texto brutalist
@Composable
fun BrutalistTextField(
    value: String,
    onValueChange: (String) -> Unit,
    label: String,
    modifier: Modifier = Modifier,
    placeholder: String = "",
    visualTransformation: VisualTransformation = VisualTransformation.None,
    keyboardOptions: KeyboardOptions = KeyboardOptions.Default,
    keyboardActions: KeyboardActions = KeyboardActions.Default,
    trailingIcon: @Composable (() -> Unit)? = null,
    singleLine: Boolean = true
) {
    Box(modifier = modifier) {
        // Sombra
        Box(
            modifier = Modifier
                .matchParentSize()
                .offset(x = 4.dp, y = 4.dp)
                .background(ColorNegro)
        )
        OutlinedTextField(
            value = value,
            onValueChange = onValueChange,
            label = {
                Text(
                    label.uppercase(),
                    fontFamily = SpaceGrotesk,
                    fontWeight = FontWeight.Bold,
                    fontSize = 12.sp
                )
            },
            placeholder = if (placeholder.isNotEmpty()) {
                { Text(placeholder, fontFamily = SpaceGrotesk) }
            } else null,
            visualTransformation = visualTransformation,
            keyboardOptions = keyboardOptions,
            keyboardActions = keyboardActions,
            trailingIcon = trailingIcon,
            singleLine = singleLine,
            shape = RectangleShape,
            colors = OutlinedTextFieldDefaults.colors(
                focusedBorderColor = ColorNegro,
                unfocusedBorderColor = ColorNegro,
                focusedLabelColor = ColorNegro,
                unfocusedLabelColor = ColorNegro,
                focusedContainerColor = ColorBlanco,
                unfocusedContainerColor = ColorBlanco,
                cursorColor = ColorNegro
            ),
            modifier = Modifier
                .fillMaxWidth()
                .border(3.dp, ColorNegro, RectangleShape)
        )
    }
}

// Tarjeta brutalist genérica
@Composable
fun BrutalistCard(
    modifier: Modifier = Modifier,
    background: Color = ColorBlanco,
    shadowOffset: Dp = 5.dp,
    content: @Composable () -> Unit
) {
    Box(modifier = modifier) {
        // Sombra desplazada
        Box(
            modifier = Modifier
                .matchParentSize()
                .offset(x = shadowOffset, y = shadowOffset)
                .background(ColorNegro)
        )
        Box(
            modifier = Modifier
                .fillMaxWidth()
                .background(background)
                .border(3.dp, ColorNegro, RectangleShape)
                .padding(16.dp)
        ) {
            content()
        }
    }
}

// Mensaje de error brutalist
@Composable
fun BrutalistError(message: String, modifier: Modifier = Modifier) {
    Box(modifier = modifier) {
        Box(
            modifier = Modifier
                .matchParentSize()
                .offset(x = 3.dp, y = 3.dp)
                .background(ColorNegro)
        )
        Box(
            modifier = Modifier
                .fillMaxWidth()
                .background(ColorRojo)
                .border(3.dp, ColorNegro)
                .padding(12.dp)
        ) {
            Text(
                text = message,
                color = ColorBlanco,
                fontFamily = SpaceGrotesk,
                fontWeight = FontWeight.Bold,
                fontSize = 14.sp
            )
        }
    }
}

// Mensaje de éxito brutalist
@Composable
fun BrutalistSuccess(message: String, modifier: Modifier = Modifier) {
    Box(modifier = modifier) {
        Box(
            modifier = Modifier
                .matchParentSize()
                .offset(x = 3.dp, y = 3.dp)
                .background(ColorNegro)
        )
        Box(
            modifier = Modifier
                .fillMaxWidth()
                .background(ColorAmarillo)
                .border(3.dp, ColorNegro)
                .padding(12.dp)
        ) {
            Text(
                text = message,
                color = ColorNegro,
                fontFamily = SpaceGrotesk,
                fontWeight = FontWeight.Bold,
                fontSize = 14.sp
            )
        }
    }
}

// Etiqueta/Tag brutalist
@Composable
fun BrutalistTag(
    text: String,
    background: Color = ColorAzul,
    modifier: Modifier = Modifier
) {
    Box(modifier = modifier) {
        Box(
            modifier = Modifier
                .matchParentSize()
                .offset(x = 2.dp, y = 2.dp)
                .background(ColorNegro)
        )
        Box(
            modifier = Modifier
                .background(background)
                .border(2.dp, ColorNegro)
                .padding(horizontal = 10.dp, vertical = 4.dp)
        ) {
            Text(
                text = text.uppercase(),
                fontFamily = SpaceGrotesk,
                fontWeight = FontWeight.Bold,
                fontSize = 11.sp,
                color = ColorNegro
            )
        }
    }
}

// Avatar con inicial
@Composable
fun BrutalistAvatar(
    initial: String,
    size: Dp = 40.dp,
    background: Color = ColorAmarillo,
    modifier: Modifier = Modifier
) {
    Box(modifier = modifier) {
        Box(
            modifier = Modifier
                .matchParentSize()
                .offset(x = 2.dp, y = 2.dp)
                .background(ColorNegro)
        )
        Box(
            modifier = Modifier
                .height(size)
                .background(background)
                .border(2.dp, ColorNegro)
                .padding(horizontal = 10.dp),
            contentAlignment = Alignment.Center
        ) {
            Text(
                text = initial,
                fontFamily = SpaceGrotesk,
                fontWeight = FontWeight.ExtraBold,
                fontSize = (size.value * 0.45).sp,
                color = ColorNegro
            )
        }
    }
}

