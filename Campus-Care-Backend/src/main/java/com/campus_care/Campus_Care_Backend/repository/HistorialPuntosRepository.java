package com.campus_care.Campus_Care_Backend.repository;

import com.campus_care.Campus_Care_Backend.domain.HistorialPuntos;
import org.springframework.data.mongodb.repository.MongoRepository;
import org.springframework.data.mongodb.repository.config.EnableMongoRepositories;

@EnableMongoRepositories
public interface HistorialPuntosRepository extends MongoRepository<HistorialPuntos, String> {



}
