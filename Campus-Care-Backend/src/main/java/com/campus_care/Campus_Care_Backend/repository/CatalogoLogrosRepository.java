package com.campus_care.Campus_Care_Backend.repository;

import com.campus_care.Campus_Care_Backend.domain.CatalogoLogros;
import org.springframework.data.mongodb.repository.MongoRepository;
import org.springframework.data.mongodb.repository.config.EnableMongoRepositories;

import java.util.List;
import java.util.Optional;

@EnableMongoRepositories
public interface CatalogoLogrosRepository extends MongoRepository<CatalogoLogros, String> {

    List<CatalogoLogros> findByCriterioRacha(String criterioRacha);

    Optional<CatalogoLogros> findByConceptos(String concepto);



}
