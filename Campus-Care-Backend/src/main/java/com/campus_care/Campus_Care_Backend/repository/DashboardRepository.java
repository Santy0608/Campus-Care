package com.campus_care.Campus_Care_Backend.repository;

import com.campus_care.Campus_Care_Backend.domain.Autoevaluacion;
import org.springframework.data.mongodb.repository.MongoRepository;

import java.time.Instant;
import java.util.List;

public interface DashboardRepository extends MongoRepository<Autoevaluacion, Long> {

    List<Autoevaluacion> findByIdUsuarioAndFechaEvaluacionBetween(
            String idUsuario, Instant inicio, Instant fin
    );

}
