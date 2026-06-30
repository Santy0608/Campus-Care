package com.campus_care.Campus_Care_Backend.auth;

import java.util.Arrays;

import com.campus_care.Campus_Care_Backend.auth.filter.JwtAuthenticationFilter;
import com.campus_care.Campus_Care_Backend.auth.filter.JwtValidationFilter;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.boot.web.servlet.FilterRegistrationBean;
import org.springframework.context.annotation.Bean;
import org.springframework.context.annotation.Configuration;
import org.springframework.core.Ordered;
import org.springframework.http.HttpMethod;
import org.springframework.security.authentication.AuthenticationManager;
import org.springframework.security.config.annotation.authentication.configuration.AuthenticationConfiguration;
import org.springframework.security.config.annotation.web.builders.HttpSecurity;
import org.springframework.security.config.http.SessionCreationPolicy;
import org.springframework.security.crypto.bcrypt.BCryptPasswordEncoder;
import org.springframework.security.crypto.password.PasswordEncoder;
import org.springframework.security.web.SecurityFilterChain;
import org.springframework.web.cors.CorsConfiguration;
import org.springframework.web.cors.CorsConfigurationSource;
import org.springframework.web.cors.UrlBasedCorsConfigurationSource;
import org.springframework.web.filter.CorsFilter;


@Configuration
public class SpringSecurityConfig {

    @Autowired
    private AuthenticationConfiguration authenticationConfiguration;

    @Bean
    AuthenticationManager authenticationManager() throws Exception {
        return authenticationConfiguration.getAuthenticationManager();
    }

    @Bean
    PasswordEncoder passwordEncoder() {
        return new BCryptPasswordEncoder();
    }

