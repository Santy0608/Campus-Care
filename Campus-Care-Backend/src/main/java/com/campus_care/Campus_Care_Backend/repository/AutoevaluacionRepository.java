package com.campus_care.Campus_Care_Backend.repository;

import com.campus_care.Campus_Care_Backend.domain.Autoevaluacion;
import org.springframework.data.mongodb.core.MongoAdminOperations;
import org.springframework.data.mongodb.repository.MongoRepository;
import org.springframework.data.mongodb.repository.config.EnableMongoRepositories;

import java.time.Instant;
import java.util.List;
import java.util.Optional;

@EnableMongoRepositories
public interface AutoevaluacionRepository extends MongoRepository<Autoevaluacion, String> {

    //Para buscar si ya existe una autoevaluación en el rango de tiempo
     Optional<Autoevaluacion> findByIdUsuarioAndFechaEvaluacionBetween(
            String idUsuario, Instant inicio, Instant fin
    );

    //Para calcular racha
    List<Autoevaluacion> findByIdUsuarioOrderByFechaEvaluacionDesc(
            String idUsuario
    );


}
