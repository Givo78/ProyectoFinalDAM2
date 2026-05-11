package com.example.filmspo.ui.screens

import androidx.compose.foundation.background
import androidx.compose.foundation.border
import androidx.compose.foundation.layout.Box
import androidx.compose.foundation.layout.Column
import androidx.compose.foundation.layout.Spacer
import androidx.compose.foundation.layout.fillMaxSize
import androidx.compose.foundation.layout.fillMaxWidth
import androidx.compose.foundation.layout.height
import androidx.compose.foundation.layout.offset
import androidx.compose.foundation.layout.padding
import androidx.compose.foundation.rememberScrollState
import androidx.compose.foundation.text.KeyboardOptions
import androidx.compose.foundation.verticalScroll
import androidx.compose.material.icons.Icons
import androidx.compose.material.icons.filled.Visibility
import androidx.compose.material.icons.filled.VisibilityOff
import androidx.compose.material3.Icon
import androidx.compose.material3.IconButton
import androidx.compose.material3.Text
import androidx.compose.material3.TextButton
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
import androidx.compose.ui.text.font.FontWeight
import androidx.compose.ui.text.input.KeyboardType
import androidx.compose.ui.text.input.PasswordVisualTransformation
import androidx.compose.ui.text.input.VisualTransformation
import androidx.compose.ui.unit.dp
import androidx.compose.ui.unit.sp
import androidx.lifecycle.viewmodel.compose.viewModel
import com.example.filmspo.ui.components.BrutalistButton
import com.example.filmspo.ui.components.BrutalistError
import com.example.filmspo.ui.components.BrutalistTextField
import com.example.filmspo.ui.theme.ColorAcento
import com.example.filmspo.ui.theme.ColorAmarillo
import com.example.filmspo.ui.theme.ColorFondo
import com.example.filmspo.ui.theme.ColorNegro
import com.example.filmspo.ui.theme.ColorVerde
import com.example.filmspo.ui.theme.PermanentMarker
import com.example.filmspo.ui.theme.SpaceGrotesk
import com.example.filmspo.viewmodel.AuthViewModel

@Composable
fun RegisterScreen(
    onNavigateToLogin: () -> Unit,
    onRegisterSuccess: () -> Unit,
    viewModel: AuthViewModel = viewModel()
) {
    val state by viewModel.state.collectAsState()

    var name by remember { mutableStateOf("") }
    var email by remember { mutableStateOf("") }
    var password by remember { mutableStateOf("") }
    var confirmPassword by remember { mutableStateOf("") }
    var passwordVisible by remember { mutableStateOf(false) }

    LaunchedEffect(state.isAuthenticated) {
        if (state.isAuthenticated) onRegisterSuccess()
    }

    Box(
        modifier = Modifier
            .fillMaxSize()
            .background(ColorFondo)
            .drawBehind {
                val dotColor = Color(0x22000000)
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
        Column(
            modifier = Modifier
                .fillMaxSize()
                .verticalScroll(rememberScrollState())
                .padding(horizontal = 24.dp),
            horizontalAlignment = Alignment.CenterHorizontally
        ) {
            Spacer(Modifier.height(48.dp))

            // Logo
            Box {
                Box(
                    modifier = Modifier
                        .matchParentSize()
                        .offset(x = 4.dp, y = 4.dp)
                        .background(ColorNegro)
                )
                Box(
                    modifier = Modifier
                        .background(ColorAcento)
                        .border(3.dp, ColorNegro)
                        .padding(horizontal = 20.dp, vertical = 10.dp)
                ) {
                    Text(
                        text = "FILMSPO",
                        fontFamily = PermanentMarker,
                        fontSize = 36.sp,
                        color = ColorNegro
                    )
                }
            }

            Spacer(Modifier.height(32.dp))

            // Tarjeta formulario
            Box {
                Box(
                    modifier = Modifier
                        .matchParentSize()
                        .offset(x = 6.dp, y = 6.dp)
                        .background(ColorNegro)
                )
                Column(
                    modifier = Modifier
                        .fillMaxWidth()
                        .background(Color.White)
                        .border(3.dp, ColorNegro)
                        .padding(24.dp)
                ) {
                    Text(
                        text = "REGISTRO",
                        fontFamily = PermanentMarker,
                        fontSize = 28.sp,
                        color = ColorNegro,
                        modifier = Modifier
                            .background(ColorVerde)
                            .border(2.dp, ColorNegro)
                            .padding(horizontal = 12.dp, vertical = 4.dp)
                    )

                    Spacer(Modifier.height(24.dp))

                    BrutalistTextField(
                        value = name,
                        onValueChange = { name = it; viewModel.clearError() },
                        label = "Nombre"
                    )

                    Spacer(Modifier.height(16.dp))

                    BrutalistTextField(
                        value = email,
                        onValueChange = { email = it; viewModel.clearError() },
                        label = "Email",
                        keyboardOptions = KeyboardOptions(keyboardType = KeyboardType.Email)
                    )

                    Spacer(Modifier.height(16.dp))

                    BrutalistTextField(
                        value = password,
                        onValueChange = { password = it; viewModel.clearError() },
                        label = "Contraseña",
                        visualTransformation = if (passwordVisible) VisualTransformation.None else PasswordVisualTransformation(),
                        keyboardOptions = KeyboardOptions(keyboardType = KeyboardType.Password),
                        trailingIcon = {
                            IconButton(onClick = { passwordVisible = !passwordVisible }) {
                                Icon(
                                    if (passwordVisible) Icons.Default.Visibility else Icons.Default.VisibilityOff,
                                    contentDescription = null
                                )
                            }
                        }
                    )

                    Spacer(Modifier.height(16.dp))

                    BrutalistTextField(
                        value = confirmPassword,
                        onValueChange = { confirmPassword = it; viewModel.clearError() },
                        label = "Confirmar Contraseña",
                        visualTransformation = PasswordVisualTransformation(),
                        keyboardOptions = KeyboardOptions(keyboardType = KeyboardType.Password)
                    )

                    if (state.error != null) {
                        Spacer(Modifier.height(16.dp))
                        BrutalistError(message = state.error!!)
                    }

                    Spacer(Modifier.height(24.dp))

                    BrutalistButton(
                        text = if (state.isLoading) "Registrando..." else "Registrarse",
                        onClick = { viewModel.register(name, email, password, confirmPassword) },
                        enabled = !state.isLoading,
                        background = ColorAmarillo,
                        textColor = ColorNegro
                    )

                    Spacer(Modifier.height(16.dp))

                    TextButton(
                        onClick = onNavigateToLogin,
                        modifier = Modifier.fillMaxWidth()
                    ) {
                        Text(
                            text = "¿Ya tienes cuenta? Entra",
                            fontFamily = SpaceGrotesk,
                            fontWeight = FontWeight.Bold,
                            fontSize = 14.sp,
                            color = ColorNegro
                        )
                    }
                }
            }

            Spacer(Modifier.height(48.dp))
        }
    }
}