    @Bean
    SecurityFilterChain filterChain(HttpSecurity http) throws Exception {

        return http.authorizeHttpRequests(authz -> authz
                        .requestMatchers(HttpMethod.POST, "/login").permitAll()

                        //Reglas para Usuarios
                        .requestMatchers(HttpMethod.GET, "/api/usuarios/listado-usuarios").permitAll()
                        .requestMatchers(HttpMethod.GET, "/api/usuarios/{idUsuario}").hasAnyRole("ESTUDIANTE", "ADMIN")
                        .requestMatchers(HttpMethod.POST, "/api/usuarios/agregar-usuario").hasRole("ADMIN")
                        .requestMatchers(HttpMethod.PUT, "/api/usuarios/actualizar-usuario/{id}").hasRole("ADMIN")
                        .requestMatchers(HttpMethod.DELETE,"/api/usuarios/eliminar-usuario/{id}").hasRole("ADMIN")
                        //Reglas para categorias
                        .requestMatchers(HttpMethod.GET,"/api/categorias/listado-categorias").permitAll()
                        .requestMatchers(HttpMethod.GET,"/api/categorias/{id}").hasAnyRole("ESTUDIANTE","ADMIN")
                        .requestMatchers(HttpMethod.POST,"/api/categorias/agregar-categoria").hasRole("ADMIN")
                        .requestMatchers(HttpMethod.PUT,"/api/categorias/actualizar-categoria/{id}").hasRole("ADMIN")
                        .requestMatchers(HttpMethod.DELETE,"/api/categorias/eliminar-categoria/{id}").hasRole("ADMIN")
                        //Reglas para Autoevaluacion
                        .requestMatchers(HttpMethod.POST,"/api/autoevaluaciones/guardar-autoevaluacion").hasRole("ESTUDIANTE")
                        //Reglas para Catalogo Logros
                        .requestMatchers(HttpMethod.GET,"/api/catalogos-logros/listado-catalogos-logros").permitAll()
                        .requestMatchers(HttpMethod.GET,"/api/catalogos-logros/{id}").hasAnyRole("ESTUDIANTE","ADMIN")
                        .requestMatchers(HttpMethod.POST,"/api/catalogos-logros/agregar-catalogo-logro").hasRole("ADMIN")
                        .requestMatchers(HttpMethod.PUT,"/api/catalogos-logros/actualizar-cactalogo-logro/{id}").hasRole("ADMIN")
                        .requestMatchers(HttpMethod.DELETE,"/api/catalogos-logros/eliminar-catalogo-logro/{id}").hasRole("ADMIN")
                        //Reglas para Diario
                        .requestMatchers(HttpMethod.GET,"/api/diarios/listado-diarios").hasRole("ESTUDIANTE")
                        .requestMatchers(HttpMethod.POST,"/api/diarios/agregar-diario").hasRole("ESTUDIANTE")
                        //Reglas para Ejercicios Practicos
                        .requestMatchers(HttpMethod.GET,"/api/ejercicios-practicos/listado-ejercicios-practicos").permitAll()
                        .requestMatchers(HttpMethod.GET,"/api/ejercicios-practicos/{id}").hasAnyRole("ESTUDIANTE","ADMIN")
                        .requestMatchers(HttpMethod.POST,"/api/ejercicios-practicos/agregar-ejercicio-practico").hasRole("ADMIN")
                        .requestMatchers(HttpMethod.PUT,"/api/ejercicios-practicos/actualizar-ejercicio-practico/{id}").hasRole("ADMIN")
                        .requestMatchers(HttpMethod.DELETE,"/api/ejercicios-practicos/eliminar-ejercicio-practico/{id}").hasRole("ADMIN")
                        //Reglas para Frases Motivacionales
                        .requestMatchers(HttpMethod.GET,"/api/frases-motivacionales/listado-frases-motivacionales").permitAll()
                        .requestMatchers(HttpMethod.GET,"/api/frases-motivacionales/{id}").hasAnyRole("ESTUDIANTE","ADMIN")
                        .requestMatchers(HttpMethod.POST,"/api/frases-motivacionales/agregar-frase-motivacional").hasRole("ADMIN")
                        .requestMatchers(HttpMethod.PUT,"/api/frases-motivacionales/actualizar-frase-motivacional").hasRole("ADMIN")
                        .requestMatchers(HttpMethod.DELETE,"/api/frases-motivacionales/eliminar-frase-motivacional/{id}").hasRole("ADMIN")
                        //Reglas para Lineas de Apoyo
                        .requestMatchers(HttpMethod.GET,"/api/lineas-apoyo/listado-lineas-apoyo").permitAll()
                        .requestMatchers(HttpMethod.GET,"/api/lineas-apoyo/{id}").hasAnyRole("ESTUDIANTE","ADMIN")
                        .requestMatchers(HttpMethod.POST,"/api/agregar-linea-apoyo").hasRole("ADMIN")
                        .requestMatchers(HttpMethod.PUT,"/api/actualizar-linea-apoyo/{id}").hasRole("ADMIN")
                        .requestMatchers(HttpMethod.DELETE,"/api/eliminar-linea-apoyo/{id}").hasRole("ADMIN")
                        //Reglas para Recursos
                        .requestMatchers(HttpMethod.GET,"/api/recursos/listado-recursos").permitAll()
                        .requestMatchers(HttpMethod.GET,"/api/recursos/{id}").hasAnyRole("ESTUDIANTE","ADMIN")
                        .requestMatchers(HttpMethod.POST,"/api/recursos/agregar-recurso").hasRole("ADMIN")
                        .requestMatchers(HttpMethod.PUT,"/api/recursos/actualizar-recurso/{id}").hasRole("ADMIN")
                        .requestMatchers(HttpMethod.DELETE,"/api/recursos/eliminar-recurso/{id}").hasRole("ADMIN")
                        //Reglas para Tipos Recursosrecurso
                        .requestMatchers(HttpMethod.GET,"/api/tipos-recurso/listado-tipos-recurso").permitAll()
                        .requestMatchers(HttpMethod.GET,"/api/tipos-recurso/{id}").hasAnyRole("ESTUDIANTE","ADMIN")
                        .requestMatchers(HttpMethod.POST,"/api/tipos-recurso/agregar-tipos-recurso").hasRole("ADMIN")
                        .requestMatchers(HttpMethod.PUT,"/api/tipos-recurso/actualizar-tipos-recurso/{id}").hasRole("ADMIN")
                        .requestMatchers(HttpMethod.DELETE,"/api/tipos-recurso/eliminar-tipos-recurso/{id}").hasRole("ADMIN")
                        //Reglas para Dashboard
                        .requestMatchers(HttpMethod.GET,"/api/dashboard/estudiante/{idUsuario}").hasRole("ESTUDIANTE")


                        .anyRequest().authenticated())
                .cors(cors -> cors.configurationSource(configurationSource()))
                .addFilter(new JwtAuthenticationFilter(authenticationManager()))
                .addFilter(new JwtValidationFilter(authenticationManager()))
                .csrf(config -> config.disable())
                .sessionManagement(management -> management.sessionCreationPolicy(SessionCreationPolicy.STATELESS))
                .build();
    }

    @Bean
    CorsConfigurationSource configurationSource() {
        CorsConfiguration config = new CorsConfiguration();
        config.setAllowedOriginPatterns(Arrays.asList("*"));
        // config.addAllowedOrigin("http://localhost:3000");
        // config.setAllowedOrigins(Arrays.asList("http://localhost:4200"));
        config.setAllowedMethods(Arrays.asList("POST", "GET", "PUT", "DELETE"));
        config.setAllowedHeaders(Arrays.asList("Authorization", "Content-Type"));
        config.setAllowCredentials(true);

        UrlBasedCorsConfigurationSource source = new UrlBasedCorsConfigurationSource();
        source.registerCorsConfiguration("/**", config);
        return source;
    }

    @Bean
    FilterRegistrationBean<CorsFilter> corsFilter() {
        FilterRegistrationBean<CorsFilter> corsBean = new FilterRegistrationBean<CorsFilter>(
                new CorsFilter(this.configurationSource()));
        corsBean.setOrder(Ordered.HIGHEST_PRECEDENCE);
        return corsBean;
    }
}
