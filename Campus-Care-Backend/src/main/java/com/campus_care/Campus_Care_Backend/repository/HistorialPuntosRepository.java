package com.campus_care.Campus_Care_Backend.repository;

import com.campus_care.Campus_Care_Backend.domain.HistorialPuntos;
import com.campus_care.Campus_Care_Backend.domain.TotalPuntos;
import org.springframework.data.mongodb.core.aggregation.AggregationResults;
import org.springframework.data.mongodb.repository.Aggregation;
import org.springframework.data.mongodb.repository.MongoRepository;
import org.springframework.data.mongodb.repository.config.EnableMongoRepositories;

import java.util.List;

@EnableMongoRepositories
public interface HistorialPuntosRepository extends MongoRepository<HistorialPuntos, String> {

    // Opción simple: trae todo el historial del usuario, sumás en el Service
    List<HistorialPuntos> findByIdUsuario(String idUsuario);

    // Opción con agregación: Mongo hace la suma, más eficiente
    @Aggregation(pipeline = {
            "{ '$match': { 'idUsuario': ?0 } }",
            "{ '$group': { '_id': null, 'total': { '$sum': '$puntosGanados' } } }"
    })
    AggregationResults<TotalPuntos> sumarPuntosPorUsuario(String idUsuario);

}
