package com.campus_care.Campus_Care_Backend.repository;

import com.campus_care.Campus_Care_Backend.domain.EjercicioPractico;
import org.springframework.data.mongodb.repository.MongoRepository;
import org.springframework.data.mongodb.repository.config.EnableMongoRepositories;

import java.util.List;

@EnableMongoRepositories
public interface EjercicioPracticoRepository extends MongoRepository<EjercicioPractico, String> {

    List<EjercicioPractico> findByActivoTrue();

}
