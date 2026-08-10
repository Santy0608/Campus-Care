package com.campus_care.Campus_Care_Backend.serviceImpl;

import com.campus_care.Campus_Care_Backend.dto.DashboardAdminDTO;
import com.campus_care.Campus_Care_Backend.projection.MetricaPromedioAgg;
import com.campus_care.Campus_Care_Backend.repository.AutoevaluacionRepository;
import com.campus_care.Campus_Care_Backend.repository.UsuarioRepository;
import com.campus_care.Campus_Care_Backend.service.DashboardAdminService;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.stereotype.Service;

import java.time.Instant;
import java.time.LocalDate;
import java.time.ZoneOffset;
import java.util.ArrayList;
import java.util.List;
import java.util.Map;
import java.util.stream.Collectors;


@Service
public class DashboardAdminServiceImpl implements DashboardAdminService {

    @Autowired
    private UsuarioRepository usuarioRepository;

    @Autowired
    private AutoevaluacionRepository autoevaluacionRepository;

    @Override
    public DashboardAdminDTO obtenerDatosDashboard() {
        long totalEstudiantes = usuarioRepository.count();

        LocalDate hoy = LocalDate.now(ZoneOffset.UTC);
        Instant inicio = hoy.atStartOfDay(ZoneOffset.UTC).toInstant();
        Instant fin = hoy.plusDays(1).atStartOfDay(ZoneOffset.UTC).toInstant();
        long evaluacionesHoy = autoevaluacionRepository.countByFechaEvaluacionBetween(inicio, fin);

        List<MetricaPromedioAgg> promedios = autoevaluacionRepository.promedioPorMetrica();
        Map<String, Double> mapa = promedios.stream()
                .collect(Collectors.toMap(MetricaPromedioAgg::getId, MetricaPromedioAgg::getPromedio));

        DashboardAdminDTO dto = new DashboardAdminDTO();
        dto.setTotalEstudiantes(totalEstudiantes);
        dto.setEvaluacionesHoy(evaluacionesHoy);
        dto.setAvgEstres(round(mapa.getOrDefault("ESTRES", 0.0)));
        dto.setAvgAnsiedad(round(mapa.getOrDefault("ANSIEDAD", 0.0)));
        dto.setAvgSueno(round(mapa.getOrDefault("SUENO", 0.0)));
        dto.setAvgEstadoAnimo(round(mapa.getOrDefault("ANIMO", 0.0)));
        dto.setAvgRelaciones(round(mapa.getOrDefault("RELACIONES", 0.0)));
        dto.setAvgMotivacion(round(mapa.getOrDefault("MOTIVACION", 0.0)));

        // riesgo = la métrica con promedio más bajo entre las que sí tienen datos
        String categoria = null;
        double minimo = 5.0;
        for (Map.Entry<String, Double> entry : mapa.entrySet()) {
            if (entry.getValue() < minimo) {
                minimo = entry.getValue();
                categoria = entry.getKey();
            }
        }
        dto.setRiesgoPromedio(categoria != null ? round(minimo) : 0.0);
        dto.setRiesgoCategoria(categoria != null ? categoria : "Sin datos");

        return dto;
    }

    private double round(double valor){
        return Math.round(valor * 10) / 10.0;
    }

}
