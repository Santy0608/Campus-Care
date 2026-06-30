package com.campus_care.Campus_Care_Backend.serviceImpl;

import com.campus_care.Campus_Care_Backend.domain.Autoevaluacion;
import com.campus_care.Campus_Care_Backend.domain.Respuesta;
import com.campus_care.Campus_Care_Backend.dto.DashboardDTO;
import com.campus_care.Campus_Care_Backend.dto.MetricaSerieDTO;
import com.campus_care.Campus_Care_Backend.repository.DashboardRepository;
import com.campus_care.Campus_Care_Backend.service.DashboardService;
import lombok.RequiredArgsConstructor;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.stereotype.Service;

import java.time.Instant;
import java.time.LocalDate;
import java.time.ZoneOffset;
import java.util.*;
import java.util.stream.Collectors;

@Service
@RequiredArgsConstructor
public class DashboardServiceImpl implements DashboardService {

    @Autowired
    private DashboardRepository dashboardRepository;

    @Override
    public DashboardDTO obtenerDashboardEstudiante(String idUsuario) {
        // Rango: últimos 7 días hasta hoy inclusive
        LocalDate hoy = LocalDate.now(ZoneOffset.UTC);
        LocalDate inicioRango = hoy.minusDays(6); // 7 días contando hoy

        Instant inicio = inicioRango.atStartOfDay(ZoneOffset.UTC).toInstant();
        Instant fin = hoy.plusDays(1).atStartOfDay(ZoneOffset.UTC).toInstant();

        List<Autoevaluacion> evaluaciones = dashboardRepository
                .findByIdUsuarioAndFechaEvaluacionBetween(idUsuario, inicio, fin);

        // Mapear: fecha -> (metrica -> score)
        Map<LocalDate, Map<String, Integer>> mapaFechaMetrica = new LinkedHashMap<>();
        for (Autoevaluacion eval : evaluaciones) {
            LocalDate fecha = eval.getFechaEvaluacion()
                    .atZone(ZoneOffset.UTC).toLocalDate();
            Map<String, Integer> scores = new HashMap<>();
            for (Respuesta r : eval.getRespuestas()) {
                scores.put(r.getMetrica(), r.getScore());
            }
            mapaFechaMetrica.put(fecha, scores);
        }

        // Construir eje X con los 7 días (tengan o no evaluación)
        List<String> fechas = new ArrayList<>();
        for (int i = 0; i < 7; i++) {
            fechas.add(inicioRango.plusDays(i).toString()); // "2026-06-21"
        }

        // Detectar todas las métricas presentes en el rango
        Set<String> metricas = evaluaciones.stream()
                .flatMap(e -> e.getRespuestas().stream())
                .map(Respuesta::getMetrica)
                .collect(Collectors.toCollection(LinkedHashSet::new));

        // Si no hay evaluaciones aún, devolver métricas predeterminadas vacías
        if (metricas.isEmpty()) {
            metricas = new LinkedHashSet<>(
                    List.of("ESTRES", "SUENO", "ANIMO", "RELACIONES", "ANSIEDAD", "MOTIVACION")
            );
        }

        // Construir series para el line chart
        List<MetricaSerieDTO> series = new ArrayList<>();
        for (String metrica : metricas) {
            MetricaSerieDTO serie = new MetricaSerieDTO();
            serie.setMetrica(metrica);

            List<Integer> valores = new ArrayList<>();
            for (int i = 0; i < 7; i++) {
                LocalDate dia = inicioRango.plusDays(i);
                Map<String, Integer> scoresDelDia = mapaFechaMetrica.get(dia);
                // null si el usuario no hizo evaluación ese día
                valores.add(scoresDelDia != null ? scoresDelDia.get(metrica) : null);
            }
            serie.setValores(valores);
            series.add(serie);
        }

        DashboardDTO dashboard = new DashboardDTO();
        dashboard.setFechas(fechas);
        dashboard.setSeries(series);
        return dashboard;
    }
}
