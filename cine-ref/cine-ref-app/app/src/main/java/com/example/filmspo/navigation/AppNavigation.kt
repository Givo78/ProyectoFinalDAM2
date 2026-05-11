package com.example.filmspo.navigation

import androidx.compose.foundation.background
import androidx.compose.foundation.border
import androidx.compose.foundation.layout.Box
import androidx.compose.foundation.layout.offset
import androidx.compose.foundation.layout.padding
import androidx.compose.foundation.layout.size
import androidx.compose.material.icons.Icons
import androidx.compose.material.icons.filled.Home
import androidx.compose.material.icons.filled.Person
import androidx.compose.material3.Icon
import androidx.compose.material3.NavigationBar
import androidx.compose.material3.NavigationBarItem
import androidx.compose.material3.NavigationBarItemDefaults
import androidx.compose.material3.Scaffold
import androidx.compose.material3.Text
import androidx.compose.runtime.Composable
import androidx.compose.runtime.collectAsState
import androidx.compose.runtime.getValue
import androidx.compose.ui.Modifier
import androidx.compose.ui.graphics.Color
import androidx.compose.ui.text.font.FontWeight
import androidx.compose.ui.unit.dp
import androidx.compose.ui.unit.sp
import androidx.lifecycle.viewmodel.compose.viewModel
import androidx.navigation.NavGraph.Companion.findStartDestination
import androidx.navigation.compose.NavHost
import androidx.navigation.compose.composable
import androidx.navigation.compose.rememberNavController
import com.example.filmspo.ui.screens.HomeScreen
import com.example.filmspo.ui.screens.LoginScreen
import com.example.filmspo.ui.screens.ModerationScreen
import com.example.filmspo.ui.screens.MovieDetailScreen
import com.example.filmspo.ui.screens.ProfileScreen
import com.example.filmspo.ui.screens.RegisterScreen
import com.example.filmspo.ui.theme.ColorAcento
import com.example.filmspo.ui.theme.ColorAmarillo
import com.example.filmspo.ui.theme.ColorFondo
import com.example.filmspo.ui.theme.ColorNegro
import com.example.filmspo.ui.theme.SpaceGrotesk
import com.example.filmspo.viewmodel.AuthViewModel

sealed class Screen(val route: String) {
    object Login : Screen("login")
    object Register : Screen("register")
    object Home : Screen("home")
    object MovieDetail : Screen("movie/{movieId}") {
        fun createRoute(movieId: Int) = "movie/$movieId"
    }
    object Profile : Screen("profile")
    object Moderation : Screen("moderation")
}

private val bottomNavItems = listOf(
    Screen.Home to Icons.Default.Home,
    Screen.Profile to Icons.Default.Person
)

@Composable
fun AppNavigation(authViewModel: AuthViewModel = viewModel()) {
    val rootNavController = rememberNavController()
    val authState by authViewModel.state.collectAsState()

    val startDestination = if (authState.isAuthenticated) Screen.Home.route else Screen.Login.route

    NavHost(navController = rootNavController, startDestination = startDestination) {
        // Auth screens (sin bottom nav)
        composable(Screen.Login.route) {
            LoginScreen(
                onNavigateToRegister = { rootNavController.navigate(Screen.Register.route) },
                onLoginSuccess = {
                    rootNavController.navigate(Screen.Home.route) {
                        popUpTo(Screen.Login.route) { inclusive = true }
                    }
                },
                viewModel = authViewModel
            )
        }

        composable(Screen.Register.route) {
            RegisterScreen(
                onNavigateToLogin = { rootNavController.popBackStack() },
                onRegisterSuccess = {
                    rootNavController.navigate(Screen.Home.route) {
                        popUpTo(Screen.Login.route) { inclusive = true }
                    }
                },
                viewModel = authViewModel
            )
        }

        // Main app con bottom nav
        composable(Screen.Home.route) {
            MainScaffold(
                currentRoute = Screen.Home.route,
                onNavigate = { route ->
                    rootNavController.navigate(route) {
                        popUpTo(rootNavController.graph.findStartDestination().id) { saveState = true }
                        launchSingleTop = true
                        restoreState = true
                    }
                }
            ) {
                HomeScreen(
                    onMovieClick = { movieId ->
                        rootNavController.navigate(Screen.MovieDetail.createRoute(movieId))
                    }
                )
            }
        }

        composable(Screen.Profile.route) {
            MainScaffold(
                currentRoute = Screen.Profile.route,
                onNavigate = { route ->
                    rootNavController.navigate(route) {
                        popUpTo(rootNavController.graph.findStartDestination().id) { saveState = true }
                        launchSingleTop = true
                        restoreState = true
                    }
                }
            ) {
                ProfileScreen(
                    onNavigateToModeration = { rootNavController.navigate(Screen.Moderation.route) },
                    onLogout = {
                        rootNavController.navigate(Screen.Login.route) {
                            popUpTo(0) { inclusive = true }
                        }
                    },
                    onMovieClick = { movieId ->
                        rootNavController.navigate(Screen.MovieDetail.createRoute(movieId))
                    },
                    authViewModel = authViewModel
                )
            }
        }

        composable(Screen.MovieDetail.route) { backStackEntry ->
            val movieId = backStackEntry.arguments?.getString("movieId")?.toIntOrNull() ?: return@composable
            MovieDetailScreen(
                movieId = movieId,
                onBack = { rootNavController.popBackStack() }
            )
        }

        composable(Screen.Moderation.route) {
            ModerationScreen(onBack = { rootNavController.popBackStack() })
        }
    }
}

@Composable
private fun MainScaffold(
    currentRoute: String,
    onNavigate: (String) -> Unit,
    content: @Composable () -> Unit
) {
    Scaffold(
        bottomBar = {
            Box {
                Box(
                    modifier = Modifier
                        .matchParentSize()
                        .offset(y = (-3).dp)
                        .background(ColorNegro)
                )
                NavigationBar(
                    containerColor = ColorAcento,
                    modifier = Modifier.border(3.dp, ColorNegro)
                ) {
                    bottomNavItems.forEach { (screen, icon) ->
                        val selected = currentRoute == screen.route
                        NavigationBarItem(
                            selected = selected,
                            onClick = { onNavigate(screen.route) },
                            icon = {
                                Icon(
                                    icon,
                                    contentDescription = screen.route,
                                    modifier = Modifier.size(24.dp)
                                )
                            },
                            label = {
                                Text(
                                    text = screen.route.uppercase(),
                                    fontFamily = SpaceGrotesk,
                                    fontWeight = FontWeight.ExtraBold,
                                    fontSize = 10.sp
                                )
                            },
                            colors = NavigationBarItemDefaults.colors(
                                selectedIconColor = ColorNegro,
                                selectedTextColor = ColorNegro,
                                unselectedIconColor = Color(0xFF555555),
                                unselectedTextColor = Color(0xFF555555),
                                indicatorColor = ColorAmarillo
                            )
                        )
                    }
                }
            }
        },
        containerColor = ColorFondo
    ) { innerPadding ->
        Box(modifier = Modifier.padding(innerPadding)) {
            content()
        }
    }
}

